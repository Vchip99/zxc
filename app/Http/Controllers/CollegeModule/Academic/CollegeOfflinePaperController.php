<?php

namespace App\Http\Controllers\CollegeModule\Academic;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Auth,Hash,DB, Redirect,Session,Validator,Input;
use App\Models\CollegeOfflinePaper;
use App\Models\CollegeSubject;
use App\Models\AssignmentAnswer;
use App\Models\AssignmentQuestion;
use App\Models\CollegeDept;
use App\Models\User;
use App\Models\CollegeOfflinePaperMarks;
use App\Models\College;
use App\Libraries\InputSanitise;

class CollegeOfflinePaperController extends Controller
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
    protected $validateCreateOfflinePaper = [
        'subject' => 'required',
        'department' => 'required',
        'year' => 'required',
        'paper' => 'required',
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
    	   $papers = CollegeOfflinePaper::getCollegeOfflinePapersByCollegeIdByAssignedDeptsWithPagination($loginUser->college_id);
        } else {
            $papers = CollegeOfflinePaper::getCollegeOfflinePapersByCollegeIdWithPagination($loginUser->college_id);
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
        $collegeDepts = CollegeDept::getDepartmentsByCollegeId($loginUser->college_id);
        if(is_object($collegeDepts) && false == $collegeDepts->isEmpty()){
            foreach($collegeDepts as $collegeDept){
                $allCollegeDepts[$collegeDept->id] = $collegeDept->name;
            }
        }
    	return view('collegeModule.offlinePaper.list', compact('papers','allSubjects','allCollegeDepts'));
    }

    /**
     *  show create UI for paper
     */
    protected function create($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $collegeDepts = [];
        $years = [];
        $loginUser = Auth::user();
    	if(User::Hod == $loginUser->user_type || User::Lecturer == $loginUser->user_type){
            $subjects = CollegeSubject::getCollegeSubjectByCollegeIdByAssignedDeptsByUser($loginUser->college_id);
        } else {
            $subjects = CollegeSubject::getCollegeSubjectByCollegeIdByUserId($loginUser->college_id,$loginUser->id);
        }
    	$paper = new CollegeOfflinePaper;
    	return view('collegeModule.offlinePaper.create', compact('subjects','paper','years','collegeDepts'));
    }

    /**
     *  store paper
     */
    protected function store($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $v = Validator::make($request->all(), $this->validateCreateOfflinePaper);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::beginTransaction();
        try
        {
            $paper = CollegeOfflinePaper::addOrUpdateCollegeOfflinePaper($request);
            if(is_object($paper)){
                DB::commit();
                return Redirect::to('college/'.$collegeUrl.'/manageCollegeOfflinePaper')->with('message', 'Offline Paper created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
		return Redirect::to('college/'.$collegeUrl.'/manageCollegeOfflinePaper');
    }


    /**
     * edit paper
     */
    protected function edit($collegeUrl,$id,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
    	$paperId = InputSanitise::inputInt(json_decode($id));
    	if(isset($paperId)){
    		$paper = CollegeOfflinePaper::find($paperId);
    		if(is_object($paper)){
                $loginUser = Auth::user();
                if($loginUser->id == $paper->created_by || (User::Hod == $loginUser->user_type || User::Directore == $loginUser->user_type)){
                    $collegeDepts = [];
                    $years = [];
                    if(User::Hod == $loginUser->user_type || User::Lecturer == $loginUser->user_type){
                        $subjects = CollegeSubject::getCollegeSubjectByCollegeIdByAssignedDeptsForList($loginUser->college_id);
                    } else {
                        $subjects = CollegeSubject::getCollegeSubjectByCollegeId($loginUser->college_id);
                    }
                    $collegeSubject = CollegeSubject::getCollegeDepartmentsBySubjectId($paper->college_subject_id);
                    if(is_object($collegeSubject)){
                        $deptIds =  explode(',',  $collegeSubject->college_dept_ids);
                        $years =  explode(',',  $collegeSubject->years);
                        if(count($deptIds) > 0){
                            $collegeDepts = CollegeDept::find($deptIds);
                        }
                    }
        			return view('collegeModule.offlinePaper.create', compact('subjects','paper','years','collegeDepts'));
                }
    		}
    	}
		return Redirect::to('college/'.$collegeUrl.'/manageCollegeOfflinePaper');
    }

    /**
     * update paper
     */
    protected function update($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $v = Validator::make($request->all(), $this->validateCreateOfflinePaper);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        $paperId = InputSanitise::inputInt($request->get('paper_id'));
        if(isset($paperId)){
            DB::beginTransaction();
            try
            {
                $paper = CollegeOfflinePaper::addOrUpdateCollegeOfflinePaper($request, true);
                if(is_object($paper)){
                    DB::commit();
                    return Redirect::to('college/'.$collegeUrl.'/manageCollegeOfflinePaper')->with('message', 'Offline Paper updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('college/'.$collegeUrl.'/manageCollegeOfflinePaper');
    }

    protected function delete($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $paperId = InputSanitise::inputInt($request->get('paper_id'));
        $paper = CollegeOfflinePaper::find($paperId);
        if(is_object($paper)){
            DB::beginTransaction();
            try
            {
                $loginUser = Auth::user();
                if($loginUser->id == $paper->created_by || (User::Hod == $loginUser->user_type || User::Directore == $loginUser->user_type)){
                    $offlinePaperMarks = CollegeOfflinePaperMarks::getOfflinePaperMarksByPaperId($paper->id);
                    if(is_object($offlinePaperMarks) && false == $offlinePaperMarks->isEmpty()){
                        foreach($offlinePaperMarks as $offlinePaperMark){
                            $offlinePaperMark->delete();
                        }
                    }
                    $paper->delete();
                    DB::commit();
                    return Redirect::to('college/'.$collegeUrl.'/manageCollegeOfflinePaper')->with('message', 'Offline Paper deleted successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('college/'.$collegeUrl.'/manageCollegeOfflinePaper');
    }

    protected function isCollegeOfflinePaperExist(Request $request){
        return CollegeOfflinePaper::isCollegeOfflinePaperExist($request);
    }

    protected function manageCollegeOfflineExam($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $loginUser = Auth::user();
        if(User::Hod == $loginUser->user_type || User::Lecturer == $loginUser->user_type){
            $subjects = CollegeSubject::getCollegeSubjectByCollegeIdByAssignedDeptsByUser($loginUser->college_id);
        } else {
            $subjects = CollegeSubject::getCollegeSubjectByCollegeIdByUserId($loginUser->college_id,$loginUser->id);
        }
        return view('collegeModule.offlinePaper.offlinePaperMarks', compact('subjects'));
    }

    protected function getCollegeOfflinePapersBySubjectId(Request $request){
        return CollegeOfflinePaper::getCollegeOfflinePapersBySubjectId($request);
    }

    protected function getCollegeStudentsAndMarksBySubjectIdByPaperId(Request $request){
        $paperId   = InputSanitise::inputInt($request->get('paper_id'));
        $paper = CollegeOfflinePaper::find($paperId);
        if(!is_object($paper)){
            return;
        }
        $result['collegeStudents'] = User::getCollegeStudentsByPaperIdByDeptIdByYear($paperId,$paper->college_dept_id,$paper->year);
        $paperMarks = CollegeOfflinePaperMarks::getOfflinePaperMarksBySubjectIdByPaperId($request);
        $result['studentMarks'] = [];
        if(is_object($paperMarks) && false == $paperMarks->isEmpty()){
            foreach($paperMarks as $paperMark){
                $result['studentMarks'][$paperMark->user_id] = $paperMark;
            }
        }
        return $result;
    }

    protected function assignCollegeOfflinePaperMarks($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        DB::beginTransaction();
        try
        {
            $result = CollegeOfflinePaperMarks::assignCollegeOfflinePaperMarks($request);
            if('true' == $result){
                $presentStudentsMark = [];
                $paperId   = InputSanitise::inputInt($request->get('paper'));
                $subjectId = InputSanitise::inputInt($request->get('subject'));
                $totalMarks   = InputSanitise::inputInt($request->get('total_marks'));
                $studentMarks = $request->except('_token','paper','subject','total_marks');
                if(count($studentMarks) > 0){
                    foreach($studentMarks as $studentId => $studentMark){
                        if(!empty($studentMark)){
                            $presentStudentsMark[$studentId] = $studentMark;
                        }
                    }
                }
                if(count($presentStudentsMark) > 0){
                    $subject = CollegeSubject::find($subjectId);
                    $paper = CollegeOfflinePaper::find($paperId);
                    $college = College::whereNotNull('url')->where('url',$collegeUrl)->where('id', Auth::user()->college_id)->first();
                    if(is_object($college) && 1 == $college->offline_exam_sms && is_object($paper) && is_object($subject)){
                        InputSanitise::sendCollegeOfflinePaperMarkSms($presentStudentsMark,$paper->name,$totalMarks,$subject->name,$college);
                    }
                }
                DB::commit();
                return Redirect::to('college/'.$collegeUrl.'/manageCollegeOfflineExam')->with('message', 'Assign marks to student successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return back()->withErrors('something went wrong.');
        }
        return Redirect::to('college/'.$collegeUrl.'/manageCollegeOfflineExam');
    }

    protected function getCollegeOfflinePapersByDeptIdByYear(Request $request){
        $loginUser = Auth::user();
        $allCollegeDepts = [];
        $collegeDepts = CollegeDept::getDepartmentsByCollegeId($loginUser->college_id);
        if(is_object($collegeDepts) && false == $collegeDepts->isEmpty()){
            foreach($collegeDepts as $collegeDept){
                $allCollegeDepts[$collegeDept->id] = $collegeDept->name;
            }
        }
        $result['depts'] = $allCollegeDepts;
        $result['papers'] = CollegeOfflinePaper::getCollegeOfflinePapersByDeptIdByYear($request);
        return $result;
    }
}
