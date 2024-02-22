<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use App\Reference;
use App\Other;
use App\Booking;
use App\BookingLineItems;
use Illuminate\Support\Facades\Validator;
use App\Niche;
use App\MemorialRoom;
use Carbon\Carbon;
use App\GSTRate;
use App\BookingNicheItem;
use App\Discount;
use Illuminate\Support\Arr;
use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\File;
use Spatie\Permission\Models\Role;
use App\User;
use Illuminate\Support\Facades\Auth;

class BookingGeneralController extends Controller
{

/**
 * @OA\Get(
 *     tags={"Booking General"},
 *     path="/api/booking-general",
 *     summary="Get list Booking General",
 *     operationId="getListBookingGeneral",
 *     @OA\Parameter(
 *         name="limit",
 *         in="query",
 *         description="limit page",
 *         @OA\Schema(
 *             type="integer",
 *             format="int64",
 *             minimum=1,
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="filter",
 *         in="query",
 *         description="filter by object json {email : customer@example.com}",
 *          @OA\Schema(
 *              type="string"
 *          )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation",
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized",
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error server",
 *     ),
 *     security={
 *         {"bearerAuth": {}}
 *     }
 * )
 */
public function listBookingGeneral(Request $request){
    $limit = intval($request->query('limit'));
    $filter = json_decode($request->query('filter'));
    ///
    $booking = Booking::with('clients')->with('status')
    ->with(['sale_agreement' => function($query){
        $query->with(['invoices' => function($q){
            $q->select('id', 'invoice_no', 'sale_agreement_id');
            $q->with(['payment' => function($_q){
                $_q->select('id', 'payment_no', 'invoice_id');
            }]);
        }]);
    }])
    ->orderBy('id', 'DESC');
    ////
    $type_1 = "d/m/Y";
    $type_2 = "d/m";
    $type_expectations_1 = "Y-m-d";
    $type_expectations_2 = "m-d";
    ////
    if (!empty($filter->all)){
        $key_word = $filter->all;
        //
        $key_word = BookingController::custom_date($type_1, $type_2, $key_word, $type_expectations_1, $type_expectations_2);
        $booking->where(function ($query) use ($key_word) {
            $query->where('booking_no', 'like', '%'.$key_word.'%')
                ->orWhere('created_at', 'like', '%'.$key_word.'%')
                ->orWhereHas('clients', function (Builder $query) use ($key_word) {
                    $query->where('display_name', 'like', '%'.$key_word.'%');
                })
                ->orWhereHas('booking_line_items.booking_type', function (Builder $query) use ($key_word) {
                    $query->where('reference_value_text', 'like', '%'.$key_word.'%');
                })
                ->orWhereHas('sale_agreement', function (Builder $query) use ($key_word) {
                    $query->where('sale_agreement_no', 'like', '%'.$key_word.'%');
                })
                ->orWhereHas('status', function (Builder $query) use ($key_word) {
                    $query->where(
                        [
                            ['reference_type', '=', 'booking_status'],
                            ['reference_value_text', 'like', '%'.$key_word.'%'],
                        ]
                    );
                });
                    
        });
    }
    if(!empty($filter->services)){
        $service_name = $filter->services;
        $booking->WhereHas('booking_line_items.booking_type', function (Builder $query) use ($service_name) {
            $query->where('reference_value_text', 'like', '%'.$service_name.'%');
        });
    }
    if(!empty($filter->clients_name)){
        $clients_name = $filter->clients_name;
        $booking->whereHas('clients', function (Builder $query) use ($clients_name) {
            $query->where('display_name', 'like', '%'.$clients_name.'%');
        });
    }
    if(!empty($filter->booking_date)){
        $booking_date = $filter->booking_date;
        $booking_date = BookingController::custom_date($type_1, $type_2, $booking_date, $type_expectations_1, $type_expectations_2);
        $booking->where('created_at', 'like', '%'.$booking_date.'%');
    }
    if(!empty($filter->booking)){
        $booking_no = $filter->booking;
        $booking->where('booking_no', 'like', '%'.$booking_no.'%');
    }
    if(!empty($filter->status)){
        $status = $filter->status;
        $booking->whereHas('status', function (Builder $query) use ($status) {
            $query->where(
                [
                    ['reference_type', '=', 'booking_status'],
                    ['reference_value_text', 'like', '%'.$status.'%'],
                ]
            );
        });
    }
    if(!empty($filter->sale_agreement)){
        $sale_agreement = $filter->sale_agreement;
        $booking->whereHas('sale_agreement', function (Builder $query) use ($sale_agreement) {
            $query->where('sale_agreement_no', 'like', '%'.$sale_agreement.'%');
        });
    }
               
        
    $booking = $booking->paginate($limit)->toArray();  
    $booking['status'] = "success";
    return response()->json($booking, 200);
}

/**
 * @OA\Get(
 *     tags={"Booking General"},
 *     path="/api/booking-general/{id}",
 *     summary="Get detail Booking General",
 *     operationId="showBookingGeneral",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID Booking Item",
 *         @OA\Schema(
 *             type="integer",
 *             format="int64",
 *             minimum=1,
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation",
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized",
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error server",
 *     ),
 *     security={
 *         {"bearerAuth": {}}
 *     }
 * )
 */

