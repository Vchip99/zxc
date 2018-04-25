<?php

namespace App\Http\Controllers\Client\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\Client\ClientHomeController;
use Redirect,Validator, Session, Auth, View, DB,Cache;
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
use App\Models\PayableClientSubCategory;

class ClientOnlineTestFrontController extends ClientHomeController
{
	protected function tests($subdomainName,Request $request){
		$purchasedSubCategories = [] ;
		$payableTestCategories = [];
		$categoryIds = [];
		$subdomain = InputSanitise::checkDomain($request);

		if(!is_object($subdomain)){
			if('local' == \Config::get('app.env')){
                return Redirect::away('http://localvchip.com');
            } else {
                return Redirect::away('https://vchipedu.com/');
            }
		}
		$loginUser = Auth::guard('clientuser')->user();
		if(is_object($loginUser)){
            $clientResult = InputSanitise::checkUserClient($request, $loginUser);
            if( !is_object($clientResult)){
                return Redirect::away($clientResult);
            }
        }
		view::share('subdomain', $subdomain);

        $testCategories = ClientOnlineTestCategory::getOnlineTestCategoriesAssociatedWithQuestion($request);
        if(is_object($testCategories) && false == $testCategories->isEmpty()){
        	foreach($testCategories as $testCategory){
        		$categoryIds[] = $testCategory->id;
        	}
        }
        $payableTestCategories = ClientOnlineTestCategory::getOnlineTestCategoriesAssociatedWithPayableSubCategory($request,$categoryIds);
        $testSubCategories = ClientOnlineTestSubCategory::showSubCategoriesAssociatedWithQuestion($request);
        if(is_object($loginUser)){
        	$clientId = $loginUser->client_id;
        	$clientUserId = $loginUser->id;
        	$purchasedSubCategories = ClientUserPurchasedTestSubCategory::getUserPurchasedTestSubCategories($clientId, $clientUserId);
        }

        $purchasedPayableSubCategories = [];
        $clientPurchasedSubCategories = [];
        $payableSubCategories = PayableClientSubCategory::getPayableSubCategoryByClientId($subdomain->client_id);

        if(is_object($payableSubCategories) && false == $payableSubCategories->isEmpty()){
            foreach($payableSubCategories as $payableSubCategory){
                $purchasedPayableSubCategories[$payableSubCategory->sub_category_id] = $payableSubCategory;
            }
        }
        $clientPurchasedSubCat = [];
        if(count(array_keys($purchasedPayableSubCategories)) > 0){
        	$clientPurchasedSubCategories = ClientOnlineTestSubCategory::showPayableSubcategoriesByIdesAssociatedWithQuestion(array_keys($purchasedPayableSubCategories));
        	if(is_object($clientPurchasedSubCategories) &&  false == $clientPurchasedSubCategories->isEmpty()){
        		foreach($clientPurchasedSubCategories as $clientPurchasedSubCategory){
        			$clientPurchasedSubCat[$clientPurchasedSubCategory->id] = [
        				'sub_category_id' =>	$clientPurchasedSubCategory->id,
        				'category_id' =>	$purchasedPayableSubCategories[$clientPurchasedSubCategory->id]->category_id,
        				'sub_category' =>	$purchasedPayableSubCategories[$clientPurchasedSubCategory->id]->sub_category,
        				'client_image' =>	$purchasedPayableSubCategories[$clientPurchasedSubCategory->id]->client_image,
        				'image_path' =>	$clientPurchasedSubCategory->image_path,
        				'client_user_price' => $purchasedPayableSubCategories[$clientPurchasedSubCategory->id]->client_user_price,
        			];
        		}
        	}
        }
		return view('client.front.onlineTests.tests', compact('testCategories', 'testSubCategories', 'purchasedSubCategories', 'clientPurchasedSubCat', 'payableTestCategories'));
	}

    /**
     *  return sub categories by categoryId
     */
    public function getOnlineTestSubCategories($subdomainName,Request $request){
        if($request->ajax()){
            $id = InputSanitise::inputInt($request->get('id'));
            return ClientOnlineTestSubCategory::getOnlineTestSubcategoriesByCategoryId($id, $request);
        }
    }

