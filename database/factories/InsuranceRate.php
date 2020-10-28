<?php

use Faker\Generator as Faker;
use Illuminate\Support\Carbon;

$factory->define(\App\Models\Rates\InsuranceRate::class, function (Faker $faker) {
    return [
        'name' => $faker->lastName,
        'amountType' => $faker->randomElement(['fixed', 'percentage']),
        'amount' => $faker->randomElement([5,10,15,20]),
        'minimum' => $faker->numberBetween(1, 48),
        'activeAt' => Carbon::now(new \DateTimeZone('Europe/Berlin')),
    ];
});
