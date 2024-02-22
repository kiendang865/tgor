<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveTvDimentionAndLocationFromMemorialRooms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('memorial_rooms', function (Blueprint $table) {
            $table->dropColumn('tv_dimention_id');
            $table->dropColumn('location');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('memorial_rooms', function (Blueprint $table) {
            $table->string('location');
            $table->bigInteger('tv_dimention_id')->nullable();
        });
    }
}
