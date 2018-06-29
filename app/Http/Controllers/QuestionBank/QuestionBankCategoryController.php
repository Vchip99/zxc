<?php

namespace App\Http\Controllers\QuestionBank;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use App\Models\QuestionBankCategory;
use App\Models\UserSolution;
use App\Models\Score;
use App\Models\PaperSection;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;

class QuestionBankCategoryController extends Controller
{
    /**
     *  check admin have permission or not, if not redirect to home
     */
    public function __construct() {
        $this->middleware(function ($request, $next) {
            $adminUser = Auth::guard('admin')->user();
            if(is_object($adminUser)){
                if($adminUser->hasRole('admin')){
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
    	$testCategories = QuestionBankCategory::paginate();
    	return view('questionBank.category.list', compact('testCategories'));
    }

    /**
     * show UI for create category
     */
    protected function create(){
    	$testCategory = new QuestionBankCategory;
    	return view('questionBank.category.create', compact('testCategory'));
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
            $category = QuestionBankCategory::addOrUpdateCategory($request);
            if(is_object($category)){
                DB::commit();
                return Redirect::to('admin/manageQuestionBankCategory')->with('message', 'Category created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
		return Redirect::to('admin/manageQuestionBankCategory');
    }

    /**
     * edit category
     */
    protected function edit($id){
    	$catId = InputSanitise::inputInt(json_decode($id));
    	if(isset($catId)){
    		$testCategory = QuestionBankCategory::find($catId);
    		if(is_object($testCategory)){
    			return view('questionBank.category.create', compact('testCategory'));
    		}
    	}
		return Redirect::to('admin/manageQuestionBankCategory');
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
                $category = QuestionBankCategory::addOrUpdateCategory($request, true);
                if(is_object($category)){
                    DB::commit();
                    return Redirect::to('admin/manageQuestionBankCategory')->with('message', 'Category updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('admin/manageQuestionBankCategory');
    }

    /**
     * delete category
     */
    protected function delete(Request $request){
    	$catId = InputSanitise::inputInt($request->get('category_id'));
    	if(isset($catId)){
    		$category = QuestionBankCategory::find($catId);
    		if(is_object($category)){
                DB::beginTransaction();
                try
                {
                    if(true == is_object($category->subcategories) && false == $category->subcategories->isEmpty()){
                        foreach($category->subcategories as $subcategory){
                            if(true == is_object($subcategory->questions) && false == $subcategory->questions->isEmpty()){
                                foreach($subcategory->questions as $question){
                                    $question->delete();
                                }
                            }
                            $subcategory->delete();
                        }
                    }
        			$category->delete();
                    DB::commit();
                    return Redirect::to('admin/manageQuestionBankCategory')->with('message', 'Category deleted successfully!');
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return back()->withErrors('something went wrong.');
                }
            }
        }
		return Redirect::to('admin/manageQuestionBankCategory');
    }

    protected function isQuestionBankCategoryExist(Request $request){
        return QuestionBankCategory::isQuestionBankCategoryExist($request);
    }
}
