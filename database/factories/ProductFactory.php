<?php

use Faker\Generator as Faker;

$factory->define(\App\Portal\Models\Product::class, function (Faker $faker) {
    return [
        'image' => $faker->imageUrl(),
        'color' => $faker->colorName,
        'size'  => $faker->colorName,
    ];
});
