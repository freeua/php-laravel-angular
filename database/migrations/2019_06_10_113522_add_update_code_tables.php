<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUpdateCodeTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('portals', function (Blueprint $table) {
            $table->string('code', 10)->nullable();
        });
        Schema::table('companies', function (Blueprint $table) {
            $table->string('code', 10)->nullable()->change();
        });
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('code', 10)->nullable()->change();
        });
        Schema::table('portal_users', function (Blueprint $table) {
            $table->string('code', 10)->nullable()->change();
        });
        Schema::table('partners', function (Blueprint $table) {
            $table->string('code', 10)->nullable();
        });
        Schema::table('offers', function (Blueprint $table) {
            $table->string('number', 11)->nullable(true)->change();
            $table->string('employee_code', 11)->nullable();
            $table->string('supplier_code', 11)->nullable();
        });
        Schema::table('contracts', function (Blueprint $table) {
            $table->string('number', 11)->nullable(true)->change();
            $table->string('employee_code', 11)->nullable();
            $table->string('supplier_code', 11)->nullable();
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->string('number', 11)->nullable(true)->change();
            $table->string('employee_code', 11)->nullable();
            $table->string('supplier_code', 11)->nullable();
        });
        $portals = \App\Models\Portal::all();
        foreach ($portals as $portal){
            $portal->code = $portal->generateCode($portal->id,  6, '', 'PE-');
            $portal->save();
        }

        $companies = \App\Models\Companies\Company::all();
        foreach ($companies as $company){
            $company->code = $company->generateCode($company->id,  6, '', 'FN-');
            $company->save();
        }

        $suppliers = \App\Portal\Models\Supplier::all();
        foreach ($suppliers as $supplier){
            $supplier->code = $supplier->generateCode($supplier->id,  6, '', 'LI-');
            $supplier->save();
        }

        $employees = \App\Portal\Models\User::query()->withTrashed()->get();
        foreach ($employees as $employee){
            $employee->code = $employee->generateCode($employee->id,  6, '', 'MA-');
            $employee->save();
        }

        $offers = \App\Portal\Models\Offer::all();
        foreach ($offers as $offer){
            $offer->number = $offer->generateCode($offer->id,  7, '', 'ANG-');
            $offer->employeeCode = $offer->user->code;
            if ($offer->supplier_id) {
                $offer->supplierCode = $offer->supplier->code;
            }
            $offer->save();
            $order = \App\Portal\Models\Order::where('offer_id','=',$offer->id)->get()->first();
            if(!empty($order)) {
                $order->number = $order->generateCode($order->id,  7, '', 'DRA-');
                $order->employeeCode = $order->user->code;
                if ($order->supplier_id) {
                    $order->supplierCode = $order->supplier->code;
                }
                $order->save();
                $contract = \App\Portal\Models\Contract::where('order_id','=',$order->id)->get()->first();
                if(!empty($contract)){
                    $contract->number = $contract->generateCode($order->id,  7, '', 'DRA-');
                    $contract->employeeCode = $contract->user->code;
                    if ($contract->supplier_id) {
                        $contract->supplierCode = $contract->supplier->code;
                    }
                    $contract->save();
                }
            }

        }

        Schema::table('companies', function (Blueprint $table) {
            $table->string('code')->unique()->change();
        });
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('code')->nullable()->unique()->change();
        });
        Schema::table('portal_users', function (Blueprint $table) {
            $table->string('code')->nullable()->unique()->change();
        });
        Schema::table('offers', function (Blueprint $table) {
            $table->string('number')->unique()->change();
        });
        Schema::table('contracts', function (Blueprint $table) {
            $table->string('number')->unique()->change();
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->string('number')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('portals', function (Blueprint $table) {
            $table->dropColumn('code');
        });
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('code', 10)->change();
        });
        Schema::table('portal_users', function (Blueprint $table) {
            $table->string('code', 10)->change();
        });
        Schema::table('partners', function (Blueprint $table) {
            $table->dropColumn('code');
        });
        Schema::table('offers', function (Blueprint $table) {
            $table->string('number', 9)->change();
            $table->dropColumn('employee_code');
            $table->dropColumn('supplier_code');
        });
        Schema::table('contracts', function (Blueprint $table) {
            $table->string('number', 9)->change();
            $table->dropColumn('employee_code');
            $table->dropColumn('supplier_code');
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->string('number', 10)->change();
            $table->dropColumn('employee_code');
            $table->dropColumn('supplier_code');
        });
    }
}
