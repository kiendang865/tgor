<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes;
    protected $table = 'invoices';
    protected $fillable = [
        'invoice_no', 'invoice_date', 'status', 'sale_agreement_id', 'user_id', 'total_amount', 'total_tax_amount','remarks','total','officer','discount_list','discount_id', 'total_discount'
    ];
    public function client(){
        return $this->belongsTo('App\User', 'user_id');
    }
    public function invoice_line_item(){
        return $this->hasMany('App\InvoiceLineItem', 'invoice_id');
    }
    public function payment(){
        return $this->hasMany('App\Payment', 'invoice_id');
    }
    public function sale_agreement(){
        return $this->belongsTo('App\SaleAgreement', 'sale_agreement_id');
    }
    public function attachments()
    {
        return $this->morphMany('App\Attachments', 'attachable');
    }
    public function admin(){
        return $this->belongsTo('App\User', 'officer');
    }
    public function discount(){
        return $this->belongsTo('App\Discount', 'discount_id');
    }
}
