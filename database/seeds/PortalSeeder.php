<?php

use Illuminate\Database\Seeder;

class PortalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     */
    public function run()
    {
        $cityId = DB::table('cities')->first()->id;
        $portal = app(\App\Models\Portal::class)->newInstance([
            'name' => 'test Portal mercator.test',
            'domain' => 'portal.mercator.test',
            'admin_first_name' => 'Test',
            'admin_last_name' => 'Test',
            'admin_email' => 'test@test.com',
            'company_name' => 'Test',
            'company_city_id' => $cityId,
            'company_address' => 'Test address',
            'company_vat' => 'Test VAT',
            'status_id' => 3,
            'automatic_credit_note' => true,
            'allow_employee_offer_creation' => true,
            'subdomain' => 'portal',
        ]);
        $portal->save();
        $user = factory(\App\Portal\Models\User::class)->make([
            'code' => 'TST001',
            'first_name' => 'Test',
            'last_name' => 'PA',
            'email' => 'test@test.com',
            'password' => bcrypt('Aa123654'),
            'password_updated_at' => \Carbon\Carbon::now(),
            'status_id' => 1,
            'portal_id' => $portal->id,
        ]);
        $role = \App\Portal\Models\Role::findById(1);
        $user->save();
        $user->assignRole($role);
        $user->guard_name = 'portal';
        $user->givePermissionTo(\App\Models\Permission::EDIT_PORTAL_DATA);

        $portal = app(\App\Models\Portal::class)->newInstance([
            'name' => 'test Portal mercator.test',
            'domain' => 'portal2.mercator.test',
            'admin_first_name' => 'Test',
            'admin_last_name' => 'Test',
            'admin_email' => 'test2@test.com',
            'company_name' => 'Test',
            'company_city_id' => $cityId,
            'company_address' => 'Test address',
            'company_vat' => 'Test VAT',
            'status_id' => 3,
            'automatic_credit_note' => true,
            'allow_employee_offer_creation' => true,
            'subdomain' => 'portal2',
        ]);
        $portal->save();
        $user = factory(\App\Portal\Models\User::class)->make([
            'code' => 'TST002',
            'first_name' => 'Test2',
            'last_name' => 'PA',
            'email' => 'test2@test.com',
            'password' => bcrypt('Aa123654'),
            'password_updated_at' => \Carbon\Carbon::now(),
            'status_id' => 1,
            'portal_id' => $portal->id,
        ]);
        $role = \App\Portal\Models\Role::findById(1);
        $user->save();
        $user->assignRole($role);
        $user->guard_name = 'portal';
        $user->givePermissionTo(\App\Models\Permission::EDIT_PORTAL_DATA);
    }
}
