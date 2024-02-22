<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('payment_no');
            $table->dateTime('payment_date');
            $table->bigInteger('status')->nullable();
            $table->bigInteger('sale_agreement_id');
            $table->bigInteger('user_id');
            $table->decimal('total_amount', 15, 2);
            $table->decimal('total_tax_amount', 15, 2);
            $table->integer('payment_type_id')->nullable();
            $table->string('cheque')->nullable();
            $table->string('cheque_bank')->nullable();
            $table->string('transaction')->nullable();
            $table->string('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment');
    }
}
