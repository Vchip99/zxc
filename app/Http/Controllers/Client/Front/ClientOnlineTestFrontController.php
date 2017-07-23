<?php

namespace App\Http\Controllers\Client\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\Client\ClientHomeController;
use Redirect;
use Validator, Session, Auth, View;
use App\Libraries\InputSanitise;
use App\Models\ClientOnlineTestCategory;
use App\Models\ClientOnlineTestSubCategory;
use App\Models\ClientOnlineTestSubject;
use App\Models\ClientOnlineTestSubjectPaper;
use App\Models\Client;
use App\Models\RegisterClientOnlinePaper;
use App\Models\ClientHomePage;
use App\Models\ClientScore;
use App\Models\ClientOnlineTestQuestion;

class ClientOnlineTestFrontController extends ClientHomeController
{
	protected function tests(Request $request){
		$subdomain = InputSanitise::checkDomain($request);
		if(!is_object($subdomain)){
			return Redirect::away('http://localvchip.com');
		}
		view::share('subdomain', $subdomain);
        $testCategories = ClientOnlineTestCategory::getOnlineTestCategoriesAssociatedWithQuestion($request);
        $testSubCategories = ClientOnlineTestSubCategory::showSubCategoriesAssociatedWithQuestion($request);

		return view('client.front.onlineTests.tests', compact('testCategories', 'testSubCategories'));
	}

    /**
     *  return sub categories by categoryId
     */
    public function getOnlineTestSubCategories(Request $request){
        if($request->ajax()){
            $id = InputSanitise::inputInt($request->get('id'));
            return ClientOnlineTestSubCategory::getOnlineTestSubcategoriesByCategoryId($id, $request);
        }
    }

    /**
	 *	show tests by categoryId by sub categoryId
	 */
	protected function getTest( $subdomain,$id, Request $request){
		$subcatId = json_decode($id);
		$testSubjectPaperIds = [];
		if(isset($subcatId)){
			$subcategory = ClientOnlineTestSubCategory::find($subcatId);
			if(is_object($subcategory)){
				$catId = $subcategory->category_id;
				$testCategories = ClientOnlineTestCategory::getOnlineTestCategoriesAssociatedWithQuestion($request);
				$testSubCategories = ClientOnlineTestSubCategory::getOnlineTestSubcategoriesByCategoryIdAssociatedWithQuestion($catId, $request);
				$testSubjects = ClientOnlineTestSubject::getOnlineSubjectsByCatIdBySubcatIdWithQuestion($catId, $subcatId, $request);
				$testSubjectPapers = ClientOnlineTestSubjectPaper::getOnlineSubjectPapersByCatIdBySubCatIdWithQuestion($catId, $subcatId, $request);
				if(is_array($testSubjectPapers)){
					foreach($testSubjectPapers as $testPapers){
						foreach($testPapers as $testPaper){
							$testSubjectPaperIds[] = $testPaper->id;
						}
					}
					$testSubjectPaperIds = array_values($testSubjectPaperIds);
				}
				$registeredPaperIds = $this->getRegisteredPaperIds();
				$alreadyGivenPapers = $this->getClientTestUserScoreByCategoryIdBySubcatIdByPaperIds($catId, $subcatId, $testSubjectPaperIds);
				$currentDate = date('Y-m-d');
				return view('client.front.onlineTests.show_tests', compact('catId', 'subcatId', 'testCategories','testSubCategories', 'testSubjects','testSubjectPapers', 'registeredPaperIds', 'alreadyGivenPapers', 'currentDate'));
			}
		}
		return Redirect::to('/');
	}

	protected function getClientTestUserScoreByCategoryIdBySubcatIdByPaperIds($catId, $subcatId, $testSubjectPaperIds){
		return ClientScore::getClientTestUserScoreByCategoryIdBySubcatIdByPaperIds($catId, $subcatId, $testSubjectPaperIds);
	}

	protected function getOnlineTestSubcategoriesByCategoryIdAssociatedWithQuestion(Request $request){
		if($request->ajax()){
            $id = InputSanitise::inputInt($request->get('id'));
            return ClientOnlineTestSubCategory::getOnlineTestSubcategoriesByCategoryIdAssociatedWithQuestion($id, $request);
        }
	}

