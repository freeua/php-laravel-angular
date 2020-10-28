<?php

use Faker\Generator as Faker;

$factory->define(\App\Portal\Models\ProductModel::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->text(15),
    ];
});