    public function showBookingGeneral($id){
        $booking = Booking::where('id', $id)->with(['booking_line_items' => function($query){
            $query->with(['information' => function($q){
                $q->with(['relationship_to_applicant' => function($_q){
                    $_q->select('id', 'reference_type', 'reference_value_text');
                }]);
                $q->select('id', 'booking_line_items_id', 'full_name', 'death_anniversary', 'relationship_to_applicant');
            }])
            ->with(['client' => function($query){
                $query->with('isTgor', 'salutation', 'religion', 'preferredContactBy');
            }])
            ->with(['niche' => function($query){
                $query->with('type', 'category');
            }])
            ->with(['other' => function($query){
                $query->with('children', 'type', 'contractor');
            }])
            ->with(['room' => function($query){
                $query->with('status');
            }])
            ->with(['booking' => function($query){
                $query->with('status');
            }])
            ->with(
                // 'booking_discount',
                'funeral_director',
                'booking_type',
                'room_type',
                'serviceType',
                'duration',
                'contractor',
                'relationship_with_license',
                'co_license',
                'coord_title',
                'relationship_to_applicant',
                'departed_title',
                'event',
                "referral"
            );
        }])
        ->with(['clients' => function($query){
            $query->with('isTgor', 'salutation', 'religion', 'preferredContactBy');
        }])
        ->with('status')->withCount("sale_agreement");
        if($booking){
            return response()->json(
                [
                    'status' => 'success',
                    'data'   =>  $booking->first()
                ], 200);
        }else{
            return response()->json(
                [
                    'status' => 'error',
                    'errors' => 'Cannot find Booking'
                ], 404);
        }
    }

/**
 * @OA\Post(
 *     tags={"Booking General"},
 *     path="/api/booking-general/{id}",
 *     summary="Update Booking General",
 *     operationId="updateBookingGeneral",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Booking ID",
 *         @OA\Schema(
 *             type="integer",
 *         )
 *     ),
 *    @OA\Parameter(
 *         name="booking",
 *         in="query",
 *         description="[{}, {}]",
 *          @OA\Schema(
 *              type="string"
 *          )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation",
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized",
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error server",
 *     ),
 *     security={
 *         {"bearerAuth": {}}
 *     }
 * )
 */

