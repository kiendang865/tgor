<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Booking;
use App\BookingLineItems;
use App\Niche;
use App\User;
use App\MemorialRoom;
use App\Remarks;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Reference;
use App\Other;
use App\Invoice;
use App\InvoiceLineItem;
use App\Payment;
use App\PaymentLineItem;
use App\SaleAgreement;
use App\SaleAgreementLineItem;
use Date;
use DateTime;
use Illuminate\Support\Arr;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Common\Type;
use Illuminate\Database\Eloquent\Builder;
use App\GSTRate;
use App\BookingNicheItem;
use Illuminate\Support\Facades\File;
use App\Duration;
use ZipArchive;
use Boolean;

class BookingController extends Controller
{

/**
 * @OA\Post(
 *     tags={"Booking"},
 *     path="/api/booking",
 *     summary="Create Booking",
 *     operationId="createBooking",
 *     @OA\Parameter(
 *         name="user_id",
 *         in="query",
 *         required=true,
 *         description="User ID",
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

    public function createBooking(Request $request){

        $booking_no = 1;
        $id_booking = Booking::select('id')->withTrashed()->orderBy('id', "DESC")->first();
        if($id_booking){
            $booking_no = (int)$id_booking->id + 1;
        }
        $number_booking_no = str_pad($booking_no, 4, '0',STR_PAD_LEFT);
        $short_year = Carbon::now()->format('y');
        $status_booked = Reference::where('reference_type', 'booking_status')->where('reference_value_text', 'Booked')->first();
        $status_draft = Reference::where('reference_type', 'booking_status')->where('reference_value_text', 'Draft')->first();
        $new_booking = Booking::create([
            'user_id'           =>  $request->user_id,
            'booking_no'        =>  "B-".$short_year."-".$number_booking_no,
            'status'            =>  !empty($request->is_draft) ? $status_draft->id : $status_booked->id
        ]);
        $new_booking = Booking::where('id', $new_booking->id)->with("clients")->first();
        return response()->json([
            'status'    => 'success',
            'data'      => $new_booking
        ]);
    }

/**
 * @OA\Get(
 *     tags={"Booking"},
 *     path="/api/booking",
 *     summary="Get list Booking",
 *     operationId="getListBooking",
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
 *         name="user_id",
 *         in="query",
 *         description="User ID",
 *         @OA\Schema(
 *             type="integer",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="type",
 *         in="query",
 *         description="Type Service (Niche, Room, Other)",
 *         @OA\Schema(
 *             type="string",
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

public function getListBooking(Request $request){
    $limit = intval($request->query('limit'));
    $filter = json_decode($request->query('filter'));
    $type = $request->get('type');
    $sort_by = $request->get('sortby');
    $type_sort = $request->get('sortDesc') === "true" ? "DESC" : "ASC";
        ///
    $type_1 = "d/m/Y";
    $type_2 = "d/m";
    $type_expectations_1 = "Y-m-d";
    $type_expectations_2 = "m-d";
        ///
    if(!empty($type)){
        switch ($type) {
            case 'Other':
                $user_id = $request->user_id;
                $booking = BookingLineItems::whereHas('booking_type', function (Builder $query) {
                    $query->where([
                            ['reference_type', '=', 'booking_type'],
                            ['reference_value_text', '=', 'Additional Services'],
                        ]);
                    })
                    ->with(['booking', 'other', 'client', 'contractor', 'serviceType','status'])
                    ->leftJoin('other', 'other.id', '=', 'booking_line_items.service_id')
                    ->select('booking_line_items.*');
                if($sort_by === "service_name"){
                    $booking ->orderBy('other.service_name', $type_sort);
                }
                if($sort_by === "created_at"){
                    $booking ->orderBy('booking_line_items.created_at', $type_sort);
                }
                ////
                if(isset($request->user_id)){
                    $booking->whereHas('booking', function (Builder $query) use ($user_id) {
                        $query->where('user_id',$user_id);
                    });
                }
                if (!empty($filter->all)){
                    $key_word = $filter->all;
                    //
                    $key_word = $this->custom_date($type_1, $type_2, $key_word, $type_expectations_1, $type_expectations_2);
                    $booking->where(function ($query) use ($key_word) {
                        $query->whereHas('serviceType', function (Builder $query) use ($key_word) {
                                $query->where('service_name', 'like', '%'.$key_word.'%');
                            })
                            ->orWhereHas('client', function (Builder $query) use ($key_word) {
                                $query->where('display_name', 'like', '%'.$key_word.'%');
                            })
                            ->orWhereHas('contractor', function (Builder $query) use ($key_word) {
                                $query->where('company_name', 'like', '%'.$key_word.'%');
                            });     
                    });
                }
                if(!empty($filter->service_type)){
                    $service_type = $filter->service_type;
                    $booking->whereHas('serviceType', function (Builder $query) use ($service_type) {
                        $query->where('service_name', 'like', '%'.$service_type.'%');
                    });
                }
                if(!empty($filter->clients_name)){
                    $clients_name = $filter->clients_name;
                    $booking->whereHas('client', function (Builder $query) use ($clients_name) {
                        $query->where('display_name', 'like', '%'.$clients_name.'%');
                        $query->orderBy('display_name', 'ASC');
                    });
                }
                if(!empty($filter->contractor)){
                    $contractor = $filter->contractor;
                    $booking->whereHas('contractor', function (Builder $query) use ($contractor) {
                        $query->where('company_name', 'like', '%'.$contractor.'%');
                    });
                }
            break;
            case 'Niche':
                $user_id = $request->user_id;

                $booking = BookingLineItems::whereHas('booking_type', function (Builder $query) {
                    $query->where([
                            ['reference_type', '=', 'booking_type'],
                            ['reference_value_text', '=', 'Niches'],
                        ]);
                    })
                    ->orderBy('booking_date', 'DESC')->with(['niche','information', 'booking','status', 'getDiscount']);
                if(isset($request->user_id)){
                    // var_dump($booking->get());exit;
                    $booking->whereHas('booking', function (Builder $query) use ($user_id) {
                        $query->where('user_id',$user_id);
                    });
                }
                if(!empty($filter->all)){
                    $key_word = $filter->all;
                    //
                    $key_word = $this->custom_date($type_1, $type_2, $key_word, $type_expectations_1, $type_expectations_2);
                    $booking->where(function ($query) use ($key_word) {
                        $query->whereHas('information', function (Builder $query) use ($key_word) {
                            $query->where('full_name', 'like', '%'.$key_word.'%');
                        })
                        ->orWhereHas('niche', function (Builder $query) use ($key_word) {
                            $query->where('reference_no', 'like', '%'.$key_word.'%');
                        })
                        ->orWhereHas('client', function($query) use ($key_word){
                            $query->where('display_name', 'like', '%'.$key_word.'%');
                        })
                        ->orWhere('lease_start_date', 'like', '%'.$key_word.'%')
                        ->orWhere('lease_expiry_date', 'like', '%'.$key_word.'%');
                    });
                }
                if(!empty($filter->started_lease_date)){
                    $started_lease_date = $filter->started_lease_date;
                    $started_lease_date = $this->custom_date($type_1, $type_2, $started_lease_date, $type_expectations_1, $type_expectations_2);
                    $booking->where('lease_start_date', 'like', '%'.$started_lease_date.'%');
                }
                if(!empty($filter->expired_lease_date)){
                    $expired_lease_date = $filter->expired_lease_date;
                    $expired_lease_date = $this->custom_date($type_1, $type_2, $expired_lease_date, $type_expectations_1, $type_expectations_2);
                    $booking->where('lease_expiry_date', 'like', '%'.$expired_lease_date.'%');
                }
                if(!empty($filter->occupant)){
                    $occupant = $filter->occupant;
                    $booking->whereHas('information', function (Builder $query) use ($occupant) {
                        $query->where('full_name', 'like', '%'.$occupant.'%');
                    });
                }
                if(!empty($filter->niche_id)){
                    $niche_id = $filter->niche_id;
                    $booking->whereHas('niche', function (Builder $query) use ($niche_id) {
                        $query->where('reference_no', 'like', '%'.$niche_id.'%');
                    });
                }
                if(!empty($filter->client_name)){
                    $client_name = $filter->client_name;
                    $booking->whereHas('client', function($query) use ($client_name){
                        $query->where('display_name', 'like', '%'.$client_name.'%');
                    });
                }

            break;
            case 'Room':
                $user_id = $request->user_id;
                $booking = BookingLineItems::whereHas('booking_type', function (Builder $query) {
                    $query->where([
                            ['reference_type', '=', 'booking_type'],
                            ['reference_value_text', '=', 'Memorial Rooms'],
                        ]);
                    })->orderBy('booking_date', 'DESC')->with(['room', 'booking','client', 'funeral_director','status']);
                if(isset($request->user_id)){
                    $booking->whereHas('booking', function (Builder $query) use ($user_id) {
                        $query->where('user_id',$user_id);
                    });
                }
                if (!empty($filter->all)){
                    $key_word = $filter->all;
                    //
                    $key_word = $this->custom_date($type_1, $type_2, $key_word, $type_expectations_1, $type_expectations_2);
                    //
                    $booking->where(function ($query) use ($key_word) {
                        $query->whereHas('client', function (Builder $query) use ($key_word) {
                            $query->where('display_name', 'like', '%'.$key_word.'%');
                        })
                        ->orWhereHas('room', function (Builder $query) use ($key_word) {
                            $query->where('room_no', 'like', '%'.$key_word.'%');
                        })
                        ->orWhereHas('funeral_director', function (Builder $query) use ($key_word) {
                            $query->where('company_name', 'like', '%'.$key_word.'%');
                        })
                        ->orWhere('check_in_date', 'like', '%'.$key_word.'%')
                        ->orWhere('departed_full_name', 'like', '%'.$key_word.'%');
                    });
                }
                if(!empty($filter->client_name)){
                    $client_name = $filter->client_name;
                    $booking->whereHas('client', function (Builder $query) use ($client_name) {
                        $query->where('display_name', 'like', '%'.$client_name.'%');
                    });
                }
                if(!empty($filter->departed_full_name)){
                    $departed_full_name = $request->departed_full_name;
                    $booking->where('departed_full_name', 'like', '%'.$departed_full_name.'%');
                }
                if(!empty($filter->room_name)){
                    $room_name = $filter->room_name;
                    $booking->whereHas('room', function (Builder $query) use ($room_name) {
                        $query->where('room_no', 'like', '%'.$room_name.'%');
                    });
                }
                if(!empty($filter->funeral_director)){
                    $funeral_director = $filter->funeral_director;
                    $booking->whereHas('funeral_director', function (Builder $query) use ($funeral_director) {
                        $query->where('company_name', 'like', '%'.$funeral_director.'%');
                    });
                }
                if(!empty($filter->check_in_date)){
                    $check_in_date = $filter->check_in_date;
                    $check_in_date = $this->custom_date($type_1, $type_2, $check_in_date, $type_expectations_1, $type_expectations_2);
                    $booking->where('check_in_date', 'like', '%'.$check_in_date.'%');
                }
            break;
        }
        // dd($booking->get()->toArray());
        $booking = $booking->paginate($limit)->toArray();  
        $booking['status'] = "success";
        return response()->json($booking, 200);
    }
    return response()->json([
        'status' => 'error',
        'errors' => "Something bad happened, can't find service"
    ], 404);

    
}

///
public static function custom_date($type_input_1, $type_input_2, $date, $type_expectations_1, $type_expectations_2){
    if (DateTime::createFromFormat($type_input_1, $date) !== FALSE) {
        $result = date_format(date_create_from_format($type_input_1, $date), $type_expectations_1);
    }
    elseif(DateTime::createFromFormat($type_input_2, $date) !== FALSE){
        $result = date_format(date_create_from_format($type_input_2, $date), $type_expectations_2);
    }
    else{
        $result = $date;
    }
    return $result;
}


/**
 * @OA\Delete(
 *     tags={"Booking"},
 *     path="/api/service",
 *     summary="Delete Service",
 *     operationId="deleteService",
 *      @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType="application/x-www-form-urlencoded",
 *             @OA\Schema(
 *                 type="object",
 *                 @OA\Property(
 *                     property="ids",
 *                     description="ID Booking Line Items",
 *                     type="string",
 *                     default="[1,2,3]"
 *                  ),
 *                  required={"ids"}
 *             )
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="type",
 *         in="query",
 *         description="Type Service (Niche, Room, Other)",
 *         @OA\Schema(
 *             type="string",
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

public function deleteService(Request $request)
{
    $ids = $request->ids;
    $type = $request->get('type');
    if(is_string($ids)){
        $ids = json_decode($ids);
    }

    if(!empty($type)){
        switch ($type) {
            case 'Other':
                $booking = BookingLineItems::whereIn('id', $ids);
                foreach($booking->get() as $key=>$value){
                    $service_id = $value->service_id;
                    $other = Other::find($service_id);
                    $other->booking_id = null;
                    $other->save();
                }
                $booking = $booking->delete();
            break;
            case 'Niche':
                $booking = BookingLineItems::whereIn('id', $ids);
                foreach($booking->get() as $key=>$value){
                    $service_id = $value->service_id;
                    $booking_line_id = $value->id;
                    $niche = Niche::find($service_id);
                    $niche->booking_id = null;
                    $niche->save();
                    BookingNicheItem::where('booking_line_items_id', '=', $booking_line_id)->delete();
                }
                $booking = $booking->delete();
            break;
            case 'Room': 
                $booking = BookingLineItems::whereIn('id', $ids);
                foreach($booking->get() as $key=>$value){
                    $service_id = $value->service_id;
                    $room = MemorialRoom::find($service_id);
                    $room->booking_id = null;
                    $room->save();
                }
                $booking = $booking->delete();
            break;
        }
        return response()->json(
            [
                'status' => 'Successfully Deleted Service',
            ], 200);
    }
    return response()->json(
        [
            'status' => 'error',
            'errors' => 'Cannot find Service'
        ], 404);
}
/**
 * @OA\Delete(
 *     tags={"Booking"},
 *     path="/api/booking",
 *     summary="Delete Booking",
 *     operationId="deleteBooking",
 *      @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType="application/x-www-form-urlencoded",
 *             @OA\Schema(
 *                 type="object",
 *                 @OA\Property(
 *                     property="ids",
 *                     description="ID Other",
 *                     type="string",
 *                     default="[1,2,3]"
 *                  ),
 *                  required={"ids"}
 *             )
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

public function deleteBooking(Request $request)
{
    $ids = $request->ids;
    if(is_string($ids)){
        $ids = json_decode($ids);
    }
    $booking = Booking::whereIn('id', $ids)->delete();
    if($booking) {  
        $booking_line_items = BookingLineItems::whereIn('booking_id', $ids)->pluck('id')->all();
        $sale_agreements = SaleAgreement::whereIn('booking_id', $ids)->pluck('id')->all();
        $invoice = Invoice::whereIn('sale_agreement_id', $sale_agreements)->pluck('id')->all();
        ///
        BookingNicheItem::whereIn('booking_line_items_id', $booking_line_items)->delete();
        BookingLineItems::whereIn('booking_id', $ids)->delete();
        SaleAgreementLineItem::whereIn('booking_id', $ids)->delete();
        Payment::whereIn('invoice_id', $invoice)->delete();
        PaymentLineItem::whereIn('invoice_id', $invoice)->delete();
        Invoice::whereIn('sale_agreement_id', $sale_agreements)->delete();
        InvoiceLineItem::whereIn('sale_agreement_id', $sale_agreements)->delete();
        SaleAgreement::whereIn('booking_id', $ids)->delete();
        
        return response()->json(
            [
                'status' => 'Successfully Deleted Booking',
            ], 200);
    }
    
    return response()->json(
        [
            'status' => 'error',
            'errors' => 'Cannot find Booking'
        ], 404);
}

/**
 * @OA\Get(
 *     tags={"Service"},
 *     path="/api/service-booking/{id}",
 *     summary="Get detail Booking",
 *     operationId="showServiceBooking",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID Booking Line Item",
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

public function showServiceBooking($id){
    $booking_line = BookingLineItems::with(['information' => function($query){
        $query->with('relationship_to_applicant');
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
        'booking',
        'status',
        "referral"
    )
    ->find($id);
    $expiry_date = new Carbon($booking_line->expiry_date);
    
    $start_date = new Carbon( $booking_line->start_date);
    
    $years = $expiry_date->diffInYears($start_date);
    
    $now = Carbon::now();

    if($expiry_date->year > $now->year){

        $duration_of_lease = $expiry_date->diffInYears($now)+1;

        if($start_date->year > $now->year)
        {
            $duration_of_lease = $expiry_date->diffInYears($start_date);
        }

        $booking_line->duration_of_lease = $duration_of_lease."/".$years." years";
        
        
    }
    else{
        $booking_line->duration_of_lease ="1/1 years";
    }
    if($booking_line){
        return response()->json(
            [
                'status' => 'success',
                'data'   =>  $booking_line->toArray()
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
 * @OA\Put(
 *     tags={"Booking"},
 *     path="/api/booking/{id}",
 *     summary="Update Booking",
 *     operationId="updateBooking",
 *     @OA\Parameter(
 *         name="user_id",
 *         in="query",
 *         required=true,
 *         description="User Id",
 *         @OA\Schema(
 *             type="integer",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="booking_no",
 *         in="query",
 *         description="Booking No",
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="booking_type_id",
 *         in="query",
 *         required=true,
 *         description="Booking Type Id",
 *         @OA\Schema(
 *             type="integer",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="service_id",
 *         in="query",
 *         required=true,
 *         description="Service Id",
 *         @OA\Schema(
 *             type="integer",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="application_date",
 *         in="query",
 *         description="Application Date",
 *         @OA\Schema(
 *             type="string",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="interment_date",
 *         in="query",
 *         description="Interment Date",
 *         @OA\Schema(
 *             type="string",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="start_date",
 *         in="query",
 *         description="Start Date",
 *         @OA\Schema(
 *             type="string",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="expiry_date",
 *         in="query",
 *         description="Duration Of Lease",
 *         @OA\Schema(
 *             type="integer",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="discount",
 *         in="query",
 *         description="Discount",
 *         @OA\Schema(
 *             type="integer",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="amount",
 *         in="query",
 *         description="Amount",
 *         @OA\Schema(
 *             type="integer",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="booking_date",
 *         in="query",
 *         description="Booking Date",
 *         @OA\Schema(
 *             type="string",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="departed_title",
 *         in="query",
 *         description="Departed Title",
 *         @OA\Schema(
 *             type="string",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="departed_first_name",
 *         in="query",
 *         description="Departed First Name",
 *         @OA\Schema(
 *             type="string",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="departed_last_name",
 *         in="query",
 *         description="Departed Last Name",
 *         @OA\Schema(
 *             type="string",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="tv_departed_display_name",
 *         in="query",
 *         description="Tv Departed Display Name",
 *         @OA\Schema(
 *             type="string",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="tv_departed_notes",
 *         in="query",
 *         description="Tv Departed Notes",
 *         @OA\Schema(
 *             type="string",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="church_attended",
 *         in="query",
 *         description="Church Attended",
 *         @OA\Schema(
 *             type="string",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="departed_notes",
 *         in="query",
 *         description="Departed Notes",
 *         @OA\Schema(
 *             type="string",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="book_funeral_director",
 *         in="query",
 *         description="Book Funeral Director",
 *         @OA\Schema(
 *             type="string",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="funeral_director_id",
 *         in="query",
 *         description="Funeral Director Id",
 *         @OA\Schema(
 *             type="integer",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="applicant_is_coordinator",
 *         in="query",
 *         description="Applicant Is Coordinator (Yes/No)",
 *         @OA\Schema(
 *             type="string",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="coord_title",
 *         in="query",
 *         description="Cord Title (if applicant_is_coordinator = No)",
 *         @OA\Schema(
 *             type="string",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="coord_first_name",
 *         in="query",
 *         description="Cord First Name (if applicant_is_coordinator = No)",
 *         @OA\Schema(
 *             type="string",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="coord_last_name",
 *         in="query",
 *         description="Cord Last Name (if applicant_is_coordinator = No)",
 *         @OA\Schema(
 *             type="string",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="coord_contact_no",
 *         in="query",
 *         description="Cord Contact No (if applicant_is_coordinator = No)",
 *         @OA\Schema(
 *             type="string",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="death_date",
 *         in="query",
 *         description="Death Date",
 *         @OA\Schema(
 *             type="string",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="check_in_date",
 *         in="query",
 *         description="Check In Date",
 *         @OA\Schema(
 *             type="string",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="check_out_date",
 *         in="query",
 *         description="Check Out Date",
 *         @OA\Schema(
 *             type="string",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="start_date",
 *         in="query",
 *         description="Start Date",
 *         @OA\Schema(
 *             type="string",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="end_date",
 *         in="query",
 *         description="End Date",
 *         @OA\Schema(
 *             type="string",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="remarks",
 *         in="query",
 *         description="Remarks",
 *         @OA\Schema(
 *             type="string",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="duration",
 *         in="query",
 *         description="Duration",
 *         @OA\Schema(
 *             type="string",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="contractor_id",
 *         in="query",
 *         description="Contractor Id",
 *         @OA\Schema(
 *             type="integer",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="relationship_to_applicant",
 *         in="query",
 *         description="Relationship To Applicant (For Booking Room)",
 *         @OA\Schema(
 *             type="string",
 *         )
 *     ),
 *    @OA\Parameter(
 *         name="service_type_id",
 *         in="query",
 *         description="Service Type ID",
 *         @OA\Schema(
 *             type="integer",
 *         )
 *     ),
 *    @OA\Parameter(
 *         name="information",
 *         in="query",
 *         description="filter by array object [{}, {}]",
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

public function updateBooking(Request $request, $id){
    if(!empty($request->booking_type_id)){
        $now = now();
        $booking_type = Reference::where('id', $request->booking_type_id)->whereNull('deleted_at')->first();
        $booking_line_item = BookingLineItems::where('id', $id)->first();
        $gst = GSTRate::where('gst_start_date', '<=', $now->format('Y-m-d').' 00:00:00')
                ->orderBy('gst_start_date', 'DESC')->first();
        if($booking_type->reference_value_text === 'Niches') {
            $validate_booking = [
                'booking_type_id'      =>  'required',
                'service_id'           =>  'required',
                'application_date'     =>  'required',
                'lease_expiry_date'    =>  'required',
                'amount'               =>  'required',
                'co_license'           =>  'required',
                'is_referral'          =>  'required'
            ];
            if(!empty($request->co_license)){
                $co_license = Reference::where('id', $request->co_license)->first();
            }
            if(!empty($request->is_referral)){
                $referral = Reference::where('id', $request->is_referral)->first();
            }
            if(!empty($request->co_license) && $co_license->reference_value_text === "Yes"){
                $validate_booking = Arr::add($validate_booking, 'co_license_name', 'required');
                $validate_booking = Arr::add($validate_booking, 'co_license_email', 'required|email');
                $validate_booking = Arr::add($validate_booking, 'co_license_phone', 'required');
                $validate_booking = Arr::add($validate_booking, 'co_license_passport', 'required'); 
                $validate_booking = Arr::add($validate_booking, 'co_license_street_name', 'required');
            }
            if(!empty($request->is_referral) && $referral->reference_value_text === "Yes"){
                $validate_booking = Arr::add($validate_booking, 'referral_name', 'required');
            }
            $v = Validator::make($request->all(), $validate_booking);
            if ($v->fails())
            {
                return response()->json([
                    'status' => 'error',
                    'errors' => $v->errors()->first()
                ], 422);
            }
            if(!$booking_line_item) {
                return response()->json([
                    'status' => 'error',
                    'errors' => "Cannot find booking"
                ], 404);
            }
            if($request->interment_date != null){
                $status_niches_occupied = Reference::where([
                    ['reference_type', '=', 'status_services_niches'],
                    ['reference_value_text', '=', 'Sold - Occupied'],
                ])->first();
                $booking_line_item->status = $status_niches_occupied->id;
            }
            $expiry_date = new Carbon($request->lease_expiry_date);
            $start_date = new Carbon($request->lease_start_date);
            $years = $expiry_date->diffInYears($start_date);

            $now = Carbon::now();
            if($expiry_date->year > $now->year){

                $duration_of_lease = $expiry_date->diffInYears($now);

                if($start_date->year > $now->year)
                {
                    $duration_of_lease = $expiry_date->diffInYears($start_date);
                }

                $booking_line_item->duration_of_lease = $duration_of_lease."/".$years." years";
                
            }
            else{
                $booking_line_item->duration_of_lease ="1/1 years";
            }
            // $booking_line_item->save();
            $data = $request->all();
            $tax_amount = 0;
            if($gst){
                $tax_amount = ($request->amount)*($gst->rate);
            }
            $data['tax_amount'] = $tax_amount;

            $booking_line_item = $booking_line_item->update($data);

            if($booking_line_item){
                foreach ($request->information as $item) {
                    if(isset($item['id'])){
                        $booking_niche_item = BookingNicheItem::where('id', $item['id'])->first();
                        $booking_niche_item->update($item);
                    }else{
                        $booking_line = BookingLineItems::where('id', $id)->first();
                        $new_niche_line = new BookingNicheItem();
                        $new_niche_line->booking_line_items_id = $booking_line->id;
                        $new_niche_line->first_name = $item['first_name'];
                        $new_niche_line->last_name = $item['last_name'];
                        $new_niche_line->relationship_to_applicant = $item['relationship_to_applicant'];
                        $new_niche_line->death_anniversary = $item['death_anniversary'];
                        $new_niche_line->save();
                    }
                }
            }
            return response()->json(
                [
                    'status' => 'Successfully Updated Booking', 
                ], 200);
        }
        if($booking_type->reference_value_text === 'Memorial Rooms') {
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
                'is_referral'                   =>  'required'
            ];
            if(!empty($request->tv_photo)){
                $validate_booking = Arr::add($validate_booking, 'tv_photo', 'mimes:jpg,jpeg,png,bmp,tiff | max:4096');
            }
            if(!empty($request->is_referral)){
                $referral = Reference::where('id', $request->is_referral)->first();
            }
            if(!empty($request->is_referral) && $referral->reference_value_text === "Yes"){
                $validate_booking = Arr::add($validate_booking, 'referral_name', 'required');
            }
            $v = Validator::make($request->all(), $validate_booking);
            if ($v->fails())
            {
                return response()->json([
                    'status' => 'error',
                    'errors' => $v->errors()->first()
                ], 422);
            }
            if(!$booking_line_item) {
                return response()->json([
                    'status' => 'error',
                    'errors' => "Cannot find booking"
                ], 404);
            }
            if(!empty($request->check_out_date) && !empty($request->check_out_time) && !empty($request->check_in_time)){
                $check_overlap = BookingLineItems::where("booking_type_id", $request->booking_type_id)->where("service_id", $request->service_id)->get();
                // dd($check_overlap);exit;
                $checkout_datetime_request =  Carbon::parse($request->check_out_date)->toDateString(). " " .$request->check_out_time;
                $checkin_datetime_request = Carbon::parse($request->check_in_date)->toDateString(). " " .$request->check_in_time;
                $flag = false;
                foreach ($check_overlap as $key => $value) {
                    if(empty($request->id) || $request->id != $value["id"]){
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
            if($request->service_id != $booking_line_item->room->id){
                $booking_line_item->room->update(['booking_id' => null]);
                $room_booking = MemorialRoom::where('id', $request->service_id)->whereNull('deleted_at')->first();
                if($room_booking){
                    $room_booking->update(['booking_id' => $id]);
                }
            }
            $url = "";
            if(!empty($request['tv_photo'])){
                $photo = $request['tv_photo'];
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
                $request["tv_photo_of_departed"] = $url;
            }
            $tax_amount = 0;
            if($gst){
                $tax_amount = ($request->amount)*($gst->rate);
            }
            $data = $request->all();
            $data['tax_amount'] = $tax_amount;
            $booking_line_item = $booking_line_item->update($data);
            return response()->json(
                [
                    'status' => 'Successfully Updated Booking',
                ], 200);
        }
        if($booking_type->reference_value_text === 'Additional Services') {
            $validate_booking = [
                'booking_date'                  =>  'required',
                'booking_type_id'               =>  'required',
                'service_id'                    =>  'required',
                'amount'                        =>  'required',
                "service_type_id"               =>  'required'
            ];
            if(!empty($request->service_id)){
                $service = Other::where('id', $request->service_id)->whereNull('deleted_at')->first();
                $reference_name = Reference::where('id', $service->type)->first();
                if($service->contractor->reference_value_text === 'Yes'){
                    $validate_booking = Arr::add($validate_booking, 'contractor_id', 'required');
                }
                if($reference_name->reference_value_text === 'Rent'){
                    $validate_booking = Arr::add($validate_booking, 'start_date', 'required');
                    $validate_booking = Arr::add($validate_booking, 'end_date', 'required');
                }
            }
            $v = Validator::make($request->all(), $validate_booking);
            if ($v->fails())
            {
                return response()->json([
                    'status' => 'error',
                    'errors' => $v->errors()->first()
                ], 422);
            }
            if(!$booking_line_item) {
                return response()->json([
                    'status' => 'error',
                    'errors' => "Cannot find booking"
                ], 404);
            }
            $tax_amount = 0;
            if($gst){
                $tax_amount = ($request->amount)*($gst->rate);
            }
            $data = $request->all();
            $data['tax_amount'] = $tax_amount;

            $booking_line_item = $booking_line_item->update($data);
            return response()->json(
                [
                    'status' => 'Successfully Updated Booking',
                ], 200);
        }
    }else{
        return response()->json([
            'status' => 'error',
            'errors' => "Something bad happened, please try later"
        ], 422);
    }
}   
/**
 * @OA\Post(
 *     tags={"Booking"},
 *     path="/api/import-booking",
 *     summary="Import file Booking excel",
 *      @OA\RequestBody(
 *          @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 type="object",
 *                 @OA\Property(
 *                     property="file_excel",
 *                     description="file_excel Booking",
 *                     type="file",
 *                 ),
 *             )
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
public function importBooking(Request $request){
    ini_set('max_execution_time', 3000);
    $file = $request->file("file_excel");
    if(!empty($file)){
        $reader = ReaderEntityFactory::createXLSXReader();
        $reader->setShouldFormatDates(true);
        // $reader = ReaderFactory::create(Type::XLSX); // for XLSX files
        // $a = public_path('/Niche Renewal DB 202007014 (1).xlsx');
        // $file = public_path("/Niche Renewal DB 202007014.xlsx");

        $reader->open($file);
        $customer = array();
        $booking = array();
        
        foreach ($reader->getSheetIterator() as $sheet) {
            if($sheet->getName() == "Niche Renewal"){
                foreach ($sheet->getRowIterator() as $key=>$row) {
                    
                    $cells = $row->getCells();
                    $flag = $cells[0]->getValue();
                    if(!($flag === "Niche No.")){
                        if(!($flag === " " || $flag == null)){
                            //
                            $niche_no               = $this->checkNullValue($cells[0]->getValue());
                            $booking_no             = $this->checkNullValue($cells[1]->getValue());
                            $date                   = $this->checkNullValue($cells[2]->getValue());
                            $lessee_id              = $this->checkNullValue($cells[3]->getValue());
                            $co_lessee_id           = $this->checkNullValue($cells[4]->getValue());
                            $renewal_niche_price    = $this->checkNullValue($cells[6]->getValue());
                            // $discount_amount        = $this->checkNullValue($cells[7]->getValue());
                            $sub_total              = $this->checkNullValue($cells[9]->getValue());
                            $total                  = $this->checkNullValue($cells[11]->getValue());
                            $lease_expiry           = $this->checkNullValue($cells[13]->getValue());
                            $booking_date = Carbon::parse($date)->format('Y-m-d');
                            // $lease_expiry = Carbon::parse($lease_expiry)->format('Y-m-d');
                            $booking_line_item = array(
                                'niche_no'              => $niche_no, 
                                'booking_no'            => $booking_no, 
                                'lessee_id'             => $lessee_id,
                                'renewal_niche_price'   => $renewal_niche_price,
                                'lease_expiry'          => $lease_expiry,
                                'booking_date'          => $booking_date,
                                'total'                 => $total,
                                'sub_total'             => $sub_total,
                                'co_lessee_id'          => $co_lessee_id,
                                // 'discount'              => $discount_amount,
                                'start_date'            => Carbon::parse($date)->format('Y-m-d'),
                                'expiry_date'           => Carbon::createFromFormat('Y-m-d',$lease_expiry.'-12-31')->toDateTimeString()
                            );
                            
                            array_push($booking, $booking_line_item);
                        }
                    } 
                }
            }
            // var_dump(count($booking));exit;
            if($sheet->getName() === "Customer Info"){
                foreach ($sheet->getRowIterator() as $key=>$row) {
                    $cells = $row->getCells();
                    $flag = $cells[1]->getValue();
                    $flag_2 = $cells[0]->getValue();
                    if(!($flag === "Salutation")){
                        if(!($flag_2 === " " || $flag_2 == null)){
                            //
                            $lessee_code        = $this->checkNullValue($cells[0]->getValue());
                            $last_name          = $this->checkNullValue($cells[2]->getValue());
                            $first_name         = $this->checkNullValue($cells[3]->getValue());
                            $phone_number       = $this->checkNullValue($cells[6]->getValue());
                            $email              = $this->checkNullValue($cells[7]->getValue());
                            $postal_code        = $this->checkNullValue($cells[11]->getValue());
                            $unit_no            = $this->checkNullValue($cells[10]->getValue());
                            $house_no           = $this->checkNullValue($cells[8]->getValue());
                            $street_name        = $this->checkNullValue($cells[9]->getValue());

                            $co_license = array(
                                'lessee_code'   => $lessee_code, 
                                'name'          => $first_name." ".$last_name, 
                                'email'         => $email,
                                'phone_number'  => $phone_number,
                                'unit_no'       => $unit_no,
                                'house_no'      => $house_no,
                                'street_name'   => $street_name,
                                'postal_code'   => $postal_code
                            );
                            array_push($customer, $co_license);
                        }
                    }
                }
            }
        } 
        $reader->close();
        // $arr_demo = array();
        $total_amount = 0;
        $total_tax_amount = 0;
        $total =  0;
        foreach($booking as $key=>$book){
            $number_booking_no = str_pad($book['booking_no'], 4, '0',STR_PAD_LEFT);
            $short_year = Carbon::now()->format('y');
            $key_temp = $key+1;
            if($key_temp <= count($booking)){
                if($key == 0){
                    $book['user_id'] = 0;
                    foreach($customer as $key_user => $user){
                        if($book['lessee_id'] === $user['lessee_code'])
                        {
                            $info_user = User::where('display_name', 'like', $user['name'])->first();
                            if($info_user){
                                $book['user_id'] = $info_user->id;
                            } 
                        }
                    }
                    $renew_booking = new Booking();
                    $renew_booking->user_id = $book['user_id'];
                    $renew_booking->booking_no= "B-".$short_year."-".$number_booking_no;
                    $renew_booking->save();
                    
                    if($renew_booking){
                        $booking_id = $renew_booking->id;
                    }
                    
                }elseif($booking[$key]['booking_no'] != $booking[$key-1]['booking_no']){
                    $book['user_id'] = 0;
                    foreach($customer as $key_user => $user){
                        if($book['lessee_id'] === $user['lessee_code'])
                        {
                            $info_user = User::where('display_name', 'like', "%".$user['name']."%")->first();
                            if($info_user){
                                $book['user_id'] = $info_user->id;
                            } 
                        }
                    }

                    $renew_booking = new Booking();
                    $renew_booking->user_id = $book['user_id'];
                    $renew_booking->booking_no= "B-".$book['booking_no'];
                    $renew_booking->save();
                    
                    if($renew_booking){
                        $booking_id = $renew_booking->id;
                    }
                }
            }   

            foreach($customer as $key_user => $user){
                if($book['co_lessee_id'] === $user['lessee_code'])
                {
                    $book['co_lessee'] = $user; 
                }
            }
            //
            $booking_type = Reference::Where(
                [
                    ['reference_type', 'like', 'booking_type'],
                    ['reference_value_text', 'like', 'Niches']
                ])->first();

            $niche_id = Niche::where('reference_no', 'like', $book['niche_no'])->first();
            if($niche_id){
                $niche_id_flag = $niche_id->id;
            }else{
                $niche_id_flag = 0;
            }
            //
            // if($book['discount'] != null)
            // {
            //     $name = ($book['discount']*100)."%";
            //     $check_discount = GSTRate::where('name',$name)->first();
            //     if(!empty($check_discount)){
            //         $discount = $check_discount->id;
            //     }
            //     else{
            //         $gst = new GSTRate();
    
            //         $gst->name = $name;                 
            //         $gst->rate = $book['discount'];
            //         $gst->save();
                    
            //         $discount = $gst->id;
            //     }
            // }
            
            $booking_line_item = New BookingLineItems();
            $booking_line_item->booking_id                  = $booking_id;
            $booking_line_item->booking_type_id             = $booking_type->id;
            $booking_line_item->service_id                  = $niche_id_flag;
            $booking_line_item->booking_date                = $book['booking_date'];
            $booking_line_item->tax_amount                  = $book['sub_total'];
            $booking_line_item->amount                      = $book['total'];
            // $booking_line_item->discount                    = $book['discount'] != null ? $discount : NULL;
            $booking_line_item->lease_start_date            = $book['start_date'];
            $booking_line_item->lease_expiry_date           = $book['expiry_date'];
            if($book['co_lessee_id'] != null){
                $co_liense = Reference::Where(
                    [
                        ['reference_type', 'like', 'co_liense'],
                        ['reference_value_text', 'like', 'Yes']
                    ])->first();
                ///
                $booking_line_item->co_license                  = $co_liense->id;
                $booking_line_item->co_license_name             = $book['co_lessee']['name'];
                $booking_line_item->co_license_email            = $book['co_lessee']['email'];
                $booking_line_item->co_license_phone            = $book['co_lessee']['phone_number'];
                $booking_line_item->co_license_postal_code      = $book['co_lessee']['postal_code'];
                $booking_line_item->co_license_street_no        = $book['co_lessee']['house_no'];
                $booking_line_item->co_license_street_name      = $book['co_lessee']['street_name'];
                $booking_line_item->co_license_unit_no          = $book['co_lessee']['unit_no'];
            }
            else{
                $co_liense = Reference::Where(
                    [
                        ['reference_type', 'like', 'co_liense'],
                        ['reference_value_text', 'like', 'No']
                    ])->first();
                //
                $booking_line_item->co_license                  = $co_liense->id;
            }
            
            $booking_line_item->save();

            $niches = Niche::find($booking_line_item->service_id);
            if(isset($niches)){
                $niches->booking_id = $booking_id != 0 ? $booking_id : NULL;
                $niches->booking_line_item = $booking_line_item->id;
                $niches->save();
            }
            // ==============================================
            // create new sale agreement
            $check_sale_agreement = SaleAgreement::where('booking_id', $booking_id)->whereNull('deleted_at')->first();
            $sale_agreement_flag = 0;
            if(empty($check_sale_agreement)){
                $sale_agreement_no = 1;
                $id_sale_agreement = SaleAgreement::select('id')->orderBy('id', "DESC")->first();
                if ($id_sale_agreement) {
                    $sale_agreement_no = (int)$id_sale_agreement->id + 1;
                }
                $number_sale_agreement_no = str_pad($sale_agreement_no,4,'0',STR_PAD_LEFT);
                $now = now();
                $dmY = Carbon::now()->format('dmY');
                if(!empty($renew_booking)){
                    $sale_agreement = SaleAgreement::create([
                        'sale_agreement_no'     =>  'N-'.$dmY.'-'.$number_sale_agreement_no,
                        'sale_agreement_date'   =>  $now->toDateTimeString(),
                        'booking_id'            =>  $booking_id,
                        'user_id'               =>  $renew_booking->user_id,
                        'sale_agreement_type'   =>  $booking_type->id
                    ]);
                    $sale_agreement_id = $sale_agreement->id;
                    $status = Reference::where('reference_type', 'booking_status')->where('reference_value_text', 'Agreement')->first();
                    $renew_booking->update(['status' => $status->id]);
                    $status_booking = Booking::where('id', $booking_id)->update(['status' => $status->id, 'is_sale' => 1]);
                }
                $sale_agreement_flag = $sale_agreement_id;
            }
            $sale_agreement_item = SaleAgreementLineItem::create([
                'sale_agreement_id'     =>  $sale_agreement_id,
                'booking_id'            =>  $booking_id,
                'line_item_id'          =>  $booking_line_item->id,
            ]);

            if($sale_agreement_flag != 0){
                $total_amount = (float)$booking_line_item->amount;
                $total_tax_amount = (float)$booking_line_item->tax_amount;
                $total =  $total_tax_amount;
            }elseif($sale_agreement_flag==0){
                $total_amount += (float)$booking_line_item->amount;
                $total_tax_amount += (float)$booking_line_item->tax_amount;
                $total = $total_amount + $total_tax_amount;
            }
            //update total sale_agreement 
            $sale_agreement->update([
                'total_amount'        =>    $total_amount,
                'total_tax_amount'    =>    $total_tax_amount,
                'total'               =>    $total
            ]);
            
            //end sale agreement
            //==============================================
            //new invoices
            $check_invoices = Invoice::where('sale_agreement_id', $sale_agreement_id)->whereNull('deleted_at')->first();
            if(empty($check_invoices)){
                $invoices_no = 1;
                $id_invoice = Invoice::select('id')->orderBy('id', "DESC")->first();
                if($id_invoice){
                    $invoices_no = (int)$id_invoice->id + 1;
                }
                $number_invoice_no = str_pad($invoices_no,4,'0',STR_PAD_LEFT);
                $year_month = Carbon::now()->format('ym');
                $now = now();
                if(!empty($sale_agreement)){
                    $invoices = Invoice::create([
                        'invoice_no'                =>  "CCPL-".$year_month.'-'.$number_invoice_no,
                        'invoice_date'              =>  $now->toDateTimeString(),
                        'sale_agreement_id'         =>  $sale_agreement_id,
                        'user_id'                   =>  $renew_booking->user_id,
                        
                    ]);
                    $invoices_id = $invoices->id;
                    $status = Reference::where('reference_type', 'booking_status')->where('reference_value_text', 'Fully Invoiced')->first();
                    $renew_booking->update(['status' => $status->id, 'is_invoice' => 1]);
                    $status_booking = Booking::where('id', $booking_id)->update(['status' => $status->id]);
                    $invoice_line_item = InvoiceLineItem::create([
                        'invoice_id'            =>  $invoices_id,
                        'sale_agreement_id'     =>  $sale_agreement_id,
                        'line_item_id'          =>  $booking_line_item->id
                    ]);
                    if($invoice_line_item){
                        SaleAgreementLineItem::where('id',$sale_agreement_item->id)->update(['isInvoice'=> 1]);
                    }
                        
                    $invoices->update([
                        'total_amount'              => $sale_agreement->total_amount,
                        'total_tax_amount'          => $sale_agreement->total_tax_amount,
                        'total'                     => $sale_agreement->total,
                    ]);
                    // end invoices
                    // ==============================================
                    // new payment
                    $check_payment = Payment::where('invoice_id', $invoices_id)->whereNull('deleted_at')->first();
                    if(empty($check_payment)){
                        $payment_no = 1;
                        $id_payment = Payment::select('id')->orderBy('id', "DESC")->first();
                        if($id_payment){
                            $payment_no = (int)$id_payment->id + 1;
                        }
                        $number_payment_no = str_pad($payment_no,4,'0',STR_PAD_LEFT);
                        $year_month = Carbon::now()->format('ym');
                        $now = now();
                        if(!empty($invoices)){
                            $payment = Payment::create([
                                'payment_no'                =>  "R-".$year_month.'-'.$number_payment_no,
                                'payment_date'              =>  $now->toDateTimeString(),
                                'invoice_id'                =>  $invoices_id,
                                'user_id'                   =>  $renew_booking->user_id,
                                
                            ]);
                            $status = Reference::where('reference_type', 'booking_status')->where('reference_value_text', 'Fully Paid')->first();
                            $renew_booking->update(['status' => $status->id]);
                            $status_booking = Booking::where('id', $booking_id)->update(['status' => $status->id]);
                            $payment_id = $payment->id;
                        }
                    }
                    $payment_line_item = PaymentLineItem::create([
                        'payment_id'            =>  $payment_id,
                        'invoice_id'            =>  $invoices_id,
                        'line_item_id'          =>  $invoices_id
                    ]);
                    if($payment_line_item){
                        InvoiceLineItem::where('id',$sale_agreement_item->id)->update(['is_payment'=> 1]);
                    }
                        
                    $payment->update([
                        'total_amount'              => $invoices->total_amount,
                        'total_tax_amount'          => $invoices->total_tax_amount,
                        'total'                     => $invoices->total,
                    ]);
                }
            }
            
        }
        
        // dd($arr_demo);
        // $path = public_path().'/import/booking/';
        // if(File::makeDirectory($path, 0777, true, true)){
        //     $name_file = $file->getClientOriginalName();
        //     $file->move($path,$name_file);
        // }else{
        //     $name_file = $file->getClientOriginalName();
        //     $file->move($path,$name_file);
        // }

        return response()->json(
            [
                'status' => 'Successfully import file'
            ], 200);
        
    }
        
    return response()->json(
        [
            'status' => 'error',
            'errors' => 'Not file'
        ], 404);
}

public static function checkNullValue($value){
    if($value == "" || $value == " "){
        $value = null;
    }
    return $value;
}

/**
 * @OA\GET(
 *     tags={"Niches"},
 *     path="/api/extension",
 *     summary="extension Niches",
 *     operationId="extensionNiche",
 *     @OA\Parameter(
 *         name="arr_id",
 *         in="query",
 *         required=true,
 *         description="Id",
 *         @OA\Schema(
 *             type="string",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="user_id",
 *         in="query",
 *         required=true,
 *         description="Id",
 *         @OA\Schema(
 *             type="integer",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="duration",
 *         in="query",
 *         required=true,
 *         description="Duration",
 *         @OA\Schema(
 *             type="string",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="discount",
 *         in="query",
 *         required=true,
 *         description="Discount",
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
public function extensionNiche(Request $request){ 
    if(isset($request->arr_id)){
        $arr_id = $request->arr_id;
        if(is_string($arr_id)){
            $arr_id = json_decode($arr_id);
        }
        $check_duplicate = BookingLineItems::whereIn('id',$arr_id)->pluck('service_id')->all();
        if(count(array_unique($check_duplicate))<count($check_duplicate))
        {
            return response()->json([
                'status' => 'error',
                'errors' => "You cannot renew multiple the same niches."
            ], 404);
        }
        $now = now();
        $booking_no = 1;
        $id_booking = Booking::select('id')->orderBy('id', "DESC")->first();
        if($id_booking){
            $booking_no = (int)$id_booking->id + 1;
        }
        $number_booking_no = str_pad($booking_no,4,'0',STR_PAD_LEFT);
        $short_year = $now->format('y');
        $gst = GSTRate::where('gst_start_date', '<=', $now->format('Y-m-d').' 00:00:00')
                ->orderBy('gst_start_date', 'DESC')->first();
        $booking_type = Reference::where('reference_type', 'booking_status')->where('reference_value_text', 'Booked')->first();
        $new_booking = Booking::create([
            'user_id'           =>  $request->user_id,
            'booking_no'        =>  "B-".$short_year."-".$number_booking_no,
            'status'            =>  $booking_type->id
        ]);
        
        foreach($arr_id as $id_line)
        { 

            $booking_niches = Reference::where('reference_type', 'booking_type')->where('reference_value_text', 'Niches')->first();

            $booking_line_item = BookingLineItems::where('booking_id',$id_line)->where('booking_type_id',$booking_niches->id)->first();
            // print_r($booking_line_item);exit;
            $check_niches = Duration::where([
                    ['niche_id',$booking_line_item['service_id']],
                    ['id',$request->duration]
                ])->whereNull('deleted_at')->first();

            $time = $check_niches->exten_year;
            $co_license = Reference::where('id', $booking_line_item["co_license"])->first();
            $current_expiry_date = Carbon::parse($booking_line_item["lease_expiry_date"]);
            $new_expiry_date = $current_expiry_date->addYears($time);

            $start_date = new Carbon( $booking_line_item["lease_expiry_date"]);
            
            $years = $new_expiry_date->diffInYears($start_date);
            if($new_expiry_date->year > $now->year){
                $duration_of_lease = $new_expiry_date->diffInYears($now);
                if($start_date->year > $now->year)
                {
                    $duration_of_lease = $new_expiry_date->diffInYears($start_date);
                }
            
                $duration_of_lease = $duration_of_lease."/".$years." years";
            }else{
                $duration_of_lease = "1/1 years";
            }
            
            $amount  = $check_niches->exten_price;
            $tax_amount = 0;
            if($gst){
                $tax_amount = $amount*$gst->rate;
            }

            $status_niches_reserved = Reference::where([
                ['reference_type', '=', 'status_services_niches'],
                ['reference_value_text', '=', 'Reserved'],
            ])->first();
            $id_booking_line = BookingLineItems::select('id')->orderBy('id', "DESC")->first();
            $line_items_booking_no = 1;
            if($id_booking_line){
                $line_items_booking_no = (int)$id_booking_line->id + 1;
            }
            $number_booking_no = str_pad($line_items_booking_no,4,'0',STR_PAD_LEFT);
            $short_year = Carbon::now()->format('y');
            if($co_license->reference_value_text === "Yes"){
                $new_booking_line_item = BookingLineItems::create([
                    'booking_no'                    =>  'N-'.$short_year.'-'.$number_booking_no,
                    'booking_id'                    =>  $new_booking->id,
                    'booking_type_id'               =>  $booking_line_item["booking_type_id"],
                    'service_id'                    =>  $booking_line_item["service_id"],
                    'application_date'              =>  $booking_line_item["application_date"],
                    'lease_start_date'              =>  $start_date,
                    'lease_expiry_date'             =>  $new_expiry_date,
                    'duration_of_lease'             =>  $duration_of_lease,
                    'amount'                        =>  $amount,
                    'tax_amount'                    =>  $tax_amount,
                    'booking_date'                  =>  $now->toDateTimeString(),
                    'co_license'                    =>  $booking_line_item["co_license"],
                    'co_license_name'               =>  $booking_line_item["co_license_name"],
                    'co_license_email'              =>  $booking_line_item["co_license_email"],
                    'co_license_phone'              =>  $booking_line_item["co_license_phone"],
                    'co_license_passport'           =>  $booking_line_item["co_license_passport"],
                    'co_license_postal_code'        =>  $booking_line_item["co_license_postal_code"],
                    'co_license_street_name'        =>  $booking_line_item["co_license_street_name"],
                    'relationship_with_license'     =>  $booking_line_item["relationship_with_license"],
                    'status'                        =>  $status_niches_reserved->id,
                    'is_referral'                   =>  $booking_line_item["is_referral"],
                    'referral_name'                 =>  $booking_line_item["referral_name"],
                    'renewal_from_id'               =>  $booking_line_item["id"]
                ]);
            }
            else{
                $new_booking_line_item = BookingLineItems::create([
                    'booking_no'                    =>  'N-'.$short_year.'-'.$number_booking_no,
                    'booking_id'                    =>  $new_booking->id,
                    'booking_type_id'               =>  $booking_line_item["booking_type_id"],
                    'service_id'                    =>  $booking_line_item["service_id"],
                    'application_date'              =>  $booking_line_item["application_date"],
                    'lease_start_date'              =>  $start_date,
                    'lease_expiry_date'             =>  $new_expiry_date,
                    'duration_of_lease'             =>  $duration_of_lease,
                    // 'discount'                      =>  !empty($request->discount) ? $request->discount : NULL,
                    'amount'                        =>  $amount,
                    'tax_amount'                    =>  $tax_amount,
                    'booking_date'                  =>  $now->toDateTimeString(),
                    'co_license'                    =>  $booking_line_item["co_license"],
                    'status'                        =>  $status_niches_reserved->id,
                    'is_referral'                   =>  $booking_line_item["is_referral"],
                    'referral_name'                 =>  $booking_line_item["referral_name"],
                    'renewal_from_id'               =>  $booking_line_item["id"]
                ]);
            }
            $booking_niche_item = BookingNicheItem::where('booking_line_items_id',$booking_line_item->id)->get();

            if($new_booking_line_item){
                foreach ($booking_niche_item as $item) {
                    $niche_item = BookingNicheItem::create([
                        'booking_line_items_id'         =>  $new_booking_line_item->id,
                        'full_name'                     =>  $item["full_name"],
                        'relationship_to_applicant'     =>  $item["relationship_to_applicant"],
                        'death_anniversary'             =>  $item["death_anniversary"],
                    ]);
                }
            }
        }
       
        return response()->json([
            'status' => 'Successfully Created Booking',
            'data' => $new_booking
        ], 200);
        

    }
}
/**
 * @OA\Post(
 *     tags={"Niches"},
 *     path="/api/niches-total",
 *     summary="Total Niches",
 *     operationId="totalNichesRenew",
 *     @OA\Parameter(
 *         name="arr_id",
 *         in="query",
 *         required=true,
 *         description="Id",
 *         @OA\Schema(
 *             type="string",
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
    public function totalNichesRenew(Request $request){
        if(isset($request->arr_id)){
            $arr_id = $request->arr_id;
            if(is_string($arr_id)){
                $arr_id = json_decode($arr_id);
            }

            $arr_service_id = BookingLineItems::whereIn('id',$arr_id)->whereNull('deleted_at')->pluck('service_id');
            $price_niche = Niche::whereIn('id', $arr_service_id)->pluck('price');
            $data = [ "total" => 0 ];
            $total = 0;
            foreach ($price_niche as $key => $value) {
                $total += (float)$value;
            }
            $data["total"] = number_format($total, 2, '.', ',');
            return response()->json([
                'status' => 'success',
                'data'  =>  $data
            ], 200);
        }
    }
/**
 * @OA\Post(
 *     tags={"Booking"},
 *     path="/api/import-ocupied",
 *     summary="Import file Booking excel",
 *      @OA\RequestBody(
 *          @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 type="object",
 *                 @OA\Property(
 *                     property="file_excel",
 *                     description="file_excel Booking",
 *                     type="file",
 *                 ),
 *             )
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
    public function importOccupied(Request $request){
        ini_set('max_execution_time', 3000);
        $file = $request->file("file_excel");
        if(!empty($file)){
            $reader = ReaderEntityFactory::createXLSXReader();
            $reader->setShouldFormatDates(true);
            $file = public_path("/New Niche Inventory May 2020v2 (For Mike).xlsm");
            $reader->open($file);
            $info = [];
            foreach ($reader->getSheetIterator() as $sheet) {
                if($sheet->getName() == "Niche Sales Record"){
                    foreach ($sheet->getRowIterator() as $key=>$row) {
                        
                        $cells = $row->getCells();
                        $flag = $cells[0]->getValue();
                        if(!($flag === "NICHE No")){
                            if(!($flag === " " || $flag == null)){
                                //
                                // var_dump($this->checkNullValue($cells[5]->getValue()));exit;
                                $niche_no = $this->checkNullValue($cells[0]->getValue());
                                $applicant_date = $this->checkNullValue($cells[8]->getValue());
                                $occupant = $this->checkNullValue($cells[5]->getValue());
                                $status =  $this->checkNullValue($cells[9]->getValue());
                                if($occupant != '_')
                                {
                                    $full_name = explode("(", $occupant);
                                    
                                    if(isset($full_name[1]))
                                    {
                                        $death_date = explode(")", $full_name[1]);
                                    }   
                                    
                                }
                                
                                $salutation = Reference::where(
                                    [
                                        ['reference_type', 'salutation'],
                                    ])->pluck('reference_value_text')->all();
     
                                $name = '';
                                $name = str_replace($salutation,' ', $full_name[0]);
                                
                                // var_dump($name);exit;
                                $date = DateTime::createFromFormat('d/m/y', $death_date[0]);
                                if(!$date){
                                    $date = DateTime::createFromFormat('d/m/Y', $death_date[0]);
                                }
                                $booking = [
                                    'niche' => $niche_no,
                                    'applicant_date' => $applicant_date,
                                    'occupant_name' => isset($full_name) ? trim($name) : NULL,
                                    'death_date' => isset($date) ?  $date : new Date(),
                                    'occupant' => $occupant
                                ];
                                array_push($info, $booking);
                            }
                        } 
                    }
                    // var_dump($info);exit;
                    // var_dump($date->format('Y-m-d'));exit;
                }
            } 
            $reader->close();
            $booking_type = Reference::where(
            [
                ['reference_type', 'like', 'booking_type'],
                ['reference_value_text', 'like', 'Niches']
            ])->first();
            $dem = 0;
            foreach($info as $key => $value){ 
                
                $check_niche = Niche::where('reference_no',$value['niche'])->select('id')->first();
                
                if(isset($check_niche)){
                    $booking_line_item = BookingLineItems::where('service_id',$check_niche->id)->where('booking_type_id',$booking_type->id)->first();

                    if(!empty($booking_line_item)){
                        $niche_item =  new BookingNicheItem();
    
                        $niche_item->booking_line_items_id = $booking_line_item->id;
    
                        $niche_item->full_name = $value['occupant_name'];
    
                        $niche_item->death_anniversary = $value['death_date']!= false || $value['death_date'] != 0 ? $value['death_date'] : null;
    
                        $niche_item->save();
                    }
                    else{
                        $dem = $dem+1;
                    }
                }

            }
            return response()->json(
            [
                'status' => 'Successfully import file',
                'data' => $dem
            ], 200);
        }
    }

/**
 * @OA\GET(
 *     tags={"Niches"},
 *     path="/api/get-niche-extension",
 *     summary="extension Niches",
 *     operationId="getNichesExtension",
 *     @OA\Parameter(
 *         name="arr_id",
 *         in="query",
 *         required=true,
 *         description="Id",
 *         @OA\Schema(
 *             type="string",
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
    public function getNichesExtension(Request $request){
        if(isset($request->arr_id)){
            $arr_id = $request->arr_id;
            if(is_string($arr_id)){
                $arr_id = json_decode($arr_id);
            }
           
            $check_duplicate = BookingLineItems::whereIn('id',$arr_id)->pluck('service_id')->all();
            if(count(array_unique($check_duplicate))<count($check_duplicate))
            {
                return response()->json([
                    'status' => 'error',
                    'errors' => "You cannot renew multiple the same niches."
                ], 404);
            }
            $booking_line = BookingLineItems::whereIn('id',$arr_id)->get();
            $list_niche = Niche::whereIn('id',$check_duplicate)->with('extension')->get();
            // var_dump($list_niche);exit;
            foreach($list_niche as $key => $value){
                foreach($booking_line as $key_line => $value_line){
                    if($value->id == $value_line->service_id){
                        $list_niche[$key]->line_id = $value_line->id;
                    }
                }
            }
            return response()->json([
                'status' => 'Successfully Created Booking',
                'data' => $list_niche
            ], 200);
            
    
        }
    }
    /**
 * @OA\GET(
 *     tags={"Niches"},
 *     path="/api/extension-mutiple-niches",
 *     summary="extension Niches",
 *     operationId="extensionMutipleNiche",
 *     @OA\Parameter(
 *         name="extent_arr",
 *         in="query",
 *         required=true,
 *         description="Id",
 *         @OA\Schema(
 *             type="string",
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
public function extensionMutipleNiche(Request $request){ 
    if(isset($request->extent_arr)){
        $extent_arr = $request->extent_arr;
        $now = now();

        $booking_niches = Reference::where('reference_type', 'booking_type')->where('reference_value_text', 'Niches')->first();
        $gst = GSTRate::where('gst_start_date', '<=', $now->format('Y-m-d').' 00:00:00')
                ->orderBy('gst_start_date', 'DESC')->first();
        foreach($extent_arr as $id_line)
        {
            $id_line = json_decode($id_line);

            $list_booking_line = BookingLineItems::where('service_id',$id_line->niche_id)->where('booking_type_id',$booking_niches->id)
            ->whereDate('start_date', '>',$now->toDateTimeString())
            ->get();

            if(count($list_booking_line)){
                // You cannot renew this niche. Because you have the booking is using
                return response()->json([
                    'status' => 'error',
                    'errors' => "You cannot renew this niche. Because you have the booking is using."
                ], 404);
            }
        }
        $booking_type = Reference::where('reference_type', 'booking_status')->where('reference_value_text', 'Booked')->first();
        $booking_no = 1;
        $id_booking = Booking::select('id')->withTrashed()->orderBy('id', "DESC")->first();
        if($id_booking){
            $booking_no = (int)$id_booking->id + 1;
        }
        $number_booking_no = str_pad($booking_no, 4, '0',STR_PAD_LEFT);
        $short_year = Carbon::now()->format('y');
        $new_booking = Booking::create([
            'user_id'           =>  $request->user_id,
            'booking_no'        =>  "B-".$short_year."-".$number_booking_no,
            'status'            =>  $booking_type->id
        ]);
    

        foreach($extent_arr as $id_line)
        { 
            $id_line = json_decode($id_line);

           if(!isset($id_line->duration)){
            return response()->json([
                'status' => 'error',
                'errors' => "Please choose duration."
            ], 404);
           }
            $booking_line_item = BookingLineItems::where('id',$id_line->arr_id)->where('booking_type_id',$booking_niches->id)->first();
            $check_niches = Duration::where([
                    ['niche_id',$booking_line_item['service_id']],
                    ['id',$id_line->duration]
                ])->whereNull('deleted_at')->first();

            foreach($extent_arr as $id_line)
            { 
                $id_line = json_decode($id_line);

                $booking_niches = Reference::where('reference_type', 'booking_type')->where('reference_value_text', 'Niches')->first();
                $booking_line_item = BookingLineItems::where('id',$id_line->arr_id)->where('booking_type_id',$booking_niches->id)->first();
                $check_niches = Duration::where([
                        ['niche_id',$booking_line_item['service_id']],
                        ['id',$id_line->duration]
                    ])->whereNull('deleted_at')->first();

                $time = $check_niches->exten_year;
                
                $co_license = Reference::where('id', $booking_line_item["co_license"])->first();

                $current_expiry_date = Carbon::parse($booking_line_item["lease_expiry_date"]);
                $new_expiry_date = $current_expiry_date->addYears($time);
                $start_date = new Carbon( $booking_line_item["lease_expiry_date"]);
                
                $years = $new_expiry_date->diffInYears($start_date);
                
                if($new_expiry_date->year > $now->year){
                    $duration_of_lease = $new_expiry_date->diffInYears($now);
                    if($start_date->year > $now->year)
                    {
                        $duration_of_lease = $new_expiry_date->diffInYears($start_date);
                    }
                
                    $duration_of_lease = $duration_of_lease."/".$years." years";
                }else{
                    $duration_of_lease = "1/1 years";
                }
                
                $amount  = $check_niches->exten_price;
                
                $tax_amount = 0;
                if($gst){
                    $tax_amount = $amount*$gst->rate;
                }

                $status_niches_reserved = Reference::where([
                    ['reference_type', '=', 'status_services_niches'],
                    ['reference_value_text', '=', 'Reserved'],
                ])->first();
                $id_booking_line = BookingLineItems::select('id')->orderBy('id', "DESC")->first();
                $line_items_booking_no = 1;
                if($id_booking_line){
                    $line_items_booking_no = (int)$id_booking_line->id + 1;
                }
                $number_booking_no = str_pad($line_items_booking_no,4,'0',STR_PAD_LEFT);
                $short_year = Carbon::now()->format('y');
                if($co_license->reference_value_text === "Yes"){
                    $new_booking_line_item = BookingLineItems::create([
                        'booking_no'                    =>  'N-'.$short_year.'-'.$number_booking_no,
                        'booking_id'                    =>  $new_booking->id,
                        'booking_type_id'               =>  $booking_line_item["booking_type_id"],
                        'service_id'                    =>  $booking_line_item["service_id"],
                        'application_date'              =>  $booking_line_item["application_date"],
                        'lease_start_date'              =>  $start_date,
                        'lease_expiry_date'             =>  $new_expiry_date,
                        'duration_of_lease'             =>  $duration_of_lease,
                        'amount'                        =>  $amount,
                        'tax_amount'                    =>  $tax_amount,
                        'booking_date'                  =>  $now->toDateTimeString(),
                        'co_license'                    =>  $booking_line_item["co_license"],
                        'co_license_name'               =>  $booking_line_item["co_license_name"],
                        'co_license_email'              =>  $booking_line_item["co_license_email"],
                        'co_license_phone'              =>  $booking_line_item["co_license_phone"],
                        'co_license_passport'           =>  $booking_line_item["co_license_passport"],
                        'co_license_postal_code'        =>  $booking_line_item["co_license_postal_code"],
                        'co_license_street_name'        =>  $booking_line_item["co_license_street_name"],
                        'relationship_with_license'     =>  $booking_line_item["relationship_with_license"],
                        'status'                        =>  $status_niches_reserved->id,
                        'is_referral'                   =>  $booking_line_item["is_referral"],
                        'referral_name'                 =>  $booking_line_item["referral_name"],
                        'renewal_from_id'               =>  $booking_line_item["id"]
                    ]);
                }
                else{
                    $new_booking_line_item = BookingLineItems::create([
                        'booking_no'                    =>  'N-'.$short_year.'-'.$number_booking_no,
                        'booking_id'                    =>  $new_booking->id,
                        'booking_type_id'               =>  $booking_line_item["booking_type_id"],
                        'service_id'                    =>  $booking_line_item["service_id"],
                        'application_date'              =>  $booking_line_item["application_date"],
                        'lease_start_date'              =>  $start_date,
                        'lease_expiry_date'             =>  $new_expiry_date,
                        'duration_of_lease'             =>  $duration_of_lease,
                        // 'discount'                      =>  !empty($request->discount) ? $request->discount : NULL,
                        'amount'                        =>  $amount,
                        'tax_amount'                    =>  $tax_amount,
                        'booking_date'                  =>  $now->toDateTimeString(),
                        'co_license'                    =>  $booking_line_item["co_license"],
                        'status'                        =>  $status_niches_reserved->id,
                        'is_referral'                   =>  $booking_line_item["is_referral"],
                        'referral_name'                 =>  $booking_line_item["referral_name"],
                        'renewal_from_id'               =>  $booking_line_item["id"]
                    ]);
                }
                $booking_niche_item = BookingNicheItem::where('booking_line_items_id',$booking_line_item->id)->get();

                if($new_booking_line_item){
                    foreach ($booking_niche_item as $item) {
                        $niche_item = BookingNicheItem::create([
                            'booking_line_items_id'         =>  $new_booking_line_item->id,
                            'full_name'                     =>  $item["full_name"],
                            'relationship_to_applicant'     =>  $item["relationship_to_applicant"],
                            'death_anniversary'             =>  $item["death_anniversary"],
                        ]);
                    }
                }
            }
        
            return response()->json([
                'status' => 'Successfully Created Booking',
                'data' => $new_booking
            ], 200);
            
        }
        }
    }
    /**
 * @OA\GET(
 *     tags={"Booking"},
 *     path="/api/update-status",
 *     summary="updateStatusServiceNiches",
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
    public function updateStatusServiceNiches(){
        $booking = BookingLineItems::whereHas('booking_type', function (Builder $query) {
            $query->where([
                    ['reference_type', '=', 'booking_type'],
                    ['reference_value_text', '=', 'Niches'],
                ]);
        })->get();
        
        $status_book = Reference::where([
            ['reference_type', '=', 'booking_status'],
            ['reference_value_text', '=', 'Booked'],
        ])->first();
        ///
        $status_niches_reserved = Reference::where([
            ['reference_type', '=', 'status_services_niches'],
            ['reference_value_text', '=', 'Reserved'],
        ])->first();
        ///
        $status_niches_unoccupied = Reference::where([
            ['reference_type', '=', 'status_services_niches'],
            ['reference_value_text', '=', 'Sold - Unoccupied'],
        ])->first();
        ///
        $status_niches_occupied = Reference::where([
            ['reference_type', '=', 'status_services_niches'],
            ['reference_value_text', '=', 'Sold - Occupied'],
        ])->first();
        // dd($status_niches_reserved, $status_niches_unoccupied,$status_niches_occupied);
        foreach($booking as $book_line){
            if($book_line->booking->status == $status_book->id){
                $book_line->status = $status_niches_reserved->id;
                $book_line->save();
            }else{
                if($book_line->interment_date == null){
                    $book_line->status = $status_niches_unoccupied->id;
                    $book_line->save();
                }else{
                    $book_line->status = $status_niches_occupied->id;
                    $book_line->save();
                }
            }
            
           
        }
        return response()->json([
            'status' => 'success',
            'success' => "Update success"
        ], 200);
        
    }
/**
 * @OA\PUT(
 *     tags={"Booking"},
 *     path="/api/update-status-booking/{id}",
 *     summary="updateStatusBooking",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Id",
 *         @OA\Schema(
 *             type="string",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="status",
 *         in="query",
 *         required=true,
 *         description="Status",
 *         @OA\Schema(
 *             type="string",
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
    public function updateStatusBooking(Request $request){

        $booking = Booking::where('id',$request->id)->first();

        if(empty($booking)){
            return response()->json([
                'status' => 'error',
                'errors' => "Booking does not exits."
            ], 404);
        }
        $status_booking = Reference::where('id',$request->status)->first();

        $booking->status = $request->status;

        $booking->save();

        if($status_booking->reference_value_text == "Cancelled"){
            $booking_line_item = BookingLineItems::where('booking_id',$booking->id)->get();

            foreach($booking_line_item as $key => $value)
            {
                $status_service = Reference::where('id',$value->booking_type_id)->first();

                if($status_service->reference_value_text == "Niches")
                {
                    $status_service_niches = Reference::where([
                        ['reference_type', '=', 'status_services_niches'],
                        ['reference_value_text', '=', 'Cancelled'],
                    ])->first();

                    $value->status = $status_service_niches->id;

                    $value->save();

                    Niche::where('id',$value->service_id)->update(['booking_id' => null]);
                }

                if($status_service->reference_value_text == "Memorial Rooms")
                {
                    $status_service_room = Reference::where([
                        ['reference_type', '=', 'status_services_rooms'],
                        ['reference_value_text', '=', 'Cancelled'],
                    ])->first();

                    $value->status = $status_service_room->id;

                    $value->save();
                }

                if($status_service->reference_value_text == "Additional Services"){
                    $status_service_product = Reference::where([
                        ['reference_type', '=', 'status_services_products'],
                        ['reference_value_text', '=', 'Cancelled'],
                    ])->first();

                    $value->status = $status_service_product->id;

                    $value->save();
                }
            }
            return response()->json([
                'status' => 'success',
                'success' => "Successfully"
            ], 200);
        }
        return response()->json([
            'status' => 'success',
            'success' => "Successfully"
        ], 200);
    }

/**
 * @OA\Post(
 *     tags={"Booking"},
 *     path="/api/import-update-booking",
 *     summary="Import file Booking excel",
 *      @OA\RequestBody(
 *          @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 type="object",
 *                 @OA\Property(
 *                     property="file_excel",
 *                     description="file_excel Booking",
 *                     type="file",
 *                 ),
 *             )
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
    public function importUpdateBooking(Request $request){
        ini_set('max_execution_time', 3000);
        $file = $request->file("file_excel");
        if(!empty($file)){
            $reader = ReaderEntityFactory::createXLSXReader();
            $reader->setShouldFormatDates(true);
            // $reader = ReaderFactory::create(Type::XLSX); // for XLSX files
            $file = public_path("/Niches_database_31082020.xlsx");
            $reader->open($file);
            $deceased = array();
            $booking = array();
            
            foreach ($reader->getSheetIterator() as $sheet) {
                if($sheet->getName() != "Niche Database Old (3)" && $sheet->getName() != "Users & Deceased" 
                    &&$sheet->getName() != "NRIC" && $sheet->getName() != "NRIC (One NIche)" && $sheet->getName() != "NRIC (Multi Niches)"
                    &&$sheet->getName() != "5D NRIC" && $sheet->getName() != "Addresses" &&$sheet->getName() != "6D NRIC"
                    &&$sheet->getName() != "NRIC formula"){
                    foreach ($sheet->getRowIterator() as $key=>$row) {
                        $cells = $row->getCells();
                        // $flag = $cells[3]->getValue();
                        $appln_date                 = $this->checkNullValue($cells[2]->getValue());
                        $niche_no                   = $this->checkNullValue($cells[3]->getValue());
                        $name_appln                 = $this->checkNullValue($cells[5]->getValue());
                        $remarks                    = $this->checkNullValue($cells[7]->getValue());
                        $Pc                         = $this->checkNullValue($cells[8]->getValue());
                        $hse_no                     = $this->checkNullValue($cells[9]->getValue());
                        $st_name                    = $this->checkNullValue($cells[10]->getValue());
                        $unit_no                    = $this->checkNullValue($cells[11]->getValue());
                        ///===================
                        $display_name_array = explode(" ",$name_appln);
                        $last_name = $display_name_array[0];
                        unset($display_name_array[0]);
                        $first_name = implode(" ",$display_name_array);
                        $name_appln = $first_name." ".$last_name;
                        ///===================
                        if($Pc === "A2 ID"){
                            $type_check = 1;
                        }
                        if($Pc === "Addr1"){
                            $type_check = 2;
                            
                        }
                        if($Pc === "A1 PC"){
                            $type_check = 3;

                        }
                        if($Pc === "Addr"){
                            $type_check = 4;
                        }
                        ///===================
                        if($type_check == 1){
                            if(!($niche_no === "NICHE No" || $appln_date === "Appln date")){
                                if(!($niche_no === " " || $niche_no == null)){
                                    $appln_date = Carbon::parse($appln_date)->format('Y-m-d');  
                                    
                                    $booking_item = array(
                                        'niche_no'          => $niche_no, 
                                        'appln_date'        => $appln_date, 
                                        'name_appln'        => $name_appln,
                                        'remarks'           => $remarks,
                                        'postal_code'       => 'N/A',
                                        'house_no'          => 'N/A',
                                        'street_name'       => 'N/A',
                                        'unit_no'           => 'N/A',
                                        'display_address'   => null,
                                        'display_address_2' => null,
                                    );
                                    
                                    array_push($booking, $booking_item);
                                    // dd($booking);
                                }
                            }
                        }
                        if($type_check == 2){
                            if(!($niche_no === "NICHE No" || $appln_date === "Appln date" || $name_appln === "A1")){
                                if(!($niche_no === " " || $niche_no == null)){
                                    $appln_date = Carbon::parse($appln_date)->format('Y-m-d');  
                                    $booking_item = array(
                                        'niche_no'          => $niche_no, 
                                        'appln_date'        => $appln_date, 
                                        'name_appln'        => $name_appln,
                                        'remarks'           => $remarks,
                                        'display_address'   => $Pc,
                                        'display_address_2' => $hse_no,
                                        'postal_code'       => 'N/A',
                                        'house_no'          => 'N/A',
                                        'street_name'       => 'N/A',
                                        'unit_no'           => 'N/A',
                                    );
                                    array_push($booking, $booking_item);
                                    // dd($booking);
                                }
                            }
                        }
                        if($type_check == 4){
                            if(!($niche_no === "NICHE No" || $appln_date === "Appln date" || $name_appln === "A1")){
                                if(!($niche_no === " " || $niche_no == null)){
                                    $appln_date = Carbon::parse($appln_date)->format('Y-m-d');  
                                    $booking_item = array(
                                        'niche_no'          => $niche_no, 
                                        'appln_date'        => $appln_date, 
                                        'name_appln'        => $name_appln,
                                        'remarks'           => $remarks,
                                        'display_address'   => $Pc,
                                        'display_address_2' => null,
                                        'postal_code'       => 'N/A',
                                        'house_no'          => 'N/A',
                                        'street_name'       => 'N/A',
                                        'unit_no'           => 'N/A',
                                    );
                                    array_push($booking, $booking_item);
                                    // dd($booking);
                                }
                            }
                        }
                        if($type_check == 3){
                            if(!($niche_no === "NICHE No" || $appln_date === "Appln date" || $name_appln === "A1")){
                                if(!($niche_no === " " || $niche_no == null)){
                                    $appln_date = Carbon::parse($appln_date)->format('Y-m-d');  
    
                                    $booking_item = array(
                                        'niche_no'          => $niche_no, 
                                        'appln_date'        => $appln_date, 
                                        'name_appln'        => $name_appln,
                                        'remarks'           => $remarks,
                                        'postal_code'       => $Pc,
                                        'house_no'          => $hse_no,
                                        'street_name'       => $st_name,
                                        'unit_no'           => $unit_no,
                                        'display_address'   => null,
                                        'display_address_2' => null,
                                    );
                                    
                                    array_push($booking, $booking_item);
                                    // dd($booking, 22);
                                }
                            }
                        } 
                    }
                }
                if($sheet->getName() === "Users & Deceased"){
                    foreach ($sheet->getRowIterator() as $key=>$row) {
                        $cells = $row->getCells();
                        // $flag = $cells[1]->getValue();
                        $niche_no                   = $this->checkNullValue($cells[1]->getValue());
                        $user1                      = $this->checkNullValue($cells[2]->getValue());
                        $user2                      = $this->checkNullValue($cells[3]->getValue());
                        $user_remarks               = $this->checkNullValue($cells[4]->getValue());
                        $deceased1                  = $this->checkNullValue($cells[5]->getValue());
                        $deceased1_date             = $cells[6]->getValue();
                        $urn1_date                  = $cells[7]->getValue();
                        $plaque1_date               = $cells[8]->getValue();
                        $deceased2                  = $this->checkNullValue($cells[9]->getValue());
                        $deceased2_date             = $cells[10]->getValue();
                        $urn2_date                  = $cells[11]->getValue();
                        $plaque2_date               = $cells[12]->getValue();
                        $remarks                    = $this->checkNullValue($cells[13]->getValue());

                        if(!($niche_no === "NICHE No" || $user1 === "User1" || $user2 === "User2")){
                            if(!($niche_no === " " || $niche_no == null)){
                                $deceased1_date     = $this->checkDateCustom($deceased1_date) ? (Carbon::parse($deceased1_date)->format('Y-m-d'))   : null;  
                                $urn1_date          = $this->checkDateCustom($urn1_date)      ? (Carbon::parse($urn1_date)->format('Y-m-d'))        : null;  
                                $plaque1_date       = $this->checkDateCustom($plaque1_date)   ? (Carbon::parse($plaque1_date)->format('Y-m-d'))     : null;  
                                ////
                                $deceased2_date     = $this->checkDateCustom($deceased2_date) ? (Carbon::parse($deceased2_date)->format('Y-m-d'))   : null;  
                                $urn2_date          = $this->checkDateCustom($urn2_date)      ? (Carbon::parse($urn2_date)->format('Y-m-d'))        : null;  
                                $plaque2_date       = $this->checkDateCustom($plaque2_date)   ? (Carbon::parse($plaque2_date)->format('Y-m-d'))     : null;  

                                $deceased_item = array(
                                    'niche_no'          => $niche_no, 
                                    'user1'             => $user1, 
                                    'user2'             => $user2,
                                    'user_remarks'      => $user_remarks,
                                    'deceased1'         => $deceased1,
                                    'deceased1_date'    => $deceased1_date,
                                    'urn1_date'         => $urn1_date,
                                    'plaque1_date'      => $plaque1_date,
                                    'deceased2'         => $deceased2,
                                    'deceased2_date'    => $deceased2_date,
                                    'urn2_date'         => $urn2_date,
                                    'plaque2_date'      => $plaque2_date,
                                    'remarks'           => $remarks,
                                );
                                array_push($deceased, $deceased_item);
                                // dd($deceased);
                            }
                        } 
                    }
                }
            } 
            $reader->close();
            $arr_demo = array();
            foreach($booking as $key=>$book){
                $niche_info = Niche::where('reference_no', '=', $book['niche_no'])->first();
                if(empty($niche_info)){
                    $new_niche = new Niche();
                    $new_niche->reference_no    = $book['niche_no'];
                    $new_niche->type_id         = 0;
                    $new_niche->price           = 0;
                    $new_niche->status          = 'Unavailable';
                    $new_niche->category_id     = 0;
                    $new_niche->bay             = 'N/A';
                    $new_niche->wing            = 'N/A';
                    $new_niche->floor           = 'N/A';
                    $new_niche->block           = 'N/A';
                    $new_niche->level           = 'N/A';
                    $new_niche->unit            = 'N/A';
                    $new_niche->save();
                    if($new_niche){
                        $niche_id = $new_niche->id;
                    }
                }else{
                    $niche_id = $niche_info->id;
                }
                $check_booking = BookingLineItems::whereHas('booking_type', function (Builder $query) {
                    $query->where([
                            ['reference_type', '=', 'booking_type'],
                            ['reference_value_text', '=', 'Niches'],
                        ]);
                    })
                    ->where('service_id', '=', $niche_id)->first();
                
                $check_customer = User::where('display_name', 'like', "%".$book['name_appln']."%")->first();
                if($check_booking){
                    if(!($check_customer)){
                        ///============
                        ///create customer
                        $new_user = new User();
                        $new_user->display_name           = $book['name_appln'];
                        $new_user->street_no              = $book['house_no'];
                        $new_user->street_name            = $book['street_name'];
                        $new_user->unit_no                = $book['unit_no'];
                        $new_user->postal_code            = $book['postal_code'];
                        $new_user->display_address        = $book['display_address'];
                        $new_user->display_address_2      = $book['display_address_2'];
                        $new_user->save();
                        if($new_user){
                            $update_booking = Booking::where('id', '=', $check_booking->booking_id)->first();
                            $update_booking->user_id = $new_user->id;
                            $update_booking->save();
                            if($update_booking){
                                $booking_id = $update_booking->id;
                                if($niche_info){
                                    $niche_info->booking_id = $booking_id;
                                    $niche_info->booking_line_item = $check_booking->id;
                                    $niche_info->save();
                                }
                            }
                        }
                    }else{
                        $check_customer->street_no              = $book['house_no'];
                        $check_customer->street_name            = $book['street_name'];
                        $check_customer->unit_no                = $book['unit_no'];
                        $check_customer->postal_code            = $book['postal_code'];
                        $check_customer->display_address        = $book['display_address'];
                        $check_customer->display_address_2      = $book['display_address_2'];
                        $check_customer->save();
                        if(!($check_booking->booking->user_id == $check_customer->id)){
                            $update_booking = Booking::where('id', '=', $check_booking->booking_id)->first();
                            $update_booking->user_id = $check_customer->id;
                            $update_booking->save();
                            if($update_booking){
                                $booking_id = $update_booking->id;
                                if($niche_info){
                                    $niche_info->booking_id = $booking_id;
                                    $niche_info->booking_line_item = $check_booking->id;
                                    $niche_info->save();
                                }
                            }
                        }else{
                            if($niche_info){
                                $niche_info->booking_id = $check_booking->booking_id;
                                $niche_info->booking_line_item = $check_booking->id;
                                $niche_info->save();
                            }
                        }
                    }
                    ///============
                    ///remarks
                    if($book['remarks'] != null){
                        $remarks = new Remarks();
                        $remarks->booking_line_item_id = $check_booking->id;
                        $remarks->remarks = $book['remarks'];
                        $remarks->save();
                    }
                    ///============
                    ///deceased
                    foreach($deceased as $key_user => $user){
                        if($book['niche_no'] === $user['niche_no']){
                            $booking = Booking::where('id', '=', $check_booking->booking_id)->first();
                            $co_liense_yes = Reference::Where(
                                [
                                    ['reference_type', 'like', 'co_liense'],
                                    ['reference_value_text', 'like', 'Yes']
                                ])->first();
                            //
                            $co_liense_no = Reference::Where(
                                [
                                    ['reference_type', 'like', 'co_liense'],
                                    ['reference_value_text', 'like', 'No']
                                ])->first();
                            //
                            if($user['deceased1'] != null){
                                $single = Reference::Where(
                                    [
                                        ['reference_type', 'like', 'type_niche'],
                                        ['reference_value_text', 'like', 'Single']
                                    ])->first();
                                $check_niche_single = Niche::where([
                                    ['reference_no', '=', $user['niche_no']],
                                ])->first();
                                if($check_niche_single->type_id == $single->id){
                                    $chech_niche_item = BookingNicheItem::where([
                                        ['booking_line_items_id', '=', $check_niche_single->booking_line_item],
                                    ])->first();
                                    if(!empty($chech_niche_item)){
                                        $chech_niche_item->full_name                  = $user['deceased1'];
                                        $chech_niche_item->death_anniversary          = $user['deceased1_date'];
                                        $chech_niche_item->save();
        
                                    }else{
                                        $niche_item = new BookingNicheItem();
                                        $niche_item->booking_line_items_id      = $check_booking->id;
                                        $niche_item->full_name                  = $user['deceased1'];
                                        $niche_item->death_anniversary          = $user['deceased1_date'];
                                        $niche_item->save();
                                    }
                                }else{
                                    $chech_niche_item = BookingNicheItem::where([
                                        ['booking_line_items_id', '=', $check_booking->id],
                                        ['full_name', '=', $user['deceased1'] ]
                                    ])->first();
                                    if(!empty($chech_niche_item)){
                                        $chech_niche_item->death_anniversary          = $user['deceased1_date'];
                                        $chech_niche_item->save();
                                    }else{
                                        $niche_item = new BookingNicheItem();
                                        $niche_item->booking_line_items_id      = $check_booking->id;
                                        $niche_item->full_name                  = $user['deceased1'];
                                        $niche_item->death_anniversary          = $user['deceased1_date'];
                                        $niche_item->save();
                                    }
                                }
                            }
                            if($user['deceased2'] != null){
                                $chech_niche_item = BookingNicheItem::where([
                                    ['booking_line_items_id', '=', $check_booking->id],
                                    ['full_name', '=', $user['deceased2'] ]
                                ])->first();
                                if($chech_niche_item){
                                    $chech_niche_item->death_anniversary          = $user['deceased2_date'];
                                    $chech_niche_item->save();
                                }else{
                                    $niche_item = new BookingNicheItem();
                                    $niche_item->booking_line_items_id      = $check_booking->id;
                                    $niche_item->full_name                  = $user['deceased2'];
                                    $niche_item->death_anniversary          = $user['deceased2_date'];
                                    $niche_item->save();
                                }
                            }
                            if($user['urn1_date'] != null){
                                //=============
                                $service_urn = Other::where('service_name', '=', 'Urn')->first();
                                //=============
                                $service_item = Other::where([
                                    ['parent_id', '=', $service_urn->id],
                                    ['service_name', '=', 'N/A']
                                ])->first();
                                //=============
                                $booking_type = Reference::where([
                                    ['reference_type', '=', 'booking_type'],
                                    ['reference_value_text', '=', 'Additional Services'],
                                ])->first();
                                //=============
                                // $chech_service_item = BookingLineItems::where([
                                //     ['booking_type_id', '=', $booking_type->id],
                                //     ['service_id', '=', $service_item->id]
                                // ])->first();
                                //=============
                                $services_item = new BookingLineItems();
                                $services_item->booking_id          = $booking->id;
                                $services_item->booking_type_id     = $booking_type->id;
                                $services_item->service_id          = $service_urn->id;
                                $services_item->booking_date        = $user['urn1_date'];
                                $services_item->service_type_id     = $service_item->id;
                                $services_item->save();
                            }
                            if($user['plaque1_date'] != null){
                                //=============
                                $service_plaque = Other::where('service_name', '=', 'Plaque')->first();
                                //=============
                                $service_item = Other::where([
                                    ['parent_id', '=', $service_plaque->id],
                                    ['service_name', '=', 'N/A']
                                ])->first();
                                //=============
                                $booking_type = Reference::where([
                                    ['reference_type', '=', 'booking_type'],
                                    ['reference_value_text', '=', 'Additional Services'],
                                ])->first();
                                //=============
                                // $chech_service_item = BookingLineItems::where([
                                //     ['booking_type_id', '=', $booking_type->id],
                                //     ['service_id', '=', $service_item->id]
                                // ])->first();
                                //=============
                                $services_item = new BookingLineItems();
                                $services_item->booking_id          = $booking->id;
                                $services_item->booking_type_id     = $booking_type->id;
                                $services_item->service_id          = $service_plaque->id;
                                $services_item->booking_date        = $user['plaque1_date'];
                                $services_item->service_type_id     = $service_item->id;
                                $services_item->save();
                            }
                            if($user['urn2_date'] != null){
                                //=============
                                $service_urn = Other::where('service_name', '=', 'Urn')->first();
                                //=============
                                $service_item = Other::where([
                                    ['parent_id', '=', $service_urn->id],
                                    ['service_name', '=', 'N/A']
                                ])->first();
                                //=============
                                $booking_type = Reference::where([
                                    ['reference_type', '=', 'booking_type'],
                                    ['reference_value_text', '=', 'Additional Services'],
                                ])->first();
                                //=============
                                // $chech_service_item = BookingLineItems::where([
                                //     ['booking_type_id', '=', $booking_type->id],
                                //     ['service_id', '=', $service_item->id]
                                // ])->first();
                                //=============
                                $services_item = new BookingLineItems();
                                $services_item->booking_id          = $booking->id;
                                $services_item->booking_type_id     = $booking_type->id;
                                $services_item->service_id          = $service_urn->id;
                                $services_item->booking_date        = $user['urn2_date'];
                                $services_item->service_type_id     = $service_item->id;
                                $services_item->save();
                            }
                            if($user['plaque2_date'] != null){
                                //=============
                                $service_plaque = Other::where('service_name', '=', 'Plaque')->first();
                                //=============
                                $service_item = Other::where([
                                    ['parent_id', '=', $service_plaque->id],
                                    ['service_name', '=', 'N/A']
                                ])->first();
                                //=============
                                $booking_type = Reference::where([
                                    ['reference_type', '=', 'booking_type'],
                                    ['reference_value_text', '=', 'Additional Services'],
                                ])->first();
                                //=============
                                // $chech_service_item = BookingLineItems::where([
                                //     ['booking_type_id', '=', $booking_type->id],
                                //     ['service_id', '=', $service_item->id]
                                // ])->first();
                                //=============
                                $services_item = new BookingLineItems();
                                $services_item->booking_id          = $booking->id;
                                $services_item->booking_type_id     = $booking_type->id;
                                $services_item->service_id          = $service_plaque->id;
                                $services_item->booking_date        = $user['plaque2_date'];
                                $services_item->service_type_id     = $service_item->id;
                                $services_item->save();
                            }
                            if($user['remarks'] != null){
                                $remarks = new Remarks();
                                $remarks->booking_line_item_id = $check_booking->id;
                                $remarks->remarks = $user['remarks'];
                                $remarks->save();
                            }   
                            if($user['user2'] != null){
                                $check_booking->co_license          = $co_liense_yes->id;
                                $check_booking->co_license_name     = $user['user2'];
                                $check_booking->save();
                            }
                        }
                    }
                }else{
                    $booking_status = Reference::where([
                        ['reference_type', '=', 'booking_status'],
                        ['reference_value_text', '=', 'Fully Paid'],
                    ])->first();

                    $booking_count = Booking::count();
                    if($check_customer){
                        $user_id = $check_customer->id;
                    }else{
                        ///============
                        ///create customer
                        $new_user = new User();
                        $new_user->display_name           = $book['name_appln'];
                        $new_user->street_no              = $book['house_no'];
                        $new_user->street_name            = $book['street_name'];
                        $new_user->unit_no                = $book['unit_no'];
                        $new_user->postal_code            = $book['postal_code'];
                        $new_user->display_address        = $book['display_address'];
                        $new_user->display_address_2      = $book['display_address_2'];
                        $new_user->save();
                        if($new_user){
                            $user_id = $new_user->id;
                        }
                    }
                    ///============
                    ///create booking
                    $booking_no = (int)$booking_count + 1;
                    $renew_booking = new Booking();
                    $renew_booking->user_id = $user_id;
                    $renew_booking->booking_no = "BK-".$booking_no;
                    $renew_booking->status = $booking_status->id;
                    $renew_booking->save();

                    if($renew_booking){
                        $booking_id = $renew_booking->id;
                    }
                    ///============
                    ///create booking line items
                    $co_liense_yes = Reference::Where(
                        [
                            ['reference_type', 'like', 'co_liense'],
                            ['reference_value_text', 'like', 'Yes']
                        ])->first();
                    //
                    $co_liense_no = Reference::Where(
                        [
                            ['reference_type', 'like', 'co_liense'],
                            ['reference_value_text', 'like', 'No']
                        ])->first();
                    //
                    $booking_type_niche = Reference::Where(
                        [
                            ['reference_type', 'like', 'booking_type'],
                            ['reference_value_text', 'like', 'Niches']
                        ])->first();
                    //
                    $booking_type_services = Reference::Where(
                        [
                            ['reference_type', 'like', 'booking_type'],
                            ['reference_value_text', 'like', 'Additional Services']
                        ])->first();
                    //
                    $status_services_products = Reference::Where(
                        [
                            ['reference_type', 'like', 'status_services_products'],
                            ['reference_value_text', 'like', 'Sold']
                        ])->first();
                    //
                    foreach($deceased as $key_user => $user){
                        if($book['niche_no'] === $user['niche_no'])
                        {
                            $niche = Niche::where('reference_no', '=', $book['niche_no'])->first();
                            if($niche){
                                $niche_id = $niche->id;
                            }else{
                                $niche_id = null;
                            }
                            //======
                            $booking_line_item = new BookingLineItems();
                            $booking_line_item->booking_id          = $booking_id;
                            $booking_line_item->booking_type_id     = $booking_type_niche->id;
                            $booking_line_item->service_id          = $niche_id;
                            $booking_line_item->application_date    = $book['appln_date'];
                            if($user['user2'] == null){
                                $booking_line_item->co_license          = $co_liense_no->id;
                            }else{
                                $booking_line_item->co_license          = $co_liense_yes->id;
                                $booking_line_item->co_license_name     = $user['user2'];
                            }
                            $booking_line_item->save();
                            if($booking_line_item){
                                $line_item_id = $booking_line_item->id;
                                if($niche){
                                    $niche->booking_id = $booking_id;
                                    $niche->booking_line_item = $line_item_id;
                                    $niche->save();
                                }
                            }
                            if($user['deceased1'] != null){
                                $niche_item = new BookingNicheItem();
                                $niche_item->booking_line_items_id      = $line_item_id;
                                $niche_item->full_name                 = $user['deceased1'];
                                $niche_item->death_anniversary          = $user['deceased1_date'];
                                $niche_item->save();
                            }
                            if($user['deceased2'] != null){
                                $niche_item = new BookingNicheItem();
                                $niche_item->booking_line_items_id      = $line_item_id;
                                $niche_item->full_name                 = $user['deceased2'];
                                $niche_item->death_anniversary          = $user['deceased2_date'];
                                $niche_item->save();
                            }
                            if($user['urn1_date'] != null){
                                $urn_id = Other::where('service_name', '=', 'Urn')->first();
                                $urn_type = Other::where([
                                    ['parent_id', '=', $urn_id->id],
                                    ['service_name', '=', 'N/A']
                                ])->first();
                                $booking_line_item = new BookingLineItems();
                                $booking_line_item->booking_id          = $booking_id;
                                $booking_line_item->booking_type_id     = $booking_type_services->id;
                                $booking_line_item->service_id          = $urn_id->id;
                                $booking_line_item->amount              = $urn_type->price;
                                $booking_line_item->service_type_id     = $urn_type->id;
                                $booking_line_item->status              = $status_services_products->id;
                                $booking_line_item->booking_date        = $user['urn1_date'];
                                $booking_line_item->save();
    
                            }
                            if($user['plaque1_date'] != null ){
                                $plaque_id = Other::where('service_name', '=', 'Plaque')->first();
                                $plaque_type = Other::where([
                                    ['parent_id', '=', $plaque_id->id],
                                    ['service_name', '=', 'N/A']
                                ])->first();
                                $booking_line_item = new BookingLineItems();
                                $booking_line_item->booking_id          = $booking_id;
                                $booking_line_item->booking_type_id     = $booking_type_services->id;
                                $booking_line_item->service_id          = $plaque_id->id;
                                $booking_line_item->amount              = $plaque_type->price;
                                $booking_line_item->service_type_id     = $plaque_type->id;
                                $booking_line_item->status              = $status_services_products->id;
                                $booking_line_item->booking_date        = $user['plaque1_date'];
                                $booking_line_item->save();
                            }
                            if($user['urn2_date'] != null){
                                $urn_id = Other::where('service_name', '=', 'Urn')->first();
                                $urn_type = Other::where([
                                    ['parent_id', '=', $urn_id->id],
                                    ['service_name', '=', 'N/A']
                                ])->first();
                                $booking_line_item = new BookingLineItems();
                                $booking_line_item->booking_id          = $booking_id;
                                $booking_line_item->booking_type_id     = $booking_type_services->id;
                                $booking_line_item->service_id          = $urn_id->id;
                                $booking_line_item->amount              = $urn_type->price;
                                $booking_line_item->service_type_id     = $urn_type->id;
                                $booking_line_item->status              = $status_services_products->id;
                                $booking_line_item->booking_date        = $user['urn2_date'];
                                $booking_line_item->save();
                            }
                            if($user['plaque2_date'] != null ){
                                $plaque_id = Other::where('service_name', '=', 'Plaque')->first();
                                $plaque_type = Other::where([
                                    ['parent_id', '=', $plaque_id->id],
                                    ['service_name', '=', 'N/A']
                                ])->first();
                                $booking_line_item = new BookingLineItems();
                                $booking_line_item->booking_id          = $booking_id;
                                $booking_line_item->booking_type_id     = $booking_type_services->id;
                                $booking_line_item->service_id          = $plaque_id->id;
                                $booking_line_item->amount              = $plaque_type->price;
                                $booking_line_item->service_type_id     = $plaque_type->id;
                                $booking_line_item->status              = $status_services_products->id;
                                $booking_line_item->booking_date        = $user['plaque2_date'];
                                $booking_line_item->save();
                            }
                            ///============
                            ///remarks
                            if($book['remarks'] != null){
                                $remarks = new Remarks();
                                $remarks->booking_line_item_id = $line_item_id;
                                $remarks->remarks = $book['remarks'];
                                $remarks->save();
                            }
                            ///============
                            ///remarks
                            if($user['user_remarks'] != null){
                                $remarks = new Remarks();
                                $remarks->booking_line_item_id = $line_item_id;
                                $remarks->remarks = $user['user_remarks'];
                                $remarks->save();
                            }
                            ///============
                            ///remarks
                            if($user['remarks'] != null){
                                $remarks = new Remarks();
                                $remarks->booking_line_item_id = $line_item_id;
                                $remarks->remarks = $user['remarks'];
                                $remarks->save();
                            }
                        }
                    }
                }
            }
            // $path = public_path().'/import/booking/';
            // if(File::makeDirectory($path, 0777, true, true)){
            //     $name_file = $file->getClientOriginalName();
            //     $file->move($path,$name_file);
            // }else{
            //     $name_file = $file->getClientOriginalName();
            //     $file->move($path,$name_file);
            // }
            return response()->json(
                [
                    'status' => 'Successfully import file'
                ], 200);
            
        }
        return response()->json(
            [
                'status' => 'error',
                'errors' => 'Not file'
            ], 404);
    }

    public static function checkDateCustom($date){
        if($date == "" || $date == " "){
            $flag = false;

        }else{
            $date_arr = explode('/', $date);
            if(isset($date_arr[2])){
                $m = trim($date_arr[0]);
                $y = trim($date_arr[2]);
                $d = trim($date_arr[1]);
            }else{
                // dd($date_arr);
                $m = trim($date_arr[0]);
                $y = trim($date_arr[1]);
                $d = null;
            }
            $flag = checkdate($m, $d, $y);
        }
        return $flag;
    }

 /**
     * @OA\get(
     *     tags={"Export Remarks"},
     *     path="/api/dowload-zip-remarks",
     *     summary="downloadZip",
     *      @OA\Parameter(
    *         name="ids",
    *         in="query",
    *         required=true,
    *         description="Ids remarks",
    *         @OA\Schema(
    *             type="string",
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
    public function downloadZip(Request $request){
        $zip = new ZipArchive;
        $ids = $request->ids;
        if (is_string($ids)) {
            $ids = json_decode($ids);
        }
        $fileName = 'remarks.zip';
        $public_dir=public_path().'/download';
        $zip->open($fileName, (ZipArchive::CREATE | ZipArchive::OVERWRITE));
        // if () === TRUE)
        // {
            $file_remarks = Remarks::whereIn('id', $ids)->get();
            foreach($file_remarks as $file){
                $arr = explode('/', $file->file_path);
                $name_file = $arr[count($arr)-1];
                $zip->addFile($file->file_path, $name_file);
            }
        // }
        $zip->close();
        return response()->download($fileName);
    }
    
    /**
    * @OA\Post(
    *     tags={"Booking"},
    *     path="/api/line-no",
    *     summary="getLineNoBooking",
    *     @OA\Parameter(
    *         name="type",
    *         in="query",
    *         required=true,
    *         description="Type Booking (Niches, Memorial Rooms, Additional Services)",
    *         @OA\Schema(
    *             type="string",
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

    public function getLineNoBooking(Request $request){
        $line_items_booking_no = 1;
        $id_booking_line = BookingLineItems::select('id')->orderBy('id', "DESC")->first();
        if($id_booking_line){
            $line_items_booking_no = (int)$id_booking_line->id + 1;
        }
        $number_booking_no = str_pad($line_items_booking_no,4,'0',STR_PAD_LEFT);
        $short_year = Carbon::now()->format('y');
        if($request->type == 'Niches'){
            $line_no = 'N-'.$short_year.'-'.$number_booking_no;
        }else if($request->type == 'Memorial Rooms'){
            $line_no = 'P-'.$short_year.'-'.$number_booking_no;
        }else if($request->type == 'Additional Services'){
            $line_no = 'S-'.$short_year.'-'.$number_booking_no;
        }
        if($line_no){
            return response()->json([
                'status' => 'success',
                'data'   => $line_no
            ], 200);
        }else{
            return response()->json([
                'status' => 'error',
                'errors' => "Something bad happened, please try later"
            ], 422);
        }
    }

    /**
 * @OA\Post(
 *     tags={"Booking"},
 *     path="/api/import-niche-new-entries",
 *     summary="Import file Booking Niche New Entries",
 *      @OA\RequestBody(
 *          @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 type="object",
 *                 @OA\Property(
 *                     property="file_excel",
 *                     description="file_excel Booking",
 *                     type="file",
 *                 ),
 *             )
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
    public function importNicheNewEntries(Request $request){
        ini_set('max_execution_time', 3000);
        $file = $request->file("file_excel");
        if(!empty($file)){
            $reader = ReaderEntityFactory::createXLSXReader();
            $reader->setShouldFormatDates(true);
            // $reader = ReaderFactory::create(Type::XLSX); // for XLSX files
            // $file = public_path("/Niches_database_31082020.xlsx");
            $reader->open($file);
            $deceased = array();
            $applicant = array();
            $booking = array();
            // dd($reader->getSheetIterator());
            
            foreach ($reader->getSheetIterator() as $sheet) {
                if($sheet->getName() === "Import (Ref)"){
                    foreach ($sheet->getRowIterator() as $key=>$row) {
                        $cells = $row->getCells();
                        $sno                        = $this->checkNullImport($cells[0]->getValue());
                        $appln_no                   = $this->checkNullImport($cells[1]->getValue());
                        $appln_date                 = $this->checkNullValue($cells[2]->getValue());
                        $niche_no                   = $this->checkNullImport($cells[3]->getValue());
                        $NRIC                       = $this->checkNullImport($cells[4]->getValue());
                        if(!($sno === "Sno")){
                            if(!($sno === " " || $sno == null)){
                                $appln_date = Carbon::parse($appln_date)->format('Y-m-d');  
                                $booking_item = array(
                                    'niche_no'    => $niche_no, 
                                    'appln_date'  => $appln_date, 
                                    'sno'         => $sno,
                                    'appln_no'    => $appln_no,
                                    'NRIC'        => $NRIC,
                                );
                                array_push($booking, $booking_item);
                                // dd($booking);
                            }
                        }
                    }
                }
                if($sheet->getName() === "Import"){
                    foreach ($sheet->getRowIterator() as $key=>$row) {
                        $cells = $row->getCells();
                        $NRIC               = $this->checkNullImport($cells[0]->getValue());
                        $name_applicant     = $this->checkNullImport($cells[1]->getValue());
                        $pc                 = $this->checkNullImport($cells[2]->getValue());
                        $hse_no             = $this->checkNullImport($cells[3]->getValue());
                        $st_name            = $this->checkNullImport($cells[4]->getValue());
                        $unit_no            = $this->checkNullImport($cells[5]->getValue());
                        $home               = $this->checkNullValue($cells[6]->getValue());
                        $mobile             = $this->checkNullImport($cells[7]->getValue());
                        $email              = $this->checkNullImport($cells[8]->getValue());
                        $church             = $this->checkNullImport($cells[9]->getValue());
                        $remarks            = $this->checkNullValue($cells[10]->getValue());

                        if(!($NRIC === "ID")){
                            if(!($niche_no === " " || $niche_no == null)){
                                $applicant_item = array(
                                    'NRIC'              => $NRIC, 
                                    'name_applicant'    => $name_applicant, 
                                    'pc'                => $pc,
                                    'hse_no'            => $hse_no,
                                    'st_name'           => $st_name,
                                    'unit_no'           => $unit_no,
                                    'home'              => $home,
                                    'mobile'            => $mobile,
                                    'email'             => $email,
                                    'church'            => $church,
                                    'remarks'           => $remarks,
                                );
                                array_push($applicant, $applicant_item);
                                // dd($deceased);
                            }
                        } 
                    }
                }
                if($sheet->getName() === "Deceased"){
                    foreach ($sheet->getRowIterator() as $key=>$row) {
                        $cells = $row->getCells();
                        $sno             = $this->checkNullValue($cells[0]->getValue());
                        $deceased_1      = $this->checkNullValue($cells[4]->getValue());
                        $d1_date         = $this->checkNullValue($cells[5]->getValue());
                        $deceased_2      = $this->checkNullValue($cells[6]->getValue());
                        $d2_date         = $this->checkNullValue($cells[7]->getValue());
                        $remarks         = $this->checkNullValue($cells[8]->getValue());

                        if(!($sno === "Sno")){
                            if(!($sno === " " || $sno == null)){
                                $deceased1_date     = $this->checkDateCustom($d1_date) ? (Carbon::parse($d1_date)->format('Y-m-d'))   : null;  
                                $deceased2_date     = $this->checkDateCustom($d2_date) ? (Carbon::parse($d2_date)->format('Y-m-d'))   : null;  

                                $deceased_item = array(
                                    'sno'          => $sno, 
                                    'deceased_1'   => $deceased_1, 
                                    'd1_date'      => $deceased1_date,
                                    'deceased_2'   => $deceased_2,
                                    'd2_date'      => $deceased2_date,
                                    'remarks'      => $remarks,
                                );
                                array_push($deceased, $deceased_item);
                                // dd($deceased);
                            }
                        } 
                    }
                }
            } 
            $reader->close();
            $arr_demo = array();
            foreach($booking as $key=>$book){
                if($key == 0){
                    foreach($applicant as $key_user => $user){
                        if($book['NRIC'] === $user['NRIC']){
                            $check_customer = User::where('display_name', 'like', "%".$user['name_applicant']."%")->first();
                            if($check_customer){
                                $user_id = $check_customer->id;
                            }else{
                                ///============
                                ///create customer
                                $display_address = $user['hse_no'].' '. $user['st_name'].', '. $user['unit_no'].', '.$user['pc'];
                                $remarks = null;
                                if($user['remarks']!= null){
                                    $remarks .= $user['remarks'];
                                }
                                if($user['home']!= null){
                                    $remarks .= " - Home phone: ".$user['home'];
                                }
                                $new_user = new User();
                                $new_user->display_name           = $user['name_applicant'];
                                $new_user->street_no              = $user['hse_no'];
                                $new_user->street_name            = $user['st_name'];
                                $new_user->unit_no                = $user['unit_no'];
                                $new_user->postal_code            = $user['pc'];
                                $new_user->display_address        = $display_address;
                                $new_user->remarks                = $remarks;
                                $new_user->phone                  = $user['mobile'];
                                $new_user->email                  = $user['email'];
                                $new_user->church_attended        = $user['church'];
                                $new_user->save();
                                if($new_user){
                                    $user_id = $new_user->id;
                                }
                            }
                            ///create booking
                            $booking_status = Reference::where([
                                ['reference_type', '=', 'booking_status'],
                                ['reference_value_text', '=', 'Booked'],
                            ])->first();
                            $booking_no = 1;
                            $id_booking = Booking::select('id')->withTrashed()->orderBy('id', "DESC")->first();
                            if($id_booking){
                                $booking_no = (int)$id_booking->id + 1;
                            }
                            $number_booking_no = str_pad($booking_no, 4, '0',STR_PAD_LEFT);
                            $short_year = Carbon::now()->format('y');
                            $renew_booking = new Booking();
                            $renew_booking->user_id = $user_id;
                            $renew_booking->booking_no = "B-".$short_year."-".$number_booking_no;
                            $renew_booking->status = $booking_status->id;
                            $renew_booking->save();
                            if($renew_booking){
                                $booking_id = $renew_booking->id;
                            }
                        }
                    }
                }elseif($booking[$key]['NRIC'] != $booking[$key-1]['NRIC']){
                    foreach($applicant as $key_user => $user){
                        if($book['NRIC'] === $user['NRIC']){
                            $check_customer = User::where('display_name', 'like', "%".$user['name_applicant']."%")->first();
                            if($check_customer){
                                $user_id = $check_customer->id;
                            }else{
                                ///============
                                ///create customer
                                $display_address = $user['hse_no'].' '. $user['st_name'].', '. $user['unit_no'].', '.$user['pc'];
                                $remarks = null;
                                if($user['remarks']!= null){
                                    $remarks .= $user['remarks'];
                                }
                                if($user['home']!= null){
                                    $remarks .= " - Home phone: ".$user['home'];
                                }
                                $new_user = new User();
                                $new_user->display_name           = $user['name_applicant'];
                                $new_user->street_no              = $user['hse_no'];
                                $new_user->street_name            = $user['st_name'];
                                $new_user->unit_no                = $user['unit_no'];
                                $new_user->postal_code            = $user['pc'];
                                $new_user->display_address        = $display_address;
                                $new_user->remarks                = $remarks;
                                $new_user->phone                  = $user['mobile'];
                                $new_user->email                  = $user['email'];
                                $new_user->church_attended        = $user['church'];
                                $new_user->save();
                                if($new_user){
                                    $user_id = $new_user->id;
                                }
                            }
                            ///create booking
                            $booking_status = Reference::where([
                                ['reference_type', '=', 'booking_status'],
                                ['reference_value_text', '=', 'Booked'],
                            ])->first();
                            $booking_no = 1;
                            $id_booking = Booking::select('id')->withTrashed()->orderBy('id', "DESC")->first();
                            if($id_booking){
                                $booking_no = (int)$id_booking->id + 1;
                            }
                            $number_booking_no = str_pad($booking_no, 4, '0',STR_PAD_LEFT);
                            $short_year = Carbon::now()->format('y');
                            $renew_booking = new Booking();
                            $renew_booking->user_id = $user_id;
                            $renew_booking->booking_no = "B-".$short_year."-".$number_booking_no;
                            $renew_booking->status = $booking_status->id;
                            $renew_booking->save();
                            if($renew_booking){
                                $booking_id = $renew_booking->id;
                            }
                        }
                    }
                }
                $niche_info = Niche::where('reference_no', '=', $book['niche_no'])->first();
                if(empty($niche_info)){
                    $new_niche = new Niche();
                    $new_niche->reference_no    = $book['niche_no'];
                    $new_niche->type_id         = 0;
                    $new_niche->price           = 0;
                    $new_niche->status          = 'Unavailable';
                    $new_niche->category_id     = 0;
                    $new_niche->bay             = 'N/A';
                    $new_niche->wing            = 'N/A';
                    $new_niche->floor           = 'N/A';
                    $new_niche->block           = 'N/A';
                    $new_niche->level           = 'N/A';
                    $new_niche->unit            = 'N/A';
                    $new_niche->save();
                    if($new_niche){
                        $niche_id = $new_niche->id;
                        $niche_price = $new_niche->price;
                    }
                }else{
                    $niche_id = $niche_info->id;
                    $niche_price = $niche_info->price;

                }
                ///============
                ///create booking line items
                $status_niches_unoccupied = Reference::where([
                    ['reference_type', '=', 'status_services_niches'],
                    ['reference_value_text', '=', 'Sold - Unoccupied'],
                ])->first();
                ///
                $co_liense_no = Reference::Where(
                    [
                        ['reference_type', 'like', 'co_liense'],
                        ['reference_value_text', 'like', 'No']
                    ])->first();
                //
                $booking_type_niche = Reference::Where(
                    [
                        ['reference_type', 'like', 'booking_type'],
                        ['reference_value_text', 'like', 'Niches']
                    ])->first();
                //======
                $booking_line_item = new BookingLineItems();
                $booking_line_item->booking_id          = $booking_id;
                $booking_line_item->booking_type_id     = $booking_type_niche->id;
                $booking_line_item->service_id          = $niche_id;
                $booking_line_item->application_date    = $book['appln_date'];
                $booking_line_item->co_license          = $co_liense_no->id;
                $booking_line_item->booking_no          = $book['sno']."-".$book['appln_no'];
                $booking_line_item->amount              = $niche_price;
                $booking_line_item->status              = $status_niches_unoccupied->id;
                $booking_line_item->save();
                if($booking_line_item){
                    $line_item_id = $booking_line_item->id;
                    // $niche = Niche::where('id',$niche_id)->first();
                    // if($niche){
                    //     $niche->booking_id = $booking_id;
                    //     $niche->booking_line_item = $line_item_id;
                    //     $niche->save();
                    // }
                    if($user['home'] != null){
                        $remarks = new Remarks();
                        $remarks->booking_line_item_id = $line_item_id;
                        $remarks->remarks = 'Home phone: '.$user['home'];
                        $remarks->save();
                    }
                }
                foreach($deceased as $key_dec => $dec){
                    if($book['sno'] === $dec['sno']){
                        if($dec['deceased_1'] != null){
                            $occupied = Reference::Where(
                                [
                                    ['reference_type', 'like', 'status_services_niches'],
                                    ['reference_value_text', 'like', 'Sold - Occupied']
                                ])->first();
                            //
                            BookingLineItems::where('id',$line_item_id)->update(['status' => $occupied->id]);
    
                            $niche_item = new BookingNicheItem();
                            $niche_item->booking_line_items_id      = $line_item_id;
                            $niche_item->full_name                 = $dec['deceased_1'];
                            $niche_item->death_anniversary          = $dec['d1_date'];
                            $niche_item->save();
                        }
                        if($dec['deceased_2'] != null){
                            $niche_item = new BookingNicheItem();
                            $niche_item->booking_line_items_id      = $line_item_id;
                            $niche_item->full_name                 = $dec['deceased_2'];
                            $niche_item->death_anniversary          = $dec['d2_date'];
                            $niche_item->save();
                        }
                        ///remarks
                        if($dec['remarks'] != null){
                            $remarks = new Remarks();
                            $remarks->booking_line_item_id = $line_item_id;
                            $remarks->remarks = $dec['remarks'];
                            $remarks->save();
                        }
                    }
                }
            }
            return response()->json(
                [
                    'status' => 'Successfully import file'
                ], 200);
        }
        return response()->json(
            [
                'status' => 'error',
                'errors' => 'Not file'
            ], 404);
    }

    public static function checkNullImport($value){
        if($value == "" || $value == " "){
            $value = "N/A";
        }
        return $value;
    }

       /**
 * @OA\Post(
 *     tags={"Booking"},
 *     path="/api/import-data-booking",
 *     summary="Import file Data Booking",
 *      @OA\RequestBody(
 *          @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 type="object",
 *                 @OA\Property(
 *                     property="file_excel",
 *                     description="file_excel Booking",
 *                     type="file",
 *                 ),
 *             )
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
    public function importDataBooking(Request $request){
        ini_set('max_execution_time', 3000);
        $file = $request->file("file_excel");
        if(!empty($file)){
            $reader = ReaderEntityFactory::createXLSXReader();
            $reader->setShouldFormatDates(true);
            // $reader = ReaderFactory::create(Type::XLSX); // for XLSX files
            // $file = public_path("/Niches_database_31082020.xlsx");
            $reader->open($file);
            $deceased = array();
            $applicant = array();
            $lst_booking = array();
            $co_lessee = array();
            // dd($reader->getSheetIterator());
            
            foreach ($reader->getSheetIterator() as $sheet) {
                if($sheet->getName() == "Niche Database"){
                    foreach ($sheet->getRowIterator() as $key=>$row) {
                        $cells = $row->getCells();
                        $flag = $cells[0]->getValue();
                        if(!($flag === "Sno")){
                            if(!($flag === " " || $flag == null)){
                                //
                                $sno             = $this->checkNullValue($cells[0]->getValue());
                                $appln_no        = $this->checkNullValue($cells[1]->getValue());
                                $niche_no        = $this->checkNullValue($cells[2]->getValue());
                                $lease_expiry    = $this->checkNullValue($cells[3]->getValue());
                                $appln_date      = $this->checkNullValue($cells[4]->getValue());
                                $payment_mode    = $this->checkNullValue($cells[5]->getValue());
                                $receipt_no      = $this->checkNullValue($cells[6]->getValue());
                                $amount          = $this->checkNullValue($cells[7]->getValue());
                                $gst             = $this->checkNullValue($cells[8]->getValue());
                                $total           = $this->checkNullValue($cells[9]->getValue());
                                $id_applicant    = $this->checkNullValue($cells[10]->getValue());
                                // Carbon::parse($lease_expiry)->format('Y-m-d'),
                                // dd(is_int($gst)?$gst:0);
                                $appln_date       = Carbon::parse($appln_date)->format('Y-m-d');  
                                $lease_expiry     = Carbon::createFromFormat('Y-m-d',$lease_expiry.'-12-31')->toDateTimeString();
                                $booking_line_item = array(
                                    'sno'            => $sno, 
                                    'appln_no'       => $appln_no, 
                                    'niche_no'       => $niche_no,
                                    'lease_expiry'   => $lease_expiry,
                                    'appln_date'     => $appln_date,
                                    'payment_mode'   => $payment_mode,
                                    'receipt_no'     => $receipt_no,
                                    'amount'         => $amount,
                                    'gst'            => is_int($gst)?$gst:0,
                                    'total'          => $total,
                                    'id_applicant'   => $id_applicant,
                                );
                                array_push($lst_booking, $booking_line_item);
                                // dd($booking);
                            }
                        } 
                    }
                }
                if($sheet->getName() === "Main Applicant Details"){
                    foreach ($sheet->getRowIterator() as $key=>$row) {
                        $cells = $row->getCells();
                        $flag = $cells[0]->getValue();
                        if(!($flag === "A1 ID")){
                            if(!($flag === " " || $flag == null)){
                                
                                $ID           = $this->checkNullValue($cells[0]->getValue());
                                $name         = $this->checkNullValue($cells[1]->getValue());
                                $status       = $this->checkNullValue($cells[2]->getValue());
                                $remarks      = $this->checkNullValue($cells[3]->getValue());
                                $pc           = $this->checkNullValue($cells[4]->getValue());
                                $hse_no       = $this->checkNullValue($cells[5]->getValue());
                                $st_name      = $this->checkNullValue($cells[6]->getValue());
                                $unit_no      = $this->checkNullValue($cells[7]->getValue());
                                $email        = $this->checkNullValue($cells[8]->getValue());
                                $mobile       = $this->checkNullValue($cells[9]->getValue());
                                $phone        = $this->checkNullValue($cells[10]->getValue());
                                $church        = $this->checkNullValue($cells[11]->getValue());

                                $applicant_detail = array(
                                    'ID'        => $ID, 
                                    'name'      => $name, 
                                    'status'    => $status,
                                    'remarks'   => $remarks,
                                    'pc'        => $pc,
                                    'hse_no'    => $hse_no,
                                    'st_name'   => $st_name,
                                    'unit_no'   => $unit_no,
                                    'email'     => $email,
                                    'mobile'    => $mobile,
                                    'phone'     => $phone,
                                    'church'    => $church,
                                );
                                array_push($applicant, $applicant_detail);
                                // dd($applicant);
                            }
                        }
                    }
                }
                if($sheet->getName() === "2nd Applicant Details"){
                    foreach ($sheet->getRowIterator() as $key=>$row) {
                        $cells = $row->getCells();
                        $flag = $cells[0]->getValue();
                        if(!($flag === "Sno")){
                            if(!($flag === " " || $flag == null)){
                                $sno            = $this->checkNullValue($cells[0]->getValue());
                                $appln_no       = $this->checkNullValue($cells[1]->getValue());
                                $appln_date     = $this->checkNullValue($cells[2]->getValue());
                                $niche_no       = $this->checkNullValue($cells[3]->getValue());
                                $A2_ID          = $this->checkNullValue($cells[4]->getValue());
                                $A2_name        = $this->checkNullValue($cells[5]->getValue());
                                $A2_status      = $this->checkNullValue($cells[6]->getValue());
                                $A2_remarks     = $this->checkNullValue($cells[7]->getValue());
                                $A2_pc          = $this->checkNullValue($cells[8]->getValue());
                                $A2_hse_no      = $this->checkNullValue($cells[9]->getValue());
                                $A2_st_name     = $this->checkNullValue($cells[10]->getValue());
                                $A2_unit_no     = $this->checkNullValue($cells[11]->getValue());
                                $A3_ID          = $this->checkNullValue($cells[12]->getValue());
                                $A3_name        = $this->checkNullValue($cells[13]->getValue());
                                $A3_status      = $this->checkNullValue($cells[14]->getValue());
                                $A3_remarks     = $this->checkNullValue($cells[15]->getValue());
                                ///
                                $appln_date       = Carbon::parse($appln_date)->format('Y-m-d');  

                                $co_license = array(
                                    'sno'          => $sno, 
                                    'appln_no'     => $appln_no, 
                                    'appln_date'   => $appln_date,
                                    'niche_no'     => $niche_no,
                                    'A2_ID'        => $A2_ID,
                                    'A2_name'      => $A2_name,
                                    'A2_status'    => $A2_status,
                                    'A2_remarks'   => $A2_remarks,
                                    'A2_pc'        => $A2_pc,
                                    'A2_hse_no'    => $A2_hse_no,
                                    'A2_st_name'   => $A2_st_name,
                                    'A2_unit_no'   => $A2_unit_no,
                                    'A3_ID'        => $A3_ID,
                                    'A3_name'      => $A3_name,
                                    'A3_status'    => $A3_status,
                                    'A3_remarks'   => $A3_remarks,
                                );
                                array_push($co_lessee, $co_license);
                            }
                        }
                    }
                }
                if($sheet->getName() === "Users & Deceased"){
                    foreach ($sheet->getRowIterator() as $key=>$row) {
                        $cells = $row->getCells();
                        $flag = $cells[0]->getValue();
                        if(!($flag === "Sno")){
                            if(!($flag === " " || $flag == null)){
                                $sno                 = $this->checkNullValue($cells[0]->getValue());
                                $niche_no            = $this->checkNullValue($cells[1]->getValue());
                                $user1               = $this->checkNullValue($cells[2]->getValue());
                                $user2               = $this->checkNullValue($cells[3]->getValue());
                                $user_remarks        = $this->checkNullValue($cells[4]->getValue());
                                $deceased1           = $this->checkNullValue($cells[5]->getValue());
                                $deceased1_date      = $this->checkDateImport($cells[6]->getValue());
                                $urn1_date           = $this->checkDateImport($cells[7]->getValue());
                                $plaque1_date        = $this->checkDateImport($cells[8]->getValue());
                                $deceased2           = $this->checkNullValue($cells[9]->getValue());
                                $deceased2_date      = $this->checkDateImport($cells[10]->getValue());
                                $urn2_date           = $this->checkDateImport($cells[11]->getValue());
                                $plaque2_date        = $this->checkDateImport($cells[12]->getValue());
                                $remarks             = $this->checkNullValue($cells[13]->getValue());
                                ///
                                // $deceased1_date     = $this->checkDateImport($deceased1_date); 
                                // $urn1_date          = $this->checkDateCustom($urn1_date)      ? (Carbon::parse($urn1_date)->format('Y-m-d'))        : null;  
                                // $plaque1_date       = $this->checkDateCustom($plaque1_date)   ? (Carbon::parse($plaque1_date)->format('Y-m-d'))     : null;  
                                // ////
                                // $deceased2_date     = $this->checkDateCustom($deceased2_date) ? (Carbon::parse($deceased2_date)->format('Y-m-d'))   : null;  
                                // $urn2_date          = $this->checkDateCustom($urn2_date)      ? (Carbon::parse($urn2_date)->format('Y-m-d'))        : null;  
                                // $plaque2_date       = $this->checkDateCustom($plaque2_date)   ? (Carbon::parse($plaque2_date)->format('Y-m-d'))     : null;  
                                $deceased_item = array(
                                    'sno'               => $sno,
                                    'niche_no'          => $niche_no, 
                                    'user1'             => $user1, 
                                    'user2'             => $user2,
                                    'user_remarks'      => $user_remarks,
                                    'deceased1'         => $deceased1,
                                    'deceased1_date'    => $deceased1_date,
                                    'urn1_date'         => $urn1_date,
                                    'plaque1_date'      => $plaque1_date,
                                    'deceased2'         => $deceased2,
                                    'deceased2_date'    => $deceased2_date,
                                    'urn2_date'         => $urn2_date,
                                    'plaque2_date'      => $plaque2_date,
                                    'remarks'           => $remarks,
                                );
                                array_push($deceased, $deceased_item);
                                // dd($deceased);
                            }
                        } 
                    }
                }
            } 
            $reader->close();
            // $arr_demo = array();
            // $total_amount = 0;
            // $total_tax_amount = 0;
            // $total =  0;
            // dd($co_lessee[0]);
            // $count_booking = count($lst_booking);
            $total_amount_booking = 0;
            $total_tax_amount_booking = 0;
            foreach($lst_booking as $key=>$book){
                $key_temp = $key+1;
                $check_duplicate_booking = 2;
                if($key_temp <= count($lst_booking)){
                    // dd($booking[$key+1]['id_applicant']);
                    if($key == 0){
                        $check_duplicate_booking = 1;
                        $book['user_id'] = 0;
                        foreach($applicant as $key_user => $user){
                            if($book['id_applicant'] === $user['ID']){
                                $info_user = User::where('display_name', 'like', $user['name'])->first();
                                if($info_user){
                                    $book['user_id'] = $info_user->id;
                                }else{
                                    ///create customer
                                    $display_address = $user['hse_no'].' '. $user['st_name'].', '. $user['unit_no'].', '.$user['pc'];
                                    $remarks = null;
                                    if($user['remarks']!= null){
                                        $remarks .= $user['remarks'];
                                    }
                                    if($user['phone']!= null){
                                        $remarks .= " - Home phone: ".$user['phone'];
                                    }
                                    if($user['status']!= null){
                                        $remarks .= " - Status: ".$user['status'];
                                    }
                                    $new_user = new User();
                                    $new_user->display_name           = $user['name'];
                                    $new_user->street_no              = $user['hse_no'];
                                    $new_user->street_name            = $user['st_name'];
                                    $new_user->unit_no                = $user['unit_no'];
                                    $new_user->postal_code            = $user['pc'];
                                    $new_user->display_address        = $display_address;
                                    $new_user->remarks                = $remarks;
                                    $new_user->phone                  = $user['mobile'];
                                    $new_user->email                  = $user['email'];
                                    $new_user->church_attended        = $user['church'];
                                    $new_user->save();
                                    if($new_user){
                                        $book['user_id'] = $new_user->id;
                                    }
                                }
                            }
                        }
                       ///create booking
                       $booking_status = Reference::where([
                            ['reference_type', '=', 'booking_status'],
                            ['reference_value_text', '=', 'Booked'],
                        ])->first();
                        $booking_no = 1;
                        $id_booking = Booking::select('id')->withTrashed()->orderBy('id', "DESC")->first();
                        if($id_booking){
                            $booking_no = (int)$id_booking->id + 1;
                        }
                        $number_booking_no = str_pad($booking_no, 4, '0',STR_PAD_LEFT);
                        $short_year = Carbon::now()->format('y');
                        $renew_booking = new Booking();
                        $renew_booking->user_id     = $book['user_id'];
                        $renew_booking->booking_no  = "B-".$short_year."-".$number_booking_no;
                        $renew_booking->status      = $booking_status->id;
                        $renew_booking->save();
                        if($renew_booking){
                            $booking_id = $renew_booking->id;
                            $total_amount_booking = 0;
                            $total_tax_amount_booking = 0;
                        }
                    }elseif($lst_booking[$key]['id_applicant'] != $lst_booking[$key-1]['id_applicant']){
                        $check_duplicate_booking = 1;
                        $book['user_id'] = 0;
                        foreach($applicant as $key_user => $user){
                            if($book['id_applicant'] === $user['ID']){
                                $info_user = User::where('display_name', 'like', $user['name'])->first();
                                if($info_user){
                                    $book['user_id'] = $info_user->id;
                                }else{
                                    ///create customer
                                    $display_address = $user['hse_no'].' '. $user['st_name'].', '. $user['unit_no'].', '.$user['pc'];
                                    $remarks = null;
                                    if($user['remarks']!= null){
                                        $remarks .= $user['remarks'];
                                    }
                                    if($user['phone']!= null){
                                        $remarks .= " - Home phone: ".$user['phone'];
                                    }
                                    if($user['status']!= null){
                                        $remarks .= " - Status: ".$user['status'];
                                    }
                                    $new_user = new User();
                                    $new_user->display_name           = $user['name'];
                                    $new_user->street_no              = $user['hse_no'];
                                    $new_user->street_name            = $user['st_name'];
                                    $new_user->unit_no                = $user['unit_no'];
                                    $new_user->postal_code            = $user['pc'];
                                    $new_user->display_address        = $display_address;
                                    $new_user->remarks                = $remarks;
                                    $new_user->phone                  = $user['mobile'];
                                    $new_user->email                  = $user['email'];
                                    $new_user->church_attended        = $user['church'];
                                    $new_user->save();
                                    if($new_user){
                                        $book['user_id'] = $new_user->id;
                                    }
                                } 
                            }
                        }
                        ///create booking
                       $booking_status = Reference::where([
                            ['reference_type', '=', 'booking_status'],
                            ['reference_value_text', '=', 'Booked'],
                        ])->first();
                        $booking_no = 1;
                        $id_booking = Booking::select('id')->withTrashed()->orderBy('id', "DESC")->first();
                        if($id_booking){
                            $booking_no = (int)$id_booking->id + 1;
                        }
                        $number_booking_no = str_pad($booking_no, 4, '0',STR_PAD_LEFT);
                        $short_year = Carbon::now()->format('y');
                        $renew_booking = new Booking();
                        $renew_booking->user_id     = $book['user_id'];
                        $renew_booking->booking_no  = "B-".$short_year."-".$number_booking_no;
                        $renew_booking->status      = $booking_status->id;
                        $renew_booking->save();
                        if($renew_booking){
                            $booking_id = $renew_booking->id;
                            $total_amount_booking = 0;
                            $total_tax_amount_booking = 0;
                        }
                    }
                    $booking_type = Reference::Where(
                        [
                            ['reference_type', 'like', 'booking_type'],
                            ['reference_value_text', 'like', 'Niches']
                        ])->first();
        
                    $niche_id = Niche::where('reference_no', 'like', $book['niche_no'])->first();
                    if($niche_id){
                        $niche_id_flag = $niche_id->id;
                    }else{
                        $new_niche = new Niche();
                        $new_niche->reference_no    = $book['niche_no'];
                        $new_niche->type_id         = 0;
                        $new_niche->price           = 0;
                        $new_niche->status          = 'Unavailable';
                        $new_niche->category_id     = 0;
                        $new_niche->bay             = 'N/A';
                        $new_niche->wing            = 'N/A';
                        $new_niche->floor           = 'N/A';
                        $new_niche->block           = 'N/A';
                        $new_niche->level           = 'N/A';
                        $new_niche->unit            = 'N/A';
                        $new_niche->save();
                        if($new_niche){
                            $niche_id_flag = $new_niche->id;
                        }
                    }
                    //
                    $book['co_lessee'] = null;
                    foreach($co_lessee as $key_co => $co_less){
                        if($book['sno'] == $co_less['sno'])
                        {
                            $book['co_lessee'] = $co_less;
                        }
                    }
                    $status_niches_unoccupied = Reference::where([
                            ['reference_type', '=', 'status_services_niches'],
                            ['reference_value_text', '=', 'Sold - Unoccupied'],
                        ])->first();
                    $booking_line_item = New BookingLineItems();
                    $booking_line_item->booking_id                  = $booking_id;
                    $booking_line_item->booking_type_id             = $booking_type->id;
                    $booking_line_item->service_id                  = $niche_id_flag;
                    $booking_line_item->booking_date                = $book['appln_date'];
                    $booking_line_item->tax_amount                  = $book['gst'];
                    $booking_line_item->amount                      = $book['amount'];
                    // $booking_line_item->start_date                  = $book['start_date'];
                    $booking_line_item->lease_expiry_date           = $book['lease_expiry'];
                    $booking_line_item->application_date            = $book['appln_date'];
                    $booking_line_item->application_no              = $book['appln_no'];
                    $booking_line_item->booking_no                  = $book['sno']."-".$book['appln_no'];
                    if($book['co_lessee'] != null){
                        $co_liense = Reference::Where(
                            [
                                ['reference_type', 'like', 'co_liense'],
                                ['reference_value_text', 'like', 'Yes']
                            ])->first();
                        ///
                        $booking_line_item->co_license                  = $co_liense->id;
                        $booking_line_item->co_license_name             = $book['co_lessee']['A2_name'];
                        $booking_line_item->co_license_email            = "N/A";
                        $booking_line_item->co_license_phone            = null;
                        $booking_line_item->co_license_postal_code      = $book['co_lessee']['A2_pc'];
                        $booking_line_item->co_license_street_no        = $book['co_lessee']['A2_hse_no'];
                        $booking_line_item->co_license_street_name      = $book['co_lessee']['A2_st_name'];
                        $booking_line_item->co_license_unit_no          = $book['co_lessee']['A2_unit_no'];
                    }else{
                        $co_liense = Reference::Where(
                            [
                                ['reference_type', 'like', 'co_liense'],
                                ['reference_value_text', 'like', 'No']
                            ])->first();
                        //
                        $booking_line_item->co_license                  = $co_liense->id;
                    }
                    $booking_line_item->status = $status_niches_unoccupied->id;
                    $booking_line_item->save();
                    $total_amount_booking += $booking_line_item->amount;
                    $total_tax_amount_booking += $booking_line_item->tax_amount;

                    Booking::where('id',$booking_id)->update(['total_amount' => $total_amount_booking,'total_tax_amount'=> $total_tax_amount_booking]);
                    $niches = Niche::find($booking_line_item->service_id);
                    if(isset($niches)){
                        $line_item_id = $booking_line_item->id;
                        $niches->booking_id         = $booking_id != 0 ? $booking_id : NULL;
                        $niches->booking_line_item  = $booking_line_item->id;
                        $niches->status             = 'Unavailable';
                        $niches->save();
                    }
                    foreach($deceased as $key_user => $deceased_detail){
                        if($book['niche_no'] === $deceased_detail['niche_no']){
                            $booking = Booking::where('id', '=', $booking_id)->first();
                            $occupied = Reference::Where(
                                [
                                    ['reference_type', 'like', 'status_services_niches'],
                                    ['reference_value_text', 'like', 'Sold - Occupied']
                                ])->first();
                            //
                            if($deceased_detail['deceased1'] != null){
                                BookingLineItems::where('id',$line_item_id)->update(['status' => $occupied->id]);
                                $niche_item = new BookingNicheItem();
                                $niche_item->booking_line_items_id      = $line_item_id;
                                $niche_item->full_name                  = $deceased_detail['deceased1'];
                                $niche_item->death_anniversary          = $deceased_detail['deceased1_date'];
                                $niche_item->save();
                            }
                            if($deceased_detail['deceased2'] != null){
                                $niche_item = new BookingNicheItem();
                                $niche_item->booking_line_items_id      = $line_item_id;
                                $niche_item->full_name                  = $deceased_detail['deceased2'];
                                $niche_item->death_anniversary          = $deceased_detail['deceased2_date'];
                                $niche_item->save();
                            }
                            if($deceased_detail['urn1_date'] != null){
                                //=============
                                $service_urn = Other::where('service_name', '=', 'Urn')->first();
                                //=============
                                $service_item = Other::where([
                                    ['parent_id', '=', $service_urn->id],
                                    ['service_name', '=', 'N/A']
                                ])->first();
                                //=============
                                $booking_type = Reference::where([
                                    ['reference_type', '=', 'booking_type'],
                                    ['reference_value_text', '=', 'Additional Services'],
                                ])->first();
                                $services_item = new BookingLineItems();
                                $services_item->booking_id          = $booking->id;
                                $services_item->booking_type_id     = $booking_type->id;
                                $services_item->service_id          = $service_urn->id;
                                $services_item->booking_date        = $deceased_detail['urn1_date'];
                                $services_item->service_type_id     = $service_item->id;
                                $services_item->booking_no          = $book['sno']."-".$book['appln_no'];
                                $services_item->save();
                                if($services_item){
                                    $sale_agreement = SaleAgreement::create([
                                        'sale_agreement_no'     =>  $services_item->booking_no,
                                        'sale_agreement_date'   =>  Carbon::now()->format('Y-m-d'),
                                        'booking_id'            =>  $booking->id,
                                        'user_id'               =>  $booking->user_id,
                                        'total_amount'        =>    null,
                                        'total_tax_amount'    =>    null,
                                        'total'               =>    null
                                    ]);
                                    if($sale_agreement){
                                        $id_sale_agreement = $sale_agreement->id;
                                        SaleAgreementLineItem::create([
                                            'sale_agreement_id'     =>  $sale_agreement->id,
                                            'booking_id'            =>  $booking->id,
                                            'line_item_id'          =>  $services_item->id
                                        ]);
                                    }
                                    $services_item->is_sale = 1;
                                    $services_item->save();
                                }
                            }
                            if($deceased_detail['plaque1_date'] != null){
                                //=============
                                $service_plaque = Other::where('service_name', '=', 'Plaque')->first();
                                //=============
                                $service_item = Other::where([
                                    ['parent_id', '=', $service_plaque->id],
                                    ['service_name', '=', 'N/A']
                                ])->first();
                                //=============
                                $booking_type = Reference::where([
                                    ['reference_type', '=', 'booking_type'],
                                    ['reference_value_text', '=', 'Additional Services'],
                                ])->first();
                                $services_item = new BookingLineItems();
                                $services_item->booking_id          = $booking->id;
                                $services_item->booking_id          = $booking->id;
                                $services_item->booking_type_id     = $booking_type->id;
                                $services_item->service_id          = $service_plaque->id;
                                $services_item->booking_date        = $deceased_detail['plaque1_date'];
                                $services_item->service_type_id     = $service_item->id;
                                $services_item->booking_no          = $book['sno']."-".$book['appln_no'];
                                $services_item->save();
                                if($services_item){
                                    $sale_agreement = SaleAgreement::create([
                                        'sale_agreement_no'     =>  $services_item->booking_no,
                                        'sale_agreement_date'   =>  Carbon::now()->format('Y-m-d'),
                                        'booking_id'            =>  $booking->id,
                                        'user_id'               =>  $booking->user_id,
                                        'total_amount'        =>    null,
                                        'total_tax_amount'    =>    null,
                                        'total'               =>    null
                                    ]);
                                    if($sale_agreement){
                                        $id_sale_agreement = $sale_agreement->id;
                                        SaleAgreementLineItem::create([
                                            'sale_agreement_id'     =>  $sale_agreement->id,
                                            'booking_id'            =>  $booking->id,
                                            'line_item_id'          =>  $services_item->id
                                        ]);
                                    }
                                    $services_item->is_sale = 1;
                                    $services_item->save();
                                }
                            }
                            if($deceased_detail['urn2_date'] != null){
                                //=============
                                $service_urn = Other::where('service_name', '=', 'Urn')->first();
                                //=============
                                $service_item = Other::where([
                                    ['parent_id', '=', $service_urn->id],
                                    ['service_name', '=', 'N/A']
                                ])->first();
                                //=============
                                $booking_type = Reference::where([
                                    ['reference_type', '=', 'booking_type'],
                                    ['reference_value_text', '=', 'Additional Services'],
                                ])->first();
                                $services_item = new BookingLineItems();
                                $services_item->booking_id          = $booking->id;
                                $services_item->booking_type_id     = $booking_type->id;
                                $services_item->service_id          = $service_urn->id;
                                $services_item->booking_date        = $deceased_detail['urn2_date'];
                                $services_item->service_type_id     = $service_item->id;
                                $services_item->booking_no          = $book['sno']."-".$book['appln_no'];
                                $services_item->save();
                                if($services_item){
                                    $sale_agreement = SaleAgreement::create([
                                        'sale_agreement_no'     =>  $services_item->booking_no,
                                        'sale_agreement_date'   =>  Carbon::now()->format('Y-m-d'),
                                        'booking_id'            =>  $booking->id,
                                        'user_id'               =>  $booking->user_id,
                                        'total_amount'        =>    null,
                                        'total_tax_amount'    =>    null,
                                        'total'               =>    null
                                    ]);
                                    if($sale_agreement){
                                        $id_sale_agreement = $sale_agreement->id;
                                        SaleAgreementLineItem::create([
                                            'sale_agreement_id'     =>  $sale_agreement->id,
                                            'booking_id'            =>  $booking->id,
                                            'line_item_id'          =>  $services_item->id
                                        ]);
                                    }
                                    $services_item->is_sale = 1;
                                    $services_item->save();
                                }
                            }
                            if($deceased_detail['plaque2_date'] != null){
                                //=============
                                $service_plaque = Other::where('service_name', '=', 'Plaque')->first();
                                //=============
                                $service_item = Other::where([
                                    ['parent_id', '=', $service_plaque->id],
                                    ['service_name', '=', 'N/A']
                                ])->first();
                                //=============
                                $booking_type = Reference::where([
                                    ['reference_type', '=', 'booking_type'],
                                    ['reference_value_text', '=', 'Additional Services'],
                                ])->first();
                                $services_item = new BookingLineItems();
                                $services_item->booking_id          = $booking->id;
                                $services_item->booking_type_id     = $booking_type->id;
                                $services_item->service_id          = $service_plaque->id;
                                $services_item->booking_date        = $deceased_detail['plaque2_date'];
                                $services_item->service_type_id     = $service_item->id;
                                $services_item->booking_no          = $book['sno']."-".$book['appln_no'];
                                $services_item->save();
                                if($services_item){
                                    $sale_agreement = SaleAgreement::create([
                                        'sale_agreement_no'     =>  $services_item->booking_no,
                                        'sale_agreement_date'   =>  Carbon::now()->format('Y-m-d'),
                                        'booking_id'            =>  $booking->id,
                                        'user_id'               =>  $booking->user_id,
                                        'total_amount'        =>    null,
                                        'total_tax_amount'    =>    null,
                                        'total'               =>    null
                                    ]);
                                    if($sale_agreement){
                                        $id_sale_agreement = $sale_agreement->id;
                                        SaleAgreementLineItem::create([
                                            'sale_agreement_id'     =>  $sale_agreement->id,
                                            'booking_id'            =>  $booking->id,
                                            'line_item_id'          =>  $services_item->id
                                        ]);
                                    }
                                    $services_item->is_sale = 1;
                                    $services_item->save();
                                }
                            }
                            if($deceased_detail['remarks'] != null){
                                $remarks = new Remarks();
                                $remarks->booking_line_item_id  = $line_item_id;
                                $remarks->remarks               = $deceased_detail['remarks'];
                                $remarks->save();
                            }   
                            if($deceased_detail['user2'] != null){
                                $remarks = new Remarks();
                                $remarks->booking_line_item_id  = $line_item_id;
                                $remarks->remarks               = 'User2: '.$deceased_detail['user2'];
                                $remarks->save();
                            }
                            if($deceased_detail['user1'] != null){
                                $remarks = new Remarks();
                                $remarks->booking_line_item_id  = $line_item_id;
                                $remarks->remarks               = 'User1: '.$deceased_detail['user1'];
                                $remarks->save();
                            }
                            if($deceased_detail['user_remarks'] != null){
                                $remarks = new Remarks();
                                $remarks->booking_line_item_id  = $line_item_id;
                                $remarks->remarks               = 'User Remarks: '.$deceased_detail['user_remarks'];
                                $remarks->save();
                            }
                        }
                    }
                    // ==============================================
                    // create remark
                    if($book['co_lessee'] != null){
                        if($book['co_lessee']['A2_status'] != null){
                            $remarks = new Remarks();
                            $remarks->booking_line_item_id = $line_item_id;
                            $remarks->remarks = "A2 Status: ".$book['co_lessee']['A2_status'];
                            $remarks->save();
                        }
                        if($book['co_lessee']['A2_remarks'] != null){
                            $remarks = new Remarks();
                            $remarks->booking_line_item_id = $line_item_id;
                            $remarks->remarks = "A2 Remarks: ".$book['co_lessee']['A2_remarks'];
                            $remarks->save();
                        }
                        if($book['co_lessee']['A3_status'] != null){
                            $remarks = new Remarks();
                            $remarks->booking_line_item_id = $line_item_id;
                            $remarks->remarks = "A3 Status: ".$book['co_lessee']['A3_status'];
                            $remarks->save();
                        }
                        if($book['co_lessee']['A3_remarks'] != null){
                            $remarks = new Remarks();
                            $remarks->booking_line_item_id = $line_item_id;
                            $remarks->remarks = "A3 Remarks: ".$book['co_lessee']['A3_remarks'];
                            $remarks->save();
                        }
                    }
                    // ==============================================
                    // create new sale agreement
                    $booking = Booking::where('id',$booking_id)->first();
                    $status = Reference::where('reference_type', 'booking_status')->where('reference_value_text', 'Agreement')->first();
                    $booking->update(['status' => $status->id]);
                    $booking_line_items = BookingLineItems::where('id', $booking_line_item->id)->first();
                    $total = $booking_line_items->amount + $booking_line_items->tax_amount;
                    $sale_agreement = SaleAgreement::create([
                        'sale_agreement_no'     =>  $booking_line_items->booking_no,
                        'sale_agreement_date'   =>  $booking_line_items->application_date,
                        'booking_id'            =>  $booking->id,
                        'user_id'               =>  $booking->user_id,
                        'total_amount'        =>    $booking_line_items->amount,
                        'total_tax_amount'    =>    $booking_line_items->tax_amount,
                        'total'               =>    $total
                    ]);
                    if($sale_agreement){
                        $id_sale_agreement = $sale_agreement->id;
                        $sale_agreement_item = SaleAgreementLineItem::create([
                            'sale_agreement_id'     =>  $sale_agreement->id,
                            'booking_id'            =>  $booking->id,
                            'line_item_id'          =>  $booking_line_items->id
                        ]);
                    }
                    $booking_line_items->is_sale = 1;
                    $booking_line_items->save();
                    //end sale agreement
                    //==============================================
                    //new invoices
                    $sale_agreement_for_invoice = SaleAgreement::where('booking_id', $booking_id)->get();
                    $status = Reference::where('reference_type', 'booking_status')->where('reference_value_text', 'Fully Invoiced')->first();
                    // SaleAgreementLineItem::where('sale_agreement_id',$id_sale_agreement)->update(['isInvoice'=> 1]);
                    $invoices_no = 1;
                    $num_id_invoice = Invoice::select('id')->orderBy('id', "DESC")->first();
                    if($num_id_invoice){
                        $invoices_no = (int)$num_id_invoice->id + 1;
                    }
                    $number_invoice_no = str_pad($invoices_no,4,'0',STR_PAD_LEFT);
                    $year_month = Carbon::now()->format('ym');
                    $invoice_date = Carbon::now()->format('Y-m-d');
                    if($check_duplicate_booking == 1){
                        $invoices = Invoice::create([
                            'invoice_no'          =>  "CCPL-".$year_month.'-'.$number_invoice_no,
                            'invoice_date'        =>  $invoice_date,
                            'user_id'             =>  $booking->user_id,
                            'total_amount'        =>  $booking->total_amount,
                            'total_tax_amount'    =>  $booking->total_tax_amount,
                            'total'               =>  $booking->total_amount + $booking->total_tax_amount,
                            // 'officer'             =>  Auth::user()->id,
                        ]);
                        $id_invoice = $invoices->id;
                    }
                    // if($check_duplicate_booking == 2){
                    //     dd($id_invoice);
                    // }
                    
                    if($id_invoice){
                        Invoice::where('id',$id_invoice)->update(['total_amount'=>$booking->total_amount,'total_tax_amount'=>$booking->total_tax_amount,'total'=>$booking->total_amount + $booking->total_tax_amount]);
                        // $total_amount = 0;
                        // $total_tax_amount = 0;
                        // $total = 0;
                        foreach($sale_agreement_for_invoice as $sale_agreement){
                            // $total_amount = $sale_agreement->total_amount == null ? 0 : $sale_agreement->total_amount;
                            // $total_tax_amount = $sale_agreement->total_tax_amount == null ? 0: $sale_agreement->total_tax_amount;
                            // $total += ($sale_agreement->total_amount == null ? 0 : $sale_agreement->total_amount) + ($sale_agreement->total_tax_amount == null ? 0: $sale_agreement->total_tax_amount);
                            $invoices_line_item = InvoiceLineItem::updateOrCreate(
                            [
                                'sale_agreement_id'     =>  $sale_agreement->id
                            ],
                            [
                                'invoice_id'            =>  $id_invoice,
                                'sale_agreement_id'     =>  $sale_agreement->id,
                                'line_item_id'          =>  $sale_agreement->sale_agreement_item->id
                            ]);
                            if($invoices_line_item){
                                SaleAgreementLineItem::where('id',$sale_agreement->sale_agreement_item->id)->update(['isInvoice'=> 1]);
                            }
                        }
                        SaleAgreement::where('id', $id_sale_agreement)->update(['invoice_id' => $id_invoice]);
                        $status_full_paid = Reference::where('reference_type', 'booking_status')->where('reference_value_text', 'Fully Invoiced')->first();
                        BookingLineItems::where('booking_id', $booking_id)->update(["status" => $status_full_paid->id]);
                        Booking::where('id', $booking_id)->update(["status" => $status_full_paid->id]);
                        // Invoice::where('id',$id_invoice)->update(['total_amount'=>$total_amount,'total_tax_amount'=>$total_tax_amount,'total'=>$total]);
                    }
                    // end invoices
                    // ==============================================
                    // new payment
                    $status = Reference::where('reference_type', 'booking_status')->where('reference_value_text', 'Fully Paid')->first();
                    // InvoiceLineItem::where('id',$sale_agreement_item->id)->update(['is_payment'=> 1]);
                    // $payment_no = 1;
                    // $id_payment = Payment::select('id')->orderBy('id', "DESC")->first();
                    // if($id_payment){
                    //     $payment_no = (int)$id_payment->id + 1;
                    // }
                    // $number_payment_no = str_pad($payment_no,4,'0',STR_PAD_LEFT);
                    // $year_month = Carbon::now()->format('ym');
                    // $now = now();
                    // $booking = Booking::where('id', $request->booking_id)->first();
                    // if(!empty($booking)){
                    $invoice_for_payment = Invoice::where('id', $id_invoice)->first();
                    if($check_duplicate_booking == 1){
                        $payment = Payment::create([
                            'payment_no'          =>  $book['receipt_no'],
                            'payment_date'        =>  $book['appln_date'],
                            'invoice_id'          =>  $id_invoice,
                            'user_id'             =>  $booking->user_id,
                            'total_amount'        =>  $invoice_for_payment->total_amount,
                            'total_tax_amount'    =>  $invoice_for_payment->total_tax_amount,
                            'total'               =>  $invoice_for_payment->total,
                            // 'total_discount'      =>  $invoice_for_payment->total_discount
                        ]);
                        $id_payment = $payment->id;
                    }
                    if($id_payment){
                        Payment::where('id',$id_payment)->update(['total_amount'=>$invoice_for_payment->total_amount,'total_tax_amount'=>$invoice_for_payment->total_tax_amount,'total'=>$invoice_for_payment->total_amount + $invoice_for_payment->total_tax_amount]);
                        $payment_line_item = PaymentLineItem::create([
                            'payment_id'            =>  $payment->id,
                            'invoice_id'            =>  $id_invoice,
                            'line_item_id'          =>  $line_item_id
                        ]);
                        InvoiceLineItem::where('invoice_id',$id_invoice)->update(['is_payment'=> 1]);
                        $status_full_paid = Reference::where('reference_type', 'booking_status')->where('reference_value_text', 'Fully Paid')->first();
                        // $status_partially_paid = Reference::where('reference_type', 'booking_status')->where('reference_value_text', 'Partially Paid')->first();
                        Booking::where('id', $booking->id)->update(["status" => $status_full_paid->id]);
                    }
                }
            }
            return response()->json(
                [
                    'status' => 'Successfully import file'
                ], 200);
        }
        return response()->json(
            [
                'status' => 'error',
                'errors' => 'Not file'
            ], 404);
    }

    public static function checkDateImport($date){
        if($date == "" || $date == " "){
            $flag = null;
        }else{
            $date_arr = explode('/', $date);
            if(isset($date_arr[2])){
                $m = (int)trim($date_arr[0]);
                $y = (int)trim($date_arr[2]);
                $d = (int)trim($date_arr[1]);
            }else{
                if(isset($date_arr[1])){
                    $date_y = (int)trim($date_arr[1]);
                    $date_m = (int)trim($date_arr[0]);
                    $date_d = 1;

                }else{
                    $date_y = (int)trim($date_arr[0]);
                    $date_m = 1;
                    $date_d = 1;
                }
                $m = $date_m;
                $y = $date_y;
                $d = $date_d;
            }
            $flag = Carbon::create($y,$m,$d)->format('Y-m-d');
        }
        return $flag;
    }
}
