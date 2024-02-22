<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Duration extends Model
{
    use SoftDeletes;
    protected $table = 'duration';
    protected $fillable = [
        'niche_id', 'exten_year', 'exten_price'
    ];
}
