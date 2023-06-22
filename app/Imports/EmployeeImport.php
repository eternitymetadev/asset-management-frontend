<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use App\Models\Employee;
use Auth;

class EmployeeImport implements ToModel,WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function model(array $rows)
    {
        // $birth_date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($rows['dateofbirth']);
        // $date_join = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($rows['dateofjoining']);
        // $wedann = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($rows['wedanniversary']);
        // $date_birth = $birth_date->format('Y-m-d');
        // $date_joining = $date_join->format('Y-m-d');
        // $wed_anniversary = $wedann->format('Y-m-d');

        if (!empty($row['dateofbirth'])) {
            if (is_numeric($row['dateofbirth'])) {
                $date_birth = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['dateofbirth']);
                $date_birth = $date_birth->format('d-m-Y');
            } else {
                $date_birth = $row['dateofbirth'];
            }
        } else {
            $date_birth = "";
        }
        if (!empty($row['dateofjoining'])) {
            if (is_numeric($row['dateofjoining'])) {
                $date_join = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['dateofjoining']);
                $date_join = $date_join->format('d-m-Y');
            } else {
                $date_join = $row['dateofjoining'];
            }
        } else {
            $date_join = "";
        }

        if (!empty($row['dateofbirth'])) {
            if (is_numeric($row['wedanniversary'])) {
                $wed_anniversary = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['wedanniversary']);
                $wed_anniversary = $wed_anniversary->format('d-m-Y');
            } else {
                $wed_anniversary = $row['wedanniversary'];
            }
        } else {
            $wed_anniversary = "";
        }
        
        $employee = Employee::where('emp_code',$rows['employeecode'])->where('name',$rows['name'])->first();
        if(empty($employee)){
            return new Employee([
                'emp_code'          => $rows['employeecode'],
                'name'              => $rows['name'],
                'office_email'      => $rows['officeemail'],
                'personal_email'    => $rows['personalemail'],
                'office_phone'      => $rows['officephone'],
                'office_phone_ext1' => $rows['officephoneext1'],
                'mobile_no'         => $rows['mobile_no'],
                'permanent_add1'    => $rows['permanentadd1'],
                'permanent_add2'    => $rows['permanentadd2'],
                'permanent_add3'    => $rows['permanentadd3'],
                'permanent_add4'    => $rows['permanentadd4'],
                'city'              => $rows['city'],
                'pincode'           => $rows['pincode'],
                'state'             => $rows['state'],
                'country'           => $rows['country'],
                'dateof_birth'      => $date_birth,
                'dateof_joining'    => $date_join,
                'salutation'        => $rows['salutation'],
                'gender'            => $rows['gender'],
                'wed_anniversary'   => $wed_anniversary,
                'pan_number'        => $rows['pannumber'],
                'location'          => $rows['location'],
                'group_code'        => $rows['groupcode'],
                'employee_status'   => $rows['employeestatus'],
                'grade'             => $rows['grade'],
                'designation'       => $rows['designation'],
            ]);
        }
            $employee =  Employee::where('id',$employee->id)->update([
            'office_email'      => $rows['officeemail'],
            'personal_email'    => $rows['personalemail'],
            'office_phone'      => $rows['officephone'],
            'office_phone_ext1' => $rows['officephoneext1'],
            'mobile_no'         => $rows['mobile_no'],
            'permanent_add1'    => $rows['permanentadd1'],
            'permanent_add2'    => $rows['permanentadd2'],
            'permanent_add3'    => $rows['permanentadd3'],
            'permanent_add4'    => $rows['permanentadd4'],
            'city'              => $rows['city'],
            'pincode'           => $rows['pincode'],
            'state'             => $rows['state'],
            'country'           => $rows['country'],
            'dateof_birth'      => $date_birth,
            'dateof_joining'    => $date_join,
            'salutation'        => $rows['salutation'],
            'gender'            => $rows['gender'],
            'wed_anniversary'   => $wed_anniversary,
            'pan_number'        => $rows['pannumber'],
            'location'          => $rows['location'],
            'group_code'        => $rows['groupcode'],
            'employee_status'   => $rows['employeestatus'],
            'grade'             => $rows['grade'],
            'designation'       => $rows['designation'],
            ]);
        
    }
}
