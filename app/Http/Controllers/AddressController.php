<?php

namespace App\Http\Controllers;

use App\Address;
use Illuminate\Http\Request;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\Validator;

class AddressController extends Controller
{
    /**
 * @OA\Post(
 *     tags={"Address"},
 *     path="/api/import-address",
 *     summary="Import file Address excel",
 *      @OA\RequestBody(
 *          @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 type="object",
 *                 @OA\Property(
 *                     property="file_excel",
 *                     description="file_excel Address",
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
    public function importAddress(Request $request){
        ini_set('max_execution_time', 3000);
        $file = $request->file("file_excel");
        if(!empty($file)){
            $reader = ReaderEntityFactory::createXLSXReader();
            $reader->setShouldFormatDates(true);
            $file = public_path("/Singapore Address_02.xlsx");
            $reader->open($file);
            foreach($reader->getSheetIterator() as $sheet) {
                if($sheet->getName() == "Addresses"){
                    foreach ($sheet->getRowIterator() as $key=>$row) {
                        $cells = $row->getCells();
                        $flag = $cells[0]->getValue();
                        if(!($flag === "Postal Code")){
                            if(!($flag === " " || $flag == null)){
                                $arr_address = array();
                                $postal_code        = BookingController::checkNullValue($cells[4]->getValue());
                                $blk_no             = BookingController::checkNullValue($cells[1]->getValue());
                                $street_name        = BookingController::checkNullValue($cells[2]->getValue());
                                $building_name      = BookingController::checkNullValue($cells[3]->getValue());
                                if(!empty($blk_no)){
                                    array_push($arr_address, $blk_no);
                                }
                                if(!empty($street_name)){
                                    array_push($arr_address, $street_name);
                                }
                                if(!empty($building_name)){
                                    array_push($arr_address, $building_name);
                                }
                                $address = implode(", ",$arr_address);
                                Address::create(['postal_code'=>$postal_code, 'address'=>$address]);
                            }
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
                'message' => 'Not file'
            ], 404);
    }

    /**
 * @OA\Post(
 *     tags={"Address"},
 *     path="/api/create-address",
 *     summary="Create Address",
 *     operationId="createAddress",
 *    @OA\Parameter(
 *         name="postal_code",
 *         in="query",
 *         description="postal_code",
 *          @OA\Schema(
 *              type="string"
 *          )
 *     ),
 *    @OA\Parameter(
 *         name="address",
 *         in="query",
 *         description="address",
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
    public function createAddress(Request $request){
        $v = Validator::make($request->all(), [
            'postal_code'          => 'required|digits:6',
            'address'               => 'required|max:191',
        ]);

        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()->first()
            ], 422);
        }
        $check_postal_code = Address::where('postal_code', $request->postal_code)->first();
        if(!empty($check_postal_code)){
            return response()->json([
                'status' => 'error',
                'errors' => "Address were duplicate."
            ], 422);
        }
        $address = Address::create($request->all());
        if($address) {
            $address =  Address::find($address->id);
            return response()->json([
                'status' => 'Successfully Added Address',
                'data'   => $address
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
 * @OA\Get(
 *     tags={"Address"},
 *     path="/api/address",
 *     summary="Get list Address",
 *     operationId="getListAddress",
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
 *    @OA\Parameter(
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

    public function getListAddress(Request $request){
        $limit = intval($request->query('limit'));
        $filter = json_decode($request->query('filter'));
        $listAddress = Address::where('id', '>', 0);
        if(!empty($filter)){
            if (!empty($filter->all)) {
                $key_word = $filter->all;
                $listAddress->where(function ($query) use ($key_word) {
                    $query->orWhere('postal_code','=',$key_word)
                    ->orWhere('address', 'like', '%'.$key_word.'%');
                });
            }
            if(!empty($filter->postal_code)){
            $listAddress->where('postal_code', '=', $filter->postal_code);
            }
            if(!empty($filter->address)){
                $listAddress->where('address', 'like', '%'.$filter->address.'%');
            }
        }
        $listAddress = $listAddress->paginate($limit)->toArray();  
        return response()->json([
            'status' => 'success',
            'data' => $listAddress
        ], 200);
    }

/**
 * @OA\Get(
 *     tags={"Address"},
 *     path="/api/detail-address/{id}",
 *     summary="Detail Address",
 *     operationId="deatilAddress",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="id address",
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

    public function deatilAddress($id){
        $address = Address::find($id);
        if($address){
            return response()->json([
                'status' => 'success',
                'data' => $address->toArray()
            ], 200);
        }
        return response()->json([
            'status' => 'error',
            'data' => 'Something bad happened, please try later'
        ], 422);
    }

/**
 * @OA\Put(
 *     tags={"Address"},
 *     path="/api/update-address/{id}",
 *     summary="Update Address",
 *     operationId="updateAddress",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="id address",
 *         @OA\Schema(
 *             type="integer",
 *             format="int64",
 *             minimum=1,
 *         )
 *     ),
 *    @OA\Parameter(
 *         name="postal_code",
 *         in="query",
 *         description="postal_code",
 *          @OA\Schema(
 *              type="string"
 *          )
 *     ),
 *    @OA\Parameter(
 *         name="address",
 *         in="query",
 *         description="address",
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
    public function updateAddress(Request $request, $id){
        $v = Validator::make($request->all(), [
            'postal_code'          => 'required|digits:6',
            'address'               => 'required|max:191',
        ]); 

        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()->first()
            ], 422);
        }
        $address = Address::where('id', $id)->first();
        $check_postal_code = Address::where('id', '<>', $address)
                            ->where('postal_code', $request->postal_code)
                            ->first();
        if(!empty($check_postal_code)){
            return response()->json([
                'status' => 'error',
                'errors' => "Address were duplicate."
            ], 422);
        }
        if($address){
            $address->postal_code = $request->postal_code;
            $address->address     = $request->address;
            $address->save();
            if($address){
                return response()->json([
                    'status' => 'Successfully Update Address.',
                    'data' => $address
                ], 200);
            }
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Address not found'
        ], 404);
    }
/**
 * @OA\Delete(
 *     tags={"Address"},
 *     path="/api/delete-address",
 *     summary="Delete Address",
 *     operationId="deleteAddress",
 *      @OA\RequestBody(
*         @OA\MediaType(
*             mediaType="application/x-www-form-urlencoded",
*             @OA\Schema(
*                 type="object",
*                 @OA\Property(
*                     property="ids",
*                     description="ID Address",
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

    public function deleteAddress(Request $request){
        $ids = $request->ids;
        if(is_string($ids)){
            $ids = json_decode($ids);
        }
        $delete_address = Address::whereIn('id', $ids)->delete();
        if($delete_address) {
            return response()->json(
                [
                    'status' => 'successfully',
                    'message' => 'Successfully Deleted'
                ], 200);
        }
        return response()->json(
            [
                'status' => 'error',
                'message' => 'Cannot find Address'
            ], 404);
    }
/**
 * @OA\Get(
 *     tags={"Address"},
 *     path="/api/find-address",
 *     summary="find Address with potal code",
 *     operationId="findAddress",
 *     @OA\Parameter(
 *         name="postal_code",
 *         in="query",
 *         required=true,
 *         description="postal_code",
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
    public function findAddress(Request $request){
        $v = Validator::make($request->all(), [
            'postal_code'          => 'required',
        ]); 
        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()->first()
            ], 422);
        }
        $postal_code = $request->postal_code;
        $address = Address::where('postal_code', '=', $postal_code)->first();  
        return response()->json([
            'status' => 'success',
            'data' => $address
        ], 200);
    }

}
