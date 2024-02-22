<?php

namespace App\Http\Controllers;

use App\PartialPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Reference;
use NumberToWords\NumberToWords;
use App\Jobs\SendEmailToClient;
use PDF;
use App;
use App\Booking;
use App\Invoice;
use File;

class PartialPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function createPartialPayment(Request $request)
    {
        $v = Validator::make($request->all(), [
            'payment_id'        => 'required',
            'user_id'           => 'required',
            'amount'            => 'required',
            'payment_mode_id'   => 'required'
        ]);

        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()->first()
            ], 422);
        }
        // echo json_encode($request->all());die;
        $detail_invoice = Invoice::select('invoices.*','sale_agreements.booking_id')
        ->join('sale_agreements','sale_agreements.id','=','invoices.sale_agreement_id')
        ->join('payment','payment.invoice_id','=','invoices.id')
        ->whereNull('sale_agreements.deleted_at')
        ->whereNull('payment.deleted_at')
        ->where('payment.id','=',$request->payment_id)
        ->first();
        if(!$detail_invoice){
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()->first()
            ], 422);
        }
        $status_full_paid = Reference::where('reference_type', 'booking_status')->where('reference_value_text', 'Fully Paid')->first();
        $status_partially_paid = Reference::where('reference_type', 'booking_status')->where('reference_value_text', 'Partially Paid')->first();
        
        $booking = Booking::where('id', $detail_invoice->booking_id)->first();

        $total = $detail_invoice->total;

        $partial_payment = PartialPayment::create($request->all());

        $list_partial_payment = PartialPayment::select(\DB::raw('SUM(amount) as total'))->where('payment_id','=',$request->payment_id)->first();
        
        if($list_partial_payment->total >= $total){
            $booking->status = $status_full_paid->id;
        }else{
            $booking->status = $status_partially_paid->id;
        }
        $booking->save();

        if($partial_payment){
            // var_dump($request->all());exit;
            $signature_offier = $request->file('signature_offier');
            $signature_client = $request->file('signature_client');
            $data = $request->all();
            if(!empty($signature_offier)){
                $path = public_path().'/partial_payment/'.$partial_payment->id.'/signature_offier/';
                if (!file_exists(public_path("partial_payment"))) {
                    mkdir(public_path("partial_payment"), 0777, true);
                }
                if (!file_exists(public_path("partial_payment".'/'.$partial_payment->id))) {
                    mkdir(public_path("partial_payment".'/'.$partial_payment->id), 0777, true);
                }
                if (!file_exists(public_path("partial_payment".'/'.$partial_payment->id.'/'."signature_offier"))) {
                    mkdir(public_path("partial_payment".'/'.$partial_payment->id.'/'."signature_offier"), 0777, true);
                }
                if(File::makeDirectory($path, 0777, true, true)){
                    $name_file = $signature_offier->getClientOriginalName();
                    $signature_offier->move($path,$name_file);
                }else{
                    $name_file = $signature_offier->getClientOriginalName();
                    $signature_offier->move($path,$name_file);
                }
                $url = url('/partial_payment/'.$partial_payment->id.'/signature_offier/'.$name_file);
                // $data["signature_tgor_officer"] = $url;
                // $data['officer'] = Auth::user()->id;
                $partial_payment->signature_tgor_officer = $url;
                $partial_payment->officer = Auth::user()->id;
                $partial_payment->save();
            }
            if(!empty($signature_client)){
                $path = public_path().'/partial_payment/'.$partial_payment->id.'/signature_client/';
                if (!file_exists(public_path("partial_payment"))) {
                    mkdir(public_path("partial_payment"), 0777, true);
                }
                if (!file_exists(public_path("partial_payment".'/'.$partial_payment->id))) {
                    mkdir(public_path("partial_payment".'/'.$partial_payment->id), 0777, true);
                }
                if (!file_exists(public_path("partial_payment".'/'.$partial_payment->id.'/'."signature_client"))) {
                    mkdir(public_path("partial_payment".'/'.$partial_payment->id.'/'."signature_client"), 0777, true);
                }
                if(File::makeDirectory($path, 0777, true, true)){
                    $name_file = $signature_client->getClientOriginalName();
                    $signature_client->move($path,$name_file);
                }else{
                    $name_file = $signature_client->getClientOriginalName();
                    $signature_client->move($path,$name_file);
                }
                $url = url('/partial_payment/'.$partial_payment->id.'/signature_client/'.$name_file);
                // $data["signature_client"] = $url;
                $partial_payment->signature_client = $url;
                $partial_payment->save();
            }
            if($partial_payment){
                return response()->json([
                    'status'    => 'Successfully Created Partial Payment',
                    'data'      =>  $partial_payment
                ]);
            }
        }
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\PartialPayment  $partialPayment
     * @return \Illuminate\Http\Response
     */
    public function show($id, PartialPayment $partialPayment)
    {
        $partial_payment = $partialPayment->where('id',$id)
        ->with(['client' => function($query){
            $query->with('preferredContactBy');
            $query->select('id', 'display_name', 'phone', 'email', 'preferred_contact_by_id');
        }])
        ->with('payment_mode', 'admin')
        ->first();
        return response()->json([
            'status' => 'success',
            'data'  =>  $partial_payment
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PartialPayment  $partialPayment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if($id){
            $v = Validator::make($request->all(), [
                'user_id'           => 'required',
                'amount'            => 'required',
                'payment_mode_id'   => 'required'
            ]);
    
            if ($v->fails())
            {
                return response()->json([
                    'status' => 'error',
                    'errors' => $v->errors()->first()
                ], 422);
            }
            $partial_payment = PartialPayment::find($id);
            if($partial_payment){
                $signature_client = $request->file('signature_client');
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
                    $partial_payment->signature_client = $url;
                    $partial_payment->save();
                }
                $partial_payment->update([
                    "user_id"         => $request->user_id,
                    "payment_mode_id" => $request->payment_mode_id,
                    "amount"          => $request->amount,
                    "remarks"         => $request->remarks,
                    "cheque"          => !empty($request->cheque) ? $request->cheque : null,
                    "cheque_bank"     => !empty($request->cheque_bank) ? $request->cheque_bank : null,
                    "transaction"     => !empty($request->transaction) ? $request->transaction : null,
                ]);
                return response()->json([
                    'status'    => 'Successfully Updated Partial Payment',
                    'data'      =>  $partial_payment
                ]);
            }
        }
        return response()->json(
        [
            'status' => 'error',
            'errors' => 'Cannot Find Partial Payment.'
        ], 404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PartialPayment  $partialPayment
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $partial_payment = PartialPayment::where('id', $id)->delete();

        if($partial_payment) {
            return response()->json(
                [
                    'status' => 'Successfully Deleted Partial Payment',
                ], 200);
        }
        return response()->json(
            [
                'status' => 'error',
                'errors' => 'Cannot Find Partial Payment.'
            ], 404);
    }

    public function sendEmailPartialPayment($id){
        if($id){
            $payment_partial = PartialPayment::where('id', $id)->with(['client' => function($query){
                $query->with('salutation');
            }, 'payment_mode', 'payment.invoice'])->first();
            if(empty($payment_partial->payment_mode_id)){
                return response()->json(
                    [
                        'status' => 'error',
                        'errors' => 'Cannot find Payment Mode'
                    ], 422);
            }

            $salutation = Reference::find($payment_partial->client->salutation);
            $numberToWords = new NumberToWords();
            $numberTransformer = $numberToWords->getNumberTransformer('en');
            
            $payment_partial["total_string"] = $numberTransformer->toWords($payment_partial->amount);
            $payment_partial["total"] = number_format($payment_partial->amount, 2, '.', ',');
            $payment_partial["payment_date"] =  date("d/m/Y", strtotime($payment_partial->created_at));
            $amount_partial = 0;
            if(!empty($payment_partial->payment->partial_amount)){
                foreach($payment_partial->payment->partial_amount as $key=>$value){
                    $amount_partial += floatval($value);
                }
            }
            $payment_partial["amount_payable"] = number_format(($payment_partial->payment->total - $amount_partial), 2, '.', ',');
            $payment_partial["amount_payable_string"] = $numberTransformer->toWords($amount_partial);
            if(!empty($salutation)){
                $payment_partial["salutation"] = $salutation->reference_value_text;
            }
            $logo = url('/images/logo_tgor.png');
            $payment_partial->toArray();
            // return view('exports.payment', compact('logo', 'payment'));
            // $pdf = PDF::loadView('exports.payment', compact('payment', 'logo'))->setPaper('a4', 'portrait')->setWarnings(false);
            // return $pdf->download('payment.pdf');

            $html = view('exports.partial_payment', compact('logo','payment_partial'))->render();
            // return $html;
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
                'errors' => 'Cannot Find Partial Payment.'
            ], 404);
        }
    }
    public function printPartialPayment($id){
        if($id){
            $payment_partial = PartialPayment::where('id', $id)->with(['client' => function($query){
                $query->with('salutation');
            }, 'payment_mode', 'payment.invoice'])->first();
            if(empty($payment_partial->payment_mode_id)){
                return response()->json(
                    [
                        'status' => 'error',
                        'errors' => 'Cannot find Payment Mode'
                    ], 422);
            }

            $salutation = Reference::find($payment_partial->client->salutation);
            $numberToWords = new NumberToWords();
            $numberTransformer = $numberToWords->getNumberTransformer('en');
            
            $payment_partial["total_string"] = $numberTransformer->toWords($payment_partial->amount);
            $payment_partial["total"] = number_format($payment_partial->amount, 2, '.', ',');
            $payment_partial["payment_date"] =  date("d/m/Y", strtotime($payment_partial->created_at));
            $amount_partial = 0;
            if(!empty($payment_partial->payment->partial_amount)){
                foreach($payment_partial->payment->partial_amount as $key=>$value){
                    $amount_partial += floatval($value);
                }
            }
            $payment_partial["amount_payable"] = number_format(($payment_partial->payment->total - $amount_partial), 2, '.', ',');
            $payment_partial["amount_payable_string"] = $numberTransformer->toWords($amount_partial);
            if(!empty($salutation)){
                $payment_partial["salutation"] = $salutation->reference_value_text;
            }
            $logo = url('/images/logo_tgor.png');
            $payment_partial->toArray();
            // return view('exports.payment', compact('logo', 'payment'));
            // $pdf = PDF::loadView('exports.payment', compact('payment', 'logo'))->setPaper('a4', 'portrait')->setWarnings(false);
            // return $pdf->download('payment.pdf');

            $html = view('exports.partial_payment', compact('logo','payment_partial'))->render();
            $pdf = App::make('dompdf.wrapper');
            $pdf = PDF::loadHTML($html);  
            return $pdf->stream();   
        }
        else{
            return response()->json(
            [
                'status' => 'error',
                'errors' => 'Cannot Find Partial Payment.'
            ], 404);
        }
    }
}
