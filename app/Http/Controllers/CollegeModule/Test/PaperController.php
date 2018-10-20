<?php

namespace App\Http\Controllers\CollegeModule\Test;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CollegeCategory;
use App\Models\TestSubCategory;
use App\Models\TestSubject;
use App\Models\TestSubjectPaper;
use App\Models\Notification;
use App\Models\UserSolution;
use App\Models\PaperSection;
use App\Models\Score;
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
    public function show($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $loginUser = Auth::guard('web')->user();
        if(User::Hod == $loginUser->user_type || User::Lecturer == $loginUser->user_type){
            $testPapers = TestSubjectPaper::getPapersByCollegeIdByAssignedDeptsWithPagination($loginUser->college_id);
        } else {
            $testPapers = TestSubjectPaper::getPapersByCollegeIdByDeptIdWithPagination($loginUser->college_id);
        }
    	return view('collegeModule.test.paper.list', compact('testPapers'));
    }

    /**
     *  show create UI for paper
     */
    protected function create($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $allSessions = [];
        $testSubCategories = [];
        $testSubjects = [];
        $loginUser = Auth::guard('web')->user();
        $testCategories = CollegeCategory::getCollegeCategoriesByCollegeIdByDeptId($loginUser->college_id);
        $paper = new TestSubjectPaper;
    	return view('collegeModule.test.paper.create', compact('testCategories','testSubCategories','testSubjects', 'paper', 'allSessions'));
    }

    /**
     *  store paper
     */
    protected function store($collegeUrl,Request $request){
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
                return Redirect::to('college/'.$collegeUrl.'/managePaper')->with('message', 'Paper created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return back()->withErrors('something went wrong.');
        }
        return Redirect::to('college/'.$collegeUrl.'/managePaper');
    }

    /**
     *  edit paper
     */
    protected function edit($collegeUrl,$id,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
    	$id = InputSanitise::inputInt(json_decode($id));
    	if(isset($id)){
    		$paper = TestSubjectPaper::find($id);
    		if(is_object($paper)){
    			$loginUser = Auth::guard('web')->user();
                $paperSubject = $paper->subject;
                if(is_object($loginUser) && $paperSubject->created_by == $loginUser->id || (User::Hod ==  $loginUser->user_type || User::Directore ==  $loginUser->user_type)){
                    $testCategories = CollegeCategory::getCollegeCategoriesByCollegeIdByDeptId($loginUser->college_id);
    				$testSubCategories = TestSubCategory::getCollegeSubcategoriesByCategoryIdForAdmin($paper->test_category_id);
                    if(User::Hod == $loginUser->user_type || User::Lecturer == $loginUser->user_type){
                        $testSubjects = TestSubject::getSubjectsByCollegeIdByAssignedDepts($loginUser->college_id);
                    } else {
                        if(User::TNP == $loginUser->user_type){
                            $testSubjects = TestSubject::getCollegeSubjectsByCatIdBySubcatIdByUser($paper->test_category_id, $paper->test_sub_category_id);
                        } else {
                            $testSubjects = TestSubject::getCollegeSubjectsByCatIdBySubcatidForAdmin($paper->test_category_id, $paper->test_sub_category_id);
                        }
                    }
                    $allSessions = PaperSection::where('test_subject_paper_id', $paper->id)->get();
    		    	return view('collegeModule.test.paper.create', compact('testCategories','testSubCategories','testSubjects', 'paper', 'allSessions'));
                }
    		}
    	}
		return Redirect::to('college/'.$collegeUrl.'/managePaper');
    }

    /**
     *  update paper
     */
    protected function update($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $v = Validator::make($request->all(), $this->validatePaper);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        // InputSanitise::deleteCacheByString('vchip:tests*');
        $paperId = InputSanitise::inputInt($request->get('paper_id'));
        if(isset($paperId)){
            DB::beginTransaction();
            try
            {
                $paper = TestSubjectPaper::addOrUpdateTestSubjectPaper($request, true);
                if(is_object($paper)){
                    DB::commit();
                    return Redirect::to('college/'.$collegeUrl.'/managePaper')->with('message', 'Paper updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return back()->withErrors('something went wrong.');
            }
        }
		return Redirect::to('college/'.$collegeUrl.'/managePaper');
    }

    /**
     *  delete paper
     */
    protected function delete($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        // InputSanitise::deleteCacheByString('vchip:tests*');
    	$paperId = InputSanitise::inputInt($request->get('paper_id'));
    	if(isset($paperId)){
    		$paper = TestSubjectPaper::find($paperId);
    		if(is_object($paper)){
                DB::beginTransaction();
                try
                {
                    $loginUser = Auth::guard('web')->user();
                    $paperSubject = $paper->subject;
                    if(is_object($loginUser) && $paperSubject->created_by == $loginUser->id || (User::Hod ==  $loginUser->user_type || User::Directore ==  $loginUser->user_type)){
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
                        return Redirect::to('college/'.$collegeUrl.'/managePaper')->with('message', 'Paper deleted successfully!');
                    }
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return back()->withErrors('something went wrong.');
                }
    		}
    	}
		return Redirect::to('college/'.$collegeUrl.'/managePaper');
    }

    /**
     *  return subjects associated with category and sub category
     */
    public function getCollegeSubjectsByCatIdBySubcatId(Request $request){
		$catId = InputSanitise::inputInt($request->get('catId'));
		$subcatId = InputSanitise::inputInt($request->get('subcatId'));
		return TestSubject::getCollegeSubjectsByCatIdBySubcatidForAdmin($catId, $subcatId);
    }

    public function getPaperSectionsByPaperId(Request $request){
        return PaperSection::where('test_subject_paper_id', $request->get('paper_id'))->get();
    }

    protected function isTestPaperExist(Request $request){
        return TestSubjectPaper::isTestPaperExist($request);
    }


    /**
     *  return subjects associated with category and sub category
     */
    public function getCollegeSubjectsByCatIdBySubcatIdByUser(Request $request){
        $catId = InputSanitise::inputInt($request->get('catId'));
        $subcatId = InputSanitise::inputInt($request->get('subcatId'));
        return TestSubject::getCollegeSubjectsByCatIdBySubcatIdByUser($catId, $subcatId);
    }
    /**
     *  return subjects associated with category and sub category
     */
    public function getCollegeSubjectsByCatIdBySubcatIdByUserType(Request $request){
        $catId = InputSanitise::inputInt($request->get('catId'));
        $subcatId = InputSanitise::inputInt($request->get('subcatId'));
        $loginUser = Auth::user();
        if(User::Lecturer == $loginUser->user_type || User::Hod == $loginUser->user_type) {
            return TestSubject::getSubjectsByCollegeIdByAssignedDeptsByCategoryIdBySubCategoryId($loginUser->college_id,$catId, $subcatId);
        } else {
           if(User::TNP == $loginUser->user_type){
                return TestSubject::getCollegeSubjectsByCatIdBySubcatIdByUser($catId, $subcatId);
            } else {
                return TestSubject::getCollegeSubjectsByCatIdBySubcatidForAdmin($catId, $subcatId);
            }
        }
    }
}
