<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB, Auth;
use App\Libraries\InputSanitise;
use App\Models\CollegeSubject;

class AssignmentTopic extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name','college_subject_id','lecturer_id', 'college_id', 'college_dept_ids', 'years','lecturer_type'];

    /**
     *  add/update assignment topic
     */
    protected static function addOrUpdateAssignmentTopic( Request $request, $isUpdate=false){
        $topicName = InputSanitise::inputString($request->get('topic'));
        $subjectId   = InputSanitise::inputInt($request->get('subject'));
        $topicId   = InputSanitise::inputInt($request->get('topic_id'));
        if( $isUpdate && isset($topicId)){
            $topic = static::find($topicId);
            if(!is_object($topic)){
            	return 'false';
            }
        } else {
            $topic = new static;
        }

        $collegeDeptIds = '';
        foreach($request->get('departments') as $index => $deptNo){
            if(0 == $index){
                if(!empty($deptNo)){
                    $collegeDeptIds = $deptNo;
                }
            } else {
                if(!empty($deptNo)){
                    if(empty($collegeDeptIds)){
                        $collegeDeptIds = $deptNo;
                    } else {
                        $collegeDeptIds .= ','.$deptNo;
                    }
                }
            }
        }
        if(empty($collegeDeptIds)){
            return 'false';
        }

        $years = '';
        foreach($request->get('years') as $index => $yearNo){
            if(0 == $index){
                if(!empty($yearNo)){
                    $years = $yearNo;
                }
            } else {
                if(!empty($yearNo)){
                    if(empty($years)){
                        $years = $yearNo;
                    } else {
                        $years .= ','.$yearNo;
                    }
                }
            }
        }
        if(empty($years)){
            return 'false';
        }

        $loginUser = Auth::user();
        $topic->name = $topicName;
        $topic->college_subject_id = $subjectId;
        $topic->lecturer_id = $loginUser->id;
        $topic->college_id = $loginUser->college_id;
        $topic->college_dept_ids = $collegeDeptIds;
        $topic->years = $years;
        $topic->lecturer_type = $loginUser->user_type;
        $topic->save();
        return $topic;
    }

    /**
     *  get subject of topic
     */
    public function subject(){
        return $this->belongsTo(CollegeSubject::class, 'college_subject_id');
    }

    protected static function getAssignmentTopics($subjectId,$year=NULL,$department=NULL){
        $loginUser = Auth::guard('web')->user();
        $subjectId = InputSanitise::inputInt($subjectId);
        if(User::Student == $loginUser->user_type){
            $result = static::join('college_subjects','college_subjects.id', '=', 'assignment_topics.college_subject_id')
                    ->join('assignment_questions', 'assignment_questions.assignment_topic_id', 'assignment_topics.id')
                    ->where('assignment_questions.question', '!=',' ');
        } else {
            $result = static::join('college_subjects','college_subjects.id', '=', 'assignment_topics.college_subject_id');
        }
        if($subjectId > 0){
            $result->where('assignment_topics.college_subject_id', $subjectId);
        }
        if(!empty($year) && $year > 0){
            $result->whereRaw("find_in_set($year , assignment_topics.years)");
        }
        if(!empty($department)){
            if('All' == $department){
                if(User::Lecturer == $loginUser->user_type || User::Hod == $loginUser->user_type){
                    $departments = explode(',',$loginUser->assigned_college_depts);
                    if(count($departments) > 0){
                        sort($departments);
                        $result->where(function($query) use($departments) {
                            foreach($departments as $index => $department){
                                if(0 == $index){
                                    $query->whereRaw("find_in_set($department , assignment_topics.college_dept_ids)");
                                } else {
                                    $query->orWhereRaw("find_in_set($department , assignment_topics.college_dept_ids)");
                                }
                            }
                        });
                    }
                }
            } elseif($department > 0) {
                $result->whereRaw("find_in_set($department , assignment_topics.college_dept_ids)");
            }
        }
        if(User::TNP == $loginUser->user_type){
            $result->where('assignment_topics.lecturer_id', $loginUser->id)->where('assignment_topics.lecturer_type', User::TNP);
        } elseif(User::Hod == $loginUser->user_type){
            $result->whereIn('assignment_topics.lecturer_type', [User::Hod,User::Lecturer]);
        } elseif(User::Lecturer == $loginUser->user_type){
            $result->where('assignment_topics.lecturer_id', $loginUser->id)->where('assignment_topics.lecturer_type', User::Lecturer);
        }
        return $result->select('assignment_topics.id', 'assignment_topics.*')->groupBy('assignment_topics.id')->get();
        // return static::join('college_subjects','college_subjects.id', '=', 'assignment_topics.college_subject_id')
        //         ->where('assignment_topics.college_subject_id', $subjectId)->select('assignment_topics.id', 'assignment_topics.*')->groupBy('assignment_topics.id')->get();
    }

    protected static function getAssignmentTopicsForStudentAssignment($subjectId,$year=NULL,$department=NULL){
        $loginUser = Auth::guard('web')->user();
        $subjectId = InputSanitise::inputInt($subjectId);
        $result = static::join('college_subjects','college_subjects.id', '=', 'assignment_topics.college_subject_id')
                ->join('assignment_questions', 'assignment_questions.assignment_topic_id', 'assignment_topics.id')
                ->where('assignment_questions.question', '!=',' ');

        if($subjectId > 0){
            $result->where('assignment_topics.college_subject_id', $subjectId);
        }
        if(!empty($year) && $year > 0){
            $result->whereRaw("find_in_set($year , assignment_topics.years)");
        }
        if(!empty($department)){
            if('All' == $department){
                if(User::Lecturer == $loginUser->user_type || User::Hod == $loginUser->user_type){
                    $departments = explode(',',$loginUser->assigned_college_depts);
                    if(count($departments) > 0){
                        sort($departments);
                        $result->where(function($query) use($departments) {
                            foreach($departments as $index => $department){
                                if(0 == $index){
                                    $query->whereRaw("find_in_set($department , assignment_topics.college_dept_ids)");
                                } else {
                                    $query->orWhereRaw("find_in_set($department , assignment_topics.college_dept_ids)");
                                }
                            }
                        });
                    }
                }
            } elseif($department > 0) {
                $result->whereRaw("find_in_set($department , assignment_topics.college_dept_ids)");
            }
        }
        if(User::TNP == $loginUser->user_type){
            $result->where('assignment_topics.lecturer_id', $loginUser->id)->where('assignment_topics.lecturer_type', User::TNP);
        } elseif(User::Hod == $loginUser->user_type){
            $result->whereIn('assignment_topics.lecturer_type', [User::Hod,User::Lecturer]);
        } elseif(User::Lecturer == $loginUser->user_type){
            $result->where('assignment_topics.lecturer_id', $loginUser->id)->where('assignment_topics.lecturer_type', User::Lecturer);
        }
        return $result->select('assignment_topics.id', 'assignment_topics.*')->groupBy('assignment_topics.id')->get();
    }

    protected static function getAssignDocumentTopics($subjectId){
        $loginUser = Auth::guard('web')->user();
        $subjectId = InputSanitise::inputInt($subjectId);
        return static::join('college_subjects','college_subjects.id', '=', 'assignment_topics.college_subject_id')
                ->join('assignment_questions', 'assignment_questions.assignment_topic_id', 'assignment_topics.id')
                ->whereRaw("find_in_set($loginUser->college_dept_id , assignment_topics.college_dept_ids)")
                ->whereRaw("find_in_set($loginUser->year , assignment_topics.years)")
                ->where('assignment_topics.college_subject_id', $subjectId)
                ->where('assignment_questions.question',' ')
                ->select('assignment_topics.id', 'assignment_topics.*')
                ->groupBy('assignment_topics.id')->get();
    }

    protected static function isAssignmentTopicExist(Request $request){
        $subjectId = InputSanitise::inputInt($request->get('subject_id'));
        $topic = InputSanitise::inputString($request->get('topic'));
        $topicId = InputSanitise::inputInt($request->get('topic_id'));
        $loginUser = Auth::guard('web')->user();
        $result = static::where('name', $topic)->where('college_subject_id', $subjectId)->where('college_id', $loginUser->college_id);
        if(!empty($topicId)){
            $result->where('id', '!=', $topicId);
        }
        $result->first();

        if(is_object($result) && 1 == $result->count()){
            return 'true';
        } else {
            return 'false';
        }
    }

    protected static function getAssignmentTopicsByCollegeId($collegeId){
        $loginUser = Auth::guard('web')->user();
        $result = static::where('college_id', $collegeId);
        if(User::Lecturer == $loginUser->user_type){
            $result->where('lecturer_id', $loginUser->id);
        }
        return $result->get();
    }

    protected static function deleteAssignmentTopicsBySubjectId($subjectId){
        $loginUser = Auth::user();
        $assignmentTopics = static::where('college_id', $loginUser->college_id)->where('college_subject_id', $subjectId)->get();
        if(is_object($assignmentTopics) && false == $assignmentTopics->isEmpty()){
            foreach($assignmentTopics as $assignmentTopic){
                $assignmentTopic->delete();
            }
        }
        return;
    }

    protected static function getAssignmentTopicsByCollegeIdByAssignedDeptsWithPagination($collegeId){
        $loginUser = Auth::guard('web')->user();
        $result = static::join('users','users.id','=','assignment_topics.lecturer_id')
            ->where('assignment_topics.college_id', $collegeId);
        if(User::Lecturer == $loginUser->user_type){
            $result->where('assignment_topics.lecturer_id', $loginUser->id)->where('users.user_type', User::Lecturer);
        } else {
            $result->whereIn('users.user_type', [User::Hod,User::Lecturer]);
        }
        $departments = explode(',',$loginUser->assigned_college_depts);
        if(count($departments) > 0){
            sort($departments);
            $result->where(function($query) use($departments) {
                foreach($departments as $index => $department){
                    if(0 == $index){
                        $query->whereRaw("find_in_set($department , assignment_topics.college_dept_ids)");
                    } else {
                        $query->orWhereRaw("find_in_set($department , assignment_topics.college_dept_ids)");
                    }
                }
            });
        }
        return $result->select('assignment_topics.*','users.name as user')->groupBy('assignment_topics.id')->paginate();
    }

    protected static function getAssignmentTopicsByCollegeIdByAssignedDeptsForList($collegeId){
        $loginUser = Auth::guard('web')->user();
        $result = static::where('college_id', $collegeId);
        if(User::Lecturer == $loginUser->user_type){
            $result->where('lecturer_id', $loginUser->id);
        }
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

    protected static function getAssignmentTopicsByCollegeIdWithPagination($collegeId){
        $loginUser = Auth::guard('web')->user();
        $result = static::join('users','users.id','=','assignment_topics.lecturer_id')
            ->where('assignment_topics.college_id', $collegeId);
        if(User::TNP == $loginUser->user_type){
            $result->where('assignment_topics.lecturer_id', $loginUser->id);
        }
        return $result->select('assignment_topics.*','users.name as user')->groupBy('assignment_topics.id')->paginate();
    }

    protected static function getAssignmentTopicsByDeptIdByYear(Request $request){
        $loginUser = Auth::guard('web')->user();
        return static::join('users','users.id','=','assignment_topics.lecturer_id')
            ->join('college_subjects','college_subjects.id', '=', 'assignment_topics.college_subject_id')
            ->where('assignment_topics.college_id', $loginUser->college_id)
            ->whereRaw("find_in_set($request->department , assignment_topics.college_dept_ids)")
            ->whereRaw("find_in_set($request->year , assignment_topics.years)")
            ->select('assignment_topics.*','users.name as user','college_subjects.name as subject')
            ->groupBy('assignment_topics.id')->get();
    }

    protected static function removeDepartmentsByCollegeIdByDepartmentIdsByUserId($collegeId,$removedDepts,$userId){
        $loginUser = Auth::guard('web')->user();
        $result = static::where('college_id', $collegeId)->where('lecturer_id', $userId);
        if(count($removedDepts) > 0){
            sort($removedDepts);
            $result->where(function($query) use($removedDepts) {
                foreach($removedDepts as $index => $department){
                    if(0 == $index){
                        $query->whereRaw("find_in_set($department , college_dept_ids)");
                    } else {
                        $query->orWhereRaw("find_in_set($department , college_dept_ids)");
                    }
                }
            });
        }
        $topics = $result->get();
        if(is_object($topics) && false == $topics->isEmpty()){
            foreach($topics as $topic){
                $oldDepartments = explode(',',$topic->college_dept_ids);
                $remainingDepts = array_values(array_diff($oldDepartments, $removedDepts));
                if(count($remainingDepts) > 0){
                    $topic->college_dept_ids = implode(',', $remainingDepts);
                } else {
                    $topic->college_dept_ids = '';
                }
                $topic->save();
            }
        }
        return;
    }

    protected static function deleteTopicsByCollegeIdByUserIdForEmptyDept($collegeId,$userId){
        $topics = static::where('college_id', $collegeId)->where('lecturer_id', $userId)->where('college_dept_ids','')->get();
        if(is_object($topics) && false == $topics->isEmpty()){
            foreach($topics as $topic){
                $topic->delete();
            }
        }
        return;
    }

    protected static function deleteAssignmentTopicsByUserId($userId){
        $assignmentTopics = static::where('lecturer_id', $userId)->get();
        if(is_object($assignmentTopics) && false == $assignmentTopics->isEmpty()){
            foreach($assignmentTopics as $assignmentTopic){
                $assignmentTopic->delete();
            }
        }
        return;
    }
}
