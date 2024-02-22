<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RemoveViewNichesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('view_niches', function (Blueprint $table) {
            DB::statement("DROP VIEW view_niches");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $statement = 'CREATE VIEW view_niches AS
        SELECT niches.id, niches.reference_no, niches.full_location as location, niches.status, type_niches.reference_value_text as type_niches, booking_line_items.duration_of_lease, booking_niches_items.first_name
        FROM niches
        LEFT JOIN reference as type_niches ON niches.type_id = type_niches.id
        LEFT JOIN booking_line_items ON niches.booking_line_item = booking_line_items.id
        LEFT JOIN booking_niches_items ON niches.booking_line_item  = booking_niches_items.booking_line_items_id';
        DB::statement($statement);
    }
}
