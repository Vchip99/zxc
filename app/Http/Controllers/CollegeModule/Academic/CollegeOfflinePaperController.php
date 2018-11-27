<?php

namespace App\Http\Controllers\CollegeModule\Academic;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Auth,Hash,DB, Redirect,Session,Validator,Input;
use App\Models\CollegeSubject;
use App\Models\AssignmentAnswer;
use App\Models\AssignmentQuestion;
use App\Models\CollegeDept;
use App\Models\User;
use App\Models\CollegeOfflinePaperMarks;
use App\Models\College;
use App\Models\CollegeClassExam;
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

    protected function getCollegeOfflineExamTopicBySubjectIdByDeptByYear(Request $request){
        $subjectId = InputSanitise::inputInt($request->get('subject_id'));
        $departmentId = InputSanitise::inputInt($request->get('department_id'));
        $year = InputSanitise::inputInt($request->get('year'));
        return CollegeClassExam::getCollegeOfflineExamsByCollegeIdByDeptIdByYear($subjectId,$departmentId,$year);
    }

    protected function getCollegeStudentsAndMarksBySubjectIdByDeptByYearByExamId(Request $request){
        $subjectId = InputSanitise::inputInt($request->get('subject_id'));
        $departmentId = InputSanitise::inputInt($request->get('department_id'));
        $year = InputSanitise::inputInt($request->get('year'));
        $examId = InputSanitise::inputInt($request->get('exam_id'));
        $loginUser = Auth::user();

        $result['collegeStudents'] = User::getCollegeStudentsByCollegeIdByDeptIdByYear($loginUser->college_id,$departmentId,$year);
        $paperMarks = CollegeOfflinePaperMarks::getOfflineMarksBySubjectIdByExamId($request);
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
                $paperId   = InputSanitise::inputInt($request->get('topic'));
                $subjectId = InputSanitise::inputInt($request->get('subject'));
                $totalMarks   = InputSanitise::inputInt($request->get('total_marks'));
                $studentMarks = $request->except('_token','topic','subject','year','department','total_marks');
                if(count($studentMarks) > 0){
                    foreach($studentMarks as $studentId => $studentMark){
                        if(!empty($studentMark)){
                            $presentStudentsMark[$studentId] = $studentMark;
                        }
                    }
                }
                if(count($presentStudentsMark) > 0){
                    $subject = CollegeSubject::find($subjectId);
                    $paper = CollegeClassExam::find($paperId);
                    $college = College::whereNotNull('url')->where('url',$collegeUrl)->where('id', Auth::user()->college_id)->first();
                    if(is_object($college) && 1 == $college->offline_exam_sms && is_object($paper) && is_object($subject)){
                        InputSanitise::sendCollegeOfflinePaperMarkSms($presentStudentsMark,$paper->topic,$totalMarks,$subject->name,$college);
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
}
