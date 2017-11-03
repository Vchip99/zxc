<?php

namespace App\Http\Controllers\Client\OnlineCourse;

use Illuminate\Http\Request;
use App\Http\Controllers\Client\ClientBaseController;
use Redirect;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;
use App\Models\ClientOnlineCourse;
use App\Models\ClientOnlineCategory;
use App\Models\ClientOnlineSubCategory;

class ClientOnlineCourseController extends ClientBaseController
{
	/**
     *  check admin have permission or not, if not redirect to admin/home
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
        'category' => 'required|integer',
        'subcategory' => 'required|integer',
        'course' => 'required|string',
        'author' => 'required|string',
        'author_introduction' => 'required|string',
        'description' => 'required|string',
        'price' => 'required',
        'difficulty_level' => 'required|integer',
        'certified' => 'required|integer',
        'release_date' => 'required|date',
        'image_path' => 'required',
    ];

    protected $validateUpdateCourse = [
        'category' => 'required|integer',
        'subcategory' => 'required|integer',
        'course' => 'required|string',
        'author' => 'required|string',
        'author_introduction' => 'required|string',
        'description' => 'required|string',
        'price' => 'required',
        'difficulty_level' => 'required|integer',
        'certified' => 'required|integer',
        'release_date' => 'required|date',
    ];

    /**
     *  show list of courses
     */
    protected function show(Request $request){
    	$courses = ClientOnlineCourse::showCourses($request);
    	return view('client.onlineCourse.course.list', compact('courses'));
    }

    /**
     *  show create course UI
     */
    protected function create(Request $request){
        $clientId = Auth::guard('client')->user()->id;
        $categories   = ClientOnlineCategory::where('client_id', $clientId)->get();
		$subCategories = new ClientOnlineSubCategory;
		$course= new ClientOnlineCourse;

		return view('client.onlineCourse.course.create', compact('categories','subCategories','course'));
    }

    /**
     *  store course
     */
    protected function store(Request $request){
    	$v = Validator::make($request->all(), $this->validateCourse);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $course = ClientOnlineCourse::addOrUpdateCourse($request);
            if(is_object($course)){
                DB::connection('mysql2')->commit();
            	return Redirect::to('manageOnlineCourse')->with('message', 'Course created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('manageOnlineCourse');
    }

    /**
     *  edit course
     */
    protected function edit($subdomain, $id, Request $request){
    	$id = InputSanitise::inputInt(json_decode($id));
    	if(isset($id)){
    		$course = ClientOnlineCourse::find($id);
    		if(is_object($course)){
    			$categories   = ClientOnlineCategory::showCategories($request);
				$subCategories = ClientOnlineSubCategory::getOnlineSubCategoriesByCategoryId($course->category_id, $request);
				return view('client.onlineCourse.course.create', compact('instituteCourses','categories','subCategories','course'));
    		}
    	}
    }

    /**
     *  update course
     */
    protected function update(Request $request){
    	$v = Validator::make($request->all(), $this->validateUpdateCourse);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $course = ClientOnlineCourse::addOrUpdateCourse($request, true);
            if(is_object($course)){
                DB::connection('mysql2')->commit();
            	return Redirect::to('manageOnlineCourse')->with('message', 'Course updated successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('manageOnlineCourse');
    }

    /**
     *  delete course
     */
    protected function delete(Request $request){
    	$courseId = InputSanitise::inputInt($request->get('course_id'));
    	if(isset($courseId)){
    		$course = ClientOnlineCourse::find($courseId);
    		if(is_object($course)){
                DB::connection('mysql2')->beginTransaction();
                try
                {
                    if(true == is_object($course->videos) && false == $course->videos->isEmpty()){
                        foreach($course->videos as $video){
                            $video->deleteCommantsAndSubComments();
                            $video->delete();
                        }
                    }
                    $course->deleteRegisteredOnlineCourses();
                    $course->deleteCourseImageFolder($request);
        			$course->delete();
                    DB::connection('mysql2')->commit();
        			return Redirect::to('manageOnlineCourse')->with('message', 'Course deleted successfully!');
                }
                catch(\Exception $e)
                {
                    DB::connection('mysql2')->rollback();
                    return redirect()->back()->withErrors('something went wrong.');
                }
    		}
    	}
    	return Redirect::to('manageOnlineCourse');
    }

    protected function getOnlineCourseByInstituteCourseId(Request $request){
        return ClientOnlineCourse::getCoursesByClientInstituteCourseId($request);
    }

}