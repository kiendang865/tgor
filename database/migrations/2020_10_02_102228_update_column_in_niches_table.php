<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateColumnInNichesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('niches', function (Blueprint $table) {
            $table->renameColumn('level', 'floor');
            $table->renameColumn('niche_level', 'level');
            $table->renameColumn('niche_block', 'block');
            $table->renameColumn('niche_number', 'unit');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('niches', function (Blueprint $table) {
            $table->renameColumn('floor', 'level');
            $table->renameColumn('level', 'niche_level');
            $table->renameColumn('block', 'niche_block');
            $table->renameColumn('unit', 'niche_number');
        });
    }
}
