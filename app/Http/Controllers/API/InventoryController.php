<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventory;
use App\Models\Category;
use App\Models\Brand;
use App\Models\InventoryHistory;
use App\Models\InventoryInvoice;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InventoryExport;
use App\Imports\InventoryImport;
use Validator;
use Helper;
use Config;
use Storage;
use Session;
use Mail;
use PDF;
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
            $query = InventoryInvoice::with('Category','Brand','Inventories')->whereIn('status',[1,2,3])->get();

            $datalist = array();
            if(!empty($query)){
                foreach($query as $key => $d){
                    $datalist[$key] = $d;

                    $datalist[$key]->asset_children = json_decode($d->asset_children_id);
                }
                if($datalist){
                    $data = $datalist;
                    $message = "Inventories fetched Successfully";
                    $status = true;
                    $errorCode = 200;
                }
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
            if(!empty($request->vendor_name)){
                $addinventory['vendor_name'] = $request->vendor_name;
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
            // echo "<pre>"; print_r($request->invoice_image); die;
            // upload rc image
            // if($request->invoice_image){
            //     $file = $request->file('invoice_image');
            //     $path = 'public/images/inventory';
            //     $name = Helper::uploadImage($file,$path);
            //     $addinventory['invoice_image']  = $name;
            // }

            if ($request->invoice_image) {

                $images = $request->invoice_image;
                $path = Storage::disk('s3')->put('invoice_image', $images);
                $get_real_names = explode('/', $path);
               Storage::disk('s3')->url($path);
               $addinventory['invoice_image'] = $get_real_names[1];
            }

            $saveinventory = Inventory::create($addinventory);
        
            if($saveinventory){
                $inv_imgs = array();
                if (!empty($request->itemImages)) {
                    $get_itemimages = $request->itemImages;
                    foreach ($get_itemimages as $key => $save_data) {
                        if (!empty($save_data['invc_image'])) {
                            $images = $save_data['invc_image'];
                            $path = Storage::disk('s3')->put('invoiceitem_image', $images);
                            $get_real_names = explode('/', $path);
                            $save_invc['invc_image'] = Storage::disk('s3')->url($path);
                        }
                        $inv_imgs[] = $get_real_names[1];
                    }
                }
                $authuser = Auth::user();
                $data=array();
                
                for($i=0;$i<sizeof($inv_imgs);$i++)
                {
                    $data[$i]['invc_image'] = $inv_imgs[$i];
                    $data[$i]['inventory_id'] = $saveinventory->id;
                    $data[$i]['sno'] = $request->inventoryItems[$i]['sno'];
                    $data[$i]['category_id'] = $request->inventoryItems[$i]['category_id'];
                    $data[$i]['brand_id'] = $request->inventoryItems[$i]['brand_id'];
                    $data[$i]['model'] = $request->inventoryItems[$i]['model'];
                    $data[$i]['unit_price'] = $request->inventoryItems[$i]['unit_price'];
                    // $data[$i]['unassigned_date'] = date("d-m-Y");
                    $data[$i]['status'] = 1;

                    $un_id = InventoryInvoice::select('id','un_id')->latest('un_id')->first();
                    if (($un_id == '') || $un_id == null) {
                        $un_id = 100001;
                    } else {
                        $un_id = $un_id['un_id'] + 1;
                    }
                    $data[$i]['un_id'] = $un_id;
                    $data[$i]['asset_children_id'] = json_encode([]);
                    // echo "<pre>"; print_r(json_encode($data[$i]['asset_children_id'])); die;

                    $saveinventoryinvoices = InventoryInvoice::create($data[$i]);

                    //insert in history
                    $add_history[$i]['inventory_invoice_id'] = $saveinventoryinvoices->id;
                    $add_history[$i]['created_user_id'] = $authuser->id;
                    $add_history[$i]['status'] = 1;
                    // $add_history[$i]['unassigned_date'] = date("d-m-Y");

                    InventoryHistory::create($add_history[$i]);
                }
                // print_r($data);
                // exit;
                // $invitems = array();
                // if (!empty($request->inventoryItems)) {
                //     $get_data = $request->inventoryItems;
                //     foreach ($get_data as $key => $save_data) {
                //         $save_invc['inventory_id'] = $saveinventory->id;
                //         $save_invc['sno'] = $save_data['sno'];
                //         $save_invc['category_id'] = $save_data['category_id'];
                //         $save_invc['brand_id'] = $save_data['brand_id'];
                //         $save_invc['model'] = $save_data['model'];
                //         $save_invc['unit_price'] = $save_data['unit_price'];
                //         $save_invc['status'] = 1;
                //         $save_invc[$key]['inv_img']=$inv_imgs[$key]['invc_image'];
                //         // $save_invc['invc_image'] = $inv_imgs;


                //         $un_id = InventoryInvoice::select('id','un_id')->latest('un_id')->first();
                //         if (empty($un_id) || $un_id == null) {
                //             $un_id = 100001;
                //         } else {
                //             $un_id = $un_id['un_id'] + 1;
                //         }
                //         $save_invc['un_id'] = $un_id;

                //         $invitems[] = $save_invc;
                        
                //     }
                //     echo "<pre>"; print_r($save_invc); die;
                    // echo "<pre>"; print_r(array_merge($inv_imgs,$invitem_imgs)); die;

                    // $saveinventoryinvoices = InventoryInvoice::insert($save_invc);
                // }
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

    public function updateAssignStatus(Request $request)
    {
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
              
            if(!empty($request->status)){
                $updateinventory['status'] = $request->status;
            }
            if(!empty($request->remarks)){
                $updateinventory['remarks'] = $request->remarks;
            }
            if($request->status == 0){
                $updateinventory['cancelled_date'] = date("d-m-Y");
                $updateinventory['assign_emp_id'] = '';
                $updateinventory['assign_emp_name'] = '';
            }
            if($request->status == 1){
                $updateinventory['unassigned_date'] = date("d-m-Y");
                $updateinventory['assign_emp_id'] = '';
                $updateinventory['assign_emp_name'] = '';
            }
            if($request->status == 2){
                $updateinventory['assigned_date'] = date("d-m-Y");
                $updateinventory['assign_emp_id'] = $request->assign_emp_id;
                $updateinventory['assign_emp_name'] = $request->assign_emp_name;
            }
            if($request->status == 3){
                $updateinventory['scraped_date'] = date("d-m-Y");
                $updateinventory['assign_emp_id'] = '';
                $updateinventory['assign_emp_name'] = '';
            }
            $updateinventory['is_approved'] = 0;

            $saveinventory = InventoryInvoice::where('id',$request->asset_id)->update($updateinventory);
            
            //insert in history
            $add_history['inventory_invoice_id'] = $request->asset_id;

            if(!empty($request->assign_emp_id)){
                $add_history['assign_emp_id'] = $request->assign_emp_id;
            }
            if(!empty($request->assign_emp_name)){
                $add_history['assign_emp_name'] = $request->assign_emp_name;
            }
            if(!empty($request->status)){
                $add_history['assign_status'] = $request->status;
            }
            if($request->status == 0){
                $add_history['cancelled_date'] = date("d-m-Y");
            }
            if($request->status == 1){
                $add_history['unassigned_date'] = date("d-m-Y");
            }
            if($request->status == 2){
                $add_history['assigned_date'] = date("d-m-Y");
            }
            if($request->status == 3){
                $add_history['scraped_date'] = date("d-m-Y");
            }
            $add_history['updated_user_id'] = $authuser->id;
            $add_history['status'] = 1;

            InventoryHistory::create($add_history);

            // mail send to authorize user
            
            $get_invoice = InventoryInvoice::where('id',$request->asset_id)->first();
            $asset_code = $get_invoice->un_id;
            $data = ['invoice_id'=>$get_invoice->id,'un_id' => $asset_code,'emp_id'=>$get_invoice->assign_emp_id,'emp_name' => $get_invoice->assign_emp_name,];
            $user['to'] = "amit.thakur@eternitysolutions.net";

            Mail::send('inventories.assign-email-template', $data, function ($messges) use ($user, $asset_code) {
                $messges->to($user['to']);
                $messges->subject('Mail for Asset Approval : Asset Code. '."FRC-CHD-".$asset_code.'');

            });
        
            if($saveinventory){

                $data = '';
                $message = "Inventory updated successfully";
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
    // email approval status
    public function approvedAsset($id)
    {
        $id = decrypt($id);
        try {
            $updateinventory['is_approved'] = 1;
            $saveinventory = InventoryInvoice::where('id',$id)->update($updateinventory);
            
            //insert in history
            $add_history['inventory_invoice_id'] = $id;
            $add_history['is_approved'] = 1;
            $add_history['status'] = 1;

            InventoryHistory::create($add_history);

            if($saveinventory){
                $data = '';
                $message = "Inventory Approved successfully";
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
        return ($message);
    }
    // unassign approval
    public function declinedAsset($id)
    {
        $id = decrypt($id);
        try {
            $updateinventory['is_approved'] = 2;
            $saveinventory = InventoryInvoice::where('id',$id)->update($updateinventory);
            
            //insert in history
            $add_history['inventory_invoice_id'] = $id;
            $add_history['is_approved'] = 1;
            $add_history['status'] = 1;

            InventoryHistory::create($add_history);

            if($saveinventory){
                $data = '';
                $message = "Inventory declined successfully";
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

    // unassign approval
    public function undertakingUpload(Request $request, $id)
    {
        try {
            $authuser = Auth::user();
            if ($request->importFile) {
                $images = $request->importFile;
                // 
                $path = Storage::disk('s3')->put('invoice_undertaking_upload', $images);
                $get_real_names = explode('/', $path);
                $updateinventory['undertaking_image'] = Storage::disk('s3')->url($path);

                // $updateinventory['undertaking_image'] = $get_real_names[1];
            }
            $saveinventory = InventoryInvoice::where('id',$id)->update($updateinventory);
            
            //insert in history
            // $add_history['inventory_invoice_id'] = $request->asset_id;
            // $add_history['updated_user_id'] = $authuser->id;
            // $add_history['is_approved'] = 1;
            // $add_history['status'] = 1;

            // InventoryHistory::create($add_history);

            if($saveinventory){
                $data = '';
                $message = "Inventory undertaking upload successfully";
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

    public function pdfInventory($id){
        $query = InventoryInvoice::where('id',$id)->first();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML('hiii');
        $pdf->setPaper('legal', 'portrait');
        return $pdf->download('itsolutionstuff.pdf');


        // $data = [
        //     // 'invoices' => $query,
        //     'title' => 'Welcome to ItSolutionStuff.com',
        //     'date' => date('d/m/Y')
        // ];
        // $pdf = PDF::loadView('inventories.inventoryPDF', $data);
        // echo "<pre>"; print_r($pdf); die;
    
        // return $pdf->download('inventory.pdf');
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

    public function getBrand()
    {
        $data['brands'] = Brand::all();
        $data['categories'] = Category::all();

        $message = "Brands and category list fetch";
        $status = true;
        $errorCode = 200;
            
        return Helper::apiResponseSend($message,$data,$status,$errorCode);
    }

    public function CheckSerialno($sno)
    {
        try{
            $check_sno = InventoryInvoice::where('sno',$sno)->first();
            if($check_sno){

                $data = $check_sno;
                $message = "Serial-no already exist";
                $status = true;
                $errorCode = 200;
            }else{
                $data = '';
                $message = "Serial-no not exist";
                $status = false;
                $errorCode = 401;
            }
        }catch(Exception $e) {
            $data = '';
            $message = "Invalid Record";
            $status = false;
            $errorCode = 401;
        }
            
        return Helper::apiResponseSend($message,$data,$status,$errorCode);
    }

    public function getEmployee()
    {
        // echo'<pre>'; print_r('hhhh'); die;
        try{
            $url = 'https://test-courier.easemyorder.com/api/get-employee-list';
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
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

            // $response = curl_exec($curl);
            
            // curl_close($curl);
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
    
    //download excel/csv
    public function exportInventory(){
        return Excel::download(new InventoryExport, 'inventories.csv');
    }
    
    public function bulkUpload(Request $request){
        try{
            $data = Excel::import(new InventoryImport,request()->file('importFile'));
            if($data){
                $data = '';
                $message = "Inventory imported successfully";
                $status = true;
                $errorCode = 200;
            }else{
                $data = '';
                $message = "Inventory imported failed!";
                $status = false;
                $errorCode = 401;
            }
        }catch(Exception $e) {
            
            $data = '';
            $message = "Invalid Record";
            $status = false;
            $errorCode = 401;
        }
    
    return Helper::apiResponseSend($message,$data,$status,$errorCode);

    }

    public function inventorySampleDownload()
    {
        $path = public_path('sample/inventory_bulkimport');
        return response()->download($path);     // use download() helper to download the file
    }

}
