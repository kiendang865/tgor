<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToDiscountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('discount', function (Blueprint $table) {
            $table->bigInteger('other_id')->nullable();
            $table->bigInteger('room_id')->nullable();
            $table->bigInteger('service_id')->nullable()->after('discount_type');
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
            $table->dropColumn('other_id');
            $table->dropColumn('room_id');
            $table->dropColumn('service_id');
        });
    }
}
