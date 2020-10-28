<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeAgreedPurchaseLength extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE offers MODIFY COLUMN agreed_purchase_price double');
        DB::statement('ALTER TABLE orders MODIFY COLUMN agreed_purchase_price double');
        DB::statement('ALTER TABLE contracts MODIFY COLUMN agreed_purchase_price double');
        DB::statement('ALTER TABLE offers MODIFY COLUMN accessories_price double');
        DB::statement('ALTER TABLE orders MODIFY COLUMN accessories_price double');
        DB::statement('ALTER TABLE contracts MODIFY COLUMN accessories_price double');
        DB::statement('ALTER TABLE offers MODIFY COLUMN accessories_discounted_price double');
        DB::statement('ALTER TABLE orders MODIFY COLUMN accessories_discounted_price double');
        DB::statement('ALTER TABLE contracts MODIFY COLUMN accessories_discounted_price double');
        DB::statement('ALTER TABLE orders MODIFY COLUMN insurance_rate double');
        DB::statement('ALTER TABLE contracts MODIFY COLUMN insurance_rate double');
        DB::statement('ALTER TABLE orders MODIFY COLUMN service_rate double');
        DB::statement('ALTER TABLE contracts MODIFY COLUMN service_rate double');
        DB::statement('ALTER TABLE orders MODIFY COLUMN leasing_rate double');
        DB::statement('ALTER TABLE contracts MODIFY COLUMN leasing_rate double');
        DB::statement('ALTER TABLE orders MODIFY COLUMN leasing_rate_subsidy double');
        DB::statement('ALTER TABLE contracts MODIFY COLUMN leasing_rate_subsidy double');
        DB::statement('ALTER TABLE orders MODIFY COLUMN insurance_rate_subsidy double');
        DB::statement('ALTER TABLE contracts MODIFY COLUMN insurance_rate_subsidy double');
        DB::statement('ALTER TABLE orders MODIFY COLUMN service_rate_subsidy double');
        DB::statement('ALTER TABLE contracts MODIFY COLUMN service_rate_subsidy double');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
