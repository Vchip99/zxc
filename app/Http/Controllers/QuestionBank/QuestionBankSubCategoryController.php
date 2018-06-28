<?php

namespace App\Http\Controllers\QuestionBank;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\QuestionBankCategory;
use App\Models\QuestionBankSubCategory;
use Redirect,Validator,Auth,DB;
use App\Libraries\InputSanitise;

class QuestionBankSubCategoryController extends Controller
{
    /**
     *  check admin have permission or not, if not redirect to admin/home
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
    protected $validateCreateSubcategory = [
        'category' => 'required',
        'name' => 'required',
    ];

    /**
     *  show all sub category
     */
    protected function show(){
    	$testSubCategories = QuestionBankSubCategory::paginate();
    	return view('questionBank.subcategory.list', compact('testSubCategories'));
    }

    /**
     *  show create UI for sub category
     */
    protected function create(){
    	$testCategories = QuestionBankCategory::all();
    	$testSubcategory = new QuestionBankSubCategory;
    	return view('questionBank.subcategory.create', compact('testCategories', 'testSubcategory'));
    }

    /**
     *  store sub category
     */
    protected function store(Request $request){
        $v = Validator::make($request->all(), $this->validateCreateSubcategory);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::beginTransaction();
        try
        {
            $subcategory = QuestionBankSubCategory::addOrUpdateSubCategory($request);
            if(is_object($subcategory)){
                DB::commit();
                return Redirect::to('admin/manageQuestionBankSubCategory')->with('message', 'Sub Category created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('admin/manageQuestionBankSubCategory');
    }

    /**
     *  edit sub category
     */
    protected function edit($id){
    	$id = InputSanitise::inputInt(json_decode($id));
    	if(isset($id)){
    		$testSubcategory = QuestionBankSubCategory::find($id);
    		if(is_object($testSubcategory)){
    			$testCategories = QuestionBankCategory::all();
    			return view('questionBank.subcategory.create', compact('testCategories', 'testSubcategory'));
    		}
        }
		return Redirect::to('admin/manageQuestionBankSubCategory');
    }

    /**
     *  update sub category
     */
    protected function update( Request $request){
        $v = Validator::make($request->all(), $this->validateCreateSubcategory);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
    	$subcatId = InputSanitise::inputInt($request->get('subcat_id'));
    	if(isset($subcatId)){
            DB::beginTransaction();
            try
            {
                $subcategory = QuestionBankSubCategory::addOrUpdateSubCategory($request, true);
                if(is_object($subcategory)){
                    DB::commit();
                    return Redirect::to('admin/manageQuestionBankSubCategory')->with('message', 'Sub Category created successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return redirect()->back()->withErrors('something went wrong.');
            }
        }
		return Redirect::to('admin/manageQuestionBankSubCategory');
    }

    /**
     *  delete sub category
     */
    protected function delete( Request $request){
    	$subcat_id = InputSanitise::inputInt($request->get('subcat_id'));
    	if(isset($subcat_id)){
    		$testSubcategory = QuestionBankSubCategory::find($subcat_id);
    		if(is_object($testSubcategory)){
                DB::beginTransaction();
                try
                {
                    // if(true == is_object($testSubcategory->subjects) && false == $testSubcategory->subjects->isEmpty()){
                    //     foreach($testSubcategory->subjects as $subject){
                    //         if(true == is_object($subject->papers) && false == $subject->papers->isEmpty()){
                    //             foreach($subject->papers as $paper){
                    //                 if(true == is_object($paper->questions) && false == $paper->questions->isEmpty()){
                    //                     foreach($paper->questions as $question){
                    //                         UserSolution::deleteUserSolutionsByQuestionId($question->id);
                    //                         $question->delete();
                    //                     }
                    //                 }
                    //                 Score::deleteUserScoresByPaperId($paper->id);
                    //                 PaperSection::deletePaperSectionsByPaperId($paper->id);
                    //                 $paper->deleteRegisteredPaper();
                    //                 $paper->delete();
                    //             }
                    //         }
                    //         $subject->delete();
                    //     }
                    // }
        			$testSubcategory->delete();
                    DB::commit();
                    return Redirect::to('admin/manageQuestionBankSubCategory')->with('message', 'Sub Category deleted successfully!');
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return redirect()->back()->withErrors('something went wrong.');
                }
            }
        }
		return Redirect::to('admin/manageQuestionBankSubCategory');
    }

    /**
     *  return sub categories by categoryId
     */
    public function getQuestionBankSubCategories(Request $request){
        if($request->ajax()){
            $id = InputSanitise::inputInt($request->get('id'));
            return QuestionBankSubCategory::getSubcategoriesByCategoryId($id);
        }
    }

    protected function isQuestionBankSubCategoryExist(Request $request){
        return QuestionBankSubCategory::isQuestionBankSubCategoryExist($request);
    }

}
