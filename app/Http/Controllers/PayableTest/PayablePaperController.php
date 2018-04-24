<?php

namespace App\Http\Controllers\PayableTest;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect, Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;
use App\Models\ClientOnlineTestCategory;
use App\Models\ClientOnlineTestSubCategory;
use App\Models\ClientOnlineTestSubject;
use App\Models\ClientOnlineTestSubjectPaper;
use App\Models\ClientUserSolution;
use App\Models\ClientScore;
use App\Models\ClientOnlinePaperSection;

class PayablePaperController extends Controller
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
    protected $validatePaper = [
        'subcategory' => 'required|integer',
        'subject' => 'required|integer',
        'name' => 'required|string',
        'date_to_active' => 'required',
        'date_to_inactive' => 'required',
    ];

    /**
     * show all test paper
     */
    public function show(Request $request){
        $testPapers = ClientOnlineTestSubjectPaper::showPayablePaper();
    	return view('payableTest.paper.list', compact('testPapers'));
    }

    /**
     *  show create UI for paper
     */
    protected function create(){
        $allSessions = [];
		$testSubCategories = ClientOnlineTestSubCategory::showPayableSubCategories();
		$testSubjects = new ClientOnlineTestSubject;
		$paper = new ClientOnlineTestSubjectPaper;
    	return view('payableTest.paper.create', compact('testSubCategories','testSubjects', 'paper', 'allSessions'));
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
        DB::connection('mysql2')->beginTransaction();
        try
        {
        	$paper = ClientOnlineTestSubjectPaper::addOrUpdatePayableSubjectPaper($request);
            if(is_object($paper)){
                DB::connection('mysql2')->commit();
                return Redirect::to('admin/managePayablePaper')->with('message', 'Paper created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }

        return Redirect::to('admin/managePayablePaper');
    }

    /**
     *  edit paper
     */
    protected function edit($id, Request $request){
    	$id = InputSanitise::inputInt(json_decode($id));
    	if(isset($id)){
    		$paper = ClientOnlineTestSubjectPaper::find($id);
    		if(is_object($paper)){
				$testSubCategories = ClientOnlineTestSubCategory::showPayableSubCategories();
				$testSubjects = ClientOnlineTestSubject::getPayableSubjectsBySubcatId($paper->sub_category_id);
                $allSessions = ClientOnlinePaperSection::payablePaperSectionsByPaperId($paper->id);

		    	return view('payableTest.paper.create', compact('testSubCategories','testSubjects', 'paper', 'allSessions'));
    		}
    	}
		return Redirect::to('admin/managePayablePaper');
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
            DB::connection('mysql2')->beginTransaction();
            try
            {
                $paper = ClientOnlineTestSubjectPaper::addOrUpdatePayableSubjectPaper($request, true);
                if(is_object($paper)){
                    DB::connection('mysql2')->commit();
                    return Redirect::to('admin/managePayablePaper')->with('message', 'Paper updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
                return redirect()->back()->withErrors('something went wrong.');
            }
        }
		return Redirect::to('admin/managePayablePaper');
    }

    /**
     *  delete paper
     */
    protected function delete(Request $request){
    	$paperId = InputSanitise::inputInt($request->get('paper_id'));
    	if(isset($paperId)){
    		$paper = ClientOnlineTestSubjectPaper::find($paperId);
    		if(is_object($paper)){
                DB::connection('mysql2')->beginTransaction();
                try
                {
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
                    DB::connection('mysql2')->commit();
                    return Redirect::to('admin/managePayablePaper')->with('message', 'Paper deleted successfully!');
                }
                catch(\Exception $e)
                {
                    DB::connection('mysql2')->rollback();
                    return redirect()->back()->withErrors('something went wrong.');
                }
    		}
    	}
		return Redirect::to('admin/managePayablePaper');
    }

    protected function getPayablePapersBySubjectId(Request $request){
        if($request->ajax()){
            $subjectId = InputSanitise::inputInt($request->get('subjectId'));
            return ClientOnlineTestSubjectPaper::getPayablePapersBySubjectId($subjectId);
        }
    }

    protected function getPayablePaperSectionsByPaperId(Request $request){
        return ClientOnlinePaperSection::payablePaperSectionsByPaperId($request->paper_id);
    }

    protected function isPayablePaperExist(Request $request){
        return ClientOnlineTestSubjectPaper::isPayablePaperExist($request);
    }
}