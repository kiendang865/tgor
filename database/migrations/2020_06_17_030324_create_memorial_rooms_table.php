<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMemorialRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('memorial_rooms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('room_no');
            $table->decimal('price', 15, 2);
            $table->string('booking_id')->nullable();
            $table->string('location');
            $table->bigInteger('tv_dimention_id')->nullable();
            $table->string('status')->default('Available');
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
        Schema::dropIfExists('memorial_rooms');
    }
}
