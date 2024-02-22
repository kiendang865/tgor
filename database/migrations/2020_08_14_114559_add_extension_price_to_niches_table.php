<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExtensionPriceToNichesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('niches', function (Blueprint $table) {
            $table->decimal('price_thirty_years', 15, 2);
            $table->decimal('price_fifty_years', 15, 2);
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
            //
        });
    }
}
