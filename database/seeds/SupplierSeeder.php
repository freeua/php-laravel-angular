<?php

use App\Portal\Models\Role;
use App\Portal\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $companies = app(\App\Models\Companies\Company::class)->all();
        factory(Supplier::class, 1)
            ->create()
            ->each(function (Supplier $supplier) use ($companies) {
                $company = $companies->find(1);
                $supplier->companies()->save($company);
                $supplier->portals()->save($company->portal, ['status_id' => Supplier::STATUS_ACTIVE]);
                $company = $companies->find(2);
                $supplier->companies()->save($company);
                $supplier->portals()->save($company->portal, ['status_id' => Supplier::STATUS_ACTIVE]);

                $supplier->status_id = Supplier::STATUS_ACTIVE;
                // Supplier Users
                $user = factory(\App\Portal\Models\User::class)->make([
                    'email'=> 'supplier@supplier.com',
                    'supplier_id' => $supplier->id,
                    'portal_id' => $company->portal->id,
                ]);
                $userArray = $user->toArray();
                $userArray['password_bcrypt'] = $user->password;

                app(\App\Portal\Services\UserService::class)
                    ->create($userArray, Role::ROLE_SUPPLIER_ADMIN, $company->portal, false);
            });
        factory(Supplier::class, 10)
            ->create()
            ->each(function (Supplier $supplier) use ($companies) {
                $company = $companies->random();
                $supplier->companies()->save($company);
                $supplier->portals()->save($company->portal, ['status_id' => Supplier::STATUS_ACTIVE]);

                // Supplier Users
                $user = factory(\App\Portal\Models\User::class)->make([
                    'email'=> $supplier->admin_email,
                    'supplier_id' => $supplier->id,
                    'portal_id' => $company->portal->id,
                ]);
                $userArray = $user->toArray();
                $userArray['password_bcrypt'] = $user->password;

                app(\App\Portal\Services\UserService::class)
                    ->create($userArray, Role::ROLE_SUPPLIER_ADMIN, $company->portal, false);
            });
    }
}
