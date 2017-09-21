<?php

namespace App\Http\Controllers\Client\OnlineTest;

use Illuminate\Http\Request;
use App\Http\Controllers\Client\ClientBaseController;
use Redirect;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;
use App\Models\ClientOnlineTestCategory;
use App\Models\ClientOnlineTestSubCategory;
use App\Models\ClientOnlineTestSubject;
use App\Models\ClientOnlineTestSubjectPaper;
use App\Models\ClientInstituteCourse;
use App\Models\ClientUserSolution;
use App\Models\ClientScore;

class ClientOnlineTestSubjectPaperController extends ClientBaseController
{
	/**
     * check admin have permission or not, if not redirect to admin/home
     */
    public function __construct(Request $request) {
        parent::__construct($request);
        $this->middleware('client');
    }

    /**
     * Define your validation rules in a property in
     * the controller to reuse the rules.
     */
    protected $validatePaper = [
        'institute_course' => 'required|integer',
        'category' => 'required|integer',
        'subcategory' => 'required|integer',
        'subject' => 'required|integer',
        'name' => 'required|string',
        'time' => 'required',
    ];

    /**
     * show all test paper
     */
    public function show(Request $request){
        $coursePermission = InputSanitise::checkModulePermission($request, 'test');
        if('false' == $coursePermission){
            return Redirect::to('manageClientHome');
        }
    	$testPapers = ClientOnlineTestSubjectPaper::showPaper($request);
    	return view('client.onlineTest.paper.list', compact('testPapers'));
    }

    /**
     *  show create UI for paper
     */
    protected function create(Request $request){
        $clientId = Auth::guard('client')->user()->id;
        $instituteCourses = ClientInstituteCourse::where('client_id', $clientId)->get();
    	$testCategories    = new ClientOnlineTestCategory;
		$testSubCategories = new ClientOnlineTestSubCategory;
		$testSubjects = new ClientOnlineTestSubject;
		$paper = new ClientOnlineTestSubjectPaper;
    	return view('client.onlineTest.paper.create', compact('instituteCourses','testCategories','testSubCategories','testSubjects', 'paper'));
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
        	$paper = ClientOnlineTestSubjectPaper::addOrUpdateOnlineTestSubjectPaper($request);
            if(is_object($paper)){
                DB::connection('mysql2')->commit();
                return Redirect::to('manageOnlineTestSubjectPaper')->with('message', 'Paper created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }

        return Redirect::to('manageOnlineTestSubjectPaper');
    }

    /**
     *  edit paper
     */
    protected function edit($subdomain, $id, Request $request){
    	$id = InputSanitise::inputInt(json_decode($id));
    	if(isset($id)){
    		$paper = ClientOnlineTestSubjectPaper::find($id);
    		if(is_object($paper)){
                $instituteCourses = ClientInstituteCourse::where('client_id', $paper->client_id)->get();
    			$testCategories    = ClientOnlineTestCategory::showCategories($request);
				$testSubCategories = ClientOnlineTestSubCategory::getOnlineTestSubcategoriesByCategoryId($paper->category_id, $request);
				$testSubjects = ClientOnlineTestSubject::getOnlineSubjectsByCatIdBySubcatId($paper->category_id, $paper->sub_category_id, $request);
		    	return view('client.onlineTest.paper.create', compact('instituteCourses','testCategories','testSubCategories','testSubjects', 'paper'));
    		}
    	}
		return Redirect::to('manageOnlineTestSubjectPaper');
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
                $paper = ClientOnlineTestSubjectPaper::addOrUpdateOnlineTestSubjectPaper($request, true);
                if(is_object($paper)){
                    DB::connection('mysql2')->commit();
                    return Redirect::to('manageOnlineTestSubjectPaper')->with('message', 'Paper updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
                return redirect()->back()->withErrors('something went wrong.');
            }
        }
		return Redirect::to('manageOnlineTestSubjectPaper');
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
                    $paper->deleteRegisteredPaper();
    	    		$paper->delete();
                    DB::connection('mysql2')->commit();
                    return Redirect::to('manageOnlineTestSubjectPaper')->with('message', 'Paper deleted successfully!');
                }
                catch(\Exception $e)
                {
                    DB::connection('mysql2')->rollback();
                    return redirect()->back()->withErrors('something went wrong.');
                }
    		}
    	}
		return Redirect::to('manageOnlineTestSubjectPaper');
    }

    protected function getOnlinePapersBySubjectId(Request $request){
        if($request->ajax()){
            $subjectId = InputSanitise::inputInt($request->get('subjectId'));
            return ClientOnlineTestSubjectPaper::getOnlinePapersBySubjectId($subjectId);
        }
    }

}