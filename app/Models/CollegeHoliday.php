<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB,Auth;
use App\Libraries\InputSanitise;

class CollegeHoliday extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['college_id','date','note','created_by'];

    /**
     *  add/update
     */
    protected static function addOrUpdateCollegeHoliday( Request $request, $isUpdate=false){
        $message   = InputSanitise::inputString($request->get('message'));
        $date   = InputSanitise::inputString($request->get('date'));
        $holidayId   = InputSanitise::inputInt($request->get('holiday_id'));
        if( $isUpdate && isset($holidayId)){
            $collegeHoliday = static::find($holidayId);
            if(!is_object($collegeHoliday)){
            	return 'false';
            }
        } else{
            $collegeHoliday = new static;
        }
        $loginUser = Auth::guard('web')->user();
        $collegeHoliday->college_id = $loginUser->college_id;
        $collegeHoliday->date = $date;
        $collegeHoliday->note = $message;
        $collegeHoliday->created_by = $loginUser->id;
        $collegeHoliday->save();
        return $collegeHoliday;
    }

    protected static function getCollegeHolidaysByCollegeIdWithPagination($collegeId){
        return static::where('college_id', $collegeId)->orderBy('date','desc')->paginate();
    }

    protected static function getCollegeHolidaysByCollegeId($collegeId){
        return static::where('college_id', $collegeId)->orderBy('id','desc')->get();
    }

    protected static function isCollegeHolidayExist(Request $request){
        $date = $request->get('date');
        $holidayId   = $request->get('holiday_id');
        $loginUser = Auth::guard('web')->user();
        $result = static::where('college_id', $loginUser->college_id)->whereDate('date', $date);
        if($holidayId > 0){
            $result->where('id', '!=', $holidayId);
        }
        $result->first();
        if(is_object($result) && 1 == $result->count()){
            return 'true';
        } else {
            return 'false';
        }
    }
}
