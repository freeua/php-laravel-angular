<?php
namespace App\Leasings\Services;

use App\Exceptions\WrongPickupCode;
use App\Leasings\Exceptions\WrongOrderStatus;
use App\Models\Audit;
use App\Models\Rates\ServiceRate;
use App\Modules\TechnicalServices\Services\TechnicalServicesAuditsService;
use App\Partners\Models\Partner;
use App\Portal\Helpers\AuthHelper;
use App\Portal\Models\Order;
use App\Portal\Models\User;
use App\Portal\Notifications\Contract\ContractCreated;
use App\Portal\Notifications\Order\OrderPickedUpForAdmin;
use App\Portal\Notifications\Order\OrderPickedUpForCompany;
use App\Portal\Notifications\Order\OrderPickedUpForDienstradSupport;
use App\Portal\Notifications\Order\OrderPickedUpForEmployee;
use App\Portal\Notifications\Order\OrderReady;
use Illuminate\Support\Carbon;

class OrderService
{
    public static function list()
    {
        $requester = request()->requester;
        if ($requester instanceof Partner) {
            return Order::query()
            ->where('partner_id', $requester->id)
            ->with('company', 'status', 'productCategory', 'offer', 'offer.accessories', 'user')
            ->get();
        }
        return Order::query()
            ->where('user_id', $requester->id)
            ->with('company', 'status', 'productCategory', 'offer', 'offer.accessories', 'user')
            ->get();
    }

    public static function markAsReady(Order $order)
    {
        if ($order->status_id == Order::STATUS_OPEN) {
            \DB::beginTransaction();
            $order->fill([
                'status_id' => Order::STATUS_READY,
                'pickup_code' => self::generatePickupCode(),
                'accepted_at' => Carbon::now()
            ]);
            $order->saveOrFail();
            Audit::orderReady($order, request()->requester);
            $order->user->notify(new OrderReady($order->fresh()));
            \DB::commit();
            return $order;
        } else {
            throw new WrongOrderStatus();
        }
    }

    public static function markCreditNoteRead(Order $order)
    {
        if ($order->status_id == Order::STATUS_SUCCESSFUL) {
            if (!$order->creditNoteRead) {
                \DB::beginTransaction();
                $order->fill([
                    'creditNoteRead' => true,
                ]);
                $order->saveOrFail();
                \DB::commit();
            }
            return $order;
        } else {
            throw new WrongOrderStatus();
        }
    }

    public static function pickup(Order $order, array $params)
    {
        if ($order->status_id != Order::STATUS_READY) {
            throw new WrongOrderStatus();
        }
        self::checkPickUpCode($order, $params['pickupCode']);
        \DB::beginTransaction();
        $user = $order->user;

        $order->fill([
            'status_id' => Order::STATUS_SUCCESSFUL,
            'picked_up_by' => $user->id,
            'picked_up_at' => Carbon::now(),
            'frame_number' => $params['serialNumber'],
            'card_issue_date' => Carbon::parse($params['idCard']['issueDate']),
            'card_issue_authority' => $params['idCard']['authority'],
        ]);

        $contract = ContractService::createFromOrder($order);
        if (!$order->relationLoaded('offer')) {
            $order->load('offer');
        }
        if (!$order->relationLoaded('contract')) {
            $order->load('contract');
        }

        $isInspection = $order->offer->serviceRate->type == ServiceRate::INSPECTION;
        if ($isInspection == false) {
            $technicalService = TechnicalServiceService::create($order->offer, $order, $contract);
        }


        $requester = request()->requester;

        Audit::orderPickedUp($order, $order->user);
        if (!is_null($requester) && $requester instanceof Partner) {
            Audit::contractCreatedByPartner($contract, $order->partner);
        } else {
            Audit::contractCreated($contract, AuthHelper::user());
        }
        if ($isInspection == false) {
            TechnicalServicesAuditsService::technicalServiceCreated($technicalService, AuthHelper::user());
        }

        $takeoverDocument = DocumentService::generateAndSaveTakeoverPdf($order);
        DocumentService::generateAndSaveCreditNotePdf($order);

        $order->save();
        $order->contract->pickup_code = $order->pickup_code;
        $order->contract->saveOrFail();
        \DB::commit();
        $order->user->notify(new OrderPickedUpForEmployee($order->fresh(), $takeoverDocument->path, $order->offer->contract_file));

        $order->company->notify(new ContractCreated($contract));
        $order->company->notify(new OrderPickedUpForCompany($order->fresh(), $takeoverDocument->path, $order->singleLeasingFile, $order->offer->contract_file));
        $order->portal->notify(new OrderPickedUpForAdmin($order->fresh(), $takeoverDocument->path, $order->singleLeasingFile));

        self::notifyDienstradSupport(new OrderPickedUpForDienstradSupport($order->fresh(), $takeoverDocument->path, $order->singleLeasingFile), $order->number);

        return $order;
    }

    public static function notifyDienstradSupport($notification, $orderNumber)
    {
        $mail = env('MERCATOR_DIENSTRAD_MAIL', null);
        if (is_null($mail)) {
            \Log::alert("MERCATOR_DIENSTRAD_MAIL is not specified. No email will be sent for order $orderNumber");
        } else {
            (new User)->forceFill(['name' => '','email' => $mail])->notify($notification);
        }
    }

    private static function checkPickupCode(Order $order, $pickupCode)
    {
        if ($order->pickup_code != $pickupCode) {
            throw new WrongPickupCode();
        }
    }

    public static function generatePickupCode()
    {
        $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $code = '';
        $lettersLength = strlen($letters);

        for ($i = 0; $i < Order::PICKUP_CODE_LETTERS_COUNT; $i++) {
            $code .= $letters[rand(0, $lettersLength - 1)];
        }

        $code .= str_pad(rand(1, 999), Order::PICKUP_CODE_DIGITS_COUNT, 0);

        return str_shuffle($code);
    }
}
