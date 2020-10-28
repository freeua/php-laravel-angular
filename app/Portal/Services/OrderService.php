<?php

namespace App\Portal\Services;

use App\Documents\Models\Document;
use App\Exceptions\LeasingBudgetReached;
use App\Helpers\PortalHelper;
use App\Helpers\StorageHelper;
use App\Leasings\Exceptions\WrongOrderStatus;
use App\Leasings\Exceptions\WrongPickupCode;
use App\Leasings\Services\DocumentService;
use App\Models\Audit;
use App\Models\Rates\Rate;
use App\Modules\TechnicalServices\Services\TechnicalServicesService;
use App\Portal\Helpers\AuthHelper;
use App\Portal\Helpers\ContractPrices;
use App\Portal\Models\Offer;
use App\Portal\Models\Order;
use App\Portal\Models\User;
use App\Portal\Notifications\Contract\ContractCreated;
use App\Portal\Notifications\Order\OrderPickedUpForAdmin;
use App\Portal\Notifications\Order\OrderPickedUpForCompany;
use App\Portal\Notifications\Order\OrderPickedUpForDienstradSupport;
use App\Portal\Notifications\Order\OrderPickedUpForEmployee;
use App\Portal\Notifications\Order\OrderPickedUpForSupplier;
use App\Portal\Notifications\Order\OrderReady;
use App\Portal\Repositories\Company\ContractRepository;
use App\Portal\Repositories\Supplier\UserRepository;
use App\Repositories\OrderRepository;
use Brick\Math\RoundingMode;
use Brick\Money\Money;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class OrderService
{


    /** @var ContractRepository */
    private $contractRepository;
    /** @var UserRepository */
    private $userRepository;
    /** @var OrderRepository */
    private $orderRepository;

    private $contractService;

    public function __construct(
        ContractService $contractService,
        OrderRepository $orderRepository,
        UserRepository $userRepository,
        ContractRepository $contractRepository,
        TechnicalServicesService $technicalService
    ) {
        $this->orderRepository = $orderRepository;
        $this->contractService = $contractService;
        $this->technicalService = $technicalService;
        $this->userRepository = $userRepository;
        $this->contractRepository = $contractRepository;
    }

    public function ready(Order $order): bool
    {
        $orderData = [
            'status_id' => Order::STATUS_READY,
            'pickup_code' => $this->orderRepository->generatePickupCode(),
            'accepted_at' => Carbon::now()
        ];
        \DB::beginTransaction();
        if (!$this->orderRepository->update($order->id, $orderData)) {
            return false;
        }
        Audit::orderReady($order, AuthHelper::user());

        $order->user->notify(new OrderReady($order->fresh()));
        \DB::commit();
        return true;
    }

    public function canBeReady(Order $order): bool
    {
        return $order->status_id === Order::STATUS_OPEN;
    }

    public function pickup(Order $order, array $params)
    {
        if ($order->status_id != Order::STATUS_READY) {
            throw new WrongOrderStatus();
        }
        $this->checkPickupCode($order, $params['pickup_code']);
        \DB::beginTransaction();
        $user = $order->user;

        $order->fill([
            'status_id' => Order::STATUS_SUCCESSFUL,
            'picked_up_by' => $user->id,
            'picked_up_at' => Carbon::now(),
            'frame_number' => $params['frame_number'],
            'card_issue_date' => $params['card_issue_date'],
            'card_issue_authority' => $params['card_issue_authority'],
        ]);

        $contract = $this->contractService->createFromOrder($order);

        Audit::orderPickedUp($order, AuthHelper::user());
        Audit::contractCreated($contract, AuthHelper::user());

        $takeoverDocument = DocumentService::generateAndSaveTakeoverPdf($order);
        DocumentService::generateAndSaveCreditNotePdf($order);
        $order->contract->pickup_code = $order->pickup_code;
        $order->contract->saveOrFail();
        \DB::commit();
        $order->user->notify(new OrderPickedUpForEmployee($order->fresh(), $takeoverDocument->path, $order->offer->contract_file));

        $order->company->notify(new ContractCreated($contract));
        $order->company->notify(new OrderPickedUpForCompany($order->fresh(), $takeoverDocument->path, $order->singleLeasingFile, $order->offer->contract_file));
        $order->supplier->notify(new OrderPickedUpForSupplier($order->fresh(), $takeoverDocument->path, $order->singleLeasingFile, $order->offer->contract_file));
        $order->portal->notify(new OrderPickedUpForAdmin($order->fresh(), $takeoverDocument->path, $order->singleLeasingFile));

        $this->notifyDienstradSupport(new OrderPickedUpForDienstradSupport($order->fresh(), $takeoverDocument->path, $order->singleLeasingFile), $order->number);
    }

    public function notifyDienstradSupport($notification, $orderNumber)
    {
        $mail = env('MERCATOR_DIENSTRAD_MAIL', null);
        if (is_null($mail)) {
            \Log::alert("MERCATOR_DIENSTRAD_MAIL is not specified. No email will be sent for order $orderNumber");
        } else {
            (new User)->forceFill(['name' => '','email' => $mail, 'id' => 0])->notify($notification);
        }
    }

    public static function create(Offer $offer)
    {
        $pricesHelper = new ContractPrices($offer);
        $offerParams = $offer->toArray();
        unset($offerParams['number']);
        $order = new Order($offerParams);
        $order->offer_id = $offer->id;
        $order->user_id = $offer->userId;
        $order->supplier_id = $offer->supplier_id;
        $order->supplierCode = $offer->supplierCode;
        $order->employeeCode = $offer->employeeCode;
        $order->partner_id = $offer->partner_id;
        $order->company_id = $offer->company_id;
        $order->companyName = $offer->user->company->name;
        $order->date = $offer->updated_at;
        $order->serviceRateName = $offer->serviceRate->name;
        $order->insuranceRateName = $offer->insuranceRate->name;
        if ($offer->serviceRate->amountType === Rate::FIXED) {
            $order->serviceRateAmount = Money::of($offer->serviceRate->amount, 'EUR', null, RoundingMode::HALF_UP)->formatTo('de');
        } else {
            $percent = str_replace('.', ',', $offer->serviceRate->amount);
            $order->serviceRateAmount = "{$percent} %";
        }
        if ($offer->insuranceRate->amountType === Rate::FIXED) {
            $order->insuranceRateAmount = Money::of($offer->insuranceRate->amount, 'EUR', null, RoundingMode::HALF_UP)->formatTo('de');
        } else {
            $percent = str_replace('.', ',', $offer->insuranceRate->amount);
            $order->insuranceRateAmount = "{$percent} %";
        }
        $percent = str_replace('.', ',', $pricesHelper->getLeasingConditionApplied()->factor);
        $order->leasingRateAmount = "{$percent} %";
        $order->leasingRate = $offer->leasingRateAmount;
        $order->insuranceRate = $offer->insuranceRateAmount;
        $order->serviceRate = $offer->serviceRateAmount;
        $order->leasingRateSubsidy = $offer->leasingRateSubsidy;
        $order->insuranceRateSubsidy = $offer->insuranceRateSubsidy;
        $order->serviceRateSubsidy = $offer->serviceRateSubsidy;
        $order->calculatedResidualValue = $pricesHelper->getResidualValue()->getAmount()->toFloat();
        $order->leasingPeriod = $pricesHelper->getLeasingConditionApplied()->period;
        $order->status_id = Order::STATUS_OPEN;
        $order->portal_id = PortalHelper::id();
        $order->serviceRateModality = $offer->serviceRate->type;
        $order->saveOrFail();
        $order->refresh();

        $singleLeasePdf = DocumentService::generateSingleLeasePdf($order);
        $singleLeaseName = "Einzelleasingvertrag_{$order->number}_{$order->employeeName}_".Carbon::now()->format('Y.m.d');
        $singleLeasePath = "/orders/{$order->number}/{$singleLeaseName}.pdf";
        Storage::disk(StorageHelper::PRIVATE_DISK)->put($singleLeasePath, $singleLeasePdf);

        $order->singleLeasingFile = $singleLeasePath;

        $documentSingleLease = new Document([
            'filename' => $singleLeaseName,
            'size' => Storage::disk(StorageHelper::PRIVATE_DISK)->size($singleLeasePath),
            'visible' => true,
            'extension' => 'pdf',
            'path' => $singleLeasePath,
            'manually_uploaded' => false,
            'type' => Document::SINGLE_LEASE
        ]);
        $documentSingleLease->documentable()->associate($order->company);
        $documentSingleLease->uploader()->associate($order->user);
        $order->document()->save($documentSingleLease);

        $order->saveOrFail();
        return $order;
    }

    private function checkPickupCode(Order $order, $pickupCode)
    {
        if ($order->pickup_code != $pickupCode) {
            throw new WrongPickupCode();
        }
    }
}
