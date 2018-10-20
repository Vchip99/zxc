<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Auth,Hash,DB, Redirect,Session,Validator,Input;
use App\Models\AssignmentTopic;
use App\Models\CollegeSubject;
use App\Models\AssignmentAnswer;
use App\Models\AssignmentQuestion;
use App\Models\CollegeDept;
use App\Models\User;
use App\Libraries\InputSanitise;

class AssignmentTopicController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
    }

    /**
     * Define your validation rules in a property in
     * the controller to reuse the rules.
     */
    protected $validateCreateSubject = [
        'subject' => 'required',
        'topic' => 'required',
        'departments' => 'required',
        'years' => 'required',
    ];

    /**
     * show all topics
     */
    protected function show($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $allSubjects = [];
        $allCollegeDepts = [];

        $loginUser = Auth::user();
        if(User::Hod == $loginUser->user_type || User::Lecturer == $loginUser->user_type){
            $topics = AssignmentTopic::getAssignmentTopicsByCollegeIdByAssignedDeptsWithPagination($loginUser->college_id);
        } else {
            $topics = AssignmentTopic::getAssignmentTopicsByCollegeIdWithPagination($loginUser->college_id);
        }

        if(User::Hod == $loginUser->user_type || User::Lecturer == $loginUser->user_type){
            $subjects = CollegeSubject::getCollegeSubjectByCollegeIdByAssignedDeptsForList($loginUser->college_id);
        } else {
            $subjects = CollegeSubject::getCollegeSubjectByCollegeId($loginUser->college_id);
        }
        if(is_object($subjects) && false == $subjects->isEmpty()){
            foreach($subjects as $subject){
                $allSubjects[$subject->id] = $subject->name;
            }
        }
        // if(User::Hod == $loginUser->user_type || User::Lecturer == $loginUser->user_type){
        //     $deptIds = explode(',',$loginUser->assigned_college_depts);
        //     $collegeDepts = CollegeDept::getDepartmentsByCollegeIdByDeptIds($loginUser->college_id,$deptIds);
        // } else {
            $collegeDepts = CollegeDept::getDepartmentsByCollegeId($loginUser->college_id);
        // }
        if(is_object($collegeDepts) && false == $collegeDepts->isEmpty()){
            foreach($collegeDepts as $collegeDept){
                $allCollegeDepts[$collegeDept->id] = $collegeDept->name;
            }
        }
    	return view('assignmentTopic.list', compact('topics','allSubjects','allCollegeDepts'));
    }

    /**
     *  show create UI for topic
     */
    protected function create($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $collegeDepts = [];
        $years = [];
        $selectedDepts = [];
        $selectedYears = [];
        $loginUser = Auth::user();
    	if(User::Hod == $loginUser->user_type || User::Lecturer == $loginUser->user_type){
            $subjects = CollegeSubject::getCollegeSubjectByCollegeIdByAssignedDeptsByUser($loginUser->college_id);
        } else {
            $subjects = CollegeSubject::getCollegeSubjectByCollegeIdByUserId($loginUser->college_id,$loginUser->id);
        }
    	$topic = new AssignmentTopic;
    	return view('assignmentTopic.create', compact('subjects','topic','years','collegeDepts','selectedDepts','selectedYears'));
    }

    /**
     *  store topic
     */
    protected function store($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $v = Validator::make($request->all(), $this->validateCreateSubject);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::beginTransaction();
        try
        {
            $topic = AssignmentTopic::addOrUpdateAssignmentTopic($request);
            if(is_object($topic)){
                DB::commit();
                return Redirect::to('college/'.$collegeUrl.'/manageAssignmentTopic')->with('message', 'Assignment Topic created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
		return Redirect::to('college/'.$collegeUrl.'/manageAssignmentTopic');
    }


    /**
     * edit topic
     */
    protected function edit($collegeUrl,$id,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
    	$topicId = InputSanitise::inputInt(json_decode($id));
    	if(isset($topicId)){
    		$topic = AssignmentTopic::find($topicId);
    		if(is_object($topic)){
                $loginUser = Auth::user();
                if($loginUser->id == $topic->lecturer_id || (User::Hod == $loginUser->user_type || User::Directore == $loginUser->user_type)){
                    $collegeDepts = [];
                    $years = [];
                    $selectedDepts = explode(',', $topic->college_dept_ids);
                    $selectedYears = explode(',', $topic->years);
        			if(User::Hod == $loginUser->user_type || User::Lecturer == $loginUser->user_type){
                        $subjects = CollegeSubject::getCollegeSubjectByCollegeIdByAssignedDeptsForList($loginUser->college_id);
                    } else {
                        $subjects = CollegeSubject::getCollegeSubjectByCollegeId($loginUser->college_id);
                    }
                    $collegeSubject = CollegeSubject::getCollegeDepartmentsBySubjectId($topic->college_subject_id);
                    if(is_object($collegeSubject)){
                        $deptIds =  explode(',',  $collegeSubject->college_dept_ids);
                        $years =  explode(',',  $collegeSubject->years);
                        if(count($deptIds) > 0){
                            $collegeDepts = CollegeDept::find($deptIds);
                        }
                    }
        			return view('assignmentTopic.create', compact('subjects','topic','years','collegeDepts','selectedDepts','selectedYears'));
                }
    		}
    	}
		return Redirect::to('college/'.$collegeUrl.'/manageAssignmentTopic');
    }

    /**
     * update topic
     */
    protected function update($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $v = Validator::make($request->all(), $this->validateCreateSubject);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        $topicId = InputSanitise::inputInt($request->get('topic_id'));
        if(isset($topicId)){
            DB::beginTransaction();
            try
            {
                $topic = AssignmentTopic::addOrUpdateAssignmentTopic($request, true);
                if(is_object($topic)){
                    DB::commit();
                    return Redirect::to('college/'.$collegeUrl.'/manageAssignmentTopic')->with('message', 'Assignment Topic updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('college/'.$collegeUrl.'/manageAssignmentTopic');
    }

    protected function delete($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $topicId = InputSanitise::inputInt($request->get('topic_id'));
        $topic = AssignmentTopic::find($topicId);
        if(is_object($topic)){
            DB::beginTransaction();
            try
            {
                $loginUser = Auth::user();
                if($loginUser->id == $topic->lecturer_id || (User::Hod == $loginUser->user_type || User::Directore == $loginUser->user_type)){
                    $assignments = AssignmentQuestion::where('lecturer_id',$loginUser->id)
                                ->where('assignment_topic_id',$topic->id)->get();
                    if(is_object($assignments) && false == $assignments->isEmpty()){
                        foreach($assignments as $assignment){
                            $answers = AssignmentAnswer::where('assignment_question_id', $assignment->id)->where('lecturer_id',$loginUser->id)->get();
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
                    $topic->delete();
                    DB::commit();
                    return Redirect::to('college/'.$collegeUrl.'/manageAssignmentTopic')->with('message', 'Assignment Topic deleted successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('college/'.$collegeUrl.'/manageAssignmentTopic');
    }

    protected function isAssignmentTopicExist(Request $request){
        return AssignmentTopic::isAssignmentTopicExist($request);
    }

    protected function getAssignmentTopicsByDeptIdByYear(Request $request){
        $loginUser = Auth::user();
        $allCollegeDepts = [];
        $collegeDepts = CollegeDept::getDepartmentsByCollegeId($loginUser->college_id);
        if(is_object($collegeDepts) && false == $collegeDepts->isEmpty()){
            foreach($collegeDepts as $collegeDept){
                $allCollegeDepts[$collegeDept->id] = $collegeDept->name;
            }
        }
        $result['depts'] = $allCollegeDepts;
        $result['topics'] = AssignmentTopic::getAssignmentTopicsByDeptIdByYear($request);
        return $result;
    }
}
