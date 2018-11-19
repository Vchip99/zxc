<?php
namespace App\Http\Controllers\CollegeModule\Academic;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use App\Models\User;
use App\Models\CollegeHoliday;
use App\Models\College;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;

class CollegeHolidayController extends Controller
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
    protected $validateCollegeHoliday = [
        'date' => 'required',
        'message' => 'required',
    ];

    /**
     *  show list of holiday
     */
    protected function show($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $allCollegeDepts = [];
        $teacherNames = [];
        $loginUser = Auth::user();
        $collegeHolidays = CollegeHoliday::getCollegeHolidaysByCollegeIdWithPagination($loginUser->college_id);
        $collegeTeachers = User::getCollegeUsersByUserTypes([User::Hod,User::Directore,User::TNP]);
        if(is_object($collegeTeachers) && false == $collegeTeachers->isEmpty()){
            foreach($collegeTeachers as $collegeTeacher){
                $teacherNames[$collegeTeacher->id] = $collegeTeacher->name;
            }
        }
    	return view('collegeModule.holiday.list', compact('collegeHolidays','teacherNames'));
    }

    /**
     *  show create holiday  UI
     */
    protected function create($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
		$collegeHoliday = new CollegeHoliday;
		return view('collegeModule.holiday.create', compact('collegeHoliday'));
    }

    /**
     *  store holiday
     */
    protected function store($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
    	$v = Validator::make($request->all(), $this->validateCollegeHoliday);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::beginTransaction();
        try
        {
            $collegeHoliday = CollegeHoliday::addOrUpdateCollegeHoliday($request);
            if(is_object($collegeHoliday)){
                $this->sendCollegeHolidayMessage($collegeUrl,$collegeHoliday);
                DB::commit();
                return Redirect::to('college/'.$collegeUrl.'/manageCollegeHoliday')->with('message', 'Holiday created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return back()->withErrors('something went wrong.');
        }
        return Redirect::to('college/'.$collegeUrl.'/manageCollegeHoliday');
    }

    /**
     *  edit holiday
     */
    protected function edit($collegeUrl,$id,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
    	$id = InputSanitise::inputInt(json_decode($id));
    	if(isset($id)){
    		$collegeHoliday = CollegeHoliday::find($id);
    		if(is_object($collegeHoliday)){
                return view('collegeModule.holiday.create', compact('collegeHoliday'));
    		}
    	}
    	return Redirect::to('college/'.$collegeUrl.'/manageCollegeHoliday');
    }

    /**
     *  update holiday
     */
    protected function update($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $v = Validator::make($request->all(), $this->validateCollegeHoliday);

        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
    	$holidayId = InputSanitise::inputInt($request->get('holiday_id'));
    	if(isset($holidayId)){
            DB::beginTransaction();
            try
            {
                $collegeHoliday = CollegeHoliday::addOrUpdateCollegeHoliday($request, true);
                if(is_object($collegeHoliday)){
                    $this->sendCollegeHolidayMessage($collegeUrl,$collegeHoliday);
                    DB::commit();
                    return Redirect::to('college/'.$collegeUrl.'/manageCollegeHoliday')->with('message', 'Holiday updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return back()->withErrors('something went wrong.');
            }
    	}
    	return Redirect::to('college/'.$collegeUrl.'/manageCollegeHoliday');
    }

    /**
     *  delete holiday
     */
    protected function delete($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $holidayId = InputSanitise::inputInt($request->get('holiday_id'));
        if(isset($holidayId)){
    		$collegeHoliday = CollegeHoliday::find($holidayId);
    		if(is_object($collegeHoliday)){
                DB::beginTransaction();
                try
                {
                    $loginUser = Auth::guard('web')->user();
                    if($collegeHoliday->created_by == $loginUser->id){
            			$collegeHoliday->delete();
                        DB::commit();
            			return Redirect::to('college/'.$collegeUrl.'/manageCollegeHoliday')->with('message', 'Holiday deleted successfully!');
                    }
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return back()->withErrors('something went wrong.');
                }
    		}
    	}
    	return Redirect::to('college/'.$collegeUrl.'/manageCollegeHoliday');
    }

    protected function isCollegeHolidayExist(Request $request){
        return CollegeHoliday::isCollegeHolidayExist($request);
    }

    /**
     * send sms for notice
     */
    protected function sendCollegeHolidayMessage($collegeUrl,$collegeHoliday){
        $college = College::whereNotNull('url')->where('url',$collegeUrl)->where('id', Auth::user()->college_id)->first();
        if(is_object($college) && !empty($college->holiday_sms)){
            $holidayValues = explode(',', $college->holiday_sms);
            if(is_object($college) && (in_array(1, $holidayValues) || in_array(2, $holidayValues) || in_array(3, $holidayValues))){
                InputSanitise::sendCollegeHolidaySms($collegeHoliday,$college);
            }
        }
        return;
    }
}