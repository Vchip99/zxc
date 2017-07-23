<?php

namespace App\Http\Controllers\Test;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\College;
use App\Models\CollegeDept;
use Auth,Hash,DB,Redirect,Validator;
use App\Libraries\InputSanitise;

class CollegeInfo extends Controller
{
    public function __construct() {
        $this->middleware('admin');
    }

    protected $validateCollege = [
        'college' => 'required',
        'department_1' => 'required',
    ];

    protected function manageCollegeInfo(){
    	$colleges = College::paginate();
        return view('college.list', compact('colleges'));
    }

    protected function create(){
    	$college = new College;
    	return view('college.create', compact('college'));
    }

    protected function store(Request $request){
    	$v = Validator::make($request->all(), $this->validateCollege);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::beginTransaction();
        try
        {
            $college = College::addOrUpdateCollege($request);
            if(is_object($college)){
	            $collegeDept = CollegeDept::addOrUpdateCollegeDept($request, $college->id);

	            if('true' == $collegeDept){
		            DB::commit();
		            return Redirect::to('admin/manageCollegeInfo')->with('message', 'College Information created successfully!');
		        }
	        }
        }
		catch(\Exception $e)
        {
            DB::rollback();
            return back()->withErrors('something went wrong.');
        }
        return Redirect::to('admin/manageCollegeInfo');
    }

    protected function edit($id){
    	$college = College::find(json_decode($id));
    	return view('college.create', compact('college'));
    }

    protected function update(Request $request){
        $collegeId = InputSanitise::inputInt($request->get('college_id'));

        if(isset($collegeId)){
            DB::beginTransaction();
            try
            {
                $college = College::addOrUpdateCollege($request, true);
                if(is_object($college)){
	                $collegeDept = CollegeDept::addOrUpdateCollegeDept($request, $college->id, true);

	                if('true' == $collegeDept){
	                    DB::commit();
	                    return Redirect::to('admin/manageCollegeInfo')->with('message', 'College Information updated successfully!');
	                }
	            }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('admin/manageCategory');
    }

    protected function delete(Request $request){
    	$collegeId = $request->get('college_id');
    	if(!empty($collegeId)){
    		$college = College::find($collegeId);
    		if(is_object($college)){
    			DB::beginTransaction();
	            try
	            {
	    			if(is_object($college->departments) && false == $college->departments->isEmpty()){
	    				foreach($college->departments as $department){
	    					$department->delete();
	    				}
	    			}
	    			$college->delete();
	    			DB::commit();
                    return Redirect::to('admin/manageCollegeInfo')->with('message', 'College Information deleted successfully!');
	    		}
	            catch(\Exception $e)
	            {
	                DB::rollback();
	                return back()->withErrors('something went wrong.');
	            }
    		}
    	}
    	return Redirect::to('admin/manageCategory');
    }
}
