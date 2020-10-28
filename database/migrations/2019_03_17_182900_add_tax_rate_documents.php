<?php

use App\Portal\Models\Contract;
use App\Portal\Models\Offer;
use App\Portal\Models\Order;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTaxRateDocuments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::table('offers', function (Blueprint $table) {
            $table->float('tax_rate', 16, 2)->after('accessories_price');
        });
        \Schema::table('orders', function (Blueprint $table) {
            $table->float('tax_rate', 16, 2)->after('accessories_price');
        });
        \Schema::table('contracts', function (Blueprint $table) {
            $table->float('tax_rate', 16, 2)->after('accessories_price');
        });
        Offer::query()->each(function(Offer $offer) {
            $offer->update([
                'taxRate' => floor(($offer->productListPrice + $offer->accessoriesPrice) * 0.005)
            ]);
        });
        Order::query()->each(function(Order $order) {
            $order->update([
                'tax_rate' => floor(($order->productListPrice + $order->accessoriesPrice) * 0.005)
            ]);
        });
        Contract::query()->each(function(Contract $contract) {
            $contract->update([
                'tax_rate' => floor(($contract->productListPrice + $contract->accessoriesPrice) * 0.005)
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Schema::table('offers', function (Blueprint $table) {
            $table->dropColumn('tax_rate');
        });
        \Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('tax_rate');
        });
        \Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn('tax_rate');
        });
    }
}
