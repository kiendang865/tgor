<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceLineItem extends Model
{
    use SoftDeletes;
    protected $table = 'invoices_line_items';
    protected $fillable = [
        'invoice_id', 'sale_agreement_id', 'line_item_id'
    ];
    public function sale_agreement_line_item(){
        return $this->belongsTo('App\SaleAgreementLineItem', 'line_item_id','id');
    }
    public function sale_agreement(){
        return $this->belongsTo('App\SaleAgreement', 'sale_agreement_id','id');
    }
}
