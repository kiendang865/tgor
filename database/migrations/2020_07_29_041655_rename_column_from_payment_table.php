<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameColumnFromPaymentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment', function (Blueprint $table) {
            $table->renameColumn('sale_agreement_id', 'invoice_id');
            $table->renameColumn('payment_type_id', 'payment_mode');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment', function (Blueprint $table) {
            $table->renameColumn('invoice_id', 'sale_agreement_id');
            $table->renameColumn('payment_mode', 'payment_type_id');
        });
    }
}
