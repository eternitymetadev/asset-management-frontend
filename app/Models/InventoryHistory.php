<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryHistory extends Model
{
    use HasFactory;
    protected $fillable = [
        'inventory_id','unit_id', 'vendor_id', 'un_id', 'invoice_no', 'invoice_date', 'invoice_price', 'invcitem_no', 'invoice_image', 'description', 'created_user_id', 'updated_user_id', 'status', 'created_at', 'updated_at'
    ];
}
