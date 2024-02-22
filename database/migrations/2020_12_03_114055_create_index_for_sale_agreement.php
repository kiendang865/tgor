<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateIndexForSaleAgreement extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('CREATE INDEX sale_agreement_items_table_idx ON sale_agreements_line_items (sale_agreement_id)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP INDEX sale_agreement_items_table_idx ON sale_agreements_line_items');
    }
}
