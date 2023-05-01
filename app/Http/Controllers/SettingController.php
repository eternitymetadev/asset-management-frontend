<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventory;
use App\Models\Category;
use App\Models\Brand;
use App\Models\User;
use Validator;
use Helper;
use Config;
use Storage;
use Session;
use DB;
use URL;
use Auth;
use Crypt;

class SettingController extends Controller
{

    public function __construct()
    {
        $this->title = "Inventories";
        $this->segment = \Request::segment(2);
    }

    public function Category(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        $peritem = Config::get('variable.PER_PAGE');
        $query = Category::query();

        if ($request->ajax()) {
            if (isset($request->resetfilter)) {
                Session::forget('peritem');
                $url = URL::to($this->prefix.'/'.$this->segment.'/category');
                return response()->json(['success' => true, 'redirect_url' => $url]);
            }

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

            $categories = $query->orderBy('id', 'ASC')->paginate($peritem);
            $categories = $categories->appends($request->query());

            $html = view('settings.category-list-ajax', ['prefix' => $this->prefix, 'peritem' => $peritem, 'categories' => $categories])->render();

            return response()->json(['html' => $html]);
        }
         
        $categories = $query->orderBy('id', 'ASC')->paginate($peritem);
        $categories = $categories->appends($request->query());

        return view('settings.category-list', ['prefix' => $this->prefix, 'segment' => $this->segment, 'peritem' => $peritem, 'categories' => $categories]);
    }

    public function addCategory(Request $request)
    {
        try {
            DB::beginTransaction();

            $this->prefix = request()->route()->getPrefix();
            $rules = array(
                // 'name'    => 'required|unique:locations',
            );
            $validator = Validator::make($request->all() , $rules);
            if ($validator->fails())
            {
                // $a['name']  = "This name already exists";
                $errors                 = $validator->errors();
                $response['success']    = false;
                $response['validation'] = false;
                $response['formErrors'] = true;
                $response['errors']     = $errors;
                return response()->json($response);
            }
            $authuser = Auth::user();

            if(!empty($request->name)){
                $addcategory['name'] = $request->name;
            }
            $addcategory['status'] = 1;
        
            $savecategory = Category::create($addcategory);
            if($savecategory){
                
                $response['success']    = true;
                $response['page']       = 'category-create';
                $response['error']      = false;
                $response['success_message'] = "Category created successfully";
                $response['redirect_url'] = URL::to($this->prefix.'/settings/category');
            }else{
                $response['success']       = false;
                $response['error']         = true;
                $response['error_message'] = "Can not created category please try again";
            }
        
            DB::commit();
        } catch (Exception $e) {
            $response['error'] = false;
            $response['error_message'] = $e;
            $response['success'] = false;
        }
        return response()->json($response);

    }

    public function Brand(Request $request)
    {
        $this->prefix = request()->route()->getPrefix();
        $peritem = Config::get('variable.PER_PAGE');
        $query = Brand::query();

        if ($request->ajax()) {
            if (isset($request->resetfilter)) {
                Session::forget('peritem');
                $url = URL::to($this->prefix.'/'.$this->segment.'/brand');
                return response()->json(['success' => true, 'redirect_url' => $url]);
            }

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

            $brands = $query->orderBy('id', 'ASC')->paginate($peritem);
            $brands = $brands->appends($request->query());

            $html = view('settings.brand-list-ajax', ['prefix' => $this->prefix, 'peritem' => $peritem, 'brands' => $brands])->render();

            return response()->json(['html' => $html]);
        }
         
        $brands = $query->orderBy('id', 'ASC')->paginate($peritem);
        $brands = $brands->appends($request->query());

        return view('settings.brand-list', ['prefix' => $this->prefix, 'segment' => $this->segment, 'peritem' => $peritem, 'brands' => $brands]);
    }

    public function addBrand(Request $request)
    {
        try {
            DB::beginTransaction();

            $this->prefix = request()->route()->getPrefix();
            $rules = array(
                // 'name'    => 'required|unique:locations',
            );
            $validator = Validator::make($request->all() , $rules);
            if ($validator->fails())
            {
                // $a['name']  = "This name already exists";
                $errors                 = $validator->errors();
                $response['success']    = false;
                $response['validation'] = false;
                $response['formErrors'] = true;
                $response['errors']     = $errors;
                return response()->json($response);
            }
            $authuser = Auth::user();

            if(!empty($request->name)){
                $addbrand['name'] = $request->name;
            }
            $addbrand['status'] = 1;
        
            $savebrand = Brand::create($addbrand);
            if($savebrand){
                
                $response['success']    = true;
                $response['page']       = 'brand-create';
                $response['error']      = false;
                $response['success_message'] = "Brand created successfully";
                $response['redirect_url'] = URL::to($this->prefix.'/settings/brand');
            }else{
                $response['success']       = false;
                $response['error']         = true;
                $response['error_message'] = "Can not created brand please try again";
            }
        
            DB::commit();
        } catch (Exception $e) {
            $response['error'] = false;
            $response['error_message'] = $e;
            $response['success'] = false;
        }
        return response()->json($response);

    }

}
