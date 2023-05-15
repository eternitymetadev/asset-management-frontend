<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
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
    public function categoryList(Request $request){
        try{
            $authuser = Auth::user();
            $query = Category::get();
            if($query){
                $data = $query;
                $message = "Categories fetched Successfully";
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

    public function addCategory(Request $request)
    {
        try {
            $this->prefix = request()->route()->getPrefix();
            $rules = array(
                'name' => 'required|unique:categories',
            );
            $validator = Validator::make($request->all(),$rules);
            
            if($validator->fails())
            {
                $errors                  = $validator->errors();
                $response['status']      = false;
                $response['formErrors']  = true;
                $response['errors']      = $errors;

                return response()->json($response);
            }
            $authuser = Auth::user();

            if(!empty($request->name)){
                $addcategory['name'] = $request->name;
            }
            $addcategory['status'] = 1;
        
            $savecategory = Category::create($addcategory);
            if($savecategory){
                $data = '';
                $message = "Category created successfully";
                $status = true;
                $errorCode = 200;
            }else{
                $data = $addcategory;
                $message = "Invalid Record";
                $status = false;
                $errorCode = 402;
            }
        }catch(Exception $e) {
            $data = '';
            $message = $e->message;
            $status = false;
            $errorCode = 500;
        }
        return Helper::apiResponseSend($message,$data,$status,$errorCode);
    }

    public function updateCategory(Request $request)
    {
        try {
            $this->prefix = request()->route()->getPrefix();
            $rules = array(
                'name' => 'required',
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

            $check_name_exist = Category::where('id', '!=', $request->id)->where(['name' => $request['name']])->get();

            if (!$check_name_exist->isEmpty()) {
                $response['success']     = false;
                $response['formErrors']  = true;
                $response['errors']      = "Name already exists.";

                return response()->json($response);
            }

            $authuser = Auth::user();

            if(!empty($request->name)){
                $addcategory['name'] = $request->name;
            }
            
            $addcategory['status'] = $request->status;
            
            // $addcategory['status'] = 1;
        
            $savecategory = Category::where('id', $request->id)->update($addcategory);
            if($savecategory){
                $data = '';
                $message = "Category updated successfully";
                $status = true;
                $errorCode = 200;
            }else{
                $data = $addcategory;
                $message = "Invalid Record";
                $status = false;
                $errorCode = 402;
            }
        }catch(Exception $e) {
            $data = '';
            $message = $e->message;
            $status = false;
            $errorCode = 500;
        }
        return Helper::apiResponseSend($message,$data,$status,$errorCode);
    }

    // brand functions

    public function brandList(Request $request)
    {
        try{
            $authuser = Auth::user();
            $query = Brand::get();
            if($query){
                $data = $query;
                $message = "Brands fetched Successfully";
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

    public function addBrand(Request $request)
    {
        try {
            $this->prefix = request()->route()->getPrefix();
            $rules = array(
                'name' => 'required|unique:brands',
            );
            $validator = Validator::make($request->all(),$rules);
            
            if($validator->fails())
            {
                $errors                  = $validator->errors();
                $response['status']      = false;
                $response['formErrors']  = true;
                $response['errors']      = $errors;

                return response()->json($response);
            }
            $authuser = Auth::user();

            if(!empty($request->name)){
                $addbrand['name'] = $request->name;
            }
            $addbrand['status'] = 1;
        
            $savebrand = Brand::create($addbrand);
            if($savebrand){
                $data = '';
                $message = "Brand created successfully";
                $status = true;
                $errorCode = 200;
            }else{
                $data = $addbrand;
                $message = "Invalid Record";
                $status = false;
                $errorCode = 402;
            }
        }catch(Exception $e) {
            $data = '';
            $message = $e->message;
            $status = false;
            $errorCode = 500;
        }
        return Helper::apiResponseSend($message,$data,$status,$errorCode);
    }

    public function updateBrand(Request $request)
    {
        try {
            $this->prefix = request()->route()->getPrefix();
            $rules = array(
                'name' => 'required',
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

            $check_name_exist = Brand::where('id', '!=', $request->id)->where(['name' => $request['name']])->get();

            if (!$check_name_exist->isEmpty()) {

                $response['success']     = false;
                $response['formErrors']  = true;
                $response['errors']      = "Name already exists.";

                return response()->json($response);
            }

            $authuser = Auth::user();

            if(!empty($request->name)){
                $addbrand['name'] = $request->name;
            }
            
            $addbrand['status'] = $request->status;
            
            // $addbrand['status'] = 1;
        
            $savebrand = Brand::where('id', $request->id)->update($addbrand);
            if($savebrand){
                $data = '';
                $message = "Brand updated successfully";
                $status = true;
                $errorCode = 200;
            }else{
                $data = $addbrand;
                $message = "Invalid Record";
                $status = false;
                $errorCode = 402;
            }
        }catch(Exception $e) {
            $data = '';
            $message = $e->message;
            $status = false;
            $errorCode = 500;
        }
        return Helper::apiResponseSend($message,$data,$status,$errorCode);
    }

}