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
            // $this->prefix = request()->route()->getPrefix();
            $authuser = Auth::user();
            // $peritem = Config::get('variable.PER_PAGE');
            $peritem = 2;
            $query = InventoryInvoice::query();
            if ($request) {
                // if (isset($request->resetfilter)) {
                //     Session::forget('peritem');
                //     return response()->json(['success' => true]); 
                // }
                if (isset($request->assetStatus)) {
                     $query->where('status',$request->assetStatus);
                }
                
                if (!empty($request->searchKeyword)) {
                    $search = $request->searchKeyword;
                    $searchT = str_replace("'", "", $search);
                    $query->where(function ($query) use ($search, $searchT) {
                        $query->where('un_id', 'like', '%' . $search . '%')
                        ->orWhere('model', 'like', '%' . $search . '%')
                        ->orWhere('unit_price', 'like', '%' . $search . '%')
                        ->orWhere('sno', 'like', '%' . $search . '%')
                        ->orWhereHas('Brand', function ($brandquery) use ($search) {
                            $brandquery->where('name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('Category', function ($categoryquery) use ($search) {
                            $categoryquery->where('name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('Inventories', function ($inventoryquery) use ($search) {
                            $inventoryquery->where('unit_id', 'like', '%' . $search . '%')
                            ->orWhere('vendor_name', 'like', '%' . $search . '%')
                            ->orWhere('invoice_no', 'like', '%' . $search . '%')
                            ->orWhere('assign_emp_name', 'like', '%' . $search . '%');
                        });
                            
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

                $inventories = $query->with('Category','Brand','Inventories')->whereNotIn('status',[0])->paginate($peritem);
                $inventories = $inventories->appends($request->query());
                if($inventories){
                    // foreach($inventories as $key => $d){
                    //     $datalist[$key] = $d;
                    //     // $datalist[$key]->asset_children = json_decode($d->asset_children_id);
                    //     $datalist[$key] = Helper::AssetInvcStatus($d->status);
                    // }
                    $data= $inventories;
                    $message = "Inventories fetched Successfully";
                    $status = true;
                    $errorCode = 200;
                }else{
                    $data= $inventories;
                    $message = "Data not found!";
                    $status = true;
                    $errorCode = 200;
                }
                return Helper::apiResponseSend($message,$data,$status,$errorCode);
            }
            $inventories = $query->with('Category','Brand','Inventories')->whereNotIn('status',[0])->paginate($peritem);

            // $datalist = array();
            if(!empty($inventories)){
                // foreach($inventories as $key => $d){
                //     $datalist[$key] = $d;
                //     // $datalist[$key]->asset_children = json_decode($d->asset_children_id);
                //     $datalist[$key] = Helper::AssetInvcStatus($d->status);
                // }
                if($inventories){
                    $data= $inventories;
                    $message = "Inventories fetched Successfully";
                    $status = true;
                    $errorCode = 200;
                }
            }
        }catch(Exception $e) {
            $data = $query;
            $message = "Invalid Record";
            $status = false;
            $errorCode = 402;
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
            if(!empty($request->description)){
                $addinventory['description'] = $request->description;
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
                    // $data[$i]['unassigned_date'] = date("d-m-Y H:i:s");
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
                    // $add_history[$i]['unassigned_date'] = date("d-m-Y H:i:s");

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

    public function updateAssignStatus(Request $request)
    {
        // return $request->all();
        try {
            $this->prefix = request()->route()->getPrefix();
            $rules = array(
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
                $updateinventory['cancelled_date'] = date("d-m-Y H:i:s");
                $updateinventory['assign_emp_id'] = '';
                $updateinventory['assign_emp_name'] = '';
            }
            if($request->status == 1){
                $updateinventory['unassigned_date'] = date("d-m-Y H:i:s");
                $updateinventory['assign_emp_id'] = '';
                $updateinventory['assign_emp_name'] = '';
                $updateinventory['assigned_date'] = '';
            }
            if($request->status == 4){
                $updateinventory['assigned_date'] = date("d-m-Y H:i:s");
                $updateinventory['assign_emp_id'] = $request->assign_emp_id;
                $updateinventory['assign_emp_name'] = $request->assign_emp_name;
            }
            if($request->status == 3){
                $updateinventory['scraped_date'] = date("d-m-Y H:i:s");
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
                $add_history['cancelled_date'] = date("d-m-Y H:i:s");
            }
            if($request->status == 1){
                $add_history['unassigned_date'] = date("d-m-Y H:i:s");
            }
            if($request->status == 2){
                $add_history['assigned_date'] = date("d-m-Y H:i:s");
            }
            if($request->status == 3){
                $add_history['scraped_date'] = date("d-m-Y H:i:s");
            }
            $add_history['updated_user_id'] = $authuser->id;
            $add_history['status'] = 1;

            InventoryHistory::create($add_history);

            // mail send to authorize user
            if($request->status == 4){
                $get_invoice = InventoryInvoice::with('Category')->where('id',$request->asset_id)->first();
                $asset_code = $get_invoice->un_id;
                $data = ['invoice_id'=>$get_invoice->id,'un_id' => $asset_code,'emp_id'=>$get_invoice->assign_emp_id,'emp_name' => $get_invoice->assign_emp_name,'asset_category' => $get_invoice->Category->name,'asset_model' => $get_invoice->model,'asset_sno' => $get_invoice->sno];
                // $user['to'] = "support1hr@frontierag.com";
                $user['to'] = "itsupport@frontierag.com";
                // $user['cc'] = ["itsupport@frontierag.com", "hrd@frontierag.com"];
                $user['cc'] = ["itsupport@frontierag.com"];
                
                Mail::send('inventories.assign-email-template', $data, function ($messges) use ($user, $asset_code) {
                    $messges->to($user['to']);
                    $messges->cc($user['cc']);
                    $messges->subject('New Asset handover to HR : Asset Code. '."FRC-CHD-".$asset_code.'');

                });
            }
        
            if($saveinventory){
                $data = '';
                $message = "Inventory updated successfully";
                $status = true;
                $errorCode = 200;
            }else{
                $data = $addinventory;
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
    // email approval status
    public function approvedAsset($id)
    {
        $id = decrypt($id);
        try {
            $get_invoice = InventoryInvoice::where('id',$id)->first();
            if($get_invoice->status == 5){
                $updateinventory['is_approved'] = 1;
                $updateinventory['status'] = 2;
                $saveinventory = InventoryInvoice::where('id',$id)->update($updateinventory);
                
                //insert in history
                $authuser = Auth::user();
                
                $add_history['inventory_invoice_id'] = $id;
                $add_history['is_approved'] = 1;
                // $add_history['updated_user_id'] = $authuser->id;
                $add_history['status'] = 2;

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
                    $errorCode = 402;
                }
            }else{
                $data = '';
                $message = "Invalid URL";
                $status = false;
                $errorCode = 402;
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
            $errorCode = 402;
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
                $errorCode = 402;
            }
        }catch(Exception $e) {
            $data = '';
            $message = "Invalid Record";
            $status = false;
            $errorCode = 402;
        }
            
        return Helper::apiResponseSend($message,$data,$status,$errorCode);
    }

    // public function getEmployee()
    // {
    //     try{
    //         $url = 'https://test-courier.easemyorder.com/api/get-employee-list';
    //         $curl = curl_init();

    //         curl_setopt_array($curl, array(
    //             CURLOPT_URL => $url,
    //             CURLOPT_RETURNTRANSFER => true,
    //             CURLOPT_ENCODING => '',
    //             CURLOPT_MAXREDIRS => 10,
    //             CURLOPT_TIMEOUT => 0,
    //             CURLOPT_FOLLOWLOCATION => true,
    //             CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //             CURLOPT_CUSTOMREQUEST => 'GET',
    //         ));

    //         $response = curl_exec($curl);

    //         curl_close($curl);

    //         // $response = curl_exec($curl);
            
    //         // curl_close($curl);
    //         $res = json_decode($response);
    //         $data['vendor_data'] = $res->data;

    //         if($data)
    //         {
    //             $data = $data;
    //             $message = "Vendors fetched Successfully";
    //             $status = true;
    //             $errorCode = 200;
    //         }
    //     }catch(Exception $e) {
    //         $data = $query;
    //         $message = "Invalid Record";
    //         $status = false;
    //         $errorCode = 402;
    //     }        
    //     return Helper::apiResponseSend($message,$data,$status,$errorCode);
    // }
    
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
                $errorCode = 402;
            }
        }catch(Exception $e) {
            $data = '';
            $message = "Invalid Record";
            $status = false;
            $errorCode = 402;
        }
        return Helper::apiResponseSend($message,$data,$status,$errorCode);
    }

    public function inventorySampleDownload()
    {
        $path = public_path('sample/inventory_bulkimport.xlsx');
        return response()->download($path);     // use download() helper to download the file
    }

    public function handoverEmployee(Request $request){
        try{
            if($request->emp_email){
                // mail send to employee
                $get_invoice = InventoryInvoice::with('Category')->where('id',$request->asset_id)->first();
                $asset_code = $get_invoice->un_id;

                $data = ['invoice_id'=>$get_invoice->id,'un_id' => $asset_code,'emp_id'=>$get_invoice->assign_emp_id,'emp_name' => $get_invoice->assign_emp_name,'asset_category' => $get_invoice->Category->name,];
                // $user['to'] = "itsupport4@frontierag.com"; //request->emp_email
                $user['to'] = "itsupport@frontierag.com"; 
                // $user['cc'] = ['itsupport@frontierag.com','hrd@frontierag.com'];
                $user['cc'] = ['itsupport@frontierag.com'];

                Mail::send('inventories.handover-emp-email-template', $data, function ($messges) use ($user, $asset_code) {
                    $messges->to($user['to']);
                    $messges->to($user['cc']);
                    $messges->subject('New asset assigned : Asset Code. '."FRC-CHD-".$asset_code.'');
                });
                $updateinventory['status'] =5;

                $data = InventoryInvoice::where('id',$request->asset_id)->update($updateinventory);
                $authuser = Auth::user();
                $add_history['inventory_invoice_id'] = $request->asset_id;
                $add_history['updated_user_id'] = $authuser->id;
                $add_history['status'] = 5;

                InventoryHistory::create($add_history);

                
                if($data){
                    $data = '';
                    $message = "Inventory updated successfully";
                    $status = true;
                    $errorCode = 200;
                }else{
                    $data = '';
                    $message = "Inventory updated failed!";
                    $status = false;
                    $errorCode = 402;
                }
            }else{
                $data = '';
                $message = "Inventory updated failed!";
                $status = false;
                $errorCode = 402;
            }
        }catch(Exception $e) {
            $data = '';
            $message = "Invalid Record";
            $status = false;
            $errorCode = 402;
        }
        return Helper::apiResponseSend($message,$data,$status,$errorCode);
    }

    public function pullbackToEmployee(Request $request){
        try{
            if($request->emp_email){
                // mail send to employee
                $get_invoice = InventoryInvoice::with('Category')->where('id',$request->asset_id)->first();
                $asset_code = $get_invoice->un_id;

                $data = ['invoice_id'=>$get_invoice->id,'un_id' => $asset_code,'emp_id'=>$get_invoice->assign_emp_id,'emp_name' => $get_invoice->assign_emp_name, 'asset_category' => $get_invoice->Category->name, 'asset_sno' =>$get_invoice->sno,'status'=>6];
                // $user['to'] = "itsupport4@frontierag.com"; //request->emp_email
                $user['to'] = "itsupport@frontierag.com"; //request->emp_email
                // $user['cc'] = ["itsupport@frontierag.com", "hrd@frontierag.com"];
                $user['cc'] = ["itsupport@frontierag.com"];

                if($request->recoveryType =='fullAndFinal'){
                    $template = 'inventories.pullback-fullfinalasset-emp-email-template';
                }else{
                    $template = 'inventories.pullback-replacement-emp-email-template';
                }

                Mail::send($template, $data, function ($messges) use ($user, $asset_code) {
                    $messges->to($user['to']);
                    $messges->subject('Pull back asset : Asset Code. '."FRC-CHD-".$asset_code.'');
                });
                $updateinventory['status'] =6;

                $data = InventoryInvoice::where('id',$request->asset_id)->update($updateinventory);
                if($data){
                    $authuser = Auth::user();
                    $add_history['inventory_invoice_id'] = $request->asset_id;
                    $add_history['updated_user_id'] = $authuser->id;
                    $add_history['status'] = 6;
                    
                    $data = '';
                    $message = "Inventory updated successfully";
                    $status = true;
                    $errorCode = 200;
                }else{
                    $data = '';
                    $message = "Inventory updated failed!";
                    $status = false;
                    $errorCode = 402;
                }
            }else{
                $data = '';
                $message = "Inventory updated failed!";
                $status = false;
                $errorCode = 402;
            }
        }catch(Exception $e) {
            $data = '';
            $message = "Invalid Record";
            $status = false;
            $errorCode = 402;
        }
        return Helper::apiResponseSend($message,$data,$status,$errorCode);
    }

    // pullback email accept request to employee
    public function acceptPullback(Request $request)
    {
        try {
            // mail send to employee
            $get_invoice = InventoryInvoice::with('Category')->where('id',$request->asset_id)->first();
            $asset_code = $get_invoice->un_id;

            $data = ['invoice_id'=>$get_invoice->id,'un_id' => $asset_code,'emp_id'=>$get_invoice->assign_emp_id,'emp_name' => $get_invoice->assign_emp_name,'asset_category' => $get_invoice->Category->name,'asset_sno' => $get_invoice->sno, 'status'=>6];
            // $user['to'] = "itsupport4@frontierag.com"; //request->emp_email
            $user['to'] = "itsupport@frontierag.com"; //request->emp_email
            // $user['cc'] = ["itsupport@frontierag.com", "hrd@frontierag.com"];
            $user['cc'] = ["itsupport@frontierag.com"];
    
            Mail::send('inventories.unasigned-req-email-template', $data, function ($messges) use ($user, $asset_code) {
                $messges->to($user['to']);
                $messges->subject('Request to Un-assign : Asset Code. '."FRC-CHD-".$asset_code.'');
            }); 

            $updateinventory['status'] = 7;
            $saveinventory = InventoryInvoice::where('id',$request->asset_id)->update($updateinventory);
            
            //insert in history
            $authuser = Auth::user();
            
            $add_history['inventory_invoice_id'] = $request->asset_id; 
            $add_history['updated_user_id'] = $authuser->id;
            $add_history['status'] = 7;

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

    public function acceptAsset($id){
        return view('inventories.accept-asset-confirmation',['invoice_id'=>$id]);
    }
    // scrap email request to account team
    public function scrapEmailRequest(Request $request){
        try{
            if($request->account_email){
                // mail send to employee
                $get_invoice = InventoryInvoice::where('id',$request->asset_id)->first();
                $asset_code = $get_invoice->un_id;

                $data = ['invoice_id'=>$get_invoice->id,'un_id' => $asset_code,'emp_id'=>$get_invoice->assign_emp_id,'emp_name' => $get_invoice->assign_emp_name,];
                $user['to'] = "amit.thakur@eternitysolutions.net"; //Email send to account

                Mail::send('inventories.scrap-email-template', $data, function ($messges) use ($user, $asset_code) {
                    $messges->to($user['to']);
                    $messges->subject('Scrap request for Asset '."FRC-CHD-".$asset_code.'');
                });
                $updateinventory['remarks'] = $request->remarks;
                $updateinventory['status'] = 8;

                $data = InventoryInvoice::where('id',$request->asset_id)->update($updateinventory);
                $authuser = Auth::user();
                $add_history['inventory_invoice_id'] = $request->asset_id;
                $add_history['updated_user_id'] = $authuser->id;
                $add_history['remarks'] = $request->remarks;
                $add_history['status'] = 8;

                InventoryHistory::create($add_history);
                
                if($data){
                    $data = '';
                    $message = "Inventory updated successfully";
                    $status = true;
                    $errorCode = 200;
                }else{
                    $data = '';
                    $message = "Inventory updated failed!";
                    $status = false;
                    $errorCode = 402;
                }
            }else{
                $data = '';
                $message = "Inventory updated failed!";
                $status = false;
                $errorCode = 402;
            }
        }catch(Exception $e) {
            $data = '';
            $message = "Invalid Record";
            $status = false;
            $errorCode = 402;
        }
        return Helper::apiResponseSend($message,$data,$status,$errorCode);
    }

    //click on  scrap accept email request by accounts
    public function acceptScrap($id)
    {
        $id = decrypt($id);
        try {
            $get_invoice = InventoryInvoice::where('id',$id)->first();
            if($get_invoice->status == 8){

                $updateinventory['status'] = 9;
                $saveinventory = InventoryInvoice::where('id',$id)->update($updateinventory);
                
                //insert in history
                $authuser = Auth::user();
                
                $add_history['inventory_invoice_id'] = $id; 
                // $add_history['updated_user_id'] = $authuser->id;
                $add_history['status'] = 9;

                InventoryHistory::create($add_history);

                if($saveinventory){
                    $data = '';
                    $message = "Inventory Scrapped successfully";
                    $status = true;
                    $errorCode = 200;


                }else{
                    $data = $addinventory;
                    $message = "Invalid Record";
                    $status = false;
                    $errorCode = 402;
                }
            }else{
                $data = '';
                $message = "Invalid URL";
                $status = false;
                $errorCode = 402;
            }
        }catch(Exception $e) {
            $data = '';
            $message = $e->message;
            $status = false;
            $errorCode = 500;
        }
        return ($message);
    }



}
