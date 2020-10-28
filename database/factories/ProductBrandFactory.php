<?php

use Faker\Generator as Faker;

$factory->define(\App\Portal\Models\ProductBrand::class, function (Faker $faker) {
    $brands = ['Derby Cycle', 'YT Industries', 'Alder', 'Brennabor', 'Birdy', 'Canyon bicycles', 'Diamant', 'Focus Bikes', 'Hoffman', 'Heinkel'];
    return [
        'name' => $faker->unique()->randomElement($brands),
    ];
});
