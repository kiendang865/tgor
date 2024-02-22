<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SaleAgreement;
use App\SaleAgreementLineItem;
use App\Invoice;
use App\InvoiceLineItem;
use App\Booking;
use App\BookingLineItems;
use App\Reference;
use App\Payment;
use App\PaymentLineItem;
use Illuminate\Database\Eloquent\Builder;
use File;
use App;
use PDF;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Auth;
use App\Discount;
use App\GSTRate;
use App\Niche;
use App\Jobs\SendEmailToClient;
use Illuminate\Support\Facades\Storage;

class InvoicesController extends Controller
{
    /**
     * @OA\Post(
     *     tags={"Invoices"},
     *     path="/api/invoices",
     *     summary="Create Invoices",
     *     operationId="createInvoices",
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         required=true,
     *         description="ID Sale Agreement",
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
     *         name="service_ids",
     *         in="query",
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
    public function createInvoices(Request $request)
    {
        if ($request->id) {
            $now = now();
            $booking = Booking::where('id', $request->booking_id)->first();
            $invoices_no = 1;
            $id_invoice = Invoice::select('id')->orderBy('id', "DESC")->first();
            if ($id_invoice) {
                $invoices_no = (int)$id_invoice->id + 1;
            }
            $number_invoice_no = str_pad($invoices_no, 4, '0', STR_PAD_LEFT);
            $year_month = Carbon::now()->format('ym');
            $check_invoice = Invoice::where("sale_agreement_id", $request->id)->first();
            if (!empty($booking)) {
                if (!empty($check_invoice)) {
                    $invoices = Invoice::updateOrCreate(
                        ['sale_agreement_id'   =>  $request->id],
                        [
                            'invoice_date'        =>  $now->toDateTimeString(),
                            'user_id'             =>  $booking->user_id,
                            'total_amount'        =>  $request->total_amount,
                            'total_tax_amount'    =>  $request->total_tax_amount,
                            'total'               =>  $request->total,
                        ]
                    );
                } else {
                    $invoices = Invoice::updateOrCreate(
                        ['sale_agreement_id'   =>  $request->id],
                        [
                            'invoice_no'          =>  "CCPL-" . $year_month . '-' . $number_invoice_no,
                            'invoice_date'        =>  $now->toDateTimeString(),
                            'user_id'             =>  $booking->user_id,
                            'total_amount'        =>  $request->total_amount,
                            'total_tax_amount'    =>  $request->total_tax_amount,
                            'total'               =>  $request->total,
                        ]
                    );
                }
                $status_full_paid = Reference::where('reference_type', 'booking_status')->where('reference_value_text', 'Fully Invoiced')->first();
                $status_partially_paid = Reference::where('reference_type', 'booking_status')->where('reference_value_text', 'Partially Invoiced')->first();
                $status_invoice_generated = Reference::where('reference_type', 'booking_status')->where('reference_value_text', 'Invoice Generated')->first();
                if ($invoices) {
                    $sale_line_items = $request->service_ids;
                    if (is_string($sale_line_items)) {
                        $sale_line_items = json_decode($sale_line_items);
                    }
                    $sale_agreement_item = SaleAgreementLineItem::where('sale_agreement_id', $request->id)->where('isInvoice', '<>', 1)->pluck('id')->toArray();
                    $check_type_invoice = array_diff($sale_agreement_item, $sale_line_items);
                    // if (count($check_type_invoice) != 0) {
                    //     $booking->update(['status' => $status_partially_paid->id]);
                    // } else {
                    //     $booking->update(['status' => $status_full_paid->id]);
                    // }
                    $booking->update(['status' => $status_invoice_generated->id]);
                    $booking->update(["is_invoice" => 1]);
                    if ($check_invoice) {
                        InvoiceLineItem::where('invoice_id', $check_invoice->id)->delete();
                    }
                    foreach ($sale_line_items as $value) {

                        $invoices_line_item = InvoiceLineItem::create([
                            'invoice_id'            =>  $invoices->id,
                            'sale_agreement_id'     =>  $request->id,
                            'line_item_id'          =>  $value
                        ]);
                        SaleAgreementLineItem::where('id', $value)->update(['isInvoice' => 1]);
                    }
                    if (!empty($request->discount_id)) {
                        $sale_agreement = SaleAgreement::where('booking_id', '=', $booking->id)->first();
                        if ($sale_agreement->discount_id != null) {
                            if ($sale_agreement->discount_id != $request->discount_id) {
                                $sale_agreement->discount_id = $request->discount_id;
                                $sale_agreement->save();
                                BookingLineItems::where('booking_id', '=', $booking->id)->update(['discount' => $request->discount_id]);
                            }
                        } else {
                            $sale_agreement->discount_id = $request->discount_id;
                            $sale_agreement->save();
                            BookingLineItems::where('booking_id', '=', $booking->id)->update(['discount' => $request->discount_id]);
                        }
                    }
                }
            } else {
                return response()->json([
                    'status' => 'error',
                    'errors' => "Something bad happened, please try later"
                ], 422);
            }
            return response()->json([
                'status' => 'Generate Invoice Successfully.',
                'data'   => $invoices
            ], 200);
        } else {
            return response()->json(
                [
                    'status' => 'error',
                    'errors' => 'Cannot find id'
                ],
                404
            );
        }
    }


    /**
     * @OA\Get(
     *     tags={"Invoices"},
     *     path="/api/invoices",
     *     summary="Get list Invoices",
     *     operationId="listInvoice",
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

    public function listInvoice(Request $request)
    {
        $limit = intval($request->query('limit'));
        $filter = json_decode($request->query('filter'));
        $invoices = Invoice::whereNull('deleted_at')
            ->with(['invoice_line_item' => function ($query) {
                $query->with('sale_agreement_line_item.booking_line_item');
            }])
            ->with('payment')
            // ->with(['payment' => function($query){
            //     $query->select('id', 'invoice_no');
            // }])
            ->with(['client' => function ($query) {
                $query->select('id', 'display_name');
            }])->orderBy('id', 'DESC');
        ///
        $type_1 = "d/m/Y";
        $type_2 = "d/m";
        $type_expectations_1 = "Y-m-d";
        $type_expectations_2 = "m-d";
        ///
        if (!empty($filter->all)) {
            $key_word = $filter->all;
            $key_word = BookingController::custom_date($type_1, $type_2, $key_word, $type_expectations_1, $type_expectations_2);
            $invoices->where(function ($query) use ($key_word) {
                $query->where('invoice_no', 'like', '%' . $key_word . '%')
                    ->orWhere('invoice_date', 'like', '%' . $key_word . '%')
                    ->orWhere('total_amount', 'like', '%' . $key_word . '%')
                    ->orWhere('total_tax_amount', 'like', '%' . $key_word . '%')
                    ->orWhere('remarks', 'like', '%' . $key_word . '%')
                    ->orWhere('total', 'like', '%' . $key_word . '%')
                    ->orWhereHas('client', function (Builder $query) use ($key_word) {
                        $query->where('display_name', 'like', '%' . $key_word . '%');
                    })
                    ->orWhereHas('payment', function (Builder $query) use ($key_word) {
                        $query->where('payment_no', 'like', '%' . $key_word . '%');
                    });
            });
        }
        if (!empty($filter->invoice_no)) {
            $key_word = $filter->invoice_no;
            $invoices->where(function ($query) use ($key_word) {
                $query->where('invoice_no', 'like', '%' . $key_word . '%');
            });
        }
        if (!empty($filter->date)) {
            $key_word = $filter->date;
            $key_word = BookingController::custom_date($type_1, $type_2, $key_word, $type_expectations_1, $type_expectations_2);
            $invoices->where(function ($query) use ($key_word) {
                $query->where('invoice_date', 'like', '%' . $key_word . '%');
            });
        }
        if (!empty($filter->amount)) {
            $key_word = $filter->amount;
            $invoices->where(function ($query) use ($key_word) {
                $query->where('total_amount', 'like', '%' . $key_word . '%');
            });
        }
        if (!empty($filter->remarks)) {
            $key_word = $filter->remarks;
            $invoices->where(function ($query) use ($key_word) {
                $query->where('remarks', 'like', '%' . $key_word . '%');
            });
        }
        if (!empty($filter->gst)) {
            $key_word = $filter->gst;
            $invoices->where(function ($query) use ($key_word) {
                $query->where('total_tax_amount', 'like', '%' . $key_word . '%');
            });
        }
        if (!empty($filter->total)) {
            $key_word = $filter->total;
            $invoices->where(function ($query) use ($key_word) {
                $query->where('total', 'like', '%' . $key_word . '%');
            });
        }
        if (!empty($filter->clients_name)) {
            $key_word = $filter->clients_name;
            $invoices->whereHas('client', function (Builder $query) use ($key_word) {
                $query->where('display_name', 'like', '%' . $key_word . '%');
            });
        }
        if (!empty($filter->payment)) {
            $key_word = $filter->payment;
            $invoices->whereHas('payment', function (Builder $query) use ($key_word) {
                $query->where('payment_no', 'like', '%' . $key_word . '%');
            });
        }
        $invoices = $invoices->paginate($limit)->toArray();
        return response()->json([
            'status' => 'success',
            'data'  =>  $invoices
        ], 200);
    }

    /**
     * @OA\Delete(
     *     tags={"Invoices"},
     *     path="/api/invoices",
     *     summary="Delete Invoices",
     *     operationId="delete",
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="ids",
     *                     description="ID Memorial Room",
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

    public function delete(Request $request)
    {
        $ids = $request->ids;
        if (is_string($ids)) {
            $ids = json_decode($ids);
        }
        if (empty($ids)) {
            return response()->json(
                [
                    'status' => 'error',
                    'errors' => 'Cannot find invoice'
                ],
                404
            );
        }
        $invoices = Invoice::whereIn('id', $ids)->get();
        foreach ($invoices as $invoice) {
            $payment_line_items = PaymentLineItem::where('invoice_id', $invoice->id)->get();
            foreach ($payment_line_items as $payment_line) {
                $payment = Payment::where('id', $payment_line->payment_id)->first();
                if ($payment) {
                    $payment->delete();
                }
                $payment_line->delete();
            }
            $invoice_line = InvoiceLineItem::where('invoice_id', $invoice->id)->get();
            foreach ($invoice_line as $line) {
                $sale_line = SaleAgreementLineItem::where('id', $line->line_item_id)->first();
                $sale_agreement = SaleAgreement::where('id', $line->sale_agreement_id)->update(['discount_id' => null, 'invoice_id' => null]);
                if ($sale_line) {
                    $id_booking = $sale_line->booking_id;
                    $status = Reference::where('reference_type', 'booking_status')->where('reference_value_text', 'Agreement')->first();
                    Booking::where('id', $id_booking)->update(['status' => $status->id]);
                    BookingLineItems::where('id', $sale_line->line_item_id)->update(['discount' => null, 'status' => $status->id]);
                    $sale_line->isInvoice = 0;
                    $sale_line->save();
                    $line->delete();
                }
            }
            $invoice->delete();
        }
        return response()->json(
            [
                'status' => 'success',
                'message' => 'Successfully Deleted Invoices'
            ],
            200
        );
    }

    /**
     * @OA\Get(
     *     tags={"Invoices"},
     *     path="/api/invoices/{id}",
     *     summary="Get detail Invoices",
     *     operationId="getDetailInvoices",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID Sale Agreement",
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

    public function getDetailInvoices($id)
    {
        if ($id) {
            // var_dump($this->listDiscount($invoices->id));exit;
            $invoices = Invoice::where('id', $id)->with(['client' => function ($query) {
                $query->with('preferred_contact_by');
                $query->select('id', 'display_name', 'phone', 'email', 'preferred_contact_by_id');
            }])
                ->with(['sale_agreement' => function ($query) {
                    $query->select('id', 'sale_agreement_no', 'booking_id', 'sale_agreement_type','gst_id');
                    $query->with("sale_agreement_type","gst_detail");
                }])
                ->with(['discount' => function ($query) {
                    $query->with('type_amount');
                }])
                ->with(['invoice_line_item' => function ($query) {
                    $query->with('sale_agreement');
                    $query->with(['sale_agreement_line_item' => function ($q) {
                        $q->with(['booking_line_item' => function ($_q) {
                            $_q->select('id', 'booking_type_id', 'service_id', 'remarks', 'amount', 'discount', 'tax_amount', 'lease_start_date', 'lease_expiry_date', 'event_id', 'room_type', 'check_in_date', 'check_out_date', 'check_in_time', 'check_out_time');
                            $_q->with('booking_type', 'serviceType', 'event', 'getDiscount', 'room_type');
                            $_q->with('getDiscount');
                            $_q->with(['niche' => function ($q1) {
                                $q1->select('id', 'reference_no', 'bay', 'wing', 'floor', 'block', 'level', 'unit', 'type_id', 'category_id');
                                $q1->with('type', 'category');
                            }]);
                            $_q->with(['room' => function ($q1) {
                                $q1->select('id', 'room_no', 'price_daily', 'price_hourly');
                            }]);
                            $_q->with(['other' => function ($q1) {
                                $q1->select('id', 'service_name');
                            }]);
                        }]);
                    }]);
                }])->with('admin')->first();
                // echo json_encode($invoices);die;
            if (isset($invoices->discount_list)) {
                $arr_id = json_decode($invoices->discount_list);
                $discount = Discount::whereIn('id', $arr_id)->with('type_amount')->get();
                $invoices->discount_list = $discount;
            }
            $is_payment = Payment::where('invoice_id', '=', $id)->count();
            if ($is_payment > 0) {
                $invoices->is_payment = true;
            } else {
                $invoices->is_payment = false;
            }
            return response()->json([
                'status' => 'success',
                'data'  =>  $invoices
            ], 200);
        } else {
            return response()->json(
                [
                    'status' => 'error',
                    'errors' => 'Cannot find id'
                ],
                404
            );
        }
    }
    /**
     * @OA\Post(
     *     tags={"Invoices"},
     *     path="/api/save-signature-invoice/{id}",
     *     summary="signature",
     *     operationId="saveSignatureInvoices",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Id Invoices",
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="discount_id",
     *         in="query",
     *         description="Discount Id",
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *    @OA\Parameter(
     *         name="discount_custom",
     *         in="query",
     *         description="Discount Custom",
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="remarks",
     *         in="path",
     *         required=false,
     *         description="Remarks",
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

    public function saveSignatureInvoices(Request $request, $id)
    {
        $signature_offier = $request->file('signature_offier');
        $signature_client = $request->file('signature_client');
        $invoices = Invoice::find($id);
        $invoices_no = $invoices->invoice_no;
        if (!empty($request->remarks) && $request->remarks != 'null') {
            $invoices->remarks = $request->remarks;
        }
        if (!empty($signature_offier)) {
            $path = public_path() . '/invoice/' . $invoices_no . '/signature_offier/';
            if (!file_exists(public_path("invoice"))) {
                mkdir(public_path("invoice"), 0777, true);
            }
            if (!file_exists(public_path("invoice" . '/' . $invoices_no))) {
                mkdir(public_path("invoice" . '/' . $invoices_no), 0777, true);
            }
            if (!file_exists(public_path("invoice" . '/' . $invoices_no . '/' . "signature_offier"))) {
                mkdir(public_path("invoice" . '/' . $invoices_no . '/' . "signature_offier"), 0777, true);
            }
            if (File::makeDirectory($path, 0777, true, true)) {
                $name_file = $signature_offier->getClientOriginalName();
                $signature_offier->move($path, $name_file);
            } else {
                $name_file = $signature_offier->getClientOriginalName();
                $signature_offier->move($path, $name_file);
            }
            $url = url('/invoice/' . $invoices_no . '/signature_offier/' . $name_file);
            $invoices->signature_tgor_officer = $url;
            $invoices->officer = Auth::user()->id;
        }

        if (!empty($signature_client)) {
            $path = public_path() . '/invoice/' . $invoices_no . '/signature_client/';
            if (!file_exists(public_path("invoice"))) {
                mkdir(public_path("invoice"), 0777, true);
            }
            if (!file_exists(public_path("invoice" . '/' . $invoices_no))) {
                mkdir(public_path("invoice" . '/' . $invoices_no), 0777, true);
            }
            if (!file_exists(public_path("invoice" . '/' . $invoices_no . '/' . "signature_client"))) {
                mkdir(public_path("invoice" . '/' . $invoices_no . '/' . "signature_client"), 0777, true);
            }
            if (File::makeDirectory($path, 0777, true, true)) {
                $name_file = $signature_client->getClientOriginalName();
                $signature_client->move($path, $name_file);
            } else {
                $name_file = $signature_client->getClientOriginalName();
                $signature_client->move($path, $name_file);
            }
            $url = url('/invoice/' . $invoices_no . '/signature_client/' . $name_file);
            $invoices->signature_client = $url;
        }
        $invoices->discount_id = $request->discount_id;
        $invoices->save();
        $invoice_line = InvoiceLineItem::where('invoice_id', $invoices->id)->with('sale_agreement_line_item')->get();
        foreach ($invoice_line as $line_item) {
            $booking_line = $line_item->sale_agreement_line_item->line_item_id;
            $booking_line_item = BookingLineItems::where('id', '=', $booking_line)->first();
            if ($booking_line_item->booking_type->reference_value_text == "Niches") {
                if ($request->discount_id) {
                    $booking_line_item->discount = $request->discount_id;
                    $booking_line_item->save();
                }
            }
        }
        if (!empty($request->discount_custom)) {
            $arr_invoice = $request->discount_custom;
            if (is_string($arr_invoice)) {
                $arr_invoice = json_decode($arr_invoice);
            }
            foreach ($arr_invoice as $item) {
                if (!empty($item->discount_amount)) {
                    $discount = Discount::where('amount', 'LIKE', $item->discount_amount)->where("is_custom", 1)->first();
                    $value_reference = Reference::where('reference_type', 'amount_type')
                        ->where('reference_value_text', 'Value')->first();
                    $sale_agreement = SaleAgreement::find($item->sale_id);
                    $booking_line = $sale_agreement->sale_agreement_item->line_item_id;
                    $booking_line_item =  BookingLineItems::where('id', '=', $booking_line)->first();
                    if (empty($discount)) {
                        $custom_discount = Discount::create([
                            'amount'        =>  $item->discount_amount,
                            'amount_type'   =>  $value_reference->id,
                            'is_custom'     =>  1
                        ]);
                        $booking_line_item->discount = $custom_discount->id;
                        $booking_line_item->save();
                    } else {
                        $booking_line_item->discount = $discount->id;
                        $booking_line_item->save();
                    }
                }
            }
        }
        if ($invoices->save()) {
            return response()->json([
                'status' => 'Saved Successfully',
                'data' => $invoices
            ], 200);
        }
    }

    /**
     * @OA\Get(
     *     tags={"Invoices"},
     *     path="/api/make-invoices/{id}",
     *     summary="Make Invoices PDF",
     *     operationId="exportInvoice",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID Invoice",
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


    public function exportInvoice(Request $request, $id)
    {
        if ($id) {
            $invoice = Invoice::where('id', $id)->with(['client' => function ($query) {
                $query->with('preferred_contact_by', 'salutation');
            }])
                ->with(['sale_agreement' => function ($query) {
                    $query->select('id', 'sale_agreement_no', 'booking_id', 'signature_tgor_officer', 'signature_client','gst_id');
                    $query->with('gst_detail');
                }])
                ->with(['admin' => function ($query) {
                    $query->select('id', 'display_name');
                }])
                ->with(['invoice_line_item' => function ($query) {
                    $query->with('sale_agreement');
                    $query->with(['sale_agreement_line_item' => function ($q) {
                        $q->with(['booking_line_item' => function ($_q) {
                            $_q->select('id', 'booking_type_id', 'service_id', 'remarks', 'amount', 'discount', 'check_in_date', 'check_out_date', 'tax_amount');
                            $_q->with('booking_type', 'booking_discount');
                            $_q->with('getDiscount');
                            $_q->with(['niche' => function ($q1) {
                                $q1->select('id', 'reference_no');
                            }]);
                            $_q->with(['room' => function ($q1) {
                                $q1->select('id', 'room_no');
                            }]);
                            $_q->with(['other' => function ($q1) {
                                $q1->select('id', 'service_name');
                            }]);
                        }]);
                    }]);
                }])->first();
            $amount_discount = 0;
            foreach ($invoice->invoice_line_item as $invoice_line) {
                $amount_discount += $invoice_line->sale_agreement_line_item->booking_line_item->discount_amount;
            }
            $invoice["amount_discount"] =  $amount_discount;
            $invoice["total_tax_amount"] = number_format($invoice->total_tax_amount, 2, '.', ',');
            $invoice["total"] = number_format(($invoice->total) - ($amount_discount), 2, '.', ',');
            $invoice["invoice_date"] =  date("d/m/Y", strtotime($invoice->invoice_date));

            // dd($invoice->toArray());
            $logo = url('/images/logo_tgor.png');
            $qr_code = url('/images/qr_code_new.png');
            $gst = @$invoice->sale_agreement->gst_detail->name;
            // return view('exports.invoices', compact('invoice', 'logo', 'qr_code','gst'));

            // $pdf = PDF::loadView('exports.invoices', compact('invoice', 'logo', 'qr_code'))->setPaper('a4', 'portrait')->setWarnings(false);
            // return $pdf->download('invoice.pdf');
            $html = view('exports.invoices', compact('logo', 'invoice', 'qr_code','gst'))->render();
            // return $html;
            $pdf = App::make('dompdf.wrapper');
            $pdf = PDF::loadHTML($html);
            // $customPaper = array(0,0,593.92,1000.24);
            // $pdf->setPaper($customPaper);
            return $pdf->download();
            return $pdf->stream();
        } else {
            return response()->json(
                [
                    'status' => 'error',
                    'errors' => 'Cannot find id'
                ],
                404
            );
        }
    }

    public static function listDiscount($id)
    {
        ///
        $discount_list = [];
        ///type_niche
        $is_niche = Reference::where([
            ['reference_type', '=', 'booking_type'],
            ['reference_value_text', '=', 'Niches'],
        ])->first();
        //
        $type_niche_single = Reference::where([
            ['reference_type', '=', 'type_niche'],
            ['reference_value_text', '=', 'Single'],
        ])->first();
        //
        $type_niche_double = Reference::where([
            ['reference_type', '=', 'type_niche'],
            ['reference_value_text', '=', 'Double'],
        ])->first();
        //
        // $type_niche_all = Reference::where([
        //     ['reference_type', '=', 'type_niche'],
        //     ['reference_value_text', '=', 'All'],
        // ])->first();
        ///category_niche
        $category_niche_standard = Reference::where([
            ['reference_type', '=', 'category_niche'],
            ['reference_value_text', '=', 'Standard'],
        ])->first();
        //
        $category_niche_premium = Reference::where([
            ['reference_type', '=', 'category_niche'],
            ['reference_value_text', '=', 'Premium'],
        ])->first();
        //
        // $category_niche_all = Reference::where([
        //     ['reference_type', '=', 'category_niche'],
        //     ['reference_value_text', '=', 'All'],
        // ])->first();
        //
        $count_standard_single = 0;
        $count_standard_double = 0;
        $count_premium_double = 0;
        $count_premium_single = 0;
        // $count_premium_all = 0;
        //
        $invoice_line_item = InvoiceLineItem::where('invoice_id', $id)->get();
        foreach ($invoice_line_item as $invoice_line) {
            $id = $invoice_line->sale_agreement_line_item->line_item_id;
            $booking_line_niche = BookingLineItems::where('id', '=', $id)->first();
            if ($booking_line_niche->booking_type_id === $is_niche->id) {
                /// Single
                if ($booking_line_niche->niche->category_id == $category_niche_standard->id) {
                    if ($booking_line_niche->niche->type_id == $type_niche_single->id) {
                        $count_standard_single++;
                        $discount = Discount::where([
                            ['category', '=', $booking_line_niche->niche->category_id],
                            ['type', '=', $booking_line_niche->niche->type_id],
                            ['minimum_qty', '<=', $count_standard_single]
                        ])->get();
                        ////
                        foreach ($discount as $item) {
                            if (!in_array($item->id, $discount_list)) {
                                array_push($discount_list, $item->id);
                            }
                        }
                    }
                    if ($booking_line_niche->niche->type_id == $type_niche_double->id) {
                        $count_standard_double++;
                        $count_standard_single += 2;

                        $discount = Discount::where([
                            ['category', '=', $booking_line_niche->niche->category_id],
                            ['type', '=', $booking_line_niche->niche->type_id],
                            ['minimum_qty', '<=', $count_standard_double]
                        ])->get();
                        foreach ($discount as $item) {
                            if (!in_array($item->id, $discount_list)) {
                                array_push($discount_list, $item->id);
                            }
                        }
                        /////
                        if ($count_standard_single >= 8) {
                            $discount_single = Discount::where([
                                ['category', '=', $booking_line_niche->niche->category_id],
                                ['type', '=', $type_niche_single->id],
                                ['minimum_qty', '<=', $count_standard_single]
                            ])->get();
                            foreach ($discount_single as $item) {
                                if (!in_array($item->id, $discount_list)) {
                                    array_push($discount_list, $item->id);
                                }
                            }
                        }
                    }
                }
                // Premium
                if ($booking_line_niche->niche->category_id == $category_niche_premium->id) {
                    if ($booking_line_niche->niche->type_id == $type_niche_single->id) {
                        $count_premium_single++;
                        $discount = Discount::where([
                            ['category', '=', $booking_line_niche->niche->category_id],
                            ['type', '=', $booking_line_niche->niche->type_id],
                            ['minimum_qty', '<=', $count_premium_single]
                        ])->get();
                        ////
                        foreach ($discount as $item) {
                            if (!in_array($item->id, $discount_list)) {
                                array_push($discount_list, $item->id);
                            }
                        }
                    }
                    if ($booking_line_niche->niche->type_id == $type_niche_double->id) {
                        $count_premium_single += 2;
                        $count_premium_double++;
                        $discount = Discount::where([
                            ['category', '=', $booking_line_niche->niche->category_id],
                            ['type', '=', $booking_line_niche->niche->type_id],
                            ['minimum_qty', '<=', $count_premium_double]
                        ])->get();
                        ////
                        foreach ($discount as $item) {
                            if (!in_array($item->id, $discount_list)) {
                                array_push($discount_list, $item->id);
                            }
                        }
                        if ($count_premium_single >= 8) {
                            $discount_single = Discount::where([
                                ['category', '=', $booking_line_niche->niche->category_id],
                                ['type', '=', $type_niche_single->id],
                                ['minimum_qty', '<=', $count_premium_single]
                            ])->get();
                            foreach ($discount_single as $item) {
                                if (!in_array($item->id, $discount_list)) {
                                    array_push($discount_list, $item->id);
                                }
                            }
                        }
                    }
                    // if($booking_line_niche->niche->type_id == $type_niche_all->id){
                    //     $count_premium_all++;
                    //     $discount = Discount::where([
                    //         ['category', '=', $booking_line_niche->niche->category_id],
                    //         ['niche_type', '=', $booking_line_niche->niche->type_id],
                    //         ['minimum_qty', '<=', $count_premium_all]
                    //     ])->get();
                    //     ////
                    //     foreach($discount as $item){
                    //         if(!empty($item)){
                    //             if(!in_array($item->id, $discount_list)){
                    //                 array_push($discount_list, $item->id);
                    //             }
                    //         }

                    //     }
                    // }
                }
            }
        }
        /// All
        // if($book_line->niche->category_id == $category_niche_all->id){
        //     $discount = Discount::where([
        //         ['category', '=', $book_line->niche->category_id],
        //     ])->get();
        //     ////
        //     foreach($discount as $item){
        //         if(!in_array($item->id, $discount_list)){
        //             array_push($discount_list, $item->id);
        //         }
        //     }
        // }

        return json_encode($discount_list);
    }

    /**
     * @OA\Get(
     *     tags={"Invoices"},
     *     path="/api/list-agreement",
     *     summary="List Sale Agreement For Invoices",
     *     operationId="getListAgreement",
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         required=true,
     *         description="ID user ",
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

    public function getListAgreement(Request $request)
    {
        if (empty($request->id)) {
            return response()->json(
                [
                    'status' => 'error',
                    'errors' => 'Cannot find user'
                ],
                422
            );
        }
        $check_service = Reference::where([
            ['reference_type', '=', 'booking_type'],
            ['reference_value_text', '=', "Additional Services"],
        ])->first();
        $sale_agreement = SaleAgreement::with(['sale_agreement_item' => function ($query) {
            $query->with(['booking_line_item' => function ($q) {
                $q->select('id', 'service_id', 'booking_type_id');
                $q->with('niche', 'room', 'other', 'booking_type');
            }]);
        }])
            ->where('user_id', '=', $request->id)
            ->whereHas('sale_agreement_item', function ($query) {
                $query->whereNotNull('line_item_id');
                $query->where('isInvoice', '=', 0);
            })
            ->get();
        return response()->json(
            [
                'status' => 'success',
                'data' => $sale_agreement
            ],
            200
        );
    }

    /**
     * @OA\Post(
     *     tags={"Invoices"},
     *     path="/api/add-agreement",
     *     summary="Add Sale Agreement For Invoices",
     *     operationId="addSaleAgreement",
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         required=true,
     *         description="ID Invoice ",
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sale_agreement_id",
     *         in="query",
     *         required=true,
     *         description="sale_agreement_id",
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

    public function addSaleAgreement(Request $request)
    {
        if (empty($request->id)) {
            return response()->json(
                [
                    'status' => 'error',
                    'errors' => 'Cannot find invoice'
                ],
                422
            );
        }
        if (empty($request->sale_agreement_id)) {
            return response()->json(
                [
                    'status' => 'error',
                    'errors' => 'Cannot find sale agreement'
                ],
                422
            );
        }
        $sale_line_item = SaleAgreementLineItem::where([['sale_agreement_id', $request->sale_agreement_id], ['isInvoice', 0]])->first();
        if (!$sale_line_item) {
            return response()->json(
                [
                    'status' => 'error',
                    'errors' => 'Sale agreement is invoice'
                ],
                422
            );
        }
        $invoices_line_item = InvoiceLineItem::create([
            'invoice_id'            =>  $request->id,
            'sale_agreement_id'     =>  $request->sale_agreement_id,
            'line_item_id'          =>  $sale_line_item->id
        ]);
        if ($invoices_line_item) {
            $check_discount = Discount::all();
            if (empty($check_discount)) {
                $list_discount = null;
            } else {
                $list_discount = $this->listDiscount($request->id);
            }
            $invoice = Invoice::where('id', $request->id)->first();
            $invoice->discount_list = $list_discount;
            $invoice->total_amount += $sale_line_item->sale_agreement->total_amount;
            $invoice->total_tax_amount += $sale_line_item->booking_line_item->tax_amount;
            $invoice->total += $sale_line_item->sale_agreement->total;
            $invoice->save();
            SaleAgreement::where('id', $request->sale_agreement_id)->update(['invoice_id' => $request->id]);
            SaleAgreementLineItem::where('id', $sale_line_item->id)->update(['isInvoice' => 1]);
            $status_full_paid = Reference::where('reference_type', 'booking_status')->where('reference_value_text', 'Fully Invoiced')->first();
            BookingLineItems::where('id', $sale_line_item->booking_line_item->id)->update(["status" => $status_full_paid->id]);
            return response()->json(
                [
                    'status' => 'Successfully Added Sale Agreement.',
                ],
                200
            );
        }
        return response()->json(
            [
                'status' => 'error',
                'message' => "Can't Added Sale Agreement.",
            ],
            422
        );
    }

    /**
     * @OA\Delete(
     *     tags={"Invoices"},
     *     path="/api/delete-agreement-line",
     *     summary="Delete Sale Agreement For Invoices",
     *     operationId="deleteSaleAgreement",
     *     @OA\Parameter(
     *         name="ids",
     *         in="query",
     *         required=true,
     *         description="ID Invoice Line Item",
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

    public function deleteSaleAgreement(Request $request)
    {
        $ids = $request->ids;
        if (is_string($ids)) {
            $ids = json_decode($ids);
        }
        $id_sale_line = InvoiceLineItem::whereIn('id', $ids)->get();
        // $delete_sale_agreement = InvoiceLineItem::whereIn('id', $ids)->delete();
        if ($id_sale_line) {
            $status_agreement = Reference::where('reference_type', 'booking_status')->where('reference_value_text', 'Agreement')->first();
            $partially_invoiced = Reference::where('reference_type', 'booking_status')->where('reference_value_text', 'Partially Invoiced')->first();
            foreach ($id_sale_line as  $value) {
                $sla = SaleAgreement::where("id", $value->sale_agreement_id)->first();
                $inv = Invoice::where('id', $value->invoice_id)->first();
                $inv->total_amount -= $sla->total_amount;
                $inv->total_tax_amount -= $sla->total_tax_amount;
                $inv->total -= $sla->total;
                $inv->save();
                if ($inv) {
                    $sla->invoice_id = null;
                    $sla->save();
                }
                $sale_line = SaleAgreementLineItem::where("id", $value->line_item_id)->first();
                $sale_line->isInvoice = 0;
                $sale_line->save();
                $check_sale = SaleAgreement::where([['booking_id', $sla->booking_id], ['invoice_id', '=', 1]])->count();
                $check_book = BookingLineItems::where('booking_id', $sla->booking_id)->count();
                if ($check_book > $check_sale) {
                    Booking::where('id', $sla->booking_id)->update(['status' => $partially_invoiced->id]);
                }
                BookingLineItems::where('id', $sale_line->line_item_id)->update(['status' => $status_agreement->id, 'discount' => null]);
                InvoiceLineItem::where('id', $value->id)->delete();
            }
            return response()->json(
                [
                    'status' => 'Successfully Deleted',
                ],
                200
            );
        }
        return response()->json(
            [
                'status' => 'error',
                'message' => 'Cannot find Sale agreement'
            ],
            404
        );
    }


    /**
     * @OA\Post(
     *     tags={"Invoices"},
     *     path="/api/add-invoices",
     *     summary="Add Invoices",
     *     operationId="addInvoices",
     *     @OA\Parameter(
     *         name="booking_id",
     *         in="query",
     *         required=true,
     *         description="ID Booking",
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
    public function addInvoices(Request $request)
    {
        if ($request->booking_id) {
            $now = now();
            $invoice_total_amount = 0;
            $invoice_total_tax_amount = 0;
            $invoice_total = 0;
            $dmY = Carbon::now()->format('dmY');
            $booking_line = BookingLineItems::where('booking_id', $request->booking_id)->get();
            $id_sale_agreement = SaleAgreement::select('id')->orderBy('id', "DESC")->first();
            $other_type = Reference::where('reference_value_text', '=', 'Additional Services')->first();
            $booking = Booking::where('id', $request->booking_id)->first();
            if ($id_sale_agreement) {
                $sale_agreement_no = (int)$id_sale_agreement->id + 1;
            }
            $number_sale_agreement_no = str_pad($sale_agreement_no, 4, '0', STR_PAD_LEFT);
            $sale_agreement = SaleAgreement::create([
                'sale_agreement_no'     =>  'S-' . $dmY . '-' . $number_sale_agreement_no,
                'sale_agreement_date'   =>  $now->toDateTimeString(),
                'booking_id'            =>  $booking->id,
                'user_id'               =>  $booking->user_id,
                'officer'               =>  Auth::user()->id,
                'sale_agreement_type'   =>  $other_type->id,
                'is_add_invoice'        =>  1
            ]);
            foreach ($booking_line as $line) {
                if ($line->is_sale == 0) {
                    $now = now();
                    $check_booking_line = Reference::where([
                        ['reference_type', '=', 'booking_type'],
                        ['reference_value_text', '=', 'Niches'],
                    ])->first();
                    if ($line->booking_type_id == $check_booking_line->id) {
                        $check_niche = Niche::where('id', $line->service_id)->whereNotNull('booking_id')->first();
                        if (empty($line->renewal_from_id)) {
                            if (!empty($check_niche)) {
                                return response()->json([
                                    'status' => 'error',
                                    'errors' => "Niche " . $check_niche->reference_no . " has been booked"
                                ], 422);
                            }
                        }
                    }
                    $total_amount = (float)$line->amount;
                    $total_tax_amount = (float)$line->tax_amount;
                    $total = $total_amount + $total_tax_amount;
                    $line->is_sale = 1;
                    $line->save();
                    if ($sale_agreement) {
                        $sale_agreement_item = SaleAgreementLineItem::create([
                            'sale_agreement_id'     =>  $sale_agreement->id,
                            'booking_id'            =>  $booking->id,
                            'line_item_id'          =>  $line->id
                        ]);
                        $sale_agreement->update([
                            'total_amount'        =>    $total_amount,
                            'total_tax_amount'    =>    $total_tax_amount,
                            'total'               =>    $total
                        ]);
                        $invoice_total_amount += $total_amount;
                        $invoice_total_tax_amount += $total_tax_amount;
                        $invoice_total += $total;
                    }
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
                    ///
                    if ($line->booking_type_id == $check_booking_line->id) {
                        if ($line->interment_date == null) {
                            $line->status = $status_niches_unoccupied->id;
                            $line->save();
                        } else {
                            $line->status = $status_niches_occupied->id;
                            $line->save();
                        }
                        Niche::where('id', '=', $line->niche->id)->update(['booking_id' => $line->booking->id, 'booking_line_item' => $line->id, 'status' => 'Unavailable']);
                    }
                } elseif ($line->is_sale == 1) {
                    $sale_agreement = SaleAgreementLineItem::where('line_item_id', $line->id)->first();
                    if ($sale_agreement->isInvoice === 0) {
                        $invoice_total_amount += $line->amount;
                        $invoice_total_tax_amount += $line->tax_amount;
                        $invoice_total += ($line->amount + $line->tax_amount);
                    }
                }
            }
            $check_discount = Discount::all();
            $invoices_no = 1;
            $id_invoice = Invoice::select('id')->orderBy('id', "DESC")->first();
            if ($id_invoice) {
                $invoices_no = (int)$id_invoice->id + 1;
            }
            $number_invoice_no = str_pad($invoices_no, 4, '0', STR_PAD_LEFT);
            $year_month = Carbon::now()->format('ym');
            $invoices = Invoice::create([
                'invoice_no'          =>  "CCPL-" . $year_month . '-' . $number_invoice_no,
                'invoice_date'        =>  $now->toDateTimeString(),
                'user_id'             =>  $booking->user_id,
                'total_amount'        =>  $invoice_total_amount,
                'total_tax_amount'    =>  $invoice_total_tax_amount,
                'total'               =>  $invoice_total,
                'officer'             =>  Auth::user()->id,
                'sale_agreement_id'   =>  $sale_agreement->id
            ]);
            if ($invoices) {
                if (empty($check_discount)) {
                    $list_discount = null;
                } else {
                    $list_discount = $this->listDiscount($invoices->id);
                }
                Invoice::where('id', $invoices->id)->update(['discount_list' => $list_discount]);
                $sale_agreement_item = SaleAgreementLineItem::where('sale_agreement_id', $sale_agreement->id)->get();
                foreach ($sale_agreement_item as $line_item) {
                    SaleAgreement::where('id', $line_item->sale_agreement_id)->update(['invoice_id' => $invoices->id]);
                    $invoices_line_item = InvoiceLineItem::create([
                        'invoice_id'            =>  $invoices->id,
                        'sale_agreement_id'     =>  $line_item->sale_agreement_id,
                        'line_item_id'          =>  $line_item->id
                    ]);
                    if ($invoices_line_item) {
                        SaleAgreementLineItem::where('id', $line_item->id)->update(['isInvoice' => 1]);
                        $status_full_paid = Reference::where('reference_type', 'booking_status')->where('reference_value_text', 'Fully Invoiced')->first();
                        BookingLineItems::where('id', $line_item->booking_line_item->id)->update(["status" => $status_full_paid->id]);
                        Booking::where('id', $line_item->booking_line_item->booking_id)->update(["status" => $status_full_paid->id]);
                    }
                }
                Booking::where('id', $request->booking_id)->update(["is_invoice" => 1]);
            }
            return response()->json([
                'status' => 'Generate Invoice Successfully.',
                'data'   => $invoices
            ], 200);
        } else {
            return response()->json(
                [
                    'status' => 'error',
                    'errors' => 'Cannot find booking'
                ],
                404
            );
        }
    }

    /**
     * @OA\get(
     *     tags={"Invoices"},
     *     path="/api/list-booking-add-invoice",
     *     summary="List Booking for add invoice",
     *     operationId="listBooking",
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
    public function listBooking()
    {
        $status_booked = Reference::where([['reference_type', '=', 'booking_status'], ['reference_value_text', '=', 'Booked']])->first();
        $status_agreement = Reference::where([['reference_type', '=', 'booking_status'], ['reference_value_text', '=', 'Agreement']])->first();
        $status_partially = Reference::where([['reference_type', '=', 'booking_status'], ['reference_value_text', '=', 'Partially Invoiced']])->first();
        // $booking = Booking::where(function ($query) use ($key){   
        //     $query->where('status',$status_booked->id)
        //         ->orWhere('status',$status_agreement->id)
        //         ->orWhere('status',$status_partially->id)
        // })->orderBy('booking_no', 'ASC')->get();
        $niches_type = Reference::where('reference_value_text', '=', 'Niches')->first();
        $room_type = Reference::where('reference_value_text', '=', 'Memorial Rooms')->first();
        $booking = Booking::whereHas('booking_line_items', function ($query) use ($niches_type, $room_type) {
            $query->where("booking_type_id", "<>", $niches_type->id);
            $query->where("booking_type_id", "<>", $room_type->id);
        })
            ->with("booking_line_items")
            ->where('status', $status_booked->id)
            ->orWhere('status', $status_agreement->id)
            ->orWhere('status', $status_partially->id)
            ->orderBy('booking_no', 'DESC')->get();
        return response()->json([
            'status' => 'success',
            'data'   => $booking->toArray()
        ], 200);
    }


    /**
     * @OA\Get(
     *     tags={"Invoices"},
     *     path="/api/send-invoice/{id}",
     *     summary="Send Document Invoices",
     *     operationId="sendInvoiceToClient",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID Invoices",
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
    public function sendInvoiceToClient($id)
    {
        if ($id) {
            $invoice = Invoice::where('id', $id)->with(['client' => function ($query) {
                $query->with('preferred_contact_by', 'salutation');
            }])
                ->with(['sale_agreement' => function ($query) {
                    $query->select('id', 'sale_agreement_no', 'booking_id', 'signature_tgor_officer', 'signature_client');
                }])
                ->with(['admin' => function ($query) {
                    $query->select('id', 'display_name');
                }])
                ->with(['invoice_line_item' => function ($query) {
                    $query->with('sale_agreement');
                    $query->with(['sale_agreement_line_item' => function ($q) {
                        $q->with(['booking_line_item' => function ($_q) {
                            $_q->select('id', 'booking_type_id', 'service_id', 'remarks', 'amount', 'discount', 'check_in_date', 'check_out_date');
                            $_q->with('booking_type', 'booking_discount');
                            $_q->with(['niche' => function ($q1) {
                                $q1->select('id', 'reference_no');
                            }]);
                            $_q->with(['room' => function ($q1) {
                                $q1->select('id', 'room_no');
                            }]);
                            $_q->with(['other' => function ($q1) {
                                $q1->select('id', 'service_name');
                            }]);
                        }]);
                    }]);
                }])->first();
            $invoice["total_amount"] = number_format($invoice->total_amount, 2, '.', ',');
            $invoice["total_tax_amount"] = number_format($invoice->total_tax_amount, 2, '.', ',');
            $invoice["total"] = number_format($invoice->total, 2, '.', ',');
            $invoice["invoice_date"] =  date("d/m/Y", strtotime($invoice->invoice_date));
            $amount_discount = 0;
            foreach ($invoice->invoice_line_item as $invoice_line) {
                $is_discount = $invoice_line->sale_agreement->discount_id;
                $amount_discount += $invoice_line->sale_agreement_line_item->booking_line_item->discount_amount;
            }
            $invoice["amount_discount"] =  $amount_discount;
            $invoice["is_discount"] =  $is_discount;

            $logo = url('/images/logo_tgor.png');
            $qr_code = url('/images/qr_code.png');
            $invoice->toArray();
            // return view('exports.invoices', compact('invoice', 'logo', 'qr_code'));

            // $pdf = PDF::loadView('exports.invoices', compact('invoice', 'logo', 'qr_code'))->setPaper('a4', 'portrait')->setWarnings(false);
            // return $pdf->download('invoice.pdf');
            $html = view('exports.invoices', compact('logo', 'invoice', 'qr_code'))->render();
            // return $html;
            $pdf = App::make('dompdf.wrapper');
            $pdf = PDF::loadHTML($html);
            Storage::disk('public')->put('invoice.pdf', $pdf->output());
            $contents = storage_path('app/public/invoice.pdf');
            $client = $invoice->client;
            dispatch(new SendEmailToClient($contents, 'The Garden of Remembrance – Invoice Documents', $client, null, 'invoice'));
            return response()->json([
                'status' => "Successfully Sended Email To Client"
            ]);
        }
    }

    public function getTotalInvoice(Request $request)
    {
        $arr_id = $request->arr_id;
        if (is_string($arr_id)) {
            $arr_id = json_decode($arr_id);
        }
        $get_line = BookingLineItems::whereIn('id', $arr_id)->get();
        $amount = 0;
        $gst_amount = 0;
        $total = 0;
        $discount = 0;
        if (!empty($get_line)) {
            foreach ($get_line as $key => $value) {
                $amount += (float)$value['amount'];
                $gst_amount += (float)$value['tax_amount'];
                $discount += (float)$value['discount_amount'];
                $total = ($amount + $gst_amount) - $discount;
            }
        }
        Invoice::where('id', $request->invoice_id)->update([
            "total_amount" => $amount,
            "total_tax_amount" => $gst_amount,
            "total_discount" => $discount,
            "total" => $total
        ]);
    }

    public function updateGST()
    {
        //Lấy thuế hiện tại
        try {
            $now = now();
            \DB::beginTransaction();
            $gst = 0.07;
            $gst_detail = GSTRate::where(\DB::raw("DATE(gst_start_date)"), '<=', \DB::raw("DATE('".$now->format('Y-m-d')."')"))
            ->where(function ($query) use ($now) {
                $query->where('gst_end_date','>=', "'".$now->format('Y-m-d h:i:s')."'")
                    ->orWhereNull('gst_end_date');
            })
            ->orderBy('gst_start_date', 'DESC')->first();
            $gst_detail = GSTRate::orderBy('id', 'desc')->limit(1)->first();
            if ($gst_detail) {
                $gst = $gst_detail->rate;
            }
            $list_booking_line_items = BookingLineItems::select(
                'booking_line_items.id',
                'booking_line_items.tax_amount',
                'booking_line_items.amount',
                \DB::raw('if(discount.amount is NOT null , discount.amount , 0 ) as discount'),
                \DB::raw('if(discount.percent is NOT null , percent , 0 ) as percent')
            )->leftJoin('discount', 'discount.id', '=', 'booking_line_items.discount')
                // ->where('booking_line_items.id','=',4608)
                // ->whereIn('booking_line_items.id', array(4614, 4615))
                ->whereNull('booking_line_items.deleted_at')
                ->get();
            $array_id_sale_agreement_id = array();
            foreach ($list_booking_line_items as $key_booking_line_items => $value_booking_line_items) {
                
                $book_line_item = BookingLineItems::find($value_booking_line_items->id);
                
                if ($book_line_item) {
                    if($value_booking_line_items->percent){
                        $gst_of_book_line_item = ((float)$value_booking_line_items->amount -  ((float)$value_booking_line_items->amount * (float)$value_booking_line_items->percent)) * (float)$gst;
                    }else{
                        $gst_of_book_line_item = ((float)$value_booking_line_items->amount -  (float)$value_booking_line_items->discount) * (float)$gst;
                    }
                    $book_line_item->tax_amount = (float)$gst_of_book_line_item;
                    $book_line_item->save();
                    //get sale sale_agreement with booking_line_item_id
                    $detail_sale_agreement = SaleAgreementLineItem::where('line_item_id', '=', $value_booking_line_items->id)->first();
                    if ($detail_sale_agreement) {
                        $array_id_sale_agreement_id[$detail_sale_agreement->sale_agreement_id][] = $detail_sale_agreement->line_item_id;
                    }
                }
            }
            if ($array_id_sale_agreement_id) {
                foreach ($array_id_sale_agreement_id as $key_id_sale_agreement_id => $value_id_sale_agreement_id) {
                    //update sale_agreement
                    $sale_agreement = SaleAgreement::find($key_id_sale_agreement_id);
                    if ($value_id_sale_agreement_id) {
                        $value_id_sale_agreement_id = array_unique($value_id_sale_agreement_id);
                        $list_book_line_item_of_sale_agreement = BookingLineItems::select(
                            \DB::raw('SUM(tax_amount) as tax_amount_total'),
                            \DB::raw('SUM(amount) as amount_total')
                        )
                            ->whereIn('id', $value_id_sale_agreement_id)
                            ->whereNull('deleted_at')->first();
                    }
                    $sale_agreement->total_amount = $list_book_line_item_of_sale_agreement->amount_total;
                    $sale_agreement->total_tax_amount = $list_book_line_item_of_sale_agreement->tax_amount_total;
                    $sale_agreement->save();
                }
            }
            //update nvoice
            $invoice = Invoice::orderBy('id', 'desc')
                ->where('id', 1268)
                ->with(['invoice_line_item' => function ($query) {
                    $query->with(['sale_agreement_line_item' => function ($q) {
                        $q->with(['booking_line_item' => function ($_q) {
                            $_q->select('id', 'booking_type_id', 'service_id', 'remarks', 'amount', 'discount', 'check_in_date', 'check_out_date', 'tax_amount');
                            $_q->with('getDiscount');
                        }]);
                    }]);
                }])->get();
            foreach ($invoice as $key_invoice => $value_invoice) {
                $detail_invoice = Invoice::find($value_invoice->id);
                if ($value_invoice->invoice_line_item) {
                    $total_amount = (float) 0;
                    $total_tax_amount = (float) 0;
                    $total = (float) 0;
                    $total_discount = (float) 0;
                    foreach ($value_invoice->invoice_line_item as $value_invoice_line_item) {
                        $total_amount  = (float)$total_amount + (float)$value_invoice_line_item->sale_agreement_line_item->booking_line_item->amount;
                        $total_tax_amount = (float)$total_tax_amount + (float)$value_invoice_line_item->sale_agreement_line_item->booking_line_item->tax_amount;
                        $total  =  (float)$total + (float)((float)$total_amount + (float)$total_tax_amount);
                        $discount = @$value_invoice_line_item->sale_agreement_line_item->booking_line_item->get_discount->amount ? (float)$value_invoice_line_item->sale_agreement_line_item->booking_line_item->get_discount->amount : 0;
                        $total_discount = (float)$total_discount + (float)$discount;
                    }
                    $detail_invoice->total_amount = (float)$total_amount;
                    $detail_invoice->total_tax_amount = (float)$total_tax_amount;
                    $detail_invoice->total = (float)$total;
                    $detail_invoice->total_discount = (float)$total_discount;
                }
                $detail_invoice->save();
            }
            \DB::commit();
            echo 'Successfully!';
        } catch (\Exception $th) {
            \DB::rollBack();
            echo 'Something went wrong, please try again!';
            die;
        }
    }
    public function checkExitsInvoice($id)
    {
        if ($id) {
            $invoices = Invoice::where('sale_agreement_id', $id)->first();
            if($invoices){
                return response()->json([
                    'status' => 'success',
                    'data'  =>  $invoices
                ], 200);
            }
            return response()->json(
                [
                    'status' => 'error',
                    'errors' => 'Invoices not found!'
                ],
                404
            );
        }else{
            return response()->json(
                [
                    'status' => 'error',
                    'errors' => 'Cannot find id'
                ],
                404
            );
        }
    }
}
