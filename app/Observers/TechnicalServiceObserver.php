<?php

namespace App\Observers;

use App\Modules\TechnicalServices\Models\TechnicalService;

class TechnicalServiceObserver
{
    /**
     * Handle the offer "created" event.
     *
     * @param  TechnicalService  $technicalService
     * @return void
     */
    public function created(TechnicalService $technicalService)
    {
        $technicalService->number = $technicalService->generateCode($technicalService->id, 7, '', 'SC-');
        $technicalService->save();
    }
}
