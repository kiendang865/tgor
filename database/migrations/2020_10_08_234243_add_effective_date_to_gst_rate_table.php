<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEffectiveDateToGstRateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('gst_rate', function (Blueprint $table) {
            $table->dateTime('gst_start_date')->nullable();
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
        Schema::table('gst_rate', function (Blueprint $table) {
            $table->dropColumn('gst_start_date');
            $table->dropSoftDeletes();
        });
    }
}
