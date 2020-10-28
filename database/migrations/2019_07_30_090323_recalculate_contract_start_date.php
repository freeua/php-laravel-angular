<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use \App\Portal\Models\Contract;

class RecalculateContractStartDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Contract::all()->each(function (Contract $contract) {
            $pickedDate = $contract->order->pickedUpAt;
            $contract->startDate = $pickedDate->day === 1 ? $pickedDate->startOfMonth() : Carbon::now()->startOfMonth()->addMonth();
            $contract->endDate = $contract->startDate->copy()->addMonths($contract->order->leasingPeriod)->subDays(1);
            $contract->saveOrFail();
        });
    }

}
