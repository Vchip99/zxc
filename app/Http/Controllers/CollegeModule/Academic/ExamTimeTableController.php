<?php
namespace App\Http\Controllers\CollegeModule\Academic;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use App\Models\User;
use App\Models\CollegeTimeTable;
use App\Models\CollegeDept;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;

class ExamTimeTableController extends Controller
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
    protected $validateCollegeTimeTable = [
        'department' => 'required',
        'year' => 'required',
    ];

    /**
     *  show list of exam tt
     */
    protected function show($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $loginUser = Auth::guard('web')->user();
    	$timeTables = CollegeTimeTable::getExamTimeTablesWithPagination();
    	return view('collegeModule.examTimeTable.list', compact('timeTables'));
    }

    /**
     *  show create exam tt UI
     */
    protected function create($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $assignedDepts = [];
        $loginUser = Auth::guard('web')->user();
        $collegeDepts = CollegeDept::getDepartmentsByCollegeId($loginUser->college_id);
		$timeTable = new CollegeTimeTable;
        if(4 == $loginUser->user_type){
            $assignedDepts = explode(',',$loginUser->assigned_college_depts);
        }
		return view('collegeModule.examTimeTable.create', compact('timeTable','collegeDepts','assignedDepts'));
    }

    /**
     *  store exam tt
     */
    protected function store($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
    	$v = Validator::make($request->all(), $this->validateCollegeTimeTable);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::beginTransaction();
        try
        {
            $timeTable = CollegeTimeTable::addOrUpdateCollegeTimeTable($request);
            if(is_object($timeTable)){
                DB::commit();
                return Redirect::to('college/'.$collegeUrl.'/manageExamTimeTable')->with('message', 'Exam Time Table created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return back()->withErrors('something went wrong.');
        }
        return Redirect::to('college/'.$collegeUrl.'/manageExamTimeTable');
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
    		$timeTable = CollegeTimeTable::find($id);
    		if(is_object($timeTable)){
                $loginUser = Auth::guard('web')->user();
                $collegeDepts = CollegeDept::getDepartmentsByCollegeId($loginUser->college_id);
                return view('collegeModule.examTimeTable.create', compact('timeTable','collegeDepts'));
    		}
    	}
    	return Redirect::to('college/'.$collegeUrl.'/manageExamTimeTable');
    }

    /**
     *  update exam tt
     */
    protected function update($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $v = Validator::make($request->all(), $this->validateCollegeTimeTable);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
    	$timeTableId = InputSanitise::inputInt($request->get('time_table_id'));
    	if(isset($timeTableId)){
            DB::beginTransaction();
            try
            {
                $timeTable = CollegeTimeTable::addOrUpdateCollegeTimeTable($request, true);
                if(is_object($timeTable)){
                    DB::commit();
                    return Redirect::to('college/'.$collegeUrl.'/manageExamTimeTable')->with('message', 'Exam Time Table updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return back()->withErrors('something went wrong.');
            }
    	}
    	return Redirect::to('college/'.$collegeUrl.'/manageExamTimeTable');
    }

    /**
     *  delete exam tt
     */
    protected function delete($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $timeTableId = InputSanitise::inputInt($request->get('time_table_id'));
    	if(isset($timeTableId)){
    		$timeTable = CollegeTimeTable::find($timeTableId);
    		if(is_object($timeTable)){
                DB::beginTransaction();
                try
                {
                    unlink($timeTable->image_path);
        			$timeTable->delete();
                    DB::commit();
        			return Redirect::to('college/'.$collegeUrl.'/manageExamTimeTable')->with('message', 'Exam Time Table deleted successfully!');
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return back()->withErrors('something went wrong.');
                }
    		}
    	}
    	return Redirect::to('college/'.$collegeUrl.'/manageExamTimeTable');
    }

    protected function isExamTimeTableExist(Request $request){
        return CollegeTimeTable::isExamTimeTableExist($request);
    }
}