<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Other extends Model
{
    use SoftDeletes;
    protected $table = 'other';
    protected $fillable = [
        'service_name', 'type', 'price', 'is_contractor', 'parent_id', 'service_type_name', 'booking_id', 'status', 'category_type'
    ];
    public $timestamps = true;

    public function type()
    {
        return $this->belongsTo('App\Reference', 'type', 'id');
    }
    public function contractor()
    {
        return $this->belongsTo('App\Reference', 'is_contractor', 'id');
    }
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }
    public function status(){
        return $this->belongsTo('App\Reference', 'status', 'id');
    }
    public function type_reference()
    {
        return $this->belongsTo('App\Reference', 'type', 'id');
    }
    public function category(){
        return $this->belongsTo('App\Reference', 'category_type', 'id');
    }
}