    public function updateBookingGeneral(Request $request, $id){
        if(!empty($request->booking)){
            $arr_booking = $request->booking;
            if(is_string($arr_booking)){
                $arr_booking = json_decode($arr_booking);
            }
            $booking = Booking::where('id',$request->id_booking)->first();
            $getRole = User::find(Auth::user()->id)->getRoleNames();
            if($getRole[0] !== "super-admin" && $booking->is_invoice === 1) {
                return response()->json([
                    'status' => 'error',
                    'errors' => "You don't have permission to edit booking."
                ], 422);
            }
            $count = 0;
            $arrID = [];
            
            foreach($arr_booking as $data){
                $data = (array)$data;
                if(!empty($data['id'])){
                    array_push($arrID, $data['id']);
                }
                $booking_type = Reference::where('id', $data["booking_type_id"])->first();
                if($booking_type->reference_value_text == 'Niches'){
                    $validate_booking = [
                        'booking_type_id'      =>  'required',
                        'service_id'           =>  'required',
                        'application_date'     =>  'required',
                        'lease_expiry_date'    =>  'required',
                        'amount'               =>  'required',
                        'co_license'           =>  'required',
                        // 'is_referral'          =>  'required',
                        'book_funeral_director'=>  'required',
                    ];
                    if(!empty($data["co_license"])){
                        $co_license = Reference::where('id', $data["co_license"])->first();
                    }
                    // if(!empty($data["is_referral"])){
                    //     $referral = Reference::where('id', $data["is_referral"])->first();
                    // }
                    if(!empty($data["book_funeral_director"]) && $data["book_funeral_director"] == "Yes"){
                        $validate_booking = Arr::add($validate_booking, 'funeral_director_id', 'required');
                    }
                    if(!empty($data["co_license"]) && $co_license->reference_value_text === "Yes"){
                        $validate_booking = Arr::add($validate_booking, 'co_license_name', 'required');
                        $validate_booking = Arr::add($validate_booking, 'co_license_passport', 'required'); 
                    }
                    if(!empty($data["co_license_same_address"]) && $co_license->co_license_same_address === "No"){
                        $validate_booking = Arr::add($validate_booking, 'co_license_street_name', 'required');
                    }
                    // if(!empty($data["is_referral"]) && $referral->reference_value_text === "Yes"){
                    //     $validate_booking = Arr::add($validate_booking, 'referral_name', 'required');
                    // }
                }else if($booking_type->reference_value_text == 'Memorial Rooms'){
                    $validate_booking =  [
                        'booking_date'                  =>  'required',
                        'booking_type_id'               =>  'required',
                        'event_id'                      =>  'required',
                        'service_id'                    =>  'required',
                        'amount'                        =>  'required',
                        'room_type'                     =>  'required',
                        'book_funeral_director'         =>  'required',
                        'applicant_is_coordinator'      =>  'required',
                        'check_in_date'                 =>  'required',
                        'check_in_time'                 =>  'required',
                        'check_out_date'                =>  'required',
                        'check_out_time'                =>  'required',
                        // 'is_referral'                   =>  'required',
                    ];
                    if(!empty($data["tv_photo"])){
                        $validate_booking = Arr::add($validate_booking, 'tv_photo', 'mimes:jpg,jpeg,png,bmp,tiff | max:4096');
                    }
                    // if(!empty($data["is_referral"])){
                    //     $referral = Reference::where('id', $data["is_referral"])->first();
                    // }
                    // if($referral->reference_value_text === "Yes"){
                    //     $validate_booking = Arr::add($validate_booking, 'referral_name', 'required');
                    // }
                }
                else if($booking_type->reference_value_text == 'Additional Services'){
                    $validate_booking = [
                        'booking_date'                  =>  'required',
                        'booking_type_id'               =>  'required',
                        'service_id'                    =>  'required',
                        'amount'                        =>  'required',
                        "service_type_id"               =>  'required'
                    ];
                    if(!empty($data["service_id"])){
                        $service = Other::where('id', $data["service_id"])->whereNull('deleted_at')->first();
                        $reference_name = Reference::where('id', $service->type)->first();
                        if($service->contractor->reference_value_text === 'Yes'){
                            $validate_booking = Arr::add($validate_booking, 'contractor_id', 'required');
                        }
                        if($reference_name->reference_value_text === 'Rent'){
                            $validate_booking = Arr::add($validate_booking, 'start_date', 'required');
                            $validate_booking = Arr::add($validate_booking, 'end_date', 'required');
                        }
                    }
                }
                $v = Validator::make($data, $validate_booking);
                if ($v->fails())
                {
                    return response()->json([
                        'status' => 'error',
                        'errors' => $v->errors()->first()
                    ], 422);
                }
            }
            
            $booking_line_item = BookingLineItems::where('booking_id', $id)->pluck('id')->all();
            $arr_id_delete = array_diff($booking_line_item, $arrID);
            $booking_delete = BookingLineItems::whereIn('id', $arr_id_delete)->delete();

            $status_booking = Reference::where('id',$request->status)->first();
            $status_booked = Reference::where('reference_type', 'booking_status')->where('reference_value_text', 'Booked')->first();
            $booking->status = $status_booking->reference_value_text == "Draft" ? $status_booked->id : $request->status;
            $booking->save();
            $now = now();
            $gst = GSTRate::where(\DB::raw("DATE(gst_start_date)"), '<=', \DB::raw("DATE('".$now->format('Y-m-d')."')"))
            ->where(function ($query) use ($now) {
                $query->where('gst_end_date','>=', "'".$now->format('Y-m-d h:i:s')."'")
                    ->orWhereNull('gst_end_date');
            })
            ->orderBy('gst_start_date', 'DESC')->first();
            //dd($gst);
            foreach($arr_booking as $key => $data){
                $data = (array)$data;
                
                $booking_type = Reference::where('id', $data["booking_type_id"])->first();

                if($booking_type->reference_value_text == 'Niches'){
                    $status_service_niches = Reference::where([
                        ['reference_type', '=', 'status_services_niches'],
                        ['reference_value_text', '=', 'Cancelled'],
                    ])->first();

                    if($status_booking->reference_value_text == "Cancelled")
                    {
                        Niche::where('id',$data["service_id"])->update(['booking_id' => null]);
                    }
                    if(empty($data["id"])){
                        $check_niche = Niche::where('id', $data["service_id"])->whereNotNull('booking_id')->first();
                    }else{
                        $check_room = Niche::where('booking_id', '<>', $id)->where('id', $data["service_id"])->first();
                    }
                    if(!empty($check_niche) && empty($data["id"])){
                        return response()->json([
                            'status' => 'error',
                            'errors' => "Niche has been booked"
                        ], 422);
                    }
                    if(!empty($data["information"])){
                        $information = $data["information"];
                        if(is_string($information)){
                            $information = json_decode($data["information"]);
                        }
                    }
                    $start_date = Carbon::parse($data["lease_start_date"]);

                    $amount = $data["amount"];
                    $discount_custom = 0;
                    //check discount
                    if(@$data['discount']){
                        $detail = Discount::where('id',$data['discount'])->first();
                        if($detail){
                            if($detail->amount_type == 89 and $detail->percent > 0 ){
                                $discount_custom = $amount * $detail->percent;
                            }else{
                                $discount_custom =  $detail->amount;
                            }
                        }
                    }
                    $tax_amount = 0;
                    if($gst){
                        $tax_amount = ($amount - $discount_custom ) * $gst->rate;
                    }
                    $co_license = Reference::where('id', $data["co_license"])->first();
                    $status_service_niche_reserved = Reference::where([
                        ['reference_type', '=', 'status_services_niches'],
                        ['reference_value_text', '=', 'Reserved'],
                    ])->first();
                    $id_booking_line = BookingLineItems::select('id')->withTrashed()->orderBy('id', "DESC")->first();
                    $line_items_booking_no = 1;
                    if($id_booking_line){
                        $line_items_booking_no = (int)$id_booking_line->id + 1;
                    }
                    $number_booking_no = str_pad($line_items_booking_no,4,'0',STR_PAD_LEFT);
                    $short_year = Carbon::now()->format('y');
                    if(!empty($data["id"])){
                        $booking_line_item = BookingLineItems::updateOrCreate(
                            [
                                'id'                =>  $data["id"],
                                'booking_type_id'   =>  $data["booking_type_id"]
                            ],
                            [
                                'booking_id'                    =>  $id,
                                'service_id'                    =>  $data["service_id"],
                                'application_date'              =>  $data["application_date"],
                                'lease_start_date'              =>  $data["lease_start_date"],
                                'lease_expiry_date'             =>  $data["lease_expiry_date"],
                                'amount'                        =>  $amount,
                                'tax_amount'                    =>  $tax_amount,
                                'booking_date'                  =>  $now->toDateTimeString(),
                                'co_license'                    =>  $data["co_license"],
                                'co_license_name'               =>  $data["co_license_name"] ? $data["co_license_name"] : null,
                                'co_license_email'              =>  $data["co_license_email"] ? $data["co_license_email"] : null,
                                'co_license_phone'              =>  $data["co_license_phone"] ? $data["co_license_phone"] : null,
                                'co_license_passport'           =>  $data["co_license_passport"] ? $data["co_license_passport"] : null,
                                'co_license_postal_code'        =>  $data["co_license_postal_code"] ? $data["co_license_postal_code"] : null,
                                'co_license_street_name'        =>  $data["co_license_street_name"] ? $data["co_license_street_name"] : null,
                                'relationship_with_license'     =>  $data["relationship_with_license"] ? $data["relationship_with_license"] : null,
                                'status'                        =>  $status_booking->reference_value_text == "Cancelled" ? $status_service_niches->id :  $data["status"],
                                'book_funeral_director'         =>  $data["book_funeral_director"] ? $data["book_funeral_director"] : null,
                                'funeral_director_id'           =>  $data["funeral_director_id"] ? $data["funeral_director_id"] : null,
                                'same_address'                  =>  !empty($data["co_license_same_address"]) ? $data["co_license_same_address"] : "No"
                            ]
                        );
                    }
                    else{
                        $booking_line_item = BookingLineItems::create([
                            'booking_no'                    =>  'N-'.$short_year.'-'.$number_booking_no,
                            'booking_id'                    =>  $id,
                            'booking_type_id'               =>  $data["booking_type_id"],
                            'service_id'                    =>  $data["service_id"],
                            'application_date'              =>  $data["application_date"],
                            'lease_start_date'              =>  $data["lease_start_date"],
                            'lease_expiry_date'             =>  $data["lease_expiry_date"],
                            'amount'                        =>  $amount,
                            'tax_amount'                    =>  $tax_amount,
                            'booking_date'                  =>  $now->toDateTimeString(),
                            'co_license'                    =>  $data["co_license"],
                            'co_license_name'               =>  $data["co_license_name"] ? $data["co_license_name"] : null,
                            'co_license_email'              =>  $data["co_license_email"] ? $data["co_license_email"] : null,
                            'co_license_phone'              =>  $data["co_license_phone"] ? $data["co_license_phone"] : null,
                            'co_license_passport'           =>  $data["co_license_passport"] ? $data["co_license_passport"] : null,
                            'co_license_postal_code'        =>  $data["co_license_postal_code"] ? $data["co_license_postal_code"] : null,
                            'co_license_street_name'        =>  $data["co_license_street_name"] ? $data["co_license_street_name"] : null,
                            'relationship_with_license'     =>  $data["relationship_with_license"] ? $data["relationship_with_license"] : null,
                            'status'                        =>  $status_service_niche_reserved->id,
                            // 'is_referral'                   =>  $data["is_referral"],
                            // 'referral_name'                 =>  $data["referral_name"] ? $data["referral_name"] : null,
                            'book_funeral_director'         =>  $data["book_funeral_director"] ? $data["book_funeral_director"] : null,
                            'funeral_director_id'           =>  $data["funeral_director_id"] ? $data["funeral_director_id"] : null,
                            'same_address'                  =>  !empty($data["co_license_same_address"]) ? $data["co_license_same_address"] : "No"
                        ]);
                        
                    }
                    if($booking_line_item){
                        foreach ($information as $item) {
                            if(!empty($item['id'])){
                                $booking_niche_item = BookingNicheItem::where('id', $item['id'])->first();
                                $booking_niche_item->update($item);
                            }else{
                                $booking_niche_item = BookingNicheItem::create([
                                    'booking_line_items_id'         =>  $booking_line_item->id,
                                    'full_name'                     =>  $item["full_name"],
                                    'relationship_to_applicant'     =>  $item["relationship_to_applicant"],
                                    'death_anniversary'             =>  $item["death_anniversary"],
                                ]);
                            }
                            
                        }
                    }
                    if($data["service_id"] != $booking_line_item->niche->id){
                        $booking_line_item->niche->update(['booking_id' => null]);
                        // $niche_booking = Niche::where('id', $data["service_id"])->whereNull('deleted_at')->first();
                        // if($niche_booking){
                        //     $niche_booking->update(['booking_id' => $id, 'booking_line_item' => $booking_line_item->id]);
                        // }
                    }
                }
                else if($booking_type->reference_value_text == 'Memorial Rooms'){
                    if(empty($data["id"])){
                        $check_room = MemorialRoom::where('id', $data["service_id"])->whereNotNull('booking_id')->first();
                    }else{
                        $check_room = MemorialRoom::where('booking_id', '<>', $id)->where('id', $data["service_id"])->first();
                    }

                    if(!empty($check_room)){
                        return response()->json([
                            'status' => 'error',
                            'errors' => "Memorial Room has been booked"
                        ], 422);
                    }

                    if(!empty($data["check_out_date"]) && !empty($data["check_out_time"]) && !empty($data["check_in_time"])){
                        $check_overlap = BookingLineItems::where("booking_type_id", $data["booking_type_id"])->where("service_id", $data["service_id"])->get();
                        // dd($check_overlap);exit;
                        $checkout_datetime_request =  Carbon::parse($data["check_out_date"])->toDateString(). " " .$data["check_out_time"];
                        $checkin_datetime_request = Carbon::parse($data["check_in_date"])->toDateString(). " " .$data["check_in_time"];
                        $flag = false;
                        foreach ($check_overlap as $key => $value) {
                            if(empty($data["id"]) || $data["id"] != $value["id"]){
                                if(!empty($value["check_out_date"]) && !empty($value["check_out_time"]) && !empty($value["check_in_time"])){
                                    $first = Carbon::parse($value["check_out_date"])->toDateString(). " " .$value["check_out_time"];
                                    $second = Carbon::parse($value["check_in_date"])->toDateString(). " " .$value["check_in_time"];
                                    $check1 = Carbon::parse($checkout_datetime_request)->between($first, $second);
                                    $check2 = Carbon::parse($checkin_datetime_request)->between($first, $second);
                                    if($check1 || $check2){
                                        $flag = true;
                                    } 
                                }
                            }
                        }
                        if($flag){
                            return response()->json([
                                'status' => 'error',
                                'errors' => "This room has been booked for this timing. Please choose different room or change timing"
                            ], 422);
                        }
                    }

                    $status_service_room = Reference::where([
                        ['reference_type', '=', 'status_services_rooms'],
                        ['reference_value_text', '=', 'Cancelled'],
                    ])->first();
                    if($status_booking->reference_value_text == "Cancelled")
                    {
                        $data['status'] = $status_service_room->id;
                    }
                    
                    $url = "";
                    if(is_file($data['tv_photo'])){
                        $photo = $data['tv_photo'];
                        $name = $photo->getClientOriginalName();
                        //
                        $path = public_path().'/booking/memorial-room/';
                        if(File::makeDirectory($path, 0777, true, true)){
                            $photo->move($path, $name);
                        }else{
                            $photo->move($path, $name);
                        }
                        $url = url('/booking/memorial-room/'.$name);
                    }else{
                        $name = null;
                    }
                    if(!empty($url)){
                        $data["tv_photo_of_departed"] = $url;
                    }
                    $line_items_booking_no = 1;
                    $id_booking_line = BookingLineItems::select('id')->withTrashed()->orderBy('id', "DESC")->first();
                    if($id_booking_line){
                        $line_items_booking_no = (int)$id_booking_line->id + 1;
                    }
                    $number_booking_no = str_pad($line_items_booking_no,4,'0',STR_PAD_LEFT);
                    $short_year = Carbon::now()->format('y');
                    if(empty($data["booking_no"])){
                        $data['booking_no'] = 'P-'.$short_year.'-'.$number_booking_no;
                    }
                    if(!empty($data["id"])){
                        $booking_line_item = BookingLineItems::updateOrCreate([
                            'id' => $data["id"],
                            'booking_type_id' => $data["booking_type_id"]
                        ], $data);
                    }else{

                        $status_service_room = Reference::where([
                            ['reference_type', '=', 'status_services_rooms'],
                            ['reference_value_text', '=', 'Reserved'],
                        ])->first();
                        $data['status'] =   $status_service_room->id;
                        $data['booking_id'] = $id;
                        $booking_line_item = BookingLineItems::create($data);
                    }
                    $amount = $data["amount"];
                    //check discount
                    $discount_custom = 0;
                    if(@$data['discount']){
                        $detail = Discount::where('id',$data['discount'])->first();
                        if($detail){
                            if($detail->amount_type == 89 and $detail->percent > 0 ){
                                $discount_custom = $amount * $detail->percent;
                            }else{
                                $discount_custom =  $detail->amount;
                            }
                        }
                    }
                    $tax_amount = 0;
                    if($gst){
                        $tax_amount = ($amount - $discount_custom ) * $gst->rate;
                    }
                    $booking_line_item->tax_amount = $tax_amount;
                    $booking_line_item->save();

                    if($data["service_id"] != $booking_line_item->room->id){
                        $booking_line_item->room->update(['booking_id' => null]);
                        $room_booking = MemorialRoom::where('id', $request->service_id)->whereNull('deleted_at')->first();
                        if($room_booking){
                            $room_booking->update(['booking_id' => $id]);
                        }
                    }
                }
                else if($booking_type->reference_value_text == 'Additional Services'){
                    // dd($data);
                    $status_service_product = Reference::where([
                        ['reference_type', '=', 'status_services_products'],
                        ['reference_value_text', '=', 'Cancelled'],
                    ])->first();

                    if($status_booking->reference_value_text == "Cancelled")
                    {
                        $data['status'] = $status_service_product->id;
                    }
                    $id_booking_line = BookingLineItems::select('id')->withTrashed()->orderBy('id', "DESC")->first();
                    $line_items_booking_no = 1;
                    if($id_booking_line){
                        $line_items_booking_no = (int)$id_booking_line->id + 1;
                    }
                    $number_booking_no = str_pad($line_items_booking_no,4,'0',STR_PAD_LEFT);
                    $short_year = Carbon::now()->format('y');
                    if(empty($data['booking_no'])){
                        $data['booking_no'] = 'S-'.$short_year.'-'.$number_booking_no;
                    }
                    if(!empty($data["id"])){
                        $booking_line_item = BookingLineItems::updateOrCreate([
                            'id'                =>  $data["id"],
                            'booking_type_id'   =>  $data["booking_type_id"]
                        ], $data);
                        $amount = $data["amount"];
                        //check discount
                        $discount_custom = 0;
                        if(@$data['discount']){
                            $detail = Discount::where('id',$data['discount'])->first();
                            if($detail){
                                if($detail->amount_type == 89 and $detail->percent > 0 ){
                                    $discount_custom = $amount * $detail->percent;
                                }else{
                                    $discount_custom =  $detail->amount;
                                }
                            }
                        }
                        $tax_amount = 0;
                        if($gst){
                            $tax_amount = ($amount - $discount_custom ) * $gst->rate;
                        }
                        $booking_line_item->tax_amount = $tax_amount;
                        $booking_line_item->save();
                    }else{
                        $data["booking_id"] = $id;
                        $service = Other::where('id', $data["service_id"])->whereNull('deleted_at')->first();
                        if($service->type_reference->reference_value_text == 'Sale'){
                            $status_service_product = Reference::where([
                                ['reference_type', '=', 'status_services_products'],
                                ['reference_value_text', '=', 'Sold'],
                            ])->first();
                        }else if($service->type_reference->reference_value_text == 'Rent'){
                            $status_service_product = Reference::where([
                                ['reference_type', '=', 'status_services_products'],
                                ['reference_value_text', '=', 'Rented Out'],
                            ])->first();
                        }
                        $data['status'] =    $status_service_product->id;     
                        $booking_line_item = BookingLineItems::create($data);
                        $tax_amount = 0;
                        if($gst){
                            $tax_amount = $data["amount"]*$gst->rate;
                        }
                        $booking_line_item->tax_amount = $tax_amount;
                        $booking_line_item->save();
                    }
                }else{
                    return response()->json([
                        'status' => 'error',
                        'errors' => "Something bad happened, please try later"
                    ], 422);
                }
                if(++$count == count($arr_booking)) {
                    return response()->json([
                        'status' => 'Successfully Updated Booking',
                        'data'  => $booking
                    ], 200);
                }
            }
        }
        else {
            return response()->json([
                'status' => 'error',
                'errors' => "Something bad happened, please try later"
            ], 422);
        }
    }

/**
 * @OA\Post(
 *     tags={"Booking General"},
 *     path="/api/status-booking-general",
 *     summary="Get List Status for Booking General",
 *     operationId="index",
 *     @OA\Parameter(
 *         name="id",
 *         in="query",
 *         required=true,
 *         description="Booking ID",
 *         @OA\Schema(
 *             type="integer",
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation",
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized",
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error server",
 *     ),
 *     security={
 *         {"bearerAuth": {}}
 *     }
 * )
 */
    public function listStatusBooking(Request $request){
        $booking = Booking::where('id', $request->id)->first();
        $booking_status = Reference::where([
            ['reference_type', '=', 'booking_status'],
            ['reference_value_text', '=', 'Booked']
        ])->first();

        if($booking->status == $booking_status->id){
            $reference = Reference::where('reference_type', '=', 'booking_status')
                ->whereIn('reference_value_text', ['Cancelled', 'Booked'])->get();
            return response()->json(
                [
                    'status' => 'success',
                    'data'   => $reference,
                ], 200);
        }else{
            $reference = Reference::where([
                ['reference_type', '=', 'booking_status'],
                ['reference_value_text', '<>', 'Draft']
            ])->get();
            return response()->json(
                [
                    'status' => 'success',
                    'data'   => $reference,
                ], 200);
        }

    }

}
