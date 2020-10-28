<?php

use Faker\Generator as Faker;

$factory->define(Model::class, function (Faker $faker) {
    return [
        'number' => $faker->numberBetween(0,100000),
        'username' => $faker->userName,
        'company_name' => $faker->company,
        'product_name' => $faker->company,
        'pickup_code' => $faker->postcode,
        'date' => $faker->dateTimeBetween('now', '+3 months'),
        'zip' => $faker->postcode,
        'address' => $faker->address,
        'agreed_purchase_price' => $faker->randomFloat(2, 10, 100000),
        'leasing_rate_type' => $faker->randomFloat(2, 10, 100000),
        'leasing_rate' => $faker->randomFloat(2, 10, 100000),
        'insurance_type' => $faker->randomFloat(2, 10, 100000),
        'insurance' => $faker->randomFloat(2, 10, 100000),
        'calculated_residual_value' => $faker->randomFloat(2, 10, 100000),
    ];
});
