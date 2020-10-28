<?php

use Faker\Generator as Faker;
use App\Models\City;

$factory->define(City::class, function (Faker $faker) {
    return [
        'name'  => $faker->city,
        'lat'   => $faker->randomFloat(4,0,360),
        'lng'   => $faker->randomFloat(4,0,360)
    ];
});
