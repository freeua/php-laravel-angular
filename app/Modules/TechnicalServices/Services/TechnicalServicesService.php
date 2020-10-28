<?php

namespace App\Modules\TechnicalServices\Services;

use App\Helpers\StorageHelper;
use App\Models\Rates\ServiceRate;
use App\Modules\TechnicalServices\Exceptions\ContractIsNotActiveException;
use App\Modules\TechnicalServices\Exceptions\IncorrectFrameNumberException;
use App\Modules\TechnicalServices\Exceptions\IncorrectInspectionCodeException;
use App\Modules\TechnicalServices\Exceptions\ServiceBudgetExceededException;
use App\Modules\TechnicalServices\Exceptions\TechnicalServiceInProgressException;
use App\Modules\TechnicalServices\Models\TechnicalService;
use App\Modules\TechnicalServices\Notifications\AcceptedTechnicalServiceForCompany;
use App\Modules\TechnicalServices\Notifications\AcceptedTechnicalServiceForEmployee;
use App\Modules\TechnicalServices\Notifications\CompletedTechnicalServiceForCompany;
use App\Modules\TechnicalServices\Notifications\CompletedTechnicalServiceForEmployee;
use App\Modules\TechnicalServices\Notifications\CompletedTechnicalServiceForSupplier;
use App\Modules\TechnicalServices\Notifications\CreatedInspectionForEmployee;
use App\Modules\TechnicalServices\Notifications\CreatedTechnicalServiceForEmployee;
use App\Modules\TechnicalServices\Notifications\ReadyTechnicalServiceForEmployee;
use App\Portal\Helpers\AuthHelper;
use App\Portal\Models\Contract;
use App\Portal\Models\Order;
use Carbon\Carbon;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class TechnicalServicesService
{


    public function __construct()
    {
    }

    public static function generateCode(): string
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

    public static function createFromContract(Contract $contract)
    {
        self::checkContractForTechnicalService($contract);
        \DB::beginTransaction();
        $technicalService = new TechnicalService();
        $technicalService->fill($contract->toArrayCamel());
        $technicalService->inspectionCode = self::generateCode();
        $technicalService->statusId = TechnicalService::STATUS_OPEN;
        $technicalService->frameNumber = $contract->serialNumber;
        $technicalService->orderId = $contract->order->id;
        $technicalService->contractId = $contract->id;
        $technicalService->serviceModality = $contract->serviceRateModality;
        $technicalService->statusUpdatedAt = null;
        $technicalService->saveOrFail();
        TechnicalServicesAuditsService::technicalServiceCreated($technicalService, AuthHelper::user());
        $technicalService->statusId = TechnicalService::STATUS_OPEN;
        $technicalService->saveOrFail();
        \DB::commit();
        if ($technicalService->serviceModality === ServiceRate::FULL_SERVICE) {
            $technicalService->user->notify(new CreatedTechnicalServiceForEmployee($technicalService->fresh()));
        } else {
            $technicalService->user->notify(new CreatedInspectionForEmployee($technicalService->fresh()));
        }

        return $technicalService;
    }

    public static function accept(TechnicalService $technicalService, array $request)
    {

        if ($technicalService->frameNumber !== $request['frameNumber']) {
            throw new IncorrectFrameNumberException();
        }
        if ($technicalService->inspectionCode !== $request['inspectionCode']) {
            throw new IncorrectInspectionCodeException();
        }

        if ($technicalService->contract->isExpired()) {
            throw new ContractIsNotActiveException();
        }

        if ($technicalService->serviceModality === ServiceRate::FULL_SERVICE) {
            if ($technicalService->contract->remainingServiceBudget() < $request['grossAmount']) {
                throw new ServiceBudgetExceededException();
            }
        } else {
            if ($technicalService->contract->serviceBudget < $request['grossAmount']) {
                throw new ServiceBudgetExceededException();
            }
        }
        DB::beginTransaction();
        $technicalService->statusId = TechnicalService::STATUS_IN_PROCESS;
        $technicalService->grossAmount = $request['grossAmount'];
        $technicalService->statusUpdatedAt = Carbon::now();
        $technicalService->saveOrFail();

        TechnicalServicesAuditsService::technicalServiceAccepted($technicalService, AuthHelper::user());
        DB::commit();

        $technicalService->user->notify(new AcceptedTechnicalServiceForEmployee($technicalService->fresh()));
        $technicalService->user->company->notify(new AcceptedTechnicalServiceForCompany($technicalService->fresh()));

        return $technicalService;
    }

    public static function ready(TechnicalService $technicalService)
    {

        DB::beginTransaction();
        $technicalService->statusId = TechnicalService::STATUS_READY;
        $technicalService->pickupCode = self::generateCode();
        $technicalService->saveOrFail();

        TechnicalServicesAuditsService::technicalServiceReady($technicalService, AuthHelper::user());

        DB::commit();
        $technicalService->user->notify(new ReadyTechnicalServiceForEmployee($technicalService->fresh()));

        return $technicalService;
    }

    public static function complete(TechnicalService $technicalService, array $request)
    {

        if ($technicalService->frameNumber !== $request['frameNumber']) {
            throw new IncorrectFrameNumberException();
        }
        if ($technicalService->pickupCode !== $request['pickupCode']) {
            throw new IncorrectInspectionCodeException();
        }
        DB::beginTransaction();
        $technicalService->statusId = TechnicalService::STATUS_SUCCESSFUL;
        $technicalService->saveOrFail();

        TechnicalServicesAuditsService::technicalServiceCompleted($technicalService, AuthHelper::user());
        if ($technicalService->portal->automaticCreditNote) {
            DocumentsService::generateAndSaveCreditNoteTechnicalServicePdf($technicalService);
        }

        DB::commit();
        $technicalService->user->notify(new CompletedTechnicalServiceForEmployee($technicalService->fresh()));
        $technicalService->supplier->notify(new CompletedTechnicalServiceForSupplier($technicalService->fresh()));
        $technicalService->user->company->notify(new CompletedTechnicalServiceForCompany($technicalService->fresh()));

        return $technicalService;
    }

    public static function notifySysAdmins(Notification $notification)
    {
        $systemAdmins = \App\System\Models\User::query()->get();
        \Notification::send($systemAdmins, $notification);
    }

    public static function servicePDF()
    {
        $pdf = AuthHelper::user()->portal->servicePdf;
        $fileName = array_reverse(explode('/', $pdf));
        $pdf_content = StorageHelper::disk('public')->get($pdf);
        $response = response($pdf_content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-disposition' => 'inline; filename="' . $fileName[0] . '"',
            'Cache-Control' => ' public, must-revalidate, max-age=0',
            'Pragma' => 'public',
            'X-Generator' => 'mPDF',
            'Expires' => 'Sat, 26 Jul 1997 05:00:00 GMT',
            'Last-Modified' => gmdate('D, d M Y H:i:s') . ' GMT',
        ]);
        return $response;
    }

    private static function checkContractForTechnicalService(Contract $contract)
    {
        if (!$contract->isActive()) {
            throw new ContractIsNotActiveException();
    }
        if (!empty($contract->lastTechnicalService()) && $contract->lastTechnicalService()->statusId != TechnicalService::STATUS_SUCCESSFUL) {
            throw new TechnicalServiceInProgressException();
        }
    }
}
