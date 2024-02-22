<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTotalToInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('total_amount', 15, 2)->nullable()->change();
            $table->decimal('total_tax_amount', 15, 2)->nullable()->change();
            $table->decimal('total', 15, 2)->nullable()->change();
            $table->dateTime('invoice_date')->nullable()->change();
            $table->bigInteger('sale_agreement_id')->nullable()->change();
            $table->bigInteger('user_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            //
        });
    }
}
