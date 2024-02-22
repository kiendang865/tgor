<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateColumnToBookingLineItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_line_items', function (Blueprint $table) {
            $table->renameColumn('expiry_date', 'lease_expiry_date');            
            $table->renameColumn('start_date', 'lease_start_date');            
            $table->renameColumn('renting_date', 'start_date');            
            $table->renameColumn('return_date', 'end_date');            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('booking_line_items', function (Blueprint $table) {
            $table->renameColumn('expiry_date', 'lease_expiry_date');            
            $table->renameColumn('start_date', 'lease_start_date');            
            $table->renameColumn('renting_date', 'start_date');            
            $table->renameColumn('return_date', 'end_date');        
        });
    }
}
