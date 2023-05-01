<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventory;
use App\Models\Category;
use App\Models\InventoryHistory;
use App\Models\InventoryInvoice;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InventoryExport;
use Validator;
use Helper;
use Config;
use Storage;
use Session;
use DB;
use URL;
use Auth;
use Crypt;

class InventoryController extends Controller
{
    public function inventoryList(Request $request)
    {
        try{
            $authuser = Auth::user();
            $query = InventoryInvoice::with('Category','Brand','Inventories','Inventories.Unit')->where('status','=','1')->get();
            if($query){
                $data = $query;
                $message = "Inventories fetched Successfully";
                $status = true;
                $errorCode = 200;
            }
        }catch(Exception $e) {
            $data = $query;
            $message = "Invalid Record";
            $status = false;
            $errorCode = 401;
        }
        return Helper::apiResponseSend($message,$data,$status,$errorCode);
    }

    public function createInventory(Request $request)
    {
        // dd($request->all());
        try {
            $this->prefix = request()->route()->getPrefix();
            $rules = array(
                // 'name' => 'required',
                // 'login_id' => 'required',
                // 'email'  => 'required',
                // 'name'      => ['required', 'string', 'unique:brands'],
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

            if(!empty($request->unit_id)){
                $addinventory['unit_id'] = $request->unit_id;
            }
            if(!empty($request->vendor_id)){
                $addinventory['vendor_id'] = $request->vendor_id;
            }
            if(!empty($request->invoice_no)){
                $addinventory['invoice_no'] = $request->invoice_no;
            }
            if(!empty($request->invoice_date)){
                $addinventory['invoice_date'] = $request->invoice_date;
            }       
            if(!empty($request->invoice_price)){
                $addinventory['invoice_price'] = $request->invoice_price;
            }    
            if(!empty($request->invoice_count)){
                $addinventory['invoice_count'] = $request->invoice_count;
            }
            
            $addinventory['created_user_id'] = $authuser->id;
            $addinventory['status'] = 1;

            // upload rc image
            // if($request->invoice_image){
            //     $file = $request->file('invoice_image');
            //     $path = 'public/images/inventory';
            //     $name = Helper::uploadImage($file,$path);
            //     $addinventory['invoice_image']  = $name;
            // }

            $saveinventory = Inventory::create($addinventory);
        
            if($saveinventory){
                // insert inventory invoices
                if (!empty($request->data)) {
                    $get_data = $request->data;
                    foreach ($get_data as $key => $save_data) {
                        $save_invc['inventory_id'] = $saveinventory->id;
                        $save_invc['sno'] = $save_data['sno'];
                        $save_invc['category_id'] = $save_data['category_id'];
                        $save_invc['brand_id'] = $save_data['brand_id'];
                        $save_invc['model'] = $save_data['model'];
                        $save_invc['unit_price'] = $save_data['unit_price'];
                        $save_invc['status'] = 1;

                        // if($save_data['invc_image']){
                        //     $file = $save_data['invc_image'];
                        //     $path = 'public/images/inventory_invoice';
                        //     $name = Helper::uploadImage($file,$path);
                        //     $save_invc['invc_image']  = $name;
                        // }

                        $un_id = InventoryInvoice::select('id','un_id')->latest('un_id')->first();
                        if (empty($un_id) || $un_id == null) {
                            $un_id = 100001;
                        } else {
                            $un_id = $un_id['un_id'] + 1;
                        }
                        $save_invc['un_id'] = $un_id;
                        $saveinventoryinvoices = InventoryInvoice::insert($save_invc);
                    }
                }

                $data = '';
                $message = "Inventory created successfully";
                $status = true;
                $errorCode = 200;
            }else{
                $data = $addinventory;
                $message = "Invalid Record";
                $status = false;
                $errorCode = 401;
            }
        }catch(Exception $e) {
            $data = '';
            $message = $e->message;
            $status = false;
            $errorCode = 500;
        }
        return Helper::apiResponseSend($message,$data,$status,$errorCode);
    }

    public function getVendor($id)
    {
        try{
            $data['unit_id'] = $id;
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://beta.finfect.biz/api/getVendorList/".$id,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            ));

            $response = curl_exec($curl);

            curl_close($curl);

            $res = json_decode($response);
            // dd($res);
            $data['vendor_data'] = $res->data;

            if($data)
            {
                $data = $data;
                $message = "Vendors fetched Successfully";
                $status = true;
                $errorCode = 200;
            }
            
        }catch(Exception $e) {
            $data = $query;
            $message = "Invalid Record";
            $status = false;
            $errorCode = 401;
        }
        return Helper::apiResponseSend($message,$data,$status,$errorCode);
    }

}
