<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddServiceTypeNameAndParentIdToOther extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('other', function (Blueprint $table) {
            $table->string('service_type_name')->nullable();
            $table->bigInteger('parent_id');
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
            $table->dropColumn('service_type_name');
            $table->dropColumn('parent_id');
        });
    }
}
