<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryHistory extends Model
{
    use HasFactory;
    protected $fillable = [
        'inventory_invoice_id','assign_emp_id', 'assign_emp_name', 'cancelled_date', 'assigned_date', 'unassigned_date', 'scraped_date', 'asset_parent_id', 'asset_children_id', 'assigned_status', 'remarks', 'created_user_id', 'updated_user_id', 'is_approved', 'status', 'created_at', 'updated_at'
    ];
}