<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNichesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('niches', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('reference_no');
            $table->bigInteger('type_id');
            $table->decimal('price', 15, 2);
            $table->string('booking_id')->nullable();
            $table->string('bay')->nullable();
            $table->string('wing')->nullable();
            $table->string('level')->nullable();
            $table->string('niche_block')->nullable();
            $table->string('niche_level')->nullable();
            $table->string('niche_number')->nullable();
            $table->string('status')->default('Available');
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
        Schema::dropIfExists('niches');
    }
}
