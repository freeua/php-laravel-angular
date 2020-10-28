<?php

use Faker\Generator as Faker;


$factory->define(\App\Portal\Models\User::class, function (Faker $faker) {

    $name = $faker->firstName;
    return [
        'code' => null,
        'portal_id' => null,
        'first_name' => $name,
        'last_name' => $faker->lastName,
        'email' => $faker->unique()->companyEmail,
        'password' => bcrypt('Aa123654'),
        'password_updated_at' => \Carbon\Carbon::now(),
        'status_id' => 1,
        'remember_token' => str_random(10),
        'city_id' => 1,
        'postal_code' => '10115',
        'phone' => '123123123',
        'street' => $faker->address,
        'employee_number' => 'ID-123',
        'salutation' => 'herr',
    ];
});
