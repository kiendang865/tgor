<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NicheReserved extends Model
{
    protected $table = 'niches_reserved';
    protected $fillable = [
        'reserved_date', 'niche_id', 'customer_name', 'mobile', 'email'
    ];
    public function niche(){
        return $this->belongsTo('App\Niche', 'niche_id','id');
    }
}
