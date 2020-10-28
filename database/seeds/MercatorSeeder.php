<?php

use App\Portal\Models\Role;
use App\Portal\Models\Supplier;
use Illuminate\Database\Seeder;

class MercatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $portal = $this->addPortal();
        $company = $this->addCompany($portal);
        $this->addEmployees($company);
        $this->addSupplier($company);
    }

    private function addPortal() {
        $cityId = DB::table('cities')->first()->id;
        $portal = app(\App\Models\Portal::class)->newInstance([
            'name' => 'Mercator',
            'domain' => 'mercator.sandbox.rcdevelopment.de',
            'admin_first_name' => 'Test',
            'admin_last_name' => 'Test',
            'admin_email' => 'mercator@sandbox.rcdevelopment.de',
            'company_name' => 'Test',
            'company_city_id' => $cityId,
            'company_address' => 'Test address',
            'company_vat' => 'Test local.VAT',
            'status_id' => 3,
        ]);
        $portal->save();
        $role = \App\Portal\Models\Role::findById(1);
        $user = factory(\App\Portal\Models\User::class)->make([
            'code' => 'TST001',
            'first_name' => 'Mercator',
            'last_name' => 'PA',
            'email' => 'mercator@sandbox.rcdevelopment.de',
            'password' => bcrypt('Aa123654'),
            'password_updated_at' => \Carbon\Carbon::now(),
            'status_id' => 1,
            'portal_id' => $portal->id,
        ]);
        $user->save();
        $user->assignRole($role);
        return $portal;
    }

    private function addCompany($portal) {
        $logo = 'companies/6/logos/rfbn7FPnyXjdryIqA1eL5XJvKrDWwJuE0WGBYjg5.png';
        $company = factory(\App\Models\Companies\Company::class)->create();
        $company->admin_email = 'company@company.com';
        $company->name = 'MLF Mercator TEST 1';
        $company->slug = 'MLF01';
        $company->logo = $logo;
        $company->max_user_contracts = 10;
        $company->max_user_amount = 20000.00;
        $company->leasing_budget = 50000.00;
        $company->portal_id = $portal->id;
        $company->save();
        $companyAdminRole = \App\Portal\Models\Role::findById(2);
        /** @var \App\Portal\Models\User $user */
        $user = factory(\App\Portal\Models\User::class)->make([
            'first_name' => 'Roman',
            'last_name' => 'Herlitz',
            'email' => 'roman.herlitz@mercator-leasing.de',
            'company_id' => $company->id,
            'portal_id' => $company->portal->id,
        ]);
        $user->save();
        $user->assignRole($companyAdminRole);
        $user->guard_name = 'company';
        $user->givePermissionTo(['Edit Company Data', 'Manage Company Employees', 'Read Company Data']);
        return $company;
    }

    private function addEmployees($company) {
        $this->addEmployee($company, 'Luisa', 'Neiser','luisa.nieser@mercator-leasing.de');
        $this->addEmployee($company, 'Saskia', 'Fell','saskia.fell@mercator-leasing.de');
    }

    private function addEmployee($company, $name, $lastName, $email) {
        $companyAdminRole = \App\Portal\Models\Role::findById(2);
        $employeeRole = \App\Portal\Models\Role::findById(4);
        $user = factory(\App\Portal\Models\User::class)->make([
            'first_name' => $name,
            'last_name' => $lastName,
            'email' => $email,
            'company_id' => $company->id,
            'portal_id' => $company->portal->id,
        ]);
        $user->save();
        $user->assignRole($employeeRole);
        $user->assignRole($companyAdminRole);
        $user->guard_name = 'company';
        $user->givePermissionTo(['Edit Company Data', 'Manage Company Employees', 'Read Company Data']);

    }

    private function addSupplier($company) {
        $supplier = factory(Supplier::class)->create([
            'name' => 'MLF Bike Shop',
            'shop_name' => 'MLF Bike Shop',
            'admin_first_name' => 'Alexander',
            'admin_last_name' => 'Hahn',
            'admin_email' => 'alexander.hahn@mercator-leasing.de',
        ]);
        $supplier->companies()->save($company);
        $supplier->portals()->save($company->portal, ['status_id' => Supplier::STATUS_ACTIVE]);

        $supplier->status_id = Supplier::STATUS_ACTIVE;
        // Supplier Users
        $user = factory(\App\Portal\Models\User::class)->make([
            'email'=> 'alexander.hahn@mercator-leasing.de',
            'first_name' => 'Alexander',
            'last_name' => 'Hahn',
            'supplier_id' => $supplier->id,
            'portal_id' => $company->portal->id,
        ]);
        $userArray = $user->toArray();
        $userArray['password_bcrypt'] = $user->password;

        app(\App\Portal\Services\UserService::class)
            ->create($userArray, Role::ROLE_SUPPLIER_ADMIN, $company->portal, false);
    }
}
