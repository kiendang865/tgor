<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleAgreementsLineItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_agreements_line_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('sale_agreement_id');
            $table->bigInteger('booking_id');
            $table->bigInteger('line_item_id');
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
        Schema::dropIfExists('sale_agreements_line_items');
    }
}
