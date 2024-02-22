<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model 
{
    use SoftDeletes;

    protected $table = 'reports';

    public $timestamps = true;

    protected $fillable = [
        'start_time', 'end_time', 'name', 'service'
    ];

    public function service_type(){
        return $this->belongsTo('App\Reference', 'service', 'id');
    }

}
