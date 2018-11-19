<?php
namespace App\Http\Controllers\CollegeModule\Academic;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use App\Models\User;
use App\Models\CollegeNotice;
use App\Models\CollegeDept;
use App\Models\College;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;

class CollegeNoticeController extends Controller
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
    protected $validateCollegeNotice = [
        'department' => 'required',
        'year' => 'required',
        'notice' => 'required',
        'is_emergency' => 'required'
    ];

    /**
     *  show list of notice
     */
    protected function show($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $allCollegeDepts = [];
        $teacherNames = [];
        $loginUser = Auth::user();
        $collegeNotices = CollegeNotice::getCollegeNoticesByCollegeIdByUserWithPagination($loginUser->college_id);
        $collegeDepts = CollegeDept::getDepartmentsByCollegeId($loginUser->college_id);
        if(is_object($collegeDepts) && false == $collegeDepts->isEmpty()){
            foreach($collegeDepts as $collegeDept){
                $allCollegeDepts[$collegeDept->id] = $collegeDept->name;
            }
        }
        $collegeTeachers = User::getCollegeUsersByUserTypes([User::Lecturer,User::Hod,User::Directore,User::TNP]);
        if(is_object($collegeTeachers) && false == $collegeTeachers->isEmpty()){
            foreach($collegeTeachers as $collegeTeacher){
                $teacherNames[$collegeTeacher->id] = $collegeTeacher->name;
            }
        }
    	return view('collegeModule.notice.list', compact('collegeNotices','allCollegeDepts','teacherNames'));
    }

    /**
     *  show create notice  UI
     */
    protected function create($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $collegeDepts = [];
        $loginUser = Auth::guard('web')->user();
		$collegeNotice = new CollegeNotice;
        $years = range(1,4);
        if(User::Lecturer == $loginUser->user_type || User::Hod == $loginUser->user_type){
            $deptIds =  explode(',',  $loginUser->assigned_college_depts);
            if(count($deptIds) > 0){
                $collegeDepts = CollegeDept::find($deptIds);
            }
        } else {
            $collegeDepts = CollegeDept::getDepartmentsByCollegeId($loginUser->college_id);
        }
		return view('collegeModule.notice.create', compact('collegeNotice','collegeDepts','years'));
    }

    /**
     *  store notice
     */
    protected function store($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
    	$v = Validator::make($request->all(), $this->validateCollegeNotice);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::beginTransaction();
        try
        {
            $collegeNotice = CollegeNotice::addOrUpdateCollegeNotice($request);
            if(is_object($collegeNotice)){
                $this->sendCollegeNoticeMessage($collegeUrl,$collegeNotice);
                DB::commit();
                return Redirect::to('college/'.$collegeUrl.'/manageCollegeNotice')->with('message', 'Notice created successfully!');
            }
        }
        catch(\Exception $e)
        {   dd( $e);
            DB::rollback();
            return back()->withErrors('something went wrong.');
        }
        return Redirect::to('college/'.$collegeUrl.'/manageCollegeNotice');
    }

    /**
     *  edit notice
     */
    protected function edit($collegeUrl,$id,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
    	$id = InputSanitise::inputInt(json_decode($id));
    	if(isset($id)){
    		$collegeNotice = CollegeNotice::find($id);
    		if(is_object($collegeNotice)){
                $loginUser = Auth::guard('web')->user();
                $collegeDepts = CollegeDept::getDepartmentsByCollegeId($loginUser->college_id);
                $years = range(1,4);
                return view('collegeModule.notice.create', compact('collegeNotice','collegeDepts','years'));
    		}
    	}
    	return Redirect::to('college/'.$collegeUrl.'/manageCollegeNotice');
    }

    /**
     *  update notice
     */
    protected function update($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $v = Validator::make($request->all(), $this->validateCollegeNotice);

        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
    	$noticeId = InputSanitise::inputInt($request->get('notice_id'));
    	if(isset($noticeId)){
            DB::beginTransaction();
            try
            {
                $collegeNotice = CollegeNotice::addOrUpdateCollegeNotice($request, true);
                if(is_object($collegeNotice)){
                    $this->sendCollegeNoticeMessage($collegeUrl,$collegeNotice);
                    DB::commit();
                    return Redirect::to('college/'.$collegeUrl.'/manageCollegeNotice')->with('message', 'Notice updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return back()->withErrors('something went wrong.');
            }
    	}
    	return Redirect::to('college/'.$collegeUrl.'/manageCollegeNotice');
    }

    /**
     *  delete notice
     */
    protected function delete($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $noticeId = InputSanitise::inputInt($request->get('notice_id'));
        if(isset($noticeId)){
    		$collegeNotice = CollegeNotice::find($noticeId);
    		if(is_object($collegeNotice)){
                DB::beginTransaction();
                try
                {
                    $loginUser = Auth::guard('web')->user();
                    if($collegeNotice->created_by == $loginUser->id){
            			$collegeNotice->delete();
                        DB::commit();
            			return Redirect::to('college/'.$collegeUrl.'/manageCollegeNotice')->with('message', 'Notice deleted successfully!');
                    }
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return back()->withErrors('something went wrong.');
                }
    		}
    	}
    	return Redirect::to('college/'.$collegeUrl.'/manageCollegeNotice');
    }

    /**
     * send sms for notice
     */
    protected function sendCollegeNoticeMessage($collegeUrl,$collegeNotice){
        $college = College::whereNotNull('url')->where('url',$collegeUrl)->where('id', Auth::user()->college_id)->first();
        if(1 == $collegeNotice->is_emergency){
            if(!empty($college->emergency_notice_sms)){
                $noticeValues = explode(',', $college->emergency_notice_sms);
                if(is_object($college) && (in_array(1, $noticeValues) || in_array(2, $noticeValues) || in_array(3, $noticeValues))){
                    InputSanitise::sendCollegeNoticeSms($collegeNotice,$college);
                }
            }
        } else {
            if(!empty($college->notice_sms)){
                $noticeValues = explode(',', $college->notice_sms);
                if(is_object($college) && (in_array(1, $noticeValues) || in_array(2, $noticeValues) || in_array(3, $noticeValues))){
                    InputSanitise::sendCollegeNoticeSms($collegeNotice,$college);
                }
            }
        }
        return;
    }
}