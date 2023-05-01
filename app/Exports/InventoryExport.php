<?php

namespace App\Exports;

use App\Models\InventoryInvoice;
use App\Models\Role;
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

        $inventories = $query->orderby('created_at','DESC')->get();
        // echo "<pre>"; print_r($inventories); die;
        if($inventories->count() > 0){
            foreach ($inventories as $key => $value){  

                if(!empty($value->un_id)){
                    $un_id = 'FRC-'.$value->un_id;
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
                    'sno'           => @$value->sno,
                    'category_id'   => @$value->Category->name,
                    'brand_id'      => @$value->Brand->name,
                    'model'         => @$value->model,
                    'unit_price'    => @$value->unit_price,
                    'unit_id'       => @$value->Inventories->Unit->name,
                    'vendor_id'     => @$value->Inventories->vendor_id,
                    'invoice_no'    => @$value->Inventories->invoice_no,
                    'invoice_date'  => Helper::ShowDayMonthYear($value->Inventories->invoice_date),
                    'invoice_price' => @$value->Inventories->invoice_price,
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
            'Sr No.',
            'Category',
            'Brand',
            'Model',
            'Unit Price',
            'Bill To Unit',
            'Vendor',
            'Invoice No.',
            'Invoice Date',
            'Invoice Price',
            'Status',
        ];
    }
}