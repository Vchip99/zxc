<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB, Auth;
use App\Libraries\InputSanitise;
use App\Models\CollegeSubject;
use App\Models\AssignmentTopic;

class AssignmentQuestion extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['question','college_subject_id','assignment_topic_id', 'attached_link', 'lecturer_id', 'college_id', 'college_dept_ids', 'years'];

    /**
     *  add/update assignment
     */
    protected static function addOrUpdateAssignment( Request $request, $isUpdate=false){
        $question = $request->get('question');
        $subjectId   = InputSanitise::inputInt($request->get('subject'));
        $topicId   = InputSanitise::inputInt($request->get('topic'));
        $assignmentId   = InputSanitise::inputInt($request->get('assignment_id'));

        if( $isUpdate && isset($assignmentId)){
            $assignment = static::find($assignmentId);
            if(!is_object($assignment)){
            	return 'false';
            }
        } else {
            $assignment = new static;
        }
        $assignmentTopic = AssignmentTopic::find($topicId);
        if(!is_object($assignmentTopic)){
            return 'false';
        }

        $loginUser = Auth::user();
        $assignment->question = $question;
        $assignment->college_subject_id = $subjectId;
        $assignment->assignment_topic_id = $topicId;
        $assignment->lecturer_id = $loginUser->id;
        $assignment->college_id = $loginUser->college_id;
        $assignment->college_dept_ids = $assignmentTopic->college_dept_ids;
        $assignment->years = $assignmentTopic->years;

        if($request->exists('attached_link')){
	        $attachmentFolderPath = "assignmentStorage/topicId-".$topicId;
	        if(!is_dir($attachmentFolderPath)){
	        	mkdir($attachmentFolderPath, 0755);
	        }
	     	$attachedLinkFile = $request->file('attached_link')->getClientOriginalName();
	        $attachedLinkFilePath = $attachmentFolderPath."/".$attachedLinkFile;
	        if(file_exists($attachedLinkFilePath)){
	        	unlink($attachedLinkFilePath);
	        }
	        $request->file('attached_link')->move($attachmentFolderPath, $attachedLinkFile);
	        $assignment->attached_link = $attachedLinkFilePath;
        }
        $assignment->save();
        return $assignment;
    }

        /**
     *  get subject
     */
    public function subject(){
        return $this->belongsTo(CollegeSubject::class, 'college_subject_id');
    }

    /**
     *  get topic
     */
    public function topic(){
        return $this->belongsTo(AssignmentTopic::class, 'assignment_topic_id');
    }

    protected static function getStudentAssignments(){
        $loginUser = Auth::user();
        return static::where('college_id', $loginUser->college_id)
                ->where('question', '!=', ' ')
                ->whereRaw("find_in_set($loginUser->college_dept_id , college_dept_ids)")
                ->whereRaw("find_in_set($loginUser->year , years)")
                ->paginate();
    }

    protected static function getStudentDocuments(){
        $loginUser = Auth::user();
        return static::where('college_id', $loginUser->college_id)
                ->where('question', '')
                ->whereRaw("find_in_set($loginUser->college_dept_id , college_dept_ids)")
                ->whereRaw("find_in_set($loginUser->year , years)")
                ->paginate();
    }

    protected static function getAssignments(Request $request){
        $loginUser = Auth::user();

        $resultQuery = static::join('users','users.id','=','assignment_questions.lecturer_id')
            ->where('assignment_questions.college_id', $loginUser->college_id);
        if(User::Lecturer == $loginUser->user_type){
            $resultQuery->where('users.user_type', User::Lecturer);
        } else if(User::TNP == $loginUser->user_type){
            $resultQuery->where('users.user_type', User::TNP);
        } else if(User::Hod == $loginUser->user_type){
            $resultQuery->whereIn('users.user_type', [User::Hod,User::Lecturer]);
        } else if(User::Directore == $loginUser->user_type){
            $resultQuery->whereIn('users.user_type', [User::Lecturer,User::Hod,User::Directore,User::TNP]);
        }

        if(!empty($request->department)){
            if('All' == $request->department){
                if(User::Lecturer == $loginUser->user_type || User::Hod == $loginUser->user_type){
                    $departments = explode(',',$loginUser->assigned_college_depts);
                    if(count($departments) > 0){
                        sort($departments);
                        $resultQuery->where(function($query) use($departments) {
                            foreach($departments as $index => $department){
                                if(0 == $index){
                                    $query->whereRaw("find_in_set($department , assignment_questions.college_dept_ids)");
                                } else {
                                    $query->orWhereRaw("find_in_set($department , assignment_questions.college_dept_ids)");
                                }
                            }
                        });
                    }
                }
            } else {
                $resultQuery->whereRaw("find_in_set($request->department , assignment_questions.college_dept_ids)");
            }
        }

        if(!empty($request->year) && 'All' != $request->year && $request->year > 0){
            $resultQuery->whereRaw("find_in_set($request->year , assignment_questions.years)");
        }else if(User::Student == $loginUser->user_type){
            $resultQuery->whereRaw("find_in_set($loginUser->year , assignment_questions.years)");
        }

        if(User::Lecturer == $loginUser->user_type || User::TNP == $loginUser->user_type){
            $resultQuery->where('assignment_questions.lecturer_id', $loginUser->id);
        } else if(!empty($request->lecturer_id) && $request->lecturer_id > 0){
            $resultQuery->where('assignment_questions.lecturer_id', $request->lecturer_id);
        }
        if(!empty($request->subject) && $request->subject > 0){
            $resultQuery->where('assignment_questions.college_subject_id', $request->subject);
        }

        if(!empty($request->topic) && $request->topic > 0){
            $resultQuery->where('assignment_questions.assignment_topic_id', $request->topic);
        }
        if(User::Student == $loginUser->user_type){
            $resultQuery->where('question','!=',' ');
        }
        return $resultQuery->select('assignment_questions.*')->groupBy('assignment_questions.id')->get();
    }

    protected static function getAssignDocuments(Request $request){
        $loginUser = Auth::user();

        $resultQuery = static::join('users','users.id','=','assignment_questions.lecturer_id')
            ->where('assignment_questions.college_id', $loginUser->college_id)
            ->whereRaw("find_in_set($loginUser->college_dept_id , assignment_questions.college_dept_ids)")
            ->whereRaw("find_in_set($loginUser->year , assignment_questions.years)")
            ->where('assignment_questions.question', '');

        if(!empty($request->lecturer_id) && $request->lecturer_id > 0){
            $resultQuery->where('assignment_questions.lecturer_id', $request->lecturer_id);
        }
        if(!empty($request->subject) && $request->subject > 0){
            $resultQuery->where('assignment_questions.college_subject_id', $request->subject);
        }

        if(!empty($request->topic) && $request->topic > 0){
            $resultQuery->where('assignment_questions.assignment_topic_id', $request->topic);
        }

        return $resultQuery->select('assignment_questions.*')->groupBy('assignment_questions.id')->get();
    }

    protected static function getAssignmentByTopic($topic){
        if(User::Student == Auth::user()->user_type){
            $userYear = Auth::user()->year;
            return static::where('assignment_topic_id',$topic)->whereRaw("find_in_set($userYear , years)")->first();
        } else {
            return static::where('assignment_topic_id',$topic)->first();
        }
    }

    protected static function getAssignDocumentByTopic($topic){
        return static::where('assignment_topic_id',$topic)->where('question', '')->first();
    }

    protected static function getAssignmentByDeptIdByYearBySubjectIdByTopicIdForStudent($deptId,$year,$subjectId,$topicId,$student){
        $loginUser = Auth::user();
        if(User::Student == $loginUser->user_type){
            $userYear = $loginUser->year;
            return static::where('assignment_topic_id',$topic)->whereRaw("find_in_set($userYear , years)")->first();
        } else {
            if(User::TNP == $loginUser->user_type){
                $result = static::join('users','users.college_id','=','assignment_questions.college_id')
                    ->where('assignment_questions.college_id',$loginUser->college_id)
                    ->where('assignment_questions.lecturer_id',$loginUser->id)
                    ->where('users.user_type',User::Student);
                if($topicId > 0){
                    $result->where('assignment_questions.assignment_topic_id',$topicId);
                }
                if($subjectId > 0){
                    $result->where('assignment_questions.college_subject_id',$subjectId);
                }
                if($year > 0){
                    $result->whereRaw("find_in_set($year , assignment_questions.years)")->where('users.year',$year);
                }
                if($deptId > 0){
                    $result->whereRaw("find_in_set($deptId , assignment_questions.college_dept_ids)")->where('users.college_dept_id',$deptId);
                }
                if($student > 0){
                    $result->where('users.id',$student);
                }
                return $result->select('assignment_questions.*','users.id as user_id','users.name as user')
                    ->groupBy('assignment_questions.id','users.id')->get();
            }elseif(User::Directore == $loginUser->user_type){
                $result = static::join('users','users.college_id','=','assignment_questions.college_id')
                    ->where('assignment_questions.college_id',$loginUser->college_id)
                    ->where('users.user_type',User::Student);
                if($topicId > 0){
                    $result->where('assignment_questions.assignment_topic_id',$topicId);
                }
                if($subjectId > 0){
                    $result->where('assignment_questions.college_subject_id',$subjectId);
                }
                if($year > 0){
                    $result->whereRaw("find_in_set($year , assignment_questions.years)")->where('users.year',$year);
                }
                if($deptId > 0){
                    $result->whereRaw("find_in_set($deptId , assignment_questions.college_dept_ids)")->where('users.college_dept_id',$deptId);
                }
                if($student > 0){
                    $result->where('users.id',$student);
                }
                return $result->select('assignment_questions.*','users.id as user_id','users.name as user')
                    ->groupBy('assignment_questions.id','users.id')->get();
            }elseif(User::Hod == $loginUser->user_type){
                $result = static::join('users','users.college_id','=','assignment_questions.college_id')
                    ->join('assignment_topics','assignment_topics.id', '=', 'assignment_questions.assignment_topic_id')
                    ->where('assignment_questions.college_id',$loginUser->college_id)
                    ->where('users.user_type',User::Student)
                    ->whereIn('assignment_topics.lecturer_type',[User::Lecturer,User::Hod]);
                if($topicId > 0){
                    $result->where('assignment_questions.assignment_topic_id',$topicId);
                }
                if($subjectId > 0){
                    $result->where('assignment_questions.college_subject_id',$subjectId);
                }
                if($year > 0){
                    $result->whereRaw("find_in_set($year , assignment_questions.years)")->where('users.year',$year);
                }
                if($deptId > 0){
                    $result->whereRaw("find_in_set($deptId , assignment_questions.college_dept_ids)")->where('users.college_dept_id',$deptId);
                } else {
                    $departments = explode(',',$loginUser->assigned_college_depts);
                    if(count($departments) > 0){
                        sort($departments);
                        $result->where(function($query) use($departments) {
                            foreach($departments as $index => $department){
                                if(0 == $index){
                                    $query->whereRaw("find_in_set($department , assignment_questions.college_dept_ids)");
                                } else {
                                    $query->orWhereRaw("find_in_set($department , assignment_questions.college_dept_ids)");
                                }
                            }
                        });
                        $result->whereIn('users.college_dept_id',$departments);
                    }
                }
                if($student > 0){
                    $result->where('users.id',$student);
                }
                return $result->select('assignment_questions.*','users.id as user_id','users.name as user')
                    ->groupBy('assignment_questions.id','users.id')->get();
            }elseif(User::Lecturer == $loginUser->user_type){
                $result = static::join('users','users.college_id','=','assignment_questions.college_id')
                    ->join('assignment_topics','assignment_topics.id', '=', 'assignment_questions.assignment_topic_id')
                    ->where('assignment_questions.college_id',$loginUser->college_id)
                    ->where('assignment_questions.lecturer_id',$loginUser->id)
                    ->where('users.user_type',User::Student)
                    ->where('assignment_topics.lecturer_type',User::Lecturer);
                if($topicId > 0){
                    $result->where('assignment_questions.assignment_topic_id',$topicId);
                }
                if($subjectId > 0){
                    $result->where('assignment_questions.college_subject_id',$subjectId);
                }
                if($year > 0){
                    $result->whereRaw("find_in_set($year , assignment_questions.years)")->where('users.year',$year);
                }
                if($deptId > 0){
                    $result->whereRaw("find_in_set($deptId , assignment_questions.college_dept_ids)")->where('users.college_dept_id',$deptId);
                } else {
                    $departments = explode(',',$loginUser->assigned_college_depts);
                    if(count($departments) > 0){
                        sort($departments);
                        $result->where(function($query) use($departments) {
                            foreach($departments as $index => $department){
                                if(0 == $index){
                                    $query->whereRaw("find_in_set($department , assignment_questions.college_dept_ids)");
                                } else {
                                    $query->orWhereRaw("find_in_set($department , assignment_questions.college_dept_ids)");
                                }
                            }
                        });
                        $result->whereIn('users.college_dept_id',$departments);
                    }
                }
                if($student > 0){
                    $result->where('users.id',$student);
                }
                return $result->select('assignment_questions.*','users.id as user_id','users.name as user')
                    ->groupBy('assignment_questions.id','users.id')->get();
            }
        }
    }

    protected static function checkAssignmentIsExist(Request $request){
        $result = [];
        $assignment = static::where('college_subject_id',$request->subject)
            ->where('assignment_topic_id', $request->topic)
            ->first();
        if(is_object($assignment)){
            $result['status'] = 'true';
            $result['id'] = $assignment->id;
        } else {
            $result['status'] = 'false';
        }
        return $result;
    }

    protected static function getAssignmentsBySubjectId($subjectId){
        $loginUser = Auth::user();
        return static::where('college_id', $loginUser->college_id)->where('college_subject_id',$subjectId)->get();
    }

    protected static function getAssignmentsByCollegeIdWithPagination($collegeId){
        $loginUser = Auth::guard('web')->user();
        $result = static::where('college_id', $collegeId);
        if(User::TNP == $loginUser->user_type){
            $result->where('lecturer_id', $loginUser->id);
        }
        return $result->paginate();
    }

    protected static function getAssignmentsByCollegeIdByAssignedDeptsWithPagination($collegeId){
        $loginUser = Auth::guard('web')->user();
        $result = static::join('users','users.id','=','assignment_questions.lecturer_id')
            ->where('assignment_questions.college_id', $collegeId);
        if(User::Lecturer == $loginUser->user_type){
            $result->where('assignment_questions.lecturer_id', $loginUser->id)->where('users.user_type', User::Lecturer);
        } else {
            $result->whereIn('users.user_type', [User::Hod,User::Lecturer]);
        }

        $departments = explode(',',$loginUser->assigned_college_depts);
        if(count($departments) > 0){
            sort($departments);
            $result->where(function($query) use($departments) {
                foreach($departments as $index => $department){
                    if(0 == $index){
                        $query->whereRaw("find_in_set($department , assignment_questions.college_dept_ids)");
                    } else {
                        $query->orWhereRaw("find_in_set($department , assignment_questions.college_dept_ids)");
                    }
                }
            });
        }
        return $result->select('assignment_questions.*')->groupBy('assignment_questions.id')->paginate();
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
        $assignments = $result->get();
        if(is_object($assignments) && false == $assignments->isEmpty()){
            foreach($assignments as $assignment){
                $oldDepartments = explode(',',$assignment->college_dept_ids);
                $remainingDepts = array_values(array_diff($oldDepartments, $removedDepts));
                if(count($remainingDepts) > 0){
                    $assignment->college_dept_ids = implode(',', $remainingDepts);
                } else {
                    $assignment->college_dept_ids = '';
                }
                $assignment->save();
            }
        }
        return;
    }

    protected static function deleteAssignmentsByCollegeIdByUserIdForEmptyDept($collegeId,$userId){
        $assignments = static::where('college_id', $collegeId)->where('lecturer_id', $userId)->where('college_dept_ids','')->get();
        if(is_object($assignments) && false == $assignments->isEmpty()){
            foreach($assignments as $assignment){
                $dir = dirname($assignment->attached_link);
                InputSanitise::delFolder($dir);
                $assignment->delete();
            }
        }
        return;
    }

    protected static function getAssignmentsByUserId($userId){
        return static::where('lecturer_id', $userId)->get();
    }
}
