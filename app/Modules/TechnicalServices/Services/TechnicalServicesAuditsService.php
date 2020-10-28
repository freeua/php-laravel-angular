<?php


namespace App\Modules\TechnicalServices\Services;

use App\Models\Audit;
use App\Models\Rates\ServiceRate;
use App\Modules\TechnicalServices\Models\TechnicalService;
use App\Portal\Helpers\AuthHelper;

class TechnicalServicesAuditsService
{

    public static function technicalServiceReady(TechnicalService $technicalService, $user)
    {

        $isInspection = $technicalService->serviceModality == ServiceRate::INSPECTION;
        $audit = self::baseFromTechnicalService($technicalService, $user);

        if ($isInspection == true) {
            $audit->fill([
                'description' => "Inspektion {$technicalService->number} als bereit markiert",
                'visibility' => Audit::VISIBLE_ALL,
                'type' => Audit::TECHNICAL_SERVICE_CREATED,
            ]);
        } else {
            $audit->fill([
                'description' => "Service {$technicalService->number} als bereit markiert",
                'visibility' => Audit::VISIBLE_ALL,
                'type' => Audit::TECHNICAL_SERVICE_CREATED,
            ]);
        }
        return $audit->saveOrFail();
    }

    public static function technicalServiceAccepted(TechnicalService $technicalService, $user)
    {

        $isInspection = $technicalService->serviceModality == ServiceRate::INSPECTION;
        $audit = self::baseFromTechnicalService($technicalService, $user);

        if ($isInspection == true) {
            $audit->fill([
                'description' => "Inspektion {$technicalService->number} wurde akzeptiert",
                'visibility' => Audit::VISIBLE_ALL,
                'type' => Audit::TECHNICAL_SERVICE_CREATED,
            ]);
        } else {
            $audit->fill([
                'description' => "Service {$technicalService->number} wurde akzeptiert",
                'visibility' => Audit::VISIBLE_ALL,
                'type' => Audit::TECHNICAL_SERVICE_CREATED,
            ]);
        }
        return $audit->saveOrFail();
    }

    public static function technicalServiceCompleted(TechnicalService $technicalService, $user)
    {

        $isInspection = $technicalService->serviceModality == ServiceRate::INSPECTION;
        $audit = self::baseFromTechnicalService($technicalService, $user);

        if ($isInspection == true) {
            $audit->fill([
                'description' => "Inspektion {$technicalService->number} wurde abgeschlossen",
                'visibility' => Audit::VISIBLE_ALL,
                'type' => Audit::TECHNICAL_SERVICE_CREATED,
            ]);
        } else {
            $audit->fill([
                'description' => "Service {$technicalService->number} wurde abgeschlossen",
                'visibility' => Audit::VISIBLE_ALL,
                'type' => Audit::TECHNICAL_SERVICE_CREATED,
            ]);
        }
        return $audit->saveOrFail();
    }

    public static function technicalServiceCreated(TechnicalService $technicalService, $user)
    {

        $isInspection = $technicalService->serviceModality == ServiceRate::INSPECTION;
        $audit = self::baseFromTechnicalService($technicalService, $user);

        if ($isInspection == true) {
            $audit->fill([
                'description' => "Inspektion {$technicalService->number} wurde erstellt",
                'visibility' => Audit::VISIBLE_ALL,
                'type' => Audit::TECHNICAL_SERVICE_CREATED,
            ]);
        } else {
            $audit->fill([
                'description' => "Service {$technicalService->number} wurde erstellt",
                'visibility' => Audit::VISIBLE_ALL,
                'type' => Audit::TECHNICAL_SERVICE_CREATED,
            ]);
        }
        return $audit->saveOrFail();
    }

    public static function technicalServiceGeneratedInspectionCode(TechnicalService $technicalService, $user)
    {

        $isInspection = $technicalService->serviceModality == ServiceRate::INSPECTION;
        $audit = self::baseFromTechnicalService($technicalService, $user);

        if ($isInspection == true) {
            $audit->fill([
                'description' => "Prüfcode wurde für die Prüfung {$technicalService->number} generiert",
                'visibility' => Audit::VISIBLE_ALL,
                'type' => Audit::TECHNICAL_SERVICE_CREATED,
            ]);
        } else {
            $audit->fill([
                'description' => "Prüfcode für den Service {$technicalService->number} wurde generier",
                'visibility' => Audit::VISIBLE_ALL,
                'type' => Audit::TECHNICAL_SERVICE_CREATED,
            ]);
        }
        return $audit->saveOrFail();
    }

    public static function technicalServiceGeneratedCreditNote(TechnicalService $technicalService, $user)
    {

        $isInspection = $technicalService->serviceModality == ServiceRate::INSPECTION;
        $audit = self::baseFromTechnicalService($technicalService, $user);

        if ($isInspection == true) {
            $audit->fill([
                'description' => "Credit note for Inspection {$technicalService->number} was generated",
                'visibility' => Audit::VISIBLE_ALL,
                'type' => Audit::TECHNICAL_SERVICE_CREATED,
            ]);
        } else {
            $audit->fill([
                'description' => "Credit note for Service {$technicalService->number} was generated",
                'visibility' => Audit::VISIBLE_ALL,
                'type' => Audit::TECHNICAL_SERVICE_CREATED,
            ]);
        }
        return $audit->saveOrFail();
    }

    public static function baseFromTechnicalService(TechnicalService $technicalService, $user): Audit
    {
        $userId = AuthHelper::id() ?? null;

        return new Audit([
            'user_id' => !empty($user) ? $user->id : null,
            'model_type' => TechnicalService::class,
            'model_id' => $technicalService->id,
            'created_by' => $userId
        ]);
    }
}
