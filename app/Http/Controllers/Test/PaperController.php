<?php

namespace App\Http\Controllers\Test;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TestCategory;
use App\Models\TestSubCategory;
use App\Models\TestSubject;
use App\Models\TestSubjectPaper;
use App\Models\Notification;
use App\Models\UserSolution;
use App\Models\PaperSection;
use App\Models\Score;
use App\Models\Admin;
use Redirect,Validator, Auth, DB;
use App\Libraries\InputSanitise;
use App\Mail\MailToSubscribedUser;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class PaperController extends Controller
{
    /**
     * check admin have permission or not, if not redirect to admin/home
     */
    public function __construct() {
        $this->middleware(function ($request, $next) {
            $adminUser = Auth::guard('admin')->user();
            if(is_object($adminUser)){
                if($adminUser->hasRole('admin') || $adminUser->hasRole('sub-admin')){
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
        'date_to_inactive' => 'required',
        'is_verification_code' => 'required',
    ];

    /**
     * show all test paper
     */
    public function show(){
        $testPapers = TestSubjectPaper::getPapersWithPagination();
    	return view('paper.list', compact('testPapers'));
    }

    /**
     *  show create UI for paper
     */
    protected function create(){
        $allSessions = [];
        $testCategories = TestCategory::getAllTestCategories();
        $testSubCategories = [];
        $testSubjects = [];
        $paper = new TestSubjectPaper;
        $paperSubcategory = '';
    	return view('paper.create', compact('testCategories','testSubCategories','testSubjects', 'paper', 'allSessions','paperSubcategory'));
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
        InputSanitise::deleteCacheByString('vchip:tests*');
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
    			$testCategories = TestCategory::getAllTestCategories();
				$testSubCategories = TestSubCategory::getSubcategoriesByCategoryIdForAdminForList($paper->test_category_id);
				$testSubjects = TestSubject::getSubjectsByCatIdBySubcatidForAdminForList($paper->test_category_id, $paper->test_sub_category_id);
                $allSessions = PaperSection::where('test_subject_paper_id', $paper->id)->get();
                $paperSubcategory =$paper->subcategory;
		    	return view('paper.create', compact('testCategories','testSubCategories','testSubjects', 'paper', 'allSessions','paperSubcategory'));
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
        InputSanitise::deleteCacheByString('vchip:tests*');
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
        InputSanitise::deleteCacheByString('vchip:tests*');
    	$paperId = InputSanitise::inputInt($request->get('paper_id'));
    	if(isset($paperId)){
    		$paper = TestSubjectPaper::find($paperId);
    		if(is_object($paper)){
                $paperSubcategory = $paper->subcategory;
                if($paperSubcategory->created_by == Auth::guard('admin')->user()->id){
                    DB::beginTransaction();
                    try
                    {
                        if( true == is_object($paper->questions) && false == $paper->questions->isEmpty()){
                            foreach($paper->questions as $question){
                                UserSolution::deleteUserSolutionsByQuestionId($question->id);
                                $question->delete();
                            }
                        }
                        Score::deleteUserScoresByPaperId($paper->id);
                        PaperSection::deletePaperSectionsByPaperId($paper->id);
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

    public function getPaperSectionsByPaperId(Request $request){
        return PaperSection::where('test_subject_paper_id', $request->get('paper_id'))->get();
    }

    protected function isTestPaperExist(Request $request){
        return TestSubjectPaper::isTestPaperExist($request);
    }

    protected function manageSubadminPapers(Request $request){
        $papers = TestSubjectPaper::getSubAdminPapersWithPagination();
        $admins = Admin::getSubAdmins();
        return view('subadmin.subadminPapers', compact('papers','admins'));
    }

    protected function getSubAdminPapers(Request $request){
        return TestSubjectPaper::getSubAdminPapers($request->get('admin_id'));
    }
}
