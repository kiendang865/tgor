<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes;
    protected $table = 'payment';
    protected $fillable = [
        'payment_no', 'payment_date', 'status', 'invoice_id', 'user_id', 'total_amount', 'total_tax_amount', 'total', 'payment_mode_id','cheque','cheque_bank','transaction','remarks', 'signature_tgor_officer', 'signature_client','is_donate','officer', 'total_discount', 'partial_payment'
    ];
    protected $appends = ['amount_payable', 'partial_amount'];

    public function getAmountPayableAttribute()
    {
        $amount_partial = 0;
        $data = json_decode($this->partial_payments->pluck('amount'));
        if(!empty($data)){
            foreach($data as $key=>$value){
                $amount_partial += floatval($value);
            }
        }
        return $this->total - $amount_partial;
    }

    public function getPartialAmountAttribute(){
        return $this->partial_payments->pluck('amount');
    }
    public function attachments()
    {
        return $this->morphMany('App\Attachments', 'attachable');
    }
    public function payment_line_item(){
        return $this->hasMany('App\PaymentLineItem', 'payment_id');
    }
    public function payment_mode(){
        return $this->belongsTo('App\Reference', 'payment_mode_id', 'id');
    }
    public function client(){
        return $this->belongsTo('App\User', 'user_id');
    }
    public function invoice(){
        return $this->belongsTo('App\Invoice', 'invoice_id');
    }
    public function admin(){
        return $this->belongsTo('App\User', 'officer');
    }
    public function partial_payments(){
        return $this->hasMany('App\PartialPayment', 'payment_id');
    }
}
