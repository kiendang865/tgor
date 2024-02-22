<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractorHasServiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contractor_has_service', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('contractor_id');
            $table->bigInteger('service_id');
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
        Schema::dropIfExists('contractor_has_service');
    }
}