     /**
     *  return sub categories by categoryId
     */
    public function getOnlineTestSubCategoriesForTestResult($subdomainName,Request $request){
        if($request->ajax()){
            $id = InputSanitise::inputInt($request->get('id'));
            $result['subcategories'] = ClientOnlineTestSubCategory::getOnlineTestSubcategoriesByCategoryId($id, $request);
            $subdomain = InputSanitise::checkDomain($request);
            $payableSubCategories = PayableClientSubCategory::getPayableSubCategoryByClientIdByCategoryId($subdomain->client_id, $id);
           	if(is_object($payableSubCategories) && false == $payableSubCategories->isEmpty()){
           		$subCategoryArr = [];
           		foreach($payableSubCategories as $payableSubCategory){
           			$subCategoryArr[] = $payableSubCategory->sub_category_id;
           		}
           		if(count($subCategoryArr) > 0){
           			$result['payableSubCategories'] = ClientOnlineTestSubCategory::getPayableSubcategoriesByIdsWithPapers($subCategoryArr);
           		}
           	} else {
           		$result['payableSubCategories'] = [];
           	}
            return $result;
        }
    }

    /**
     *  return sub categories by categoryId with papers
     */
    public function getOnlineTestSubcategoriesWithPapers($subdomainName,Request $request){
        if($request->ajax()){
            $id = InputSanitise::inputInt($request->get('id'));
            $result['subcategories'] = ClientOnlineTestSubCategory::getOnlineTestSubcategoriesByCategoryIdWithPapers($id, $request);
            $subdomain = InputSanitise::checkDomain($request);
            $payableSubCategories = PayableClientSubCategory::getPayableSubCategoryByClientIdByCategoryId($subdomain->client_id, $id);
           	if(is_object($payableSubCategories) && false == $payableSubCategories->isEmpty()){
           		$subCategoryArr = [];
           		foreach($payableSubCategories as $payableSubCategory){
           			$subCategoryArr[] = $payableSubCategory->sub_category_id;
           		}
           		if(count($subCategoryArr) > 0){
           			$result['payableSubCategories'] = ClientOnlineTestSubCategory::getPayableSubcategoriesByIdsWithPapers($subCategoryArr);
           		}
           	} else {
           		$result['payableSubCategories'] = [];
           	}
            return $result;
        }
    }

