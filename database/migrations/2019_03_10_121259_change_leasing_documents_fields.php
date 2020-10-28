<?php

use App\Portal\Models\Contract;
use App\Portal\Models\Order;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use \App\Portal\Models\Offer;

class ChangeLeasingDocumentsFields extends Migration
{

    public function up()
    {
        \Schema::table('offers', function (Blueprint $table) {
            $table->string('sender')->nullable()->after('insurance_rate_id');
            $table->string('supplier_name')->after('insurance_rate_id');
            $table->string('supplier_street')->after('insurance_rate_id');
            $table->string('supplier_postal_code')->after('insurance_rate_id');
            $table->string('supplier_city')->after('insurance_rate_id');
            $table->unsignedInteger('supplier_id')->nullable()->change();
            $table->string('employee_salutation')->nullable()->after('insurance_rate_id');
            $table->string('employee_name')->after('insurance_rate_id');
            $table->string('employee_street')->nullable()->after('insurance_rate_id');
            $table->string('employee_postal_code')->nullable()->after('insurance_rate_id');
            $table->string('employee_number')->nullable()->after('insurance_rate_id');
            $table->string('employee_email')->after('insurance_rate_id');
            $table->string('employee_phone')->nullable()->after('insurance_rate_id');
            $table->string('employee_city')->nullable()->after('insurance_rate_id');
            $table->string('product_brand')->after('insurance_rate_id');
            $table->string('product_model')->after('insurance_rate_id');
            $table->string('product_color')->after('insurance_rate_id');
            $table->string('product_size')->after('insurance_rate_id');
            $table->unsignedInteger('product_category_id')->after('insurance_rate_id');
            $table->string('offer_pdf')->nullable()->after('contract_file');
            $table->renameColumn('bike_list_price', 'product_list_price')->after('insurance_rate_id');
            $table->renameColumn('bike_discounted_price', 'product_discounted_price')->after('insurance_rate_id');
            $table->renameColumn('bike_discount', 'product_discount')->after('insurance_rate_id');
            $table->renameColumn('expired_date', 'expiry_date');
            $table->dropColumn(['contract_data']);
            $table->dropForeign('offers_supplier_user_id_foreign');
            $table->dropColumn('supplier_user_id');
            $table->unsignedInteger('product_id')->nullable()->change();
            $table->float('accessories_discounted_price')->after('accessories_price')->change();
            $table->float('agreed_purchase_price');
        });
        Offer::query()->get()->each(function (Offer $offer) {

            \DB::table('offers')
                ->where([
                    'id' => $offer->id,
                ])
                ->update([
                    'supplier_city' => $offer->supplier->city && $offer->supplier->city->name,
                    'supplier_name' => $offer->supplier->name,
                    'supplier_postal_code' => $offer->supplier->zip,
                    'supplier_street' => $offer->supplier->address,
                    'employee_city' => $offer->user->city && $offer->user->city->name,
                    'employee_salutation' => $offer->user->salutation,
                    'employee_name' => $offer->user->fullName,
                    'employee_postal_code' => $offer->user->postal_code,
                    'employee_street' => $offer->user->street,
                    'employee_email' => $offer->user->email,
                    'employee_phone' => $offer->user->phone,
                    'agreed_purchase_price' => $offer->accessoriesDiscountedPrice + $offer->productDiscountedPrice,
                    'product_category_id' => $offer->product->category_id,
                    'product_brand' => $offer->product->brand->name,
                    'product_model' => $offer->product->model->name,
                    'product_size' => $offer->product->size,
                    'product_color' => $offer->product->color,
                ]);
        });
        \Schema::table('offers', function (Blueprint $table) {
            $table->foreign('product_category_id')->on('product_categories')->references('id');
        });
        \Schema::table('orders', function (Blueprint $table) {
            $table->string('sender')->nullable()->after('agreed_purchase_price');
            $table->string('supplier_name')->after('agreed_purchase_price');
            $table->string('supplier_street')->after('agreed_purchase_price');
            $table->string('supplier_postal_code')->after('agreed_purchase_price');
            $table->string('supplier_city')->after('agreed_purchase_price');
            $table->string('employee_salutation')->after('agreed_purchase_price');
            $table->string('employee_name')->after('agreed_purchase_price');
            $table->string('employee_street')->after('agreed_purchase_price');
            $table->string('employee_postal_code')->after('agreed_purchase_price');
            $table->string('employee_number')->after('agreed_purchase_price');
            $table->string('employee_email')->after('agreed_purchase_price');
            $table->string('employee_phone')->after('agreed_purchase_price');
            $table->string('employee_city')->after('agreed_purchase_price');
            $table->unsignedInteger('supplier_id')->nullable()->change();
            $table->unsignedInteger('product_category_id')->after('product_name');
            $table->string('product_color')->after('product_name');
            $table->string('product_brand')->after('product_name');
            $table->string('product_model')->after('product_name');
            $table->renameColumn('bike_list_price', 'product_list_price')->after('product_name');
            $table->renameColumn('bike_discounted_price', 'product_discounted_price')->after('product_name');
            $table->renameColumn('bike_discount', 'product_discount')->after('product_name');
            $table->dropColumn('username');
            $table->dropColumn('address');
            $table->dropColumn('zip');
            $table->dropColumn('product_name');
            $table->unsignedInteger('product_id')->nullable()->change();
            $table->float('accessories_discounted_price')->after('accessories_price')->change();
        });
        Order::query()->get()->each(function (Order $order) {
            \DB::table('orders')
                ->where([
                    'id' => $order->id,
                ])
                ->update([
                'supplier_city' => $order->supplier->city && $order->supplier->city->name,
                'supplier_name' => $order->supplier->name,
                'supplier_postal_code' => $order->supplier->zip,
                'supplier_street' => $order->supplier->address,
                'employee_city' => $order->user->city && $order->user->city->name,
                'employee_salutation' => $order->user->salutation,
                'employee_name' => $order->user->fullName,
                'employee_postal_code' => $order->user->postal_code,
                'employee_street' => $order->user->street,
                'employee_email' => $order->user->email,
                'employee_phone' => $order->user->phone,
                'product_category_id' => $order->product->category_id,
                'product_brand' => $order->product->brand->name,
                'product_model' => $order->product->model->name,
                'product_size' => $order->product->size,
                'product_color' => $order->product->color,
            ]);
        });
        \Schema::table('orders', function (Blueprint $table) {
            $table->foreign('product_category_id')->on('product_categories')->references('id');
        });
        \Schema::table('contracts', function (Blueprint $table) {
            $table->string('sender')->nullable()->after('agreed_purchase_price');
            $table->string('supplier_name')->after('agreed_purchase_price');
            $table->string('supplier_street')->after('agreed_purchase_price');
            $table->string('supplier_postal_code')->after('agreed_purchase_price');
            $table->string('supplier_city')->after('agreed_purchase_price');
            $table->string('employee_salutation')->after('agreed_purchase_price');
            $table->string('employee_name')->after('agreed_purchase_price');
            $table->string('employee_street')->after('agreed_purchase_price');
            $table->string('employee_postal_code')->after('agreed_purchase_price');
            $table->string('employee_number')->after('agreed_purchase_price');
            $table->string('employee_email')->after('agreed_purchase_price');
            $table->string('employee_phone')->after('agreed_purchase_price');
            $table->string('employee_city')->after('agreed_purchase_price');
            $table->unsignedInteger('supplier_id')->nullable()->change();
            $table->unsignedInteger('product_category_id')->after('agreed_purchase_price');
            $table->string('product_color')->after('product_name');
            $table->string('product_model')->after('product_name');
            $table->renameColumn('bike_list_price', 'product_list_price')->after('product_name');
            $table->renameColumn('bike_discounted_price', 'product_discounted_price')->after('product_name');
            $table->renameColumn('bike_discount', 'product_discount')->after('product_name');
            $table->dropColumn('username');
            $table->dropColumn('product_name');
            $table->dropColumn('product_notes');
            $table->dropColumn('product_supplier');
            $table->unsignedInteger('product_id')->nullable()->change();
            $table->float('accessories_discounted_price')->after('accessories_price')->change();
        });
        Contract::query()->get()->each(function (Contract $contract) {
            \DB::table('contracts')
                ->where([
                    'id' => $contract->id,
                ])
                ->update([
                'supplier_city' => $contract->supplier->city && $contract->supplier->city->name,
                'supplier_name' => $contract->supplier->name,
                'supplier_postal_code' => $contract->supplier->zip,
                'supplier_street' => $contract->supplier->address,
                'employee_city' => $contract->user->city && $contract->user->city->name,
                'employee_salutation' => $contract->user->salutation,
                'employee_name' => $contract->user->fullName,
                'employee_postal_code' => $contract->user->postal_code,
                'employee_street' => $contract->user->street,
                'employee_email' => $contract->user->email,
                'employee_phone' => $contract->user->phone,
                'product_category_id' => $contract->product->category_id,
                'product_brand' => $contract->product->brand->name,
                'product_model' => $contract->product->model->name,
                'product_size' => $contract->product->size,
                'product_color' => $contract->product->color,
            ]);
        });
        \Schema::table('contracts', function (Blueprint $table) {
            $table->foreign('product_category_id')->on('product_categories')->references('id');
        });
    }

