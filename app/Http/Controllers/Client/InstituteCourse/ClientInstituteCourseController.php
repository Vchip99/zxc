<?php

namespace App\Http\Controllers\Client\InstituteCourse;

use Illuminate\Http\Request;
use App\Http\Controllers\Client\ClientBaseController;
use Redirect,Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;
use App\Models\ClientInstituteCourse;
use App\Models\Client;

class ClientInstituteCourseController extends ClientBaseController
{
	/**
     *  check admin have permission or not, if not redirect to home
     */
	public function __construct(Request $request) {
        parent::__construct($request);
        $this->middleware('client');
    }

	/**
     * Define your validation rules in a property in
     * the controller to reuse the rules.
     */
    protected $validateCourse = [
        'course' => 'required|string',
    ];

    /**
     *  show list of course category
     */
   	public function show(Request $request){
   		$clientId = Auth::guard('client')->user()->id;
        $instituteCourses = ClientInstituteCourse::where('client_id', $clientId)->paginate();
        return view('client.instituteCourse.list', compact('instituteCourses'));
    }

    public function create(){
    	$course = new ClientInstituteCourse;
    	return view('client.instituteCourse.create', compact('course'));
    }

    public function store(Request $request){
    	$v = Validator::make($request->all(), $this->validateCourse);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::connection('mysql2')->beginTransaction();
    	try{
	    	$course = ClientInstituteCourse::addOrUpdateInstituteCourse($request);
	    	if(is_object($course)){
	    		DB::connection('mysql2')->commit();
	    		return Redirect::to('manageInstituteCourses')->with('message', 'Institute course created successfully.');
	    	}
	    }
	    catch(\Exception $e){
	    	DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong.');
	    }
    	return Redirect::to('manageInstituteCourses');
    }

    /**
     *  edit institute course
     */
    protected function edit($subdomain, $id){
        $id = InputSanitise::inputInt(json_decode($id));
    	if(isset($id)){
    		$course = ClientInstituteCourse::find($id);

    		if(is_object($course)){
    			return view('client.instituteCourse.create', compact('course'));
    		}
    	}
    	return Redirect::to('manageInstituteCourses');
    }

        /**
     *  update institute category
     */
    protected function update(Request $request){
        $v = Validator::make($request->all(), $this->validateCourse);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }

    	$courseId = InputSanitise::inputInt($request->get('course_id'));
    	if(isset($courseId)){
            DB::connection('mysql2')->beginTransaction();
            try
            {
                $course = ClientInstituteCourse::addOrUpdateInstituteCourse($request, true);
                if(is_object($course)){
                    DB::connection('mysql2')->commit();
                    return Redirect::to('manageInstituteCourses')->with('message', 'Institute course updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
                return redirect()->back()->withErrors('something went wrong.');
            }
    	}
    	return Redirect::to('manageInstituteCourses');
    }

        /**
     *  delete institute course
     */
    protected function delete(Request $request){
    	$courseId = InputSanitise::inputInt($request->get('course_id'));
    	if(isset($courseId)){
    		$instituteCourse = ClientInstituteCourse::find($courseId);
    		if(is_object($instituteCourse)){
                DB::connection('mysql2')->beginTransaction();
                try
                {
        			$instituteCourse->delete();
                    DB::connection('mysql2')->commit();
        			return Redirect::to('manageInstituteCourses')->with('message', 'Institute course deleted successfully!');
                }
                catch(\Exception $e)
                {
                    DB::connection('mysql2')->rollback();
                    return redirect()->back()->withErrors('something went wrong.');
                }
    		}
    	}
    	return Redirect::to('manageInstituteCourses');
    }
}