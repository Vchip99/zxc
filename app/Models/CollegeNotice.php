<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB,Auth;
use App\Libraries\InputSanitise;

class CollegeNotice extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['college_id','college_dept_ids','years','date','created_by','notice','is_emergency'];

    /**
     *  add/update course category
     */
    protected static function addOrUpdateCollegeNotice( Request $request, $isUpdate=false){
        if(1 == count($request->get('department'))){
            $departments = $request->get('department')[0];
        } else {
            foreach($request->get('department') as $index => $deptId){
                if(0 == $index){
                    $departments = $deptId;
                } else {
                    $departments .= ','.$deptId;
                }
            }
        }

        if(1 == count($request->get('year'))){
            $years = $request->get('year')[0];
        } else {
            foreach($request->get('year') as $index => $year){
                if(0 == $index){
                    $years = $year;
                } else {
                    $years .= ','.$year;
                }
            }
        }
        $notice   = InputSanitise::inputString($request->get('notice'));
        $date   = InputSanitise::inputString($request->get('date'));
        $isEmergency   = InputSanitise::inputString($request->get('is_emergency'));
        $noticeId   = InputSanitise::inputInt($request->get('notice_id'));
        if( $isUpdate && isset($noticeId)){
            $collegeClassExam = static::find($noticeId);
            if(!is_object($collegeClassExam)){
            	return 'false';
            }
        } else{
            $collegeClassExam = new static;
        }
        $loginUser = Auth::guard('web')->user();
        $collegeClassExam->college_id = $loginUser->college_id;
        $collegeClassExam->college_dept_ids = $departments;
        $collegeClassExam->years = $years;
        $collegeClassExam->created_by = $loginUser->id;
        $collegeClassExam->notice = $notice;
        $collegeClassExam->date = $date;
        $collegeClassExam->is_emergency = $isEmergency;
        $collegeClassExam->save();
        return $collegeClassExam;
    }

    protected static function getCollegeNoticesByCollegeIdByUserWithPagination($collegeId){
        $loginUser = Auth::user();
        if(User::Lecturer == $loginUser->user_type || User::Hod == $loginUser->user_type){
        	$departments = explode(',',$loginUser->assigned_college_depts);
            $resultQuery = static::where('college_id', $collegeId);
            if(count($departments) > 0){
                sort($departments);
                $resultQuery->where(function($query) use($departments) {
                    foreach($departments as $index => $department){
                        if(0 == $index){
                            $query->whereRaw("find_in_set($department , college_dept_ids)");
                        } else {
                            $query->orWhereRaw("find_in_set($department , college_dept_ids)");
                        }
                    }
                });
            }
            return $resultQuery->orderBy('date','desc')->paginate();
        } else {
        	return static::where('college_id', $collegeId)->orderBy('date','desc')->paginate();
        }
    }

    protected static function getCollegeEmergencyNoticesByCollegeIdByUser($collegeId){
        $loginUser = Auth::user();
        if(User::Lecturer == $loginUser->user_type || User::Hod == $loginUser->user_type){
            $departments = explode(',',$loginUser->assigned_college_depts);
            $resultQuery = static::where('college_id', $collegeId);
            if(count($departments) > 0){
                sort($departments);
                $resultQuery->where(function($query) use($departments) {
                    foreach($departments as $index => $department){
                        if(0 == $index){
                            $query->whereRaw("find_in_set($department , college_dept_ids)");
                        } else {
                            $query->orWhereRaw("find_in_set($department , college_dept_ids)");
                        }
                    }
                });
            }
            return $resultQuery->where('is_emergency', 1)->orderBy('date','desc')->get();
        } else {
            return static::where('college_id', $collegeId)->where('is_emergency', 1)->orderBy('date','desc')->get();
        }
    }


    protected static function getCollegeEmergencyNoticesByCollegeIdByDeptId($collegeId, $deptId){
        return static::where('college_id', $collegeId)->whereRaw("find_in_set($deptId , college_dept_ids)")->where('is_emergency', 1)->orderBy('date','desc')->get();
    }

    protected static function getCollegeNoticesByCollegeIdByUser($collegeId){
        $loginUser = Auth::user();
        if(User::Lecturer == $loginUser->user_type || User::Hod == $loginUser->user_type){
            $departments = explode(',',$loginUser->assigned_college_depts);
            $resultQuery = static::where('college_id', $collegeId);
            if(count($departments) > 0){
                sort($departments);
                $resultQuery->where(function($query) use($departments) {
                    foreach($departments as $index => $department){
                        if(0 == $index){
                            $query->whereRaw("find_in_set($department , college_dept_ids)");
                        } else {
                            $query->orWhereRaw("find_in_set($department , college_dept_ids)");
                        }
                    }
                });
            }
            return $resultQuery->where('is_emergency', 0)->orderBy('date','desc')->get();
        } else {
            return static::where('college_id', $collegeId)->where('is_emergency', 0)->orderBy('date','desc')->get();
        }
    }

    protected static function getCollegeNoticesByCollegeIdByDeptId($collegeId, $deptId){
        return static::where('college_id', $collegeId)->whereRaw("find_in_set($deptId , college_dept_ids)")->where('is_emergency', 0)->orderBy('date','desc')->get();
    }
}
