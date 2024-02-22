<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Other;
use App\Reference;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Builder;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

class OtherController extends Controller
{
/**
 * @OA\Post(
 *     tags={"Other"},
 *     path="/api/other",
 *     summary="Create Other",
 *     operationId="store",
 *     @OA\Parameter(
 *         name="service_name",
 *         in="query",
 *         required=true,
 *         description="Service Name",
 *         @OA\Schema(
 *             type="string",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="type",
 *         in="query",
 *         required=true,
 *         description="Type",
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="is_contractor",
 *         in="query",
 *         required=true,
 *         description="Is Contractor",
 *         @OA\Schema(
 *             type="string",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="status",
 *         in="path",
 *         required=true,
 *         description="Status",
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

    public function store(Request $request)
    {   
        $v = Validator::make($request->all(), [
            'service_name'      => 'required',
            'type'              => 'required',
            'is_contractor'     => 'required',
            'status'            => 'required',
            'category_type'     => 'required',
        ]);

        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()->first()
            ], 422);
        }
        $other = Other::create($request->all());
        if($other) {
            $other = Other::with('type')->with('contractor')->find($other->id);
            return response()->json([
                'status' => 'Successfully Added Other',
                'data'   => $other
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
 *      tags={"Other"},
 *      path="/api/other",
 *      summary="Get list Other",
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
    public function index(Request $request)
    {   
        $limit = intval($request->query('limit'));
        $others = Other::where('id', '>', 0)->whereNull('parent_id');
        $filter = json_decode($request->query('filter'));

        if(!empty($filter->id)){
            $others->where('id', 'like', '%'.$filter->id.'%');
        }

        if (!empty($filter->service_name)) {
            $others->where('service_name', 'like', '%'.$filter->service_name.'%');
        }
        if (!empty($filter->type)) {
            $type = $filter->type;
            $others->whereHas('type', function (Builder $query) use ($type) {
                $query->where(
                    [
                        ['reference_type', '=', 'other_type'],
                        ['reference_value_text', 'like', '%'.$type.'%'],
                    ]
                );
            });
        }
        // if (!empty($filter->type)) {
        //     $others->where('type','like', '%'.$filter->type.'%');
        // }

        if (!empty($filter->is_contractor)) {
            $is_contractor = $filter->is_contractor;
            $others->whereHas('contractor', function (Builder $query) use ($is_contractor) {
                $query->where(
                    [
                        ['reference_type', '=', 'contractor_required'],
                        ['reference_value_text', 'like', '%'.$is_contractor.'%'],
                    ]
                );
            });
        }
        if (!empty($filter->all)) {
            $key_word = $filter->all;
            $others->where(function ($query) use ($key_word) {
                $query->whereHas('type', function (Builder $query) use ($key_word) {
                    $query->where(
                        [
                            ['reference_type', '=', 'other_type'],
                            ['reference_value_text', 'like', '%'.$key_word.'%'],
                        ]
                    );
                })
                ->orWhereHas('contractor', function (Builder $query) use ($key_word) {
                    $query->where(
                        [
                            ['reference_type', '=', 'contractor_required'],
                            ['reference_value_text', 'like', '%'.$key_word.'%'],
                        ]
                    );
                })
                ->orWhere('id',             'like', "%".$key_word."%")
                ->orWhere('service_name',   'like', "%".$key_word."%");
            });
        }
        $others = $others->with('type', 'contractor', 'category')->paginate($limit)->toArray();
        $others['status'] = "success"; 
        return response()->json($others, 200);
    }

/**
 * @OA\Delete(
 *     tags={"Other"},
 *     path="/api/other",
 *     summary="Delete Other",
 *     operationId="delete",
 *      @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType="application/x-www-form-urlencoded",
 *             @OA\Schema(
 *                 type="object",
 *                 @OA\Property(
 *                     property="ids",
 *                     description="ID Other",
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
        $others = Other::whereIn('id', $ids)->delete();
        
        if($others) {
            $service_type = Other::whereIn('parent_id', $ids)->delete();
            // if($service_type){
            //     return response()->json(
            //         [
            //             'status' => 'Successfully Deleted Other',
            //         ], 200);
            // }
            return response()->json(
                [
                    'status' => 'Successfully Deleted Additional Service',
                ], 200);
        }
        return response()->json(
            [
                'status' => 'error',
                'errors' => 'Cannot find Additional Service'
            ], 404);
    }

/**
 * @OA\Put(
 *     tags={"Other"},
 *     path="/api/other/{id}",
 *     summary="Update Other",
 *     operationId="update",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Id Other",
 *         @OA\Schema(
 *             type="integer",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="service_name",
 *         in="query",
 *         required=true,
 *         description="Service Name",
 *         @OA\Schema(
 *             type="string",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="type",
 *         in="query",
 *         required=true,
 *         description="Type",
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="is_contractor",
 *         in="query",
 *         required=true,
 *         description="Is Contractor",
 *         @OA\Schema(
 *             type="string",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="status",
 *         in="path",
 *         required=true,
 *         description="Status",
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

    public function update(Request $request, $id)
    {
        $v = Validator::make($request->all(), [
            'service_name'      => 'required',
            'type'              => 'required',
            'is_contractor'     => 'required',    
            'status'            => 'required',
            'category_type'     => 'required',
        ]);

        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()->first()
            ], 422);
        }
        $other = Other::where('id', $id)->first();
        if(!$other) {
            return response()->json([
                'status' => 'error',
                'errors' => "Cannot find Additional Service"
            ], 404);
        } 
        $other = $other->update($request->all());
        if($other) {
            $other = Other::with('type')->with('contractor')->find($id);

            return response()->json([
                'status' => 'Successfully Update Other',
                'data'   => $other
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
*     tags={"Other"},
*     path="/api/other/{id}",
*     summary="Get detail Other",
*     operationId="detail",
*     @OA\Parameter(
*         name="id",
*         in="path",
*         required=true,
*         description="ID Other",
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
        $other = Other::whereNull('parent_id')->with('type', 'status', 'contractor', 'category')->find($id);
        // $other = Other::with('type')->with('contractor')->find($id);
        
        if($other) {
            $this->ordinalNumberService($other->id);
            return response()->json(
                [
                    'status' => 'success',
                    'data' => $other->toArray()
                ], 200);
        }
        return response()->json(
            [
                'status' => 'error',
                'errors' => 'Cannot find Additional Service'
            ], 404);
    }

/**
 * @OA\Get(
 *      tags={"Other"},
 *      path="/api/other-by-contractor",
 *      summary="Get list Other By Contractor",
 *      operationId="index",
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
    public function listOtherByContractor(){
        $others = Other::where('id', '>', 0)->whereNull('parent_id');
        $others->whereHas('contractor', function (Builder $query){
            $query->where(
                [
                    ['reference_type', '=', 'contractor_required'],
                    ['reference_value_text', '=', 'Yes'],
                ]
            );
        });
        $others = $others->with('type')->with('contractor')->get();
        return response()->json(
            [
                'status' => 'success',
                'data' => $others->toArray()
            ], 200);
    }

/**
 * @OA\Post(
 *     tags={"Service Type"},
 *     path="/api/service-type",
 *     summary="Create Service Type",
 *     operationId="createServiceType",
 * *     @OA\Parameter(
 *         name="parent_id",
 *         in="query",
 *         required=true,
 *         description="Parent Id",
 *         @OA\Schema(
 *             type="integer",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="service_name",
 *         in="query",
 *         required=true,
 *         description="Service Name",
 *         @OA\Schema(
 *             type="string",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="price",
 *         in="query",
 *         required=true,
 *         description="Price",
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

    public function createServiceType(Request $request){
        $v = Validator::make($request->all(), [
            'parent_id'     => 'required',  
            'service_name'  => 'required',
            'price'         => 'required',
        ]);

        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()->first()
            ], 422);
        }
        $other = Other::create($request->all());
        if($other) {
            $other = Other::with('type')->with('contractor')->find($other->id);
            $this->ordinalNumberService($other->parent_id);
            return response()->json([
                'status' => 'Successfully Added Service Type',
                'data'   => $other
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
 *      tags={"Service Type"},
 *      path="/api/service-type",
 *      summary="Get list Service Type",
 *      operationId="listServiceType",
 *      @OA\Parameter(
 *         name="parent_id",
 *         in="query",
 *         description="Parent ID",
 *         @OA\Schema(
 *             type="integer"
 *         )
 *      ),
 *      @OA\Parameter(
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

    public function listServiceType(Request $request){
        $limit = intval($request->query('limit'));
        $others = Other::where('parent_id', $request->parent_id);
        $filter = json_decode($request->query('filter'));
        $this->ordinalNumberService($request->parent_id);
        if(!empty($filter->id)){
            $others->where('id', 'like', '%'.$filter->id.'%');
        }
        if (!empty($filter->service_type_name)) {
            $others->where('service_name', 'like', '%'.$filter->service_type_name.'%');
        }
        if (!empty($filter->price)) {
            $others->where('price', 'like', '%'.$filter->price.'%');
        }
        if (!empty($filter->all)) {
            $key_word = $filter->all;
            $others->where(function ($query) use ($key_word) {
                $query->where('id', 'like', '%'.$key_word.'%')
                ->orWhere('service_name', 'like', '%'.$key_word.'%')
                ->orWhere('price', 'like', '%'.$key_word.'%');
            });
        }
        if(!empty($filter->ordinal_number)){
            $others->where('ordinal_number', 'like', '%'.$filter->ordinal_number.'%');
        }
        $others = $others->paginate($limit)->toArray();
        $others['status'] = "success"; 
        return response()->json($others, 200);
    }
/**
 * @OA\Put(
 *     tags={"Service Type"},
 *     path="/api/service-type/{id}",
 *     summary="Update Service Type",
 *     operationId="updateServiceType",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Id Other",
 *         @OA\Schema(
 *             type="integer",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="parent_id",
 *         in="query",
 *         required=true,
 *         description="Type",
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="service_name",
 *         in="query",
 *         required=true,
 *         description="Service Name",
 *         @OA\Schema(
 *             type="string",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="price",
 *         in="query",
 *         required=true,
 *         description="Price",
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

    public function updateServiceType(Request $request, $id)
    {
        $v = Validator::make($request->all(), [
            'parent_id'     => 'required',  
            'service_name'  => 'required',
            'price'         => 'required',
        ]);

        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()->first()
            ], 422);
        }
        $other = Other::where('id', $id)->first();
        if(!$other) {
            return response()->json([
                'status' => 'error',
                'errors' => "Cannot find description"
            ], 404);
        } 
        $other = $other->update($request->all());
        if($other) {
            $other = Other::whereNotNull('parent_id')->find($id);

            return response()->json([
                'status' => 'Successfully Update Service Type',
                'data'   => $other
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
 *     tags={"Service Type"},
 *     path="/api/service-type",
 *     summary="Delete Service Type",
 *     operationId="deleteServiceType",
 *      @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType="application/x-www-form-urlencoded",
 *             @OA\Schema(
 *                 type="object",
 *                 @OA\Property(
 *                     property="ids",
 *                     description="ID Other",
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

    public function deleteServiceType(Request $request)
    {
        $ids = $request->ids;
        if(is_string($ids)){
            $ids = json_decode($ids);
        }
        foreach($ids as $id){
            $service_type_default = Other::where('id', $id)->where('is_default', 1)->first();
            if($service_type_default){
                return response()->json([
                    'status' => 'error',
                    'errors' => 'Cannot Delete Default Service Type'
                ], 400);
            }
        }
        $service_type = Other::whereIn('id', $ids)->whereNotNull('parent_id')->delete();
        
        if($service_type) {
            return response()->json(
                [
                    'status' => 'Successfully Deleted Service Type',
                ], 200);
        }
        return response()->json(
            [
                'status' => 'error',
                'errors' => 'Cannot find Service Type'
            ], 404);
    }

/**
 * @OA\Get(
 *     tags={"All Service"},
 *     path="/api/list-service",
 *     summary="Get list Service",
 *     operationId="getServiceAndChild",
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

    public function getServiceAndChild(Request $request){
        $status = Reference::where('reference_type', 'other_status')->where('reference_value_text', 'Available')->first();
        $service_type = Other::whereNull('deleted_at')
        ->whereNull('parent_id')
        ->where('status', $status->id)
        ->with('contractor', 'children', 'type')
        ->has('children')
        ->orderBy('service_name', 'ASC')
        ->get();
        return response()->json(
            [
                'status' => 'success',
                'data' => $service_type
            ], 200);
    }
    public static function ordinalNumberService($parent_id){
        
        $service_type = Other::where('parent_id', $parent_id)->orderBy('id', 'asc')->get();
        
            for($i = 0; $i < count($service_type); $i++){
                foreach($service_type as $key=>$value){
                    if($key == $i){
                        $value->ordinal_number = $i+1;
                        $value->save();
                    }
                }
            }
        return 0;
    }
  /**
 * @OA\Post(
 *     tags={"Other"},
 *     path="/api/import-services",
 *     summary="Import file Services&Products excel",
 *      @OA\RequestBody(
 *          @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 type="object",
 *                 @OA\Property(
 *                     property="file_excel",
 *                     description="file_excel services",
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
    public function importServices(Request $request){
        ini_set('max_execution_time', 3000);
        $file = $request->file("file_excel");
        if(!empty($file)){
            $reader = ReaderEntityFactory::createXLSXReader();
            $reader->setShouldFormatDates(true);
            $reader->open($file);
            // dd($reader);
            $services = array();

            if(!empty($reader)){
                foreach ($reader->getSheetIterator() as $sheet) {
                    if($sheet->getName() == "Products & Services"){
                        foreach ($sheet->getRowIterator() as $row) {
                            $cells = $row->getCells();
                            //
                            $typeCells                   = $cells[0]->getValue();
                            $itemCells                  = $cells[1]->getValue();
                            $priceCells                 = $cells[2]->getValue();
                            $contractorCells          = $cells[3]->getValue();
                            //
                            if(!($typeCells == "Type"||$itemCells == "Item"||$priceCells == "Price"||$contractorCells == "Supplier/Contractor")){
                                if(!($typeCells == "" || $typeCells == " ")){
                                    $type_sale = Reference::where(
                                        [
                                            ['reference_type', '=', 'other_type'],
                                            ['reference_value_text', '=', 'Sale'],
                                        ]
                                    )->first();
                                    //
                                    $contractor_yes = Reference::where(
                                        [
                                            ['reference_type', '=', 'contractor_required'],
                                            ['reference_value_text', '=', 'Yes'],
                                        ]
                                    )->first();
                                    //
                                    $contractor_no = Reference::where(
                                        [
                                            ['reference_type', '=', 'contractor_required'],
                                            ['reference_value_text', '=', 'No'],
                                        ]
                                    )->first();
                                    //
                                    $status = Reference::where(
                                        [
                                            ['reference_type', '=', 'other_status'],
                                            ['reference_value_text', '=', 'Available'],
                                        ]
                                    )->first();
                                    //
                                    if($contractorCells == ""|| $contractorCells == " "){
                                        $is_contractor = $contractor_no->id;
                                    }else{
                                        $is_contractor = $contractor_yes->id;
                                    }

                                    $service = array(
                                        'type'               => $typeCells, 
                                        'item'               => $itemCells, 
                                        'price'              => $priceCells,
                                        'is_contractor'      => $is_contractor,
                                        'status'             => $status->id,
                                        'type_service'       => $type_sale->id, 
                                    );
                                    
                                    array_push($services, $service);                                    
                                }
                            }
                        }
                    }
                } 
                $reader->close();
                foreach($services as $key=>$service){
                    $key_temp = $key+1;
                    if($key_temp <= count($services)){
                        // if($key != 0){
                        //     dd($services[$key]['type'] != $services[$key-1]['type']);
                        // }
                        if($key == 0){
                            $check_type = Other::where('service_name', '=', $service['type'])->first();

                            //Services 
                            if(isset($check_type)){
                                $parent_id = $check_type->id;
                            }else{
                                $service_new = Other::create([
                                    'service_name'           => $service['type'],
                                    'type'                   => $service['type_service'],
                                    'is_contractor'          => $service['is_contractor'],
                                    'status'                 => $service['status'],
                                ]);
                                if($service_new){
                                    $parent_id = $service_new->id;
                                }
                            }
                           
                            
                        }elseif($services[$key]['type'] != $services[$key-1]['type']){
                            $check_type = Other::where('service_name', '=', $service['type'])->first();

                            if(isset($check_type)){
                                $parent_id = $check_type->id;
                            }else{
                                $service_new = Other::create([
                                    'service_name'           => $service['type'],
                                    'type'                   => $service['type_service'],
                                    'is_contractor'          => $service['is_contractor'],
                                    'status'                 => $service['status'],
                                ]);
                                if($service_new){
                                    $parent_id = $service_new->id;
                                }
                            }
                           
                        }
                        if(isset($parent_id)){
                            $check_service_type = Other::where('service_name', '=', $service['item'])->first();
                            //Services Type
                            if(!isset($check_service_type)){
                                Other::create([
                                    'service_name'           => $service['item'],
                                    'price'                  => $service['price'],
                                    'parent_id'              => $parent_id,
                                ]);
                            }
                        }
                    }                      
                }
                //
                // $path = public_path().'/import/services/';
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

    public function getListServiceByType($id){
        $other = Other::whereNull('parent_id')->with('type', 'status', 'contractor')->where("type", $id)->get();
        return response()->json(
            [
                'status' => 'success',
                'data' => $other
            ], 200);
    }
    public function getListOtherCategory(){
        $list_category = Reference::where('reference_type', 'other_category')->get();
        return response()->json(
            [
                'status' => 'success',
                'data' => $list_category
            ], 200);
    }

}