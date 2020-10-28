<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Portal\Models\Offer;

class AddOfferStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::table('statuses')->insert([
            'id' => 20,
            'label' => 'Überlassungsvertrag akzeptiert',
            'type' => 'success',
            'icon' => 'done',
            'table' => 'offers'
        ]);

        \DB::table('statuses')->where('id', 10)->update([
            'type' => 'warning',
        ]);
        $offers = \App\Portal\Models\Offer::all();
        $offersToUpdate = $offers
            ->filter(function ($offer) {
                return isset($offer->order);
            })
            ->map(function ($offer) {
                return $offer->id;
            });
        Offer::query()->whereIn('id', $offersToUpdate)->update(['status_id' => Offer::STATUS_CONTRACT_APPROVED]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::table('statuses')->delete(20);
    }
}
