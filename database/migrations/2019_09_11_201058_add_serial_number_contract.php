<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use \App\Portal\Models\Contract;

class AddSerialNumberContract extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->string('serial_number');
        });
        Contract::query()
            ->each(function (Contract $contract) {
                $contract->serialNumber = $contract->order->frame_number;
                $contract->saveOrFail();
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn('serial_number');
        });
    }
}
