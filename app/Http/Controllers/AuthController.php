<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DB;

class AuthController extends Controller
{
    function __construct()
    {
        function __construct()
        {
            //  $this->middleware('permission:', ['only' => ['index','store']]);
        }
    }

    public function register(Request $request)
    {
        $v = Validator::make($request->all(), [
            'email' => 'required|email|unique:users',
            'password'  => 'required|min:3',
        ]);

        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()
            ], 422);
        }

        $user = new User;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();

        return response()->json(['status' => 'success'], 200);
    }

/**
 * @OA\Post(
 *     tags={"Login"},
 *     path="/api/auth/login",
 *     summary="Login",
 *     operationId="login",
 *     @OA\Parameter(
 *         name="email",
 *         in="query",
 *         description="Email",
 *         @OA\Schema(
 *             type="string",
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="password",
 *         in="query",
 *         description="Password",
 *         @OA\Schema(
 *             type="string",
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
 * )
 */

    public function login(Request $request)
    {
        $v = Validator::make($request->all(), [
            'email' => 'required|email',
            'password'  => 'required|min:3',
        ], 
        [
            'email.required' => 'The email field is required.'
        ]
        );

        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()->first(),
            ], 422);
        }
        $credentials = $request->only('email', 'password');
        if ($token = $this->guard()->attempt($credentials)) {
            $user =  User::find(Auth::user()->id);
            $status = $user->adminStatus()->first();
            $roles_id = $user->roles->pluck('id')->all();
            if($status->reference_value_text == "Inactive"){
                return response()->json([
                    'status' => 'error',
                    'errors' => 'Account has not been activated'
                ], 403);
            }
            if(in_array("super-admin", $user->getRoleNames()->toArray()) || in_array("admin", $user->getRoleNames()->toArray())) {
                
                $user =  array_merge($user->toArray(),$this->respondWithToken($token));
                unset($user['roles']);
                $user['roles_id'] = $roles_id[0];
                return response()->json([
                    'status' => 'Login success',
                    'data' => $user,
                ],200);
            }
            else {
                return response()->json([
                    'status' => 'error',
                    'errors' => 'Please login by Admin account'
                ], 403);
            }
        }

        return response()->json([
            'status' => 'error',
            'errors' => 'Invalid account or password'
        ], 401);
    }

    public function logout()
    {
        $this->guard()->logout();

        return response()->json([
            'status' => 'success',
            'msg' => 'Logged out Successfully.'
        ], 200);
    }

    public function user(Request $request)
    {
        $user = User::find(Auth::user()->id);

        return response()->json([
            'status' => 'success',
            'data' => $user
        ]);
    }

    public function refresh()
    {
        if ($token = $this->guard()->refresh()) {
            return response()
                ->json(['status' => 'successs'], 200)
                ->header('Authorization', $token);
        }

        return response()->json(['error' => 'refresh_token_error'], 401);
    }

    protected function respondWithToken($token)
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * 60
        ];
    }

    private function guard()
    {
        return Auth::guard();
    }
        /**
 * @OA\Post(
 *     tags={"Profile"},
 *     path="/api/me",
 *     summary="Profile",
 *     operationId="getUserInfo",
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
 * security={
*         {"bearerAuth": {}}
*     }
 * )
 */

    public function getUserInfo(Request $request){
        $user = User::find(Auth::user()->id);
        $roles_id = $user->roles->pluck('id')->all();
        $user['roles_id'] = $roles_id[0];
        return response()->json([
            'status' => 'success',
            'data' => $user,
        ],200);
    }
}