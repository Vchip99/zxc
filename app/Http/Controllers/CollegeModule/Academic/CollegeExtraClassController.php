<?php
namespace App\Http\Controllers\CollegeModule\Academic;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use App\Models\User;
use App\Models\CollegeExtraClass;
use App\Models\CollegeSubject;
use App\Models\CollegeDept;
use App\Models\College;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;

class CollegeExtraClassController extends Controller
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
    protected $validateCollegeExtraClass = [
        'subject' => 'required',
        'department' => 'required',
        'year' => 'required',
        'topic' => 'required',
        'date' => 'required',
        'from_time' => 'required',
        'to_time' => 'required',
    ];

    /**
     *  show list of exam tt
     */
    protected function show($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $loginUser = Auth::user();
        $collegeClasses = CollegeExtraClass::getCollegeExtraClassByCollegeIdByUserWithPagination($loginUser->college_id);
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
    	return view('collegeModule.extraClass.list', compact('collegeClasses','allSubjects','allCollegeDepts'));
    }

    /**
     *  show create exam tt UI
     */
    protected function create($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $collegeDepts = [];
        $years = [];
        $loginUser = Auth::guard('web')->user();
        $subjects = CollegeSubject::getCollegeSubjectByCollegeIdByUserId($loginUser->college_id,$loginUser->id);
		$collegeClass = new CollegeExtraClass;
		return view('collegeModule.extraClass.create', compact('collegeClass','subjects','collegeDepts','years'));
    }

    /**
     *  store exam tt
     */
    protected function store($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
    	$v = Validator::make($request->all(), $this->validateCollegeExtraClass);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::beginTransaction();
        try
        {
            $collegeClass = CollegeExtraClass::addOrUpdateCollegeExtraClass($request);
            if(is_object($collegeClass)){
                $this->sendCollegeExtraClassMessage($collegeUrl,$collegeClass);
                DB::commit();
                return Redirect::to('college/'.$collegeUrl.'/manageCollegeExtraClass')->with('message', 'Extra Class created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return back()->withErrors('something went wrong.');
        }
        return Redirect::to('college/'.$collegeUrl.'/manageCollegeExtraClass');
    }

    /**
     *  edit exam tt
     */
    protected function edit($collegeUrl,$id,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
    	$id = InputSanitise::inputInt(json_decode($id));
    	if(isset($id)){
    		$collegeClass = CollegeExtraClass::find($id);
    		if(is_object($collegeClass)){
                $loginUser = Auth::guard('web')->user();
                if($collegeClass->created_by == $loginUser->id){
                    $subjects = CollegeSubject::getCollegeSubjectByCollegeIdByUserId($loginUser->college_id,$loginUser->id);
                    $collegeSubject = CollegeSubject::getCollegeDepartmentsBySubjectId($collegeClass->college_subject_id);
                        if(is_object($collegeSubject)){
                            $deptIds =  explode(',',  $collegeSubject->college_dept_ids);
                            $years =  explode(',',  $collegeSubject->years);
                            array_push($years, 0);
                            if(count($deptIds) > 0){
                                $collegeDepts = CollegeDept::find($deptIds);
                            }
                        }
                    return view('collegeModule.extraClass.create', compact('collegeClass','subjects','collegeDepts','years'));
                }
    		}
    	}
    	return Redirect::to('college/'.$collegeUrl.'/manageCollegeExtraClass');
    }

    /**
     *  update exam tt
     */
    protected function update($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $v = Validator::make($request->all(), $this->validateCollegeExtraClass);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
    	$classId = InputSanitise::inputInt($request->get('class_id'));
    	if(isset($classId)){
            DB::beginTransaction();
            try
            {
                $collegeClass = CollegeExtraClass::addOrUpdateCollegeExtraClass($request, true);
                if(is_object($collegeClass)){
                    $this->sendCollegeExtraClassMessage($collegeUrl,$collegeClass);
                    DB::commit();
                    return Redirect::to('college/'.$collegeUrl.'/manageCollegeExtraClass')->with('message', 'Extra Class updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return back()->withErrors('something went wrong.');
            }
    	}
    	return Redirect::to('college/'.$collegeUrl.'/manageCollegeExtraClass');
    }

    /**
     *  delete exam tt
     */
    protected function delete($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $classId = InputSanitise::inputInt($request->get('class_id'));
        if(isset($classId)){
    		$collegeClass = CollegeExtraClass::find($classId);
    		if(is_object($collegeClass)){
                DB::beginTransaction();
                try
                {
                    $loginUser = Auth::guard('web')->user();
                    if($collegeClass->created_by == $loginUser->id){
            			$collegeClass->delete();
                        DB::commit();
            			return Redirect::to('college/'.$collegeUrl.'/manageCollegeExtraClass')->with('message', 'Extra Class deleted successfully!');
                    }
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return back()->withErrors('something went wrong.');
                }
    		}
    	}
    	return Redirect::to('college/'.$collegeUrl.'/manageCollegeExtraClass');
    }

    /**
     * send sms for extra class
     */
    protected function sendCollegeExtraClassMessage($collegeUrl,$collegeClass){
        $college = College::whereNotNull('url')->where('url',$collegeUrl)->where('id', Auth::user()->college_id)->first();
        if(is_object($college) && 1 == $college->lecture_sms){
            $subject = CollegeSubject::find($collegeClass->college_subject_id);
            if(is_object($subject)){
                InputSanitise::sendCollegeExtraClassSms($collegeClass,$subject->name,$college);
            }
        }
        return;
    }
}