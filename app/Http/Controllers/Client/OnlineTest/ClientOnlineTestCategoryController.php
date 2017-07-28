<?php

namespace App\Http\Controllers\Client\OnlineTest;

use Illuminate\Http\Request;
use App\Http\Controllers\Client\ClientBaseController;
use Redirect;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;
use App\Models\ClientOnlineTestCategory;
use App\Models\ClientInstituteCourse;

class ClientOnlineTestCategoryController extends ClientBaseController
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
    protected $validateCreateCategory = [
        'institute_course' => 'required|integer',
        'category' => 'required|string',
    ];

    /**
     * show all category
     */
    protected function show(Request $request){
        $coursePermission = InputSanitise::checkModulePermission($request, 'test');
        if('false' == $coursePermission){
            return Redirect::to('manageClientHome');
        }
    	$testCategories = ClientOnlineTestCategory::showCategories($request);
    	return view('client.onlineTest.category.list', compact('testCategories'));
    }

    /**
     * show UI for create category
     */
    protected function create(){
        $clientId = Auth::guard('client')->user()->id;
        $instituteCourses = ClientInstituteCourse::where('client_id', $clientId)->get();
    	$testCategory = new ClientOnlineTestCategory;
    	return view('client.onlineTest.category.create', compact('instituteCourses','testCategory'));
    }

    /**
     *  store category
     */
    protected function store(Request $request){
        $v = Validator::make($request->all(), $this->validateCreateCategory);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $category = ClientOnlineTestCategory::addOrUpdateCategory($request);
            if(is_object($category)){
                DB::connection('mysql2')->commit();
                return Redirect::to('manageOnlineTestCategory')->with('message', 'Category created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
		return Redirect::to('manageOnlineTestCategory');
    }

    /**
     * edit category
     */
    protected function edit($subdomain, $id){
    	$catId = InputSanitise::inputInt(json_decode($id));
    	if(isset($catId)){
    		$testCategory = ClientOnlineTestCategory::find($catId);
    		if(is_object($testCategory)){
                $instituteCourses = ClientInstituteCourse::where('client_id', $testCategory->client_institute_course_id)->get();
    			return view('client.onlineTest.category.create', compact('instituteCourses','testCategory'));
    		}
    	}
		return Redirect::to('manageOnlineTestCategory');
    }

    /**
     * update category
     */
    protected function update(Request $request){
        $v = Validator::make($request->all(), $this->validateCreateCategory);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }

        $categoryId = InputSanitise::inputInt($request->get('category_id'));
        if(isset($categoryId)){
            DB::connection('mysql2')->beginTransaction();
            try
            {
                $category = ClientOnlineTestCategory::addOrUpdateCategory($request, true);
                if(is_object($category)){
                    DB::connection('mysql2')->commit();
                    return Redirect::to('manageOnlineTestCategory')->with('message', 'Category updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
                return redirect()->back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('manageOnlineTestCategory');
    }

    /**
     * delete category
     */
    protected function delete(Request $request){
    	$catId = InputSanitise::inputInt($request->get('category_id'));
    	if(isset($catId)){
    		$category = ClientOnlineTestCategory::find($catId);
    		if(is_object($category)){
                DB::connection('mysql2')->beginTransaction();
                try
                {
                    if(true == is_object($category->subcategories) && false == $category->subcategories->isEmpty()){
                        foreach($category->subcategories as $testSubcategory){
                            if(true == is_object($testSubcategory->subjects) && false == $testSubcategory->subjects->isEmpty()){
                                foreach($testSubcategory->subjects as $testSubject){
                                    if(true == is_object($testSubject->papers) && false == $testSubject->papers->isEmpty()){
                                        foreach($testSubject->papers as $paper){
                                            if(true == is_object($paper->questions) && false == $paper->questions->isEmpty()){
                                                foreach($paper->questions as $question){
                                                    $question->delete();
                                                }
                                            }
                                            $paper->deleteRegisteredPaper();
                                            $paper->delete();
                                        }
                                    }
                                    $testSubject->delete();
                                }
                            }
                            $testSubcategory->deleteSubCategoryImageFolder($request);
                            $testSubcategory->delete();
                        }
                    }
        			$category->delete();
                    DB::connection('mysql2')->commit();
                    return Redirect::to('manageOnlineTestCategory')->with('message', 'Category deleted successfully!');
                }
                catch(\Exception $e)
                {
                    DB::connection('mysql2')->rollback();
                    return redirect()->back()->withErrors('something went wrong.');
                }
            }
        }
		return Redirect::to('manageOnlineTestCategory');
    }

}