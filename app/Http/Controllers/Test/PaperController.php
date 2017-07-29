<?php

namespace App\Http\Controllers\Test;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TestCategory;
use App\Models\TestSubCategory;
use App\Models\TestSubject;
use App\Models\TestSubjectPaper;
use Redirect;
use Validator, Auth, DB;
use App\Libraries\InputSanitise;

class PaperController extends Controller
{
    /**
     * check admin have permission or not, if not redirect to admin/home
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
    protected $validatePaper = [
        'category' => 'required|integer',
        'subcategory' => 'required|integer',
        'subject' => 'required|integer',
        'name' => 'required|string',
        'date_to_active' => 'required',
        'time' => 'required',
    ];

    /**
     * show all test paper
     */
    public function show(){
    	$testPapers = TestSubjectPaper::paginate();
    	return view('paper.list', compact('testPapers'));
    }

    /**
     *  show create UI for paper
     */
    protected function create(){
    	$testCategories    = TestCategory::all();
		$testSubCategories = new TestSubCategory;
		$testSubjects = new TestSubject;
		$paper = new TestSubjectPaper;
    	return view('paper.create', compact('testCategories','testSubCategories','testSubjects', 'paper'));
    }

    /**
     *  store paper
     */
    protected function store(Request $request){
        $v = Validator::make($request->all(), $this->validatePaper);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::beginTransaction();
        try
        {
        	$paper = TestSubjectPaper::addOrUpdateTestSubjectPaper($request);
            if(is_object($paper)){
                DB::commit();
                return Redirect::to('admin/managePaper')->with('message', 'Paper created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return back()->withErrors('something went wrong.');
        }
        return Redirect::to('admin/managePaper');
    }

    /**
     *  edit paper
     */
    protected function edit($id){
    	$id = InputSanitise::inputInt(json_decode($id));
    	if(isset($id)){
    		$paper = TestSubjectPaper::find($id);
    		if(is_object($paper)){
    			$testCategories    = TestCategory::all();
				$testSubCategories = TestSubCategory::getSubcategoriesByCategoryIdForAdmin($paper->test_category_id);
				$testSubjects = TestSubject::getSubjectsByCatIdBySubcatidForAdmin($paper->test_category_id, $paper->test_sub_category_id);
		    	return view('paper.create', compact('testCategories','testSubCategories','testSubjects', 'paper'));
    		}
    	}
		return Redirect::to('admin/managePaper');
    }

    /**
     *  update paper
     */
    protected function update(Request $request){
        $v = Validator::make($request->all(), $this->validatePaper);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        $paperId = InputSanitise::inputInt($request->get('paper_id'));
        if(isset($paperId)){
            DB::beginTransaction();
            try
            {
                $paper = TestSubjectPaper::addOrUpdateTestSubjectPaper($request, true);
                if(is_object($paper)){
                    DB::commit();
                    return Redirect::to('admin/managePaper')->with('message', 'Paper updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return back()->withErrors('something went wrong.');
            }
        }
		return Redirect::to('admin/managePaper');
    }

    /**
     *  delete paper
     */
    protected function delete(Request $request){
    	$paperId = InputSanitise::inputInt($request->get('paper_id'));
    	if(isset($paperId)){
    		$paper = TestSubjectPaper::find($paperId);
    		if(is_object($paper)){
                DB::beginTransaction();
                try
                {
                    if( true == is_object($paper->questions) && false == $paper->questions->isEmpty()){
                        foreach($paper->questions as $question){
                            $question->delete();
                        }
                    }
                    $paper->deleteRegisteredPaper();
    	    		$paper->delete();
                    DB::commit();
                    return Redirect::to('admin/managePaper')->with('message', 'Paper deleted successfully!');
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return back()->withErrors('something went wrong.');
                }
    		}
    	}
		return Redirect::to('admin/managePaper');
    }

    /**
     *  return subjects associated with category and sub category
     */
    public function getSubjectsByCatIdBySubcatId(Request $request){
    	if($request->ajax()){
    		$catId = InputSanitise::inputInt($request->get('catId'));
    		$subcatId = InputSanitise::inputInt($request->get('subcatId'));
    		return TestSubject::getSubjectsByCatIdBySubcatidForAdmin($catId, $subcatId);
    	}
    	return Redirect::to('admin/managePaper');
    }
}