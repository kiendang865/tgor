<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SaleAgreement;
use App\SaleAgreementLineItem;
use App\Booking;
use App\Reference;
use App\Payment;
use App\PaymentLineItem;
use App\InvoiceLineItem;
use Illuminate\Database\Eloquent\Builder;
use File;
use App;
use PDF;
use NumberToWords\NumberToWords;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Jobs\SendEmailToClient;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{

/**
 * @OA\Post(
 *     tags={"Payment"},
 *     path="/api/payment",
 *     summary="Create Payment",
 *     operationId="createPayment",
 *     @OA\Parameter(
 *         name="id",
 *         in="query",
 *         required=true,
 *         description="ID Invoice",
 *         @OA\Schema(
 *             type="string",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="booking_id",
 *         in="query",
 *         required=true,
 *         description="ID Booking",
 *         @OA\Schema(
 *             type="string",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="user_id",
 *         in="query",
 *         required=true,
 *         description="ID user",
 *         @OA\Schema(
 *             type="integer",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="service_ids",
 *         in="query",
 *         required=true,
 *         description="Service ID",
 *         @OA\Schema(
 *             type="string",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="total_amount",
 *         in="query",
 *         required=true,
 *         description="Total Amount",
 *         @OA\Schema(
 *             type="string",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="total_tax_amount",
 *         in="query",
 *         required=true,
 *         description="Total Tax Amount",
 *         @OA\Schema(
 *             type="string",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="total",
 *         in="query",
 *         required=true,
 *         description="Total",
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
    public function createPayment(Request $request){
        if($request->id){
            $payment_no = 1;
            $id_payment = Payment::select('id')->orderBy('id', "DESC")->first();
            if($id_payment){
                $payment_no = (int)$id_payment->id + 1;
            }
            $number_payment_no = str_pad($payment_no,4,'0',STR_PAD_LEFT);
            $year_month = Carbon::now()->format('ym');
            $now = now();
            $check_payment = Payment::where("invoice_id", $request->id)->first();
            // $booking = Booking::where('id', $request->booking_id)->first();
            // if(!empty($booking)){
            $payment = Payment::updateOrCreate(
                [
                    'invoice_id'      =>  $request->id,
                ],
                [
                'payment_no'          =>  "R-".$year_month.'-'.$number_payment_no,
                'payment_date'        =>  $now->toDateTimeString(),
                'invoice_id'          =>  $request->id,
                'user_id'             =>  $request->user_id,
                'total_amount'        =>  $request->total_amount,
                'total_tax_amount'    =>  $request->total_tax_amount,
                'total'               =>  $request->total,
                'total_discount'      =>  $request->total_discount
            ]);
            if($payment){
                $payment_line_items = $request->service_ids;
                if(is_string($payment_line_items)){
                    $payment_line_items = json_decode($payment_line_items);
                }
                if($check_payment){
                    PaymentLineItem::where('payment_id', $check_payment->id)->delete();
                }
                foreach ($payment_line_items as $value) {
                    $payment_line_items = PaymentLineItem::create([
                        'payment_id'            =>  $payment->id,
                        'invoice_id'            =>  $request->id,
                        'line_item_id'          =>  $value
                    ]);
                    InvoiceLineItem::where('id',$value)->update(['is_payment'=> 1]);
                }

                $invoice_list = InvoiceLineItem::whereIn('id',$request->service_ids)->get();
                foreach($invoice_list as $val){
                    $booking = Booking::where('id', $val->sale_agreement->booking_id)->first();

                    $status_full_paid = Reference::where('reference_type', 'booking_status')->where('reference_value_text', 'Fully Paid')->first();
                    $status_partially_paid = Reference::where('reference_type', 'booking_status')->where('reference_value_text', 'Partially Paid')->first();
                    $status_receipt_generated = Reference::where('reference_type', 'booking_status')->where('reference_value_text', 'Receipt Generated')->first();
                    $invoice_item = InvoiceLineItem::where('invoice_id', $request->id)->count();
                    $payment_line_items = PaymentLineItem::where('invoice_id', $request->id)->count();
                    // if($payment_line_items == $invoice_item){
                    //     $booking->update(['status' => $status_full_paid->id]);
                    // }else{
                    //     $booking->update(['status' => $status_partially_paid->id]);
                    // }
                    $booking->update(['status' => $status_receipt_generated->id]);
                }
                
            }
            // }
            // else{
            //     return response()->json([
            //         'status' => 'error',
            //         'errors' => "Something bad happened, please try later"
            //     ], 422);
            // }
            return response()->json([
                'status' => 'Generate Payment Successfully.',
                'data'   => $payment
            ], 200);
        }else{
            return response()->json(
                [
                    'status' => 'error',
                    'errors' => 'Cannot find id'
                ], 404);
        }
    }

/**
 * @OA\Post(
 *     tags={"Payment"},
 *     path="/api/update-payment/{id}",
 *     summary="Update Payment",
 *     operationId="updatePayment",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Id Payment",
 *         @OA\Schema(
 *             type="integer",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="payment_mode_id",
 *         in="query",
 *         required=false,
 *         description="Payment Mode",
 *         @OA\Schema(
 *             type="integer",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="cheque",
 *         in="query",
 *         required=false,
 *         description="Cheque",
 *         @OA\Schema(
 *             type="string",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="cheque_bank",
 *         in="query",
 *         required=false,
 *         description="Cheque Bank",
 *         @OA\Schema(
 *             type="string",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="transaction",
 *         in="query",
 *         required=false,
 *         description="Transaction",
 *         @OA\Schema(
 *             type="string",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="remarks",
 *         in="query",
 *         required=false,
 *         description="remarks",
 *         @OA\Schema(
 *             type="string",
 *         )
 *     ),
 *      @OA\RequestBody(
 *          @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 type="object",
 *                 @OA\Property(
 *                     property="signature_offier",
 *                     description="File signature_offier",
 *                     type="file",
 *                 ),
 *                  @OA\Property(
 *                     property="signature_client",
 *                     description="File signature_client",
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

    public function updatePayment(Request $request, $id){
        // var_dump($request->all());exit;
        if($id){
            $signature_offier = $request->file('signature_offier');
            $signature_client = $request->file('signature_client');
            $payment = Payment::where('id', $id)->first();
            $payment_no = $payment->payment_no;
            $data = $request->all();
            if(!$payment){
                return response()->json([
                    'status' => 'error',
                    'errors' => "Cannot find Payment"
                ], 404);
            }
            if(!empty($signature_offier)){
                $path = public_path().'/payment/'.$payment_no.'/signature_offier/';
                if (!file_exists(public_path("payment"))) {
                    mkdir(public_path("payment"), 0777, true);
                }
                if (!file_exists(public_path("payment".'/'.$payment_no))) {
                    mkdir(public_path("payment".'/'.$payment_no), 0777, true);
                }
                if (!file_exists(public_path("payment".'/'.$payment_no.'/'."signature_offier"))) {
                    mkdir(public_path("payment".'/'.$payment_no.'/'."signature_offier"), 0777, true);
                }
                if(File::makeDirectory($path, 0777, true, true)){
                    $name_file = $signature_offier->getClientOriginalName();
                    $signature_offier->move($path,$name_file);
                }else{
                    $name_file = $signature_offier->getClientOriginalName();
                    $signature_offier->move($path,$name_file);
                }
                $url = url('/payment/'.$payment_no.'/signature_offier/'.$name_file);
                // $data["signature_tgor_officer"] = $url;
                // $data['officer'] = Auth::user()->id;
                $payment->signature_tgor_officer = $url;
                $payment->officer = Auth::user()->id;
                $payment->save();
            }
            if(!empty($signature_client)){
                $path = public_path().'/payment/'.$payment_no.'/signature_client/';
                if (!file_exists(public_path("payment"))) {
                    mkdir(public_path("payment"), 0777, true);
                }
                if (!file_exists(public_path("payment".'/'.$payment_no))) {
                    mkdir(public_path("payment".'/'.$payment_no), 0777, true);
                }
                if (!file_exists(public_path("payment".'/'.$payment_no.'/'."signature_client"))) {
                    mkdir(public_path("payment".'/'.$payment_no.'/'."signature_client"), 0777, true);
                }
                if(File::makeDirectory($path, 0777, true, true)){
                    $name_file = $signature_client->getClientOriginalName();
                    $signature_client->move($path,$name_file);
                }else{
                    $name_file = $signature_client->getClientOriginalName();
                    $signature_client->move($path,$name_file);
                }
                $url = url('/payment/'.$payment_no.'/signature_client/'.$name_file);
                // $data["signature_client"] = $url;
                $payment->signature_client = $url;
                $payment->save();
            }
            $payment = $payment->update([
                "payment_mode_id" => $request->payment_mode_id,
                "remarks"         => $request->remarks,
                "cheque"          => !empty($request->cheque) ? $request->cheque : null,
                "cheque_bank"     => !empty($request->cheque_bank) ? $request->cheque_bank : null,
                "transaction"     => !empty($request->transaction) ? $request->transaction : null,
            ]);
            if($payment){
                $payment = Payment::find($id);
                return response()->json([
                    'status'    => 'Successfully Updated Payment',
                    'data'      =>  $payment
                ]);
            }
            
        }else{
            return response()->json([
                'status' => 'error',
                'errors' => "Something bad happened, please try later"
            ], 422);
        }
    }

/**
 * @OA\Get(
 *     tags={"Payment"},
 *     path="/api/payment",
 *     summary="Get list Payment",
 *     operationId="listPayment",
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
 *         description="Filter",
 *         @OA\Schema(
 *             type="string"
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


    public function listPayment(Request $request){
        $limit = intval($request->query('limit'));
        $filter = json_decode($request->query('filter'));
        $payment = Payment::whereNull('deleted_at')->with('payment_line_item', 'client', 'payment_mode')->orderBy('id', 'DESC');
        $type_1 = "d/m/Y";
        $type_2 = "d/m";
        $type_expectations_1 = "Y-m-d";
        $type_expectations_2 = "m-d";
        if (!empty($filter->all)){
            $key_word = $filter->all;
            $key_word = BookingController::custom_date($type_1, $type_2, $key_word, $type_expectations_1, $type_expectations_2);
            $payment->where(function ($query) use ($key_word) {
                $query->where('payment_no', 'like', '%'.$key_word.'%')
                ->orWhere('payment_date', 'like', '%'.$key_word.'%')
                ->orWhere('total_amount', 'like', '%'.$key_word.'%')
                ->orWhere('total_tax_amount', 'like', '%'.$key_word.'%')
                ->orWhere('total', 'like', '%'.$key_word.'%')
                ->orWhere('remarks', 'like', '%'.$key_word.'%')
                ->orWhereHas('payment_mode', function ($query) use ($key_word){
                    $query->where(
                        [
                            ['reference_type', '=', 'payment_mode'],
                            ['reference_value_text', 'like', '%'.$key_word.'%'],
                        ]
                    );
                })
                ->orWhereHas('client', function($query) use ($key_word){
                    $query->where('display_name', 'like', '%'.$key_word.'%');
                });
            });
        }
        if (!empty($filter->payment_no)){
            $key_word = $filter->payment_no;
            $payment->where(function ($query) use ($key_word) {
                $query->where('payment_no', 'like', '%'.$key_word.'%');
            });
        }
        if (!empty($filter->date)){
            $key_word = $filter->date;
            $key_word = BookingController::custom_date($type_1, $type_2, $key_word, $type_expectations_1, $type_expectations_2);
            $payment->where(function ($query) use ($key_word) {
                $query->where('payment_date', 'like', '%'.$key_word.'%');
            });
        }
        if (!empty($filter->amount)){
            $key_word = $filter->amount;
            $payment->where(function ($query) use ($key_word) {
                $query->where('total_amount', 'like', '%'.$key_word.'%');
            });
        }
        if (!empty($filter->remarks)){
            $key_word = $filter->remarks;
            $payment->where(function ($query) use ($key_word) {
                $query->where('remarks', 'like', '%'.$key_word.'%');
            });
        }
        if (!empty($filter->gst)){
            $key_word = $filter->gst;
            $payment->where(function ($query) use ($key_word) {
                $query->where('total_tax_amount', 'like', '%'.$key_word.'%');
            });
        }
        if (!empty($filter->total)){
            $key_word = $filter->total;
            $payment->where(function ($query) use ($key_word) {
                $query->where('total', 'like', '%'.$key_word.'%');
            });
        }
        if (!empty($filter->clients_name)){
            $key_word = $filter->clients_name;
            $payment->whereHas('client', function ($query) use ($key_word) {
                $query->where('display_name', 'like', '%'.$key_word.'%');
            });
        }
        $payment = $payment->paginate($limit)->toArray();
        return response()->json([
            'status' => 'success',
            'data'  =>  $payment
        ], 200);
    }

/**
 * @OA\Get(
 *     tags={"Payment"},
 *     path="/api/payment/{id}",
 *     summary="Get detail Payment",
 *     operationId="getDetailPayment",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID Payment",
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

    public function getDetailPayment($id){
        if($id){
            $payment = Payment::where('id', $id)->with(['client' => function($query){
                $query->with('preferredContactBy');
                $query->select('id', 'display_name', 'phone', 'email', 'preferred_contact_by_id');
            }])
            ->with(['payment_line_item' => function($query){
                $query->with(['line_item' => function($q){
                    $q->select('id', 'sale_agreement_id', 'line_item_id', 'is_payment');
                    $q->with(['sale_agreement_line_item' => function($q1){
                        $q1->select('id', 'sale_agreement_id', 'booking_id', 'line_item_id');
                        $q1->with(['booking_line_item' => function($q2){
                            $q2->select('id', 'booking_type_id', 'service_id', 'remarks', 'amount','discount', 'tax_amount', 'lease_start_date', 'lease_expiry_date', 'event_id', 'room_type', 'check_in_date', 'check_out_date', 'check_in_time', 'check_out_time');
                            $q2->with('booking_type', 'serviceType', 'event', 'getDiscount', 'room_type');
                            $q2->with(['niche' => function($q3){
                                $q3->select('id', 'reference_no', 'bay', 'wing', 'floor', 'block', 'level', 'unit', 'type_id', 'category_id');
                                $q3->with('category', 'type');
                            }]);
                            $q2->with(['room' => function($q3){
                                $q3->select('id', 'room_no', 'price_daily', 'price_hourly');
                            }]);
                            $q2->with(['other' => function($q3){
                                $q3->select('id', 'service_name');
                            }]);
                        }]);
                    }]);
                }])
                ->with(['invoice' => function($query){
                    $query->select('id', 'invoice_no');
                }]);
            }])
            ->with('payment_mode', 'admin', 'partial_payments')
            ->with(['invoice' => function($query){
                $query->select('id', 'invoice_no', 'sale_agreement_id');
                $query->with(['sale_agreement' => function($q){
                    $q->select('id', 'sale_agreement_type','gst_id');
                    $q->with("sale_agreement_type");
                }]);
            }])
            ->first();
            // dd($payment->toArray());
            return response()->json([
                'status' => 'success',
                'data'  =>  $payment
            ], 200);
        }
        else{
            return response()->json(
            [
                'status' => 'error',
                'errors' => 'Cannot find id'
            ], 404);
        }
    }

/**
 * @OA\Delete(
 *     tags={"Payment"},
 *     path="/api/payment",
 *     summary="Delete Payment",
 *     operationId="deletePayment",
 *      @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType="application/x-www-form-urlencoded",
 *             @OA\Schema(
 *                 type="object",
 *                 @OA\Property(
 *                     property="ids",
 *                     description="ID Payment",
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

    public function deletePayment(Request $request)
    {
        $ids = $request->ids;
        if(is_string($ids)){
            $ids = json_decode($ids);
        }
        $payment = Payment::whereIn('id', $ids)->delete();

        if($payment) {

            PaymentLineItem::whereIn('payment_id',$ids)->delete();

            return response()->json(
                [
                    'status' => 'Successfully Deleted Payment',
                ], 200);
        }
        return response()->json(
            [
                'status' => 'error',
                'errors' => 'Cannot find payment'
            ], 404);
    }

    /**
 * @OA\Get(
 *     tags={"Payment"},
 *     path="/api/make-payment/{id}",
 *     summary="Make Payment PDF",
 *     operationId="exportPayment",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID Payment",
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


    public function exportPayment($id){
        
        if($id){
            $payment = Payment::where('id', $id)->with(['client' => function($query){
                $query->with('salutation');
            }])->with('payment_mode', 'invoice', 'client')->first();
            if(empty($payment->payment_mode_id)){
                return response()->json(
                    [
                        'status' => 'error',
                        'errors' => 'Cannot find Payment Mode'
                    ], 422);
            }

            $salutation = Reference::find($payment->client->salutation);
            $numberToWords = new NumberToWords();
            $numberTransformer = $numberToWords->getNumberTransformer('en');
            
            $payment["total_string"] = $numberTransformer->toWords($payment->total);
            $payment["total_amount"] = number_format($payment->total, 2, '.', ',');
            $payment["payment_date"] =  date("d/m/Y", strtotime($payment->payment_date));
            if(!empty($salutation)){
                $payment["salutation"] = $salutation->reference_value_text;
            }
            $logo = url('/images/logo_tgor.png');
            $payment->toArray();
            $html = view('exports.payment', compact('logo','payment'))->render();
            $pdf = App::make('dompdf.wrapper');
            $pdf = PDF::loadHTML($html);  
            return $pdf->stream();   
        }
        else{
            return response()->json(
            [
                'status' => 'error',
                'errors' => 'Cannot find payment'
            ], 404);
        }
    }

/**
* @OA\Get(
*     tags={"Payment"},
*     path="/api/get-donation/{id}",
*     summary="Get List Donation",
*     operationId="getDonation",
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
*         description="Filter",
*         @OA\Schema(
*             type="string"
*         )
*     ),
*     @OA\Parameter(
*         name="id",
*         in="path",
*         required=true,
*         description="User Id",
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
    public function getDonation(Request $request, $user_id){
        $limit = intval($request->query('limit'));
        $filter = json_decode($request->query('filter'));
        ///
        $type_1 = "d/m/Y";
        $type_2 = "d/m";
        $type_expectations_1 = "Y-m-d";
        $type_expectations_2 = "m-d";
        ///
        $list_donation = Payment::where('user_id', $user_id) ->where('is_donate', 1);
        if(!empty($filter->all)){
            $key_word = $filter->all;
            $key_word = BookingController::custom_date($type_1, $type_2, $key_word, $type_expectations_1, $type_expectations_2);
            $list_donation->where(function ($query) use ($key_word) {
                $query->whereHas('payment_mode', function (Builder $query) use ($key_word) {
                    $query->where(
                        [
                            ['reference_type', '=', 'payment_mode'],
                            ['reference_value_text', 'like', '%'.$key_word.'%'],
                        ]
                    );
                })
                // ->orWhereHas('client', function(Builder $query) use ($key_word){
                //             $query->where('display_name', 'like', '%'.$key_word.'%');
                // })
                ->orWhere('total','like', $key_word."%")
                ->orWhere('payment_no',     'like', "%".$key_word."%")
                ->orWhereDate('payment_date', 'like', '%'.$key_word.'%')
                ->orWhere('remarks', 'like', '%'.$key_word."%");
            });
        }
        if(!empty($filter->payment_no)){
            $list_donation->where('payment_no', 'like', '%'.$filter->payment_no."%");
        }
        if(!empty($filter->payment_date)){
            $payment_date = BookingController::custom_date($type_1, $type_2, $filter->payment_date, $type_expectations_1, $type_expectations_2);
            $list_donation->whereDate('payment_date', 'like', '%'.$payment_date."%");
        }
        // if(!empty($filter->clients_name)){
        //     $clients_name = $filter->clients_name;
        //     $list_donation->whereHas('client', function (Builder $query) use ($clients_name) {
        //         $query->where('display_name', 'like', '%'.$clients_name.'%');
        //     });
        // }
        if(!empty($filter->payment_mode)){
            $payment_mode = $filter->payment_mode;
            $list_donation->whereHas('payment_mode', function (Builder $query) use ($payment_mode) {
                $query->where([['reference_type','like','payment_mode'],['reference_value_text','like','%'.$payment_mode.'%']]);
            });
        }
        if(!empty($filter->remarks)){
            $list_donation->where('remarks', 'like', '%'.$filter->remarks."%");
        }
        if(!empty($filter->total)){
            $list_donation->where('total', 'like', $filter->total."%");
        }
        $list_donation = $list_donation->with(['client' => function($query){
                        $query->select('id', 'display_name', 'phone', 'email');
                    }])
                    ->with('payment_mode')
                    ->paginate($limit)
                    ->toArray();
        return response()->json([
            'status' => 'success',
            'data'   => $list_donation
        ]);
    }

        /**
 * @OA\Get(
 *     tags={"Payment"},
 *     path="/api/send-payment/{id}",
 *     summary="Send Document Payment",
 *     operationId="sendEmailPayment",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID Payment",
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


    public function sendEmailPayment($id){
        $payment = Payment::find($id);
        if($payment){
            $payment->with(['client' => function($query){
                $query->with('salutation');
            }, 'payment_mode', 'invoice'])->first();
            if(empty($payment->payment_mode_id)){
                return response()->json(
                    [
                        'status' => 'error',
                        'errors' => 'Cannot find Payment Mode'
                    ], 422);
            }

            $salutation = Reference::find($payment->client->salutation);
            $numberToWords = new NumberToWords();
            $numberTransformer = $numberToWords->getNumberTransformer('en');
            
            $payment["total_string"] = $numberTransformer->toWords($payment->total);
            $payment["total"] = number_format($payment->total, 2, '.', ',');
            $payment["payment_date"] =  date("d/m/Y", strtotime($payment->payment_date));
            if(!empty($salutation)){
                $payment["salutation"] = $salutation->reference_value_text;
            }
            $logo = url('/images/logo_tgor.png');
            $payment->toArray();
            // return view('exports.payment', compact('logo', 'payment'));
            // $pdf = PDF::loadView('exports.payment', compact('payment', 'logo'))->setPaper('a4', 'portrait')->setWarnings(false);
            // return $pdf->download('payment.pdf');

            $html = view('exports.payment', compact('logo','payment'))->render();
            $pdf = App::make('dompdf.wrapper');
            $pdf = PDF::loadHTML($html);
            Storage::disk('public')->put('payment.pdf', $pdf->output());
            $contents = storage_path('app/public/payment.pdf');
            $client = $payment->client;
            dispatch(new SendEmailToClient($contents, 'The Garden of Remembrance – Payment Receipt', $client, null, 'payment'));
            return response()->json([
                'status' => "Successfully Sended Email To Client"
            ]);
        }
        else{
            return response()->json(
            [
                'status' => 'error',
                'errors' => 'Cannot find payment'
            ], 404);
        }
    }
}