<?php

use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Models\Companies\Company::class, 1)->make()->each(function (\App\Models\Companies\Company $company) {
            // Company Admin
            $company->admin_email = 'company@company.com';
            $company->name = 'Test Company';
            $company->slug = 'test-company';
            $company->slug = 'test-company';
            $company->portal_id = 1;
            $company->end_contract = new \Carbon\Carbon('next year');
            $company->save();
            $companyAdminRole = \App\Portal\Models\Role::findById(2);
            $employeeRole = \App\Portal\Models\Role::findById(4);
            /** @var \App\Portal\Models\User $user */
            $user = factory(\App\Portal\Models\User::class)->make([
                'first_name' => 'Company',
                'last_name' => 'Admin',
                'email' => $company->admin_email,
                'company_id' => $company->id,
                'portal_id' => $company->portal->id,
            ]);
            $user->save();
            $user->assignRole($companyAdminRole);
            $user->guard_name = 'company';
            $user->givePermissionTo(['Edit Company Data', 'Manage Company Employees', 'Read Company Data']);
            $user->save();

            // Employees
            $user = factory(\App\Portal\Models\User::class)->make([
                'first_name' => 'Employee',
                'last_name' => 'Admin',
                'email' => 'employee_'.$company->admin_email,
                'company_id' => $company->id,
                'portal_id' => $company->portal->id,
            ]);
            $user->save();
            $user->assignRole($employeeRole);
            $user->assignRole($companyAdminRole);
            $user->guard_name = 'company';
            $user->givePermissionTo(['Edit Company Data', 'Manage Company Employees', 'Read Company Data']);
            $user->save();

            // Employees
            $users = factory(\App\Portal\Models\User::class, 3)->make([
                'company_id' => $company->id,
                'portal_id' => $company->portal->id,
            ]);

            $users->each(function (\App\Portal\Models\User $user) use ($employeeRole) {
                $user->save();
                $user->assignRole($employeeRole);
            });
            $company->save();
            \App\Models\ProductCategory::all()->each(function (\App\Models\ProductCategory $productCategory) use ($company) {
                $companyProductCategory = new \App\Portal\Models\CompanyProductCategory([
                    'company_id' => $company->id,
                    'category_id' => $productCategory->id,
                    'status' => true
                ]);

                $companyProductCategory->saveOrFail();
            });
        });

        factory(\App\Models\Companies\Company::class, 1)->make()->each(function (\App\Models\Companies\Company $company) {
            // Company Admin
            $company->admin_email = 'company2@company.com';
            $company->name = 'Test Company';
            $company->slug = 'test-company';
            $company->slug = 'test-company';
            $company->slug = 'test-company';
            $company->portal_id = 2;
            $company->end_contract = new \Carbon\Carbon('next year');
            $company->save();
            $companyAdminRole = \App\Portal\Models\Role::findById(2);
            $employeeRole = \App\Portal\Models\Role::findById(4);
            /** @var \App\Portal\Models\User $user */
            $user = factory(\App\Portal\Models\User::class)->make([
                'first_name' => 'Company',
                'last_name' => 'Admin',
                'email' => $company->admin_email,
                'company_id' => $company->id,
                'portal_id' => $company->portal->id,
            ]);
            $user->save();
            $user->assignRole($companyAdminRole);
            $user->guard_name = 'company';
            $user->givePermissionTo(['Edit Company Data', 'Manage Company Employees', 'Read Company Data']);

            // Employees
            $user = factory(\App\Portal\Models\User::class)->make([
                'first_name' => 'Employee',
                'last_name' => 'Admin',
                'email' => 'employee2_'.$company->admin_email,
                'company_id' => $company->id,
                'portal_id' => $company->portal->id,
            ]);
            $user->save();
            $user->assignRole($employeeRole);
            $user->assignRole($companyAdminRole);
            $user->guard_name = 'company';
            $user->givePermissionTo(['Edit Company Data', 'Manage Company Employees', 'Read Company Data']);

            // Employees
            $users = factory(\App\Portal\Models\User::class, 3)->make([
                'company_id' => $company->id,
                'portal_id' => $company->portal->id,
            ]);

            $users->each(function (\App\Portal\Models\User $user) use ($employeeRole) {
                $user->save();
                $user->assignRole($employeeRole);
            });
            $company->save();
            \App\Models\ProductCategory::all()->each(function (\App\Models\ProductCategory $productCategory) use ($company) {
                $companyProductCategory = new \App\Portal\Models\CompanyProductCategory([
                    'company_id' => $company->id,
                    'category_id' => $productCategory->id,
                    'status' => true
                ]);

                $companyProductCategory->saveOrFail();
            });
        });
    }
}
