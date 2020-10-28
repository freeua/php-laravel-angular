<?php

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            ['name' => 'Portal Admin', 'guard_name' => 'api'],
            ['name' => 'Company Admin', 'guard_name' => 'api'],
            ['name' => 'Supplier Admin', 'guard_name' => 'api'],
            ['name' => 'Employee', 'guard_name' => 'api'],
        ]);
    }
}
