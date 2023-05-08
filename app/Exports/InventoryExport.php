<?php

namespace App\Exports;

use App\Models\Inventory;
use App\Models\InventoryInvoice;
use App\Models\InventoryHistory;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\FromQuery;
use DB;
use Session;
use Helper;
use Auth;

class InventoryExport implements FromCollection, WithHeadings,ShouldQueue
{
    /**
    * @return \Illuminate\Support\Collection
    */   
    public function collection()
    {
        ini_set('memory_limit', '2048M');
        set_time_limit ( 6000 );
        $arr = array();
        // $authuser = Auth::user();
        // $role_id = Role::where('id','=',$authuser->role_id)->first();
        // $regclient = explode(',',$authuser->regionalclient_id);
        // $cc = explode(',',$authuser->branch_id);
        
        $query = InventoryInvoice::query();
        $query = InventoryInvoice::with('Category','Brand','Inventories')->whereIn('status',[1,2,3]);

        $inventories = $query->orderby('created_at','DESC')->get();
        // echo "<pre>"; print_r($inventories); die;
        if($inventories->count() > 0){
            foreach ($inventories as $key => $value){  
                // echo "<pre>"; print_r($value->Inventories->unit_id); die;
                if(!empty($value->un_id)){
                    $un_id = 'FRC-CHD-'.$value->un_id;
                }else{
                    $un_id = '';
                }

                if(!empty($value->Consigner->nick_name)){
                    $consigner = $value->Consigner->nick_name;
                }else{
                    $consigner = '';
                }

                if(!empty($value->dealer_type == '1')){
                    $dealer_type = 'Registered';
                }else{
                    $dealer_type = 'Unregistered';
                }
                $arr[] = [
                    'un_id'         =>  $un_id,
                    'category_id'   => @$value->Category->name,
                    'brand_id'      => @$value->Brand->name,
                    'model'         => @$value->model,
                    'sno'           => @$value->sno,
                    'unit_price'    => @$value->unit_price,
                    'unit_id'       => @$value->Inventories->unit_id,
                    'vendor_id'     => @$value->Inventories->vendor_id,
                    'invoice_no'    => @$value->Inventories->invoice_no,
                    'invoice_date'  => Helper::ShowDayMonthYear($value->Inventories->invoice_date),
                    'invoice_price' => @$value->Inventories->invoice_price,
                    'assign_emp_name' => @$value->assign_emp_name,
                    'assigned_date' => @$value->assigned_date,
                    // 'unassigned_date' => @$value->unassigned_date,
                    // 'scraped_date' => @$value->scraped_date,
                    // 'cancelled_date' => @$value->cancelled_date,
                    'status'        => Helper::AssetInvcStatus($value->status),
                    
                ];
            }
        }
        return collect($arr);
    }
    public function headings(): array
    {
        return [
            'UN Id',
            'Category',
            'Brand',
            'Model',
            'Sr No.',
            'Unit Price',
            'Bill To Unit',
            'Vendor',
            'Invoice No.',
            'Invoice Date',
            'Invoice Price',
            'Assigned Employee',
            'Assigned Date',
            'Status',
        ];
    }
}