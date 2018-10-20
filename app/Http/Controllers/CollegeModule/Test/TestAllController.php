<?php

namespace App\Http\Controllers\CollegeModule\Test;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use App\Models\CollegeCategory;
use App\Models\TestSubCategory;
use App\Models\TestSubject;
use App\Models\TestSubjectPaper;
use App\Models\User;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;

class TestAllController extends Controller
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
    protected $validateCreateCategory = [
        'category' => 'required|string',
    ];

    protected $validateCreateSubcategory = [
        'category' => 'required',
        'name' => 'required',
        'image_path' => 'required',
    ];

    protected $validateCreateSubject = [
        'category' => 'required|integer',
        'subcategory' => 'required|integer',
        'name' => 'required|string',
    ];

    protected $validatePaper = [
        'category' => 'required|integer',
        'subcategory' => 'required|integer',
        'subject' => 'required|integer',
        'name' => 'required|string',
        'date_to_active' => 'required',
        'date_to_inactive' => 'required',
    ];

    /**
     * show UI for create category
     */
    protected function showAll($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $loginUser = Auth::guard('web')->user();
        // $testCategories = TestCategory::getTestCategoriesByCollegeIdByDeptIdWithPagination($loginUser->college_id,$loginUser->college_dept_id);
        $testCategories = CollegeCategory::getCollegeCategoriesByCollegeIdByDeptId($loginUser->college_id);
    	return view('collegeModule.test.testAll.create', compact('testCategories'));
    }

    /**
     *  store category
     */
    protected function storeCategory($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $v = Validator::make($request->all(), $this->validateCreateCategory);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        // InputSanitise::deleteCacheByString('vchip:tests*');
        DB::beginTransaction();
        try
        {
             $category = CollegeCategory::addOrUpdateCollegeCategory($request);
            if(is_object($category)){
                DB::commit();
                return Redirect::to('college/'.$collegeUrl.'/manageTestAll')->with('message', 'Category created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
		return Redirect::to('college/'.$collegeUrl.'/manageTestAll');
    }

    /**
     *  store sub category
     */
    protected function storeSubCategory($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $v = Validator::make($request->all(), $this->validateCreateSubcategory);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        // InputSanitise::deleteCacheByString('vchip:tests*');
        DB::beginTransaction();
        try
        {
            $subcategory = TestSubCategory::addOrUpdateSubCategory($request);
            if(is_object($subcategory)){
                DB::commit();
                return Redirect::to('college/'.$collegeUrl.'/manageTestAll')->with('message', 'Sub Category created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('college/'.$collegeUrl.'/manageTestAll');
    }

    /**
     *  store subject
     */
    protected function storeSubject($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $v = Validator::make($request->all(), $this->validateCreateSubject);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        // InputSanitise::deleteCacheByString('vchip:tests*');
        DB::beginTransaction();
        try
        {
            $testSubject = TestSubject::addOrUpdateSubject($request);
            if(is_object($testSubject)){
                DB::commit();
                return Redirect::to('college/'.$collegeUrl.'/manageTestAll')->with('message', 'Subject created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return back()->withErrors('something went wrong.');
        }
        return Redirect::to('college/'.$collegeUrl.'/manageTestAll');
    }

    /**
     *  store paper
     */
    protected function storePaper($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $v = Validator::make($request->all(), $this->validatePaper);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        // InputSanitise::deleteCacheByString('vchip:tests*');
        DB::beginTransaction();
        try
        {
            $paper = TestSubjectPaper::addOrUpdateTestSubjectPaper($request);
            if(is_object($paper)){
                DB::commit();
                return Redirect::to('college/'.$collegeUrl.'/manageTestAll')->with('message', 'Paper created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return back()->withErrors('something went wrong.');
        }
        return Redirect::to('college/'.$collegeUrl.'/manageTestAll');
    }
}
