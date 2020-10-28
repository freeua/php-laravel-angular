<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangePortalUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::table('portal_users', function (Blueprint $table) {
            $table->string('email')->unique()->change();
        });
        \App\Models\Permission::create([
            'name' => \App\Models\Permission::EDIT_PORTAL_DATA,
            'guard_name' => 'portal',
            'label' => 'Benutzer darf bearbeiten',
        ]);

        \App\Portal\Models\User::query()->whereHas('roles', function($query) {
            $query->where('name', \App\Portal\Models\Role::ROLE_PORTAL_ADMIN);
        })
        ->each(function ($user) {
            $user->guard_name = 'portal';
            $user->givePermissionTo(\App\Models\Permission::EDIT_PORTAL_DATA);
        });
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
