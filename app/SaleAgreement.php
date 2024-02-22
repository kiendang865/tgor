<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleAgreement extends Model
{
    use SoftDeletes;
    protected $table = 'sale_agreements';
    protected $fillable = [
        'sale_agreement_no', 'sale_agreement_date', 'discount_list', 'discount_id', 'status', 'booking_id', 'user_id', 'total_amount', 'total_tax_amount', 'total','officer', 'invoice_id', 'total_discount', 'sale_agreement_type', 'is_add_invoice','gst_id'
    ];
    public $timestamps = true;

    public function sale_agreement_item(){
        return $this->hasMany('App\SaleAgreementLineItem', 'sale_agreement_id' );
    }
    public function booking(){
        return $this->belongsTo('App\Booking', 'booking_id');
    }
    public function client(){
        return $this->belongsTo('App\User', 'user_id');
    }
    public function invoices(){
        return $this->hasMany('App\Invoice', 'sale_agreement_id');
    }
    public function admin(){
        return $this->belongsTo('App\User', 'officer');
    }
    public function discount(){
        return $this->belongsTo('App\Discount', 'discount_id');
    }
    public function invoice(){
        return $this->belongsTo('App\Invoice', 'invoice_id');
    }
    public function sale_agreement_type(){
        return $this->belongsTo('App\Reference', 'sale_agreement_type', 'id');
    }
    public function sa_type(){
        return $this->belongsTo('App\Reference', 'sale_agreement_type', 'id');
    }
    public function gst_detail(){
        return $this->belongsTo('App\GSTRate', 'gst_id', 'id');
    }
}
