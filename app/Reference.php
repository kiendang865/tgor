<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reference extends Model
{
    use SoftDeletes;
    protected $table = 'reference';
    protected $fillable = [
        'reference_type', 'reference_value_text',
    ];
    public $timestamps = true;

}
