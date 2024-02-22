<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('salutation')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->string('phone')->nullable();
            $table->string('passport')->nullable();
            $table->string('nationality')->nullable();
            $table->string('street_no')->nullable();
            $table->string('street_name')->nullable();
            $table->string('unit_no')->nullable();
            $table->string('building_name')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('alternative_contact_no')->nullable();
            $table->string('church_attended')->nullable();
            $table->bigInteger('religion_id')->nullable();
            $table->bigInteger('preferred_contact_by_id')->nullable();
            $table->bigInteger('is_tgor')->nullable();
            $table->string('contractor')->nullable();
            $table->string('contact_person')->nullable();
            $table->bigInteger('service_id')->nullable();
            $table->string('display_address')->nullable();
            $table->string('display_name')->nullable();
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
        Schema::dropIfExists('users');
    }
}
