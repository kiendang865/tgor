<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \Znck\Eloquent\Traits\BelongsToThrough;
use App\GSTRate;

class BookingLineItems extends Model
{
    use SoftDeletes;
    use BelongsToThrough;
    protected $table = "booking_line_items";
    protected $fillable = [
        'booking_id', 'booking_type_id', 'service_id', 'application_no', 'application_date', 'start_date', 'end_date', 'expiry_date', 'duration_of_lease', 'discount', 'amount',
        'first_name', 'last_name', 'relationship_to_applicant', 'death_anniversary', 'booking_date', 'tax_amount', 'departed_title', 'departed_full_name', 'departed_last_name',
        'tv_display_name', 'tv_departed_notes', 'date_of_death', 'church_attended', 'departed_notes', 'book_funeral_director', 'funeral_director_id',
        'applicant_is_coordinator', 'coord_title', 'coordinator_full_name', 'coord_last_name', 'check_in_date', 'check_out_date', 'remarks', 'renting_date',
        'return_date', 'duration', 'contractor_id', 'service_type_id', 'room_type', 'event_id', 'co_license', 'interment_date','co_license_name', 'co_license_email', 'co_license_phone', 'relationship_with_license',
        'co_license_passport', 'co_license_postal_code', 'co_license_street_no', 'co_license_street_name', 'co_license_unit_no', 'co_license_building_name','is_sale','status','tv_photo_of_departed','tv_life_years','tv_wake_service','tv_encoffin_service','tv_cottage_leaves',
        'lease_start_date', 'lease_expiry_date', 'mobile', 'check_in_time', 'check_out_time', 'booking_no', 'is_referral', 'referral_name', 'renewal_from_id', 'same_address'
    ];
    protected $appends = [
        'display_name',
        'gst_amount',
        'total_amount',
        'gst_amount_format',
        'amount_format',
        'time_now',
        'discount_amount',
        'sale_agreement_total_amount'
    ];
    public $timestamps = true;

    public function getDisplayNameAttribute() {
        return $this->first_name.' '.$this->last_name;
    }
    public function type_booking(){
        return $this->belongsTo('App\Reference', 'booking_type_id', 'id');
    }
    public function serviceType(){
        return $this->belongsTo('App\Other', 'service_type_id', 'id');
    }
    public function booking_type(){
        return $this->belongsTo('App\Reference', 'booking_type_id', 'id');
    }
    public function niche(){
        return $this->belongsTo('App\Niche', 'service_id', 'id');
    }
    public function room(){
        return $this->belongsTo('App\MemorialRoom', 'service_id', 'id');
    }
    public function other(){
        return $this->belongsTo('App\Other', 'service_id', 'id');
    }
    public function service_other(){
        return $this->belongsTo('App\Other', 'service_id', 'id')->with(['category']);
    }
    public function booking_discount(){
        return $this->belongsTo('App\GSTRate', 'discount', 'id');
    }
    public function booking(){
        return $this->belongsTo('App\Booking', 'booking_id');
    }
    public function contractor(){
        return $this->belongsTo('App\Company', 'contractor_id');
    }
    public function funeral_director(){
        return $this->belongsTo('App\Company', 'funeral_director_id');
    }
    public function client(){
        return $this->BelongsToThrough('App\User', 'App\Booking');
    }
    public function room_type(){
        return $this->belongsTo('App\Reference', 'room_type', 'id');
    }
    public function duration(){
        return $this->belongsTo('App\Reference', 'duration', 'id');
    }
    public function information(){
        return $this->hasMany('App\BookingNicheItem');
    }
    public function duration_other(){
        return $this->belongsTo('App\Reference', 'duration');
    }
    public function relationship_with_license(){
        return $this->belongsTo('App\Reference', 'relationship_with_license', 'id');
    }
    public function co_license(){
        return $this->belongsTo('App\Reference', 'co_license', 'id');
    }
    public function is_co_license(){
        return $this->belongsTo('App\Reference', 'co_license', 'id');
    }
    public function coord_title(){
        return $this->belongsTo('App\Reference', 'coord_title', 'id');
    }
    public function relationship_to_applicant(){
        return $this->belongsTo('App\Reference', 'relationship_to_applicant', 'id');
    }
    public function is_relationship_to_applicant(){
        return $this->belongsTo('App\Reference', 'relationship_to_applicant', 'id');
    }
    public function departed_title(){
        return $this->belongsTo('App\Reference', 'departed_title', 'id');
    }
    public function event(){     
        return $this->belongsTo('App\Reference', 'event_id', 'id');
    }
    public function getDiscount(){
        return $this->belongsTo('App\Discount', 'discount', 'id');
    }
    public function getGstAmountAttribute(){
        $now = now();
        $gst = GSTRate::where('gst_start_date', '<=', $now->format('Y-m-d').' 00:00:00')
                ->orderBy('gst_start_date', 'DESC')->first();
        $gst_amount = 0;
        if($gst){
            $gst_amount = $this->amount * $gst->rate;
        }
        // return number_format($gst_amount, 2, '.', ',');
        return $gst_amount;
    }
    public function getTotalAmountAttribute(){
        if(!empty($this->getDiscount)){
            if($this->getDiscount->type_amount->reference_value_text === 'Value'){
                $total = ($this->amount + $this->tax_amount - $this->getDiscount->amount);
                return number_format($total, 2, '.', ',');
            }
            if($this->getDiscount->type_amount->reference_value_text === 'Percentage'){
                $discount = $this->amount * $this->getDiscount->percent;
                $total = ($this->amount + $this->tax_amount);
                return number_format($total, 2, '.', ',');
            }
        }else{
            $total = ($this->amount + $this->tax_amount);
            return number_format($total, 2, '.', ',');
        }
    }
    public function getGstAmountFormatAttribute(){
        $now = now();
        $gst = GSTRate::where('gst_start_date', '<=', $now->format('Y-m-d').' 00:00:00')
                ->orderBy('gst_start_date', 'DESC')->first();
        $gst_amount = 0;
        if($gst){
            $gst_amount = $this->amount * $gst->rate;
        }
        return number_format($gst_amount, 2, '.', ',');
    }
    public function getAmountFormatAttribute(){
        return number_format($this->amount, 2, '.', ',');
    }
    public function getTimeNowAttribute(){
        $start_date = $this->start_date;
        $expiry_date = $this->expiry_date;
        $now = now();
        $is_between = false;

        if($now <= $start_date)
        {
            $is_between = true;
        }

        return $is_between;
    }
    public function status(){
        return $this->belongsTo('App\Reference', 'status', 'id');
    }
    public function status_niche(){
        return $this->belongsTo('App\Reference', 'status', 'id');
    }
    public function getDiscountAmountAttribute(){
        if(!empty($this->getDiscount)){
            if($this->getDiscount->type_amount->reference_value_text === 'Value'){
                $discount = $this->getDiscount->amount;
                return $discount;
            }
            if($this->getDiscount->type_amount->reference_value_text === 'Percentage'){
                $discount = $this->amount * $this->getDiscount->percent;
                return $discount;
            }
        }
    }
    public function referral(){
        return $this->belongsTo('App\Reference', 'is_referral', 'id');
    }
    public function getSaleAgreementTotalAmountAttribute(){
        $now = now();
        $gst = GSTRate::where('gst_start_date', '<=', $now->format('Y-m-d').' 00:00:00')
                ->orderBy('gst_start_date', 'DESC')->first();
        $gst_rate = 0;
        if($gst){
            $gst_rate = $gst->rate;
        }
        $total = $this->amount + ($this->amount * $gst_rate);
        return number_format($total, 2, '.', ',');
    }
}
