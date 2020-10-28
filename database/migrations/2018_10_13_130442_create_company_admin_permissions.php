<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyAdminPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('permissions', function (Blueprint $table) {
           $table->string('label');
        });
        \App\Models\Permission::create(['guard_name' => 'company', 'name' => 'Read Company Data', 'label' => 'Lesen']);
        \App\Models\Permission::create(['guard_name' => 'company', 'name' => 'Edit Company Data', 'label' => 'Bearbeiten']);
        \App\Models\Permission::create(['guard_name' => 'company', 'name' => 'Manage Company Employees',
            'label' => 'Mitarbeiter freischalten']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \App\Models\Permission::query()->where('name', '=', 'Read Company Data')->delete();
        \App\Models\Permission::query()->where('name', '=', 'Edit Company Data')->delete();
        \App\Models\Permission::query()->where('name', '=', 'Manage Company Employees')->delete();
    }
}
