<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\User;
use App\Models\UserClient;
use App\Models\UserRegClient;
use Carbon;
use Helper;
use URL;
use Auth;

class JwtAuthController extends Controller
{
    /**
     * @OA\Post(path="/login",
     *   tags={"User"},
     *   summary="Login",
     *   description="",
     *   @OA\Parameter(
     *     name="email",
     *     in="query",
     *     description="Enter email",
     *     required=true,
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="password",
     *     in="query",
     *     description="Enter password",
     *     required=true,
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *
     *  @OA\Response(
     *         response="200",
     *         description="login successful",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="token",
     *                         type="string",
     *                         description="JWT access token"
     *                     ),
     *                     example={
     *                         "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
     *                     }
     *                 )
     *             )
     *         }
     *     ),
     *   @OA\Response(response=401, description="invalid credentials"),
     *   @OA\Response(response=500, description="could not create token")
     * )
     */
     public function authenticate(Request $request)
     {
        $data = [];
        $message = "";
        $status = false;
        $errorCode = 400;

        $validator = Validator::make($request->all(),[
            'email' => 'required',
            'password' => 'required',
        ]);
        if ($validator->fails()) {   
            $status = false;
            $message =  $validator->errors();
            $errorCode = 422;
        } else{ 
            $credentials = $request->only('email', 'password');
            try { 
            $token = JWTAuth::attempt($credentials, ['exp' => Carbon\Carbon::now()->addDays(1)->timestamp]);
                if (! $token = JWTAuth::attempt($credentials)) {
                    $message = "invalid credentials";
                    $status = false;
                    $errorCode = 401;
                } else{
                    $user = Auth::user();
                    if($user->status==1){
                        $message = "login successful";
                        $status = true;
                        $errorCode = 200;
                        $data['token']  = $token;
                        $data['user'] = $user;
                        $data['warehouses'] = json_decode($user->warehouse_id);
                    }else{
                        $message = "User account is not active";
                        $status = false;
                        $errorCode = 403;
                    }

                    // dd(gettype($data['warehouses']));
                }
            } catch (JWTException $e) {
                $message = "could not create token";
                $status = false;
                $errorCode = 500;
            }
        }
        return Helper::apiResponseSend($message,$data,$status,$errorCode);
    }
    
    /**
     * @OA\Post(path="/register",
     *   tags={"User"},
     *   summary="Register",
     *   description="",
     *    security={{ "apiAuth": {} }},
     *   @OA\Parameter(
     *     name="email",
     *     in="query",
     *     description="Enter here user Email",
     *     required=true,
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="password",
     *     in="query",
     *     description="User password",
     *     required=true,
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="name",
     *     in="query",
     *     description="Enter here user name",
     *     required=true,
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="role_id",
     *     in="query",
     *     description="User role",
     *     required=true,
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="warehouse_id",
     *     in="query",
     *     description="Enter Warehouse name",
     *     required=true,
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\Response(
     *         response="200",
     *         description="User Registered successfully.",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="edit_profile",
     *                         type="string",
     *                         description="profile update"
     *                     ),
     *                     example={
     *                         "message": "User Registered successfully.",
     *                         "success": true,
     *                         "error": false
     *                     }
     *                 )
     *             )
     *         }
     *     ),
     *  
     *   @OA\Response(response=401, description="Authorization Token not found"),
     *   @OA\Response(response=500, description="Internal server error")
     * )
     */

     public function registerauthenticate(Request $request)
    {
        $data = [];
        $message = "";
        $status = false;
        $errorCode = 400;

        $validator = Validator::make($request->all(),[
            // 'email' => ['required', 'string'],
            'email' => 'required|unique:users',
            'password' => 'required',
            // 'role_id'  => 'required',
        ]);
        if ($validator->fails()) {  
            $status = false;
            $message =  $validator->errors();
            $errorCode = 422;
        } else{ 
            // $email = User::where('email',$data['email'])->first();
            // if($email){
            //     $status = false;
            //     $message = 'Email is already exists.';
            //     $errorCode = 422;
            // }else{
                $password = Hash::make($request->password);
                $requestData = $request->only('email', $password);
                $requestData['password'] = $password;
                $requestData['user_password'] = $request->password;
                $requestData['name'] = $request->name;
                $requestData['login_id'] = $request->login_id;
                $requestData['phone'] = $request->phone;
                $requestData['role_id'] = $request->role_id;
                $requestData['branch_id'] = $request->branch_id;
                $requestData['status'] = 1;
                
                try {
                    $getUser = User::create($requestData);
                    if ($getUser){
                        // $clientData['user_id'] = $getUser->id;
                        // $saveclient = UserClient::create($clientData);
                        // if ($saveclient){
                            $data = '';
                            $message = "Register successful";
                            $status = true;
                            $errorCode = 200;
                        // }else{
                        //     $data = $clientData;
                        //     $message = "Invalid Record";
                        //     $status = false;
                        //     $errorCode = 401;
                        // }
                    } else{
                        $data = $requestData;
                        $message = "Invalid Record";
                        $status = false;
                        $errorCode = 401;
                    }
                } catch (Exception $e) {
                    $data = '';
                    $message = $e->message;
                    $status = false;
                    $errorCode = 500;
                }
            // }
        }
        return Helper::apiResponseSend($message,$data,$status,$errorCode);
    }

}