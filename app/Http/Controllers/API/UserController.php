<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Role;
use App\Models\Location;
use App\Models\Department;
use App\Models\Permission;
use Helper;
use URL;
use Auth;

class UserController extends Controller
{
    public function userList(Request $request)
    {
        try{
            $authuser = Auth::user();
            $query = User::where('id','!=',$authuser->id)->where('status','=','1')->get();
            if($query){
                $data = $query;
                $message = "Users fetched Successfully";
                $status = true;
                $errorCode = 200;
            }
            
        }catch(Exception $e) {
            $data = $query;
            $message = "Invalid Record";
            $status = false;
            $errorCode = 402;
        }
        return Helper::apiResponseSend($message,$data,$status,$errorCode);
    }

    public function getRole()
    {
        // $getpermissions = Permission::all();
            $getroles = Role::all();
            $getlocations = Location::all();
            $getdepartments = Department::all();
            $getpermissions = Permission::all();

            $data['roles'] = $getroles;
            $data['locations'] = $getlocations;
            $data['departments'] = $getdepartments;
            $data['permissions'] = $getpermissions;
            $message = "Role and location list fetch";
            $status = true;
            $errorCode = 200;
            
        return Helper::apiResponseSend($message,$data,$status,$errorCode);
    }

    public function registerUser(Request $request)
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
            $password = Hash::make($request->password);
            $requestData = $request->only('email', $password);
            $requestData['password'] = $password;
            $requestData['user_password'] = $request->password;
            $requestData['name'] = $request->name;
            $requestData['login_id'] = $request->login_id;
            $requestData['phone'] = $request->phone;
            $requestData['role_id'] = $request->role_id;
            // $requestData['department_id'] = $request->department_id;
            $requestData['status'] = 1;
            
            try {
                $getUser = User::create($requestData);
                if ($getUser){
                    $data = '';
                    $message = "Register successful";
                    $status = true;
                    $errorCode = 200;
                } else{
                    $data = $requestData;
                    $message = "Invalid Record";
                    $status = false;
                    $errorCode = 402;
                }
            } catch (Exception $e) {
                $data = '';
                $message = $e->message;
                $status = false;
                $errorCode = 500;
            }
        }
        return Helper::apiResponseSend($message,$data,$status,$errorCode);
    }

    public function updateUser(Request $request, $id)
    {
        try {
            $this->prefix = request()->route()->getPrefix();
            $rules = array(
                // 'name' => 'required',
                // 'login_id' => 'required',
                // 'email'  => 'required',
            );
            $validator = Validator::make($request->all(),$rules);
            
            if($validator->fails())
            {
                $errors                  = $validator->errors();
                $response['success']     = false;
                $response['formErrors']  = true;
                $response['errors']      = $errors;
                return response()->json($response);
            }
            $getpass = User::where('id',$id)->get();

            $usersave['login_id']   = $request->login_id;
            $usersave['password']    = $request->password;
            $usersave['name']       = $request->name;
            $usersave['email']      = $request->email;
            $usersave['phone']      = $request->phone;

            if(!empty($request->password)){
                $usersave['password'] = Hash::make($request->password);
                $usersave['user_password'] = $request->password;
            }else if(!empty($getpass->password)){
                $usersave['password'] = $getpass->password;
            }
                
            $updateuser = User::where('id',$id)->update($usersave);
            if($updateuser){
                // $userid = $request->user_id;
                // UserPermission::where('user_id',$userid)->delete();
                // if(!empty($request->permisssion_id)){                
                //     foreach ($request->permisssion_id as $key => $permissionvalue)  {
                //         $savepermissions[] = [
                //           'user_id'=>$userid,
                //           'permisssion_id'=>$permissionvalue,
                //         ];   
                //     }
                //     UserPermission::insert($savepermissions); 
                // }

                $data = '';
                $message = "User Updated Successfully";
                $status = true;
                $errorCode = 200;
            }else{
                $data = $usersave;
                $message = "Invalid Record";
                $status = false;
                $errorCode = 402;
            }
        }catch(Exception $e) {
            $data = $usersave;
            $message = "Invalid Record";
            $status = false;
            $errorCode = 402;
        }
        return Helper::apiResponseSend($message,$data,$status,$errorCode);
    }
    
    public function updatePassword(Request $request, $id)
    {
        try {
            $this->prefix = request()->route()->getPrefix();
            $rules = array(
                // 'name' => 'required',
                // 'login_id' => 'required',
                // 'email'  => 'required',
            );
            $validator = Validator::make($request->all(),$rules);
            
            if($validator->fails())
            {
                $errors                  = $validator->errors();
                $response['success']     = false;
                $response['formErrors']  = true;
                $response['errors']      = $errors;
                return response()->json($response);
            }
            $getpass = User::where('id',$id)->get();
            
            if(!empty($request->password)){
                $usersave['password'] = Hash::make($request->password);
                $usersave['user_password'] = $request->password;
            }else if(!empty($getpass->password)){
                $usersave['password'] = $getpass->password;
            }
                
            $updateuser = User::where('id',$id)->update($usersave);
            if($updateuser){
                $data = '';
                $message = "User Updated Successfully";
                $status = true;
                $errorCode = 200;
            }else{
                $data = $usersave;
                $message = "Invalid Record";
                $status = false;
                $errorCode = 402;
            }
        }catch(Exception $e) {
            $data = $usersave;
            $message = "Invalid Record";
            $status = false;
            $errorCode = 402;
        }
        return Helper::apiResponseSend($message,$data,$status,$errorCode);
    }   
    
    public function userDetail($userid)
    {
        $authuser = Auth::user();
        if($authuser->role_id==1){
            $getuser = User::where('id',$userid)->first();
        }else{
            $getuser = User::select('name','email','phone','role_id')->where('id',$userid)->first();
            // ->exclude(['user_password', 'email'])
        }
        if(!empty($getuser)){
            $data = $getuser;
            $message = "User fetched Successfully";
            $status = true;
            $errorCode = 200;
        }else{
            $data = $getuser;
            $message = "User fetch failed";
            $status = false;
            $errorCode = 402;
        }
        return Helper::apiResponseSend($message,$data,$status,$errorCode);
    }

    public function deleteUser(Request $request,$id)
    {
        try{
            $user = User::where('id',$id)->update(['status'=> '0']);
            if($user){
                $data = '';
                $message = "User deleted Successfully";
                $status = true;
                $errorCode = 200;
            }else{
                $data = $user;
                $message = "Delete Failed";
                $status = false;
                $errorCode = 402;
            }
        }catch(Exception $e) {
            $data = $user;
            $message = "Delete Failed";
            $status = false;
            $errorCode = 402;
        }
        return Helper::apiResponseSend($message,$data,$status,$errorCode);
    }

    public function getEmail($email_id)
    {
        $user_email_check = User::select('email')->where('email',$email_id)->first();
        if($user_email_check){
            $data = $user_email_check;
            $message = "User email already exist";
            $status = true;
            $errorCode = 200;
        }else{
            $data = '';
            $message = "Can not fetch email";
            $status = false;
            $errorCode = 402;
        }
        return Helper::apiResponseSend($message,$data,$status,$errorCode);
    }
    
}