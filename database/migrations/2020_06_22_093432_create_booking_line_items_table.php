<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingLineItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking_line_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('booking_id');
            $table->bigInteger('booking_type_id');
            $table->bigInteger('service_id');
            $table->string('application_no')->nullable();
            $table->dateTime('application_date')->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('expiry_date')->nullable();
            $table->bigInteger('duration_of_lease')->nullable();
            $table->bigInteger('discount')->nullable();
            $table->decimal('tax_amount', 8, 2)->nullable();
            $table->decimal('amount', 8, 2)->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('relationship_to_applicant');
            $table->dateTime('death_anniversary')->nullable();
            $table->dateTime('booking_date')->nullable();
            $table->string('departed_title')->nullable();
            $table->string('departed_first_name')->nullable();
            $table->string('departed_last_name')->nullable();
            $table->string('tv_departed_display_name')->nullable();
            $table->string('tv_departed_notes')->nullable();
            $table->dateTime('death_date')->nullable();
            $table->string('church_attended')->nullable();
            $table->string('departed_notes')->nullable();
            $table->string('book_funerial_director')->nullable();
            $table->string('applicant_is_coordinator')->nullable();
            $table->string('coord_title')->nullable();
            $table->string('coord_first_name')->nullable();
            $table->string('coord_last_name')->nullable();
            $table->string('coord_contact_no')->nullable();
            $table->dateTime('check_in_date')->nullable();
            $table->dateTime('check_out_date')->nullable();
            $table->dateTime('renting_date')->nullable();
            $table->dateTime('return_date')->nullable();
            $table->string('duration')->nullable();
            $table->bigInteger('contractor_id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('booking_line_items');
    }
}
