<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\UserPermission;
use Config;
use Response;
use Session;
use Validator;
use DB;
use URL;
use Auth;
use Crypt;
use Helper;
use Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->title = "Users";
        $this->segment = \Request::segment(2);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        $peritem = Config::get('variable.PER_PAGE');
        $query = User::query();

        if ($request->ajax()) {
            $query = $query->with('UserRole');

            if (!empty($request->search)) {
                $search = $request->search;
                $searchT = str_replace("'", "", $search);
                $query->where(function ($query) use ($search, $searchT) {
                    $query->where('id', 'like', '%' . $search . '%')
                    ->orWhere('name', 'like', '%' . $search . '%');
                        
                });
            }
            if ($request->peritem) {
                Session::put('peritem', $request->peritem);
            }

            $peritem = Session::get('peritem');
            if (!empty($peritem)) {
                $peritem = $peritem;
            } else {
                $peritem = Config::get('variable.PER_PAGE');
            }
            $users = $query->orderBy('id', 'DESC')->paginate($peritem);
            $users = $users->appends($request->query());

            $html = view('users.user-list-ajax', ['prefix' => $this->prefix, 'users' => $users, 'peritem' => $peritem])->render();

            return response()->json(['html' => $html]);
        }

        $users = $query->with('UserRole')->orderBy('id', 'DESC')->paginate($peritem);
        $users = $users->appends($request->query());
        
        return view('users.user-list', ['prefix' => $this->prefix, 'peritem' => $peritem, 'users' => $users]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->prefix = request()->route()->getPrefix();
        $getpermissions = Permission::all();
        $locations = Helper::getLocations();
        $getroles = Role::all();

        return view('users.create-user', ['prefix' => $this->prefix, 'segment' => $this->segment, 'locations' => $locations, 'getroles' => $getroles, 'getpermissions' => $getpermissions]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        $rules = array(
            'name' => 'required',
            'login_id' => 'required|unique:users,login_id',
            'email'  => 'required',
            'password' => 'required',
        );

        $validator = Validator::make($request->all(),$rules);
    
        if($validator->fails())
        {
            $errors                  = $validator->errors();
            $response['success']     = false;
            $response['validation']  = false;
            $response['formErrors']  = true;
            $response['errors']      = $errors;
            return response()->json($response);
        }
        if(!empty($request->name)){
            $usersave['name']   = $request->name;
        }
        if(!empty($request->login_id)){
            $usersave['login_id']   = $request->login_id;
        }
        if(!empty($request->email)){
            $usersave['email']  = $request->email;
        }
        if(!empty($request->password)){
            $usersave['password'] = Hash::make($request->password);
        }

        if(!empty($request->role_id)){
            $usersave['role_id']   = $request->role_id;
        }
        $usersave['user_password'] = $request->password;
        // $usersave['branch_id']     = $request->branch_id;
        $usersave['phone']         = $request->phone;
        if(!empty($request->location_id)){
            $usersave['location_id']  = $request->location_id;
            // $branch = $request->location_id;
            // $usersave['location_id']  = implode(',',$branch);
        }
        $usersave['status'] = "1";
        
        $saveuser = User::create($usersave);
        if($saveuser)
        {
            if(!empty($request->permisssion_id)){         
                foreach ($request->permisssion_id as $key => $permissionvalue){
                    $savepermissions[] = [
                        'user_id' => $saveuser->id,
                        'permisssion_id' => $permissionvalue,
                    ];
                }
                UserPermission::insert($savepermissions); 
            }
            $url    =   URL::to($this->prefix.'/users');
            $response['success'] = true;
            $response['success_message'] = "Users Added successfully";
            $response['error'] = false;
            $response['page'] = 'user-create';
            $response['redirect_url'] = $url;
        }else{
            $response['success'] = false;
            $response['error_message'] = "Can not created user please try again";
            $response['error'] = true;
        }
        return response()->json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
