<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use DB,File,Auth,Redirect;
use Intervention\Image\ImageManagerStatic as Image;
use App\Libraries\InputSanitise;

class CollegeTimeTable extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['college_id', 'college_dept_id', 'year','image_path','type','created_by'];

    const CollegeTimeTable = 1;
    const ExamTimeTable 		= 2;
    const Calendar  = 3;

    /**
     *  add/update sub category
     */
    protected static function addOrUpdateCollegeTimeTable( Request $request, $isUpdate=false){
        $collegeDeptId = InputSanitise::inputInt($request->get('department'));
        $year = InputSanitise::inputInt($request->get('year'));
        $type = InputSanitise::inputInt($request->get('type'));
        $timeTableId = InputSanitise::inputInt($request->get('time_table_id'));

        if( $isUpdate && isset($timeTableId)){
            $timeTable = static::find($timeTableId);
            if(!is_object($timeTable)){
                return 'false';
            }
        } else{
            $timeTable = new static;
        }
        $loginUser = Auth::user();
        $timeTable->college_id = $loginUser->college_id;
        $timeTable->college_dept_id = $collegeDeptId;
        $timeTable->year = $year;
        $timeTable->type = $type;
        $timeTable->created_by = $loginUser->id;
        if($request->exists('image_path')){
            $ttImage = $request->file('image_path')->getClientOriginalName();
            $collegeImageFolder = "collegeImages/";

            if(self::CollegeTimeTable == $type){
            	$collegeImageFolderPath = $collegeImageFolder.$loginUser->college_id.'/'.$collegeDeptId.'/'.$year.'/timetable';
            } else if(self::ExamTimeTable == $type){
            	$collegeImageFolderPath = $collegeImageFolder.$loginUser->college_id.'/'.$collegeDeptId.'/'.$year.'/exam';
            } else {
            	$collegeImageFolderPath = $collegeImageFolder.$loginUser->college_id;
            }
            if(!is_dir($collegeImageFolderPath)){
                File::makeDirectory($collegeImageFolderPath, $mode = 0777, true, true);
            }
            $ttImagePath = $collegeImageFolderPath ."/". $ttImage;
            if(file_exists($ttImagePath)){
                unlink($ttImagePath);
            } elseif(!empty($timeTable->id) && file_exists($timeTable->image_path)){
                unlink($timeTable->image_path);
            }
            $request->file('image_path')->move($collegeImageFolderPath, $ttImage);
            $timeTable->image_path = $ttImagePath;

            if(in_array($request->file('image_path')->getClientMimeType(), ['image/jpg', 'image/jpeg', 'image/png'])){
	            // open image
	            $img = Image::make($timeTable->image_path);
	            // enable interlacing
	            $img->interlace(true);
	            // save image interlaced
	            $img->save();
	        }
        }

        $timeTable->save();
        return $timeTable;
    }

    protected static function getCollegeTimeTablesWithPagination(){
    	$loginUser = Auth::user();
    	if(User::Hod == $loginUser->user_type){
    		$assignedDepts = explode(',',$loginUser->assigned_college_depts);
    		return static::where('college_id', $loginUser->college_id)->whereIn('college_dept_id', $assignedDepts)->where('type', self::CollegeTimeTable)->paginate();
    	} else {
    		return static::where('college_id', $loginUser->college_id)->where('type', self::CollegeTimeTable)->paginate();
    	}
    }

    protected static function isCollegeTimeTableExist(Request $request){
        $year = InputSanitise::inputInt($request->get('year'));
        $department = InputSanitise::inputInt($request->get('department'));
        $timeTableId   = InputSanitise::inputInt($request->get('time_table_id'));
        $loginUser = Auth::guard('web')->user();
        $result = static::where('college_id', $loginUser->college_id)->where('college_dept_id', $department)->where('year', '=',$year)->where('type', self::CollegeTimeTable);
        if(!empty($timeTableId)){
            $result->where('id', '!=', $timeTableId);
        }
        $result->first();
        if(is_object($result) && 1 == $result->count()){
            return 'true';
        } else {
            return 'false';
        }
    }

    public function department(){
        return $this->belongsTo(CollegeDept::class, 'college_dept_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'created_by');
    }

    protected static function getExamTimeTablesWithPagination(){
    	$loginUser = Auth::user();
    	if(User::Hod == $loginUser->user_type){
    		$assignedDepts = explode(',',$loginUser->assigned_college_depts);
    		return static::where('college_id', $loginUser->college_id)->whereIn('college_dept_id', $assignedDepts)->where('type', self::ExamTimeTable)->paginate();
    	} else {
    		return static::where('college_id', $loginUser->college_id)->where('type', self::ExamTimeTable)->paginate();
    	}
    }

    protected static function isExamTimeTableExist(Request $request){
        $year = InputSanitise::inputInt($request->get('year'));
        $department = InputSanitise::inputInt($request->get('department'));
        $timeTableId   = InputSanitise::inputInt($request->get('time_table_id'));
        $loginUser = Auth::guard('web')->user();
        $result = static::where('college_id', $loginUser->college_id)->where('college_dept_id', $department)->where('year', '=',$year)->where('type', self::ExamTimeTable);
        if(!empty($timeTableId)){
            $result->where('id', '!=', $timeTableId);
        }
        $result->first();
        if(is_object($result) && 1 == $result->count()){
            return 'true';
        } else {
            return 'false';
        }
    }

    protected static function getCollegeCalendar(){
    	$loginUser = Auth::user();
    	return static::where('college_id', $loginUser->college_id)->where('type', self::Calendar)->first();
    }

    protected static function getStudentCollegeTimeTable(){
    	$loginUser = Auth::user();
		return static::where('college_id', $loginUser->college_id)->where('college_dept_id', $loginUser->college_dept_id)->where('year', $loginUser->year)->where('type', self::CollegeTimeTable)->first();

    }

    protected static function getStudentExamTimeTable(){
    	$loginUser = Auth::user();
		return static::where('college_id', $loginUser->college_id)->where('college_dept_id', $loginUser->college_dept_id)->where('year', $loginUser->year)->where('type', self::ExamTimeTable)->first();

    }
}
