<?php

use Illuminate\Database\Seeder;

class PartnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $partner = new \App\Partners\Models\Partner([
            'name' => 'Pexco Dev',
            'oauth_client_id' => 'partner-client',
            'bike_configurator_url' => 'http://dev.pexco-bikes.com/#/de/order?token={token}',
            'info_iframe_url' => 'http://dev.pexco-bikes.com/#/de/orderinfo?token={token}',
            'configurator_menu_text' => 'Bike Berater',
            'calculator_cid' => '5d317295c9ad7',
        ]);
        $partner->saveOrFail();
        \App\Models\Portal::find(1)->update(['partner_id' => $partner->id]);
    }
}
