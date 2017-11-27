<?php

namespace App\Http\Controllers\Client\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\Client\ClientHomeController;
use Redirect,Validator, Session, Auth, View, DB;
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
use App\Models\ClientNotification;
use App\Models\ClientReadNotification;
use App\Models\ClientUserPurchasedTestSubCategory;

class ClientOnlineTestFrontController extends ClientHomeController
{
	protected function tests(Request $request){
		$purchasedSubCategories = [] ;
		$subdomain = InputSanitise::checkDomain($request);
		if(!is_object($subdomain)){
			if('local' == \Config::get('app.env')){
                return Redirect::away('http://localvchip.com');
            } else {
                return Redirect::away('https://vchipedu.com/');
            }
		}
		view::share('subdomain', $subdomain);

        $testCategories = ClientOnlineTestCategory::getOnlineTestCategoriesAssociatedWithQuestion($request);
        $testSubCategories = ClientOnlineTestSubCategory::showSubCategoriesAssociatedWithQuestion($request);

        if(is_object(Auth::guard('clientuser')->user())){
        	$purchasedSubCategories = ClientUserPurchasedTestSubCategory::getUserPurchasedTestSubCategories(Auth::guard('clientuser')->user()->client_id, Auth::guard('clientuser')->user()->id);
        }
		return view('client.front.onlineTests.tests', compact('testCategories', 'testSubCategories', 'purchasedSubCategories'));
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
	protected function getTest( $subdomain,$id,Request $request,$subject=NULL,$paper=NULL){
		$subcatId = json_decode($id);
		$testSubjectPaperIds = [];
		$isTestSubCategoryPurchased = 'false';
		if(isset($subcatId)){
			$subcategory = ClientOnlineTestSubCategory::find($subcatId);
			if(is_object($subcategory)){
				$catId = $subcategory->category_id;
				$selectedSubCategory = $subcategory;
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
				$currentDate = date('Y-m-d H:i:s');
				if(is_object(Auth::guard('clientuser')->user())){
			        $clientId = Auth::guard('clientuser')->user()->client_id;
			        $userId = Auth::guard('clientuser')->user()->id;
			        $isTestSubCategoryPurchased = ClientUserPurchasedTestSubCategory::isTestSubCategoryPurchased($clientId, $userId, $subcategory->id);
                    $currentUser = Auth::guard('clientuser')->user()->id;
                    if($subject > 0 && $paper > 0){
                        DB::connection('mysql2')->beginTransaction();
                        try
                        {
                        	$readNotification = ClientReadNotification::readNotificationByModuleByModuleIdByUser(ClientNotification::CLIENTPAPER,$paper,$currentUser);
                            if(is_object($readNotification)){
                                DB::connection('mysql2')->commit();
                            }
                        }
                        catch(\Exception $e)
                        {
                            DB::connection('mysql2')->rollback();
                            return redirect()->back()->withErrors('something went wrong.');
                        }
                    }
                }


				return view('client.front.onlineTests.show_tests', compact('catId', 'subcatId', 'testCategories','testSubCategories', 'testSubjects','testSubjectPapers', 'registeredPaperIds', 'alreadyGivenPapers', 'currentDate', 'isTestSubCategoryPurchased','subject','paper', 'selectedSubCategory'));
			}
		}
		return Redirect::to('/');
	}

	protected function getClientTestUserScoreByCategoryIdBySubcatIdByPaperIds($catId, $subcatId, $testSubjectPaperIds){
		return ClientScore::getClientTestUserScoreByCategoryIdBySubcatIdByPaperIds($catId, $subcatId, $testSubjectPaperIds);
	}

	protected function getOnlineTestSubcategoriesByCategoryIdAssociatedWithQuestion(Request $request){
		if($request->ajax()){
			$result = [];
            $id = InputSanitise::inputInt($request->get('id'));
            $result['sub_categories'] = ClientOnlineTestSubCategory::getOnlineTestSubcategoriesByCategoryIdAssociatedWithQuestion($id, $request);
            if(is_object(Auth::guard('clientuser')->user())){
	        	$result['purchasedSubCategories'] = ClientUserPurchasedTestSubCategory::getUserPurchasedTestSubCategories(Auth::guard('clientuser')->user()->client_id, Auth::guard('clientuser')->user()->id);
	        } else {
	        	$result['purchasedSubCategories'] = [];
	        }
        	return $result;
        }
	}

	protected function getOnlineSubjectsAndPapersByCatIdBySubcatIdAssociatedWithQuestion(Request $request){
		if($request->ajax()){
			$result = [];
			$testSubjectPaperIds = [];
			$isTestSubCategoryPurchased = 'false';
			$catId = InputSanitise::inputInt($request->get('cat'));
			$subcatId = InputSanitise::inputInt($request->get('subcat'));
			$result['subjects'] = ClientOnlineTestSubject::getOnlineSubjectsByCatIdBySubcatIdWithQuestion($catId, $subcatId, $request);
			$result['papers'] = ClientOnlineTestSubjectPaper::getOnlineSubjectPapersByCatIdBySubCatIdWithQuestion($catId, $subcatId, $request);

			$result['registeredPaperIds'] = $this->getRegisteredPaperIds();
			if(is_object(Auth::guard('clientuser')->user())){
		        $clientId = Auth::guard('clientuser')->user()->client_id;
		        $userId = Auth::guard('clientuser')->user()->id;
		       	$result['isTestSubCategoryPurchased'] = ClientUserPurchasedTestSubCategory::isTestSubCategoryPurchased($clientId, $userId, $subcatId);

				if(is_array($result['papers'])){
					foreach($result['papers'] as $testPapers){
						foreach($testPapers as $testPaper){
							$testSubjectPaperIds[] = $testPaper->id;
						}
					}
					$testSubjectPaperIds = array_values($testSubjectPaperIds);
				}

				$result['alreadyGivenPapers'] = $this->getClientTestUserScoreByCategoryIdBySubcatIdByPaperIds($catId, $subcatId, $testSubjectPaperIds);
				$result['currentDate'] = date('Y-m-d H:i:s');
			}
			return $result;
		}
	}

	protected function getRegisteredSubjectsAndPapersByCatIdBySubcatId(Request $request){
		if($request->ajax()){
			$result = [];
			$testSubjectPaperIds = [];
			$assignedTestSubjectPapersIds = [];
			$catId = InputSanitise::inputInt($request->get('cat'));
			$subcatId = InputSanitise::inputInt($request->get('subcat'));
			$userId = InputSanitise::inputInt($request->get('userId'));
			$result['subjects'] = ClientOnlineTestSubject::getOnlineSubjectsByCatIdBySubcatIdWithQuestion($catId, $subcatId, $request);
			$result['papers'] = ClientOnlineTestSubjectPaper::getRegisteredPapersByCatIdBySubCatId($catId, $subcatId, $userId);
			if(is_array($result['papers'])){
				foreach($result['papers'] as $testPapers){
					foreach($testPapers as $testPaper){
						$testSubjectPaperIds[] = $testPaper->id;
					}
				}
				$testSubjectPaperIds = array_values($testSubjectPaperIds);
			}

			$result['alreadyGivenPapers'] = $this->getClientTestUserScoreByCategoryIdBySubcatIdByPaperIds($catId, $subcatId, $testSubjectPaperIds);
			$result['currentDate'] = date('Y-m-d H:i:s');

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
    	$userId = $request->get('user_id');
    	$paperId = $request->get('paper_id');
    	return RegisterClientOnlinePaper::registerTestPaper($userId, $paperId);
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