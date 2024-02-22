<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnToBookingNichesItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_niches_items', function (Blueprint $table) {
            $table->renameColumn('first_name', 'full_name');            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('booking_niches_items', function (Blueprint $table) {
            $table->renameColumn('first_name', 'full_name');
        });
    }
}
