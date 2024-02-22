<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingNichesItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking_niches_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('booking_line_items_id');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->bigInteger('relationship_to_applicant')->nullable();
            $table->dateTime('death_anniversary')->nullable();
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
        Schema::dropIfExists('booking_niches_items');
    }
}
