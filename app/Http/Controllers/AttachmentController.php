<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Invoice;
use App\InvoiceLineItem;
use App\Booking;
use App\Reference;
use Illuminate\Database\Eloquent\Builder;
use File;
use App\Attachments;
use App\Payment;

class AttachmentController extends Controller
{
/**
 * @OA\Post(
 *     tags={"Attachments"},
 *     path="/api/attachment",
 *     summary="Add Attachment",
 *     operationId="saveAttachment",
 *     @OA\Parameter(
 *         name="id",
 *         in="query",
 *         required=true,
 *         description="ID",
 *         @OA\Schema(
 *             type="integer",
 *             format="int64",
 *             minimum=1,
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="type",
 *         in="query",
 *         required=true,
 *         description="ID",
 *         @OA\Schema(
 *             type="integer",
 *             format="int64",
 *             minimum=1,
 *         )
 *     ),
 *      @OA\RequestBody(
 *          @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 type="object",
 *                 @OA\Property(
 *                     property="file[]",
 *                     description="File report",
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
public function saveAttachment(Request $request){
    if($request->hasFile('file')){
        $files = $request->file('file');
        // var_dump($files);exit;
        $name_file = $files->getClientOriginalName();
        // dd($name_file);
        $size_file = $files->getClientSize();
        $extension = $files->getClientOriginalExtension();
        $hash_name = $name_file.'_'.time().'.'.$extension;
        $arr = explode('.', $name_file);
    
        $name = $arr[0];
        $remarks =  public_path().'/attchments/'.$name_file;
        $path = public_path().'/attchments/';
        if(File::makeDirectory($path, 0777, true, true)){
            $files->move(public_path().'/attchments/',$hash_name);
        }else{
            $files->move(public_path().'/attchments/',$hash_name);
        }
        
        //  
        $now = now();
        $model = false;
        switch ($request->type) {
            case 1:
                $model = Invoice::find($request->id)->attachments()->create([
                    'attachable_file_name' => $name_file,
                    'attachable_name' => $hash_name,
                    'attachable_file_size' => $size_file,
                    'attachable_content_type' => $extension,
                    'attachable_updated_at' => $now->toDateTimeString(),
                ]);
                break;
            case 2:
                $model = Payment::find($request->id)->attachments()->create([
                    'attachable_file_name' => $name_file,
                    'attachable_name' => $hash_name,
                    'attachable_file_size' => $size_file,
                    'attachable_content_type' => $extension,
                    'attachable_updated_at' => $now->toDateTimeString(),
                ]);
                break;
            
            default:
                # code...
                break;
        }

    

      
       if($model){
            return response()->json([
                'status' => 'Created Successfully Attachment.',
                'data'  =>  'Created Successfully Attachment.'
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
}
/**
 * @OA\GET(
 *     tags={"Attachments"},
 *     path="/api/attachment",
 *     summary="Get Attachment",
 *     operationId="getAttachment",
 *     @OA\Parameter(
 *         name="id",
 *         in="query",
 *         required=true,
 *         description="ID",
 *         @OA\Schema(
 *             type="integer",
 *             format="int64",
 *             minimum=1,
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="type",
 *         in="query",
 *         required=true,
 *         description="ID",
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
public function getAttachment(Request $request){

    $model = false;
    switch ($request->type) {
        case 1:
            $model = Invoice::find($request->id)->attachments()->get();
            break;
        case 2:
            $model = Payment::find($request->id)->attachments()->get();
            break;
        
        default:
            # code...
            break;
    }
    if($model){
        return response()->json([
            'status' => 'Updated Successfully Attachment.',
            'data'  =>  $model
        ], 200);
   }
}
/**
 * @OA\Delete(
 *     tags={"Attachments"},
 *     path="/api/attachment",
 *     summary="Delete Attachment",
 *     operationId="deleteAttachment",
 *      @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType="application/x-www-form-urlencoded",
 *             @OA\Schema(
 *                 type="object",
 *                 @OA\Property(
 *                     property="ids",
 *                     description="ID Report File",
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
public function deleteAttachment(Request $request){
    $ids = $request->ids;
        if(is_string($ids)){
            $ids = json_decode($ids);
        }
        $attchment = Attachments::whereIn('id', $ids)->get();
        if($attchment){
            foreach($attchment as $key => $value){
                $path = public_path().'/attchments/' .  $value['attachable_name'];
                if (file_exists($path)) {
                    unlink($path);
                }
            }

            $attchment = Attachments::whereIn('id', $ids)->delete();
                if($attchment) {
                return response()->json(
                [
                    'status' => 'Successfully Deleted Attachment File',
                ], 200);
            }
            
        }
        return response()->json(
            [
                'status' => 'error',
                'errors' => 'Cannot find Attachment'
            ], 404);
}
/**
 * @OA\GET(
 *     tags={"Attachments"},
 *     path="/api/download-attachment",
 *     summary="Download Attachment",
 *     operationId="downloadAttachment",
 *     @OA\Parameter(
 *         name="id",
 *         in="query",
 *         required=true,
 *         description="ID",
 *         @OA\Schema(
 *             type="integer",
 *             format="int64",
 *             minimum=1,
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="arr_id",
 *         in="query",
 *         required=true,
 *         description="ID",
 *         @OA\Schema(
 *             type="string",
 *             format="int64",
 *             minimum=1,
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="type",
 *         in="query",
 *         required=true,
 *         description="ID",
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
public function downloadAttachment(Request $request){
    $ids = $request->arr_id;
    if(is_string($ids)){
        $ids = json_decode($ids);
    }
    $model = false;
    switch ($request->type) {
        case 1:
            $model = Invoice::find($request->id)->attachments()->whereIn('id',$ids)->get();
            break;
        case 2:
            $model = Payment::find($request->id)->attachments()->get();
            break;
        
        default:
            # code...
            break;
    }
    if($model){
        return response()->json([
            'status' => 'Updated Successfully Attachment.',
            'data'  =>  $model
        ], 200);
   }
}
}
