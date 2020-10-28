<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\LeasingCondition::class, function (Faker $faker) {
    return [
        'factor' => $faker->randomElement([5,10,15,20]),
        'period' => $faker->randomElement([36, 48, 60]),
        'residualValue' => $faker->randomElement([5,10,15,20]),
    ];
});
