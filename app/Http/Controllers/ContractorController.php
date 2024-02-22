<?php

namespace App\Http\Controllers;

use App\Contractor;
use App\Http\Controllers\UserController;
use App\Other;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Builder;
use App\User;


class ContractorController extends Controller
{
    
/**
 * @OA\Get(
 *     tags={"Contractor"},
 *     path="/api/contractor",
 *     summary="Get list Contractor",
 *     operationId="indexContractor",
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
 *         description="filter by object json {email : customer@example.com}",
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
    public function indexContractor(Request $request)
    {   
        $limit = intval($request->query('limit'));
        $contractors = Contractor::where('id', '>', 0)->where('is_contractor', true)->with('services');
        $filter = json_decode($request->query('filter'));

        if (!empty($filter->company_name)) {
            $contractors->where('company_name', 'like', '%'.$filter->company_name.'%');
        }
        if (!empty($filter->address)) {
            $contractors->where('address', 'like', '%'.$filter->address.'%');
        }
        if (!empty($filter->website)) {
            $contractors->where('website', 'like', '%'.$filter->website.'%');
        }
        // Where service
        if (!empty($filter->services)) {
            $service = $filter->services;
            $contractors->whereHas('services', function (Builder $query) use ($service) {
                $query->where(
                    [
                        ['service_name', 'like', '%'.$service.'%'],
                    ]   
                );
            });
        }
        if (!empty($filter->all)) {
            $key_word = $filter->all;
            $contractors->where(function ($query) use ($key_word) {
                $query->whereHas('services', function (Builder $query) use ($key_word) {
                    $query->where(
                        [
                            ['service_name', 'like', '%'.$key_word.'%'],
                        ]   
                    );
                })
                ->orWhere('company_name',  'like', "%".$key_word."%")
                ->orWhere('address',  'like', "%".$key_word."%")
                ->orWhere('website',  'like', "%".$key_word."%");
            });
        }
        ///
        
        $contractors = $contractors->paginate($limit)->toArray();
        $contractors['status'] = "success";
       
        return response()->json($contractors, 200);
    }

    /**
 * @OA\Get(
 *     tags={"Contractor"},
 *     path="/api/contractor/{id}",
 *     summary="Get detail Contractor",
 *     operationId="showContractor",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID Contractor",
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

    public function showContractor($id)
    {
        $contractor = Contractor::with('services')->find($id);
        // dd($contractor->services);
        $contractor->services = null;
        // $contractor = $contractor->find($id);
        UserController::ordinalNumber($contractor->id);
        if($contractor) {   
            return response()->json(
                [
                    'status'        => 'success',
                    'data'          => $contractor->toArray()
                ], 200);
        }
        return response()->json(
            [
                'status' => 'error',
                'errors' => 'Cannot find contractor'
            ], 404);
    }

/**
 * @OA\Post(
 *     tags={"Contractor"},
 *     path="/api/contractor",
 *     summary="Create Contractor",
 *     operationId="createContractor",
 *    @OA\Parameter(
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
 *     @OA\Parameter(
 *         name="postal_code",
 *         in="query",
 *         required=true,
 *         description="Postal Code",
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Parameter(
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
 *     @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType="application/x-www-form-urlencoded",
 *             @OA\Schema(
 *                 type="object",
 *                 @OA\Property(
 *                     property="service_id",
 *                     description="List service_id",
 *                     type="string",
 *                     default="[1,2,3]"
 *                  ),
 *                  required={"ids"}
 *             )
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

    public function createContractor(Request $request)
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
        
        $contractor = new Contractor();
        $contractor->company_name       = $request->company_name;
        $contractor->bank_name          = $request->bank_name;
        $contractor->account_number     = $request->account_number;
        $contractor->address            = $request->address;
        $contractor->website            = $request->website;
        $contractor->company_main_tel   = $request->company_main_tel;
        $contractor->uen_no             = $request->uen_no;
        $contractor->remarks            = $request->remarks;
        $contractor->postal_code        = $request->postal_code;
        $contractor->is_contractor      = true;
        $contractor->save();

        $services = $request->service_id;
        if (is_string($services)) {
            $services = json_decode($services);
        }
        // dd($services);
        if($contractor->save()){
            if(!empty($services)){
                foreach($services as $key=>$value){
                    $contractor->services()->attach($value);
                }
            }
            
        }
        if($contractor) {
            $contractor = Contractor::with('services')->find($contractor->id);
            // UserController::ordinalNumber($contractor->id);
            return response()->json([
                'status'        => 'Successfully Added Contractor',
                'data'          => $contractor
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
 *     tags={"Contractor"},
 *     path="/api/contractor/{id}",
 *     summary="Update Contractor",
 *     operationId="updateContractor",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID Contractor",
 *         @OA\Schema(
 *             type="integer",
 *             format="int64",
 *             minimum=1,
 *         )
 *     ),
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
 *     @OA\Parameter(
 *         name="postal_code",
 *         in="query",
 *         required=true,
 *         description="Postal Code",
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Parameter(
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
 *     @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType="application/x-www-form-urlencoded",
 *             @OA\Schema(
 *                 type="object",
 *                 @OA\Property(
 *                     property="service_id",
 *                     description="List service_id",
 *                     type="string",
 *                     default="[1,2,3]"
 *                  ),
 *                  required={"ids"}
 *             )
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

    public function updateContractor(Request $request, $id)
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
        $contractor = Contractor::where('id', $id)->first();
        if(!$contractor) {
            return response()->json([
                'status' => 'error',
                'errors' => "Cannot find contractor"
            ], 404);
        } 
        $services_new = $request->service_id;
        if (is_string($services_new)) {
            $services_new = json_decode($services_new);
        }
        $services_old = $contractor->services->pluck('id')->toArray();
        ///
        $contractor->company_name       = $request->company_name;
        $contractor->bank_name          = $request->bank_name;
        $contractor->account_number     = $request->account_number;
        $contractor->address            = $request->address;
        $contractor->website            = $request->website;
        $contractor->company_main_tel   = $request->company_main_tel;
        $contractor->uen_no             = $request->uen_no;
        $contractor->remarks            = $request->remarks;
        $contractor->postal_code        = $request->postal_code;
        $contractor->is_contractor      = true;
        $contractor->save();
        ///
        if($contractor->save()){
            if(!empty($services_new)){
               
                foreach($services_new as $key=>$value){
                    if(!in_array($value,$services_old)){
                        $contractor->services()->attach($value);
                    }
                }
                if(!empty($services_old)){
                    foreach($services_old as $key=>$value){
                        if(!in_array($value,$services_new)){
                            $contractor->services()->detach($value);
                        }
                    }
                }
               
            }else{
                $contractor->services()->detach();
            }
        }
        if($contractor) {
            $contractor = Contractor::with('services')->find($id);
            UserController::ordinalNumber($contractor->id);
            return response()->json([
                'status' => 'Successfully Update Contractor',
                'data'   => $contractor
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
 *     tags={"Contractor"},
 *     path="/api/contractor",
 *     summary="Delete Contractor",
 *     operationId="deleteContractor",
 *      @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType="application/x-www-form-urlencoded",
 *             @OA\Schema(
 *                 type="object",
 *                 @OA\Property(
 *                     property="ids",
 *                     description="ID Contractor",
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

    public function deleteContractor(Request $request)
    {
        $ids = $request->ids;
        if(is_string($ids)){
            $ids = json_decode($ids);
        }

        $contractors = Contractor::whereIn('id', $ids);
        foreach($contractors->get() as $contractor){
            $contractor->services()->detach();
        }
        $contractors = $contractors->delete();
        if($contractors) {
            $contact_person = User::whereIn('company_id', $ids)->delete();
            if($contact_person){
                return response()->json(
                    [
                        'status' => 'Successfully Deleted Contractor',
                    ], 200);
            }
            
        }
        return response()->json(
            [
                'status' => 'error',
                'errors' => 'Cannot find contractor'
            ], 404);
    }

/**
 * @OA\Get(
 *     tags={"Contractor"},
 *     path="/api/list-contractor",
 *     summary="Get list all contractor",
 *     operationId="getAllContractor",
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

    public function getAllContractor(Request $request){
        $contractors = Contractor::where('is_contractor', true)->whereNull('deleted_at')->get();
        return response()->json(
            [
                'status' => 'success',
                'data' => $contractors
            ], 200);
    }
    
}