<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->bigInteger('service');
            $table->dropColumn('remarks');
            $table->dropColumn('title');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn('start_time');
            $table->dropColumn('end_time');
            $table->dropColumn('service');
            $table->string('title')->nullable();
            $table->string('remarks')->nullable();
        });
    }
}
