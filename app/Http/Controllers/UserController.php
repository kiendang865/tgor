<?php

namespace App\Http\Controllers;

use App\User;
use App\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Reference;
use App\Payment;
use File;
use Illuminate\Support\Facades\Validator;
use Box\Spout\Autoloader;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
// use Mail;
use Illuminate\Support\Facades\Mail;
use App\Mail\ForgetPassword as ForgetPassword;
use Socketlabs\SocketLabsClient;
use Socketlabs\Message\BasicMessage;
use Socketlabs\Message\EmailAddress;
use Illuminate\Support\Arr;

class UserController extends Controller
{
    /**
 * @OA\Get(
 *      tags={"Customer"},
 *      path="/api/customer",
 *      summary="get list customer",
 *      operationId="indexCustomer",
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
 *         name="filter",
 *         in="query",
 *         description="filter by object json {email : customer@example.com}",
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
    public function indexCustomer(Request $request)
    {   
        $limit = intval($request->query('limit'));
        $users = User::role('customer')->where('id', '>', 0);
        $filter = json_decode($request->query('filter'));
        if (!empty($filter->name)) {
            $users->where('display_name', 'like', '%'.$filter->name.'%');
        }
        if (!empty($filter->phone)) {
            $users->where('phone', 'like', '%'.$filter->phone.'%');
        }
        if (!empty($filter->email)) {
            $users->where('email', 'like', '%'.$filter->email.'%');
        }
        if (!empty($filter->passport)) {
            $users->where('passport', 'like', '%'.$filter->passport.'%');
        }
        if(!empty($filter->address)){
            $users->where('display_address',  'like', '%'.$filter->address.'%');
        }
        if(!empty($filter->search_customer)){
            $key_word = $filter->search_customer;
            $users->where(function ($query) use ($key_word) {
                $query->where('display_name', 'like', "%".$key_word."%")
                      ->orWhere('passport',   'like', "%".$key_word."%");
            });
        }
        if (!empty($filter->all)) {
            $key_word = $filter->all;
            $users->where(function ($query) use ($key_word) {
                $query->where('display_name', 'like', "%".$key_word."%")
                      ->orWhere('phone',      'like', "%".$key_word."%")
                      ->orWhere('passport',   'like', "%".$key_word."%")
                      ->orWhere('display_address',   'like', "%".$key_word."%")
                      ->orWhere('email',      'like', "%".$key_word."%");
            });
        }
        if(!empty($filter)){
            $users = $users->orderBy("display_name");
        }
        if($limit == -1){
            $users = $users->with('religion','preferredContactBy', 'isTgor', 'salutation')->toArray();
        }else{
            $users = $users->with('religion','preferredContactBy', 'isTgor', 'salutation')->paginate($limit)->toArray();
        }
        $users['status'] = "success"; 
        return response()->json($users, 200);
    }

     /**
 * @OA\Get(
 *      tags={"Customer"},
 *      path="/api/update-salutation",
 *      summary="upadate salutation customer",
 *      operationId="updateSalutation",
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
    public function updateSalutation(){
        $user = User::role('customer')->get();
        foreach($user as $key=>$value){
            // dd($value);
            $salutation_id = Reference::where(
                [
                    ['reference_type', '=', 'salutation'],
                    ['reference_value_text', 'like', '%'.$value->salutation.'%'],
                ]
            )->first();
            // dd($salutation_id);
            if($salutation_id){
                // dd($salutation_id);
                $update_user = User::find($value->id);
                // dd($update_user);
                $update_user->salutation = $salutation_id->id;
                $update_user->save();
            }
        }
        if($user) {
            return response()->json([
                'status' => 'Successfully Updated Customer',
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
 *     tags={"Customer"},
 *     path="/api/customer/{id}",
 *     summary="Get detail customer",
 *     operationId="showCustomer",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID customer",
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
    public function showCustomer($id)
    {
        $user = User::role('customer')->with('religion','preferredContactBy', 'isTgor', 'salutation')->find($id);
        
        if($user) {
            return response()->json(
                [
                    'status' => 'success',
                    'data' => $user->toArray()
                ], 200);
        }
        return response()->json(
            [
                'status' => 'error',
                'errors' => 'Cannot find client.'
            ], 404);
    }
    /**
 * @OA\Post(
 *      tags={"Customer"},
 *      path="/api/customer",
 *      summary="Create customer",
 *      operationId="createCustomer",
 *     @OA\Parameter(
 *         name="display_name",
 *         in="query",
 *         description="Full name customer",
 *          @OA\Schema(
 *              type="string"
 *          )
 *     ),
 *     @OA\Parameter(
 *         name="phone",
 *         in="query",
 *         description="Mobile number customer",
 *          @OA\Schema(
 *             type="integer",
 *             format="int64",
 *             minimum=1,
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="passport",
 *         in="query",
 *         description="Passport customer",
 *          @OA\Schema(
 *              type="string"
 *          )
 *     ),
 *     @OA\Parameter(
 *         name="email",
 *         in="query",
 *         description="Email customer",
 *          @OA\Schema(
 *              type="string"
 *          )
 *     ),
 *     @OA\Parameter(
 *         name="nationality",
 *         in="query",
 *         description="Nationality customer",
 *          @OA\Schema(
 *              type="string"
 *          )
 *     ),
 *     @OA\Parameter(
 *         name="display_address",
 *         in="query",
 *         description="Address customer",
 *          @OA\Schema(
 *              type="string"
 *          )
 *     ),
 *    @OA\Parameter(
 *         name="postal_code",
 *         in="query",
 *         description="Postal Code customer",
 *          @OA\Schema(
 *              type="string"
 *          )
 *     ),
 *   @OA\Parameter(
 *         name="alternative_contact_no",
 *         in="query",
 *         description="Alternative Contact No customer",
 *          @OA\Schema(
 *              type="string"
 *          )
 *     ),
 *    @OA\Parameter(
 *         name="church_attended",
 *         in="query",
 *         description="Church Attended customer",
 *          @OA\Schema(
 *              type="string"
 *          )
 *     ),
 *    @OA\Parameter(
 *         name="religion_id",
 *         in="query",
 *         description="Religion customer (id : 4 - 3)",
 *         @OA\Schema(
 *             type="integer",
 *             format="int64",
 *             minimum=1,
 *         )
 *     ),
 *    @OA\Parameter(
 *         name="is_tgor",
 *         in="query",
 *         description="how did you hear about us| know tgor (id : 1 - 2)",
 *         @OA\Schema(
 *             type="integer",
 *             format="int64",
 *             minimum=1,
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="remarks",
 *         in="query",
 *         description="Remarks",
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
    public function createCustomer(Request $request)
    {
        
        $v = [
            'display_name'            => 'required',
            'passport'                => 'required',
            'nationality'             => 'required',
            'religion_id'             => 'required',
            'display_address'         => 'required',
        ];
        if(!empty($request->postal_code)){
            $v = Arr::add($v, 'postal_code', 'numeric|digits:6');
        }
        $customMessages = [
            'email.email'                        =>  'The email field must be correct format in Client Info.',
            'display_name.required'              =>  'The full name field is required in Client Info.',
            'passport.required'                  =>  'The passport field is required in Client Info.',
            'nationality.required'               =>  'The nationality field is required in Client Info.',
            'display_address.required'           =>  'The address field is required in Client Info.',
            'postal_code.numeric'                =>  'The postal code field must be number in Client Info.',
            'postal_code.digits'                 =>  'The postal code field must be 6 digits in Client Info.',
            'religion_id.required'               =>  'The religion field is required in Client Info.',
        ];
        $validator = Validator::make($request->all(), $v, $customMessages);
        if ($validator->fails())
        {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()->first()
            ], 422);
        }
        if(!empty($request->email)){
            $check_email = User::role('customer')->where('email', $request->email)->first();
            if(!empty($check_email)){
                return response()->json([
                    'status' => 'error',
                    'errors' => "The email has already been taken."
                ], 422);
            }
        }
        $check_passport = User::role('customer')->where('passport', $request->passport)->first();
        if(!empty($check_passport)){
            return response()->json([
                'status' => 'error',
                'errors' => "The NRIC/Passport has already been taken."
            ], 422);
        }
        $data = $request->all();

        $name = trim($data['display_name']);
        $name = explode(' ', $name, 2);
        $salutation = ucfirst(strtolower($name[0]));
        $reference = Reference::where('reference_type', 'salutation')->where('reference_value_text', $salutation)->first();
        if(!empty($reference)){
            $data['salutation'] = $reference->id;
            $data['display_name'] = $name[1];
        }
        $user = User::create($data);
        if($user) {
            $user->assignRole('customer');
            $user = User::with('religion', 'isTgor')->find($user->id);
            return response()->json([
                'status' => 'Successfully Added Customer',
                'data'   => $user
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
 *     tags={"Customer"},
 *     path="/api/customer",
 *     summary="Delete customer",
 *     operationId="deleteCustomer",
 *     @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType="application/x-www-form-urlencoded",
 *             @OA\Schema(
 *                 type="object",
 *                 @OA\Property(
 *                     property="ids",
 *                     description="List ID",
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
    public function deleteCustomer(Request $request)
    {
        if (is_string($request->ids)) {
            $request->ids = json_decode($request->ids);
        }
        $ids = $request->ids;
        if(!$ids) {
            $ids = [];
        }
        $user = User::role('customer')->whereIn('id', $ids)->delete();
        
        if($user) {
            return response()->json(
                [
                    'status' => 'Successfully Deleted Client',
                ], 200);
        }
        return response()->json(
            [
                'status' => 'error',
                'errors' => 'Cannot find client'
            ], 404);
    }

/**
 * @OA\Put(
 *      tags={"Customer"},
 *      path="/api/customer/{id}",
 *      summary="Update customer",
 *      operationId="updateCustomer",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID customer",
 *         @OA\Schema(
 *             type="integer",
 *             format="int64",
 *             minimum=1,
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="display_name",
 *         in="query",
 *         description="Display name customer",
 *          @OA\Schema(
 *              type="string"
 *          )
 *     ),
 *     @OA\Parameter(
 *         name="email",
 *         in="query",
 *         description="email customer",
 *          @OA\Schema(
 *              type="string"
 *          )
 *     ),
 *     @OA\Parameter(
 *         name="phone",
 *         in="query",
 *         description="Phone customer",
 *          @OA\Schema(
 *              type="string"
 *          )
 *     ),
 *     @OA\Parameter(
 *         name="passport",
 *         in="query",
 *         description="Passport customer",
 *          @OA\Schema(
 *              type="string"
 *          )
 *     ),
 *     @OA\Parameter(
 *         name="nationality",
 *         in="query",
 *         description="Nationality customer",
 *          @OA\Schema(
 *              type="string"
 *          )
 *     ),
 *    @OA\Parameter(
 *         name="postal_code",
 *         in="query",
 *         description="Postal Code customer",
 *          @OA\Schema(
 *              type="string"
 *          )
 *     ),
 *     @OA\Parameter(
 *         name="display_address",
 *         in="query",
 *         description="display_address customer",
 *          @OA\Schema(
 *              type="string"
 *          )
 *     ),
 *   @OA\Parameter(
 *         name="alternative_contact_no",
 *         in="query",
 *         description="Alternative Contact No customer",
 *          @OA\Schema(
 *              type="string"
 *          )
 *     ),
 *    @OA\Parameter(
 *         name="church_attended",
 *         in="query",
 *         description="Church Attended customer",
 *          @OA\Schema(
 *              type="string"
 *          )
 *     ),
 *    @OA\Parameter(
 *         name="religion_id",
 *         in="query",
 *         description="Religion customer",
 *         @OA\Schema(
 *             type="integer",
 *             format="int64",
 *             minimum=1,
 *         )
 *     ),
 *    @OA\Parameter(
 *         name="is_tgor",
 *         in="query",
 *         description="Customer know tgor",
 *         @OA\Schema(
 *             type="integer",
 *             format="int64",
 *             minimum=1,
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="remarks",
 *         in="query",
 *         description="Remarks",
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
    public function updateCustomer(Request $request, $id)
    {
        $v = [
            'display_name'            => 'required',
            'phone'                   => 'required',
            'passport'                => 'required',
            'nationality'             => 'required',
            'email'                   => 'required|email',
            // 'postal_code'             => 'numeric|digits:6',
            // 'street_no'               => 'required',
            // 'building_name'           => 'required',
            // 'unit_no'                 => 'required',
            // 'postal_code'             => 'required',
            // 'alternative_contact_no'  => 'required',
            // 'church_attended'         => 'required',
            'religion_id'             => 'required',
            // 'preferred_contact_by_id' => 'required',
            'display_address'         => 'required',
            // 'is_tgor'                 => 'required', 
        ];
        if(!empty($request->postal_code)){
            $v = Arr::add($v, 'postal_code', 'numeric|digits:6');
        }
        $validator = Validator::make($request->all(), $v);
        if ($validator->fails())
        {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()->first()
            ], 422);
        }
        $user = User::role('customer')->where('id', $id)->first();
        if(!$user) {
            return response()->json([
                'status' => 'error',
                'errors' => "Cannot find client"
            ], 404);
        } 
        $check_email = User::where('id', '<>', $id)->where('email', $request->email)->whereNull('password')->first();
        if(!empty($check_email)){
            return response()->json([
                'status' => 'error',
                'errors' => "The email has already been taken."
            ], 422);
        }
        $data = $request->all();
        $user = $user->update($data);
        if($user) {
            $user = User::with('religion', 'isTgor')->find($id);
            return response()->json([
                'status' => 'Successfully Updated Client',
                'data'   => $user
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
     * @OA\Post(
     *     tags={"Customer"},
     *     path="/api/import-customer",
     *     summary="Import file Customer excel",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="file_excel",
     *                     description="file_excel Customer",
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
    public function importListCustomer(Request $request){
        ini_set('max_execution_time', 3000);
        $file = $request->file("file_excel");
        if(!empty($file)){
            $reader = ReaderEntityFactory::createXLSXReader();
            $reader->setShouldFormatDates(true);
            $file = public_path("/Niche Renewal DB 202007014.xlsx");
            $reader->open($file);
            
            if(!empty($reader)){
                foreach ($reader->getSheetIterator() as $sheet) {
                    if($sheet->getName() == "Customer Info"){
                        foreach ($sheet->getRowIterator() as $key=>$row) {
                            ///
                            $cells = $row->getCells();
                            //
                            $customerIDCells      = $this->checkNullValue($cells[0]->getValue());
                            $salutationCells      = $this->checkNullValue($cells[1]->getValue());
                            $lastNameCells        = $this->checkNullValue($cells[2]->getValue());
                            $firstNameCells       = $this->checkNullValue($cells[3]->getValue());
                            $phoneCells           = $this->checkNullValue($cells[6]->getValue());
                            $emailCells           = $this->checkNullValue($cells[7]->getValue());
                            $street_noCells       = $this->checkNullValue($cells[8]->getValue());
                            $street_nameCells     = $this->checkNullValue($cells[9]->getValue());
                            $unit_noCells         = $this->checkNullValue($cells[10]->getValue());
                            $postalCodeCells      = $this->checkNullValue($cells[11]->getValue());
                            //
                            if(!($customerIDCells == "Customer ID"||$salutationCells == "Salutation"||$lastNameCells == "Last Name"||$firstNameCells == "First Name"||$phoneCells == "Mobile"||$emailCells == "Email"||$street_noCells == "House No"||$street_nameCells == "Street Name"||$unit_noCells == "Unit No."||$postalCodeCells == "Postal Code")){
                                if(!($customerIDCells == " ")){
                                    $salutation = Reference::where(
                                        [
                                            ['reference_type', '=', 'salutation'],
                                            ['reference_value_text', 'like', '%'.$salutationCells.'%'],
                                        ]
                                    )->first();
                                    if($salutation){
                                        $salutationCells =$salutation->id;
                                    }
                                    $user = User::create([
                                        'salutation'              => $salutationCells,  
                                        'first_name'              => $firstNameCells,
                                        'last_name'               => $lastNameCells,
                                        'display_name'            => $firstNameCells." ".$lastNameCells,
                                        'email'                   => $emailCells,
                                        'phone'                   => $phoneCells,
                                        'street_no'               => $street_noCells,
                                        'street_name'             => $street_nameCells,
                                        'unit_no'                 => $unit_noCells,
                                        'postal_code'             => $postalCodeCells,
                                    ]);
                                    $user->assignRole('customer');
                                }
                            }
                        }
                    }
                } 
            //    
                // $path = public_path().'/import/customer/';
                // if(File::makeDirectory($path, 0777, true, true)){
                //     $name_file = $file->getClientOriginalName();
                //     $file->move($path,$name_file);
                // }else{
                //     $name_file = $file->getClientOriginalName();
                //     $file->move($path,$name_file);
                // }
    
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

    public static function checkNullValue($value){
        if($value == "" || $value == " "){
            $value = null;
        }
        return $value;
    }




/**
 * @OA\Post(
 *     tags={"User Managements"},
 *     path="/api/admin",
 *     summary="Create Niches",
 *     operationId="store",
 *     @OA\Parameter(
 *         name="name",
 *         in="query",
 *         required=true,
 *         description="Name",
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
 *     @OA\Parameter(
 *         name="password",
 *         in="query",
 *         required=true,
 *         description="Password",
 *         @OA\Schema(
 *             type="string",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="confirm_password",
 *         in="query",
 *         required=true,
 *         description="Confirm Password",
 *         @OA\Schema(
 *             type="string",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="status",
 *         in="query",
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

    public function createAdmin(Request $request){
        $v = Validator::make($request->all(), [
            'name'                  =>  'required',
            'email'                 =>  'required|email',
            'password'              =>  'required',
            'confirm_password'      =>  'required|same:password',
            'status'                =>  'required'
        ]);
        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()->first()
            ], 422);
        }
        $check_email = User::role('admin')->where('email', $request->email)->first();
        if(!empty($check_email)){
            return response()->json([
                'status' => 'error',
                'errors' => "The email has already been taken."
            ], 422);
        }
        $data = [
            "email"         =>  $request->email,
            "display_name"  =>  $request->name,
            "password"      =>  Hash::make($request['password']),
            "status"        =>  $request->status
        ];
        $user = User::create($data);
        if($user) {
            $user->assignRole('admin');
            $user = User::with('religion','preferredContactBy', 'isTgor', 'salutation')->find($user->id);
            return response()->json([
                'status' => 'Successfully Added Admin',
                'data'   => $user
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
 *      tags={"User Managements"},
 *      path="/api/admin",
 *      summary="get list admin",
 *      operationId="listAdmin",
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
 *     @OA\Parameter(
 *         name="filter",
 *         in="query",
 *         description="filter by object json {email : customer@example.com}",
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

    public function listAdmin(Request $request){
        $limit = intval($request->query('limit'));
        $users = User::role('admin')->where('id', '>', 0);
        $filter = json_decode($request->query('filter'));
        if (!empty($filter->id)) {
            $users->where('id', 'like', '%'.$filter->id.'%');
        }
        if (!empty($filter->display_name)) {
            $users->where('display_name', 'like', '%'.$filter->display_name.'%');
        }
        if (!empty($filter->email)) {
            $users->where('email', 'like', '%'.$filter->email.'%');
        }
        if (!empty($filter->status)) {
            $status = $filter->status;
            $users->whereHas('adminStatus', function (Builder $query) use ($status) {
                $query->where(
                    [
                        ['reference_type', '=', 'admin_status'],
                        ['reference_value_text', 'like', $status.'%'],
                    ]
                );
            });
        }
        if (!empty($filter->all)) {
            $key_word = $filter->all;
            $users->where(function ($query) use ($key_word) {
                $query->whereHas('adminStatus', function (Builder $query) use ($key_word) {
                    $query->where(
                        [
                            ['reference_type', '=', 'admin_status'],
                            ['reference_value_text', 'like', $key_word.'%'],
                        ]
                    );
                })
                ->orWhere('id', 'like', "%".$key_word."%")
                ->orWhere('display_name', 'like', "%".$key_word."%")
                ->orWhere('phone',      'like', "%".$key_word."%")
                ->orWhere('status',     'like', $key_word."%")
                ->orWhere('email',      'like', "%".$key_word."%");
            });
        }
        $users = $users->with('religion','preferredContactBy', 'isTgor', 'salutation', 'adminStatus')->paginate($limit)->toArray();
        $users['status'] = "success"; 
        return response()->json($users, 200);
    }

    /**
 * @OA\Get(
 *     tags={"User Managements"},
 *     path="/api/admin/{id}",
 *     summary="Get detail admin",
 *     operationId="showAdmin",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID customer",
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
    public function showAdmin($id)
    {
        $user = User::role(['admin', 'super-admin'])->with('religion','preferredContactBy', 'isTgor', 'salutation', 'adminStatus')->find($id);
        
        if($user) {
            return response()->json(
                [
                    'status' => 'success',
                    'data' => $user->toArray()
                ], 200);
        }
        return response()->json(
            [
                'status' => 'error',
                'errors' => 'Cannot find admin'
            ], 404);
    }

/**
 * @OA\Delete(
 *     tags={"User Managements"},
 *     path="/api/admin",
 *     summary="Delete Admin",
 *     operationId="deleteAdmin",
 *      @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType="application/x-www-form-urlencoded",
 *             @OA\Schema(
 *                 type="object",
 *                 @OA\Property(
 *                     property="ids",
 *                     description="ID Admin",
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

    public function deleteAdmin(Request $request)
    {
        $ids = $request->ids;
        if(is_string($ids)){
            $ids = json_decode($ids);
        }
        if (in_array(1, $ids)) {
            return response()->json([
                'status' => 'error',
                'errors' => "Something bad happened, please try later"
            ], 422);
        }
        $users = User::whereIn('id', $ids)->delete();

        if($users) {
            return response()->json(
                [
                    'status' => 'Successfully Deleted Admin',
                ], 200);
        }
        return response()->json(
            [
                'status' => 'error',
                'errors' => 'Cannot find Admin'
            ], 404);
    }

/**
 * @OA\Put(
 *     tags={"User Managements"},
 *     path="/api/admin/{id}",
 *     summary="Update Admin",
 *     operationId="updateAdmin",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Id Admin",
 *         @OA\Schema(
 *             type="integer",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="name",
 *         in="query",
 *         required=true,
 *         description="Name",
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
 *     @OA\Parameter(
 *         name="password",
 *         in="query",
 *         required=true,
 *         description="Password",
 *         @OA\Schema(
 *             type="string",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="confirm_password",
 *         in="query",
 *         required=true,
 *         description="Confirm Password",
 *         @OA\Schema(
 *             type="string",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="status",
 *         in="query",
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

    public function updateAdmin(Request $request, $id)
    {
        $v = Validator::make($request->all(), [
            'name'                  =>  'required',
            'email'                 =>  'required|email',
            'status'                =>  'required'
        ]);
        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()->first()
            ], 422);
        }
        $user = User::where('id', $id)->first();
        if(!$user) {
            return response()->json([
                'status' => 'error',
                'errors' => "Cannot find Admin"
            ], 404);
        }
        $check_email = User::role('admin')->where('id', '<>', $id)
                    ->where('email', $request->email)
                    ->first();
        if(!empty($check_email)){
            return response()->json([
                'status' => 'error',
                'errors' => "The email has already been taken."
            ], 422);
        }
        if(!empty($request['password'])){
            $data = [
                "email"         =>  $request->email,
                "display_name"  =>  $request->name,
                "password"      =>  Hash::make($request['password']),
                "status"        =>  $request->status
            ];
        }else{
            $data = [
                "email"         =>  $request->email,
                "display_name"  =>  $request->name,
                "status"        =>  $request->status
            ]; 
        }
        
        $user = $user->update($data);
        if($user) {
            $user = User::with('religion','preferredContactBy', 'isTgor', 'salutation', 'adminStatus')->find($id);
            return response()->json([
                'status' => 'Successfully Updated Admin',
                'data'   => $user
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
*     tags={"Contact Person"},
*     path="/api/contact-person",
*     summary="Get List Contact Person",
*     operationId="listContactPerson",
*     @OA\Parameter(
*         name="id",
*         in="query",
*         required=true,
*         description="ID Company",
*         @OA\Schema(
*             type="integer",
*             format="int64",
*             minimum=1,
*         )
*     ),
*      @OA\Parameter(
*         name="limit",
*         in="query",
*         description="limit",
*         @OA\Schema(
*             type="integer",
*             format="int64",
*             minimum=1,
*         )
*     ),
*      @OA\Parameter(
*         name="filter",
*         in="query",
*         description="filter by object json ",
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

    public function listContactPerson(Request $request){

        $limit = intval($request->query('limit'));
        $filter = json_decode($request->query('filter'));
        $contact_person = User::where('company_id', $request->id);
        if (!empty($filter->id)) {
            $contact_person->where('id', 'like', '%'.$filter->id.'%');
        }
        if (!empty($filter->name)) {
            $contact_person->where('display_name', 'like', '%'.$filter->name.'%');
        }
        if (!empty($filter->email)) {
            $contact_person->where('email', 'like', '%'.$filter->email.'%');
        }
        if (!empty($filter->phone)) {
            $contact_person->where('phone', 'like', '%'.$filter->phone.'%');
        }
        if (!empty($filter->all)) {
            $key_word = $filter->all;
            $contact_person->where(function ($query) use ($key_word) {
                $query->orWhere('id', 'like', "%".$key_word."%")
                ->orWhere('display_name', 'like', "%".$key_word."%")
                ->orWhere('phone',      'like', "%".$key_word."%")
                ->orWhere('email',      'like', "%".$key_word."%");
            });
        }
        if (!empty($filter->ordinal_number)) {
            $contact_person->where('ordinal_number', 'like', '%'.$filter->ordinal_number.'%');
        }
        if($contact_person){
            $this->ordinalNumber($request->id);
            $contact_person = $contact_person->paginate($limit)->toArray();
            $contact_person['status'] = "success"; 
            return response()->json($contact_person, 200);
        }

        return response()->json([
            'status' => 'error',
            'errors' => "Something bad happened, please try later"
        ], 422);
    }

/**
 * @OA\Post(
 *     tags={"Contact Person"},
 *     path="/api/contact-person",
 *     summary="Create Contact Person",
 *     operationId="createContactPerson",
 *     @OA\Parameter(
 *         name="company_id",
 *         in="query",
 *         required=true,
 *         description="Company ID",
 *         @OA\Schema(
 *             type="integer",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="display_name",
 *         in="query",
 *         required=true,
 *         description="Display Name",
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="email",
 *         in="query",
 *         required=true,
 *         description="Email",
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="phone",
 *         in="query",
 *         required=true,
 *         description="Phone",
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
    public function createContactPerson(Request $request)
    {   
        $v = Validator::make($request->all(), [
            'company_id'    =>  'required',
            'display_name'  =>  'required',
            'email'         =>  'required|email',
            'phone'         =>  'required',
        ]);

        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()->first()
            ], 422);
        }
        $check_email = User::where('company_id', $request->company_id)->where('email', $request->email)->whereNull('deleted_at')->first();
        if(!empty($check_email)){
            return response()->json([
                'status' => 'error',
                'errors' => "The email has already been taken."
            ], 422);
        }
        $users = User::create($request->all());
        if($users) {
            $users = User::whereNotNull('company_id')->find($users->id);
            $this->ordinalNumber($users->company_id);
            return response()->json([
                'status' => 'Successfully Added Contact Person',
                'data'   => $users
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
 *      tags={"Contact Person"},
 *      path="/api/contact-person/{id}",
 *      summary="Update Contact Person",
 *      operationId="updateContactPerson",
 *      @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID contact person",
 *         @OA\Schema(
 *             type="integer",
 *             format="int64",
 *             minimum=1,
 *         )
 *     ),
 *      @OA\Parameter(
 *         name="company_id",
 *         in="query",
 *         required=true,
 *         description="Company ID",
 *         @OA\Schema(
 *             type="integer",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="display_name",
 *         in="query",
 *         required=true,
 *         description="Display Name",
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="email",
 *         in="query",
 *         required=true,
 *         description="Email",
 *         @OA\Schema(
 *             type="string"
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="phone",
 *         in="query",
 *         required=true,
 *         description="Phone",
 *         @OA\Schema(
 *             type="integer",
 *         )
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
    public function updateContactPerson(Request $request, $id)
    {
        $v = Validator::make($request->all(), [
            'company_id'    =>  'required',
            'display_name'  =>  'required',
            'email'         =>  'required|email',
            'phone'         =>  'required',
        ]);

        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()->first()
            ], 422);
        }
        $user = User::where('id', $id)->first();
        if(!$user) {
            return response()->json([
                'status' => 'error',
                'errors' => "Cannot find contact person"
            ], 404);
        } 
        $check_email = User::where('id', '<>', $id)->where('email', $request->email)->first();
        if(!empty($check_email)){
            return response()->json([
                'status' => 'error',
                'errors' => "The email has already been taken."
            ], 422);
        }
        $data = $request->all();
        $user = $user->update($data);
        if($user) {
            $user = User::where('company_id', $request->company_id)->find($id);
            
            return response()->json([
                'status' => 'Successfully Updated Contact Person',
                'data'   => $user
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
 *     tags={"Contact Person"},
 *     path="/api/contact-person",
 *     summary="Delete Contact Persion",
 *     operationId="deleteContactPerson",
 *      @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType="application/x-www-form-urlencoded",
 *             @OA\Schema(
 *                 type="object",
 *                 @OA\Property(
 *                     property="ids",
 *                     description="ID Admin",
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

    public function deleteContactPerson(Request $request)
    {
        $ids = $request->ids;
        if(is_string($ids)){
            $ids = json_decode($ids);
        }
        if (in_array(1, $ids)) {
            return response()->json([
                'status' => 'error',
                'errors' => "Something bad happened, please try later"
            ], 422);
        }
        $users = User::whereIn('id', $ids)->delete();
        
        if($users) {
            return response()->json(
                [
                    'status' => 'Successfully Deleted Contact Person',
                ], 200);
        }
        return response()->json(
            [
                'status' => 'error',
                'errors' => 'Cannot find contact person'
            ], 404);
    }

    public static function ordinalNumber($company_id){
        
        $contact_person = User::where('company_id', $company_id)->orderBy('id', 'asc')->get();
        
            for($i = 0; $i < count($contact_person); $i++){
                foreach($contact_person as $key=>$value){
                    if($key == $i){
                        $value->ordinal_number = $i+1;
                        $value->save();
                    }
                }
            }
        return 0;
    }
/**
 * @OA\POST(
 *      tags={"Customer"},
 *      path="/api/donate",
 *      summary="Donate",
 *      operationId="donateCustomer",
 *      @OA\Parameter(
 *         name="user_id",
 *         in="query",
 *         required=true,
 *         description="User ID",
 *         @OA\Schema(
 *             type="integer",
 *             format="int64",
 *             minimum=1,
 *         )
 *     ),
 *      @OA\Parameter(
 *         name="user_id",
 *         in="query",
 *         required=true,
 *         description="User ID",
 *         @OA\Schema(
 *             type="integer",
 *             format="int64",
 *             minimum=1,
 *         )
 *     ),
 *      @OA\Parameter(
 *         name="amount",
 *         in="query",
 *         required=true,
 *         description="Amount",
 *         @OA\Schema(
 *             type="integer",
 *         )
 *     ),
 *      @OA\Parameter(
 *         name="remarks",
 *         in="query",
 *         required=true,
 *         description="Remarks",
 *         @OA\Schema(
 *             type="string",
 *         )
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
    public function donateCustomer(Request $request)
    {
        if($request->user_id){
            $payment_no = 1;
            $id_payment = Payment::select('id')->orderBy('id', "DESC")->first();
            if($id_payment){
                $payment_no = (int)$id_payment->id + 1;
            }
            $now = now();
            $payment = Payment::create([
                'payment_no'          =>  "PAY-".$payment_no,
                'payment_date'        =>  $now->toDateTimeString(),
                'user_id'             =>  $request->user_id,
                'total_amount'        =>  $request->amount,
                'total_tax_amount'    =>  0,
                'total'               =>  $request->amount,
                'is_donate'           => 1,
                'remarks'             => $request->remarks  
            ]);
            if($payment){
                return response()->json([
                    'status' => 'success',
                    'data'   => $payment
                ], 200);
            }
            else{
                return response()->json([
                    'status' => 'error',
                    'errors' => "Something bad happened, please try later"
                ], 422);
            }

        }else{
            return response()->json(
                [
                    'status' => 'error',
                    'errors' => 'Cannot find id'
                ], 404);
        }
    }
    /**
 * @OA\POST(
 *      tags={"Customer"},
 *      path="/api/forget-password",
 *      summary="Forget Password",
 *      operationId="userForgotPassword",
 *      @OA\Parameter(
 *         name="email",
 *         in="query",
 *         required=true,
 *         description="Email",
 *         @OA\Schema(
 *             type="string",
 *         )
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
    public function userForgotPassword(Request $request){
        
		$validator = Validator::make($request->all(), [			
			'email' => 'required|max:155|email'		
		]);
		
		if($validator->fails()){			
			
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()->first()
            ], 422);
		}else{			
            $user = User::where("email", $request->email)->first(); 
            if(empty($user)){
                return response()->json([
                    'status' => 'error',
                    'errors' => "This email does not exist."
                ], 422);
            }
			$isMailSent = Mail::to($request->email)->send(new ForgetPassword($user, base64_encode($request->email)));
            return response()->json([
                'status' => 'success',
                'data'   => "Your account is awaiting confirmation from the email. The email has been sent to ".$request->email."."
            ], 200);
		
		}
    } 
/**
* @OA\GET(
    *      tags={"Customer"},
    *      path="/api/confirm/password/{code}",
    *      summary="Confirm Password",
    *      operationId="confirmEmailLogin",
    *     @OA\Parameter(
    *         name="code",
    *         in="path",
    *         required=true,
    *         description="Code",
    *         @OA\Schema(
    *             type="string",
    *         )
    *     ),
    *     @OA\Parameter(
    *         name="password",
    *         in="query",
    *         required=true,
    *         description="Password",
    *         @OA\Schema(
    *             type="string",
    *         )
    *     ),
    *     @OA\Parameter(
    *         name="confirm_password",
    *         in="query",
    *         required=true,
    *         description="Confirm Password",
    *         @OA\Schema(
    *             type="string",
    *         )
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
    public function confirmEmailLogin(Request $request,$code){
		if(!empty($code)){
            $decode = base64_decode($code);
			// dd($params);
            $v = Validator::make($request->all(), [
                'password'              =>  'required',
                'confirm_password'      =>  'required|same:password',
            ]);
            if ($v->fails())
            {
                return response()->json([
                    'status' => 'error',
                    'errors' => $v->errors()->first()
                ], 422);
            }
            $check_email = User::where('email', $decode)->first();
            // Hash::make($request['password']),
            if(!empty($check_email)){
                $new_password = Hash::make($request['password']);

                $user = User::where('id',$check_email->id)->update(['password'=>$new_password]);

                if($user){
                    return response()->json([
                        'status' => 'success',
                        'data'   => "Reset Password Successfully."
                    ], 200);
                }
            }
		}
    }

    public function testView(){
        $user = User::where("email", "phanlinher@gmail.com")->first(); 
        $forgot_password_link = url('confirm-password/?token='.base64_encode("phanlinher@gmail.com"));
        return view('Mail.forgetPassword', compact('user', 'forgot_password_link'));
    } 
}
