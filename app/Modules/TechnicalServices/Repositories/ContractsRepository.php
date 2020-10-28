<?php


namespace App\Modules\TechnicalServices\Repositories;

use App\Models\Rates\ServiceRate;
use App\Modules\TechnicalServices\Models\TechnicalService;
use App\Portal\Models\Contract;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class ContractsRepository
{

    public static function contractsForFirstInspectionHalfYear()
    {
        $query = Contract::query()
            ->where('end_date', '>', Carbon::now())
            ->where('start_date', '<', Carbon::now())
            ->where('status_id', '!=', Contract::STATUS_CANCELED)
            ->where('service_rate_modality', ServiceRate::INSPECTION)
            ->whereHas('order', function (Builder $query) {
                return $query
                    ->whereDate('picked_up_at', '<=', Carbon::now()->subMonthsNoOverflow(6));
            })
            ->whereDoesntHave('technicalServices');

        return $query->get();
    }

    public static function contractsYearlyInspection()
    {
        $query = Contract::query()
            ->where('end_date', '>', Carbon::now())
            ->where('start_date', '<', Carbon::now())
            ->where('status_id', '!=', Contract::STATUS_CANCELED)
            ->where('service_rate_modality', ServiceRate::INSPECTION)
            ->whereHas('order', function (Builder $query) {
                return $query
                    ->whereDate('picked_up_at', '<=', Carbon::now()->subMonthsNoOverflow(12));
            })
            ->whereHas('technicalServices')
            ->whereHas('technicalServices', function (Builder $query) {
                return $query
                    ->where('status_id', TechnicalService::STATUS_SUCCESSFUL)
                    ->whereDate('status_updated_at', '<=', Carbon::now()->subMonthsNoOverflow(12))
                    ->latest('created_at')
                    ->limit(1);
            })
            ->whereDoesntHave('technicalServices', function (Builder $query) {
                return $query
                    ->where('status_id', '!=', TechnicalService::STATUS_SUCCESSFUL);
            });
        return $query->get();
    }
}
