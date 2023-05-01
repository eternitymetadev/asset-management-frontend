<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryInvoice extends Model
{
    use HasFactory;
    protected $fillable = [
        'inventory_id', 'sno', 'un_id', 'category_id', 'brand_id', 'model', 'unit_price', 'invc_image', 'status', 'created_at', 'updated_at'
    ];

    public function Inventories(){
    	return $this->belongsTo('App\Models\Inventory','inventory_id','id');	
    }

    public function Category(){
        return $this->hasOne('App\Models\Category','id','category_id');
    }
    public function Brand(){
        return $this->hasOne('App\Models\Brand','id','brand_id');
    }
}