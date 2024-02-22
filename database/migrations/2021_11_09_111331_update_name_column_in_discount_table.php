<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateNameColumnInDiscountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('discount', function (Blueprint $table) {
            $table->renameColumn('niche_category', 'category');
            $table->renameColumn('niche_type', 'type');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('discount', function (Blueprint $table) {
            $table->renameColumn('category', 'niche_category');
            $table->renameColumn('type', 'niche_type');
        });
    }
}
