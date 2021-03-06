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
use App\Models\ClientUserSolution;
use App\Models\ClientScore;
use App\Models\ClientOnlinePaperSection;

class ClientOnlineTestSubjectPaperController extends ClientBaseController
{
	/**
     * check admin have permission or not, if not redirect to admin/home
     */
    public function __construct(Request $request) {
        parent::__construct($request);
        // $this->middleware('client');
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
    ];

    /**
     * show all test paper
     */
    public function show($subdomainName,Request $request){
        if(false == InputSanitise::checkDomain($request)){
            return Redirect::to('/');
        }
        if(false == InputSanitise::getCurrentGuard()){
            return Redirect::to('/');
        }
        $loginUser = InputSanitise::getLoginUserByGuardForClient();
        if(!is_object($loginUser)){
            return Redirect::to('/');
        } elseif(is_object($loginUser) && 'clientuser' == InputSanitise::getCurrentGuard() && 2 != $loginUser->user_type){
            return Redirect::to('/');
        }
    	$testPapers = ClientOnlineTestSubjectPaper::showPaper($request);
    	return view('client.onlineTest.paper.list', compact('testPapers', 'subdomainName','loginUser'));
    }

    /**
     *  show create UI for paper
     */
    protected function create($subdomainName,Request $request){
        if(false == InputSanitise::checkDomain($request)){
            return Redirect::to('/');
        }
        if(false == InputSanitise::getCurrentGuard()){
            return Redirect::to('/');
        }
        $loginUser = InputSanitise::getLoginUserByGuardForClient();
        if(!is_object($loginUser)){
            return Redirect::to('/');
        } elseif(is_object($loginUser) && 'clientuser' == InputSanitise::getCurrentGuard() && 2 != $loginUser->user_type){
            return Redirect::to('/');
        }
        $allSessions = [];
        $resultArr = InputSanitise::getClientIdAndCretedBy();
        $clientId = $resultArr[0];
    	$testCategories    = ClientOnlineTestCategory::where('client_id', $clientId)->get();
		$testSubCategories = new ClientOnlineTestSubCategory;
		$testSubjects = new ClientOnlineTestSubject;
		$paper = new ClientOnlineTestSubjectPaper;
    	return view('client.onlineTest.paper.create', compact('testCategories','testSubCategories','testSubjects', 'paper', 'allSessions', 'subdomainName','loginUser'));
    }

    /**
     *  store paper
     */
    protected function store($subdomain,Request $request){
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
    protected function edit($subdomainName, $id, Request $request){
        if(false == InputSanitise::checkDomain($request)){
            return Redirect::to('/');
        }
        if(false == InputSanitise::getCurrentGuard()){
            return Redirect::to('/');
        }
        $loginUser = InputSanitise::getLoginUserByGuardForClient();
        if(!is_object($loginUser)){
            return Redirect::to('/');
        } elseif(is_object($loginUser) && 'clientuser' == InputSanitise::getCurrentGuard() && 2 != $loginUser->user_type){
            return Redirect::to('/');
        }
        $resultArr = InputSanitise::getClientIdAndCretedBy();
        $clientId = $resultArr[0];
    	$id = InputSanitise::inputInt(json_decode($id));
    	if(isset($id)){
    		$paper = ClientOnlineTestSubjectPaper::find($id);
    		if(is_object($paper)){
    			$testCategories    = ClientOnlineTestCategory::showCategories($request);
				$testSubCategories = ClientOnlineTestSubCategory::getOnlineTestSubcategoriesByCategoryId($paper->category_id, $request);
				$testSubjects = ClientOnlineTestSubject::getOnlineSubjectsByCatIdBySubcatId($paper->category_id, $paper->sub_category_id, $request);
                $allSessions = ClientOnlinePaperSection::paperSectionsByPaperId($paper->id, $clientId);

		    	return view('client.onlineTest.paper.create', compact('testCategories','testSubCategories','testSubjects', 'paper', 'allSessions', 'subdomainName','loginUser'));
    		}
    	}
		return Redirect::to('manageOnlineTestSubjectPaper');
    }

    /**
     *  update paper
     */
    protected function update($subdomain,Request $request){
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
    protected function delete($subdomain,Request $request){
    	$paperId = InputSanitise::inputInt($request->get('paper_id'));
    	if(isset($paperId)){
    		$paper = ClientOnlineTestSubjectPaper::find($paperId);
    		if(is_object($paper)){
                DB::connection('mysql2')->beginTransaction();
                try
                {
                    $loginUser = InputSanitise::getLoginUserByGuardForClient();
                    if($paper->created_by > 0 && $loginUser->id != $paper->created_by){
                        return Redirect::to('manageOnlineTestSubjectPaper');
                    }
                    if('clientuser' == InputSanitise::getCurrentGuard() && 2 != $loginUser->user_type){
                        return Redirect::to('manageOnlineTestSubjectPaper');
                    }
                    if(true == is_object($paper->questions) && false == $paper->questions->isEmpty()){
                        foreach($paper->questions as $question){
                            ClientUserSolution::deleteClientUserSolutionsByQuestionId($question->id);
                            $question->delete();
                        }
                    }
                    ClientScore::deleteScoresByPaperId($paper->id);
                    ClientOnlinePaperSection::deleteClientPaperSectionsByClientIdByPaperId($paper->client_id,$paper->id);
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

    protected function getOnlinePapersBySubjectIdWithPayable(Request $request){
        if($request->ajax()){
            $subjectId = InputSanitise::inputInt($request->get('subjectId'));
            $categoryId = $request->get('categoryId');
            if($categoryId > 0){
                return ClientOnlineTestSubjectPaper::getOnlinePapersBySubjectId($subjectId);
            } else {
                return ClientOnlineTestSubjectPaper::getPayablePapersBySubjectId($subjectId);
            }
        }
    }

    protected function paperSectionsByPaperId(Request $request){
        $resultArr = InputSanitise::getClientIdAndCretedBy();
        $clientId = $resultArr[0];
        return ClientOnlinePaperSection::paperSectionsByPaperId($request->paper_id, $clientId);
    }

    protected function isClientTestPaperExist(Request $request){
        return ClientOnlineTestSubjectPaper::isClientTestPaperExist($request);
    }
}