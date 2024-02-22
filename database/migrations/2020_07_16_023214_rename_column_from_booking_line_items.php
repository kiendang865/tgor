<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameColumnFromBookingLineItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_line_items', function (Blueprint $table) {
            $table->renameColumn('cord_title', 'coord_title');
            $table->renameColumn('cord_first_name', 'coord_first_name');
            $table->renameColumn('cord_last_name', 'coord_last_name');
            $table->renameColumn('cord_contact_no', 'coord_contact_no');
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
            $table->renameColumn('coord_title', 'cord_title');
            $table->renameColumn('coord_first_name', 'cord_first_name');
            $table->renameColumn('coord_last_name', 'cord_last_name');
            $table->renameColumn('coord_contact_no', 'cord_contact_no');
        });
    }
}