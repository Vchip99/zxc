<?php
namespace App\Http\Controllers\CollegeModule\Academic;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use App\Models\User;
use App\Models\CollegeClassExam;
use App\Models\CollegeSubject;
use App\Models\CollegeDept;
use App\Models\College;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;

class CollegeClassExamController extends Controller
{
    /**
     *  check admin have permission or not, if not redirect to home
     */
	public function __construct() {
        $this->middleware(function ($request, $next) {
            $loginUser = Auth::guard('web')->user();
            if(is_object($loginUser)){
                return $next($request);
            }
            return Redirect::to('/');
        });
    }

	/**
     * Define your validation rules in a property in
     * the controller to reuse the rules.
     */
    protected $validateCollegeClassExam = [
        'subject' => 'required',
        'department' => 'required',
        'year' => 'required',
        'topic' => 'required',
        'date' => 'required',
        'from_time' => 'required',
        'to_time' => 'required',
    ];

    /**
     *  show list of exam
     */
    protected function show($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $loginUser = Auth::user();
        $collegeClassExams = CollegeClassExam::getCollegeClassExamByCollegeIdByUserWithPagination($loginUser->college_id);
        $subjects = CollegeSubject::getCollegeSubjectByCollegeId($loginUser->college_id);
        if(is_object($subjects) && false == $subjects->isEmpty()){
            foreach($subjects as $subject){
                $allSubjects[$subject->id]['name'] = $subject->name;
                $allSubjects[$subject->id]['college_dept_ids'] = $subject->college_dept_ids;
                $allSubjects[$subject->id]['years'] = $subject->years;
            }
        }
        $collegeDepts = CollegeDept::getDepartmentsByCollegeId($loginUser->college_id);
        if(is_object($collegeDepts) && false == $collegeDepts->isEmpty()){
            foreach($collegeDepts as $collegeDept){
                $allCollegeDepts[$collegeDept->id] = $collegeDept->name;
            }
        }
    	return view('collegeModule.classExam.list', compact('collegeClassExams','allSubjects','allCollegeDepts'));
    }

    /**
     *  show create exam  UI
     */
    protected function create($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $collegeDepts = [];
        $years = [];
        $loginUser = Auth::guard('web')->user();
        $subjects = CollegeSubject::getCollegeSubjectByCollegeIdByUserId($loginUser->college_id,$loginUser->id);
		$collegeClassExam = new CollegeClassExam;
		return view('collegeModule.classExam.create', compact('collegeClassExam','subjects','collegeDepts','years'));
    }

    /**
     *  store exam
     */
    protected function store($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
    	$v = Validator::make($request->all(), $this->validateCollegeClassExam);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::beginTransaction();
        try
        {
            $collegeClassExam = CollegeClassExam::addOrUpdateCollegeClassExam($request);
            if(is_object($collegeClassExam)){
                $this->sendCollegeClassExamMessage($collegeUrl,$collegeClassExam);
                DB::commit();
                return Redirect::to('college/'.$collegeUrl.'/manageCollegeClassExam')->with('message', 'Class Exam created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return back()->withErrors('something went wrong.');
        }
        return Redirect::to('college/'.$collegeUrl.'/manageCollegeClassExam');
    }

    /**
     *  edit exam
     */
    protected function edit($collegeUrl,$id,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
    	$id = InputSanitise::inputInt(json_decode($id));
    	if(isset($id)){
    		$collegeClassExam = CollegeClassExam::find($id);
    		if(is_object($collegeClassExam)){
                $loginUser = Auth::guard('web')->user();
                if($collegeClassExam->created_by == $loginUser->id){
                    $subjects = CollegeSubject::getCollegeSubjectByCollegeIdByUserId($loginUser->college_id,$loginUser->id);
                    $collegeSubject = CollegeSubject::getCollegeDepartmentsBySubjectId($collegeClassExam->college_subject_id);
                        if(is_object($collegeSubject)){
                            $deptIds =  explode(',',  $collegeSubject->college_dept_ids);
                            $years =  explode(',',  $collegeSubject->years);
                            array_push($years, 0);
                            if(count($deptIds) > 0){
                                $collegeDepts = CollegeDept::find($deptIds);
                            }
                        }
                    return view('collegeModule.classExam.create', compact('collegeClassExam','subjects','collegeDepts','years'));
                }
    		}
    	}
    	return Redirect::to('college/'.$collegeUrl.'/manageCollegeClassExam');
    }

    /**
     *  update exam
     */
    protected function update($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $v = Validator::make($request->all(), $this->validateCollegeClassExam);

        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
    	$examId = InputSanitise::inputInt($request->get('exam_id'));
    	if(isset($examId)){
            DB::beginTransaction();
            try
            {
                $collegeClassExam = CollegeClassExam::addOrUpdateCollegeClassExam($request, true);
                if(is_object($collegeClassExam)){
                    $this->sendCollegeClassExamMessage($collegeUrl,$collegeClassExam);
                    DB::commit();
                    return Redirect::to('college/'.$collegeUrl.'/manageCollegeClassExam')->with('message', 'Class Exam updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return back()->withErrors('something went wrong.');
            }
    	}
    	return Redirect::to('college/'.$collegeUrl.'/manageCollegeClassExam');
    }

    /**
     *  delete exam
     */
    protected function delete($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $examId = InputSanitise::inputInt($request->get('exam_id'));
        if(isset($examId)){
    		$collegeClassExam = CollegeClassExam::find($examId);
    		if(is_object($collegeClassExam)){
                DB::beginTransaction();
                try
                {
                    $loginUser = Auth::guard('web')->user();
                    if($collegeClassExam->created_by == $loginUser->id){
            			$collegeClassExam->delete();
                        DB::commit();
            			return Redirect::to('college/'.$collegeUrl.'/manageCollegeClassExam')->with('message', 'Class Exam deleted successfully!');
                    }
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return back()->withErrors('something went wrong.');
                }
    		}
    	}
    	return Redirect::to('college/'.$collegeUrl.'/manageCollegeClassExam');
    }

    /**
     * send sms for class exam
     */
    protected function sendCollegeClassExamMessage($collegeUrl,$collegeClassExam){
        $college = College::whereNotNull('url')->where('url',$collegeUrl)->where('id', Auth::user()->college_id)->first();
        if(is_object($college) && 1 == $college->exam_sms){
            $subject = CollegeSubject::find($collegeClassExam->college_subject_id);
            if(is_object($subject)){
                InputSanitise::sendCollegeClassExamSms($collegeClassExam,$subject->name,$college);
            }
        }
        return;
    }
}