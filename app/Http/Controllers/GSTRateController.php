<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\GSTRate;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Svg\Tag\Rect;

class GSTRateController extends Controller
{

/**
* @OA\Get(
*     tags={"GST Rate"},
*     path="/api/gst-rate",
*     summary="Get GST Rate",
*     operationId="index",
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
        $id = @$request->gst_id;
        $gst = null;
        if($id){
            $gst = GSTRate::find($id);
        }else{
            $now = now();
            $gst = GSTRate::where(\DB::raw("DATE(gst_start_date)"), '<=', \DB::raw("DATE('".$now->format('Y-m-d')."')"))
            ->where(function ($query) use ($now) {
                $query->where('gst_end_date','>=', "'".$now->format('Y-m-d h:i:s')."'")
                    ->orWhereNull('gst_end_date');
            })
            ->orderBy('gst_start_date', 'DESC')->first();
        }
        
        return response()->json(
            [
                'status' => 'success',
                'data' => $gst
            ], 
            200
        );
    }

/**
* @OA\Get(
*     tags={"GST Rate"},
*     path="/api/history-gst-rate",
*     summary="Get History GST Rate",
*     operationId="getHistory",
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
    public function getHistory(){
        $gst_rate = GSTRate::whereNotNull('gst_end_date')->orderBy('gst_end_date', 'ASC')->get();
        return response()->json(
            [
                'status' => 'success',
                'data' => $gst_rate
            ], 200);
    }

/**
* @OA\Post(
*     tags={"GST Rate"},
*     path="/api/gst-rate",
*     summary="Create GST Rate",
*     operationId="create",
*     @OA\Parameter(
*         name="current_rate",
*         in="query",
*         description="Current Rate",
*         @OA\Schema(
*             type="integer"
*         )
*     ),
*     @OA\Parameter(
*         name="effective_start_date",
*         in="query",
*         description="Effective Start Date",
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
    public function create(Request $request){
        $v = Validator::make($request->all(), [
            'current_rate'          =>  'required',
            'effective_start_date'  =>  'required'
        ]);
        if ($v->fails()){
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()->first()
            ], 422);
        }

        $new_gst_rate = GSTRate::create([
            'name'              =>  $request->current_rate.'%',
            'rate'              =>  $request->current_rate/100,
            'gst_start_date'    => Carbon::parse($request->effective_start_date)
        ]);
        $now = now();
        $old_gst = GSTRate::where('id', '<>', $new_gst_rate->id)
                        ->whereNull('gst_end_date')
                        ->whereDate('gst_start_date', '<=', $now->format('Y-m-d').' 00:00:00')
                        ->orderBy('gst_end_date', 'DESC')
                        ->first();
        if(!empty($old_gst)){
            $old_gst->gst_end_date = Carbon::parse($request->effective_start_date);
            $old_gst->save();
        }
        if($new_gst_rate){
            return response()->json(
            [
                'status' => 'Successfully Added GST Rate.',
                'data' => $new_gst_rate
            ], 200);
        }
    }
}
