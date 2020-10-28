<?php

use Illuminate\Database\Seeder;

class BicicliSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->addPortal();
    }

    private function addPortal() {
        $cityId = DB::table('cities')->first()->id;
        $portal = app(\App\Models\Portal::class)->newInstance([
            'name' => 'Bicicli',
            'domain' => 'bicicli.sandbox.rcdevelopment.de',
            'admin_first_name' => 'Bicicli',
            'admin_last_name' => 'PA',
            'admin_email' => 'bicicli@sandbox.rcdevelopment.de',
            'company_name' => 'Bicicli',
            'company_city_id' => $cityId,
            'company_address' => 'Test address',
            'company_vat' => 'Test local.VAT',
            'status_id' => 3,
        ]);
        $portal->save();
        $role = \App\Portal\Models\Role::findById(1);
        $user = factory(\App\Portal\Models\User::class)->make([
            'code' => 'TST001',
            'first_name' => 'Bicicli',
            'last_name' => 'PA',
            'email' => 'bicicli@sandbox.rcdevelopment.de',
            'password' => bcrypt('Aa123654'),
            'password_updated_at' => \Carbon\Carbon::create(),
            'status_id' => 1,
            'portal_id' => $portal->id,
        ]);
        $user->save();
        $user->assignRole($role);
    }
}
