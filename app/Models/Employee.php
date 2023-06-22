<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;
    protected $fillable = [
        'emp_code', 'name','office_email', 'personal_email', 'office_phone', 'office_phone_ext1', 'mobile_no', 'permanent_add1','permanent_add2', 'permanent_add3','permanent_add4', 'city','pincode', 'state','country', 'dateof_birth','dateof_joining', 'salutation','gender', 'wed_anniversary','pan_number','location','group_code','employee_status','grade','designation', 'status','created_at','updated_at'
    ];
    
}
