<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToSaleAgreementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sale_agreements', function (Blueprint $table) {
            $table->string('signature_tgor_officer')->nullable();
            $table->string('signature_client')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sale_agreements', function (Blueprint $table) {
            $table->dropColumn(['signature_tgor_officer',  'signature_client']);
        });
    }
}
