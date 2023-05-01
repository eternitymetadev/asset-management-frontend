<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;
    protected $fillable = [
        'unit_id', 'vendor_id', 'un_id', 'invoice_no', 'invoice_date', 'invoice_price', 'invoice_count', 'invoice_image', 'description', 'created_user_id', 'updated_user_id', 'status', 'created_at', 'updated_at'
    ];

    public function InventoryInvoices()
    {
        return $this->hasMany('App\Models\InventoryInvoice','inventory_id','id');
    }
    
    public function Unit(){
        return $this->hasOne('App\Models\Unit','id','unit_id');
    }

}