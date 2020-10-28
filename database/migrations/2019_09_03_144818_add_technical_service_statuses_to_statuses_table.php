<?php

use App\Modules\TechnicalServices\Models\TechnicalService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTechnicalServiceStatusesToStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('statuses', function (Blueprint $table) {
            DB::table('statuses')->insert([
                    'id' => 23,
                    'label' => 'in Bearbeitung',
                    'type' => 'success',
                    'table' => 'technical_services',
                ]
            );
            DB::table('statuses')->insert([
                    'id' => 24,
                    'label' => 'Abgeschlossen',
                    'type' => 'info',
                    'table' => 'technical_services',
                ]
            );
            DB::table('statuses')->insert([
                    'id' => 25,
                    'label' => 'Offen',
                    'type' => 'warning',
                    'table' => 'technical_services',
                ]
            );
            DB::table('statuses')->insert([
                    'id' => 26,
                    'label' => 'Vertrag wurde storniert',
                    'type' => 'danger',
                    'icon' => 'close',
                    'table' => 'technical_services'
                ]
            );

            TechnicalService::query()->where('status_id', 12)->update(['status_id' => 23]);
            TechnicalService::query()->where('status_id', 13)->update(['status_id' => 24]);
            TechnicalService::query()->where('status_id', 14)->update(['status_id' => 25]);
            TechnicalService::query()->where('status_id', 22)->update(['status_id' => 26]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('statuses', function (Blueprint $table) {

            TechnicalService::query()->where('status_id', 23)->update(['status_id' => 12]);
            TechnicalService::query()->where('status_id', 24)->update(['status_id' => 13]);
            TechnicalService::query()->where('status_id', 25)->update(['status_id' => 14]);
            TechnicalService::query()->where('status_id', 26)->update(['status_id' => 22]);

            DB::statement('SET FOREIGN_KEY_CHECKS = 0');
            DB::table('statuses')->where('id', 23)->delete();
            DB::table('statuses')->where('id', 24)->delete();
            DB::table('statuses')->where('id', 25)->delete();
            DB::table('statuses')->where('id', 26)->delete();
            DB::statement('SET FOREIGN_KEY_CHECKS = 1');
        });
    }
}
