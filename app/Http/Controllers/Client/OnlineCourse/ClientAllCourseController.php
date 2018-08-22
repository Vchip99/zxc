<?php

namespace App\Http\Controllers\Client\OnlineCourse;

use Illuminate\Http\Request;
use App\Http\Controllers\Client\ClientBaseController;
use Redirect;
use Validator, Session, Auth, DB,LRedis;
use App\Libraries\InputSanitise;
use App\Models\ClientOnlineCategory;
use App\Models\ClientOnlineSubCategory;
use App\Models\ClientOnlineCourse;
use App\Models\Client;

class ClientAllCourseController extends ClientBaseController
{
    /**
     *  check admin have permission or not, if not redirect to home
     */
	public function __construct(Request $request) {

        parent::__construct($request);
    }

	/**
     * Define your validation rules in a property in
     * the controller to reuse the rules.
     */
    protected $validateCategory = [
        'category' => 'required|string'
    ];

    protected $validateSubcategory = [
        'category' => 'required|integer',
        'subcategory' => 'required|string',
    ];

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

    /**
     *  show list of course category
     */
    protected function showAll($subdomainName,Request $request){
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
    	return view('client.onlineCourse.course_all', compact('categories', 'subdomainName','loginUser'));
    }

    /**
     *  store course category
     */
    protected function storeCategory($subdomainName,Request $request){
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
                return Redirect::to('manageAllCourse')->with('message', 'category created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('manageAllCourse');
    }

    /**
     *  store sub category
     */
    protected function storeSubCategory($subdomainName,Request $request){
        $v = Validator::make($request->all(), $this->validateSubcategory);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $subcategory = ClientOnlineSubCategory::addOrUpdateClientOnlineSubCategory($request);
            if(is_object($subcategory)){
                DB::connection('mysql2')->commit();
                return Redirect::to('manageAllCourse')->with('message', 'Sub category created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('manageAllCourse');
    }

     /**
     *  store course
     */
    protected function storeCourse($subdomainName, Request $request){
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
                return Redirect::to('manageAllCourse')->with('message', 'Course created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('manageAllCourse');
    }

}
