<?php

use App\Portal\Models\Contract;
use App\System\Repositories\ContractRepository;
use Illuminate\Database\Seeder;

/**
 * Class FillContractProductDataSeeder
 */
class FillContractProductDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Usage: php artisan tenancy:db:seed --class=FillContractProductDataSeeder
     *
     * @return void
     */
    public function run()
    {
        $contractIds = Contract::all()->pluck('id')->toArray();

        $contractRepository = app(ContractRepository::class);

        foreach ($contractIds as $contractId) {
            $contract = $contractRepository->getContract($contractId);
            $contract->productBrand = $contract->product->brand->name;
            $contract->save();
        }
    }
}
