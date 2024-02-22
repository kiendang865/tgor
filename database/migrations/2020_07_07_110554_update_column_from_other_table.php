<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateColumnFromOtherTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('other', function (Blueprint $table) {
            $table->string('is_contractor')->nullable()->change();
            $table->decimal('price', 15, 2)->nullable()->change();
            $table->string('type')->nullable()->change();
            $table->bigInteger('parent_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('other', function (Blueprint $table) {
            //
        });
    }
}
