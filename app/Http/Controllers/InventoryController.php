<?php

namespace App\Http\Controllers;

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
    public function __construct()
    {
        $this->title = "Inventories";
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
        $query = InventoryInvoice::query();

        if ($request->ajax()) {
            if (isset($request->resetfilter)) {
                Session::forget('peritem');
                $url = URL::to($this->prefix . '/' . $this->segment);
                return response()->json(['success' => true, 'redirect_url' => $url]);
            }

            if (!empty($request->search)) {
                $search = $request->search;
                $searchT = str_replace("'", "", $search);
                $query->where(function ($query) use ($search, $searchT) {
                    $query->where('sno', 'like', '%' . $search . '%')
                    ->orWhere('model', 'like', '%' . $search . '%')
                    ->orWhere('unit_price', 'like', '%' . $search . '%')
                    ->orWhereHas('Category', function($categoryquery) use ($search){
                        $categoryquery->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('Brand', function($brandquery) use ($search){
                        $brandquery->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('Inventories', function($inventoryquery) use ($search){
                        $inventoryquery->where('invoice_no', 'like', '%' . $search . '%')
                        ->orWhere('invoice_price', 'like', '%' . $search . '%')
                        ->orWhereHas('Unit', function($unitquery) use ($search){
                            $unitquery->where('name', 'like', '%' . $search . '%');
                        });
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
            $users = User::select('id','name','role_id')->where('status','1')->get();

            $inventories = $query->orderBy('id', 'DESC')->paginate($peritem);
            $inventories = $inventories->appends($request->query());

            $html = view('inventories.inventory-list-ajax', ['prefix' => $this->prefix, 'peritem' => $peritem, 'inventories' => $inventories, 'users'=>$users,])->render();

            return response()->json(['html' => $html]);
        }
        $users = User::select('id','name','role_id')->where('status','1')->get();
         
        $inventories = $query->orderBy('id', 'DESC')->paginate($peritem);
        $inventories = $inventories->appends($request->query());

        return view('inventories.inventory-list', ['prefix' => $this->prefix, 'segment' => $this->segment, 'peritem' => $peritem, 'inventories' => $inventories,'users'=>$users,]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->prefix = request()->route()->getPrefix();
        $categories = Helper::getCategories();
        $units = Helper::getUnits();
        $brands = Helper::getBrands();

        return view('inventories.create-inventory', ['prefix' => $this->prefix, 'segment' => $this->segment, 'brands' => $brands, 'categories' => $categories,'units'=>$units]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
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
            if(!empty($request->invcitem_no)){
                $addinventory['invcitem_no'] = $request->invcitem_no;
            }  
            
            $addinventory['created_user_id'] = $authuser->id;
            $addinventory['status'] = 1;
            
            // upload rc image
            if($request->invoice_image){
                $file = $request->file('invoice_image');
                $path = 'public/images/inventory_invoice_images';
                $name = Helper::uploadImage($file,$path);
                $addinventory['invoice_image']  = $name;
            }

            $saveinventory = Inventory::create($addinventory);
            if($saveinventory){
                $addinventory['inventory_id'] = $saveinventory->id;
                $save_inv_history = InventoryHistory::create($addinventory);

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

                        if($save_data['invc_image']){
                            $file = $save_data['invc_image'];
                            $path = 'public/images/inventory_invc_images';
                            $name = Helper::uploadImage($file,$path);
                            $save_invc['invc_image']  = $name;
                        }

                        $un_id = InventoryInvoice::select('un_id')->latest('un_id')->first();
                        if (empty($un_id) || $un_id == null) {
                            $un_id = 100001;
                        } else {
                            $un_id = $un_id['un_id'] + 1;
                        }
                        $save_invc['un_id'] = $un_id;
                        $saveinventoryinvoices = InventoryInvoice::insert($save_invc);
                    }
                }
                
                $response['success']    = true;
                $response['page']       = 'inventory-create';
                $response['error']      = false;
                $response['success_message'] = "Inventory created successfully";
                $response['redirect_url'] = URL::to($this->prefix.'/inventories');
            }else{
                $response['success']       = false;
                $response['error']         = true;
                $response['error_message'] = "Can not created inventory please try again";
            }
        
            DB::commit();
        } catch (Exception $e) {
            $response['error'] = false;
            $response['error_message'] = $e;
            $response['success'] = false;
            // $response['redirect_url'] = $url;
        }
        return response()->json($response);
    }

    public function inventoryExport()
    {
        return Excel::download(new InventoryExport, 'inventories.csv');
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