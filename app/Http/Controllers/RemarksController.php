<?php

namespace App\Http\Controllers;
use App\Remarks;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Controllers\BookingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class RemarksController extends Controller
{
/**
 * @OA\Get(
 *     tags={"Remarks"},
 *     path="/api/remarks",
 *     summary="Get list Remarks",
 *     operationId="getListRemarks",
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
 *         name="booking_line_item_id",
 *         in="query",
 *         description="ID booking Line Item",
 *         @OA\Schema(
 *             type="integer",
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
 *    security={
 *         {"bearerAuth": {}}
 *     }
 * )
 */

    public function getListRemarks(Request $request){
        $limit = intval($request->query('limit'));
        $filter = json_decode($request->query('filter'));
        $booking_line_item_id = $request->get('booking_line_item_id');
        // dd($booking_line_item_id);
        ///
        $type_1 = "d/m/Y";
        $type_2 = "d/m";
        $type_expectations_1 = "Y-m-d";
        $type_expectations_2 = "m-d";
        ///
        if(!$booking_line_item_id){
            return response()->json([
                'status' => 'error',
                'errors' => "Not found remarks"
            ], 404);
        }
        $remarks = Remarks::where('booking_line_item_id', 'like', $booking_line_item_id)->with('user');
        if($remarks){
            if (!empty($filter->all)){
                $key_word = $filter->all;
                $key_word = BookingController::custom_date($type_1, $type_2, $key_word, $type_expectations_1, $type_expectations_2);
                $remarks->where(function ($query) use ($key_word) {
                    $query->where('id', 'like', '%'.$key_word.'%')
                        ->orWhere('created_at', 'like', '%'.$key_word.'%')
                        ->orWhereHas('user', function (Builder $query) use ($key_word) {
                            $query->where('display_name', 'like', '%'.$key_word.'%');
                        })
                        ->orWhere('remarks', 'like', '%'.$key_word.'%');
                });
            }
            if (!empty($filter->id)){
                $key_word = $filter->id;
                $remarks->where(function ($query) use ($key_word) {
                    $query->where('id', 'like', '%'.$key_word.'%');
                });
            }
            if (!empty($filter->date)){
                $key_word = $filter->date;
                $key_word = BookingController::custom_date($type_1, $type_2, $key_word, $type_expectations_1, $type_expectations_2);
                $remarks->where(function ($query) use ($key_word) {
                    $query->where(DB::raw("(DATE_FORMAT(created_at,'%Y-%m-%d'))"), 'like', '%'.$key_word.'%');
                });
            }
            if (!empty($filter->time)){
                $key_word = $filter->time;
                $key_word = BookingController::custom_date("h:i:s d/m/Y", "h:i", $key_word, "Y-m-d h:i:s", "h:i");
                $remarks->where(function ($query) use ($key_word) {
                    $query->where(DB::raw("(DATE_FORMAT(created_at,'%h:%i'))"), 'like', '%'.$key_word.'%');
                });
            }
            if (!empty($filter->user_name)){
                $key_word = $filter->user_name;
                $remarks->where(function ($query) use ($key_word) {
                    $query->whereHas('user', function (Builder $query) use ($key_word) {
                            $query->where('display_name', 'like', '%'.$key_word.'%');
                        });
                });
            }
            if (!empty($filter->remarks)){
                $key_word = $filter->remarks;
                $remarks->where(function ($query) use ($key_word) {
                    $query->where('remarks', 'like', '%'.$key_word.'%');
                });
            }

            $remarks = $remarks->paginate($limit)->toArray();  
            $remarks['status'] = "success";
            return response()->json($remarks, 200);
        }

        return response()->json([
            'status' => 'error',
            'errors' => "Something bad happened, can't find remarks"
        ], 404);

    }

    /**
 * @OA\Post(
 *     tags={"Remarks"},
 *     path="/api/remarks",
 *     summary="Create Remarks",
 *     operationId="store",
 *     @OA\Parameter(
 *         name="booking_line_item_id",
 *         in="query",
 *         required=true,
 *         description="booking line item",
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="user_id",
 *         in="query",
 *         required=true,
 *         description="user id",
 *         @OA\Schema(
 *             type="integer"
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

    public function store(Request $request){   
        $messages = [
            'remarks.required' => 'Remarks must be filled out.',
        ];
        $v = Validator::make($request->all(), [
            'remarks'                       => 'required',
            'booking_line_item_id'          => 'required',
            'user_id'                       => 'required'

        ], $messages);

        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()->first()
            ], 422);
        }
        $url = "";
        if($request->hasFile('file')){
            $file = $request->file;
            $name_file = $file->getClientOriginalName();
            $arr = explode('.', $name_file);
            $ext = $arr[1];
            $timestamp = time();
            $name_replace = $timestamp.'.'.$ext;
            //
            $path = public_path().'/remarks/';
            if(File::makeDirectory($path, 0777, true, true)){
                $file->move($path, $name_replace);
            }else{
                $file->move($path, $name_replace);
            }
            $url = url('/remarks/'.$name_replace);
            $path_file = public_path().'/remarks/'.$name_replace;

        }
        $data = $request->all();
        if(!empty($url)){
            $data["file_url"] = $url;
            $data["name_file"] = $name_file;
            $data["file_path"] = $path_file;
        }
        $remarks = Remarks::create($data);
        if($remarks) {
            $remarks =  Remarks::with('user')->find($remarks->id);
            return response()->json([
                'status' => 'Successfully Added remarks',
                'data'   => $remarks
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
*     tags={"Remarks"},
*     path="/api/remarks/{id}",
*     summary="Get detail Remarks",
*     operationId="detail",
*     @OA\Parameter(
*         name="id",
*         in="path",
*         required=true,
*         description="ID Remarks",
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
        $remarks = Remarks::with('user')->find($id);
        if($remarks) {
            return response()->json(
                [
                    'status' => 'success',
                    'data' => $remarks->toArray()
                ], 200);
        }
        return response()->json(
            [
                'status' => 'error',
                'errors' => 'Cannot find remarks'
            ], 404);
    }
    
/**
 * @OA\POST(
 *     tags={"Remarks"},
 *     path="/api/remarks/{id}",
 *     summary="Update remarks",
 *     operationId="update",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Id Remarks",
 *         @OA\Schema(
 *             type="integer",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="remarks",
 *         in="query",
 *         required=true,
 *         description="remarks",
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="user_id",
 *         in="query",
 *         required=true,
 *         description="user_id",
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

    public function update(Request $request, $id){
        $messages = [
            'remarks.required' => 'Remarks must be filled out.',
        ];
        $v = Validator::make($request->all(), [
            'remarks'         => 'required'

        ], $messages);

        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()->first()
            ], 422);
        }
        $remarks = Remarks::where('id', $id)->first();
        $user_id = $request->get('user_id');
        if($remarks->user_id == $user_id){
            if(!$remarks) {
                return response()->json([
                    'status' => 'error',
                    'errors' => "Cannot find remarks"
                ], 404);
            } 
            $url = "";
            if($request->hasFile('file')){
                $file = $request->file;
                $name = $file->getClientOriginalName();
                $arr = explode('.', $name);
                $ext = $arr[1];
                $timestamp = time();
                $name_replace = $timestamp.'.'.$ext;
                //
                $path = public_path().'/remarks/';
                if(File::makeDirectory($path, 0777, true, true)){
                    $file->move($path, $name_replace);
                }else{
                    $file->move($path, $name_replace);
                }
                $url = url('/remarks/'.$name_replace);
                $path_file = public_path().'/remarks/'.$name_replace;
            }
            $data = $request->all();
            if(!empty($url)){
                $data["file_url"] = $url;
                $data["name_file"] = $name;
                $data["file_path"] = $path_file;
            }
            $remarks = $remarks->update($data);
            if($remarks) {
                $remarks =  Remarks::with('user')->find($id);
                return response()->json([
                    'status' => 'Successfully Updated Remarks',
                    'data'   => $remarks
                ], 200);
            }
            else {
                return response()->json([
                    'status' => 'error',
                    'errors' => "Something bad happened, please try later"
                ], 422);
            }
        }
        return response()->json([
            'status' => 'error',
            'errors' => "Cannot update remarks"
        ], 422);
        
    }
    
/**
* @OA\Get(
*     tags={"Remarks"},
*     path="/api/download-remarks/{id}",
*     summary="Download Remarks",
*     operationId="downloadRemarks",
*     @OA\Parameter(
 *        name="id",
 *        in="path",
 *        required=true,
 *        description="Id Remarks",
 *        @OA\Schema(
 *            type="integer",
 *        )
 *    ),
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
    public function downloadRemarks($id){
        $remarks_files = Remarks::where("id", $id)->select('file_url', 'name_file')->first();
        return response()->json([
            "status"    => "success",
            "data"      =>  $remarks_files
        ], 200);
    }

}
