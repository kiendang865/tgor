<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRoomTypeToMemorialRooms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('memorial_rooms', function (Blueprint $table) {
            $table->bigInteger('room_type')->nullable();
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
            $table->dropColumn('room_type');
        });
    }
}
