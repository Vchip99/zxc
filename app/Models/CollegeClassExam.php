<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB,Auth;
use App\Libraries\InputSanitise;

class CollegeClassExam extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['college_id','college_dept_ids','years','college_subject_id','created_by','topic','date','from_time','to_time','marks','exam_type'];

    /**
     *  add/update course category
     */
    protected static function addOrUpdateCollegeClassExam( Request $request, $isUpdate=false){
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
        $marks   = InputSanitise::inputString($request->get('marks'));
        $examType   = InputSanitise::inputInt($request->get('exam_type'));
        $examId   = InputSanitise::inputInt($request->get('exam_id'));
        if( $isUpdate && isset($examId)){
            $collegeClassExam = static::find($examId);
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
        $collegeClassExam->college_subject_id = $subject;
        $collegeClassExam->created_by = $loginUser->id;
        $collegeClassExam->topic = $topic;
        $collegeClassExam->date = $date;
        $collegeClassExam->from_time = $fromTime;
        $collegeClassExam->to_time = $toTime;
        $collegeClassExam->marks = $marks;
        $collegeClassExam->exam_type = $examType;
        $collegeClassExam->save();
        return $collegeClassExam;
    }

    protected static function getCollegeClassExamByCollegeIdByUserWithPagination($collegeId){
        $loginUser = Auth::user();
        return static::where('college_id', $collegeId)
            ->where('created_by', $loginUser->id)
            ->select('*')->orderBy('date','desc')->paginate();
    }

    protected static function deleteCollegeClassExamsBySubjectId($subjectId){
        $loginUser = Auth::user();
        $collegeClassExams = static::where('college_id', $loginUser->college_id)->where('college_subject_id', $subjectId)->get();
        if(is_object($collegeClassExams) && false == $collegeClassExams->isEmpty()){
            foreach($collegeClassExams as $collegeClassExam){
                $collegeClassExam->delete();
            }
        }
        return;
    }

    protected static function getCollegeClassExamsByCollegeIdByDeptId($collegeId, $deptId){
        return static::where('college_id', $collegeId)
            ->whereRaw("find_in_set($deptId , college_dept_ids)")
            ->orderBy('date','desc')->get();
    }

    protected static function getCollegeClassExamsByCollegeIdByUser($collegeId){
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

    protected static function getCollegeOfflineExamsByCollegeIdByDeptIdByYear($subjectId,$departmentId,$year){
        return static::where('college_id', Auth::user()->college_id)
            ->where('college_subject_id', $subjectId)
            ->whereRaw("find_in_set($departmentId , college_dept_ids)")
            ->whereRaw("find_in_set($year , years)")
            ->where('exam_type', 0)->get();
    }
}
