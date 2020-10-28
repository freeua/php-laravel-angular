<?php

use Carbon\CarbonInterval;
use Faker\Generator as Faker;
use Illuminate\Support\Carbon;
use App\Portal\Models\Contract;

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
$factory->define(Contract::class, function (Faker $faker) {
    $acceptedAt = Carbon::instance($faker->dateTimeBetween('-12 months'));
    return [
        'number' => $faker->randomNumber(),
        'username' => $faker->userName,
        'pickup_code' => $faker->postcode,
        'start_date' => $acceptedAt->firstOfMonth(),
        'end_date' => $acceptedAt->add(CarbonInterval::months(12)),
        'status_id' => Contract::STATUS_ACTIVE,
        'agreed_purchase_price' => $faker->randomFloat(2, 10, 100000),
        'leasing_rate_type' => $faker->randomElement(['fixed', 'percentage']),
        'leasing_rate' => $faker->randomFloat(2, 10, 100000),
        'insurance_type' => $faker->randomElement(['fixed', 'percentage']),
        'insurance' => $faker->randomFloat(2, 10, 100000),
        'calculated_residual_value' => $faker->randomFloat(2, 10, 100000),
        'leasing_period' => 12,
        'product_size' => 'XL',
        'product_notes' => $faker->text,
        'notes' => $faker->text,
        'accepted_at' => $acceptedAt,
    ];
});
