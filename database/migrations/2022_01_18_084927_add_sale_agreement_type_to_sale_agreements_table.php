<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSaleAgreementTypeToSaleAgreementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sale_agreements', function (Blueprint $table) {
            $table->bigInteger("sale_agreement_type")->nullable();
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
            $table->dropColumn("sale_agreement_type");
        });
    }
}
