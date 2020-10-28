<?php

use Faker\Generator as Faker;

if (! function_exists('generateCode')) {
    function generateCode(
        string $name,
        int $lettersCount = 3,
        int $digitsCount = 3,
        string $delimiter = '',
        string $prefix = '',
        string $lastId = ''
    ): string {
        $code = $prefix . strtoupper(substr(preg_replace('/\W/', '', $name), 0, $lettersCount)) . $delimiter;
        $code .= str_pad(++$lastId, $digitsCount, '0', STR_PAD_LEFT);

        return $code;
    }
}


$factory->define(App\System\Models\User::class, function (Faker $faker) {
    $name = $faker->firstName;
    return [
        'code' => generateCode($name),
        'first_name' => $name,
        'last_name' => $faker->lastName,
        'email' => $faker->unique()->companyEmail,
        'password' => bcrypt('Aa123654'),
        'password_updated_at' => \Carbon\Carbon::now(),
        'status_id' => 1,
    ];
});
