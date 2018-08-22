<?php

namespace App\Http\Controllers\Client\OnlineCourse;

use Illuminate\Http\Request;
use App\Http\Controllers\Client\ClientBaseController;
use Redirect,Validator, Session, Auth, DB;
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
        // $this->middleware('client');
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
    protected function show($subdomainName,Request $request){
        if(false == InputSanitise::checkDomain($request)){
            return Redirect::to('/');
        }
        if(false == InputSanitise::getCurrentGuard()){
            return Redirect::to('/');
        }
        $loginUser = InputSanitise::getLoginUserByGuardForClient();
        if(!is_object($loginUser)){
            return Redirect::to('/');
        } elseif(is_object($loginUser) && 'clientuser' == InputSanitise::getCurrentGuard() && 2 != $loginUser->user_type){
            return Redirect::to('/');
        }
    	$courses = ClientOnlineCourse::showCourses($request);
    	return view('client.onlineCourse.course.list', compact('courses', 'subdomainName','loginUser'));
    }

    /**
     *  show create course UI
     */
    protected function create($subdomainName,Request $request){
        if(false == InputSanitise::checkDomain($request)){
            return Redirect::to('/');
        }
        if(false == InputSanitise::getCurrentGuard()){
            return Redirect::to('/');
        }
        $loginUser = InputSanitise::getLoginUserByGuardForClient();
        if(!is_object($loginUser)){
            return Redirect::to('/');
        } elseif(is_object($loginUser) && 'clientuser' == InputSanitise::getCurrentGuard() && 2 != $loginUser->user_type){
            return Redirect::to('/');
        }
        $resultArr = InputSanitise::getClientIdAndCretedBy();
        $clientId = $resultArr[0];
        $categories   = ClientOnlineCategory::where('client_id', $clientId)->get();
		$subCategories = new ClientOnlineSubCategory;
		$course= new ClientOnlineCourse;
		return view('client.onlineCourse.course.create', compact('categories','subCategories','course', 'subdomainName','loginUser'));
    }

    /**
     *  store course
     */
    protected function store($subdomainName, Request $request){
        $v = Validator::make($request->all(), $this->validateCourse);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $course = ClientOnlineCourse::addOrUpdateCourse($subdomainName,$request);
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
    protected function edit($subdomainName, $id, Request $request){
        if(false == InputSanitise::checkDomain($request)){
            return Redirect::to('/');
        }
        if(false == InputSanitise::getCurrentGuard()){
            return Redirect::to('/');
        }
        $loginUser = InputSanitise::getLoginUserByGuardForClient();
        if(!is_object($loginUser)){
            return Redirect::to('/');
        } elseif(is_object($loginUser) && 'clientuser' == InputSanitise::getCurrentGuard() && 2 != $loginUser->user_type){
            return Redirect::to('/');
        }
    	$id = InputSanitise::inputInt(json_decode($id));
    	if(isset($id)){
    		$course = ClientOnlineCourse::find($id);
    		if(is_object($course)){
    			$categories   = ClientOnlineCategory::showCategories($request);
				$subCategories = ClientOnlineSubCategory::getOnlineSubCategoriesByCategoryId($course->category_id, $request);
				return view('client.onlineCourse.course.create', compact('instituteCourses','categories','subCategories','course', 'subdomainName','loginUser'));
    		}
    	}
    }

    /**
     *  update course
     */
    protected function update($subdomainName,Request $request){
        $v = Validator::make($request->all(), $this->validateUpdateCourse);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $course = ClientOnlineCourse::addOrUpdateCourse($subdomainName,$request, true);
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
    protected function delete($subdomainName,Request $request){
    	$courseId = InputSanitise::inputInt($request->get('course_id'));
    	if(isset($courseId)){
    		$course = ClientOnlineCourse::find($courseId);
    		if(is_object($course)){
                DB::connection('mysql2')->beginTransaction();
                try
                {
                    $loginUser = InputSanitise::getLoginUserByGuardForClient();
                    if($course->created_by > 0 && $loginUser->id != $course->created_by){
                        return Redirect::to('manageOnlineCourse');
                    }
                    if('clientuser' == InputSanitise::getCurrentGuard() && 2 != $loginUser->user_type){
                        return Redirect::to('manageOnlineCourse');
                    }
                    $clientName = $subdomainName;
                    if(true == is_object($course->videos) && false == $course->videos->isEmpty()){
                        foreach($course->videos as $video){
                            $video->deleteCommantsAndSubComments();
                            if(true == preg_match('/clientCourseVideos/',$video->video_path)){
                                $courseVideoFolder = "clientCourseVideos/".$clientName."/".$video->course_id."/".$video->id;
                                if(is_dir($courseVideoFolder)){
                                    InputSanitise::delFolder($courseVideoFolder);
                                }
                            }
                            $video->delete();
                        }
                    }
                    $course->deleteRegisteredOnlineCourses();
                    $course->deleteCourseImageFolder($request);
                    $courseVideoFolder = "clientCourseVideos/".$clientName."/".$course->id;
                    if(is_dir($courseVideoFolder)){
                        InputSanitise::delFolder($courseVideoFolder);
                    }
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

    protected function getOnlineCourseByCatIdBySubCatIdForClient(Request $request){
        $subCategoryId = InputSanitise::inputInt($request->get('sub_category_id'));
        $categoryId = InputSanitise::inputInt($request->get('category'));
        return ClientOnlineCourse::getOnlineCourseByCatIdBySubCatIdForClient($categoryId,$subCategoryId);
    }

    protected function isClientOnlineCourseExist(Request $request){
        return ClientOnlineCourse::isClientOnlineCourseExist($request);
    }
}