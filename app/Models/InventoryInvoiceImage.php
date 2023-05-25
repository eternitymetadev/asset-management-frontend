<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryInvoiceImage extends Model
{
    use HasFactory;
    protected $fillable = [
        'invoice_id', 'invc_image', 'status', 'created_at', 'updated_at'
    ];
}
