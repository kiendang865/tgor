<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Booking extends Model
{
    use SoftDeletes;
    protected $table = "booking";
    protected $fillable = [
        'booking_no', 'status', 'user_id', 'total_amount', 'total_tax_amount', 'is_sale', 'is_invoice'
    ];
    protected $appends = ['services'];

    public $timestamps = true;
    public function booking_line_items()
    {
        return $this->hasMany('App\BookingLineItems', 'booking_id');
    }
    public function niches(){
        return $this->hasMany('App\Niche', 'booking_id');
    }
    public function rooms(){
        return $this->hasMany('App\MemorialRoom', 'booking_id');
    }
    // public function others(){
    //     return $this->hasOne('App\Other', 'booking_id');
    // }
    public function clients(){
        return $this->belongsTo('App\User', 'user_id');
    } 
    public function sale_agreement(){
        return $this->hasMany('App\SaleAgreement', 'booking_id');
    }
    public function getServicesAttribute()
    {
        $service = [];
        foreach($this->booking_line_items as $value ){
            if($value->booking_type->reference_value_text == 'Niches'){
                $flag = "Niches";
                array_push($service, $flag);
            }
            if($value->booking_type->reference_value_text == 'Memorial Rooms'){
                $flag = "Memorial Rooms";
                array_push($service, $flag);
            }
            if($value->booking_type->reference_value_text == 'Additional Services'){
                $flag = "Additional Services";
                array_push($service, $flag);
            }
        }
        return $service;
    }
    public function status(){
        return $this->belongsTo('App\Reference', 'status');
    }
}
