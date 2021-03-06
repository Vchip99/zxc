<?php

namespace App\Http\Controllers\Client\OnlineCourse;

use Illuminate\Http\Request;
use App\Http\Controllers\Client\ClientBaseController;
use Redirect;
use Validator, Session, Auth, DB,LRedis;
use App\Libraries\InputSanitise;
use App\Models\ClientOnlineCategory;
use App\Models\Client;

class ClientOnlineCategoryController extends ClientBaseController
{
    /**
     *  check admin have permission or not, if not redirect to home
     */
	public function __construct(Request $request) {
        parent::__construct($request);
        // $this->middleware('client');
    }

	/**
     * Define your validation rules in a property in
     * the controller to reuse the rules.
     */
    protected $validateCategory = [
        'category' => 'required|string'
    ];

    /**
     *  show list of course category
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
    	$categories = ClientOnlineCategory::showCategories($request);
    	return view('client.onlineCourse.category.list', compact('categories', 'subdomainName','loginUser'));
    }

    /**
     *  show create course category UI
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
		$category = new ClientOnlineCategory;
		return view('client.onlineCourse.category.create', compact('category', 'subdomainName','loginUser'));
    }

    /**
     *  store course category
     */
    protected function store($subdomainName,Request $request){
        $v = Validator::make($request->all(), $this->validateCategory);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $category = ClientOnlineCategory::addOrUpdateOnlineCategory($request);
            if(is_object($category)){
                DB::connection('mysql2')->commit();
                return Redirect::to('manageOnlineCategory')->with('message', 'category created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('manageOnlineCategory');
    }

    /**
     *  edit course category
     */
    protected function edit($subdomainName, $id,Request $request){
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
    		$category = ClientOnlineCategory::find($id);
    		if(is_object($category)){
    			return view('client.onlineCourse.category.create', compact('category', 'subdomainName','loginUser'));
    		}
    	}
    	return Redirect::to('manageOnlineCategory');
    }

    /**
     *  update course category
     */
    protected function update($subdomainName,Request $request){
        $v = Validator::make($request->all(), $this->validateCategory);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
    	$categoryId = InputSanitise::inputInt($request->get('category_id'));
    	if(isset($categoryId)){
            DB::connection('mysql2')->beginTransaction();
            try
            {
                $category = ClientOnlineCategory::addOrUpdateOnlineCategory($request, true);
                if(is_object($category)){
                    DB::connection('mysql2')->commit();
                    return Redirect::to('manageOnlineCategory')->with('message', 'category updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
                return redirect()->back()->withErrors('something went wrong.');
            }
    	}
    	return Redirect::to('manageOnlineCategory');
    }

    /**
     *  delete course category
     */
    protected function delete($subdomainName,Request $request){
    	$categoryId = InputSanitise::inputInt($request->get('category_id'));
    	if(isset($categoryId)){
    		$courseCategory = ClientOnlineCategory::find($categoryId);
    		if(is_object($courseCategory)){
                $loginUser = InputSanitise::getLoginUserByGuardForClient();
                if($courseCategory->created_by > 0 && $loginUser->id != $courseCategory->created_by){
                    return Redirect::to('manageOnlineCategory');
                }
                if('clientuser' == InputSanitise::getCurrentGuard() && 2 != $loginUser->user_type){
                    return Redirect::to('manageOnlineCategory');
                }
                DB::connection('mysql2')->beginTransaction();
                try
                {
                    $clientName = $subdomainName;
                    if(is_object($courseCategory->subcategories) && false == $courseCategory->subcategories->isEmpty()){
                        foreach($courseCategory->subcategories as $courseSubcategory){
                            if(true == is_object($courseSubcategory->courses) && false == $courseSubcategory->courses->isEmpty()){
                                    foreach($courseSubcategory->courses as $course){
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
                                    }
                                }
                            $courseSubcategory->delete();
                        }
                    }
        			$courseCategory->delete();
                    DB::connection('mysql2')->commit();
        			return Redirect::to('manageOnlineCategory')->with('message', 'category deleted successfully!');
                }
                catch(\Exception $e)
                {
                    DB::connection('mysql2')->rollback();
                    return redirect()->back()->withErrors('something went wrong.');
                }
    		}
    	}
    	return Redirect::to('manageOnlineCategory');
    }

    protected function isClientCourseCategoryExist(Request $request){
        return ClientOnlineCategory::isClientCourseCategoryExist($request);
    }
}
