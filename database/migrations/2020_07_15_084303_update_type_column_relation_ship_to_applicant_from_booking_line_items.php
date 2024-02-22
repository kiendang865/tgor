<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTypeColumnRelationShipToApplicantFromBookingLineItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_line_items', function (Blueprint $table) {
            $table->bigInteger('relationship_to_applicant')->nullable()->change();
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
            $table->string('relationship_to_applicant')->nullable()->change();
        });
    }
}
