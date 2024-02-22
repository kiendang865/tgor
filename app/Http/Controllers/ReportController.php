<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Report;
use Illuminate\Support\Facades\Validator;
use App\Attachments;
use SplFileInfo;
use Illuminate\Support\Facades\File;
use Excel;
use App\Exports\CollectionsSummaryExport;
use App\Exports\MemorialRoomReportExport;
use App\Exports\AdditionalServiceReportExport;
use App\Exports\RenewalExport;
use App\Exports\NichesReportExport;

class ReportController extends Controller
{
    /**
     * @OA\Get(
     *     tags={"Reports"},
     *     path="/api/report",
     *     summary="Get list Report",
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
    public function listReport(Request $request){
        $limit      = intval($request->query('limit'));
        $report     = Report::where('id', '>', 0);
        $filter     = json_decode($request->query('filter'));
        if(!empty($filter->title)){
            $key_word = $filter->title;
            $report->where('name', 'like', '%'.$key_word.'%');
        }
        if(!empty($filter->id)){
            $key_word = $filter->id;
            $report->where('id', 'like', '%'.$key_word.'%');
        }
        if(!empty($filter->all)){
            $key_word = $filter->all;
            $report->where(function ($query) use ($key_word) {
                $query->where('id', 'like', "%".$key_word."%")
                      ->orWhere('name', 'like', "%".$key_word."%");
            });
        }
        $report = $report->with('service_type')->paginate($limit)->toArray();
        return response()->json($report, 200);
    }

    /**
    * @OA\Post(
    *     tags={"Reports"},
    *     path="/api/report",
    *     summary="Create Report",
    *     @OA\Parameter(
    *         name="start_time",
    *         in="query",
    *         description="Start Time",
    *         @OA\Schema(
    *             type="string"
    *         )
    *     ),
    *     @OA\Parameter(
    *         name="end_time",
    *         in="query",
    *         description="End Time",
    *         @OA\Schema(
    *             type="string"
    *         )
    *     ),
    *     @OA\Parameter(
    *         name="name",
    *         in="query",
    *         description="Name",
    *         @OA\Schema(
    *             type="string"
    *         )
    *     ),
    *     @OA\Parameter(
    *         name="service",
    *         in="query",
    *         description="Service",
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
            'start_time'    =>  'required',
            'end_time'      =>  'required',
            'name'          =>  'required',
            'service'       =>  'required'
        ]);
        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()->first()
            ], 422);
        }
        $report = Report::create($request->all());
        return response()->json([
            'status' => "Successfully Added Report.",
            'data'  =>  $report
        ]);

    }

    public static function hasInListFiles($list, $fileName){
        foreach($list as $key=>$value){
            $name = $value->name;
            if($fileName == $name){
                return true;
            }
        }
        return false;
    }
    // add file report
    // public function updateFile(Request $request, $id){
    //     // $data = [];
       
    //     $v = Validator::make($request->all(), [
    //         'title'   => 'required',
    //     ]);
    //     if ($v->fails())
    //     {
    //         return response()->json([
    //             'status' => 'error',
    //             'errors' => $v->errors()->first()
    //         ], 422);
    //     }
    //     $title = $request->get('title');
    //     //
        
        
    //     //
    //     if($request->hasFile('file')){
    //         //
    //         $files = $request->file('file');
    //         //
    //         $report_file = Report::find($id);
    //         if($report_file){
    //             $file_name = $report_file->name;
    //             $path = public_path().'/reports/' .  $file_name;
    //             if (file_exists($path)) {
    //                 unlink($path);
    //             }
    //         }
    //         foreach($files as $key=>$report){
    //             //
    //             $name_file = $report->getClientOriginalName();
    //             $arr = explode('.', $name_file);
    //             $name = $arr[0];
    //             $path =  public_path().'/reports/'.$name_file;
    //             //
    //             $report->move(public_path().'/reports/',$name_file);
    //             //  
    //             $report_file->name        = $name_file;
    //             $report_file->remarks     = $path;
    //             $report_file->title       = $title;
    //             $report_file->save();

    //         }
            
    //         return response()->json([
    //             'status' => 'Successfully Added Report File',
    //             'data'   => $report_file
    //         ], 200);
    //     }   

    //     $message['errors'] = " You don't have any file";
    //     return response()->json($message, 404);
    // }

 /**
 * @OA\Delete(
 *     tags={"Reports"},
 *     path="/api/report",
 *     summary="Delete Reports file",
 *     operationId="deleteReportsFile",
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
    public function deleteFile(Request $request){
        $ids = $request->ids;
        if(is_string($ids)){
            $ids = json_decode($ids);
        }
        $reports = Report::whereIn('id', $ids)->get();
        if($reports){
            foreach($reports as $key=>$value){
                $path = public_path().'/reports/' .  $value['name'];
                if (file_exists($path)) {
                    unlink($path);
                }
                // else{
                //     return response()->json(
                //         [
                //             'error' => 'Not find file '.$value['name'],
                //         ], 404);
                // }
            }

            $report = Report::whereIn('id', $ids)->delete();
                if($report) {
                return response()->json(
                [
                    'status' => 'Successfully Deleted Report File',
                ], 200);
            }
            
        }
        return response()->json(
            [
                'status' => 'error',
                'errors' => 'Cannot find Reports'
            ], 404);
    }
    /**
    * @OA\Get(
    *     tags={"Reports"},
    *     path="/api/generate-report/{id}",
    *     summary="Generate Report",
    *     @OA\Parameter(
    *         name="id",
    *         in="path",
    *         description="ID Report",
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

    public function generateCollections($id){
        $report = Report::find($id);
        if(!empty($report)){
            $start = $report->start_time;
            $end = $report->end_time;
            $type = $report->service_type->reference_value_text;
            if($type == "Niches"){
                return Excel::download(new NichesReportExport($start, $end), $report->name.'.xlsx');
            }
            if($type == "Memorial Rooms"){
                return Excel::download(new MemorialRoomReportExport($start, $end), $report->name.'.xlsx');
            }
            if($type == "Additional Services"){
                return Excel::download(new AdditionalServiceReportExport($start, $end), $report->name.'.xlsx');
            }
            if($type == "Renewal"){
                return Excel::download(new RenewalExport($start, $end), $report->name.'.xlsx');
            }
            if($type == "Collections Summary"){
                return Excel::download(new CollectionsSummaryExport($start, $end), $report->name.'.xlsx');
            }
        }
        else{
            return response()->json([
                'status' => 'error',
                'errors' => "Cannot find report."
            ], 422);
        }
        
    }
}
