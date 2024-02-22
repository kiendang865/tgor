<?php

namespace App\Http\Controllers;

use App\Directors;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Validator;
use App\User;

class DirectorsController extends Controller
{

/**
 * @OA\Get(
 *      tags={"Directors"},
 *      path="/api/directors",
 *      summary="Get list Directors",
 *      operationId="index",
 *       @OA\Parameter(
 *         name="limit",
 *         in="query",
 *         description="limit page",
 *         @OA\Schema(
 *             type="integer",
 *             format="int64",
 *             minimum=1,
 *         )
 *     ),
 *      @OA\Parameter(
 *         name="page",
 *         in="query",
 *         description="page",
 *         @OA\Schema(
 *             type="integer",
 *             format="int64",
 *             minimum=1,
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="filter",
 *         in="query",
 *         description="filter by object json",
 *          @OA\Schema(
 *              type="string"
 *          )
 *     ),
 *      @OA\Response(
 *          response=200,
 *          description="Successful operation",
 *      ),
 *      @OA\Response(
 *          response=401,
 *          description="Unauthorized",
 *      ),
 *      @OA\Response(
 *          response=500,
 *          description="Error server",
 *      ),
 *      security={
 *          {"bearerAuth": {}}
 *      }
 * )
 */

    public function indexDirectors(Request $request)
    {   
        $limit = intval($request->query('limit'));
        $directors = Directors::where('id', '>', 0)->where('is_contractor', false);
        $filter = json_decode($request->query('filter'));
        
        if (!empty($filter->company_name)) {
            $directors->where('company_name', 'like', '%'.$filter->company_name.'%');
        }
        if (!empty($filter->address)) {
            $directors->where('address', 'like', '%'.$filter->address.'%');
        }
        if (!empty($filter->website)) {
            $directors->where('website', 'like', '%'.$filter->website.'%');
        }
        if (!empty($filter->all)) {
            $key_word = $filter->all;
            $directors->where(function ($query) use ($key_word) {
                $query->where('company_name',  'like', "%".$key_word."%")
                        ->orWhere('address',  'like', "%".$key_word."%")
                        ->orWhere('website',  'like', "%".$key_word."%");
            });
        }
        
        $directors = $directors->paginate($limit)->toArray();
        $directors['status'] = "success"; 
        return response()->json($directors, 200);
    }

/**
* @OA\Get(
*     tags={"Directors"},
*     path="/api/directors/{id}",
*     summary="Get detail Directors",
*     operationId="showDirectors",
*     @OA\Parameter(
*         name="id",
*         in="path",
*         required=true,
*         description="ID Directors",
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


    public function showDirectors($id)
    {
        $directors = Directors::where('is_contractor', false)->find($id);
        UserController::ordinalNumber($directors->id);
        if($directors) {
            return response()->json(
                [
                    'status' => 'success',
                    'data' => $directors->toArray()
                ], 200);
        }
        return response()->json(
            [
                'status' => 'error',
                'errors' => 'Cannot find directors'
            ], 404);
    }

/**
 * @OA\Post(
 *     tags={"Directors"},
 *     path="/api/directors",
 *     summary="Create Directors",
 *     operationId="createDirectors",
 *     @OA\Parameter(
 *         name="company_name",
 *         in="query",
 *         required=true,
 *         description="company_name",
 *         @OA\Schema(
 *             type="string",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="address",
 *         in="query",
 *         required=true,
 *         description="address",
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *    @OA\Parameter(
 *         name="postal_code",
 *         in="query",
 *         required=true,
 *         description="Postal Code",
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *    @OA\Parameter(
 *         name="company_main_tel",
 *         in="query",
 *         required=true,
 *         description="Company Main Tel",
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="website",
 *         in="query",
 *         required=true,
 *         description="Website",
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="uen_no",
 *         in="query",
 *         required=true,
 *         description="UEN No",
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="bank_name",
 *         in="query",
 *         required=true,
 *         description="bank_name",
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="account_number",
 *         in="query",
 *         required=true,
 *         description="account_number",
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="remarks",
 *         in="query",
 *         required=true,
 *         description="Remarks",
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


    public function createDirectors(Request $request)
    {
        $v = Validator::make($request->all(), [
            'company_name'      => 'required',
        ]);

        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()->first()
            ], 422);
        }

        $directors = Directors::create($request->all());
        $directors->is_contractor = false;
        $directors->save();
        if($directors) {
            $directors = Directors::where('is_contractor', false)->find($directors->id);
            // UserController::ordinalNumber($directors->id);
            return response()->json([
                'status' => 'Successfully Added Directors',
                'data'   => $directors
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
 * @OA\Put(
 *     tags={"Directors"},
 *     path="/api/directors/{id}",
 *     summary="Update Directors",
 *     operationId="updateDirectors",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID Directors",
 *         @OA\Schema(
 *             type="integer",
 *             format="int64",
 *             minimum=1,
 *         )
 *     ),
 *      @OA\Parameter(
 *         name="company_name",
 *         in="query",
 *         required=true,
 *         description="company_name",
 *         @OA\Schema(
 *             type="string",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="address",
 *         in="query",
 *         required=true,
 *         description="address",
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *    @OA\Parameter(
 *         name="postal_code",
 *         in="query",
 *         required=true,
 *         description="Postal Code",
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *    @OA\Parameter(
 *         name="company_main_tel",
 *         in="query",
 *         required=true,
 *         description="Company Main Tel",
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="website",
 *         in="query",
 *         required=true,
 *         description="Website",
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="uen_no",
 *         in="query",
 *         required=true,
 *         description="UEN No",
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="bank_name",
 *         in="query",
 *         required=true,
 *         description="bank_name",
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="account_number",
 *         in="query",
 *         required=true,
 *         description="account_number",
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="remarks",
 *         in="query",
 *         required=true,
 *         description="Remarks",
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

    public function updateDirectors(Request $request, $id)
    {
        $v = Validator::make($request->all(), [
            'company_name'      => 'required',
        ]);

        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()->first()
            ], 422);
        }

        $directors = Directors::where('id', $id)->where('is_contractor', false)->first();
        if(!$directors) {
            return response()->json([
                'status' => 'error',
                'errors' => "Cannot find directors"
            ], 404);
        } 
        $directors = $directors->update($request->all());
        if($directors) {
            $directors = Directors::where('is_contractor', false)->find($id);
            UserController::ordinalNumber($directors->id);
            return response()->json([
                'status' => 'Successfully Update Directors',
                'data'   => $directors
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
 *     tags={"Directors"},
 *     path="/api/directors",
 *     summary="Delete Directors",
 *     operationId="deleteDirectors",
 *      @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType="application/x-www-form-urlencoded",
 *             @OA\Schema(
 *                 type="object",
 *                 @OA\Property(
 *                     property="ids",
 *                     description="ID Directors",
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

    public function deleteDirectors(Request $request)
    {
        $ids = $request->ids;
        if(is_string($ids)){
            $ids = json_decode($ids);
        }
        $directors = Directors::whereIn('id', $ids)->where('is_contractor', false)->delete();
        
        if($directors) {
            $contact_person = User::whereIn('company_id', $ids)->delete();
            return response()->json(
                [
                    'status' => 'Successfully Deleted Directors',
                ], 200);
        }
        return response()->json(
            [
                'status' => 'error',
                'errors' => 'Cannot find directors'
            ], 404);
    }


    /**
 * @OA\Get(
 *     tags={"Directors"},
 *     path="/api/list-director",
 *     summary="Get list Funeral Director",
 *     operationId="listRoomNotBooking",
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

    public function getAllFuneralDirector(Request $request)
    {
        $directors = Directors::whereNull('deleted_at')->get();
        return response()->json(
            [
                'status' => 'success',
                'data' => $directors
            ], 200);
    }

}