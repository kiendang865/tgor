<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLisenseToBookingLineItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_line_items', function (Blueprint $table) {
            $table->bigInteger('co_license')->nullable();
            $table->string('co_license_name')->nullable();
            $table->string('co_license_email')->nullable();
            $table->string('co_license_phone')->nullable();
            $table->bigInteger('relationship_with_license')->nullable();
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
            $table->dropColumn('co_license');
            $table->dropColumn('co_license_name');
            $table->dropColumn('co_license_email');
            $table->dropColumn('co_license_phone');
            $table->dropColumn('relationship_with_license');
        });
    }
}
