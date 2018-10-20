<?php

namespace App\Http\Controllers\CollegeModule\Test;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use App\Models\TestCategory;
use App\Models\UserSolution;
use App\Models\Score;
use App\Models\PaperSection;
use App\Models\User;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;

class CategoryController extends Controller
{
    /**
     *  check admin have permission or not, if not redirect to home
     */
    public function __construct() {
        $this->middleware(function ($request, $next) {
            $loginUser = Auth::guard('web')->user();
            if(is_object($loginUser) && (User::Lecturer == $loginUser->user_type || User::Hod == $loginUser->user_type)){
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

    /**
     * show all category
     */
    protected function show($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $loginUser = Auth::guard('web')->user();
        $testCategories = TestCategory::getTestCategoriesByCollegeIdByDeptIdWithPagination($loginUser->college_id);
    	return view('collegeModule.test.category.list', compact('testCategories'));
    }

    /**
     * show UI for create category
     */
    protected function create($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
    	$testCategory = new TestCategory;
    	return view('collegeModule.test.category.create', compact('testCategory'));
    }

    /**
     *  store category
     */
    protected function store($collegeUrl,Request $request){
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
            $category = TestCategory::addOrUpdateCategory($request);
            if(is_object($category)){
                DB::commit();
                return Redirect::to('college/'.$collegeUrl.'/manageCategory')->with('message', 'Category created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
		return Redirect::to('college/'.$collegeUrl.'/manageCategory');
    }

    /**
     * edit category
     */
    protected function edit($collegeUrl,$id,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
    	$catId = InputSanitise::inputInt(json_decode($id));
    	if(isset($catId)){
    		$testCategory = TestCategory::find($catId);
    		if(is_object($testCategory)){
                $loginUser = Auth::guard('web')->user();
                if(is_object($loginUser) && $testCategory->college_id == $loginUser->college_id && $testCategory->user_id == $loginUser->id){
            	   return view('collegeModule.test.category.create', compact('testCategory'));
                }
    		}
    	}
		return Redirect::to('college/'.$collegeUrl.'/manageCategory');
    }

    /**
     * update category
     */
    protected function update($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $v = Validator::make($request->all(), $this->validateCreateCategory);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        // InputSanitise::deleteCacheByString('vchip:tests*');
        $categoryId = InputSanitise::inputInt($request->get('category_id'));
        if(isset($categoryId)){
            DB::beginTransaction();
            try
            {
                $category = TestCategory::addOrUpdateCategory($request, true);
                if(is_object($category)){
                    DB::commit();
                    return Redirect::to('college/'.$collegeUrl.'/manageCategory')->with('message', 'Category updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('college/'.$collegeUrl.'/manageCategory');
    }

    /**
     * delete category
     */
    protected function delete($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        // InputSanitise::deleteCacheByString('vchip:tests*');
    	$catId = InputSanitise::inputInt($request->get('category_id'));
    	if(isset($catId)){
    		$category = TestCategory::find($catId);
    		if(is_object($category)){
                DB::beginTransaction();
                try
                {
                    $loginUser = Auth::guard('web')->user();
                    if(is_object($loginUser) && $category->college_id == $loginUser->college_id && $category->user_id == $loginUser->id){
                        if(true == is_object($category->subcategories) && false == $category->subcategories->isEmpty()){
                            foreach($category->subcategories as $subcategory){
                                if(true == is_object($subcategory->subjects) && false == $subcategory->subjects->isEmpty()){
                                    foreach($subcategory->subjects as $subject){
                                        if(true == is_object($subject->papers) && false == $subject->papers->isEmpty()){
                                            foreach($subject->papers as $paper){
                                                if(true == is_object($paper->questions) && false == $paper->questions->isEmpty()){
                                                    foreach($paper->questions as $question){
                                                        UserSolution::deleteUserSolutionsByQuestionId($question->id);
                                                        $question->delete();
                                                    }
                                                }
                                                Score::deleteUserScoresByPaperId($paper->id);
                                                PaperSection::deletePaperSectionsByPaperId($paper->id);
                                                $paper->deleteRegisteredPaper();
                                                $paper->delete();
                                            }
                                        }
                                        $subject->delete();
                                    }
                                }
                                $subcategory->deleteSubCategoryImageFolder();
                                $subcategory->delete();
                            }
                        }
            			$category->delete();
                        DB::commit();
                        return Redirect::to('college/'.$collegeUrl.'/manageCategory')->with('message', 'Category deleted successfully!');
                    }
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return back()->withErrors('something went wrong.');
                }
            }
        }
		return Redirect::to('college/'.$collegeUrl.'/manageCategory');
    }

    protected function isTestCategoryExist(Request $request){
        return TestCategory::isTestCategoryExist($request);
    }
}
