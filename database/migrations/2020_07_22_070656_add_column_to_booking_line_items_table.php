<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToBookingLineItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_line_items', function (Blueprint $table) {
            $table->string('co_license_passport')->nullable();
            $table->string('co_license_postal_code')->nullable();
            $table->string('co_license_street_no')->nullable();
            $table->string('co_license_street_name')->nullable();
            $table->string('co_license_unit_no')->nullable();
            $table->string('co_license_building_name')->nullable();
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
            $table->dropColumn(['co_license_passport',  'co_license_postal_code',  'co_license_street_no',  'co_license_street_name',  'co_license_unit_no',  'co_license_building_name']);
        });
    }
}
