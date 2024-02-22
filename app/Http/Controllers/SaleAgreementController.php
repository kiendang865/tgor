<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Booking;
use App\BookingLineItems;
use App\Reference;
use File;
use Illuminate\Support\Facades\Validator;
use App\SaleAgreement;
use App\Http\Controllers\BookingController;
use App\SaleAgreementLineItem;
use Illuminate\Database\Eloquent\Builder;
use App\Invoice;
use App\InvoiceLineItem;
use App\PaymentLineItem;
use App\Payment;
use App\Discount;
use App;
use PDF;
use Illuminate\Support\Facades\Auth;
use App\Niche;
use App\Jobs\SendEmailToClient;
use Illuminate\Support\Facades\Storage;
use App\GSTRate;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SaleAgreementController extends Controller
{

    /**
     * @OA\Post(
     *     tags={"Sale Agreement"},
     *     path="/api/sale-agreement",
     *     summary="Create Sale Agreement",
     *     operationId="createSaleAgreement",
     *     @OA\Parameter(
     *         name="id",
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
    // public function createSaleAgreement(Request $request)
    // {
    //     if ($request->id) {
    //         $check_sale_agreement = SaleAgreement::where('booking_id', $request->id)->whereNull('deleted_at')->first();
    //         if (!empty($check_sale_agreement)) {
    //             return response()->json([
    //                 'status' => 'error',
    //                 'errors' => "Sale Agreement has been created"
    //             ], 422);
    //         }
    //         $sale_agreement_no = 1;
    //         $id_sale_agreement = SaleAgreement::select('id')->orderBy('id', "DESC")->first();
    //         if ($id_sale_agreement) {
    //             $sale_agreement_no = (int)$id_sale_agreement->id + 1;
    //         }
    //         $now = now();
    //         $booking = Booking::where('id', $request->id)->first();
    //         $check_discount = Discount::all();
    //         $check_booking_line = BookingLineItems::whereHas('booking_type', function (Builder $query) {
    //             $query->where([
    //                 ['reference_type', '=', 'booking_type'],
    //                 ['reference_value_text', '=', 'Niches'],
    //             ]);
    //         })->where([
    //             ['booking_id', '=', $booking->id],
    //         ])->get();
    //         foreach ($check_booking_line as $check_line) {
    //             $check_niche = Niche::where('id', $check_line->service_id)->whereNotNull('booking_id')->first();
    //             if (empty($check_line->renewal_from_id)) {
    //                 if (!empty($check_niche)) {
    //                     return response()->json([
    //                         'status' => 'error',
    //                         'errors' => "Niche " . $check_niche->reference_no . " has been booked"
    //                     ], 422);
    //                 }
    //             }
    //         }
    //         if (!empty($booking)) {
    //             $status = Reference::where('reference_type', 'booking_status')->where('reference_value_text', 'Agreement')->first();
    //             $booking->update(['status' => $status->id]);
    //             $booking_line_items = BookingLineItems::where('booking_id', $booking->id)->get();
    //             if (count($booking_line_items) === 0) {
    //                 return response()->json([
    //                     'status' => 'error',
    //                     'errors' => "Something bad happened, please try later"
    //                 ], 422);
    //             }
    //             foreach ($booking_line_items as $value) {
    //                 $total_amount = 0;
    //                 $total_tax_amount = 0;
    //                 $total = 0;
    //                 $value->is_sale = 1;
    //                 $value->save();

    //                 $sale_agreement = SaleAgreement::create([
    //                     'sale_agreement_no'     =>  $value->booking_no,
    //                     'sale_agreement_date'   =>  $now->toDateTimeString(),
    //                     'booking_id'            =>  $booking->id,
    //                     'user_id'               =>  $booking->user_id,
    //                 ]);
    //                 if ($sale_agreement) {
    //                     $sale_agreement_item = SaleAgreementLineItem::create([
    //                         'sale_agreement_id'     =>  $sale_agreement->id,
    //                         'booking_id'            =>  $booking->id,
    //                         'line_item_id'          =>  $value->id
    //                     ]);
    //                     if($sale_agreement_item){
    //                         if(empty($check_discount)){
    //                             $list_discount = null;
    //                         }else{
    //                             $list_discount = $this->listDiscount($sale_agreement_item->id);
    //                         }
    //                         SaleAgreement::where('id', $sale_agreement->id)->update(['discount_list'=>$list_discount]);
    //                     }
    //                     $total_amount = (float)$value->amount;
    //                     $total_tax_amount = (float)$value->tax_amount;
    //                     $total = $total_amount + $total_tax_amount;
    //                     $sale_agreement->update([
    //                         'total_amount'        =>    $total_amount,
    //                         'total_tax_amount'    =>    $total_tax_amount,
    //                         'total'               =>    $total
    //                     ]);
    //                 }
    //             }


    //             $booking_line_niche = BookingLineItems::whereHas('booking_type', function (Builder $query) {
    //                 $query->where([
    //                     ['reference_type', '=', 'booking_type'],
    //                     ['reference_value_text', '=', 'Niches'],
    //                 ]);
    //             })->where([
    //                 ['booking_id', '=', $booking->id],
    //             ])->get();
    //             ///
    //             $status_niches_unoccupied = Reference::where([
    //                 ['reference_type', '=', 'status_services_niches'],
    //                 ['reference_value_text', '=', 'Sold - Unoccupied'],
    //             ])->first();
    //             ///
    //             $status_niches_occupied = Reference::where([
    //                 ['reference_type', '=', 'status_services_niches'],
    //                 ['reference_value_text', '=', 'Sold - Occupied'],
    //             ])->first();
    //             ///
    //             foreach ($booking_line_niche as $book_line) {
    //                 if ($book_line->interment_date == null) {
    //                     $book_line->status = $status_niches_unoccupied->id;
    //                     $book_line->save();
    //                 } else {
    //                     $book_line->status = $status_niches_occupied->id;
    //                     $book_line->save();
    //                 }
    //                 Niche::where('id', '=', $book_line->niche->id)->update(['booking_id' => $book_line->booking->id, 'booking_line_item' => $book_line->id, 'status' => 'Unavailable']);
    //             }
    //         } else {
    //             return response()->json([
    //                 'status' => 'error',
    //                 'errors' => "Something bad happened, please try later"
    //             ], 422);
    //         }
    //         return response()->json([
    //             'status' => 'Successfully Generate Sale Agreement.',
    //             'data'   => $sale_agreement
    //         ], 200);
    //     } else {
    //         return response()->json(
    //             [
    //                 'status' => 'error',
    //                 'errors' => 'Cannot find id'
    //             ],
    //             404
    //         );
    //     }
    // }

    public function createSaleAgreement(Request $request){
        
        if($request->id){
            $check_sale_agreement = SaleAgreement::where('booking_id', $request->id)->whereNull('deleted_at')->first();
            // if(!empty($check_sale_agreement)){
            //     return response()->json([
            //         'status' => 'error',
            //         'errors' => "Sale Agreement has been created"
            //     ], 422);
            // }
            // $sale_agreement_no = 1;
            // $id_sale_agreement = SaleAgreement::select('id')->orderBy('id', "DESC")->first();
            // if($id_sale_agreement){
            //     $sale_agreement_no = (int)$id_sale_agreement->id + 1;
            // }
            $now = now();
            $booking = Booking::where('id', $request->id)->first();
            
            $check_discount = Discount::all();
            if(empty($check_discount)){
                $list_discount = null;
            }else{
                $list_discount = $this->listDiscount($booking->id);
            }
            // $check_booking_line = BookingLineItems::whereHas('booking_type', function (Builder $query) {
            //     $query->where([
            //             ['reference_type', '=', 'booking_type'],
            //             ['reference_value_text', '=', 'Niches'],
            //         ]);
            // })->where([
            //     ['booking_id', '=', $booking->id],
            // ])->get();
            // foreach($check_booking_line as $check_line){
            //     $check_niche = Niche::where('id', $check_line->service_id)->whereNotNull('booking_id')->first();
            //     if(!empty($check_niche)){
            //         return response()->json([
            //             'status' => 'error',
            //             'errors' => "Niche ".$check_niche->reference_no." has been booked"
            //         ], 422);
            //     }
            // }
            if(!empty($booking)){
                try{
                    DB::beginTransaction();
                    $sale_agreement_no = 1;
                    $dmY = Carbon::now()->format('dmY');
                    $booking_line_items = BookingLineItems::where('booking_id', $booking->id)
                    ->with("type_booking", "service_other")->get();
                    $sale_agreement_niche = null;
                    $sale_agreement_room = null;
                    $array_type = [
                        'Niches' => false,
                        'Memorial Rooms' => false
                    ];
                    $gst = GSTRate::where(\DB::raw("DATE(gst_start_date)"), '<=', \DB::raw("DATE('".$now->format('Y-m-d')."')"))
                    ->where(function ($query) use ($now) {
                        $query->where('gst_end_date','>=', "'".$now->format('Y-m-d h:i:s')."'")
                            ->orWhereNull('gst_end_date');
                    })
                    ->orderBy('gst_start_date', 'DESC')->first();
                    foreach ($booking_line_items as $key => $value) {
                        // $checkBookingHasSaleAgreement = SaleAgreementLineItem::where("line_item_id", $value->id)->first();
                        $id_sale_agreement = SaleAgreement::select('id')->orderBy('id', "DESC")->first();
                        if ($id_sale_agreement) {
                            $sale_agreement_no = (int)$id_sale_agreement->id + 1;
                        }
                        $number_sale_agreement_no = str_pad($sale_agreement_no,4,'0',STR_PAD_LEFT);
                        $check_sa = SaleAgreement::where('booking_id', $booking->id)->where('sale_agreement_type', $value->type_booking->id)->first();

                        if(!empty($check_sa)){
                            if($value->type_booking->reference_value_text === "Niches"){
                                $sale_agreement_niche = $check_sa;
                                $array_type['Niches'] = true;
                            }
                            if($value->type_booking->reference_value_text === "Memorial Rooms"){
                                $sale_agreement_room = $check_sa;
                                $array_type['Memorial Rooms'] = true;
                            }
                        }else{
                            if($value->type_booking->reference_value_text === "Niches"){
                                $sale_agreement_niche = SaleAgreement::create([
                                    'sale_agreement_no'     =>  'N-'.$dmY.'-'.$number_sale_agreement_no,
                                    'sale_agreement_date'   =>  $now->toDateTimeString(),
                                    'booking_id'            =>  $booking->id,
                                    'user_id'               =>  $booking->user_id,
                                    'discount_list'         =>  $list_discount,
                                    'sale_agreement_type'   =>  $value->type_booking->id,
                                    'gst_id'                =>  @$gst->id
                                ]);
                                $array_type['Niches'] = true;
                            }
                            if($value->type_booking->reference_value_text === "Memorial Rooms"){
                                $sale_agreement_room = SaleAgreement::create([
                                    'sale_agreement_no'     =>  'P-'.$dmY.'-'.$number_sale_agreement_no,
                                    'sale_agreement_date'   =>  $now->toDateTimeString(),
                                    'booking_id'            =>  $booking->id,
                                    'user_id'               =>  $booking->user_id,
                                    'discount_list'         =>  $list_discount,
                                    'sale_agreement_type'   =>  $value->type_booking->id,
                                    'gst_id'                =>  @$gst->id
                                ]);
                                $array_type['Memorial Rooms'] = true;
                            }
                        }
                    }
                    if(!$array_type['Memorial Rooms'] && !$array_type['Niches']){
                        DB::rollBack();
                        return response()->json([
                            'status' => 'error',
                            'errors' => "No service found to add additional services!"
                        ], 422);
                    }
                    $status = Reference::where('reference_type', 'booking_status')->where('reference_value_text', 'Agreement')->first();
                    $booking->update(['status' => $status->id, 'is_sale' => 1]);
                    if($sale_agreement_niche || $sale_agreement_room){
                        $total_amount_niches = 0;
                        $total_tax_amount_niches = 0;
                        $total_niches = 0;

                        $total_amount_room = 0;
                        $total_tax_amount_room = 0;
                        $total_room = 0;
                        if($request->is_sale){
                            SaleAgreementLineItem::where("sale_agreement_id", @$sale_agreement_niche->id)->orWhere("sale_agreement_id", @$sale_agreement_room->id)->delete();
                        }
                        foreach ($booking_line_items as $value) {
                            $value->is_sale = 1;
                            $value->save();
                            SaleAgreementLineItem::where("line_item_id", $value->id)->delete();
                            // echo $value->type_booking->reference_value_text."</br>";
                            if($value->type_booking->reference_value_text === "Niches"){
                                // echo $value->type_booking->reference_value_text."</br>";
                                $sale_agreement_item = SaleAgreementLineItem::create([
                                    'sale_agreement_id'     =>  $sale_agreement_niche->id,
                                    'booking_id'            =>  $booking->id,
                                    'line_item_id'          =>  $value->id
                                ]);
                                $total_amount_niches += (float)$value->amount;
                                $total_tax_amount_niches += (float)$value->tax_amount;
                                $total_niches = $total_amount_niches + $total_tax_amount_niches;
                            }
                            if($value->type_booking->reference_value_text === "Memorial Rooms"){
                                // echo $value->type_booking->reference_value_text."</br>";
                                $sale_agreement_item = SaleAgreementLineItem::create([
                                    'sale_agreement_id'     =>  $sale_agreement_room->id,
                                    'booking_id'            =>  $booking->id,
                                    'line_item_id'          =>  $value->id
                                ]);
                                $total_amount_room += (float)$value->amount;
                                $total_tax_amount_room += (float)$value->tax_amount;
                                $total_room = $total_amount_room + $total_tax_amount_room;
                            }
                            if($value->type_booking->reference_value_text === "Additional Services"){
                                if(!empty($value->service_other->category)){
                                    if($value->service_other->category->reference_value_text === "Niche"){
                                        if(!$array_type['Niches']){
                                            DB::rollBack();
                                            return response()->json([
                                                'status' => 'error',
                                                'errors' => "Not found service Niche!"
                                            ], 422);
                                        }
                                        $sale_agreement_item = SaleAgreementLineItem::create([
                                            'sale_agreement_id'     =>  $sale_agreement_niche->id,
                                            'booking_id'            =>  $booking->id,
                                            'line_item_id'          =>  $value->id
                                        ]);
                                        $total_amount_niches += (float)$value->amount;
                                        $total_tax_amount_niches += (float)$value->tax_amount;
                                        $total_niches = $total_amount_niches + $total_tax_amount_niches;
                                    }else if($value->service_other->category->reference_value_text === "Memorial Room") {
                                        if(!$array_type['Memorial Rooms']){
                                            DB::rollBack();
                                            return response()->json([
                                                'status' => 'error',
                                                'errors' => "Not found service Memorial Rooms!"
                                            ], 422);
                                        }
                                        $sale_agreement_item = SaleAgreementLineItem::create([
                                            'sale_agreement_id'     =>  $sale_agreement_room->id,
                                            'booking_id'            =>  $booking->id,
                                            'line_item_id'          =>  $value->id
                                        ]);
                                        $total_amount_room += (float)$value->amount;
                                        $total_tax_amount_room += (float)$value->tax_amount;
                                        $total_room = $total_amount_room + $total_tax_amount_room;
                                    }else{
                                        DB::rollBack();
                                        return response()->json([
                                            'status' => 'error',
                                            'errors' => "Select type Additional Services miss category!"
                                        ], 422);
                                    }
                                }else{
                                    DB::rollBack();
                                    return response()->json([
                                        'status' => 'error',
                                        'errors' => "Select type Additional Services miss category!"
                                    ], 422);
                                }
                            }
                        }
                        if(!empty($sale_agreement_niche)){
                            $sale_agreement_niche->update([
                                'total_amount'        =>    $total_amount_niches,
                                'total_tax_amount'    =>    $total_tax_amount_niches,
                                'total'               =>    $total_niches
                            ]);
                        }
                        if(!empty($sale_agreement_room)){
                            $sale_agreement_room->update([
                                'total_amount'        =>    $total_amount_room,
                                'total_tax_amount'    =>    $total_tax_amount_room,
                                'total'               =>    $total_room
                            ]);
                        }
                        if(!empty($sale_agreement_services)){
                            $sale_agreement_services->update([
                                'total_amount'        =>    $total_amount_room,
                                'total_tax_amount'    =>    $total_tax_amount_room,
                                'total'               =>    $total_room
                            ]);
                        }
                        $booking_line_niche = BookingLineItems::whereHas('booking_type', function (Builder $query) {
                            $query->where([
                                    ['reference_type', '=', 'booking_type'],
                                    ['reference_value_text', '=', 'Niches'],
                                ]);
                        })->where([
                            ['booking_id', '=', $booking->id],
                        ])->get();
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
                        foreach($booking_line_niche as $book_line){
                            if($book_line->interment_date == null){
                                $book_line->status = $status_niches_unoccupied->id;
                                $book_line->save();
                            }else{
                                $book_line->status = $status_niches_occupied->id;
                                $book_line->save();
                            }
                            Niche::where('id', '=', $book_line->niche->id)->update(['booking_id' => $book_line->booking->id, 'booking_line_item' => $book_line->id, 'status' => 'Unavailable']);
                        }
                    }
                    DB::commit();
                }catch(\Exception $th){
                    DB::rollBack();
                    return $this->error('Something went wrong, please try again!', 422);
                }
                
            }
            else{
                return response()->json([
                    'status' => 'error',
                    'errors' => "Something bad happened, please try later"
                ], 422);
            }
            return response()->json([
                'status' => 'success',
                'message'   => "Successfully Generate Sale Agreement."
            ], 200);
        }else{
            return response()->json(
                [
                    'status' => 'error',
                    'errors' => 'Cannot find id'
                ], 404);
        }
    }

    public static function listDiscount($id){
        $discount_list = [];
        ///type_niche
        $is_niche = Reference::where([
            ['reference_type', '=', 'booking_type'],
            ['reference_value_text', '=', 'Niches'],
        ])->first();
        // type_room
        $is_room = Reference::where([
            ['reference_type', '=', 'booking_type'],
            ['reference_value_text', '=', 'Memorial Rooms'],
        ])->first();
        // type additional service
        $is_additional_service = Reference::where([
            ['reference_type', '=', 'booking_type'],
            ['reference_value_text', '=', 'Additional Services'],
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
        $category_niche_standard = Reference::where([
            ['reference_type', '=', 'category_niche'],
            ['reference_value_text', '=', 'Standard'],
        ])->first();
        //
        $category_niche_premium = Reference::where([
            ['reference_type', '=', 'category_niche'],
            ['reference_value_text', '=', 'Premium'],
        ])->first();
        // Discount Room
        $count_standard_single = 0;
        $count_standard_double = 0;
        $count_premium_double = 0;
        $count_premium_single = 0;

        $booking_line_niche = BookingLineItems::where('booking_id', '=', $id)->get();
        foreach($booking_line_niche as $key=>$book_line){
            /// Single
            if($book_line->niche->category_id == $category_niche_standard->id){
                if($book_line->niche->type_id == $type_niche_single->id){
                    $count_standard_single++;

                    $discount = Discount::where([
                        ['service_id', '=', $is_niche->id],
                        ['category', '=', $book_line->niche->category_id],
                        ['type', '=', $book_line->niche->type_id],
                        ['minimum_qty', '<=', $count_standard_single]
                    ])->get();
                    ////
                    foreach($discount as $item){
                        if(!in_array($item->id, $discount_list)){
                            array_push($discount_list, $item->id);
                        }
                    }
                            
                }
                if($book_line->niche->type_id == $type_niche_double->id){
                    $count_standard_double++;
                    $count_standard_single += 2;

                    $discount = Discount::where([
                        ['service_id', '=', $is_niche->id],
                        ['category', '=', $book_line->niche->category_id],
                        ['type', '=', $book_line->niche->type_id],
                        ['minimum_qty', '<=', $count_standard_double]
                    ])->get();
                    foreach($discount as $item){
                        if(!in_array($item->id, $discount_list)){
                            array_push($discount_list, $item->id);
                        }
                    }
                    /////
                    if($count_standard_single >= 8){
                        $discount_single = Discount::where([
                            ['service_id', '=', $is_niche->id],
                            ['category', '=', $book_line->niche->category_id],
                            ['type', '=', $type_niche_single->id],
                            ['minimum_qty', '<=', $count_standard_single]
                        ])->get();
                        foreach($discount_single as $item){
                            if(!in_array($item->id, $discount_list)){
                                array_push($discount_list, $item->id);
                            }
                        }
                    }
                }
            }
            // Premium
            if($book_line->niche->category_id == $category_niche_premium->id){
                if($book_line->niche->type_id == $type_niche_single->id){
                    $count_premium_single++;
                    $discount = Discount::where([
                        ['service_id', '=', $is_niche->id],
                        ['category', '=', $book_line->niche->category_id],
                        ['type', '=', $book_line->niche->type_id],
                        ['minimum_qty', '<=', $count_premium_single]
                    ])->get();
                    ////
                    foreach($discount as $item){
                        if(!in_array($item->id, $discount_list)){
                            array_push($discount_list, $item->id);
                        }
                    }
                }
                if($book_line->niche->type_id == $type_niche_double->id){
                    $count_premium_single += 2;
                    $count_premium_double++;
                    $discount = Discount::where([
                        ['service_id', '=', $is_niche->id],
                        ['category', '=', $book_line->niche->category_id],
                        ['type', '=', $book_line->niche->type_id],
                        ['minimum_qty', '<=', $count_premium_double]
                    ])->get();
                    ////
                    foreach($discount as $item){
                        if(!in_array($item->id, $discount_list)){
                            array_push($discount_list, $item->id);
                        }
                    }
                    if($count_premium_single >= 8){
                        $discount_single = Discount::where([
                            ['service_id', '=', $is_niche->id],
                            ['category', '=', $book_line->niche->category_id],
                            ['type', '=', $type_niche_single->id],
                            ['minimum_qty', '<=', $count_premium_single]
                        ])->get();
                        foreach($discount_single as $item){
                            if(!in_array($item->id, $discount_list)){
                                array_push($discount_list, $item->id);
                            }
                        }
                    }
                }
                if($book_line->niche->type_id == $type_niche_all->id){
                    $count_premium_all++;
                    $discount = Discount::where([
                        ['service_id', '=', $is_niche->id],
                        ['category', '=', $book_line->niche->category_id],
                        ['type', '=', $book_line->niche->type_id],
                        ['minimum_qty', '<=', $count_premium_all]
                    ])->get();
                    ////
                    foreach($discount as $item){
                        if(!empty($item)){
                            if(!in_array($item->id, $discount_list)){
                                array_push($discount_list, $item->id);
                            }
                        }
                        
                    }
                }
            }
            if($book_line->booking_type_id === $is_room->id){
                $discount = Discount::where([
                    ['service_id', '=', $is_room->id],
                    ['room_id', '=', $book_line->room->id]
                ])->get();
                foreach($discount as $item){
                    if(!in_array($item->id, $discount_list)){
                        array_push($discount_list, $item->id);
                    }
                }
            }
            if($book_line->booking_type_id === $is_additional_service->id){
                $discount = Discount::where([
                    ['service_id', '=', $is_additional_service->id],
                    ['other_id', '=', $book_line->other->id],
                    ['type', '=', $book_line->service_other->type],
                ])->get();
                foreach($discount as $item){
                    if(!in_array($item->id, $discount_list)){
                        array_push($discount_list, $item->id);
                    }
                }
            }
        }

        // $sale_agreement_item = SaleAgreementLineItem::where('id', $id)->get();
        // foreach($sale_agreement_item as $sale_agreement_line){
        //     $id = $sale_agreement_line->line_item_id;
        //     $booking_line = BookingLineItems::where('id', '=', $id)->first();
        //     if($booking_line->booking_type_id === $is_niche->id){
        //         /// Single
        //         if($booking_line->niche->category_id == $category_niche_standard->id){
        //             if($booking_line->niche->type_id == $type_niche_single->id){
        //                 $count_standard_single++;
        //                 $discount = Discount::where([
        //                     ['service_id', '=', $is_niche->id],
        //                     ['category', '=', $booking_line->niche->category_id],
        //                     ['type', '=', $booking_line->niche->type_id],
        //                     ['minimum_qty', '<=', $count_standard_single]
        //                 ])->get();
        //                 ////
        //                 foreach($discount as $item){
        //                     if(!in_array($item->id, $discount_list)){
        //                         array_push($discount_list, $item->id);
        //                     }
        //                 }
        //             }
        //             if($booking_line->niche->type_id == $type_niche_double->id){
        //                 $count_standard_double++;
        //                 $count_standard_single += 2;
        
        //                 $discount = Discount::where([
        //                     ['service_id', '=', $is_niche->id],
        //                     ['category', '=', $booking_line->niche->category_id],
        //                     ['type', '=', $booking_line->niche->type_id],
        //                     ['minimum_qty', '<=', $count_standard_double]
        //                 ])->get();
        //                 foreach($discount as $item){
        //                     if(!in_array($item->id, $discount_list)){
        //                         array_push($discount_list, $item->id);
        //                     }
        //                 }
        //                 /////
        //                 if($count_standard_single >= 8){
        //                     $discount_single = Discount::where([
        //                         ['service_id', '=', $is_niche->id],
        //                         ['category', '=', $booking_line->niche->category_id],
        //                         ['type', '=', $type_niche_single->id],
        //                         ['minimum_qty', '<=', $count_standard_single]
        //                     ])->get();
        //                     foreach($discount_single as $item){
        //                         if(!in_array($item->id, $discount_list)){
        //                             array_push($discount_list, $item->id);
        //                         }
        //                     }
        //                 }
        //             }
        //         }
        //         // Premium
        //         if($booking_line->niche->category_id == $category_niche_premium->id){
        //             if($booking_line->niche->type_id == $type_niche_single->id){
        //                 $count_premium_single++;
        //                 $discount = Discount::where([
        //                     ['service_id', '=', $is_niche->id],
        //                     ['category', '=', $booking_line->niche->category_id],
        //                     ['type', '=', $booking_line->niche->type_id],
        //                     ['minimum_qty', '<=', $count_premium_single]
        //                 ])->get();
        //                 ////
        //                 foreach($discount as $item){
        //                     if(!in_array($item->id, $discount_list)){
        //                         array_push($discount_list, $item->id);
        //                     }
        //                 }
        //             }
        //             if($booking_line->niche->type_id == $type_niche_double->id){
        //                 $count_premium_single += 2;
        //                 $count_premium_double++;
        //                 $discount = Discount::where([
        //                     ['service_id', '=', $is_niche->id],
        //                     ['category', '=', $booking_line->niche->category_id],
        //                     ['type', '=', $booking_line->niche->type_id],
        //                     ['minimum_qty', '<=', $count_premium_double]
        //                 ])->get();
        //                 ////
        //                 foreach($discount as $item){
        //                     if(!in_array($item->id, $discount_list)){
        //                         array_push($discount_list, $item->id);
        //                     }
        //                 }
        //                 if($count_premium_single >= 8){
        //                     $discount_single = Discount::where([
        //                         ['service_id', '=', $is_niche->id],
        //                         ['category', '=', $booking_line->niche->category_id],
        //                         ['type', '=', $type_niche_single->id],
        //                         ['minimum_qty', '<=', $count_premium_single]
        //                     ])->get();
        //                     foreach($discount_single as $item){
        //                         if(!in_array($item->id, $discount_list)){
        //                             array_push($discount_list, $item->id);
        //                         }
        //                     }
        //                 }
        //             }
        //         }

        //     }
        //     if($booking_line->booking_type_id === $is_room->id){
        //         $discount = Discount::where([
        //             ['service_id', '=', $is_room->id],
        //             ['room_id'], '=', $booking_line->room->id
        //         ])->get();
        //         foreach($discount as $item){
        //             if(!in_array($item->id, $discount_list)){
        //                 array_push($discount_list, $item->id);
        //             }
        //         }
        //     }
        //     if($booking_line->booking_type_id === $is_additional_service->id){
        //         $discount = Discount::where([
        //             ['service_id', '=', $is_room->id],
        //             ['other_id', '=', $booking_line->other->id],
        //             ['type', '=', $booking_line->other->type->id],
        //         ])->get();
        //         foreach($discount as $item){
        //             if(!in_array($item->id, $discount_list)){
        //                 array_push($discount_list, $item->id);
        //             }
        //         }
        //     }
        // }
    
        return json_encode($discount_list);
    }

    /**
     * @OA\Get(
     *     tags={"Sale Agreement"},
     *     path="/api/update-list-discount",
     *     summary="Update list discount Sale Agreement for Booking",
     *     operationId="updateListDiscountForBooking",
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

    public static function updateListDiscountForBooking()
    {
        $booking_id = SaleAgreement::whereNull('discount_list')->pluck('booking_id')->all();
        // dd($booking_id);
        ///type_niche
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
        $type_niche_all = Reference::where([
            ['reference_type', '=', 'type_niche'],
            ['reference_value_text', '=', 'All'],
        ])->first();
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
        $category_niche_all = Reference::where([
            ['reference_type', '=', 'category_niche'],
            ['reference_value_text', '=', 'All'],
        ])->first();
        //
        foreach ($booking_id as $id) {
            $booking_line_niche = BookingLineItems::whereHas('booking_type', function (Builder $query) {
                $query->where([
                    ['reference_type', '=', 'booking_type'],
                    ['reference_value_text', '=', 'Niches'],
                ]);
            })->where([
                ['booking_id', '=', $id],
            ])->get();
            ///
            $discount_list = [];

            $count_standard_single = 0;
            $count_standard_double = 0;
            $count_premium_double = 0;
            $count_premium_single = 0;
            $count_premium_all = 0;
            //
            foreach ($booking_line_niche as $key => $book_line) {
                if (!($book_line->service_id == 0 && $book_line->service_id == null)) {
                    /// Single
                    if ($book_line->niche->category_id == $category_niche_standard->id) {
                        if ($book_line->niche->type_id == $type_niche_single->id) {
                            $count_standard_single++;

                            $discount = Discount::where([
                                ['category', '=', $book_line->niche->category_id],
                                ['type', '=', $book_line->niche->type_id],
                                ['minimum_qty', '<=', $count_standard_single]
                            ])->get();
                            ////
                            foreach ($discount as $item) {
                                if (!in_array($item->id, $discount_list)) {
                                    array_push($discount_list, $item->id);
                                }
                            }
                        }
                        if ($book_line->niche->type_id == $type_niche_double->id) {
                            $count_standard_double++;
                            $count_standard_single += 2;

                            $discount = Discount::where([
                                ['category', '=', $book_line->niche->category_id],
                                ['type', '=', $book_line->niche->type_id],
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
                                    ['category', '=', $book_line->niche->category_id],
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
                    if ($book_line->niche->category_id == $category_niche_premium->id) {
                        if ($book_line->niche->type_id == $type_niche_single->id) {
                            $count_premium_single++;
                            $discount = Discount::where([
                                ['category', '=', $book_line->niche->category_id],
                                ['type', '=', $book_line->niche->type_id],
                                ['minimum_qty', '<=', $count_premium_single]
                            ])->get();
                            ////
                            foreach ($discount as $item) {
                                if (!in_array($item->id, $discount_list)) {
                                    array_push($discount_list, $item->id);
                                }
                            }
                        }
                        if ($book_line->niche->type_id == $type_niche_double->id) {
                            $count_premium_single += 2;
                            $count_premium_double++;
                            $discount = Discount::where([
                                ['category', '=', $book_line->niche->category_id],
                                ['type', '=', $book_line->niche->type_id],
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
                                    ['category', '=', $book_line->niche->category_id],
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
                        if ($book_line->niche->type_id == $type_niche_all->id) {
                            $count_premium_all++;
                            $discount = Discount::where([
                                ['category', '=', $book_line->niche->category_id],
                                ['type', '=', $book_line->niche->type_id],
                                ['minimum_qty', '<=', $count_premium_all]
                            ])->get();
                            ////
                            foreach ($discount as $item) {
                                if (!in_array($item->id, $discount_list)) {
                                    array_push($discount_list, $item->id);
                                }
                            }
                        }
                    }
                    /// All
                    if ($book_line->niche->category_id == $category_niche_all->id) {
                        $discount = Discount::where([
                            ['category', '=', $book_line->niche->category_id],
                        ])->get();
                        ////
                        foreach ($discount as $item) {
                            if (!in_array($item->id, $discount_list)) {
                                array_push($discount_list, $item->id);
                            }
                        }
                    }
                }
            }
            $list = json_encode($discount_list);
            $sale_agreement = SaleAgreement::where('booking_id', '=', $id)->first();
            $sale_agreement->discount_list = $list;
            $sale_agreement->save();
        }
        return response()->json([
            'status' => 'success',
        ], 200);
    }

    /**
     * @OA\Get(
     *     tags={"Sale Agreement"},
     *     path="/api/sale-agreement/{id}",
     *     summary="Get detail Sale Agreement",
     *     operationId="getDetailSaleAgreement",
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

    // public function getDetailSaleAgreement($id)
    // {
    //     if ($id) {
    //         $sale_agreement = SaleAgreement::where('id', $id)->with(['client' => function ($query) {
    //             $query->with('preferred_contact_by');
    //             $query->select('id', 'display_name', 'phone', 'email', 'preferred_contact_by_id');
    //         }])
    //             ->with(['booking' => function ($query) {
    //                 $query->select('id', 'booking_no');
    //             }])
    //             ->with(['discount' => function ($query) {
    //                 $query->with('type_amount');
    //             }])
    //             ->with(['sale_agreement_item' => function ($query) {
    //                 $query->with(['booking_line_item' => function ($q) {
    //                     $q->select('id', 'booking_type_id', 'service_id', 'service_type_id', 'remarks', 'amount', 'discount', 'tax_amount');
    //                     $q->with('booking_type');
    //                     $q->with('getDiscount');
    //                     $q->with(['niche' => function ($_q) {
    //                         $_q->select('id', 'reference_no');
    //                     }]);
    //                     $q->with(['room' => function ($_q) {
    //                         $_q->select('id', 'room_no');
    //                     }]);
    //                     $q->with(['other' => function ($_q) {
    //                         $_q->select('id', 'service_name');
    //                     }]);
    //                     $q->with(['serviceType' => function ($_q) {
    //                         $_q->select('id', 'service_name');
    //                     }]);
    //                 }]);
    //             }])
    //             ->with('admin')->first();
    //         if(isset($sale_agreement->discount_list)){
    //             $arr_id = json_decode($sale_agreement->discount_list);
    //             $discount = Discount::whereIn('id', $arr_id)->with('type_amount')->get();
    //             $sale_agreement->discount_list = $discount;
    //         }
    //         $is_invoice = Invoice::where('sale_agreement_id', '=', $id)->count();

    //         // $sale_agreement->discount_list = $discount;
    //         if ($is_invoice > 0) {
    //             $sale_agreement->is_invoice = true;
    //         } else {
    //             $sale_agreement->is_invoice = false;
    //         }
    //         return response()->json([
    //             'status' => 'success',
    //             'data'  =>  $sale_agreement
    //         ], 200);
    //     } else {
    //         return response()->json(
    //             [
    //                 'status' => 'error',
    //                 'errors' => 'Cannot find id'
    //             ],
    //             404
    //         );
    //     }
    // }
    public function getDetailSaleAgreement($id){
        if($id){
            $sale_agreement = SaleAgreement::where('id', $id)->with(['client' => function($query){
                $query->with('preferred_contact_by');
                $query->select('id' ,'display_name', 'phone', 'email', 'preferred_contact_by_id');
            }])
            ->with(['booking' => function ($query){
                $query->select('id', 'booking_no');
            }])
            ->with(['discount' => function($query){
                $query->with('type_amount', 'service_type');
            }])
            ->with("sale_agreement_type")
            ->with("gst_detail")
            ->with(['sale_agreement_item' => function($query){
                $query->has('booking_line_item');
                $query->with(['booking_line_item' => function($q){
                    $q->select('id', 'booking_type_id', 'service_id', 'service_type_id', 'remarks', 'amount','discount', 'tax_amount', 'lease_start_date', 'lease_expiry_date', 'event_id', 'room_type', 'check_in_date', 'check_out_date', 'check_in_time', 'check_out_time');
                    $q->with('booking_type', 'event', 'getDiscount', 'room_type');
                    $q->with(['niche' => function($_q){
                        $_q->select('id', 'reference_no', 'bay', 'wing', 'floor', 'block', 'level', 'unit', 'type_id', 'category_id');
                        $_q->with('type', 'category');
                    }]);
                    $q->with(['room' => function($_q){
                        $_q->select('id', 'room_no', 'price_daily', 'price_hourly');
                    }]);
                    $q->with(['other' => function($_q){
                        $_q->select('id', 'service_name', 'type');
                    }]);
                    $q->with(['serviceType' => function($_q){
                        $_q->select('id', 'service_name');
                    }]);
                }]);
            }])
            ->first();
            ////
            $arr_id = json_decode($sale_agreement->discount_list);
            if($arr_id !== null){
                $discount = Discount::whereIn('id', $arr_id)->with('type_amount', 'service_type')->get();
                $sale_agreement->discount_list = $discount;
            }
           
            $is_invoice = Invoice::where('sale_agreement_id', '=', $id)->count();
            if($is_invoice >0){
                $sale_agreement->is_invoice = true;
            }else{
                $sale_agreement->is_invoice = false;
            }
            return response()->json([
                'status' => 'success',
                'data'  =>  $sale_agreement
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
     * @OA\Post(
     *     tags={"Sale Agreement"},
     *     path="/api/amount-count",
     *     summary="Amount Count",
     *     operationId="amountCount",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="arr_id",
     *                     description="ID Booking Line Items",
     *                     type="string",
     *                     default="[1,2,3]"
     *                  ),
     *                  required={"arr_id"}
     *             )
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         required=true,
     *         description="",
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

    public function amountCount(Request $request)
    {

        $arr_id = $request->arr_id;
        if (is_string($arr_id)) {
            $arr_id = json_decode($arr_id);
        }
        switch ($request->type) {
            case 1:
                break;
            case 2:
                $arr_id =  InvoiceLineItem::whereIn('id', $arr_id)->pluck('line_item_id')->all();
                break;

            case 3:
                $arr_line_invoice = PaymentLineItem::whereIn('id', $arr_id)->pluck('line_item_id')->all();

                $arr_id =  InvoiceLineItem::whereIn('id', $arr_line_invoice)->pluck('line_item_id')->all();
                break;

            default:
                # code...
                break;
        }

        $total_amount = SaleAgreementLineItem::whereIn('id', $arr_id)->with(['booking_line_item' => function ($q) {
            // $q->select('id', 'booking_type_id', 'service_id', 'remarks', 'amount','discount');
            $q->with('getDiscount');
        }])->get();

        $data = [
            "amount" => 0,
            "gst_amount" => 0,
            "total_amount" => 0,
            "total_minus_discount" => 0
        ];
        $amount = 0;
        $gst_amount = 0;
        $total = 0;
        $discount = 0;
        $total_minus_discount = 0;
        foreach ($total_amount->toArray() as $key => $value) {
            $amount += (float)$value['booking_line_item']['amount'];
            $gst_amount += (float)$value['booking_line_item']['tax_amount'];
            $discount += (float)$value['booking_line_item']['discount_amount'];

            $total = ($amount + $gst_amount);
            $total_minus_discount = ($total - $discount);
        }
        $data["amount"] = number_format($amount, 2, '.', ',');
        $data["gst_amount"] = number_format($gst_amount, 2, '.', ',');
        $data["total_amount"] = number_format($total, 2, '.', ',');
        $data["discount"] = number_format($discount, 2, '.', ',');
        $data['total_minus_discount'] = number_format($total_minus_discount, 2, '.', ',');
        return response()->json([
            'status' => 'success',
            'data'  =>  $data
        ], 200);
    }
    /**
     * @OA\Post(
     *     tags={"Payment"},
     *     path="/api/amount-donate",
     *     summary="Amount Donate",
     *     operationId="amountDonate",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="arr_id",
     *                     description="ID Booking Line Items",
     *                     type="string",
     *                     default="[1,2,3]"
     *                  ),
     *                  required={"arr_id"}
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
    public function amountDonate(Request $request)
    {

        $arr_id = $request->arr_id;
        if (is_string($arr_id)) {
            $arr_id = json_decode($arr_id);
        }
        $payment = Payment::whereIn('id', $arr_id)->first();

        $data["amount"] = number_format($payment->total_amount, 2, '.', ',');
        $data["gst_amount"] = number_format($payment->total_tax_amount, 2, '.', ',');
        $data["total_amount"] = number_format($payment->total, 2, '.', ',');
        return response()->json([
            'status' => 'success',
            'data'  =>  $data
        ], 200);
    }
    /**
     * @OA\Get(
     *     tags={"Sale Agreement"},
     *     path="/api/sale-agreement",
     *     summary="Get list Sale Agreement",
     *     operationId="listSaleAgreement",
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

    // public function listSaleAgreement(Request $request)
    // {
    //     $limit = intval($request->query('limit'));
    //     $filter = json_decode($request->query('filter'));
    //     $sale_agreement = SaleAgreement::select('sale_agreements.*', 'reference.reference_type', 'reference.reference_value_text')
    //         ->whereNull('sale_agreements.deleted_at')
    //         ->join('sale_agreements_line_items', 'sale_agreements.id', '=', 'sale_agreements_line_items.sale_agreement_id')
    //         ->join('booking_line_items', 'sale_agreements_line_items.line_item_id', '=', 'booking_line_items.id')
    //         ->join('reference', 'booking_line_items.booking_type_id', '=', 'reference.id')
    //         ->where('reference.reference_type', '=', 'booking_type')
    //         ->with('invoice')
    //         ->with(['client' => function ($query) {
    //             $query->select('id', 'display_name');
    //         }])->orderBy('id', 'DESC');
    //     // dd($sale_agreement->get()->toArray());
    //     $type_1 = "d/m/Y";
    //     $type_2 = "d/m";
    //     $type_expectations_1 = "Y-m-d";
    //     $type_expectations_2 = "m-d";
    //     ///
    //     if (!empty($filter->all)) {
    //         $key_word = $filter->all;
    //         $key_word = BookingController::custom_date($type_1, $type_2, $key_word, $type_expectations_1, $type_expectations_2);
    //         $sale_agreement->where(function ($query) use ($key_word) {
    //             $query->where('sale_agreement_no', 'like', '%' . $key_word . '%')
    //                 ->orWhere('sale_agreement_date', 'like', '%' . $key_word . '%')
    //                 ->orWhere('total_amount', 'like', '%' . $key_word . '%')
    //                 ->orWhere('total_tax_amount', 'like', '%' . $key_word . '%')
    //                 ->orWhere('total', 'like', '%' . $key_word . '%')
    //                 ->orWhereHas('client', function (Builder $query) use ($key_word) {
    //                     $query->where('display_name', 'like', '%' . $key_word . '%');
    //                 })
    //                 ->orWhereHas('invoice', function (Builder $query) use ($key_word) {
    //                     $query->where('invoice_no', 'like', '%' . $key_word . '%');
    //                 });
    //         });
    //     }
    //     if (!empty($filter->sale_agreement)) {
    //         $key_word = $filter->sale_agreement;
    //         $sale_agreement->where(function ($query) use ($key_word) {
    //             $query->where('sale_agreement_no', 'like', '%' . $key_word . '%');
    //         });
    //     }
    //     if (!empty($filter->created_date)) {
    //         $key_word = $filter->created_date;
    //         $key_word = BookingController::custom_date($type_1, $type_2, $key_word, $type_expectations_1, $type_expectations_2);
    //         $sale_agreement->where(function ($query) use ($key_word) {
    //             $query->where('sale_agreement_date', 'like', '%' . $key_word . '%');
    //         });
    //     }
    //     if (!empty($filter->amount)) {
    //         $key_word = $filter->amount;
    //         $sale_agreement->where(function ($query) use ($key_word) {
    //             $query->where('total_amount', 'like', '%' . $key_word . '%');
    //         });
    //     }
    //     if (!empty($filter->gst)) {
    //         $key_word = $filter->gst;
    //         $sale_agreement->where(function ($query) use ($key_word) {
    //             $query->where('total_tax_amount', 'like', '%' . $key_word . '%');
    //         });
    //     }
    //     if (!empty($filter->total)) {
    //         $key_word = $filter->total;
    //         $sale_agreement->where(function ($query) use ($key_word) {
    //             $query->where('total', 'like', '%' . $key_word . '%');
    //         });
    //     }
    //     if (!empty($filter->clients_name)) {
    //         $key_word = $filter->clients_name;
    //         $sale_agreement->whereHas('client', function (Builder $query) use ($key_word) {
    //             $query->where('display_name', 'like', '%' . $key_word . '%');
    //         });
    //     }
    //     if (!empty($filter->invoices)) {
    //         $key_word = $filter->invoices;
    //         $sale_agreement->whereHas('invoice', function (Builder $query) use ($key_word) {
    //             $query->where('invoice_no', 'like', '%' . $key_word . '%');
    //         });
    //     }
    //     $sale_agreement = $sale_agreement->paginate($limit)->toArray();
    //     // dd($sale_agreement);
    //     return response()->json([
    //         'status' => 'success',
    //         'data'  =>  $sale_agreement
    //     ], 200);
    // }
    public function listSaleAgreement(Request $request){
        $limit = intval($request->query('limit'));
        $filter = json_decode($request->query('filter'));
        $sale_agreement = SaleAgreement::whereNull('deleted_at')
                        ->with('sale_agreement_item')
                        ->with('invoices')
                        ->with(['client' => function($query){
                            $query->select('id', 'display_name');
                        }])->orderBy('id', 'DESC');
        ///
        $type_1 = "d/m/Y";
        $type_2 = "d/m";
        $type_expectations_1 = "Y-m-d";
        $type_expectations_2 = "m-d";
        ///
        if (!empty($filter->all)){
            $key_word = $filter->all;
            $key_word = BookingController::custom_date($type_1, $type_2, $key_word, $type_expectations_1, $type_expectations_2);
            $sale_agreement->where(function ($query) use ($key_word) {
                $query->where('sale_agreement_no', 'like', '%'.$key_word.'%')
                    ->orWhere('sale_agreement_date', 'like', '%'.$key_word.'%')
                    ->orWhere('total_amount', 'like', '%'.$key_word.'%')
                    ->orWhere('total_tax_amount', 'like', '%'.$key_word.'%')
                    ->orWhere('total', 'like', '%'.$key_word.'%')
                    ->orWhereHas('client', function (Builder $query) use ($key_word) {
                        $query->where('display_name', 'like', '%'.$key_word.'%');
                    })
                    ->orWhereHas('invoices', function (Builder $query) use ($key_word) {
                        $query->where('invoice_no', 'like', '%'.$key_word.'%');
                    });
            });
        }
        if (!empty($filter->sale_agreement)){
            $key_word = $filter->sale_agreement;
            $sale_agreement->where(function ($query) use ($key_word) {
                $query->where('sale_agreement_no', 'like', '%'.$key_word.'%');
            });
        }
        if (!empty($filter->created_date)){
            $key_word = $filter->created_date;
            $key_word = BookingController::custom_date($type_1, $type_2, $key_word, $type_expectations_1, $type_expectations_2);
            $sale_agreement->where(function ($query) use ($key_word) {
                $query->where('sale_agreement_date', 'like', '%'.$key_word.'%');
            });
        }
        if (!empty($filter->amount)){
            $key_word = $filter->amount;
            $sale_agreement->where(function ($query) use ($key_word) {
                $query->where('total_amount', 'like', '%'.$key_word.'%');
            });
        }
        if (!empty($filter->gst)){
            $key_word = $filter->gst;
            $sale_agreement->where(function ($query) use ($key_word) {
                $query->where('total_tax_amount', 'like', '%'.$key_word.'%');
            });
        }
        if (!empty($filter->total)){
            $key_word = $filter->total;
            $sale_agreement->where(function ($query) use ($key_word) {
                $query->where('total', 'like', '%'.$key_word.'%');
            });
        }
        if (!empty($filter->clients_name)){
            $key_word = $filter->clients_name;
            $sale_agreement->whereHas('client', function (Builder $query) use ($key_word) {
                $query->where('display_name', 'like', '%'.$key_word.'%');
            });
        }
        if (!empty($filter->invoices)){
            $key_word = $filter->invoices;
            $sale_agreement->whereHas('invoices', function (Builder $query) use ($key_word) {
                $query->where('invoice_no', 'like', '%'.$key_word.'%');
            });
        }
        $sale_agreement = $sale_agreement->paginate($limit)->toArray();
        return response()->json([
            'status' => 'success',
            'data'  =>  $sale_agreement
        ], 200);
    }


    /**
     * @OA\Post(
     *     tags={"Sale Agreement"},
     *     path="/api/save-signature/{id}",
     *     summary="signature",
     *     operationId="saveSignature",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Id Sale Agreement",
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="discount_id",
     *         in="query",
     *         description="Id discount",
     *         @OA\Schema(
     *             type="integer",
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

    public function saveSignature(Request $request, $id)
    {
        $signature_offier = $request->file('signature_offier');
        $signature_client = $request->file('signature_client');
        $sale_agreement = SaleAgreement::find($id);
        $sale_agreement_no = $sale_agreement->sale_agreement_no;

        // if(empty($signature_offier) && empty($signature_client)){
        //     return response()->json(
        //         [
        //             'status' => 'error',
        //             'errors' => "Don't Forget Signature"
        //         ], 404);
        // }

        if (!empty($signature_offier)) {
            $path = public_path() . '/sale_agreement/' . $sale_agreement_no . '/signature_offier/';
            if (!file_exists(public_path("sale_agreement"))) {
                mkdir(public_path("sale_agreement"), 0777, true);
            }
            if (!file_exists(public_path("sale_agreement" . '/' . $sale_agreement_no))) {
                mkdir(public_path("sale_agreement" . '/' . $sale_agreement_no), 0777, true);
            }
            if (!file_exists(public_path("sale_agreement" . '/' . $sale_agreement_no . '/' . "signature_offier"))) {
                mkdir(public_path("sale_agreement" . '/' . $sale_agreement_no . '/' . "signature_offier"), 0777, true);
            }
            if (File::makeDirectory($path, 0777, true, true)) {
                $name_file = $signature_offier->getClientOriginalName();
                $signature_offier->move($path, $name_file);
            } else {
                $name_file = $signature_offier->getClientOriginalName();
                $signature_offier->move($path, $name_file);
            }
            $url = url('/sale_agreement/' . $sale_agreement_no . '/signature_offier/' . $name_file);
            $sale_agreement->officer = Auth::user()->id;
            $sale_agreement->signature_tgor_officer = $url;
        }
        // else{
        //     return response()->json(
        //         [
        //             'status' => 'error',
        //             'errors' => "Don't Forget Offier Signature"
        //         ], 404);
        // }

        if (!empty($signature_client)) {
            $path = public_path() . '/sale_agreement/' . $sale_agreement_no . '/signature_client/';
            if (!file_exists(public_path("sale_agreement"))) {
                mkdir(public_path("sale_agreement"), 0777, true);
            }
            if (!file_exists(public_path("sale_agreement" . '/' . $sale_agreement_no))) {
                mkdir(public_path("sale_agreement" . '/' . $sale_agreement_no), 0777, true);
            }
            if (!file_exists(public_path("sale_agreement" . '/' . $sale_agreement_no . '/' . "signature_client"))) {
                mkdir(public_path("sale_agreement" . '/' . $sale_agreement_no . '/' . "signature_client"), 0777, true);
            }
            if (File::makeDirectory($path, 0777, true, true)) {
                $name_file = $signature_client->getClientOriginalName();
                $signature_client->move($path, $name_file);
            } else {
                $name_file = $signature_client->getClientOriginalName();
                $signature_client->move($path, $name_file);
            }
            $url = url('/sale_agreement/' . $sale_agreement_no . '/signature_client/' . $name_file);
            $sale_agreement->signature_client = $url;
        }
        $sale_agreement->discount_id = $request->discount_id;
        $sale_agreement->total = $request->total;
        $sale_agreement->save();
        $sale_agreement_line = SaleAgreementLineItem::where('sale_agreement_id', $sale_agreement->id)->get();
        $discount = Discount::find($request->discount_id);
        foreach($sale_agreement_line as $line_item){
            $booking_line_item = BookingLineItems::where('id', '=', $line_item->line_item_id)->first();
            if($request->discount_id){
                if($booking_line_item->booking_type->reference_value_text == "Niches" && $discount->service_type->reference_value_text == "Niches"){
                    $booking_line_item->discount = $request->discount_id;
                }
                if(
                    $booking_line_item->booking_type->reference_value_text == "Memorial Rooms" &&
                    $discount->service_type->reference_value_text == "Memorial Rooms" &&
                    $booking_line_item->room->id == $discount->room_id
                ){
                    $booking_line_item->discount = $request->discount_id;
                }
                if(
                    $booking_line_item->booking_type->reference_value_text == "Additional Services" &&
                    $discount->service_type->reference_value_text == "Additional Services" &&
                    $booking_line_item->service_other->type == $discount->type &&
                    $$discount->other_id == $booking_line_item->service_other->id

                ){
                    $booking_line_item->discount = $request->discount_id;
                }
                $booking_line_item->save();
            }
            
        }
        if(!empty($request->discount_custom)){
            $arr_sale_agreement = $request->discount_custom;
            if(is_string($arr_sale_agreement)){
                $arr_sale_agreement = json_decode($arr_sale_agreement);
            }
            foreach($arr_sale_agreement as $item){
                if(!empty($item->discount_amount)){
                    if($item->discount_type == "Percentage"){
                        $value = $item->discount_amount.'%';
                        $discount = Discount::where('amount', $value)->where("is_custom", 1)->first();
                    }else{
                        $discount = Discount::where('amount', 'LIKE', $item->discount_amount)->where("is_custom", 1)->first();
                    }
                    
                    $sale_agreement = SaleAgreement::find($item->sale_id);
                    
                    if(empty($discount)){
                        if($item->discount_type == "Percentage"){
                            $value_reference = Reference::where('reference_type', 'amount_type')
                            ->where('reference_value_text', 'Percentage')->first();
                            $custom_discount = Discount::create([
                                'amount'        =>  $item->discount_amount.'%',
                                'amount_type'   =>  $value_reference->id,
                                'is_custom'     =>  1,
                                'percent'       => $item->discount_amount/100
                            ]);
                        }else{
                            $value_reference = Reference::where('reference_type', 'amount_type')
                                        ->where('reference_value_text', 'Value')->first();
                            $custom_discount = Discount::create([
                                'amount'        =>  $item->discount_amount,
                                'amount_type'   =>  $value_reference->id,
                                'is_custom'     =>  1
                            ]);
                        }
                    }
                    foreach($sale_agreement->sale_agreement_item as $value){
                        $now = now();
                        $booking_line = $item->line_item_id;
                        $booking_line_item = BookingLineItems::where('id', '=', $booking_line)->first();
                        $gst = 0.07;
                        $gst_detail = GSTRate::where('gst_start_date', '<=', $now->format('Y-m-d').' 00:00:00')
                        ->orderBy('gst_start_date', 'DESC')->first();
                        if ($gst_detail) {
                            $gst = $gst_detail->rate;
                        }
                        if(empty($discount)){
                            $booking_line_item->discount = $custom_discount->id;
                            if(@$custom_discount->percent){
                                $booking_line_item->tax_amount = ((float)$booking_line_item->amount -  ((float)$booking_line_item->amount * (float)$custom_discount->percent)) * (float)$gst;
                            }else{
                                $booking_line_item->tax_amount = ((float)$booking_line_item->amount -  (float)$custom_discount->amount) * (float)$gst;
                            }
                            $booking_line_item->save();
                        }else{
                            if(@$discount->percent){
                                $booking_line_item->tax_amount = ((float)$booking_line_item->amount -  ((float)$booking_line_item->amount * (float)$discount->percent)) * (float)$gst;
                            }else{
                                $booking_line_item->tax_amount = ((float)$booking_line_item->amount -  (float)$discount->amount) * (float)$gst;
                            }
                            $booking_line_item->discount = $discount->id;
                            $booking_line_item->save();
                        }
                    }
                    
                }
            }
        }
        return response()->json([
            'status' => 'Saved Successfully',
            'data' => $sale_agreement,
        ], 200);
    }
    /**
     * @OA\Delete(
     *     tags={"Sale Agreement"},
     *     path="/api/sale-agreement",
     *     summary="Delete Sale Agreement",
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
        $sale_agreement = SaleAgreement::whereIn('id', $ids)->delete();

        if ($sale_agreement) {
            $invoice = Invoice::whereIn('sale_agreement_id', $ids)->pluck('id')->all();

            SaleAgreementLineItem::whereIn('sale_agreement_id', $ids)->delete();
            Invoice::whereIn('sale_agreement_id', $ids)->delete();
            InvoiceLineItem::whereIn('sale_agreement_id', $ids)->delete();
            Payment::whereIn('invoice_id', $invoice)->delete();
            PaymentLineItem::whereIn('invoice_id', $invoice)->delete();

            return response()->json(
                [
                    'status' => 'Successfully Deleted Sale Agreement',
                ],
                200
            );
        }
        return response()->json(
            [
                'status' => 'error',
                'errors' => 'Cannot find room'
            ],
            404
        );
    }
    /**
     * @OA\Get(
     *     tags={"Niches"},
     *     path="/api/make-sale-agreement/{id}",
     *     summary="Make Sale Agreement",
     *     operationId="printSaleAgreement",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID Niche Licence",
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
    public function printSaleAgreement(Request $request, $id)
    {
        $sale_agreement = SaleAgreement::where('id', $id)->with(['client' => function ($query) {
            $query->with('preferred_contact_by');
        }])
            ->with(['booking' => function ($query) {
                $query->select('id', 'booking_no');
            }])
            ->with(['admin' => function ($query) {
                $query->select('id', 'display_name');
            }])
            ->with(['sale_agreement_item' => function ($query) {
                $query->with(['booking_line_item' => function ($q) {
                    // $q->select('id', 'booking_type_id', 'service_id', 'remarks', 'amount');
                    // $q->with('co_license');
                    $q->with('booking_type');
                    $q->with(['information' =>  function ($r) {
                        // $r->with('relationship_to_applicant');
                    }]);
                    $q->with(['niche' => function ($_q) {
                        // $_q->select('id', 'reference_no');
                    }]);
                    $q->with(['room' => function ($_q) {
                        $_q->select('id', 'room_no');
                    }]);
                    $q->with(['other' => function ($_q) {
                        $_q->select('id', 'service_name');
                    }]);
                }]);
            }])->first();
        $sale_agreement->sale_agreement_date =  date("d/m/Y", strtotime($sale_agreement->sale_agreement_date));
        $amount_discount = 0;
        $count = 0;
        foreach ($sale_agreement->sale_agreement_item as $key => $value) {
            $count++;
            $co_license = Reference::find($value->booking_line_item->co_license);

            $sale_agreement->sale_agreement_item[$key]->booking_line_item->co_license = "No";
            if (!empty($co_license)) {
                $sale_agreement->sale_agreement_item[$key]->booking_line_item->co_license = $co_license->reference_value_text;
            }
            foreach ($value->booking_line_item->information as $key_info => $value_info) {
                $relationship = Reference::find($value_info->relationship_to_applicant);
                $sale_agreement->sale_agreement_item[$key]->booking_line_item->information[$key_info]->relationship_to_applicant = '--';
                if (!empty($relationship)) {
                    $sale_agreement->sale_agreement_item[$key]->booking_line_item->information[$key_info]->relationship_to_applicant = $relationship->reference_value_text;
                }

                $sale_agreement->sale_agreement_item[$key]->booking_line_item->information[$key_info]->death_anniversary =  date("d/m/Y", strtotime($value_info->death_anniversary));
            }
            // $sale_agreement['discount'] = $value->booking_line_item->discount;

            $discount = $value->booking_line_item->discount;
        }

        $logo = url('/images/logo_tgor.png');
        if ($count >= 5 && $count <= 9 || $count == 16) {
            $html = view('exports.saleAgreement_2', compact('logo', 'sale_agreement', 'discount'))->render();
        } else {
            $html = view('exports.saleAgreement', compact('logo', 'sale_agreement', 'discount'))->render();
        }

        $pdf = App::make('dompdf.wrapper');
        $pdf = PDF::loadHTML($html);
        // $customPaper = array(0,0,593.92,1000.24);
        // $pdf->setPaper($customPaper);

        return $pdf->stream();

        // return $pdf->download('NicheLicence.pdf');
    }

    /**
     * @OA\Get(
     *     tags={"Sale Agreement"},
     *     path="/api/make-info-sale-agreement/{id}",
     *     summary="Make Info Sale Agreement",
     *     operationId="infoSaleAgreement",
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
    public function infoSaleAgreement(Request $request, $id)
    {
        $sale_agreement = SaleAgreement::where('id', $id)->with(['client' => function ($query) {
            $query->with('preferred_contact_by');
        }])
            ->with(['booking'])
            ->with(['admin' => function ($query) {
                $query->select('id', 'display_name');
            }])
            ->with(['sale_agreement_item' => function ($query) {
                $query->with(['booking_line_item' => function ($q) {
                    // $q->select('id', 'booking_type_id', 'service_id', 'remarks', 'amount');
                    $q->with([
                        'information', 'booking_type', 'niche', 'room', 'contractor', 'funeral_director',
                        'duration', 'relationship_with_license', 'co_license', 'coord_title', 'relationship_to_applicant', 'departed_title'
                    ]);
                    //     $_q->select('id', 'service_name');
                    // }]);
                }]);
            }])->first();
        return response()->json(
            [
                'status' => 'success',
                'data'   => $sale_agreement
            ],
            200
        );
    }


    /**
     * @OA\Get(
     *     tags={"Sale Agreement"},
     *     path="/api/print-document/{id}",
     *     summary="Print Document Sale Agreement",
     *     operationId="printDocument",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID Sale Agreement",
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
    public function printDocument($id)
    {
        $sale_agreement = SaleAgreement::where('id', $id)->first();
        if (!empty($sale_agreement)) {
            if ($sale_agreement->sa_type->reference_value_text == "Niches") {
                $sale_agreement = SaleAgreement::where('id', $id)->with(['client' => function ($query) {
                    $query->with('preferred_contact_by', 'salutation_name');
                }])
                    ->with(['admin' => function ($query) {
                        $query->select('id', 'display_name');
                    }])
                    ->with(['sale_agreement_item' => function ($query) {
                        $query->with(['booking_line_item' => function ($q) {
                            // $q->select('id', 'booking_type_id', 'service_id', 'remarks', 'amount', 'co_license');
                            $q->with('is_co_license', 'booking_type');
                            $q->with(['information' =>  function ($r) {
                                $r->with('is_relationship_to_applicant');
                            }]);
                            $q->with(['niche' => function ($_q) {
                                $_q->with("category");
                                $_q->select('id', 'reference_no', 'full_location', 'category_id');
                            }]);
                        }]);
                    }])->first();
                $logo = url('/images/logo_tgor.png');
                // echo json_encode($sale_agreement);die;
                // echo($sale_agreement);exit;
                $html = view('exports.niche-cover', compact('logo', 'sale_agreement'))->render();
                $pdf = App::make('dompdf.wrapper');
                $pdf = PDF::loadHTML($html);
                return $pdf->stream();
            } elseif ($sale_agreement->sa_type->reference_value_text == "Memorial Rooms") {
                $sale_agreement = SaleAgreement::where('id', $id)->with(['client' => function ($query) {
                    $query->with('preferred_contact_by', 'salutation_name');
                }])
                    ->with(['admin' => function ($query) {
                        $query->select('id', 'display_name');
                    }])
                    ->with(['sale_agreement_item' => function ($query) {
                        $query->with(['booking_line_item' => function ($q) {
                            $q->with('room');
                        }]);
                    }])->first();
                $logo = url('/images/logo_tgor.png');
                // echo json_encode($sale_agreement);die;
                // dd($sale_agreement->toArray());exit;
                $html = view('exports.parlour-rental', compact('logo', 'sale_agreement'))->render();
                $pdf = App::make('dompdf.wrapper');
                $pdf = PDF::loadHTML($html);
                return $pdf->stream();
            }
        }
    }

    /**
     * @OA\Get(
     *     tags={"Sale Agreement"},
     *     path="/api/send-document/{id}",
     *     summary="Send Document Sale Agreement",
     *     operationId="sendEmailAttachFileToClient",
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
    public function sendEmailAttachFileToClient($id)
    {
        $sale_agreement = SaleAgreement::find($id);
        if (!empty($sale_agreement)) {
            if ($sale_agreement->sale_agreement_item->booking_line_item->booking_type->reference_value_text == "Niches") {
                $sale_agreement = SaleAgreement::where('id', $id)->with(['client' => function ($query) {
                    $query->with('preferred_contact_by');
                }])
                    ->with(['admin' => function ($query) {
                        $query->select('id', 'display_name');
                    }])
                    ->with(['sale_agreement_item' => function ($query) {
                        $query->with(['booking_line_item' => function ($q) {
                            // $q->select('id', 'booking_type_id', 'service_id', 'remarks', 'amount', 'co_license');
                            $q->with('is_co_license', 'booking_type');
                            $q->with(['information' =>  function ($r) {
                                $r->with('is_relationship_to_applicant');
                            }]);
                            $q->with(['niche' => function ($_q) {
                                $_q->with("category");
                                $_q->select('id', 'reference_no', 'full_location', 'category_id');
                            }]);
                        }]);
                    }])->first();
                $logo = url('/images/logo_tgor.png');
                $html = view('exports.niche-cover', compact('logo', 'sale_agreement'))->render();
                $pdf = App::make('dompdf.wrapper');
                $pdf = PDF::loadHTML($html);

                $booking_type = Reference::where('reference_type', 'booking_type')
                    ->where('reference_value_text', 'Niches')
                    ->whereNull('deleted_at')->first();

                $count_booking_niches = SaleAgreement::with('client')
                    ->join('booking_line_items', 'booking_line_items.booking_id', '=', 'sale_agreements.booking_id')
                    ->where('sale_agreements.id', $id)
                    ->whereNull('sale_agreements.deleted_at')
                    ->where('booking_line_items.booking_type_id', $booking_type->id);
                // dd($count_booking_niches->get());

                if ($count_booking_niches->count() > 0) {
                    // dd($count_booking_niches->get());
                    $booking_niches = $count_booking_niches->get();

                    $html = '';
                    $logo = url('/images/logo_tgor.png');
                    $now = now();
                    $gst_rate = GSTRate::where('gst_start_date', '<=', $now->format('Y-m-d') . ' 00:00:00')
                        ->orderBy('gst_start_date', 'DESC')->first();
                    foreach ($booking_niches as $key => $value) {
                        $niche =  Niche::where('id', $value->id)->whereNull('deleted_at')->first();

                        $booking_niches[$key]->service_id = $niche;
                        // $booking_niches[$key]->start_date = date("j F Y", strtotime($value->start_date));
                        // $booking_niches[$key]->expiry_date = date("j F Y", strtotime($value->expiry_date));
                        // $booking_niches[$key]->countYear = date("Y", strtotime($value->expiry_date)) - date("Y", strtotime($value->start_date));
                        // $booking_niches[$key]->sale_agreement_date =  date("d/m/Y", strtotime($value->sale_agreement_date));
                        $booking_niches[$key]->start_date = Carbon::parse($value->lease_start_date)->format('j F Y');
                        $booking_niches[$key]->expiry_date = Carbon::parse($value->lease_expiry_date)->format('j F Y');
                        $booking_niches[$key]->countYear = Carbon::parse($value->lease_expiry_date)->format('Y') - Carbon::parse($value->lease_start_date)->format('Y');
                        $booking_niches[$key]->sale_agreement_date = Carbon::parse($value->sale_agreement_date)->format('d/m/Y');
                        $booking_niches[$key]->total = number_format($value->total, 2, '.', ',');
                        $booking_niches[$key]->tax_amount = number_format($value->tax_amount, 2, '.', ',');
                        $gst_value = 0;
                        if ($gst_rate) {
                            $gst_value = $gst_rate->rate;
                        }
                        $gst = $value->amount * $gst_value;
                        $booking_niches[$key]->gst_amount = number_format($gst, 2, '.', ',');
                        $sale_agreement = $booking_niches[$key];

                        $logo = url('/images/logo_tgor.png');
                        $html = view('exports.niches-licence', compact('logo', 'sale_agreement'))->render();
                    }
                    
                    $pdf1 = App::make('dompdf.wrapper');
                    $pdf1 = PDF::loadHTML($html);
                    Storage::disk('public')->put('NicheLicence.pdf', $pdf1->output());
                    $contents2 = storage_path('app/public/NicheLicence.pdf');
                }
                Storage::disk('public')->put('sale_agreement.pdf', $pdf->output());
                $contents = storage_path('app/public/sale_agreement.pdf');
                $client = $sale_agreement->client;
                dispatch(new SendEmailToClient($contents, 'The Garden of Remembrance – Niche Documents', $client, $contents2, 'niche documents'));
            } elseif ($sale_agreement->sale_agreement_item->booking_line_item->booking_type->reference_value_text == "Memorial Rooms") {
                $sale_agreement = SaleAgreement::where('id', $id)->with(['client' => function ($query) {
                    $query->with('preferred_contact_by');
                }])
                    ->with(['admin' => function ($query) {
                        $query->select('id', 'display_name');
                    }])
                    ->with(['sale_agreement_item' => function ($query) {
                        $query->with(['booking_line_item' => function ($q) {
                            $q->with('room');
                        }]);
                    }])->first();
                $logo = url('/images/logo_tgor.png');
                // var_dump($sale_agreement->toArray());exit;
                $html = view('exports.parlour-rental', compact('logo', 'sale_agreement'))->render();
                $pdf = App::make('dompdf.wrapper');
                $pdf = PDF::loadHTML($html);
                Storage::disk('public')->put('sale_agreement.pdf', $pdf->output());
                $contents = storage_path('app/public/sale_agreement.pdf');
                $client = $sale_agreement->client;
                dispatch(new SendEmailToClient($contents, 'The Garden of Remembrance – Memorial Room Documents', $client, null,'memorial room documents'));
            }
            return response()->json([
                'status' => "Successfully Sended Email To Client"
            ]);
        }
    }
    public function handleTotalSaleAgreement(Request $request){
        $get_line = BookingLineItems::where('id', $request->id)->get();
        $amount = 0;
        $gst_amount = 0;
        $total = 0;
        $discount = 0;
        if(!empty($get_line)){
            foreach($get_line as $key => $value){
                $amount += (float)$value['amount'];
                $gst_amount += (float)$value['tax_amount'];
                $discount += (float)$value['discount_amount'];
                $total = ($amount + $gst_amount) - $discount;
            }
        }
        SaleAgreement::where('id', $request->invoice_id)->update([
            "total_amount" => $amount,
            "total_tax_amount" => $gst_amount,
            "total_discount" => $discount,
            "total" => $total
        ]);
    }
}
