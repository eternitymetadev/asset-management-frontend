<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use App\Models\Inventory;
use App\Models\InventoryInvoice;
use App\Models\InventoryHistory;
use App\Models\Category;
use App\Models\Brand;
use App\Models\User;
use Auth;

class InventoryImport implements ToModel,WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function model(array $rows)
    {
        // echo "<pre>"; print_r($rows['unit']); die;
        // foreach($rows as $row){
            $authuser = Auth::user();
            // echo "<pre>"; print_r($rows['serial_no']); die;
            $check_sno = InventoryInvoice::where('sno',$rows['serial_no'])->first();
            if(empty($check_sno)){
                $add_inventory['unit_id'] = $rows['unit'];
                $add_inventory['vendor_id'] = $rows['vendor_id'];
                $add_inventory['vendor_name'] = $rows['vendor_name'];
                $add_inventory['invoice_no'] = $rows['invoice_no'];
                $add_inventory['invoice_date'] = $rows['invoice_date'];
                $add_inventory['invoice_price'] = $rows['invoice_price'];
                $add_inventory['created_user_id'] = $authuser->id;
                $add_inventory['status'] = 1;
                $save_inventory = Inventory::create($add_inventory);

                $add_inventory_item['inventory_id'] = $save_inventory->id;
                $add_inventory_item['sno'] = $rows['serial_no'];
                $add_inventory_item['category_id'] = $rows['category_id'];
                $add_inventory_item['brand_id'] = $rows['brand_id'];
                $add_inventory_item['model'] = $rows['model'];
                $add_inventory_item['unit_price'] = $rows['unit_price'];
                $add_inventory_item['asset_children_id'] = json_encode([]);
                $add_inventory_item['status'] = 1;


                $un_id = InventoryInvoice::select('id','un_id')->latest('un_id')->first();
                if (($un_id == '') || $un_id == null) {
                    $add_inventory_item['un_id'] = 100001;
                } else {
                    $add_inventory_item['un_id'] = $un_id['un_id'] + 1;
                }
                $save_inventory_item = InventoryInvoice::create($add_inventory_item);

                $add_history['inventory_invoice_id'] = $save_inventory_item->id;
                $add_history['created_user_id'] = $authuser->id;
                $add_history['status'] = 1;
                InventoryHistory::create($add_history);
            }

        // }
        
        
        
    }
}
