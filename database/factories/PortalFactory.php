<?php

use Faker\Generator as Faker;
use App\Models\City;
use App\Models\Portal;

$factory->define(Portal::class, function (Faker $faker) {
    return [
        'name' => $faker->company,
        'domain' => $faker->domainName,
        'admin_first_name' => 'Test',
        'admin_last_name' => 'Test',
        'admin_email' => 'test@test.com',
        'company_name' => 'Test',
        'company_city_id' => function() {
            $city = app(City::class)->first();
            if (!$city) {
                $city = factory(City::class)->create();
            }
            return $city->id;
        },
        'company_address' => 'Test address',
        'company_vat' => 'Test VAT',
        'status_id' => 3,
    ];
});
