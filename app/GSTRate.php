<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GSTRate extends Model
{
    use SoftDeletes;
    protected $table = 'gst_rate';
    protected $fillable = ['rate', 'name', 'gst_start_date', 'gst_end_date'];
}
