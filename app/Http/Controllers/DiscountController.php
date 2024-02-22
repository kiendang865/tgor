<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Discount;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Common\Type;
use App\Reference;
use Illuminate\Database\Eloquent\Builder;
use App\Niche;
class DiscountController extends Controller
{
    /**
 * @OA\Get(
 *     tags={"Discount"},
 *     path="/api/discount-niche",
 *     summary="Get list Discount",
 *     operationId="index",
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
    public function index(Request $request){
        $limit = intval($request->query('limit'));
        $discount = Discount::where('id', '>', 0)->where('is_custom', 0);
        $filter = json_decode($request->query('filter'));
        // Where all
        // var_dump($filter->all);exit;
        if (!empty($filter->all)) {
            $key_word = $filter->all;
            $discount->where('discount_code','like', "%".$key_word."%")
                    ->orWhereHas('type_discount', function (Builder $query) use ($key_word) {
                        $query->where('reference_value_text','like', "%".$key_word."%");
                    })
                    ->orWhereHas('type', function (Builder $query) use ($key_word) {
                        $query->where('reference_value_text',   'like', "%".$key_word."%");
                    })
                    ->orWhereHas('category', function (Builder $query) use ($key_word) {
                        $query->where('reference_value_text', 'like', '%'.$key_word.'%');
                    })
                    ->orWhere('minimum_qty',   'like', "%".$key_word."%")
                    ->orWhere('amount',     'like', "%".$key_word."%")
                    ->orWhere('remarks',     'like', "%".$key_word."%");
        }
        // Where id
        if (!empty($filter->discount_code)) {
            $discount->where('discount_code','like', "%".$filter->discount_code."%");
        }
        // Where type
        if (!empty($filter->discount_type)) {
            $key_word = $filter->discount_type;
            $discount->WhereHas('type_discount', function (Builder $query) use ($key_word) {
                $query->where('reference_value_text','like', "%".$key_word."%");
            });        
        }
        // Where location
        if (!empty($filter->type)) {
            $key_word = $filter->type;
            $discount->WhereHas('type', function (Builder $query) use ($key_word) {
                $query->where('reference_value_text',   'like', "%".$key_word."%");
            });
        }
        // Where status
        if (!empty($filter->minimum_qty)) {
            $discount->where('minimum_qty', '=', $filter->minimum_qty);
        }
        //where category
        if (!empty($filter->amount)) {
            $discount->where('amount', 'like', "%".$filter->amount."%");
        }

        if (!empty($filter->remarks)) {
            $remarks = $filter->remarks;
            $discount->where('remarks',     'like', "%".$remarks."%");
        }

        if (!empty($filter->category)) {
            $key_word = $filter->category;
            $discount->WhereHas('category', function (Builder $query) use ($key_word) {
                $query->where('reference_value_text', 'like', '%'.$key_word.'%');
            });
        }
        
        $discount = $discount->with('type_amount','category','type','type_discount')->paginate($limit)->toArray();
        $discount['status'] = "success"; 
        return response()->json($discount, 200);
    }
    /**
 * @OA\POST(
 *     tags={"Discount"},
 *     path="/api/discount-niche",
 *     summary="Create Discount",
 *     operationId="store",
 *     @OA\Parameter(
 *         name="discount_code",
 *         in="query",
 *         description="Discount Code",
 *         @OA\Schema(
 *             type="string",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="discount_type",
 *         in="query",
 *         description="Discount Type",
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="type",
 *         in="query",
 *         description="Niche Type",
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
*     @OA\Parameter(
 *         name="minimum_qty",
 *         in="query",
 *         description="Minimum Qty",
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="amount",
 *         in="query",
 *         description="Amount",
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
  *     @OA\Parameter(
 *         name="remarks",
 *         in="query",
 *         description="Remarks",
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
  *     @OA\Parameter(
 *         name="category",
 *         in="query",
 *         description="Niche Category",
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="amount_type",
 *         in="query",
 *         description="Amount Type",
 *         @OA\Schema(
 *             type="integer"
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
    public function store(Request $request)
    {   
        if($request->discount_service === "Niches"){
            $v = Validator::make($request->all(), [
                'discount_code'        => 'required',
                'discount_type'        => 'required',
                'type'                 => 'required',
                'minimum_qty'          => 'required',
                'amount'               => 'required',
                'category'             => 'required',
                'amount_type'          => 'required' 
            ]);
        }
        if($request->discount_service === "Rooms"){
            $v = Validator::make($request->all(), [
                'discount_code'        => 'required',
                'discount_type'        => 'required',
                'amount'               => 'required',
                'room_id'              => 'required',
                'amount_type'          => 'required' 
            ]); 
        }
        if($request->discount_service === "Additional Services"){
            $v = Validator::make($request->all(), [
                'discount_code'        => 'required',
                'discount_type'        => 'required',
                'amount'               => 'required',
                'other_id'             => 'required',
                'amount_type'          => 'required',
                'type'                 => 'required',
            ]); 
        }

        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()->first()
            ], 422);
        }
        $discount = Discount::create($request->all());
        if($discount) {
            $discount =  Discount::with('type_amount','category','type','type_discount')->find($discount->id);
            return response()->json([
                'status' => 'Successfully Added Discount',
                'data'   => $discount
            ], 200);
        }
        else {
            return response()->json([
                'status' => 'error',
                'errors' => "Something bad happened, please try later"
            ], 422);
        }
    }
   /**
 * @OA\PUT(
 *     tags={"Discount"},
 *     path="/api/discount-niche",
 *     summary="Update Discount",
 *     operationId="update",
 *     @OA\Parameter(
 *         name="discount_code",
 *         in="query",
 *         description="Discount Code",
 *         @OA\Schema(
 *             type="string",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="discount_type",
 *         in="query",
 *         description="Discount Type",
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="type",
 *         in="query",
 *         description="Niche Type",
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
*     @OA\Parameter(
 *         name="minimum_qty",
 *         in="query",
 *         description="Minimum Qty",
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="amount",
 *         in="query",
 *         description="Amount",
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
  *     @OA\Parameter(
 *         name="remarks",
 *         in="query",
 *         description="Remarks",
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
  *     @OA\Parameter(
 *         name="category",
 *         in="query",
 *         description="Niche Category",
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="amount_type",
 *         in="query",
 *         description="Amount Type",
 *         @OA\Schema(
 *             type="integer"
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
    public function update(Request $request, $id)
    {
        if($request->discount_service === "Niches"){
            $v = Validator::make($request->all(), [
                'discount_code'        => 'required',
                'discount_type'        => 'required',
                'type'           => 'required',
                'minimum_qty'          => 'required',
                'amount'               => 'required',
                'category'             => 'required',
                'amount_type'          => 'required' 
            ]);
        }
        if($request->discount_service === "Rooms"){
            $v = Validator::make($request->all(), [
                'discount_code'        => 'required',
                'discount_type'        => 'required',
                'amount'               => 'required',
                'room_id'              => 'required',
                'amount_type'          => 'required' 
            ]); 
        }
        if($request->discount_service === "Additional Services"){
            $v = Validator::make($request->all(), [
                'discount_code'        => 'required',
                'discount_type'        => 'required',
                'amount'               => 'required',
                'other_id'             => 'required',
                'amount_type'          => 'required',
                'type'                 => 'required',
            ]); 
        }
        
        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()->first()
            ], 422);
        }
        $discount = Discount::where('id', $id)->first();
        if(!$discount) {
            return response()->json([
                'status' => 'error',
                'errors' => "Cannot find niche"
            ], 404);
        } 
        $discount = $discount->update($request->all());
        if($discount) {
            $discount =  Discount::with('type_amount','category','type','type_discount')->find($id);
            return response()->json([
                'status' => 'Successfully Updated Discount',
                'data'   => $discount
            ], 200);
        }
        else {
            return response()->json([
                'status' => 'error',
                'errors' => "Something bad happened, please try later"
            ], 422);
        }
    }
    /**
 * @OA\Delete(
 *     tags={"Discount"},
 *     path="/api/discount-niche",
 *     summary="Delete Discount",
 *     operationId="delete",
 *      @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType="application/x-www-form-urlencoded",
 *             @OA\Schema(
 *                 type="object",
 *                 @OA\Property(
 *                     property="ids",
 *                     description="ID Niches",
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
    if(is_string($ids)){
        $ids = json_decode($ids);
    }
    $niches = Discount::whereIn('id', $ids)->delete();

    if($niches) {
        return response()->json(
            [
                'status' => 'Successfully Deleted Discount',
            ], 200);
    }
    return response()->json(
        [
            'status' => 'error',
            'errors' => 'Cannot find niche'
        ], 404);
}
/**
* @OA\Get(
*     tags={"Discount"},
*     path="/api/discount-detail/{id}",
*     summary="Get detail Discount",
*     operationId="detail",
*     @OA\Parameter(
*         name="id",
*         in="path",
*         required=true,
*         description="ID Niches",
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

public function detail($id)
{
    $discount =  Discount::with('type_amount','category','type','type_discount', 'service_type', 'room', 'service')->find($id);
    if($discount) {
        return response()->json(
            [
                'status' => 'success',
                'data' => $discount->toArray()
            ], 200);
    }
    return response()->json(
        [
            'status' => 'error',
            'errors' => 'Cannot find niche'
        ], 404);
}
/**
 * @OA\Post(
 *     tags={"Discount"},
 *     path="/api/import-discount",
 *     summary="Import file Discount excel",
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
public function importDiscount(Request $request){
    ini_set('max_execution_time', 3000);
    $file = $request->file("file_excel");
    if(!empty($file)){
        $reader = ReaderEntityFactory::createXLSXReader();
        $reader->setShouldFormatDates(true);
        $reader->open($file);
        $info = [];
        foreach ($reader->getSheetIterator() as $sheet) {
            if($sheet->getName() == "Discount"){
                foreach ($sheet->getRowIterator() as $key=>$row) {
                    
                    $cells = $row->getCells();
                    $flag = $cells[0]->getValue();
                    if(!($flag === "Discount Code")){
                        if(!($flag === " " || $flag == null)){
                            
                            if($this->checkNullValue($cells[0]->getValue()) == "Discount - None")
                            {
                                continue;
                            }
                            else{
                                $discount_code = $this->checkNullValue($cells[0]->getValue());
                                $discount_type = $this->checkNullValue($cells[1]->getValue());
                                $niche_category = $this->checkNullValue($cells[2]->getValue());
                                $niche_type = $this->checkNullValue($cells[3]->getValue());
                                $minimum_qty =  $this->checkNullValue($cells[4]->getValue());
                                $amount = $this->checkNullValue($cells[5]->getValue());
                                $remarks = '';
                                if(count($cells) == 7)
                                {
                                    $remarks = $this->checkNullValue($cells[6]->getValue());
                                }
                                
                                
                                $discount = [
                                    'discount_code'        => $discount_code,
                                    'discount_type'        => $discount_type,
                                    'type'                 => $niche_type,
                                    'minimum_qty'          => $minimum_qty,
                                    'amount'               => $amount,
                                    'remarks'              => $remarks,
                                    'category'             => $niche_category,
                                ];
                                array_push($info, $discount);
                            
                            }
                        }
                    } 
                }
                // var_dump($info);exit;
                // var_dump($date->format('Y-m-d'));exit;
            }
        } 
        $reader->close();

        foreach($info as $key => $value){ 
            
            $discount_type = Reference::where(
                [
                    ['reference_type', 'like', 'discount_type'],
                    ['reference_value_text', 'like', $value['discount_type']]
                ])->first();

            $niche_type = Reference::where(
                [
                    ['reference_type', 'like', 'type_niche'],
                    ['reference_value_text', 'like', $value['type']]
                ])->first();    
            
            $category_niche = Reference::where(
                [
                    ['reference_type', 'like', 'category_niche'],
                    ['reference_value_text', 'like', $value['category']]
                ])->first();   
            $amount_type = Reference::where(
                [
                    ['reference_type', 'like', 'amount_type'],
                    ['reference_value_text', 'like', 'Value']
                ])->first();     

                $disc = new Discount();
                $disc->discount_code = $value['discount_code'];
                $a = $discount_type->id;
                // var_dump($a);exit;
                $disc->discount_type = $a;

                if(empty($niche_type)){
                    $nichesRef = new Reference();
                    $nichesRef->reference_type = 'type_niche';
                    $nichesRef->reference_value_text = $value['type'];
                    $nichesRef->save();
                    $disc->type = $nichesRef->id;
                }  
                else{
                    $disc->type = $niche_type->id;
                }   

               
                $disc->minimum_qty = $value['minimum_qty'];


                if(empty($category_niche)){
                    $nichesRef = new Reference();
                    $nichesRef->reference_type = 'category_niche';
                    $nichesRef->reference_value_text = $value['category'];
                    $nichesRef->save();
                    $disc->category = $nichesRef->id;
                }  
                else{
                    $disc->category = $category_niche->id;
                }   
                
                $disc->remarks = $value['remarks'];
                $amount = $value['amount'];
                $disc->amount = $amount;
                $findme   = '.';
                $pos = strpos($amount, $findme); 
                if($pos === 1){
                    $amount_type = Reference::where(
                        [
                            ['reference_type', 'like', 'amount_type'],
                            ['reference_value_text', 'like', 'Percentage']
                        ])->first();   
                        $disc->percent =  $amount;  
                        $disc->amount = ($amount*100)."%";
                }

                $disc->amount_type = $amount_type->id;

                $disc->save();
        }
        return response()->json(
        [
            'status' => 'Successfully import file',
        ], 200);
    }
}
public static function checkNullValue($value){
    if($value == "" || $value == " "){
        $value = null;
    }
    return $value;
}
/**
 * @OA\Post(
 *     tags={"Discount"},
 *     path="/api/import-price-niches",
 *     summary="Import file Price Niches excel",
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
public function importPriceNiches(Request $request){
    ini_set('max_execution_time', 3000);
    $file = $request->file("file_excel");
    if(!empty($file)){
        $reader = ReaderEntityFactory::createXLSXReader();
        $reader->setShouldFormatDates(true);
        $reader->open($file);
        $info = [];
        foreach ($reader->getSheetIterator() as $sheet) {
            if($sheet->getName() == "Niche Prices"){
                foreach ($sheet->getRowIterator() as $key=>$row) {
                    
                    $cells = $row->getCells();
                    $flag = $cells[0]->getValue();
                    if(!($flag === "Price Code")){
                        if(!($flag === " " || $flag == null)){
                            
       
                            $category = $this->checkNullValue($cells[1]->getValue());
                            $wing = $this->checkNullValue($cells[2]->getValue());
                            $type = $this->checkNullValue($cells[3]->getValue());
                            $level = $this->checkNullValue($cells[4]->getValue());
                            $price =  $this->checkNullValue($cells[5]->getValue());
                            $renew_price = $this->checkNullValue($cells[6]->getValue());
                            
                            
                            $niches = [
                                'category'       => $category,
                                'wing'           => $wing,
                                'type'           => $type,
                                'level'          => $level,
                                'price'          => $price,
                                'renew_price'    => $renew_price,
                            ];
                            array_push($info, $niches);
                        }
                    } 
                }
                // var_dump($info);exit;
                // var_dump($date->format('Y-m-d'));exit;
            }
        } 
        $reader->close();

        foreach($info as $key => $value){ 

            $niche_type = Reference::where(
                [
                    ['reference_type', 'like', 'type_niche'],
                    ['reference_value_text', 'like', $value['type']]
                ])->first();    
            
            $category_niche = Reference::where(
                [
                    ['reference_type', 'like', 'category_niche'],
                    ['reference_value_text', 'like', $value['category']]
                ])->first();   
           $listNiche = Niche::where([
               ['category_id',$category_niche->id],
               ['type_id',$niche_type->id],
               ['wing',$value['wing']],
               ['level',$value['level']]
           ])->get();

           foreach($listNiche as $key_niche => $val_niche)
           {
                $val_niche->price = $value['price'];
                if(!empty($value['renew_price']))
                {
                    $val_niche->renew_price = $value['renew_price'];
                }
                
                $val_niche->save();
           }
        }
        return response()->json(
        [
            'status' => 'Successfully import file',
        ], 200);
    }
}
}
