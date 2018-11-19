<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB,Auth;
use App\Libraries\InputSanitise;

class CollegeExtraClass extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['college_id','college_dept_ids','years','college_subject_id','created_by','topic','date','from_time','to_time'];

    /**
     *  add/update course category
     */
    protected static function addOrUpdateCollegeExtraClass( Request $request, $isUpdate=false){
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
        $topic = InputSanitise::inputString($request->get('topic'));
        $subject   = InputSanitise::inputInt($request->get('subject'));
        $date   = InputSanitise::inputString($request->get('date'));
        $fromTime   = InputSanitise::inputString($request->get('from_time'));
        $toTime   = InputSanitise::inputString($request->get('to_time'));
        $classId   = InputSanitise::inputInt($request->get('class_id'));
        if( $isUpdate && isset($classId)){
            $collegeClass = static::find($classId);
            if(!is_object($collegeClass)){
            	return 'false';
            }
        } else{
            $collegeClass = new static;
        }
        $loginUser = Auth::guard('web')->user();
        $collegeClass->college_id = $loginUser->college_id;
        $collegeClass->college_dept_ids = $departments;
        $collegeClass->years = $years;
        $collegeClass->college_subject_id = $subject;
        $collegeClass->created_by = $loginUser->id;
        $collegeClass->topic = $topic;
        $collegeClass->date = $date;
        $collegeClass->from_time = $fromTime;
        $collegeClass->to_time = $toTime;
        $collegeClass->save();
        return $collegeClass;
    }

    protected static function getCollegeExtraClassByCollegeIdByUserWithPagination($collegeId){
        $loginUser = Auth::user();
        return static::where('college_id', $collegeId)
            ->where('created_by', $loginUser->id)
            ->select('*')->orderBy('date','desc')->paginate();
    }

    protected static function deleteCollegeExtraClassesBySubjectId($subjectId){
        $loginUser = Auth::user();
        $collegeClasses = static::where('college_id', $loginUser->college_id)->where('college_subject_id', $subjectId)->get();
        if(is_object($collegeClasses) && false == $collegeClasses->isEmpty()){
            foreach($collegeClasses as $collegeClass){
                $collegeClass->delete();
            }
        }
        return;
    }

    protected static function getCollegeExtraClassesByCollegeIdByDeptId($collegeId, $deptId){
        return static::where('college_id', $collegeId)
            ->whereRaw("find_in_set($deptId , college_dept_ids)")
            ->orderBy('date','desc')->get();
    }

    protected static function getCollegeExtraClassesByCollegeIdByUser($collegeId){
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
            return $resultQuery->orderBy('date','desc')->get();
        } else {
            return static::where('college_id', $collegeId)->orderBy('date','desc')->get();
        }
    }
}
