<?php

use Faker\Generator as Faker;

$factory->define(\App\Portal\Models\Offer::class, function (Faker $faker) {
    return [
        'number' => $faker->numberBetween(0,100000),
        'normal_price' => $faker->randomFloat(2, 100, 10000),
        'discount_price' => $faker->randomFloat(2, 10, 100),
        'accessories_price' => $faker->randomFloat(2,0, 1000),
        'expired_date' => $faker->dateTimeBetween('+1 day', '+3 months'),
        'product_notes' => $faker->text(),
        'notes' => $faker->text(),
        'contract_data' => $faker->text(),
        'contract_file' => $faker->url(),
    ];
});
