<?php

use App\Models\City;
use App\Models\Portal;
use Faker\Generator as Faker;

$factory->define(\App\Models\Companies\Company::class, function (Faker $faker) {
    $companyName = $faker->company;
    $firstName = $faker->firstName;
    $lastName = $faker->lastName;
    $slug = preg_replace("/[^A-Za-z0-9]/", '', $companyName);
    $email = preg_replace("/[^A-Za-z0-9]/", '', $firstName) . '.'
        . preg_replace("/[^A-Za-z0-9]/", '', $lastName) . '@'
        . $slug . '.com';

    return [
        'code' => 'TST-' . $faker->randomDigitNotNull,
        'name' => $companyName,
        'color' => $faker->randomElement(['#50e3c2',
            '#6fc213',
            '#f9a110',
            '#4a90e2',
            '#ec4640',
            '#cc56e4',
            '#b8e986',
            '#91b6e0',
            '#ffabb5',
            '#d1935c']),
        'slug' => strtolower(preg_replace("/[^A-Za-z0-9]/", '-', $companyName)),
        'vat' => $faker->randomNumber(9),
        'invoice_type' => $faker->randomElement(['net', 'gross']),
        'admin_first_name' => $firstName,
        'admin_last_name' => $lastName,
        'admin_email' => $email,
        'zip' => $faker->postcode,
        'city_id' => function () {
            $city = app(City::class)->first();
            if (!$city) {
                $city = factory(City::class)->create();
            }
            return $city->id;
        },
        'address' => $faker->address,
        'phone' => '+' . $faker->numberBetween(2000000000),
        'leasing_budget' => $faker->randomFloat(2, 1000, 10000000),
        'max_user_contracts' => $faker->numberBetween(1, 20),
        'max_user_amount' => $faker->numberBetween(500, 100000),
        'insurance_covered' => 1,
        'insurance_covered_type' => $faker->randomElement(['fixed', 'percentage']),
        'insurance_covered_amount' => $faker->randomElement([2,4,6,8,10]),
        'maintenance_covered' => 1,
        'maintenance_covered_type' => $faker->randomElement(['fixed', 'percentage']),
        'maintenance_covered_amount' => $faker->randomElement([2,4,6,8,10]),
        'leasing_rate' => 1,
        'leasing_rate_type' => $faker->randomElement(['fixed', 'percentage']),
        'leasing_rate_amount' => $faker->randomElement([2,4,6,8,10]),
        'status_id' => \App\Models\Companies\Company::STATUS_ACTIVE,
    ];
});
