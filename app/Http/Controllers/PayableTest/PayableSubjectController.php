<?php

namespace App\Http\Controllers\PayableTest;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect, Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;
use App\Models\ClientOnlineTestSubCategory;
use App\Models\ClientOnlineTestSubject;
use App\Models\ClientOnlinePaperSection;
use App\Models\ClientScore;
use App\Models\ClientUserSolution;

class PayableSubjectController extends Controller
{
	/**
     *  check admin have permission or not, if not redirect to admin/home
     */
    public function __construct(Request $request) {
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
    protected $validateCreateSubject = [
        'subcategory' => 'required|integer',
        'name' => 'required|string',
    ];

    /**
     *	show all subjects
     */
	public function show(Request $request){
		$testSubjects 	   = ClientOnlineTestSubject::showPayableSubjects();
		return view('payableTest.subject.list', compact('testSubjects'));
	}

	/**
	 *	show create UI for subject
	 */
	protected function create(Request $request){
		$testSubCategories = ClientOnlineTestSubCategory::showPayableSubCategories();
		$subject = new ClientOnlineTestSubject;
		return view('payableTest.subject.create', compact('testSubCategories','subject'));
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
        DB::connection('mysql2')->beginTransaction();
        try
        {
	        $testSubject = ClientOnlineTestSubject::addOrUpdatePayableSubject($request);
	        if(is_object($testSubject)){
	        	DB::connection('mysql2')->commit();
	            return Redirect::to('admin/managePayableSubject')->with('message', 'Subject created successfully!');
	        }
	    }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('');
	}

	/**
	 *	edit subject
	 */
	protected function edit($id, Request $request){
		$id = InputSanitise::inputInt(json_decode($id));
		if(isset($id)){
			$subject = ClientOnlineTestSubject::find($id);
			if(is_object($subject)){
				$testSubCategories = ClientOnlineTestSubCategory::showPayableSubCategories();
				return view('payableTest.subject.create', compact('testSubCategories','subject'));
			}
		}
		return Redirect::to('admin/managePayableSubject');
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
			DB::connection('mysql2')->beginTransaction();
	        try
	        {
				$testSubject = ClientOnlineTestSubject::addOrUpdatePayableSubject($request, true);
		        if(is_object($testSubject)){
		        	DB::connection('mysql2')->commit();
		            return Redirect::to('admin/managePayableSubject')->with('message', 'Subject updated successfully!');
		        }
		    }
	        catch(\Exception $e)
	        {
	            DB::connection('mysql2')->rollback();
	            return redirect()->back()->withErrors('something went wrong.');
	        }
		}
		return Redirect::to('admin/managePayableSubject');
	}

	/**
	 *	delete subject
	 */
	protected function delete(Request $request){
		$subjectId = InputSanitise::inputInt($request->get('subject_id'));
		if(isset($subjectId)){
			$testSubject = ClientOnlineTestSubject::find($subjectId);
			if( isset($testSubject)){
				DB::connection('mysql2')->beginTransaction();
		        try
		        {
		        	if(true == is_object($testSubject->papers) && false == $testSubject->papers->isEmpty()){
		        		foreach($testSubject->papers as $paper){
		        			if(true == is_object($paper->questions) && false == $paper->questions->isEmpty()){
		                        foreach($paper->questions as $question){
		                        	ClientUserSolution::deleteClientUserSolutionsByQuestionId($question->id);
		                            $question->delete();
		                        }
		                    }
		                    ClientScore::deleteScoresByPaperId($paper->id);
		                    ClientOnlinePaperSection::deletePayablePaperSectionsByPaperId($paper->id);
		                    $paper->deletePayableRegisteredPaper();
		    	    		$paper->delete();
		        		}
		        	}
					$testSubject->delete();
					DB::connection('mysql2')->commit();
					return Redirect::to('admin/managePayableSubject')->with('message', 'Subject deleted successfully!');
				}
		        catch(\Exception $e)
		        {
		            DB::connection('mysql2')->rollback();
		            return redirect()->back()->withErrors('something went wrong.');
		        }
			}
		}
		return Redirect::to('admin/managePayableSubject');
	}

	protected function isPayableSubjectExist(Request $request){
		return ClientOnlineTestSubject::isPayableSubjectExist($request);
	}

	protected function getPayableSubjectsBySubcatId(Request $request){
		$subcatId = InputSanitise::inputInt($request->get('subcatId'));
		return ClientOnlineTestSubject::getPayableSubjectsBySubcatId($subcatId);
	}
}