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
use App\Models\ClientUserSolution;

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
		if(is_object(Auth::guard('clientuser')->user())){
            $clientResult = InputSanitise::checkUserClient($request, Auth::guard('clientuser')->user());
            if( !is_object($clientResult)){
                return Redirect::away($clientResult);
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
     *  return sub categories by categoryId
     */
    public function getOnlineTestSubcategoriesWithPapers(Request $request){
        if($request->ajax()){
            $id = InputSanitise::inputInt($request->get('id'));
            return ClientOnlineTestSubCategory::getOnlineTestSubcategoriesByCategoryIdWithPapers($id, $request);
        }
    }

    /**
	 *	show tests by categoryId by sub categoryId
	 */
	protected function getTest( $subdomain,$id,Request $request,$subject=NULL,$paper=NULL){
		$subcatId = json_decode($id);
		$testSubjectPaperIds = [];
		$isTestSubCategoryPurchased = 'false';
		if(is_object(Auth::guard('clientuser')->user())){
            $clientResult = InputSanitise::checkUserClient($request, Auth::guard('clientuser')->user());
            if( !is_object($clientResult)){
                return Redirect::away($clientResult);
            }
        }
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
        if(!is_object(Auth::guard('clientuser')->user())){
        	return Redirect::to('/');
        }
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
        $userAnswers = [];
        $positiveMarks = 0;
        $negativeMarks = 0;
        $score = ClientScore::getClientUserTestResultByCategoryIdBySubcategoryIdBySubjectIdByPaperId($categoryId,$subcategoryId,$paperId,$subjectId,$userId);

        if(is_object($score)){
        	$rank =ClientScore::getClientUserTestRankByCategoryIdBySubcategoryIdBySubjectIdByPaperIdByTestScore($categoryId,$subcategoryId,$subjectId, $paperId,$score->test_score);
        	$totalRank =ClientScore::getClientUserTestTotalRankByCategoryIdBySubcategoryIdBySubjectIdByPaperId($categoryId,$subcategoryId,$subjectId, $paperId);

        	$userSolutions = ClientUserSolution::getClientUserSolutionsByUserIdByscoreIdByBubjectIdByPaperId($userId, $score->id, $subjectId, $paperId);
        	if(is_object($userSolutions) && false == $userSolutions->isEmpty()){
        		foreach($userSolutions as $userSolution){
        			$userAnswers[$userSolution->ques_id] = $userSolution->user_answer;
        		}
        	}
        	$questions = ClientOnlineTestQuestion::getClientQuestionsByCategoryIdBySubcategoryIdBySubjectIdByPaperId($categoryId, $subcategoryId, $subjectId, $paperId, $request);
        	if(is_object($questions) && false == $questions->isEmpty()){
	        	foreach($questions as $question){
	        		if(isset($userAnswers[$question->id])){
	        			$totalMarks += $question->positive_marks;
		        		if($question->answer == $userAnswers[$question->id] && $question->question_type == 1){
		                    $positiveMarks = (float) $positiveMarks + (float) $question->positive_marks;
		                } else if($userAnswers[$question->id] >= $question->min && $userAnswers[$question->id] <= $question->max && $question->question_type == 0){
		                    $positiveMarks = (float) $positiveMarks + (float) $question->positive_marks;
		                } else if($userAnswers[$question->id]=='unsolved' || $userAnswers[$question->id] =='' ){
		                	continue;
		                } else {
		                    $negativeMarks =  (float) $negativeMarks + (float) $question->negative_marks;
		                }
		            }
	        	}
	        }
        	$result = [];
            $result['category_id'] = (int) $categoryId;
            $result['subcat_id'] = (int) $subcategoryId;
            $result['subject_id'] = (int) $subjectId;
            $result['paper_id'] = (int) $paperId;
            $result['right_answered'] = $score->right_answered;
            $result['wrong_answered'] = $score->wrong_answered;
            $result['unanswered'] = $score->unanswered;
            $result['marks'] = $score->test_score;

            if($totalRank > 0){
                $percentile = ceil(((($totalRank + 1) - ($rank +1) )/ $totalRank)*100);
            } else {
                $percentile = 0;
            }
            $percentage = ceil(($score->test_score/$totalMarks)*100);
            if(($score->right_answered + $score->wrong_answered) > 0){
                $accuracy =  ceil(($score->right_answered/($score->right_answered + $score->wrong_answered))*100);
            } else {
                $accuracy = 0;
            }

	        return view('client.front.onlineTests.user_test_result', compact('score', 'rank', 'totalMarks', 'totalRank', 'percentile', 'percentage', 'accuracy','result','positiveMarks','negativeMarks'));
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