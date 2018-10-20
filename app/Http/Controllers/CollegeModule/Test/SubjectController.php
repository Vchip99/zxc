<?php

namespace App\Http\Controllers\CollegeModule\Test;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CollegeCategory;
use App\Models\TestSubCategory;
use App\Models\TestSubject;
use App\Models\UserSolution;
use App\Models\Score;
use App\Models\PaperSection;
use App\Models\User;
use Redirect;
use Validator, Auth, DB;
use App\Libraries\InputSanitise;

class SubjectController extends Controller
{
	/**
     * check admin have permission or not, if not redirect to home
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
    protected $validateCreateSubject = [
            'category' => 'required|integer',
            'subcategory' => 'required|integer',
            'name' => 'required|string',
    ];

    /**
     *	show all subjects
     */
	public function show($collegeUrl,Request $request){
		if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
		$loginUser = Auth::guard('web')->user();

		if(User::Hod == $loginUser->user_type || User::Lecturer == $loginUser->user_type){
            $testSubjects = TestSubject::getSubjectsByCollegeIdByAssignedDeptsWithPagination($loginUser->college_id);
        } else {
            $testSubjects = TestSubject::getSubjectsByCollegeIdByDeptIdWithPagination($loginUser->college_id);
        }

		return view('collegeModule.test.subject.list', compact('testSubjects'));
	}

	/**
	 *	show create UI for subject
	 */
	protected function create($collegeUrl,Request $request){
		if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
		$loginUser = Auth::guard('web')->user();
		$testCategories = CollegeCategory::getCollegeCategoriesByCollegeIdByDeptId($loginUser->college_id);
		$testSubCategories = [];
		$subject = new TestSubject;
		return view('collegeModule.test.subject.create', compact('testCategories','testSubCategories','subject'));
	}

	/**
	 *	store subject
	 */
	protected function store($collegeUrl,Request $request){
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
	            return Redirect::to('college/'.$collegeUrl.'/manageSubject')->with('message', 'Subject created successfully!');
	        }
	    }
        catch(\Exception $e)
        {
            DB::rollback();
            return back()->withErrors('something went wrong.');
        }
        return Redirect::to('college/'.$collegeUrl.'/manageSubject');
	}

	/**
	 *	edit subject
	 */
	protected function edit($collegeUrl,$id,Request $request){
		if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
		$id = InputSanitise::inputInt(json_decode($id));
		if(isset($id)){
			$subject = TestSubject::find($id);
			if(is_object($subject)){
				$loginUser = Auth::guard('web')->user();
                if(is_object($loginUser) && $subject->created_by == $loginUser->id || (User::Hod ==  $loginUser->user_type || User::Directore ==  $loginUser->user_type)){
					$testCategories = CollegeCategory::getCollegeCategoriesByCollegeIdByDeptId($loginUser->college_id);
					$testSubCategories = TestSubCategory::getCollegeSubCategoriesByCategoryId($subject->test_category_id);
					return view('collegeModule.test.subject.create', compact('testCategories','testSubCategories','subject'));
				}
			}
		}
		return Redirect::to('college/'.$collegeUrl.'/manageSubject');
	}

	/**
	 *	update subject
	 */
	protected function update($collegeUrl,Request $request){
		if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
		$v = Validator::make($request->all(), $this->validateCreateSubject);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        // InputSanitise::deleteCacheByString('vchip:tests*');
		$subjectId = InputSanitise::inputInt($request->get('subject_id'));
		if(isset($subjectId)){
			DB::beginTransaction();
	        try
	        {
				$testSubject = TestSubject::addOrUpdateSubject($request, true);
		        if(is_object($testSubject)){
		        	DB::commit();
		            return Redirect::to('college/'.$collegeUrl.'/manageSubject')->with('message', 'Subject updated successfully!');
		        }
		    }
	        catch(\Exception $e)
	        {
	            DB::rollback();
	            return back()->withErrors('something went wrong.');
	        }
		}
		return Redirect::to('college/'.$collegeUrl.'/manageSubject');
	}

	/**
	 *	delete subject
	 */
	protected function delete($collegeUrl,Request $request){
		if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
		// InputSanitise::deleteCacheByString('vchip:tests*');
		$subjectId = InputSanitise::inputInt($request->get('subject_id'));
		if(isset($subjectId)){
			$testSubject = TestSubject::find($subjectId);
			if(is_object($testSubject)){
				DB::beginTransaction();
		        try
		        {
		        	$loginUser = Auth::guard('web')->user();
		        	if(is_object($loginUser) && $testSubject->created_by == $loginUser->id || (User::Hod ==  $loginUser->user_type || User::Directore ==  $loginUser->user_type)){
						if(true == is_object($testSubject->papers) && false == $testSubject->papers->isEmpty()){
							foreach($testSubject->papers as $paper){
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
						$testSubject->delete();
						DB::commit();
						return Redirect::to('college/'.$collegeUrl.'/manageSubject')->with('message', 'Subject deleted successfully!');
					}
				}
		        catch(\Exception $e)
		        {
		            DB::rollback();
		            return back()->withErrors('something went wrong.');
		        }
			}
		}
		return Redirect::to('college/'.$collegeUrl.'/manageSubject');
	}

	protected function isTestSubjectExist(Request $request){
		return TestSubject::isTestSubjectExist($request);
	}
}
