<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartialPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partial_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('payment_id')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->decimal('amount', 8, 2)->nullable();
            $table->bigInteger('payment_mode_id')->nullable();
            $table->string('cheque')->nullable();
            $table->string('cheque_bank')->nullable();
            $table->string('transaction')->nullable();
            $table->string('remarks')->nullable();
            $table->string('signature_tgor_officer')->nullable();
            $table->string('signature_client')->nullable();
            $table->bigInteger('officer')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('partial_payments');
    }
}
