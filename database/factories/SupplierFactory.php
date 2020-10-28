<?php

use Faker\Generator as Faker;

$factory->define(\App\Portal\Models\Supplier::class, function (Faker $faker) {
    return [
        'code' => 'TST' . $faker->unique()->randomNumber(5),
        'city_id' => function() {
            $city = app(\App\Models\City::class)->first();
            if (!$city) {
                $city = factory(\App\Models\City::class)->create();
            }
            return $city->id;
        },
        'name' => $faker->name,
        'admin_first_name' => $faker->name,
        'admin_last_name' => $faker->lastName,
        'admin_email' => $faker->safeEmail,
        'address' => $faker->address,
        'zip' => $faker->postcode,
        'vat' => $faker->randomNumber(9),
        'phone' => $faker->phoneNumber,
        'status_id' => \App\Portal\Models\Supplier::STATUS_ACTIVE,
    ];
});
