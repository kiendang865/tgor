<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Remarks extends Model
{
    protected $table = 'remarks';
    protected $fillable = [
        'booking_line_item_id', 'user_id', 'remarks', 'file_url','name_file','file_path'
    ];

    use SoftDeletes;

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
}
