<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToMemorialRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('memorial_rooms', function (Blueprint $table) {
            $table->decimal('price_complimentary', 15, 2)->default(0);
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('memorial_rooms', function (Blueprint $table) {
            $table->dropColumn('price_complimentary');
            
        });
    }
}
