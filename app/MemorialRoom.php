<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MemorialRoom extends Model
{
    use SoftDeletes;
    protected $table = 'memorial_rooms';
    protected $fillable = [
        'room_no', 'price_daily', 'price_hourly', 'booking_id', 'status', 'status'
    ];
    public $timestamps = true;

    public function status()
    {
        return $this->belongsTo('App\Reference', 'status', 'id');
    }
}