	protected function getOnlineSubjectsAndPapersByCatIdBySubcatIdAssociatedWithQuestion(Request $request){
		if($request->ajax()){
			$result = [];
			$testSubjectPaperIds = [];
			$catId = InputSanitise::inputInt($request->get('cat'));
			$subcatId = InputSanitise::inputInt($request->get('subcat'));
			$result['subjects'] = ClientOnlineTestSubject::getOnlineSubjectsByCatIdBySubcatIdWithQuestion($catId, $subcatId, $request);
			$result['papers'] = ClientOnlineTestSubjectPaper::getOnlineSubjectPapersByCatIdBySubCatIdWithQuestion($catId, $subcatId, $request);

			$result['registeredPaperIds'] = $this->getRegisteredPaperIds();
			if(is_object(Auth::guard('clientuser')->user())){
				if(is_array($result['papers'])){
					foreach($result['papers'] as $testPapers){
						foreach($testPapers as $testPaper){
							$testSubjectPaperIds[] = $testPaper->id;
						}
					}
					$testSubjectPaperIds = array_values($testSubjectPaperIds);
				}

				$result['alreadyGivenPapers'] = $this->getClientTestUserScoreByCategoryIdBySubcatIdByPaperIds($catId, $subcatId, $testSubjectPaperIds);
				$result['currentDate'] = date('Y-m-d');
			}
			return $result;
		}
	}


	/**
	 *	set sessions
	 */
	protected function setClientUserSessions(Request $request){
		if($request->ajax()){
			$paper = $request->get('paper');
			$subject = $request->get('subject');
			$category = $request->get('category');
			$subcategory = $request->get('subcategory');

			Session::put('client_paper', $paper);
			Session::put('client_subject', $subject);
        	Session::put('client_category', $category);
        	Session::put('client_subcategory', $subcategory);
        	return "true";
		} else {
			return Redirect::back();
		}
	}

	/**
	 *	show instructions
	 */
	public function showInstructions(){
		$categoryId = Session::get('client_category');
        $subcategoryId = Session::get('client_subcategory');
        $subjectId = Session::get('client_subject');
        $paperId = Session::get('client_paper');
        return view('client.front.instructions', compact('categoryId', 'subcategoryId', 'subjectId', 'paperId'));
    }

    protected function getRegisteredPaperIds(){
    	$registeredPaperIds = [];
    	if(is_object(Auth::guard('clientuser')->user())){
			$userId = Auth::guard('clientuser')->user()->id;
			$clientId = Auth::guard('clientuser')->user()->client_id;
			$registeredPapers = RegisterClientOnlinePaper::getRegisteredPapersByUserId($userId, $clientId);
			if( is_object($registeredPapers) && false == $registeredPapers->isEmpty()){
				foreach($registeredPapers as $registeredPaper){
					$registeredPaperIds[] = $registeredPaper->client_paper_id;
				}
			}
		}
		return $registeredPaperIds;
    }

    protected function registerClientUserPaper(Request $request){
    	return RegisterClientOnlinePaper::registerTestPaper($request);
    }

    protected function showUserTestResult(Request $request){
    	$categoryId = $request->get('category_id');
        $subcategoryId = $request->get('subcategory_id');
        $subjectId = $request->get('subject_id');
        $paperId = $request->get('paper_id');
        $userId = Auth::guard('clientuser')->user()->id;
        $totalMarks = 0 ;
        $score = ClientScore::getClientUserTestResultByCategoryIdBySubcategoryIdBySubjectIdByPaperId($categoryId,$subcategoryId,$paperId,$subjectId,$userId);

        if(is_object($score)){
        	$rank =ClientScore::getClientUserTestRankByCategoryIdBySubcategoryIdBySubjectIdByPaperIdByTestScore($categoryId,$subcategoryId,$subjectId, $paperId,$score->test_score);
        	$totalRank =ClientScore::getClientUserTestTotalRankByCategoryIdBySubcategoryIdBySubjectIdByPaperId($categoryId,$subcategoryId,$subjectId, $paperId);
        	$questions = ClientOnlineTestQuestion::getClientQuestionsByCategoryIdBySubcategoryIdBySubjectIdByPaperId($categoryId, $subcategoryId, $subjectId, $paperId, $request);
        	foreach($questions as $question){
        		$totalMarks += $question->positive_marks;
        	}
	        return view('client.front.onlineTests.user_test_result', compact('score', 'rank', 'totalMarks', 'totalRank'));
        } else {
    		return Redirect::to('/');
        }
    }

    protected function isTestGiven(Request $request){
    	$categoryId = $request->get('category');
        $subcategoryId = $request->get('subcategory');
        $subjectId = $request->get('subject');
        $paperId = $request->get('paper');
        $userId = Auth::guard('clientuser')->user()->id;

        $score = ClientScore::getClientUserTestResultByCategoryIdBySubcategoryIdBySubjectIdByPaperId($categoryId,$subcategoryId,$paperId,$subjectId,$userId);
        if(is_object($score)){
        	return 'true';
        }else{
        	return 'false';
        }
    }

}