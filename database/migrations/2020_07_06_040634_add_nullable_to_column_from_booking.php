<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNullableToColumnFromBooking extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking', function (Blueprint $table) {
            $table->decimal('total_amount', 15, 2)->nullable()->change();
            $table->decimal('total_tax_amount', 15, 2)->nullable()->change();
            $table->string('status')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('booking', function (Blueprint $table) {
            $table->decimal('total_amount', 15, 2)->change();
            $table->decimal('total_tax_amount', 15, 2)->change();
            $table->string('status')->change();
        });
    }
}
