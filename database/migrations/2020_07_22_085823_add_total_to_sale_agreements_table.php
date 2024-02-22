<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTotalToSaleAgreementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sale_agreements', function (Blueprint $table) {
            $table->decimal('total_amount', 15, 2)->nullable()->change();
            $table->decimal('total_tax_amount', 15, 2)->nullable()->change();
            $table->decimal('total', 15, 2)->nullable();
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
            $table->decimal('total_amount', 15, 2)->change();
            $table->decimal('total_tax_amount', 15, 2)->change();
            $table->dropColumn('total', 15, 2);
        });
    }
}
