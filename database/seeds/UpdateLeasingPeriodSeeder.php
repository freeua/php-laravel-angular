<?php

use Illuminate\Database\Seeder;
use App\Portal\Models\Order;
use App\Portal\Models\Contract;

/**
 * Class UpdateLeasingPeriodSeeder
 */
class UpdateLeasingPeriodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (Order::all() as $row) {
            $row->leasingPeriod = intval($row->leasingPeriod);
            $row->save();
        }

        foreach (Contract::all() as $row) {
            $row->leasingPeriod = intval($row->leasingPeriod);
            $row->save();
        }
    }
}
