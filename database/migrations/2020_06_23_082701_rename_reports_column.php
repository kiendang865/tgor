<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameReportsColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reports', function(Blueprint $table) {
            $table->renameColumn('file_name', 'name');
            $table->renameColumn('file_path', 'remarks');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reports', function(Blueprint $table) {
            $table->renameColumn('name', 'file_name');
            $table->renameColumn('remarks', 'file_path');
        });
    }
}
