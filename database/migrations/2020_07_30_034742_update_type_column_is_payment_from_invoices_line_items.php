<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTypeColumnIsPaymentFromInvoicesLineItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices_line_items', function (Blueprint $table) {
            $table->boolean('is_payment')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoices_line_items', function (Blueprint $table) {
            $table->boolean('is_payment')->nullable()->change();
        });
    }
}
