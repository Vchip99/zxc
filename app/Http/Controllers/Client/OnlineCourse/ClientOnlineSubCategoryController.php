<?php

namespace App\Http\Controllers\Client\OnlineCourse;

use Illuminate\Http\Request;
use App\Http\Controllers\Client\ClientBaseController;
use Redirect;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;
use App\Models\ClientOnlineCategory;
use App\Models\ClientOnlineSubCategory;
use App\Models\ClientInstituteCourse;

class ClientOnlineSubCategoryController extends ClientBaseController
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
    protected $validateSubcategory = [
        'institute_course' => 'required|integer',
        'category' => 'required|integer',
        'subcategory' => 'required|string',
    ];

    /**
     *  show list of course sub category
     */
    protected function show(Request $request){
        $coursePermission = InputSanitise::checkModulePermission($request, 'course');
        if('false' == $coursePermission){
            return Redirect::to('manageClientHome');
        }
    	$subCategories = ClientOnlineSubCategory::showSubCategories($request);
    	return view('client.onlineCourse.subcategory.list', compact('subCategories'));
    }

    /**
     *  show create category UI
     */
    protected function create(Request $request){
        $clientId = Auth::guard('client')->user()->id;
        $instituteCourses = ClientInstituteCourse::where('client_id', $clientId)->get();
    	$categories = [];
    	$subcategory = new ClientOnlineSubCategory;
    	return view('client.onlineCourse.subcategory.create', compact('instituteCourses','subcategory','categories'));
    }

    /**
     *  store sub category
     */
    protected function store(Request $request){
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
        		return Redirect::to('manageOnlineSubCategory')->with('message', 'Sub category created successfully!');
        	}
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
    	return Redirect::to('manageOnlineSubCategory');

    }

    /**
     *  edit sub category
     */
    protected function edit($subdomain, $id, Request $request){
    	$id = InputSanitise::inputInt(json_decode($id));
    	if(isset($id)){
    		$subcategory = ClientOnlineSubCategory::find($id);
    		if(is_object($subcategory)){
                $categories = ClientOnlineCategory::showCategories($request);
                $instituteCourses = ClientInstituteCourse::where('client_id', $subcategory->client_id)->get();
	    		return view('client.onlineCourse.subcategory.create', compact('instituteCourses','subcategory', 'categories'));
    		}
    	}
    	return Redirect::to('manageOnlineSubCategory');
    }

    /**
     *  update sub category
     */
    protected function update(Request $request){
    	$v = Validator::make($request->all(), $this->validateSubcategory);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }

        $subCategoryId = InputSanitise::inputInt($request->get('subCategory_id'));
    	if(isset($subCategoryId)){
            DB::connection('mysql2')->beginTransaction();
            try
            {
        		$subcategory = ClientOnlineSubCategory::addOrUpdateClientOnlineSubCategory($request, true);
                if(is_object($subcategory)){
                    DB::connection('mysql2')->commit();
                    return Redirect::to('manageOnlineSubCategory')->with('message', ' sub category updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
                return redirect()->back()->withErrors('something went wrong.');
            }
    	}
    	return Redirect::to('manageOnlineSubCategory');
    }

    /**
     *  delete sub category
     */
    protected function delete(Request $request){
    	$subCategoryId = InputSanitise::inputInt($request->get('subCategory_id'));
    	if(isset($subCategoryId)){
    		$courseSubcategory = ClientOnlineSubCategory::find($subCategoryId);
    		if(is_object($courseSubcategory)){
                DB::connection('mysql2')->beginTransaction();
                try
                {
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
                    DB::connection('mysql2')->commit();
        			return Redirect::to('manageOnlineSubCategory')->with('message', ' sub category deleted successfully!');
                }
                catch(\Exception $e)
                {
                    DB::connection('mysql2')->rollback();
                    return redirect()->back()->withErrors('something went wrong.');
                }
    		}
    	}
    	return Redirect::to('manageOnlineSubCategory');
    }

    protected function getOnlineCategories(Request $request){
        return ClientOnlineCategory::getCategoriesByInstituteCourseId($request->get('id'));
    }
}
