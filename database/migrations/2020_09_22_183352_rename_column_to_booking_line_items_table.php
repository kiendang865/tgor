<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameColumnToBookingLineItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_line_items', function (Blueprint $table) {
            $table->renameColumn('departed_first_name', 'departed_full_name');    
            $table->renameColumn('death_date', 'date_of_death');            
            $table->renameColumn('tv_departed_display_name', 'tv_display_name');            
            $table->renameColumn('coord_first_name', 'coordinator_full_name');            
            $table->renameColumn('coord_contact_no', 'mobile');            
            $table->string('tv_photo_of_departed')->nullable();
            $table->string('tv_life_years')->nullable();
            $table->string('tv_wake_service')->nullable();
            $table->string('tv_encoffin_service')->nullable();
            $table->string('tv_cottage_leaves')->nullable();
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
            $table->renameColumn('departed_first_name', 'departed_full_name');    
            $table->renameColumn('death_date', 'date_of_death');            
            $table->renameColumn('tv_departed_display_name', 'tv_display_name');            
            $table->renameColumn('coord_first_name', 'coordinator_full_name');            
            $table->renameColumn('coord_contact_no', 'mobile');
            $table->dropColumn('tv_photo_of_departed');
            $table->dropColumn('tv_life_years');
            $table->dropColumn('tv_wake_service');
            $table->dropColumn('tv_encoffin_service');
            $table->dropColumn('tv_cottage_leaves');
        });
    }
}
