<?php

namespace App\Http\Controllers\Test;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TestCategory;
use App\Models\TestSubCategory;
use App\Models\UserSolution;
use App\Models\Score;
use App\Models\PaperSection;
use Redirect;
use Validator, Auth, DB;
use App\Libraries\InputSanitise;

class SubCategoryController extends Controller
{
    /**
     *  check admin have permission or not, if not redirect to admin/home
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
    protected $validateCreateSubcategory = [
        'category' => 'required',
        'name' => 'required',
        'image_path' => 'required',
    ];

    protected $validateUpdateSubcategory = [
        'category' => 'required',
        'name' => 'required',
    ];

    /**
     *  show all sub category
     */
    protected function show(){
        $testSubCategories = TestSubCategory::getSubcategoriesWithPagination();
    	return view('subcategory.list', compact('testSubCategories'));
    }

    /**
     *  show create UI for sub category
     */
    protected function create(){
    	$testCategories = TestCategory::getAllTestCategories();
    	$testSubcategory = new TestSubCategory;
    	return view('subcategory.create', compact('testCategories', 'testSubcategory'));
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
        InputSanitise::deleteCacheByString('vchip:tests*');
        DB::beginTransaction();
        try
        {
            $subcategory = TestSubCategory::addOrUpdateSubCategory($request);
            if(is_object($subcategory)){
                DB::commit();
                return Redirect::to('admin/manageSubCategory')->with('message', 'Sub Category created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('admin/manageSubCategory');
    }

    /**
     *  edit sub category
     */
    protected function edit($id){
    	$id = InputSanitise::inputInt(json_decode($id));
    	if(isset($id)){
    		$testSubcategory = TestSubCategory::find($id);
    		if(is_object($testSubcategory)){
    			$testCategories = TestCategory::getAllTestCategories();
                return view('subcategory.create', compact('testCategories', 'testSubcategory'));
    		}
        }
		return Redirect::to('admin/manageSubCategory');
    }

    /**
     *  update sub category
     */
    protected function update( Request $request){
        $v = Validator::make($request->all(), $this->validateUpdateSubcategory);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        InputSanitise::deleteCacheByString('vchip:tests*');
    	$subcatId = InputSanitise::inputInt($request->get('subcat_id'));
    	if(isset($subcatId)){
            DB::beginTransaction();
            try
            {
                $subcategory = TestSubCategory::addOrUpdateSubCategory($request, true);
                if(is_object($subcategory)){
                    DB::commit();
                    return Redirect::to('admin/manageSubCategory')->with('message', 'Sub Category created successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return redirect()->back()->withErrors('something went wrong.');
            }
        }
		return Redirect::to('admin/manageSubCategory');
    }

    /**
     *  delete sub category
     */
    protected function delete( Request $request){
        InputSanitise::deleteCacheByString('vchip:tests*');
    	$subcat_id = InputSanitise::inputInt($request->get('subcat_id'));
    	if(isset($subcat_id)){
    		$testSubcategory = TestSubCategory::find($subcat_id);
    		if(is_object($testSubcategory)){
                DB::beginTransaction();
                try
                {
                    if(true == is_object($testSubcategory->subjects) && false == $testSubcategory->subjects->isEmpty()){
                        foreach($testSubcategory->subjects as $subject){
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
                    $testSubcategory->deleteSubCategoryImageFolder();
        			$testSubcategory->delete();
                    DB::commit();
                    return Redirect::to('admin/manageSubCategory')->with('message', 'Sub Category deleted successfully!');
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return redirect()->back()->withErrors('something went wrong.');
                }
            }
        }
		return Redirect::to('admin/manageSubCategory');
    }

    /**
     *  return sub categories by categoryId
     */
    public function getSubCategories(Request $request){
        if($request->ajax()){
            $id = InputSanitise::inputInt($request->get('id'));
            return $subCategories = TestSubCategory::getSubcategoriesByCategoryIdForAdmin($id);
        }
    }

    protected function isTestSubCategoryExist(Request $request){
        return TestSubCategory::isTestSubCategoryExist($request);
    }

}
