<?php

namespace App\Http\Controllers\Client\OnlineCourse;

use Illuminate\Http\Request;
use App\Http\Controllers\Client\ClientBaseController;
use Redirect;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;
use App\Models\ClientOnlineCategory;
use App\Models\Client;
use App\Models\ClientInstituteCourse;

class ClientOnlineCategoryController extends ClientBaseController
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
    protected $validateCategory = [
        'institute_course' => 'required|integer',
        'category' => 'required|string',
    ];

    /**
     *  show list of course category
     */
    protected function show(Request $request){
        $coursePermission = InputSanitise::checkModulePermission($request, 'course');
        if('false' == $coursePermission){
            return Redirect::to('manageClientHome');
        }

    	$categories = ClientOnlineCategory::showCategories($request);
    	return view('client.onlineCourse.category.list', compact('categories'));
    }

    /**
     *  show create course category UI
     */
    protected function create(){
        $clientId = Auth::guard('client')->user()->id;
        $instituteCourses = ClientInstituteCourse::where('client_id', $clientId)->get();
		$category = new ClientOnlineCategory;
		return view('client.onlineCourse.category.create', compact('category', 'instituteCourses'));
    }

    /**
     *  store course category
     */
    protected function store(Request $request){
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
    protected function edit($subdomain, $id){
        $id = InputSanitise::inputInt(json_decode($id));
    	if(isset($id)){
    		$category = ClientOnlineCategory::find($id);

    		if(is_object($category)){
                $instituteCourses = ClientInstituteCourse::where('client_id', $category->client_id)->get();
    			return view('client.onlineCourse.category.create', compact('category', 'instituteCourses'));
    		}
    	}
    	return Redirect::to('manageOnlineCategory');
    }

    /**
     *  update course category
     */
    protected function update(Request $request){
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
    protected function delete(Request $request){
    	$categoryId = InputSanitise::inputInt($request->get('category_id'));
    	if(isset($categoryId)){
    		$courseCategory = ClientOnlineCategory::find($categoryId);
    		if(is_object($courseCategory)){
                DB::connection('mysql2')->beginTransaction();
                try
                {
                    if(is_object($courseCategory->subcategories) && false == $courseCategory->subcategories->isEmpty()){
                        foreach($courseCategory->subcategories as $courseSubcategory){
                            if(true == is_object($courseSubcategory->courses) && false == $courseSubcategory->courses->isEmpty()){
                                    foreach($courseSubcategory->courses as $course){
                                        if(true == is_object($course->videos) && false == $course->videos->isEmpty()){
                                            foreach($course->videos as $video){
                                                $video->deleteCommantsAndSubComments();
                                                $video->delete();
                                            }
                                        }
                                        $course->deleteRegisteredOnlineCourses();
                                        $course->deleteCourseImageFolder($request);
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

}