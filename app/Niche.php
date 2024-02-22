<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \Znck\Eloquent\Traits\BelongsToThrough;

class Niche extends Model
{
    use SoftDeletes;
    use BelongsToThrough;
    protected $table = 'niches';
    protected $fillable = [
        'reference_no', 'type_id', 'category_id', 'price', 'booking_id', 'status', 'bay', 'wing', 'floor', 'block', 'level', 'unit', 'full_location', 'booking_line_item','renew_price'
    ];
    public $timestamps = true;
    protected $appends = [
        'location',
    ];
    
    public function type()
    {
        return $this->belongsTo('App\Reference', 'type_id', 'id');
    }
    public function category()
    {
        return $this->belongsTo('App\Reference', 'category_id', 'id');
    }
    public function booking_line_item()
    {
        return $this->belongsTo('App\BookingLineItems', 'booking_line_item', 'id');
    }
    public function client(){
        return $this->BelongsToThrough('App\User', 'App\Booking');
    }
    public function booking(){
        return $this->belongsTo('App\Booking', 'booking_id','id');
    }
    public function booking_item(){
        return $this->hasOne('App\BookingLineItems', 'service_id');
    }
    public function getLocationAttribute() {
        $location = "";
        if($this->wing){
            $location = $this->wing.' Wing - ';
        }
        if($this->floor){
            $location .= 'Floor '.$this->floor.' - ';
        }
        if($this->bay){
            $location .= 'Bay '.$this->bay.' - ';
        }
        if($this->block){
            $location .= 'Block '.$this->block.' - ';
        }
        if($this->level){
            $location .= 'Level '.$this->level.' - ';
        }
        if($this->unit){
            $location .= 'Unit '.$this->unit;
        }
       return $location;
    }
    public function extension(){
        return $this->hasMany('App\Duration', 'niche_id');
    }
}
