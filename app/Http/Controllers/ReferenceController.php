<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Reference;

class ReferenceController extends Controller
{
    
/**
 * @OA\Post(
 *     tags={"Reference"},
 *     path="/api/reference",
 *     summary="Get List Reference",
 *     operationId="index",
 *      @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType="application/x-www-form-urlencoded",
 *             @OA\Schema(
 *                 type="object",
 *                 @OA\Property(
 *                     property="ids",
 *                     description="Type",
 *                     type="string",
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

    public function index(Request $request)
    {   
        $type = $request->reference_type;
        if(is_string($type)){
            $type = json_decode($type);
        }
        $reference  = Reference::whereIn('reference_type',$type)->whereNull('deleted_at')->get();
        return response()->json(
            [
                'status' => 'success',
                'data'   => $reference,
            ], 200);
    }
}