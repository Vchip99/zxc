<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB, Auth;
use App\Libraries\InputSanitise;
use App\Models\CollegeUserAttendance;
use App\Models\CollegeOfflinePaper;
use App\Models\CollegeOfflinePaperMarks;
use App\Models\AssignmentTopic;
use App\Models\AssignmentQuestion;
use App\Models\AssignmentAnswer;

class CollegeSubject extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'college_id', 'college_dept_ids', 'years', 'lecturer_id'];


    /**
     *  add/update college subject
     */
    protected static function addOrUpdateCollegeSubject( Request $request, $isUpdate=false){
        $subjectName = InputSanitise::inputString($request->get('subject'));
        if(1 == count($request->get('years'))){
        	$years   = $request->get('years')[0];
        } else {
        	$years   = implode(',', $request->get('years'));
        }
        if(1 == count($request->get('depts'))){
        	$depts   = $request->get('depts')[0];
        } else {
        	$depts   = implode(',', $request->get('depts'));
        }
        $subjectId   = InputSanitise::inputInt($request->get('subject_id'));
        if( $isUpdate && isset($subjectId)){
            $subject = static::find($subjectId);
            if(!is_object($subject)){
            	return 'false';
            }
        } else{
            $subject = new static;
        }
        $loginUser = Auth::user();
        $subject->name = $subjectName;
        $subject->college_id = $loginUser->college_id;
        $subject->college_dept_ids = $depts;
        $subject->years = $years;
        $subject->lecturer_id = $loginUser->id;
        $subject->save();
        return $subject;
    }

    protected static function isCollegeSubjectExist(Request $request){
        $subject = InputSanitise::inputString($request->get('subject'));
        $subjectId = InputSanitise::inputInt($request->get('subject_id'));
        $loginUser = Auth::user();
        $result = static::where('name', $subject)->where('college_id', $loginUser->college_id);
        if(!empty($subjectId)){
            $result->where('id', '!=', $subjectId);
        }
        $result->first();

        if(is_object($result) && 1 == $result->count()){
            return 'true';
        } else {
            return 'false';
        }
    }

    protected static function getCollegeSubjectsByDepartmentIdByYear($department,$year){
    	$loginUser = Auth::user();
        return static::where('college_id', $loginUser->college_id)->where('lecturer_id', $loginUser->id)->whereRaw("find_in_set($department , college_dept_ids)")->whereRaw("find_in_set($year , years)")->select('id','name')->get();
    }

    protected static function getCollegeDepartmentsBySubjectId($subjectId){
        $loginUser = Auth::user();
        return static::where('college_id', $loginUser->college_id)->where('id',$subjectId)->first();
    }

    protected static function getCollegeSubjectByCollegeId($collegeId){
        $loginUser = Auth::user();
        $result = static::where('college_id', $collegeId);
        if(User::TNP == $loginUser->user_type){
            $result->where('lecturer_id', $loginUser->id);
        }
        return $result->get();
    }

    protected static function getCollegeSubjectByCollegeIdByUserId($collegeId,$userId){
        return static::where('college_id', $collegeId)->where('lecturer_id', $userId)->get();
    }

    protected static function getCollegeSubjectByCollegeIdWithPagination($collegeId){
        $loginUser = Auth::user();
        $result = static::join('users','users.id','=','college_subjects.lecturer_id')
                ->where('college_subjects.college_id', $collegeId);
        if(User::TNP == $loginUser->user_type){
            $result->where('college_subjects.lecturer_id', $loginUser->id);
        }
        return $result->select('college_subjects.*','users.name as user')->groupBy('college_subjects.id')->paginate();
    }

    protected static function getCollegeSubjectByCollegeIdByAssignedDeptsWithPagination($collegeId){
        $loginUser = Auth::user();
        $result = static::join('users','users.id','=','college_subjects.lecturer_id')
            ->where('college_subjects.college_id', $collegeId);
        if(User::Lecturer == $loginUser->user_type){
            $result->where('college_subjects.lecturer_id', $loginUser->id)->where('users.user_type', User::Lecturer);
        } else {
            $result->whereIn('users.user_type', [User::Hod,User::Lecturer]);
        }
        $departments = explode(',',$loginUser->assigned_college_depts);
        if(count($departments) > 0){
            sort($departments);
            $result->where(function($query) use($departments) {
                foreach($departments as $index => $department){
                    if(0 == $index){
                        $query->whereRaw("find_in_set($department , college_subjects.college_dept_ids)");
                    } else {
                        $query->orWhereRaw("find_in_set($department , college_subjects.college_dept_ids)");
                    }
                }
            });
        }
        return $result->select('college_subjects.*','users.name as user')->groupBy('college_subjects.id')->paginate();
    }

    protected static function getCollegeSubjectByCollegeIdByAssignedDeptsForList($collegeId){
        $loginUser = Auth::user();
        $result = static::join('users','users.id','=','college_subjects.lecturer_id')
            ->where('college_subjects.college_id', $collegeId);
        if(User::Lecturer == $loginUser->user_type){
            $result->where('college_subjects.lecturer_id', $loginUser->id)->where('users.user_type', User::Lecturer);
        } else {
            $result->whereIn('users.user_type', [User::Hod,User::Lecturer]);
        }
        $departments = explode(',',$loginUser->assigned_college_depts);
        if(count($departments) > 0){
            sort($departments);
            $result->where(function($query) use($departments) {
                foreach($departments as $index => $department){
                    if(0 == $index){
                        $query->whereRaw("find_in_set($department , college_subjects.college_dept_ids)");
                    } else {
                        $query->orWhereRaw("find_in_set($department , college_subjects.college_dept_ids)");
                    }
                }
            });
        }
        return $result->select('college_subjects.*')->groupBy('college_subjects.id')->get();
    }

    protected static function getCollegeSubjectByCollegeIdByAssignedDeptsByUser($collegeId){
        $loginUser = Auth::user();
        $result = static::where('college_id', $collegeId)->where('lecturer_id', $loginUser->id);
        $departments = explode(',',$loginUser->assigned_college_depts);
        if(count($departments) > 0){
            sort($departments);
            $result->where(function($query) use($departments) {
                foreach($departments as $index => $department){
                    if(0 == $index){
                        $query->whereRaw("find_in_set($department , college_dept_ids)");
                    } else {
                        $query->orWhereRaw("find_in_set($department , college_dept_ids)");
                    }
                }
            });
        }
        return $result->get();
    }

    protected static function getCollegeSubjectByCollegeIdByAssignedDepts($collegeId){
        $loginUser = Auth::user();
        $result = static::where('college_id', $collegeId);
        $departments = explode(',',$loginUser->assigned_college_depts);
        if(count($departments) > 0){
            sort($departments);
            $result->where(function($query) use($departments) {
                foreach($departments as $index => $department){
                    if(0 == $index){
                        $query->whereRaw("find_in_set($department , college_dept_ids)");
                    } else {
                        $query->orWhereRaw("find_in_set($department , college_dept_ids)");
                    }
                }
            });
        }
        return $result->get();
    }

    protected static function getCollegeSubjectByYear($year,$lecturer=NULL,$collegeDept=NULL){
        $loginUser = Auth::user();
        if($lecturer > 0){
            $resultQuery = static::join('users','users.id','=','college_subjects.lecturer_id')
                ->join('assignment_topics','assignment_topics.college_subject_id', '=', 'college_subjects.id')->where('college_subjects.lecturer_id', $lecturer);
        } else {
            if(User::Lecturer == $loginUser->user_type || User::TNP == $loginUser->user_type){
                $resultQuery = static::join('users','users.id','=','college_subjects.lecturer_id')
                    ->join('assignment_topics','assignment_topics.college_subject_id', '=', 'college_subjects.id')->where('college_subjects.lecturer_id', $loginUser->id);
            } else {
                $resultQuery = static::join('users','users.id','=','college_subjects.lecturer_id')
                    ->join('assignment_topics','assignment_topics.college_subject_id', '=', 'college_subjects.id');
            }
        }
        $resultQuery->where('college_subjects.college_id', $loginUser->college_id);
        if(User::Lecturer == $loginUser->user_type){
            $resultQuery->where('users.user_type', User::Lecturer);
        } else if(User::TNP == $loginUser->user_type){
            $resultQuery->where('users.user_type', User::TNP);
        } else if(User::Hod == $loginUser->user_type){
            $resultQuery->whereIn('users.user_type', [User::Hod,User::Lecturer]);
        } else if(User::Directore == $loginUser->user_type){
            $resultQuery->whereIn('users.user_type', [User::Lecturer,User::Hod,User::Directore,User::TNP]);
        }

        if(!empty($collegeDept)){
            if('All' == $collegeDept){
                if(User::Lecturer == $loginUser->user_type || User::Hod == $loginUser->user_type){
                    $departments = explode(',',$loginUser->assigned_college_depts);
                    if(count($departments) > 0){
                        sort($departments);
                        $resultQuery->where(function($query) use($departments) {
                            foreach($departments as $index => $department){
                                if(0 == $index){
                                    $query->whereRaw("find_in_set($department , college_subjects.college_dept_ids)");
                                } else {
                                    $query->orWhereRaw("find_in_set($department , college_subjects.college_dept_ids)");
                                }
                            }
                        });
                    }
                }
            } else if($collegeDept > 0) {
                $resultQuery->whereRaw("find_in_set($collegeDept , college_subjects.college_dept_ids)");
            }
        } else if($loginUser->college_dept_id > 0){
        	$resultQuery->whereRaw("find_in_set($loginUser->college_dept_id , college_subjects.college_dept_ids)");
        }
        if(!empty($year) && 'All' != $year && $year > 0){
            $resultQuery->whereRaw("find_in_set($year , college_subjects.years)");
        }
        return $resultQuery->select('college_subjects.id','college_subjects.*')->groupBy('college_subjects.id')->get();
    }

    protected static function getAssignmentSubjectsOfGivenAssignmentByLecturer(Request $request){
        return static::join('assignment_questions', 'assignment_questions.college_subject_id', '=', 'college_subjects.id')->where('assignment_questions.lecturer_id',$request->lecturer_id)
            ->select('college_subjects.id','college_subjects.*')->groupBy('college_subjects.id')->get();
    }

    protected static function getCollegeSubjectsByDeptIdByYear(Request $request){
        $loginUser = Auth::guard('web')->user();
        return static::join('users','users.id','=','college_subjects.lecturer_id')
            ->where('college_subjects.college_id', $loginUser->college_id)
            ->whereRaw("find_in_set($request->department , college_subjects.college_dept_ids)")
            ->whereRaw("find_in_set($request->year , college_subjects.years)")
            ->select('college_subjects.*','users.name as user')
            ->groupBy('college_subjects.id')->get();
    }

    protected static function deleteCollegeSubjectsByUserId($userId){
        $subjects =  static::where('lecturer_id', $userId)->get();
        if(is_object($subjects) && false == $subjects->isEmpty()){
            foreach($subjects as $subject){
                // delete attendance
                CollegeUserAttendance::deleteAttendanceByUserId($userId);
                // delete offline paper
                CollegeOfflinePaper::deleteCollegeOfflinePapersByUserId($userId);
                // delete offline paper marks
                CollegeOfflinePaperMarks::deleteCollegeOfflinePaperMarksByUserId($userId);
                // delete assignment topic
                AssignmentTopic::deleteAssignmentTopicsByUserId($userId);
                // delete assignment
                $assignments = AssignmentQuestion::getAssignmentsByUserId($userId);
                if(is_object($assignments) && false == $assignments->isEmpty()){
                    foreach($assignments as $assignment){
                        $answers = AssignmentAnswer::where('assignment_question_id', $assignment->id)->get();
                        if(is_object($answers) && false == $answers->isEmpty()){
                            foreach($answers as $answer){
                                $dir = dirname($answer->attached_link);
                                InputSanitise::delFolder($dir);
                                $answer->delete();
                            }
                        }
                        $dir = dirname($assignment->attached_link);
                        InputSanitise::delFolder($dir);
                        $assignment->delete();
                    }
                }
                $subject->delete();
            }
        }
        return;
    }
}
