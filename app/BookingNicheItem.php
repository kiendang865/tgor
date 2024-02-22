<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingNicheItem extends Model
{
    use SoftDeletes;
    protected $table = "booking_niches_items";
    protected $fillable = [
        'booking_line_items_id', 'full_name', 'last_name', 'relationship_to_applicant', 'death_anniversary'
    ];
    public $timestamps = true;
    public function relationship_to_applicant(){
        return $this->belongsTo('App\Reference', 'relationship_to_applicant', 'id');
    }
    public function is_relationship_to_applicant(){
        return $this->belongsTo('App\Reference', 'relationship_to_applicant', 'id');
    }
}
