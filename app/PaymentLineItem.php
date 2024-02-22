<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentLineItem extends Model
{
    use SoftDeletes;
    protected $table = 'payment_line_items';
    protected $fillable = [
        'payment_id', 'invoice_id', 'line_item_id'
    ];

    public function line_item(){
        return $this->belongsTo('App\InvoiceLineItem', 'line_item_id');
    }
    public function invoice(){
        return $this->belongsTo('App\Invoice', 'invoice_id');
    }
}
