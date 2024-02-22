<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNichesReservedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('niches_reserved', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->dateTime('reserved_date')->nullable();
            $table->bigInteger('niche_id')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();
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
        Schema::dropIfExists('niches_reserved');
    }
}
