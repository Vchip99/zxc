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
use App\Models\ClientUserSolution;
use App\Models\ClientScore;
use App\Models\ClientOnlinePaperSection;

class ClientOnlineTestSubjectController extends ClientBaseController
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
    protected $validateCreateSubject = [
        'category' => 'required|integer',
        'subcategory' => 'required|integer',
        'name' => 'required|string',
    ];

    /**
     *	show all subjects
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
		$testSubjects = ClientOnlineTestSubject::showSubjects($request);
		return view('client.onlineTest.subject.list', compact('testSubjects', 'subdomainName','loginUser'));
	}

	/**
	 *	show create UI for subject
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
        $resultArr = InputSanitise::getClientIdAndCretedBy();
        $clientId = $resultArr[0];
		$testCategories    = ClientOnlineTestCategory::where('client_id', $clientId)->get();
		$testSubCategories = new ClientOnlineTestSubCategory;
		$subject = new ClientOnlineTestSubject;
		return view('client.onlineTest.subject.create', compact('testCategories','testSubCategories','subject', 'subdomainName','loginUser'));
	}

	/**
	 *	store subject
	 */
	protected function store($subdomain,Request $request){
		$v = Validator::make($request->all(), $this->validateCreateSubject);

        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::connection('mysql2')->beginTransaction();
        try
        {
	        $testSubject = ClientOnlineTestSubject::addOrUpdateSubject($request);
	        if(is_object($testSubject)){
	        	DB::connection('mysql2')->commit();
	            return Redirect::to('manageOnlineTestSubject')->with('message', 'Subject created successfully!');
	        }
	    }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('manageOnlineTestSubject');
	}

	/**
	 *	edit subject
	 */
	protected function edit( $subdomainName, $id, Request $request){
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
		$id = InputSanitise::inputInt(json_decode($id));
		if(isset($id)){
			$subject = ClientOnlineTestSubject::find($id);
			if(is_object($subject)){
				$testCategories    = ClientOnlineTestCategory:: showCategories($request);
				$testSubCategories = ClientOnlineTestSubCategory::getOnlineTestSubcategoriesByCategoryId($subject->category_id, $request);
				return view('client.onlineTest.subject.create', compact('testCategories','testSubCategories','subject', 'subdomainName','loginUser'));
			}
		}
		return Redirect::to('manageOnlineTestSubject');
	}

	/**
	 *	update subject
	 */
	protected function update($subdomain,Request $request){
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
				$testSubject = ClientOnlineTestSubject::addOrUpdateSubject($request, true);
		        if(is_object($testSubject)){
		        	DB::connection('mysql2')->commit();
		            return Redirect::to('manageOnlineTestSubject')->with('message', 'Subject updated successfully!');
		        }
		    }
	        catch(\Exception $e)
	        {
	            DB::connection('mysql2')->rollback();
	            return redirect()->back()->withErrors('something went wrong.');
	        }
		}
		return Redirect::to('manageOnlineTestSubject');
	}

	/**
	 *	delete subject
	 */
	protected function delete($subdomain,Request $request){
		$subjectId = InputSanitise::inputInt($request->get('subject_id'));
		if(isset($subjectId)){
			$testSubject = ClientOnlineTestSubject::find($subjectId);
			if( isset($testSubject)){
				DB::connection('mysql2')->beginTransaction();
		        try
		        {
		        	$loginUser = InputSanitise::getLoginUserByGuardForClient();
                    if($testSubject->created_by > 0 && $loginUser->id != $testSubject->created_by){
                        return Redirect::to('manageOnlineTestSubject');
                    }
                    if('clientuser' == InputSanitise::getCurrentGuard() && 2 != $loginUser->user_type){
                        return Redirect::to('manageOnlineTestSubject');
                    }
		        	if(true == is_object($testSubject->papers) && false == $testSubject->papers->isEmpty()){
		        		foreach($testSubject->papers as $paper){
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
		        		}
		        	}
					$testSubject->delete();
					DB::connection('mysql2')->commit();
					return Redirect::to('manageOnlineTestSubject')->with('message', 'Subject deleted successfully!');
				}
		        catch(\Exception $e)
		        {
		            DB::connection('mysql2')->rollback();
		            return redirect()->back()->withErrors('something went wrong.');
		        }
			}
		}
		return Redirect::to('manageOnlineTestSubject');
	}

	protected static function getOnlineSubjectsByCatIdBySubcatId(Request $request){
		if($request->ajax()){
    		$catId = InputSanitise::inputInt($request->get('catId'));
    		$subcatId = InputSanitise::inputInt($request->get('subcatId'));
    		if($catId > 0){
				return ClientOnlineTestSubject::getOnlineSubjectsByCatIdBySubcatId($catId, $subcatId, $request);
    		} else {
    			return ClientOnlineTestSubject::getPayableSubjectsBySubcatId($subcatId);
    		}
    	}
    	return Redirect::to('manageOnlineTestSubject');
	}

	protected function isClientTestSubjectExist(Request $request){
		return ClientOnlineTestSubject::isClientTestSubjectExist($request);
	}
}