    /**
	 *	show tests by categoryId by sub categoryId
	 */
	protected function getTest( $subdomainName,$id,Request $request,$subject=NULL,$paper=NULL){
		$subcatId = json_decode($id);
		$testSubjectPaperIds = [];
        $categoryIds = [];
		$isTestSubCategoryPurchased = 'false';

		$loginUser = Auth::guard('clientuser')->user();
		if(is_object($loginUser)){
            $clientResult = InputSanitise::checkUserClient($request, $loginUser);
            if( !is_object($clientResult)){
                return Redirect::away($clientResult);
            }
        }
		if(isset($subcatId)){
			$subcategory = ClientOnlineTestSubCategory::find($subcatId);
			if(is_object($subcategory)){

				$testCategories = ClientOnlineTestCategory::getOnlineTestCategoriesAssociatedWithQuestion($request);
                if(is_object($testCategories) && false == $testCategories->isEmpty()){
                    foreach($testCategories as $testCategory){
                        $categoryIds[] = $testCategory->id;
                    }
                }
                $payableTestCategories = ClientOnlineTestCategory::getOnlineTestCategoriesAssociatedWithPayableSubCategory($request,$categoryIds);
        		$payableTestSubCategories = [];
				$purchasedPayableSubCategories = [];
				if( 0 == $subcategory->client_id && 0 == $subcategory->category_id){
					$isPayableSubCategory = 'true';
					$subdomain = InputSanitise::checkDomain($request);
					$selectedPayableSubCategory = PayableClientSubCategory::getPayableSubCategoryByClientIdBySubCategoryId($subdomain->client_id, $subcategory->id);
					$catId = $selectedPayableSubCategory->category_id;
					$selectedSubCategory = $selectedPayableSubCategory;
					$payableSubCategories = PayableClientSubCategory::getPayableSubCategoryByClientIdByCategoryId($subdomain->client_id, $catId);

			        if(is_object($payableSubCategories) && false == $payableSubCategories->isEmpty()){
			            foreach($payableSubCategories as $payableSubCategory){
			                $purchasedPayableSubCategories[$payableSubCategory->sub_category_id] = $payableSubCategory;
			            }
			        }
			        if(count(array_keys($purchasedPayableSubCategories)) > 0){
			        	$payableTestSubCategories = ClientOnlineTestSubCategory::showPayableSubcategoriesByIdesAssociatedWithQuestion(array_keys($purchasedPayableSubCategories));
			        }
			        $testSubCategories = ClientOnlineTestSubCategory::getOnlineTestSubcategoriesByCategoryIdAssociatedWithQuestion($catId, $request);
			        $testSubjects = ClientOnlineTestSubject::showPayableSubjectsBySubCategoryIdAssociatedWithQuestion($subcatId);
			        $testSubjectPapers = ClientOnlineTestSubjectPaper::showPayablePapersBySubCategoryIdAssociatedWithQuestion($subcatId);
			        if(is_array($testSubjectPapers)){
						foreach($testSubjectPapers as $testPapers){
							foreach($testPapers as $testPaper){
								$testSubjectPaperIds[] = $testPaper->id;
							}
						}
						$testSubjectPaperIds = array_values($testSubjectPaperIds);
					}
				} else {
					$isPayableSubCategory = 'false';
					$catId = $subcategory->category_id;
					$selectedSubCategory = $subcategory;
					$testSubCategories = ClientOnlineTestSubCategory::getOnlineTestSubcategoriesByCategoryIdAssociatedWithQuestion($catId, $request);

					$payableSubCategories = PayableClientSubCategory::getPayableSubCategoryByClientIdByCategoryId($subcategory->client_id, $catId);

			        if(is_object($payableSubCategories) && false == $payableSubCategories->isEmpty()){
			            foreach($payableSubCategories as $payableSubCategory){
			                $purchasedPayableSubCategories[$payableSubCategory->sub_category_id] = $payableSubCategory;
			            }
			        }
			        if(count(array_keys($purchasedPayableSubCategories)) > 0){
			        	$payableTestSubCategories = ClientOnlineTestSubCategory::showPayableSubcategoriesByIdesAssociatedWithQuestion(array_keys($purchasedPayableSubCategories));
			        }
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
				}
				$currentDate = date('Y-m-d H:i:s');
				if(is_object($loginUser)){
			        $clientId = $loginUser->client_id;
			        $userId = $loginUser->id;
					$registeredPaperIds = $this->getRegisteredPaperIds($subdomainName);
					$alreadyGivenPapers = $this->getClientTestUserScoreByCategoryIdBySubcatIdByPaperIds($catId, $subcatId, $testSubjectPaperIds);

			        $isTestSubCategoryPurchased = ClientUserPurchasedTestSubCategory::isTestSubCategoryPurchased($clientId, $userId, $subcatId);
                    if($subject > 0 && $paper > 0){
                        DB::connection('mysql2')->beginTransaction();
                        try
                        {
                        	$readNotification = ClientReadNotification::readNotificationByModuleByModuleIdByUser(ClientNotification::CLIENTPAPER,$paper,$loginUser->id);
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
                } else {
                	$registeredPaperIds = [];
                	$alreadyGivenPapers = [];
                }


				return view('client.front.onlineTests.show_tests', compact('catId', 'subcatId', 'testCategories','testSubCategories', 'testSubjects','testSubjectPapers', 'registeredPaperIds', 'alreadyGivenPapers', 'currentDate', 'isTestSubCategoryPurchased','subject','paper', 'selectedSubCategory', 'loginUser', 'isPayableSubCategory', 'payableTestSubCategories', 'purchasedPayableSubCategories', 'payableTestCategories'));
			}
		}
		return Redirect::to('/');
	}

	protected function getClientTestUserScoreByCategoryIdBySubcatIdByPaperIds($catId, $subcatId, $testSubjectPaperIds){
		return ClientScore::getClientTestUserScoreByCategoryIdBySubcatIdByPaperIds($catId, $subcatId, $testSubjectPaperIds);
	}

	protected function getOnlineTestSubcategoriesByCategoryIdAssociatedWithQuestion($subdomainName,Request $request){
		if($request->ajax()){
			$result = [];
			$purchasedPayableSubCategories = [];
            $id = InputSanitise::inputInt($request->get('id'));
            $result['sub_categories'] = ClientOnlineTestSubCategory::getOnlineTestSubcategoriesByCategoryIdAssociatedWithQuestion($id, $request);
            $subdomain = InputSanitise::checkDomain($request);
            $payableSubCategories = PayableClientSubCategory::getPayableSubCategoryByClientIdByCategoryId($subdomain->client_id, $id);
	        if(is_object($payableSubCategories) && false == $payableSubCategories->isEmpty()){
	            foreach($payableSubCategories as $payableSubCategory){
	                $purchasedPayableSubCategories[$payableSubCategory->sub_category_id] = $payableSubCategory;
	            }
	        }
	        if(count(array_keys($purchasedPayableSubCategories)) > 0){
	        	$clientPurchasedSubCategories = ClientOnlineTestSubCategory::showPayableSubcategoriesByIdesAssociatedWithQuestion(array_keys($purchasedPayableSubCategories));
	        	if(is_object($clientPurchasedSubCategories) &&  false == $clientPurchasedSubCategories->isEmpty()){
	        		foreach($clientPurchasedSubCategories as $clientPurchasedSubCategory){
	        			$result['clientPurchasedSubCategories'][$clientPurchasedSubCategory->id] = [
	        				'sub_category_id' =>	$clientPurchasedSubCategory->id,
	        				'category_id' =>	$purchasedPayableSubCategories[$clientPurchasedSubCategory->id]->category_id,
	        				'sub_category' =>	$purchasedPayableSubCategories[$clientPurchasedSubCategory->id]->sub_category,
	        				'client_image' =>	$purchasedPayableSubCategories[$clientPurchasedSubCategory->id]->client_image,
	        				'image_path' =>	$clientPurchasedSubCategory->image_path,
	        				'client_user_price' => $purchasedPayableSubCategories[$clientPurchasedSubCategory->id]->client_user_price,
	        			];
	        		}
	        	}
	        } else {
	        	$result['clientPurchasedSubCategories'] = [];
	        }

            $loginUser = Auth::guard('clientuser')->user();
            if(is_object($loginUser)){
            	$clientId = $loginUser->client_id;
            	$clientUserId = $loginUser->id;

	        	$result['purchasedSubCategories'] = ClientUserPurchasedTestSubCategory::getUserPurchasedTestSubCategories($clientId, $clientUserId);
	        } else {
	        	$result['purchasedSubCategories'] = [];
	        }
        	return $result;
        }
	}

	protected function getOnlineSubjectsAndPapersByCatIdBySubcatIdAssociatedWithQuestion($subdomainName, Request $request){
		if($request->ajax()){
			$result = [];
			$testSubjectPaperIds = [];
			$isTestSubCategoryPurchased = 'false';
			$catId = InputSanitise::inputInt($request->get('cat'));
			$subcatId = InputSanitise::inputInt($request->get('subcat'));
			$loginUser = Auth::guard('clientuser')->user();
			$subcategory = ClientOnlineTestSubCategory::find($subcatId);
			if( is_object($subcategory) && 0 == $subcategory->client_id && 0 == $subcategory->category_id){
				$result['subjects'] = ClientOnlineTestSubject::showPayableSubjectsBySubCategoryIdAssociatedWithQuestion($subcatId);

		        $result['papers'] = ClientOnlineTestSubjectPaper::showPayablePapersBySubCategoryIdAssociatedWithQuestion($subcatId);
			} else {
				$result['subjects']  = ClientOnlineTestSubject::getOnlineSubjectsByCatIdBySubcatIdWithQuestion($catId, $subcatId, $request);

				$result['papers'] = ClientOnlineTestSubjectPaper::getOnlineSubjectPapersByCatIdBySubCatIdWithQuestion($catId, $subcatId, $request);
			}

			if(is_object($loginUser)){
		        $clientId = $loginUser->client_id;
		        $userId = $loginUser->id;
				$result['registeredPaperIds'] = $this->getRegisteredPaperIds($subdomainName);
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
			} else {
				$result['registeredPaperIds'] = [];
			}
			return $result;
		}
	}

	protected function getRegisteredSubjectsAndPapersByCatIdBySubcatId($subdomainName, Request $request){
		if($request->ajax()){
			$result = [];
			$testSubjectPaperIds = [];
			$assignedTestSubjectPapersIds = [];
			$catId = InputSanitise::inputInt($request->get('cat'));
			$subcatId = InputSanitise::inputInt($request->get('subcat'));
			$userId = InputSanitise::inputInt($request->get('userId'));
			$subcategory = ClientOnlineTestSubCategory::find($subcatId);

			if( is_object($subcategory) && 0 == $subcategory->client_id && 0 == $subcategory->category_id){
				$result['subjects'] = ClientOnlineTestSubject::showPayableSubjectsBySubCategoryIdAssociatedWithQuestion($subcatId);

		        $result['papers'] = ClientOnlineTestSubjectPaper::showPayablePapersBySubCategoryIdAssociatedWithQuestion($subcatId);
			} else {

				$result['subjects']  = ClientOnlineTestSubject::getOnlineSubjectsByCatIdBySubcatIdWithQuestion($catId, $subcatId, $request);

				$result['papers'] = ClientOnlineTestSubjectPaper::getOnlineSubjectPapersByCatIdBySubCatIdWithQuestion($catId, $subcatId, $request);
			}
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

    protected function getRegisteredPaperIds($subdomainName){
    	$registeredPaperIds = [];
    	$loginUser = Auth::guard('clientuser')->user();
    	if(is_object($loginUser)){
			$userId = $loginUser->id;
			$clientId = $loginUser->client_id;
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

    protected function showUserTestResult($subdomainName,Request $request){
        $subcategoryId = $request->get('subcategory_id');
        $subjectId = $request->get('subject_id');
        $paperId = $request->get('paper_id');
        $loginUser = Auth::guard('clientuser')->user();
        $userId = $loginUser->id;
        $clientId = $loginUser->client_id;
        $totalMarks = 0 ;
        $userAnswers = [];
        $positiveMarks = 0;
        $negativeMarks = 0;
    	$selectedPayableSubCategory = PayableClientSubCategory::getPayableSubCategoryByClientIdBySubCategoryId($clientId, $subcategoryId);
    	if(is_object($selectedPayableSubCategory)){
    		$categoryId = $selectedPayableSubCategory->category_id;
    	} else {
    		$categoryId = $request->get('category_id');
    	}
        $score = ClientScore::getClientUserTestResultByCategoryIdBySubcategoryIdBySubjectIdByPaperId($categoryId,$subcategoryId,$paperId,$subjectId,$userId);

        if(is_object($score)){
        	$rank = ClientScore::getClientUserTestRankByCategoryIdBySubcategoryIdBySubjectIdByPaperIdByTestScore($categoryId,$subcategoryId,$subjectId, $paperId,$score->test_score);
        	$totalRank = ClientScore::getClientUserTestTotalRankByCategoryIdBySubcategoryIdBySubjectIdByPaperId($categoryId,$subcategoryId,$subjectId, $paperId);

        	$userSolutions = ClientUserSolution::getClientUserSolutionsByUserIdByscoreIdByBubjectIdByPaperId($userId, $score->id, $subjectId, $paperId);
        	if(is_object($userSolutions) && false == $userSolutions->isEmpty()){
        		foreach($userSolutions as $userSolution){
        			$userAnswers[$userSolution->ques_id] = $userSolution->user_answer;
        		}
        	}
        	if(is_object($selectedPayableSubCategory)){
        		$questions = ClientOnlineTestQuestion::getClientQuestionsByCategoryIdBySubcategoryIdBySubjectIdByPaperId(0, $subcategoryId, $subjectId, $paperId, $request);
        	} else {
        		$questions = ClientOnlineTestQuestion::getClientQuestionsByCategoryIdBySubcategoryIdBySubjectIdByPaperId($categoryId, $subcategoryId, $subjectId, $paperId, $request);
        	}
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

    protected function isTestGiven($subdomainName,Request $request){
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