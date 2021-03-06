<?php

namespace App\Http\Controllers\Test;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TestCategory;
use App\Models\TestSubCategory;
use App\Models\TestSubject;
use App\Models\UserSolution;
use App\Models\Score;
use App\Models\PaperSection;
use App\Models\Admin;
use Redirect,Validator, Auth, DB;
use App\Libraries\InputSanitise;

class SubjectController extends Controller
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
    protected $validateCreateSubject = [
            'category' => 'required|integer',
            'subcategory' => 'required|integer',
            'name' => 'required|string',
    ];

    /**
     *	show all subjects
     */
	public function show(){
		$testSubjects = TestSubject::getSubjectsWithPagination();
		return view('subject.list', compact('testSubjects'));
	}

	/**
	 *	show create UI for subject
	 */
	protected function create(){
		$testCategories = TestCategory::getAllTestCategories();
		$testSubCategories = [];
		$subject = new TestSubject;
		$subjectSubcategory = '';
		return view('subject.create', compact('testCategories','testSubCategories','subject','subjectSubcategory'));
	}

	/**
	 *	store subject
	 */
	protected function store(Request $request){
		$v = Validator::make($request->all(), $this->validateCreateSubject);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        InputSanitise::deleteCacheByString('vchip:tests*');
        DB::beginTransaction();
        try
        {
	        $testSubject = TestSubject::addOrUpdateSubject($request);
	        if(is_object($testSubject)){
	        	DB::commit();
	            return Redirect::to('admin/manageSubject')->with('message', 'Subject created successfully!');
	        }
	    }
        catch(\Exception $e)
        {
            DB::rollback();
            return back()->withErrors('something went wrong.');
        }
        return Redirect::to('admin/manageSubject');
	}

	/**
	 *	edit subject
	 */
	protected function edit($id){
		$id = InputSanitise::inputInt(json_decode($id));
		if(isset($id)){
			$subject = TestSubject::find($id);
			if(is_object($subject)){
				$testCategories = TestCategory::getAllTestCategories();
				$testSubCategories = TestSubCategory::getSubjectSubcategoriesByCategoryId($subject->test_category_id);
				$subjectSubcategory = $subject->subcategory;
				return view('subject.create', compact('testCategories','testSubCategories','subject','subjectSubcategory'));
			}
		}
		return Redirect::to('admin/manageSubject');
	}

	/**
	 *	update subject
	 */
	protected function update(Request $request){
		$v = Validator::make($request->all(), $this->validateCreateSubject);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        InputSanitise::deleteCacheByString('vchip:tests*');
		$subjectId = InputSanitise::inputInt($request->get('subject_id'));
		if(isset($subjectId)){
			DB::beginTransaction();
	        try
	        {
				$testSubject = TestSubject::addOrUpdateSubject($request, true);
		        if(is_object($testSubject)){
		        	DB::commit();
		            return Redirect::to('admin/manageSubject')->with('message', 'Subject updated successfully!');
		        }
		    }
	        catch(\Exception $e)
	        {
	            DB::rollback();
	            return back()->withErrors('something went wrong.');
	        }
		}
		return Redirect::to('admin/manageSubject');
	}

	/**
	 *	delete subject
	 */
	protected function delete(Request $request){
		InputSanitise::deleteCacheByString('vchip:tests*');
		$subjectId = InputSanitise::inputInt($request->get('subject_id'));
		if(isset($subjectId)){
			$testSubject = TestSubject::find($subjectId);
			if(is_object($testSubject)){
				$subjectSubcategory = $testSubject->subcategory;
				if($subjectSubcategory->created_by == Auth::guard('admin')->user()->id){
					DB::beginTransaction();
			        try
			        {
			        	$subjectCategory = $testSubject->category;
	                    if(0 == $subjectCategory->college_id && 0 == $subjectCategory->user_id){
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
							return Redirect::to('admin/manageSubject')->with('message', 'Subject deleted successfully!');
						}
					}
			        catch(\Exception $e)
			        {
			            DB::rollback();
			            return back()->withErrors('something went wrong.');
			        }
			    }
			}
		}
		return Redirect::to('admin/manageSubject');
	}

	protected function isTestSubjectExist(Request $request){
		return TestSubject::isTestSubjectExist($request);
	}

	protected function manageSubadminSubjects(Request $request){
        $subjects = TestSubject::getSubAdminSubjectsWithPagination();
        $admins = Admin::getSubAdmins();
        return view('subadmin.subadminSubjects', compact('subjects','admins'));
    }

    protected function getSubAdminSubjects(Request $request){
        return TestSubject::getSubAdminSubjects($request->get('admin_id'));
    }
}
