<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\NicheReserved;
use Carbon\Carbon;
use App\Niche;
use App\Http\Controllers\BookingController;

class NicheReservedController extends Controller
{
/**
* @OA\Post(
*     tags={"Niche Reserved"},
*     path="/api/niche-reserved",
*     summary="Create Niche Reserved",
*     operationId="create",
*     @OA\Parameter(
*         name="reserved_date",
*         in="query",
*         required=true,
*         description="Reserved Date",
*         @OA\Schema(
*             type="string",
*         )
*     ),
*     @OA\Parameter(
*         name="niche_id",
*         in="query",
*         required=true,
*         description="Niche Id",
*         @OA\Schema(
*             type="integer",
*         )
*     ),
*     @OA\Parameter(
*         name="customer_name",
*         in="query",
*         required=true,
*         description="Customer Name",
*         @OA\Schema(
*             type="string",
*         )
*     ),
*     @OA\Parameter(
*         name="mobile",
*         in="query",
*         required=true,
*         description="Mobile",
*         @OA\Schema(
*             type="string",
*         )
*     ),
*     @OA\Parameter(
*         name="email",
*         in="query",
*         required=true,
*         description="Email",
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
    public function create(Request $request){
        $v = Validator::make($request->all(), [
            'reserved_date'     =>  'required',
            'niche_id'          =>  'required',
            'customer_name'     =>  'required',
            'mobile'            =>  'required',
            'email'             =>  'required'
        ]);
        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()->first()
            ], 422);
        }
        $niche_reserved = NicheReserved::create([
            'reserved_date' => $request->reserved_date ? Carbon::parse($request->reserved_date) : null,
            'niche_id'      => $request->niche_id ? $request->niche_id : null,
            'customer_name'      => $request->customer_name ? $request->customer_name : null,
            'mobile'      => $request->mobile ? $request->mobile : null,
            'email'      => $request->email ? $request->email : null,
        ]);
        if($niche_reserved){
            Niche::where('id', $request->niche_id)->update(['status' => 'Reserved']);
        }
        return response()->json([
            'status' => 'Successfully Added Niche Reserved',
            'data'   => $niche_reserved
        ]);
    }

/**
* @OA\Put(
*     tags={"Niche Reserved"},
*     path="/api/niche-reserved/{id}",
*     summary="Update Niche Reserved",
*     operationId="update",
*     @OA\Parameter(
*         name="id",
*         in="path",
*         required=true,
*         description="ID Niche Reserved",
*         @OA\Schema(
*             type="integer",
*             format="int64",
*             minimum=1,
*         )
*     ),
*     @OA\Parameter(
*         name="reserved_date",
*         in="query",
*         required=true,
*         description="Reserved Date",
*         @OA\Schema(
*             type="string",
*         )
*     ),
*     @OA\Parameter(
*         name="niche_id",
*         in="query",
*         required=true,
*         description="Niche Id",
*         @OA\Schema(
*             type="integer",
*         )
*     ),
*     @OA\Parameter(
*         name="customer_name",
*         in="query",
*         required=true,
*         description="Customer Name",
*         @OA\Schema(
*             type="string",
*         )
*     ),
*     @OA\Parameter(
*         name="mobile",
*         in="query",
*         required=true,
*         description="Mobile",
*         @OA\Schema(
*             type="string",
*         )
*     ),
*     @OA\Parameter(
*         name="email",
*         in="query",
*         required=true,
*         description="Email",
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
    public function update(Request $request, $id){
        $v = Validator::make($request->all(), [
            'reserved_date'     =>  'required',
            'niche_id'          =>  'required',
            'customer_name'     =>  'required',
            'mobile'            =>  'required',
            'email'             =>  'required'
        ]);
        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()->first()
            ], 422);
        }
        $niche_reserved = NicheReserved::find($id);
        if(empty($niche_reserved)){
            return response()->json(
                [
                    'status' => 'error',
                    'errors' => 'Cannot find Niche Reserved'
                ], 404);
        }
        if($niche_reserved->niche_id !== $request->niche_id){
            Niche::where('id', $niche_reserved->niche_id)->update(['status' => 'Available']);
            Niche::where('id', $request->niche_id)->update(['status' => 'Reserved']);
        }
        $update_niche_reserved = $niche_reserved->update([
            'reserved_date' => $request->reserved_date ? Carbon::parse($request->reserved_date) : null,
            'niche_id'      => $request->niche_id ? $request->niche_id : null,
            'customer_name'      => $request->customer_name ? $request->customer_name : null,
            'mobile'      => $request->mobile ? $request->mobile : null,
            'email'      => $request->email ? $request->email : null,
        ]);
        if($update_niche_reserved){
            return response()->json([
                'status' => 'Successfully Update Niche Reserved',
                'data'   => $niche_reserved
            ]);
        }
        
    }

/**
* @OA\Delete(
*     tags={"Niche Reserved"},
*     path="/api/niche-reserved",
*     summary="Delete Niches",
*     operationId="delete",
*      @OA\RequestBody(
*         @OA\MediaType(
*             mediaType="application/x-www-form-urlencoded",
*             @OA\Schema(
*                 type="object",
*                 @OA\Property(
*                     property="ids",
*                     description="ID Niche Reserved",
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

        $niche_reserved = NicheReserved::whereIn('id', $ids)->pluck('niche_id');
        // var_dump($niche_reserved->all());exit;
        Niche::whereIn('id', $niche_reserved->all())->update(['status' => 'Available']);
        $delete_niche_reserved = NicheReserved::whereIn('id', $ids)->delete();
        if($delete_niche_reserved) {
            return response()->json(
                [
                    'status' => 'Successfully Deleted Niches Reserved',
                ], 200);
        }
        return response()->json(
            [
                'status' => 'error',
                'errors' => 'Cannot find Niches Reserved'
            ], 404);
    }

/**
* @OA\Get(
*     tags={"Niche Reserved"},
*     path="/api/niche-reserved",
*     summary="Get list Niche Reserved",
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
*         description="Filter by title",
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

    //list report
    public function index(Request $request){
        $limit      = intval($request->query('limit'));
        $niche_reserved     = NicheReserved::where('id', '>', 0);
        $filter     = json_decode($request->query('filter'));
        $type_1 = "d/m/Y";
        $type_2 = "d/m";
        $type_expectations_1 = "Y-m-d";
        $type_expectations_2 = "m-d";
        if(!empty($filter->all)){
            $key_word = $filter->all;
            $key_word = BookingController::custom_date($type_1, $type_2, $key_word, $type_expectations_1, $type_expectations_2);
            $niche_reserved->where(function ($query) use ($key_word) {
                $query->whereHas('niche', function($q) use ($key_word){
                            $q->where('reference_no', 'like', '%'.$key_word.'%');
                        })
                        ->orWhere('mobile', 'like', "%".$key_word."%")
                        ->orWhere('customer_name', 'like', '%'.$key_word.'%')
                        ->orWhere('email', 'like', '%'.$key_word.'%')
                        ->orWhere('reserved_date', 'like', '%'.$key_word.'%');
            });
        }
        if(!empty($filter->niche_ref)){
            $key_word = $filter->niche_ref;
            $niche_reserved->whereHas('niche', function($query) use ($key_word){
                $query->where('reference_no', 'like', '%'.$key_word.'%');
            });
        }
        if(!empty($filter->customer_name)){
            $key_word = $filter->customer_name;
            $niche_reserved->where('customer_name', 'like', '%'.$key_word.'%');
        }
        if(!empty($filter->mobile)){
            $key_word = $filter->mobile;
            $niche_reserved->where('mobile', 'like', '%'.$key_word.'%');
        }
        if(!empty($filter->email)){
            $key_word = $filter->email;
            $niche_reserved->where('email', 'like', '%'.$key_word.'%');
        }
        if(!empty($filter->reserved_date)){
            $key_word = $filter->reserved_date;
            $key_word = BookingController::custom_date($type_1, $type_2, $key_word, $type_expectations_1, $type_expectations_2);
            $niche_reserved->where('reserved_date', 'like', '%'.$key_word.'%');
        }
        $niche_reserved = $niche_reserved->with('niche')->paginate($limit)->toArray();
        return response()->json($niche_reserved, 200);
    }
    /**
 * @OA\Get(
 *     tags={"Niche Reserved"},
 *     path="/api/niche-reserved/{id}",
 *     summary="Get detail Niche Reserved",
 *     operationId="show",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID Reserved",
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

    public function show($id)
    {
        $niche_reserved = NicheReserved::with('niche')->find($id);

        if($niche_reserved) {   
            return response()->json(
                [
                    'status'        => 'success',
                    'data'          => $niche_reserved->toArray()
                ], 200);
        }
        return response()->json(
            [
                'status' => 'error',
                'errors' => 'Cannot find report'
            ], 404);
    }
}
