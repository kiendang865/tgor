<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleAgreementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_agreements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('sale_agreement_no');
            $table->dateTime('sale_agreement_date');
            $table->bigInteger('booking_id');
            $table->bigInteger('user_id');
            $table->decimal('total_amount', 15, 2);
            $table->decimal('total_tax_amount', 15, 2);
            $table->bigInteger('status')->nullable();
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
        Schema::dropIfExists('sale_agreements');
    }
}
