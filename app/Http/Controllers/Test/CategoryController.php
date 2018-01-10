<?php

namespace App\Http\Controllers\Test;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use App\Models\TestCategory;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;

class CategoryController extends Controller
{
    /**
     *  check admin have permission or not, if not redirect to home
     */
    public function __construct() {
        $this->middleware(function ($request, $next) {
            $adminUser = Auth::guard('admin')->user();
            if(is_object($adminUser)){
                if($adminUser->hasRole('admin') || $adminUser->hasPermission('manageOnlineTest')){
                    return $next($request);
                }
            }
            return Redirect::to('admin/home');
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
    protected function show(){
    	$testCategories = TestCategory::paginate();
    	return view('category.list', compact('testCategories'));
    }

    /**
     * show UI for create category
     */
    protected function create(){
    	$testCategory = new TestCategory;
    	return view('category.create', compact('testCategory'));
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
        DB::beginTransaction();
        try
        {
            $category = TestCategory::addOrUpdateCategory($request);
            if(is_object($category)){
                DB::commit();
                return Redirect::to('admin/manageCategory')->with('message', 'Category created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
		return Redirect::to('admin/manageCategory');
    }

    /**
     * edit category
     */
    protected function edit($id){
    	$catId = InputSanitise::inputInt(json_decode($id));
    	if(isset($catId)){
    		$testCategory = TestCategory::find($catId);
    		if(is_object($testCategory)){
    			return view('category.create', compact('testCategory'));
    		}
    	}
		return Redirect::to('admin/manageCategory');
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
            DB::beginTransaction();
            try
            {
                $category = TestCategory::addOrUpdateCategory($request, true);
                if(is_object($category)){
                    DB::commit();
                    return Redirect::to('admin/manageCategory')->with('message', 'Category updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('admin/manageCategory');
    }

    /**
     * delete category
     */
    protected function delete(Request $request){
    	$catId = InputSanitise::inputInt($request->get('category_id'));
    	if(isset($catId)){
    		$category = TestCategory::find($catId);
    		if(is_object($category)){
                DB::beginTransaction();
                try
                {
                    if(true == is_object($category->subcategories) && false == $category->subcategories->isEmpty()){
                        foreach($category->subcategories as $subcategory){
                            if(true == is_object($subcategory->subjects) && false == $subcategory->subjects->isEmpty()){
                                foreach($subcategory->subjects as $subject){
                                    if(true == is_object($subject->papers) && false == $subject->papers->isEmpty()){
                                        foreach($subject->papers as $paper){
                                            if(true == is_object($paper->questions) && false == $paper->questions->isEmpty()){
                                                foreach($paper->questions as $question){
                                                    $question->delete();
                                                }
                                            }
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
                    return Redirect::to('admin/manageCategory')->with('message', 'Category deleted successfully!');
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return back()->withErrors('something went wrong.');
                }
            }
        }
		return Redirect::to('admin/manageCategory');
    }

    protected function isTestCategoryExist(Request $request){
        return TestCategory::isTestCategoryExist($request);
    }
}