    public function down()
    {
        \Schema::table('offers', function (Blueprint $table) {
            $table->dropColumn('sender');
            $table->dropColumn('supplier_name');
            $table->dropColumn('supplier_street');
            $table->dropColumn('supplier_postal_code');
            $table->dropColumn('supplier_city');
            $table->dropColumn('employee_salutation');
            $table->dropColumn('employee_name');
            $table->dropColumn('employee_street');
            $table->dropColumn('employee_postal_code');
            $table->dropColumn('employee_number');
            $table->dropColumn('employee_email');
            $table->dropColumn('employee_phone');
            $table->dropColumn('employee_city');
            $table->dropColumn('supplier_id');
            $table->dropColumn('product_brand');
            $table->dropColumn('product_model');
            $table->dropColumn('product_color');
            $table->dropColumn('product_size');
            $table->renameColumn('product_list_price', 'bike_list_price');
            $table->renameColumn('product_discounted_price', 'bike_discounted_price');
            $table->renameColumn('product_discount', 'bike_discount');
            $table->text('contract_data')->nullable();
            $table->unsignedInteger('supplier_user_id');
            $table->foreign('supplier_user_id')->on('portal_users')->references('id');
        });
        \Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('sender');
            $table->dropColumn('supplier_name');
            $table->dropColumn('supplier_street');
            $table->dropColumn('supplier_postal_code');
            $table->dropColumn('supplier_city');
            $table->dropColumn('employee_salutation');
            $table->dropColumn('employee_name');
            $table->dropColumn('employee_street');
            $table->dropColumn('employee_postal_code');
            $table->dropColumn('employee_number');
            $table->dropColumn('employee_email');
            $table->dropColumn('employee_phone');
            $table->dropColumn('employee_city');
            $table->dropColumn('supplier_id');
            $table->dropColumn('product_brand');
            $table->dropColumn('product_model');
            $table->dropColumn('product_color');
            $table->dropColumn('product_size');
            $table->dropColumn('product_discount');
            $table->renameColumn('product_list_price', 'bike_list_price');
            $table->renameColumn('product_discounted_price', 'bike_discounted_price');
        });
        \Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('sender');
            $table->dropColumn('supplier_name');
            $table->dropColumn('supplier_street');
            $table->dropColumn('supplier_postal_code');
            $table->dropColumn('supplier_city');
            $table->dropColumn('employee_salutation');
            $table->dropColumn('employee_name');
            $table->dropColumn('employee_street');
            $table->dropColumn('employee_postal_code');
            $table->dropColumn('employee_number');
            $table->dropColumn('employee_email');
            $table->dropColumn('employee_phone');
            $table->dropColumn('employee_city');
            $table->dropColumn('supplier_id');
            $table->dropColumn('product_brand');
            $table->dropColumn('product_model');
            $table->dropColumn('product_color');
            $table->dropColumn('product_size');
            $table->dropColumn('product_discount');
            $table->renameColumn('product_list_price', 'bike_list_price');
            $table->renameColumn('product_discounted_price', 'bike_discounted_price');
        });
    }
}
