<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'name', 'nick_name', 'email', 'phone', 'address', 'status', 'created_at', 'updated_at'
    ];
}