<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNameFileToRemarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('remarks', function (Blueprint $table) {
            $table->string('name_file')->nullable();
            $table->string('file_path')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('remarks', function (Blueprint $table) {
            $table->dropColumn('name_file');
            $table->dropColumn('file_path');
        });
    }
}
