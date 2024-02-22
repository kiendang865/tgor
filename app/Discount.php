<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Discount extends Model
{
    use SoftDeletes;
    protected $table = 'discount';
    protected $fillable = [
        'discount_code', 'discount_type', 'category','type','minimum_qty','amount','amount_type','remarks','percent', 'is_custom', 'room_id', 'service_id', 'other_id'
    ];
    public function type_amount(){
        return $this->belongsTo('App\Reference', 'amount_type', 'id');
    }
    public function category(){
        return $this->belongsTo('App\Reference', 'category', 'id');
    }
    public function type(){
        return $this->belongsTo('App\Reference', 'type', 'id');
    }
    public function type_discount(){
        return $this->belongsTo('App\Reference', 'discount_type', 'id');
    }
    public function room(){
        return $this->belongsTo('App\MemorialRoom', 'room_id', 'id');
    }
    public function service_type(){
        return $this->belongsTo('App\Reference', 'service_id', 'id');
    }
    public function service(){
        return $this->belongsTo('App\Other', 'other_id', 'id');
    }
}
