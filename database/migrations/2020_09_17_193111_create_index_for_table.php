<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateIndexForTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('CREATE INDEX niches_table_idx ON niches (booking_line_item, reference_no, full_location, status)');
        DB::statement('CREATE INDEX booking_line_items_id_idx ON booking_niches_items (booking_line_items_id)');
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP INDEX niches_table_idx ON niches');
        DB::statement('DROP INDEX booking_line_items_id_idx ON booking_niches_items');
    }
}
