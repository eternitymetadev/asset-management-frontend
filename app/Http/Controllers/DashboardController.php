<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use Carbon\Carbon;
use DB;
use Auth;

class DashboardController extends Controller
{
    public $prefix;
    public $title;
    public $segment;

    public function __construct()
    {
      $this->title =  "Dashboard";
      $this->segment = \Request::segment(2);
    }
    public function index()
    {
        $this->prefix = request()->route()->getPrefix();
        
    
        return view('dashboard',['prefix'=>$this->prefix,'title'=>$this->title]);
    }

    public function ForbiddenPage(Request $request)
    {
        return view('forbidden');
    }

    public function ForgotSession(){
        // Session::forget('lead-search');
        Session::forget('peritem');
        Session::forget('startdate'); 
        Session::forget('endate');
        
        Session::forget('internalperitem');
        Session::forget('searchvehicle');
         
         $response['success'] = true;
         return response()->json($response);
    }
    
    
}
