<?php

namespace App\Http\Controllers;

use App\BookingLineItems;
use Illuminate\Http\Request;
use App\Niche;
use App\Reference;
use App\Exports\ExportNiche;
use Box\Spout\Autoloader;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Illuminate\Support\Facades\Validator;
use File;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Database\Eloquent\Builder;
use Excel;
use PDF;
use App;
use App\SaleAgreement;
use App\Http\Controllers\BookingController;
use App\SaleAgreementLineItem;
use Illuminate\Support\Facades\DB;
use App\Duration;
use App\Booking;
use App\ViewNiche;
use App\GSTRate;

class NicheController extends Controller
{

/**
 * @OA\Post(
 *     tags={"Niches"},
 *     path="/api/niche",
 *     summary="Create Niches",
 *     operationId="store",
 *     @OA\Parameter(
 *         name="reference_no",
 *         in="query",
 *         required=true,
 *         description="Reference No",
 *         @OA\Schema(
 *             type="string",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="type_id",
 *         in="query",
 *         required=true,
 *         description="Type Id",
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="category_id",
 *         in="query",
 *         required=true,
 *         description="Category Id",
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="price",
 *         in="query",
 *         required=true,
 *         description="Price",
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="bay",
 *         in="query",
 *         description="Bay",
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="wing",
 *         in="query",
 *         description="Wing",
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="floor",
 *         in="query",
 *         description="Floor",
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="block",
 *         in="query",
 *         description="Block",
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="level",
 *         in="query",
 *         description="Level",
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="unit",
 *         in="query",
 *         description="Unit",
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="status",
 *         in="query",
 *         required=true,
 *         description="Status",
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

    public function store(Request $request)
    {   
        $messages = [
            'reference_no.unique' => 'Niche has already been taken.',
        ];
        $v = Validator::make($request->all(), [
            'reference_no'          => 'required|unique:niches,reference_no',
            'type_id'               => 'required',
            'category_id'           => 'required',
            'price'                 => 'required',
            'status'                => 'required'
        ],$messages); 

        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()->first()
            ], 422);
        }
        $niche = Niche::create($request->all());
        if($niche) {
            $niche =  Niche::with('type')->with('category')->find($niche->id);
            return response()->json([
                'status' => 'Successfully Added Niche',
                'data'   => $niche
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
 *     tags={"Niches"},
 *     path="/api/niche",
 *     summary="Get list Niches",
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

    public function index(Request $request)
    {   
        $limit = intval($request->query('limit'));
        $niches = Niche::where('id', '>', 0);
        $filter = json_decode($request->query('filter'));
        $type_1 = "d/m/Y";
        $type_2 = "d/m";
        $type_expectations_1 = "Y-m-d";
        $type_expectations_2 = "m-d";
        // Where all
        if(!empty($filter)){
            if (!empty($filter->all)) {
                $key_word = $filter->all;
                $key_word = BookingController::custom_date($type_1, $type_2, $key_word, $type_expectations_1, $type_expectations_2);
                $niches->where(function ($query) use ($key_word) {
                    $query->whereHas('type', function (Builder $query) use ($key_word) {
                        $query->where(
                            [
                                ['reference_type', '=', 'type_niche'],
                                ['reference_value_text', 'like', '%'.$key_word.'%'],
                            ]
                        );
                    })
                    ->orWhereHas('booking_line_item.information', function(Builder $query) use ($key_word){
                                $query->where('full_name', 'like', '%'.$key_word.'%');
                    })
                    ->orWhereHas('booking_line_item', function(Builder $query) use ($key_word){
                        $query->where('lease_expiry_date', 'like', '%'.$key_word.'%');
                    })
                    ->orWhere('full_location',     'like', "%".$key_word."%")
                    ->orWhere('reference_no', 'like', '%'.$key_word.'%')
                    ->orWhere('status', 'like', $key_word."%");
                });
            }
            // Where id
            if (!empty($filter->id)) {
                $niches->where('reference_no', 'like', '%'.$filter->id.'%');
            }
            // Where type
            if (!empty($filter->type)) {
                $type = $filter->type;
                $niches->whereHas('type', function (Builder $query) use ($type) {
                    $query->where(
                        [
                            ['reference_type', '=', 'type_niche'],
                            ['reference_value_text', 'like', '%'.$type.'%'],
                        ]
                    );
                });
            }
            // Where location
            if (!empty($filter->location)) {
                $key_word = $filter->location;
                $niches->where(function ($query) use ($key_word) {
                    $query->where('full_location',  'like', "%".$key_word."%");
                });
            }
            // Where status
            if (!empty($filter->status)) {
                $niches->where('status', 'like', $filter->status."%");
            }
            
            if (!empty($filter->occupant_name)) {
                $occupant_name = $filter->occupant_name;
                $niches->whereHas('booking_line_item.information', function (Builder $query) use ($occupant_name) {
                    $query->where('full_name', 'like', '%'.$occupant_name.'%');
                });
            }
    
            if (!empty($filter->expiry_day)) {
                $expiry_day = $filter->expiry_day;
                $expiry_day = BookingController::custom_date($type_1, $type_2, $expiry_day, $type_expectations_1, $type_expectations_2);
                $niches->whereHas('booking_line_item', function (Builder $query) use ($expiry_day) {
                    $query->where('lease_expiry_date', 'like', '%'.$expiry_day.'%');
                });
            }
            $niches = $niches->with(['type', 'category'])
                            ->with(['booking_line_item' => function($query){
                                $query->select('id', 'duration_of_lease', 'lease_expiry_date', 'lease_start_date');
                                $query->with(['information' => function($q){
                                    $q->select('id', 'booking_line_items_id', 'full_name');
                                }]);
                            }])
                            ->paginate($limit)->toArray();
        }else{
            $niches = [];
        }
        return response()->json($niches, 200);
    }

/**
 * @OA\Delete(
 *     tags={"Niches"},
 *     path="/api/niche",
 *     summary="Delete Niches",
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
        $niches = Niche::whereIn('id', $ids)->delete();

        if($niches) {
            return response()->json(
                [
                    'status' => 'Successfully Deleted Niche',
                ], 200);
        }
        return response()->json(
            [
                'status' => 'error',
                'errors' => 'Cannot find niche'
            ], 404);
    }

/**
 * @OA\Put(
 *     tags={"Niches"},
 *     path="/api/niche/{id}",
 *     summary="Update Niches",
 *     operationId="update",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Id Niche",
 *         @OA\Schema(
 *             type="integer",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="reference_no",
 *         in="query",
 *         required=true,
 *         description="Reference No",
 *         @OA\Schema(
 *             type="string",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="type_id",
 *         in="query",
 *         required=true,
 *         description="Type Id",
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="category_id",
 *         in="query",
 *         required=true,
 *         description="Category Id",
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="price",
 *         in="query",
 *         required=true,
 *         description="Price",
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="bay",
 *         in="query",
 *         required=true,
 *         description="Bay",
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="bay",
 *         in="query",
 *         required=true,
 *         description="Bay",
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="wing",
 *         in="query",
 *         required=true,
 *         description="Wing",
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="floor",
 *         in="query",
 *         required=true,
 *         description="Floor",
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="niche_block",
 *         in="query",
 *         required=true,
 *         description="Block",
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="level",
 *         in="query",
 *         required=true,
 *         description="Level",
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="unit",
 *         in="query",
 *         required=true,
 *         description="Unit",
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="status",
 *         in="query",
 *         required=true,
 *         description="Status",
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

    public function update(Request $request, $id)
    {
        $v = Validator::make($request->all(), [
            'reference_no'          => 'required',
            'type_id'               => 'required',
            'category_id'           => 'required',
            'price'                 => 'required',
            'status'                => 'required'

        ]);
        
        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()->first()
            ], 422);
        }
        $niche = Niche::where('id', $id)->first();
        if(!$niche) {
            return response()->json([
                'status' => 'error',
                'errors' => "Cannot find niche"
            ], 404);
        } 
        $niche = $niche->update($request->all());
        if($niche) {
            $niche =  Niche::with('type', 'category', 'booking_line_item.information')->find($id);
            return response()->json([
                'status' => 'Successfully Updated Niche',
                'data'   => $niche
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
*     tags={"Niches"},
*     path="/api/niche/{id}",
*     summary="Get detail Niches",
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
        $niche = Niche::with(['type', 'booking_line_item.information', 'category'])
        ->with(['client' => function($q){
            $q->with('preferred_contact_by');
        }])
        ->find($id);
        if($niche) {
            $booking_line_item = BookingLineItems::find($niche->booking_line_item);
            if($booking_line_item){
                $expiry_date = new Carbon( $booking_line_item->expiry_date);
                $start_date = new Carbon( $booking_line_item->start_date);
                $years = $expiry_date->diffInYears($start_date);

                $now = Carbon::now();
                if($expiry_date->year > $now->year){

                    $duration_of_lease = $expiry_date->diffInYears($now)+1;

                    if($start_date->year > $now->year)
                    {
                        $duration_of_lease = $expiry_date->diffInYears($start_date);
                    }

                    $booking_line_item->duration_of_lease = $duration_of_lease."/".$years." years";
                    
                   
                }
                else{
                    $booking_line_item->duration_of_lease ="1/1 years";
                }
                $booking_line_item->save();
            }
            return response()->json(
                [
                    'status' => 'success',
                    'data' => $niche->toArray()
                ], 200);
        }
        return response()->json(
            [
                'status' => 'error',
                'errors' => 'Cannot find niche'
            ], 404);
    }
    //
    /**
     * @OA\Post(
     *     tags={"Niches"},
     *     path="/api/export-niche",
     *     summary="Export Niches",
     *     @OA\RequestBody(
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
    public function exportListNiches(Request $request){
        $ids = $request->ids;
        if (is_string($ids)) {
            $ids = json_decode($ids);
        }
        return Excel::download(new ExportNiche($ids), 'Niches.csv');
    }

    /**
     * @OA\Post(
     *     tags={"Niches"},
     *     path="/api/import-niche",
     *     summary="Import file Niches excel",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="file_excel",
     *                     description="file_excel niches",
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
    //import file
    public function importListNiches(Request $request){
        ini_set('max_execution_time', 3000);
        $file = $request->file("file_excel");
        if(!empty($file)){
            $reader = ReaderEntityFactory::createXLSXReader();
            $reader->setShouldFormatDates(true);
            $file = public_path("/niches.xlsx");
            $reader->open($file);
            // dd($reader);
            if(!empty($reader)){
                foreach ($reader->getSheetIterator() as $sheet) {
                    if($sheet->getName() == "Niches"){
                        foreach ($sheet->getRowIterator() as $row) {
                            $cells = $row->getCells();
                            //
                            $bayCells                   = $cells[0]->getValue();
                            $wingCells                  = $cells[1]->getValue();
                            $levelCells                 = $cells[2]->getValue();
                            $niche_block_Cells          = $cells[3]->getValue();
                            $niche_level_Cells          = $cells[4]->getValue();
                            $niche_number_Cells         = $cells[5]->getValue();
                            $niche_id_Cells             = $cells[6]->getValue();
                            $niche_category_Cells       = $cells[8]->getValue();
                            $niche_type_Cells           = $cells[9]->getValue();
                            $niche_status_Cells         = $cells[10]->getValue();
                            $niche_lease_price_Cells    = $cells[13]->getValue();
                            //
                            if(!($bayCells == "Niche Bay"||$wingCells == "Wing"||$levelCells == "Level"||$niche_block_Cells == "Niche Block"||$niche_level_Cells == "Niche Level"||$niche_number_Cells == "Niche Number"||$niche_id_Cells == "Niche ID"||$niche_category_Cells == "Niche Category"||$niche_type_Cells == "Niche Type"||$niche_status_Cells == "Niche Status"||$niche_lease_price_Cells == "New Lease Price")){
                                if(!($bayCells == "Total" || $bayCells == " " ||$niche_lease_price_Cells == " " )){
                                    $category = Reference::where(
                                        [
                                            ['reference_type', '=', 'category_niche'],
                                            ['reference_value_text', 'like', '%'.$niche_category_Cells.'%'],
                                        ]
                                    )->first();
                                    $type = Reference::where(
                                        [
                                            ['reference_type', '=', 'type_niche'],
                                            ['reference_value_text', 'like', '%'.$niche_type_Cells.'%'],
                                        ]
                                    )->first();
                                    //
                                    Niche::create([
                                        'reference_no'              => $niche_id_Cells,
                                        'type_id'                   => $type->id,
                                        'price'                     => $niche_lease_price_Cells,
                                        'status'                    => 'Available',
                                        'bay'                       => $bayCells,
                                        'wing'                      => $wingCells,
                                        'floor'                     => $levelCells,
                                        'block'                     => $niche_block_Cells,
                                        'level'                     => $niche_level_Cells,
                                        'unit'                      => $niche_number_Cells,
                                        'category_id'               => $category->id,
                                    ]);
                                }
                            }
                        }
                    }
                } 
                //
                // $path = public_path().'/import/niches/';
                // if(File::makeDirectory($path, 0777, true, true)){
                //     $name_file = $file->getClientOriginalName();
                //     $file->move($path,$name_file);
                // }else{
                //     $name_file = $file->getClientOriginalName();
                // $file->move($path,$name_file);
                // }
               
                //  
    
                return response()->json(
                    [
                        'status' => 'Successfully import file'
                    ], 200);
                // 
                $reader->close();
            }
            return response()->json(
                [
                    'status' => 'error',
                    'errors' => 'Cannot import file'
                ], 404);
                
        }
        return response()->json(
            [
                'status' => 'error',
                'errors' => 'Not file'
            ], 404);
    }

/**
* @OA\Get(
*     tags={"Niches"},
*     path="/api/niche-booking",
*     summary="Get all Niches not booking",
*     operationId="getNicheNotBooking",
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

    public function getNicheNotBooking(Request $request){
        $filter = json_decode($request->query('filter'));
        if (!empty($filter->name)) {
            $niche = Niche::where('status', 'Available')
                    ->whereNull('booking_id')
                    ->whereNull('deleted_at');
            $niche = $niche->where('reference_no', 'like', '%' . $filter->name . '%');
            $niche = $niche->with('type', 'category')->take(300)->get();
        }
        else{
            $niche = [];
        }
        return response()->json(
            [
                'status' => 'success',
                'data' => $niche
            ], 200);
    }

       /**
 * @OA\Get(
 *     tags={"Niches"},
 *     path="/api/make-niche-licence/{id}",
 *     summary="Make Niche Licence PDF",
 *     operationId="exportNicheLicence",
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


    public function exportNicheLicence($id){

        $booking_type = Reference::where('reference_type', 'booking_type')
        ->where('reference_value_text', 'Niches')
        ->whereNull('deleted_at')->first();

        $count_booking_niches = SaleAgreement::with('client')
        ->join('booking_line_items','booking_line_items.booking_id','=','sale_agreements.booking_id')
        ->where('sale_agreements.id',$id)
        ->whereNull('sale_agreements.deleted_at')
        ->where('booking_line_items.booking_type_id',$booking_type->id);
        // dd($count_booking_niches->get());
        
        if($count_booking_niches->count() > 0)
        {
            // dd($count_booking_niches->get());
            $booking_niches = $count_booking_niches->get();
            
            $html = '';
            $logo = url('/images/logo_tgor.png');
            $now = now();
            $gst_rate = GSTRate::where('gst_start_date', '<=', $now->format('Y-m-d').' 00:00:00')
                ->orderBy('gst_start_date', 'DESC')->first();
            foreach($booking_niches as $key => $value){ 
               $niche =  Niche::where('id',$value->id)->whereNull('deleted_at')->first();

               $booking_niches[$key]->service_id = $niche;
               $booking_niches[$key]->start_date = Carbon::parse($value->lease_start_date)->format('j F Y');
               $booking_niches[$key]->expiry_date = Carbon::parse($value->lease_expiry_date)->format('j F Y');
               $booking_niches[$key]->countYear = Carbon::parse($value->lease_expiry_date)->format('Y') - Carbon::parse($value->lease_start_date)->format('Y');
               $booking_niches[$key]->sale_agreement_date = Carbon::parse($value->sale_agreement_date)->format('d/m/Y');
               $booking_niches[$key]->total = number_format($value->total, 2, '.', ',');
               $booking_niches[$key]->tax_amount = number_format($value->tax_amount, 2, '.', ',');
               $gst_value = 0;
               if($gst_rate){
                $gst_value = $gst_rate->rate;
               }
               $gst = $value->amount * $gst_value;
               $booking_niches[$key]->gst_amount = number_format($gst, 2, '.', ',');
               $sale_agreement = $booking_niches[$key];
         
               $logo = url('/images/logo_tgor.png');
               $html .= view('exports.niches-licence', compact('logo','sale_agreement'))->render();
            }
           
            $pdf = App::make('dompdf.wrapper');
            $pdf = PDF::loadHTML($html); 
            // return $html; 
            return $pdf->stream();
            return $pdf->download('NicheLicence.pdf');
        }
        return response()->json(
            [
                'status' => 'success',
            ], 200);
         
    }
    /**
 * @OA\Post(
 *     tags={"Niches"},
 *     path="/api/duration-niches",
 *     summary="Create Niches",
 *     operationId="store_duration",
 *     @OA\Parameter(
 *         name="niche_id",
 *         in="query",
 *         required=true,
 *         description="Status",
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="exten_year",
 *         in="query",
 *         required=true,
 *         description="Status",
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="exten_price",
 *         in="query",
 *         required=true,
 *         description="Status",
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

public function store_duration(Request $request)
{   

    $v = Validator::make($request->all(), [
        'niche_id'          => 'required',
        'exten_year'        => 'required',
        'exten_price'       => 'required',
    ]); 

    if ($v->fails())
    {
        return response()->json([
            'status' => 'error',
            'errors' => $v->errors()->first()
        ], 422);
    }
    $niche = Duration::create($request->all());
    if($niche) {
        return response()->json([
            'status' => 'Successfully Added Duration',
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
 *     tags={"Niches"},
 *     path="/api/duration-niches",
 *     summary="Get list Niches",
 *     operationId="list_duration",
 *     @OA\Parameter(
 *         name="id",
 *         in="query",
 *         description="limit page",
 *         @OA\Schema(
 *             type="integer",
 *             format="int64",
 *             minimum=1,
 *         )
 *     ),
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

public function list_duration(Request $request)
{
    $limit = intval($request->query('limit'));
    $filter = json_decode($request->query('filter'));
    $duration = Duration::where('niche_id',$request->id);
    if (!empty($filter->all)) {
        $key_word = $filter->all;
        $duration->where(function ($query) use ($key_word) {
            $query->where('id', 'like', '%'.$key_word.'%')
                ->orWhere('exten_year','like', '%'.$key_word.'%')
                ->orWhere('exten_price','like', '%'.$key_word.'%');
        });
    }
    if (!empty($filter->id)) {
        $duration->where('id', 'like', '%'.$filter->id.'%');
    }
    if (!empty($filter->exten_year)) {
        $duration->where('exten_year', 'like', '%'.$filter->exten_year.'%');
    }
    if (!empty($filter->exten_price)) {
        $duration->where('exten_price', 'like', '%'.$filter->exten_price.'%');
    }
    
    $duration = $duration->paginate($limit)->toArray();
    $duration['status'] = "success"; 

    return response()->json($duration, 200);
}
/**
 * @OA\PUT(
 *     tags={"Niches"},
 *     path="/api/duration-niches/{id}",
 *     summary="Create Niches",
 *     operationId="update_duration",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID",
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="niche_id",
 *         in="query",
 *         required=true,
 *         description="Status",
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="exten_year",
 *         in="query",
 *         required=true,
 *         description="Status",
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="exten_price",
 *         in="query",
 *         required=true,
 *         description="Status",
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
public function update_duration(Request $request, $id)
{
    $v = Validator::make($request->all(), [
        'niche_id'          => 'required',
        'exten_year'        => 'required',
        'exten_price'       => 'required',
    ]);
    
    if ($v->fails())
    {
        return response()->json([
            'status' => 'error',
            'errors' => $v->errors()->first()
        ], 422);
    }

    $niche = Duration::where('id', $id)->first();
    if(!$niche) {
        return response()->json([
            'status' => 'error',
            'errors' => "Cannot find duration"
        ], 404);
    }
    $niche = $niche->update($request->all());
    if($niche) {
        return response()->json([
            'status' => 'Successfully Updated Duration',
            'data'   => $niche
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
 *     tags={"Niches"},
 *     path="/api/duration-niches",
 *     summary="Delete Niches",
 *     operationId="deleteDuration",
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

public function deleteDuration(Request $request)
{
    $ids = $request->ids;
    if(is_string($ids)){
        $ids = json_decode($ids);
    }
    $duration = Duration::whereIn('id', $ids)->delete();

    if($duration) {
        return response()->json(
            [
                'status' => 'Successfully Deleted Duration.',
            ], 200);
    }
    return response()->json(
        [
            'status' => 'error',
            'errors' => 'Cannot find duration.'
        ], 404);

}
/**
 * @OA\GET(
 *     tags={"Niches"},
 *     path="/api/list-duration-niches",
 *     summary="List Duration Niches",
 *     operationId="listDurationNiche",
  *     @OA\Parameter(
 *         name="niche_id",
 *         in="query",
 *         required=true,
 *         description="ID niches",
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

public function listDurationNiche(Request $request)
{
    $niche_id = $request->niche_id;

    $duration = Duration::where('niche_id', $niche_id)->wherenull('deleted_at')->get();

    if(!empty($duration)) {
        return response()->json(
            [
                'status' => 'Successfully.',
                'data'   => $duration
            ], 200);
    }
    return response()->json(
        [
            'status' => 'error',
            'errors' => 'Cannot find duration.'
        ], 404);     
}

/**
 * @OA\GET(
 *     tags={"Niches"},
 *     path="/api/update-status-niches",
 *     summary="Status Niches",
 *     operationId="updateStatusNiche",
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

    public function updateStatusNiche(Request $request)
    {
        $status = Reference::where([
            ['reference_type', 'like', 'booking_status'],
            ['reference_value_text', 'like', 'Booked']
        ])->first();
        $status_booking = $status->id;
        $niche = Niche::whereHas('booking', function (Builder $query) use ($status_booking) {
            $query->where([
                    ['status', '!=',  $status_booking],
                ]);
            })->update(['status' => 'Unavailable']);
        
        if($niche){
            return response()->json(
                [
                    'status' => 'Successfully.',
                ], 200);
        }
        return response()->json(
            [
                'error' => 'Error.',
            ], 200);
    }

    public function updateAllNiche(Request $request){
        $variable = Niche::where('id', '>', 0)->get();
        foreach ($variable as $key => $value) {
            $value->full_location = $value->wing.' Wing'.' - Bay '.$value->bay.' - Floor '.$value->floor.' - Block '.$value->block.', Niche Level '.$value->level.', '.$value->unit;
            $value->save();
        }

    }

    /**
    * @OA\Post(
    *     tags={"Niches"},
    *     path="/api/bulk-action",
    *     summary="Bulk Action",
    *      @OA\Parameter(
    *         name="type_action",
    *         in="query",
    *         required=true,
    *         description="Type Action ('extension, status, price')",
    *         @OA\Schema(
    *             type="string"
    *         )
    *     ),
    *     @OA\RequestBody(
    *         @OA\MediaType(
    *             mediaType="application/x-www-form-urlencoded",
    *             @OA\Schema(
    *                 type="object",
    *                 @OA\Property(
    *                     property="ids",
    *                     description="ID Niche",
    *                     type="string",
    *                     default="[1,2,3]"
    *                  ),
    *                  required={"ids"}
    *             )
    *         )
    *     ),
    *    @OA\Parameter(
    *         name="extension_duration",
    *         in="query",
    *         description="Extension Duration",
    *         @OA\Schema(
    *             type="string"
    *         )
    *     ),
    *     @OA\Parameter(
    *         name="price",
    *         in="query",
    *         description="Price",
    *         @OA\Schema(
    *             type="integer"
    *         )
    *     ),
    *     @OA\Parameter(
    *         name="status",
    *         in="query",
    *         description="Status",
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
    public function bulkActionUpdateNiche(Request $request){
        if(!empty($request->type_action)){
            if($request->type_action == 'extension'){
                $v = Validator::make($request->all(), [
                    'extension_duration'    =>  'required',
                    'price'                 =>  'required',
                    'ids'                   =>  'required'
                ]);
                if ($v->fails())
                {
                    return response()->json([
                        'status' => 'error',
                        'errors' => $v->errors()->first()
                    ], 422);
                }
                $ids = $request->ids;
                if(is_string($ids)){
                    $ids = json_decode($ids);
                }
                foreach ($ids as $id) {
                    Duration::create([
                        'niche_id'              =>  $id,
                        'exten_price'           =>  $request->price,
                        'exten_year'            =>  $request->extension_duration,
                    ]);
                }
                return response()->json([
                    'status' => 'Successfully Added Duration',
                ], 200);
            }else if ($request->type_action == 'status') {
                $v = Validator::make($request->all(), [
                    'status'                =>  'required',
                    'ids'                   =>  'required'
                ]);
                if ($v->fails())
                {
                    return response()->json([
                        'status' => 'error',
                        'errors' => $v->errors()->first()
                    ], 422);
                }
                $ids = $request->ids;
                if(is_string($ids)){
                    $ids = json_decode($ids);
                }
                foreach ($ids as $id) {
                    $niche = Niche::where('id', $id)->first();
                    if(!empty($niche) && !empty($niche->booking_id)){
                        return response()->json([
                            'status' => 'error',
                            'errors' => "Niches ".$niche->reference_no.' is having booking cannot be update status.'
                        ], 422);
                    }
                    $niche->update(['status' => $request->status]);
                }
                return response()->json([
                    'status' => 'Successfully Updated Status',
                ], 200);
            }else if ($request->type_action == 'price') {
                $v = Validator::make($request->all(), [
                    'price'                =>  'required',
                    'ids'                   =>  'required'
                ]);
                if ($v->fails())
                {
                    return response()->json([
                        'status' => 'error',
                        'errors' => $v->errors()->first()
                    ], 422);
                }
                $ids = $request->ids;
                if(is_string($ids)){
                    $ids = json_decode($ids);
                }
                foreach ($ids as $id) {
                    Niche::where('id', $id)->update(['price' => $request->price]);
                }
                return response()->json([
                    'status' => 'Successfully Updated Price',
                ], 200);
            }
        }else{
            return response()->json([
                'error' => 'Cannot find action',
            ], 422);
        }
    }

    /**
 * @OA\Get(
 *     tags={"Niches"},
 *     path="/api/all-id-niche",
 *     summary="Get list ID Niches",
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

    public function allIDNiche(Request $request)
    {   
        $niches = Niche::where('id', '>', 0);
        $filter = json_decode($request->query('filter'));
        // Where all
        if(!empty($filter)){
            if (!empty($filter->all)) {
                $key_word = $filter->all;
                $niches->where(function ($query) use ($key_word) {
                    $query->whereHas('type', function (Builder $query) use ($key_word) {
                        $query->where(
                            [
                                ['reference_type', '=', 'type_niche'],
                                ['reference_value_text', 'like', '%'.$key_word.'%'],
                            ]
                        );
                    })
                    ->orWhereHas('booking_line_item.information', function(Builder $query) use ($key_word){
                                $query->where('full_name', 'like', '%'.$key_word.'%');
                    })
                    ->orWhereHas('booking_line_item', function(Builder $query) use ($key_word){
                        $query->where('duration_of_lease', 'like', '%'.$key_word.'%');
                    })
                    ->orWhere('full_location',     'like', "%".$key_word."%")
                    ->orWhere('reference_no', 'like', '%'.$key_word.'%')
                    ->orWhere('status', 'like', $key_word."%");
                });
            }
            // Where id
            if (!empty($filter->id)) {
                $niches->where('reference_no', 'like', '%'.$filter->id.'%');
            }
            // Where type
            if (!empty($filter->type)) {
                $type = $filter->type;
                $niches->whereHas('type', function (Builder $query) use ($type) {
                    $query->where(
                        [
                            ['reference_type', '=', 'type_niche'],
                            ['reference_value_text', 'like', '%'.$type.'%'],
                        ]
                    );
                });
            }
            // Where location
            if (!empty($filter->location)) {
                $key_word = $filter->location;
                $niches->where(function ($query) use ($key_word) {
                    $query->where('full_location',  'like', "%".$key_word."%");
                });
            }
            // Where status
            if (!empty($filter->status)) {
                $niches->where('status', 'like', $filter->status."%");
            }
            
            if (!empty($filter->occupant_name)) {
                $occupant_name = $filter->occupant_name;
                $niches->whereHas('booking_line_item.information', function (Builder $query) use ($occupant_name) {
                    $query->where('full_name', 'like', '%'.$occupant_name.'%');
                });
            }

            if (!empty($filter->duration_of_lease)) {
                $duration_of_lease = $filter->duration_of_lease;
                $niches->whereHas('booking_line_item', function (Builder $query) use ($duration_of_lease) {
                    $query->where('duration_of_lease', 'like', '%'.$duration_of_lease.'%');
                });
            }
            $niches = $niches->pluck('id')->toArray();
        }else{
            $niches = [];
        }
        return response()->json($niches, 200);
    }
}