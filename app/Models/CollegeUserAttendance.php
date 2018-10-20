<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB, Auth;
use App\Libraries\InputSanitise;

class CollegeUserAttendance extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['attendance_date', 'college_id', 'college_dept_id', 'year','college_subject_id','student_ids','created_by'];

    /**
     *  add/update attendance
     */
    protected static function addOrUpdateCollegeUserAttendance(Request $request){
        $date = InputSanitise::inputString($request->get('attendance_date'));
        $departmentId   = InputSanitise::inputInt($request->get('department'));
        $year   = InputSanitise::inputInt($request->get('year'));
        $subjectId   = InputSanitise::inputInt($request->get('subject'));
        $markAttendance = InputSanitise::inputInt($request->get('mark_attendance'));

        if($request->get('students')){
            $students = $request->get('students');
        } else {
            $students = [];
        }
        if($request->get('all_users')){
            $allUsers = explode(',', $request->get('all_users'));
        } else {
            $allUsers = [];
        }

        $loginUser = Auth::user();

        $attendance = static::where('attendance_date', $date)->where('college_id', $loginUser->college_id)->where('college_dept_id', $departmentId)->where('year', $year)->where('college_subject_id', $subjectId)->first();
        if(!is_object($attendance)){
            $attendance = new static;
        }
        $attendance->attendance_date = $date;
        $attendance->college_id = $loginUser->college_id;
        $attendance->college_dept_id = $departmentId;
        $attendance->year = $year;
        $attendance->college_subject_id = $subjectId;
        $attendance->created_by = $loginUser->id;
        if(1 == $markAttendance){
        	$attendance->student_ids = implode(',',$students);
        } else {
            if(count(array_diff($allUsers, $students)) > 0){
        	   $attendance->student_ids = implode(',', array_diff($allUsers, $students));
            } else {
                $attendance->student_ids = '';
            }
        }
        $attendance->save();
        return $attendance;
    }

    protected static function getCollegeStudentAttendanceByDepartmentIdByYearBySubject(Request $request){
    	$date = InputSanitise::inputString($request->get('attendance_date'));
        $departmentId   = InputSanitise::inputInt($request->get('department_id'));
        $year   = InputSanitise::inputInt($request->get('college_year'));
        $subjectId   = InputSanitise::inputInt($request->get('subject_id'));

        $loginUser = Auth::user();

       	return static::where('attendance_date', $date)->where('college_id', $loginUser->college_id)->where('college_dept_id', $departmentId)->where('year', $year)->where('college_subject_id', $subjectId)->first();
    }

    protected static function getCollegeStudentAttendanceByYearByDepartmentIdByCollegeYearBySubject($year,$selectedDepartment,$selectedCollegeYear,$selectedSubject){
    	$loginUser = Auth::user();
    	return static::whereYear('attendance_date', $year)->where('college_id', $loginUser->college_id)->where('college_dept_id', $selectedDepartment)->where('year', $selectedCollegeYear)->where('college_subject_id', $selectedSubject)->get();
    }

    protected static function deleteAttendanceBySubjectId($subjectId){
        $loginUser = Auth::user();
        $allAttendance = static::where('college_id', $loginUser->college_id)->where('college_subject_id', $subjectId)->get();
        if(is_object($allAttendance) && false == $allAttendance->isEmpty()){
            foreach($allAttendance as $attendance){
                $attendance->delete();
            }
        }
        return;
    }

    protected static function deleteAttendanceByCollegeIdByDepartmentIdsByUserId($collegeId,$removedDepts,$userId){
        $allAttendance = static::where('college_id', $collegeId)
                ->whereIn('college_dept_id', $removedDepts)
                ->where('created_by', $userId)->get();
        if(is_object($allAttendance) && false == $allAttendance->isEmpty()){
            foreach($allAttendance as $attendance){
                $attendance->delete();
            }
        }
        return;
    }

    protected static function deleteAttendanceByUserId($userId){
        $allAttendance = static::where('created_by', $userId)->get();
        if(is_object($allAttendance) && false == $allAttendance->isEmpty()){
            foreach($allAttendance as $attendance){
                $attendance->delete();
            }
        }
        return;
    }

}
