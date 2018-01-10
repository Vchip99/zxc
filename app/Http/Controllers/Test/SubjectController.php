<?php

namespace App\Http\Controllers\Test;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TestCategory;
use App\Models\TestSubCategory;
use App\Models\TestSubject;
use Redirect;
use Validator, Auth, DB;
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
    protected $validateCreateSubject = [
            'category' => 'required|integer',
            'subcategory' => 'required|integer',
            'name' => 'required|string',
    ];

    /**
     *	show all subjects
     */
	public function show(){
		$testSubjects 	   = TestSubject::paginate();
		return view('subject.list', compact('testSubjects'));
	}

	/**
	 *	show create UI for subject
	 */
	protected function create(){
		$testCategories    = TestCategory::all();
		$testSubCategories = new TestSubCategory;
		$subject = new TestSubject;

		return view('subject.create', compact('testCategories','testSubCategories','subject'));
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
				$testCategories    = TestCategory::all();
				$testSubCategories = TestSubCategory::getSubjectSubcategoriesByCategoryId($subject->test_category_id);
				return view('subject.create', compact('testCategories','testSubCategories','subject'));
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
		$subjectId = InputSanitise::inputInt($request->get('subject_id'));
		if(isset($subjectId)){
			$testSubject = TestSubject::find($subjectId);
			if(is_object($testSubject)){
				DB::beginTransaction();
		        try
		        {
					if(true == is_object($testSubject->papers) && false == $testSubject->papers->isEmpty()){
						foreach($testSubject->papers as $paper){
							if(true == is_object($paper->questions) && false == $paper->questions->isEmpty()){
								foreach($paper->questions as $question){
									$question->delete();
								}
							}
							$paper->delete();
						}
					}
					$testSubject->delete();
					DB::commit();
					return Redirect::to('admin/manageSubject')->with('message', 'Subject deleted successfully!');
				}
		        catch(\Exception $e)
		        {
		            DB::rollback();
		            return back()->withErrors('something went wrong.');
		        }
			}
		}
		return Redirect::to('admin/manageSubject');
	}

	protected function isTestSubjectExist(Request $request){
		return TestSubject::isTestSubjectExist($request);
	}
}
