<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PartialPayment extends Model
{
    use SoftDeletes;
    protected $table = 'partial_payments';
    protected $fillable = [
        'payment_id', 'user_id', 'amount', 'payment_mode_id','cheque','cheque_bank','transaction','remarks', 'signature_tgor_officer', 'signature_client', 'officer'
    ];

    public function attachments()
    {
        return $this->morphMany('App\Attachments', 'attachable');
    }
    public function payment(){
        return $this->belongsTo('App\Payment', 'payment_id', 'id');
    }
    public function payment_mode(){
        return $this->belongsTo('App\Reference', 'payment_mode_id', 'id');
    }
    public function client(){
        return $this->belongsTo('App\User', 'user_id');
    }
    public function admin(){
        return $this->belongsTo('App\User', 'officer');
    }

}
