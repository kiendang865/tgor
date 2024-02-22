<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleAgreementLineItem extends Model
{
    use SoftDeletes;
    protected $table = 'sale_agreements_line_items';
    protected $fillable = [
        'sale_agreement_id', 'booking_id', 'line_item_id','isInvoice'
    ];
    public $timestamps = true;

    public function booking_line_item(){
        return $this->belongsTo('App\BookingLineItems', 'line_item_id', 'id');
    }
    public function sale_agreement(){
        return $this->belongsTo('App\SaleAgreement', 'sale_agreement_id', 'id');
    }
}
