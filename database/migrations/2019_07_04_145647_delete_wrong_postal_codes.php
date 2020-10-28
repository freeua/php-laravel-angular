<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteWrongPostalCodes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $citiesWithoutPostalCode = \DB::select('select cities.id from cities left join postal_codes on cities.id = postal_codes.city_id GROUP BY cities.id having count(postal_codes.id) = 0');
        $users = \App\Portal\Models\User::withTrashed()->get();
        $postalCodes = \App\Models\PostalCode::all()->keyBy('code');
        \DB::table('portal_users')->where('city_id', 221)->update(['city_id' => 2691]);
        foreach ($citiesWithoutPostalCode as $city) {
            $usersOfCity = $users->filter(function ($user) use ($city) {
                return strval($user->city_id) == strval($city->id);
            })->all();
            foreach ($usersOfCity as $user) {
                $postalCode = $postalCodes->get($user->postal_code);
                if (isset($postalCode)) {
                    $user->update(['city_id' => $postalCodes->get($user->postal_code)->city_id]);
                    $user->update(['city_id' => $postalCodes->get($user->postal_code)->city_id]);   } else {
                    $user->update(['city_id' => null]);
                }
            }
            \DB::table('cities')->delete($city->id);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
