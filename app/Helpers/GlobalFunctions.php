<?php
namespace App\Helpers;

use App\Models\Branch;
use App\Models\Location;
use App\Models\State;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Brand;
use DB;
use Image;
use Storage;

class GlobalFunctions
{

    public static function AssetStatus($status)
    {
        if ($status == 1) {
            $status = 'Active';
        }
        else if ($status == 2) {
            $status = 'Scraped';
        }
        return $status;
    }
    public static function AssetInvcStatus($status)
    {
        if ($status == 0) {
            $status = 'Cancel';
        } else if ($status == 1) {
            $status = 'Unassigned';
        }else if ($status == 2) {
            $status = 'Assigned';
        } else if ($status == 3) {
            $status = 'Scrapped';
        }
        return $status;
    }

    public static function MaintenanceStatus($status)
    {
        if ($status == 1) {
            $status = 'UpToDate';
        } else if ($status == 2) {
            $status = 'Due';
        }
        return $status;
    }

    public static function apiResponseSend($message,$data,$status = true,$errorCode){
        $errorCode = $status ? 200 : $errorCode;
        $result = [
            "status" => $status,
            "message" => $message,
            "data" => $data,
            'statuscode' => $errorCode
        ];
        return response()->json($result);
    }

    // get locations //

    public static function getLocations()
    {
        $locations = Location::where('status', 1)->orderby('name', 'ASC')->pluck('name', 'id');
        return $locations;
    }

    public static function getCategories()
    {
        $categories = Category::where('status', 1)->orderby('name', 'ASC')->pluck('name', 'id');
        return $categories;
    }

    public static function getUnits()
    {
        $units = Unit::where('status', 1)->orderby('name', 'ASC')->pluck('name', 'id');
        return $units;
    }
    public static function getBrands()
    {
        $brands = Brand::where('status', 1)->orderby('name', 'ASC')->pluck('name', 'id');
        return $brands;
    }

    public static function uploadImage($file, $path)
    {
        $name = time() . '.' . $file->getClientOriginalName();
        //save original
        $img = Image::make($file->getRealPath());
        $img->stream();
        Storage::disk('local')->put($path . '/' . $name, $img, 'public');
        //savethumb
        $img = Image::make($file->getRealPath());
        $img->resize(50, 50, function ($constraint) {
            $constraint->aspectRatio();
        });
        $img->stream();
        Storage::disk('local')->put($path . '/thumb/' . $name, $img, 'public');
        return $name;
    }

    // function for show date in frontend //
    public static function ShowFormatDate($date)
    {
        if (!empty($date)) {
            $changeformat = date('d-M-Y', strtotime($date));
        } else {
            $changeformat = '-';
        }
        return $changeformat;
    }
    //////format 10-07-2000
    public static function ShowDayMonthYear($date)
    {
        if (!empty($date)) {
            $changeformat = date('d-m-Y', strtotime($date));
        } else {
            $changeformat = '-';
        }
        return $changeformat;
    }
    //////format 10/07/2000
    public static function ShowDayMonthYearslash($date)
    {

        if (!empty($date)) {
            $changeformat = date('d/m/Y', strtotime($date));
        } else {
            $changeformat = '-';
        }
        return $changeformat;
    }
    //////format 2022/07/01
    public static function yearmonthdate($date)
    {

        if (!empty($date)) {
            $changeformat = date('Y-m-d', strtotime($date));
        } else {
            $changeformat = '-';
        }
        return $changeformat;
    }

    // function for get random unique number //
    public static function random_number($length_of_number)
    {
        // Number of all number
        $str_result = '0123456789';
        // Shufle the $str_result and returns substring
        // of specified length
        return substr(str_shuffle($str_result),
            0, $length_of_number);
    }

    // function for generate unique number //
    public static function generateSku()
    {
        $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $skuId = substr(str_shuffle($str_result), 0, 6);
        $exist = ConsignmentNote::where('consignment_no', $skuId)->count();
        if ($exist > 0) {
            self::generateSku();
        }
        return 'C-' . $skuId;
    }

    public static function getJobs($job_id)
    {
        $job = DB::table('consignment_notes')->select('jobs.status as job_status', 'jobs.response_data as trail')
            ->where('consignment_notes.job_id', $job_id)
            ->leftjoin('jobs', function ($data) {
                $data->on('jobs.job_id', '=', 'consignment_notes.job_id')
                    ->on('jobs.id', '=', DB::raw("(select max(id) from jobs WHERE jobs.job_id = consignment_notes.job_id)"));
            })->first();

        if (!empty($job)) {
            $job_data = json_decode($job->trail);
        } else {
            $job_data = '';
        }
        return $job_data;
    }

}
