<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Class UsersTableSeeder
 */
class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'code' => 'TST-0001',
            'first_name' => 'admin',
            'last_name' => 'admin',
            'email' => 'test@test.com',
            'password' => bcrypt('Aa123654'),
            'status_id' => 1,
            'password_updated_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
        DB::table('settings')->where('key', 'domain')->update(['value' => 'mercator.test', 'active' => 1 ]);
        DB::table('settings')->where('key', 'email')->update(['value' => 'test@test.com', 'active' => 1 ]);
        DB::table('settings')->where('key', 'color')->update(['value' => '#ec4640', 'active' => 1 ]);
        DB::table('settings')->where('key', 'timezone')->update(['value' => 'Europe/Berlin', 'active' => 1 ]);
    }
}
