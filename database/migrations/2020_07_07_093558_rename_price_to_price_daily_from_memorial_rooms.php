<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenamePriceToPriceDailyFromMemorialRooms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('memorial_rooms', function (Blueprint $table) {
            $table->renameColumn('price', 'price_daily');
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
            //
        });
    }
}
