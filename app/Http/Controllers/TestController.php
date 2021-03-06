<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TestCategory;
use App\Models\TestSubCategory;
use App\Models\TestSubject;
use App\Models\TestSubjectPaper;
use App\Models\RegisterPaper;
use App\Models\Score;
use App\Models\Question;
use App\Models\Notification;
use App\Models\ReadNotification;
use App\Models\UserSolution;
use Session, Redirect, Auth, DB,Cache;
use App\Models\Add;
use App\Models\Rating;
use App\Models\User;

class TestController extends Controller
{
	/**
	 *	show test sub categories by categoryId
	 */
	public function index(Request $request){
		$testCategories = Cache::remember('vchip:tests:testCategoriesWithQuestions',60, function() {
            return TestCategory::getTestCategoriesAssociatedWithQuestion();
        });
        if(is_object(Auth::user()) && 'ceo@vchiptech.com' == Auth::user()->email){
        	$testSubCategories = TestSubCategory::getTestSubCategoriesAssociatedWithQuestion();
        } else {
			$testSubCategories = Cache::remember('vchip:tests:testSubCategories',60, function() {
	            return TestSubCategory::getTestSubCategoriesAssociatedWithQuestion();
	        });
        }

		$catId = 0;
		$date = date('Y-m-d');
        $ads = Add::getAdds($request->url(),$date);
        $reviewData = [];
        $ratingUsers = [];
        $userNames = [];
        $allRatings = Rating::getRatingsByModuleType(Rating::SubCategory);
        if(is_object($allRatings) && false == $allRatings->isEmpty()){
            foreach($allRatings as $rating){
                $reviewData[$rating->module_id]['rating'][$rating->user_id] = [ 'rating' => $rating->rating,'review' => $rating->review, 'review_id' => $rating->id, 'updated_at' => $rating->updated_at->diffForHumans()];
                $ratingUsers[] = $rating->user_id;
            }
            foreach($reviewData as $dataId => $rating){
                $ratingSum = 0.0;
                foreach($rating as $userRatings){
                    foreach($userRatings as $userId => $userRating){
                        $ratingSum = (double) $ratingSum + (double) $userRating['rating'];
                    }
                    $reviewData[$dataId]['avg']  = $ratingSum/count($userRatings);
                }
            }
        }
        if(count($ratingUsers) > 0){
            $users = User::find($ratingUsers);
            if(is_object($users) && false == $users->isEmpty()){
                foreach($users as $user){
                    $userNames[$user->id] = [ 'name' => $user->name,'photo' => $user->photo];
                }
            }
        }
        $purchasedSubCategories = $this->getRegisteredPaperIds(true);
		return view('tests.test_info', compact('catId','testCategories', 'testSubCategories', 'ads','reviewData','userNames','purchasedSubCategories'));
	}

	/**
	 *	show test info by categoryId
	 */
	protected function showTest(Request $request,$id){
		$catId = json_decode($id);
		if(isset($catId)){
			$category = Cache::remember('vchip:tests:testCategory-'.$catId,30, function() use ($catId) {
	            return TestCategory::find($catId);
	        });
			if(is_object($category)){
				$testCategories = Cache::remember('vchip:tests:testCategoriesWithQuestions',60, function() {
		            return TestCategory::getTestCategoriesAssociatedWithQuestion();
		        });
		        if(is_object(Auth::user()) && 'ceo@vchiptech.com' == Auth::user()->email){
		        	$testSubCategories = TestSubCategory::getSubcategoriesByCategoryId($catId);
		        } else {
					$testSubCategories = Cache::remember('vchip:tests:testSubCategories:cat-'.$catId,30, function() use ($catId) {
			            return TestSubCategory::getSubcategoriesByCategoryId($catId);
			        });
			    }
				$date = date('Y-m-d');
        		$ads = Add::getAdds($request->url(),$date);
        		$reviewData = [];
		        $ratingUsers = [];
		        $userNames = [];
		        $allRatings = Rating::getRatingsByModuleType(Rating::SubCategory);
		        if(is_object($allRatings) && false == $allRatings->isEmpty()){
		            foreach($allRatings as $rating){
		                $reviewData[$rating->module_id]['rating'][$rating->user_id] = [ 'rating' => $rating->rating,'review' => $rating->review, 'review_id' => $rating->id, 'updated_at' => $rating->updated_at->diffForHumans()];
		                $ratingUsers[] = $rating->user_id;
		            }
		            foreach($reviewData as $dataId => $rating){
		                $ratingSum = 0.0;
		                foreach($rating as $userRatings){
		                    foreach($userRatings as $userId => $userRating){
		                        $ratingSum = (double) $ratingSum + (double) $userRating['rating'];
		                    }
		                    $reviewData[$dataId]['avg']  = $ratingSum/count($userRatings);
		                }
		            }
		        }
		        if(count($ratingUsers) > 0){
		            $users = User::find($ratingUsers);
		            if(is_object($users) && false == $users->isEmpty()){
		                foreach($users as $user){
		                    $userNames[$user->id] = [ 'name' => $user->name,'photo' => $user->photo];
		                }
		            }
		        }
		        $purchasedSubCategories = $this->getRegisteredPaperIds(true);
				return view('tests.test_info', compact('catId','testCategories', 'testSubCategories', 'ads','reviewData','userNames','purchasedSubCategories'));
			}
		}
		return Redirect::to('/');
	}

	/**
	 *	show tests by categoryId by sub categoryId
	 */
	protected function getTest($id,$subject=NULL,$paper=NULL){
		$subcatId = json_decode($id);
		$testSubjectPaperIds = [];
		$isSubCategoryPurchased = false;
		if(isset($subcatId)){
			if(is_object(Auth::user()) && 'ceo@vchiptech.com' == Auth::user()->email){
				$subcategory = TestSubCategory::find($subcatId);
		    } else {
		    	$subcategory = Cache::remember('vchip:tests:testSubCategory-'.$subcatId,30, function() use ($subcatId) {
		            return TestSubCategory::find($subcatId);
		        });
		    }
			if(is_object($subcategory)){
				if(is_object(Auth::user())){
	                if('ceo@vchiptech.com' != Auth::user()->email && 0 == $subcategory->admin_approve){
	                    return Redirect::to('online-tests');
	                }
	            } else {
	                if(0 == $subcategory->admin_approve){
	                    return Redirect::to('online-tests');
	                }
	            }

				$catId = $subcategory->test_category_id;
				$testCategories = Cache::remember('vchip:tests:testCategoriesWithQuestions',60, function() {
		            return TestCategory::getTestCategoriesAssociatedWithQuestion();
		        });
				$testSubCategories = Cache::remember('vchip:tests:testSubCategories:cat-'.$catId,30, function() use ($catId) {
		            return TestSubCategory::getSubcategoriesByCategoryId($catId);
		        });
				$testSubjects = Cache::remember('vchip:tests:testSubjects:cat-'.$catId.':subcat-'.$subcatId,30, function() use ($catId, $subcatId) {
		            return TestSubject::getSubjectsByCatIdBySubcatid($catId, $subcatId);
		        });
		        $testSubjectPapers = Cache::remember('vchip:tests:testSubjectPapers:cat-'.$catId.':subcat-'.$subcatId,30, function() use ($catId, $subcatId) {
		            return TestSubjectPaper::getSubjectPapersByCatIdBySubCatId($catId, $subcatId);
		        });

				if(is_array($testSubjectPapers)){
					foreach($testSubjectPapers as $testPapers){
						foreach($testPapers as $testPaper){
							$testSubjectPaperIds[] = $testPaper->id;
						}
					}
					$testSubjectPaperIds = array_values($testSubjectPaperIds);
				}

				$registeredPaperIds = $this->getRegisteredPaperIds();
				$alreadyGivenPapers = $this->getTestUserScoreByCategoryIdBySubcatIdByPaperIds($catId, $subcatId, $testSubjectPaperIds);
				$loginUser = Auth::user();
				if(is_object($loginUser)){
                    $currentUser = $loginUser->id;
                    if($subject > 0 && $paper > 0){
                        DB::beginTransaction();
                        try
                        {
                            $readNotification = ReadNotification::readNotificationByModuleByModuleIdByUser(Notification::ADMINPAPER,$paper,$currentUser);
                            if(is_object($readNotification)){
                                DB::commit();
                            }
                        }
                        catch(\Exception $e)
                        {
                            DB::rollback();
                            return redirect()->back()->withErrors('something went wrong.');
                        }
                    }
                    $registeredSubCategory = RegisterPaper::getRegisteredSubCategoryByUserIdBySubCategoryId($loginUser->id,$subcatId);
                    if(is_object($registeredSubCategory)){
                    	$isSubCategoryPurchased = true;
                    }
                }
				$currentDate = date('Y-m-d H:i:s');
				$reviewData = [];
		        $ratingUsers = [];
		        $userNames = [];
		        $allRatings = Rating::getRatingsByModuleIdByModuleType($subcatId,Rating::SubCategory);
		        if(is_object($allRatings) && false == $allRatings->isEmpty()){
		            foreach($allRatings as $rating){
		                $reviewData[$rating->module_id]['rating'][$rating->user_id] = [ 'rating' => $rating->rating,'review' => $rating->review, 'review_id' => $rating->id, 'updated_at' => $rating->updated_at->diffForHumans()];
		                $ratingUsers[] = $rating->user_id;
		            }
		            foreach($reviewData as $dataId => $rating){
		                $ratingSum = 0.0;
		                foreach($rating as $userRatings){
		                    foreach($userRatings as $userId => $userRating){
		                        $ratingSum = (double) $ratingSum + (double) $userRating['rating'];
		                    }
		                    $reviewData[$dataId]['avg']  = $ratingSum/count($userRatings);
		                }
		            }
		        }
		        if(count($ratingUsers) > 0){
		            $users = User::find($ratingUsers);
		            if(is_object($users) && false == $users->isEmpty()){
		                foreach($users as $user){
		                    $userNames[$user->id] = [ 'name' => $user->name,'photo' => $user->photo];
		                }
		            }
		        }
				return view('tests.show_tests', compact('catId', 'subcatId', 'testCategories','testSubCategories', 'testSubjects','testSubjectPapers', 'registeredPaperIds', 'alreadyGivenPapers', 'currentDate', 'subject', 'paper','reviewData','userNames','isSubCategoryPurchased'));
			}
		}
		return Redirect::to('online-tests');
	}

	protected function getTestUserScoreByCategoryIdBySubcatIdByPaperIds($catId, $subcatId, $testSubjectPaperIds){
		return Score::getTestUserScoreByCategoryIdBySubcatIdByPaperIds($catId, $subcatId, $testSubjectPaperIds);
	}

	/**
	 *	show tests by categoryId by sub categoryId
	 */
	protected function showTests(Request $request){
		$catId = $request->get('category_id');
		$subcatId = $request->get('subcategory_id');
		if(isset($catId) && isset($subcatId)){
			$testCategories = TestCategory::all();
			$testSubCategories = Cache::remember('vchip:tests:testSubCategories:cat-'.$catId,30, function() use ($catId) {
	            return TestSubCategory::getSubcategoriesByCategoryId($catId);
	        });
			$testSubjects = Cache::remember('vchip:tests:testSubjects:cat-'.$catId.':subcat-'.$subcatId,30, function() use ($catId, $subcatId) {
	            return TestSubject::getSubjectsByCatIdBySubcatid($catId, $subcatId);
	        });
			$testSubjectPapers = Cache::remember('vchip:tests:testSubjectPapers:cat-'.$catId.':subcat-'.$subcatId,30, function() use ($catId, $subcatId) {
		            return TestSubjectPaper::getSubjectPapersByCatIdBySubCatId($catId, $subcatId);
		        });
			return view('tests.show_tests', compact('catId', 'subcatId', 'testCategories','testSubCategories', 'testSubjects','testSubjectPapers'));
		} else {
			return Redirect::to('/');
		}
	}

	/**
	 *	return sub categories by categoryId or by registered subject papers
	 */
	public function getSubCategories(Request $request){
		if($request->ajax()){
			$result = [];
			$categoryId = $request->get('id');
			$userId = $request->get('user_id');
			$rating = $request->get('rating');
		    if(true == $rating){
			    $result['subcategories'] = TestSubCategory::getSubcategoriesByCategoryId($categoryId);
			    $result['purchasedSubCategories'] = $this->getRegisteredPaperIds(true);
	            $ratingUsers = [];
	            $allRatings = Rating::getRatingsByModuleType(Rating::SubCategory);
	            if(is_object($allRatings) && false == $allRatings->isEmpty()){
	                foreach($allRatings as $rating){
	                    $result['ratingData'][$rating->module_id]['rating'][$rating->user_id] = [ 'rating' => $rating->rating,'review' => $rating->review, 'review_id' => $rating->id, 'updated_at' => $rating->updated_at->diffForHumans()];
	                    $ratingUsers[] = $rating->user_id;
	                }
	                foreach($result['ratingData'] as $dataId => $rating){
	                    $ratingSum = 0.0;
	                    foreach($rating as $userRatings){
	                        foreach($userRatings as $userId => $userRating){
	                            $ratingSum = (double) $ratingSum + (double) $userRating['rating'];
	                        }
	                        $result['ratingData'][$dataId]['avg']  = $ratingSum/count($userRatings);
	                    }
	                }
	            } else {
	            	$result['ratingData'] = [];
	            }
	            if(count($ratingUsers) > 0){
	                $users = User::find($ratingUsers);
	                if(is_object($users) && false == $users->isEmpty()){
	                    foreach($users as $user){
	                        if(is_file($user->photo) && true == preg_match('/userStorage/',$user->photo)){
	                            $isImageExist = 'system';
	                        } else if(!empty($user->photo) && false == preg_match('/userStorage/',$user->photo)){
	                            $isImageExist = 'other';
	                        } else {
	                            $isImageExist = 'false';
	                        }
	                        $result['userNames'][$user->id] = [ 'name' => $user->name,'photo' => $user->photo,'image_exist' => $isImageExist];
	                    }
	                }
	            } else {
	            	$result['userNames'] = [];
	            }
	        	return $result;
	        } else {
	        	return TestSubCategory::getSubcategoriesByCategoryId($categoryId);
	        }
		}
	}

		/**
	 *	return sub categories by categoryId or by registered subject papers
	 */
	public function getCollegeTestSubCategories(Request $request){
		if($request->ajax()){
			$categoryId = $request->get('id');
			return Cache::remember(Session::get('college_user_url').':tests:testSubCategories:cat-'.$categoryId,30, function() use ($categoryId) {
	            return TestSubCategory::getCollegeSubCategoriesByCategoryId($categoryId);
	        });
		}
	}

	/**
	 *	return subjects and papers by categoryId by sub categoryId
	 */
	public function getDataByCatSubCat(Request $request){
		$result= [];
		$testSubjectPaperIds = [];
		$catId = $request->get('cat');
		$subcatId = $request->get('subcat');
		$userId = $request->get('user_id');
		$result['isSubCategoryPurchased'] = false;
		$result['subjects'] = Cache::remember('vchip:tests:testSubjects:cat-'.$catId.':subcat-'.$subcatId,30, function() use ($catId, $subcatId) {
            return TestSubject::getSubjectsByCatIdBySubcatid($catId, $subcatId);
        });
		$result['papers'] = Cache::remember('vchip:tests:testSubjectPapers:cat-'.$catId.':subcat-'.$subcatId,30, function() use ($catId, $subcatId) {
            return TestSubjectPaper::getSubjectPapersByCatIdBySubCatId($catId, $subcatId);
        });
		$result['registeredPaperIds'] = $this->getRegisteredPaperIds();
		$result['purchasedSubCategories'] = $this->getRegisteredPaperIds(true);
		if(is_array($result['papers'])){
			foreach($result['papers'] as $testPapers){
				foreach($testPapers as $testPaper){
					$testSubjectPaperIds[] = $testPaper->id;
				}
			}
			$testSubjectPaperIds = array_values($testSubjectPaperIds);
		}
		if($userId > 0){
			$registeredSubCategory = RegisterPaper::getRegisteredSubCategoryByUserIdBySubCategoryId($userId,$subcatId);
            if(is_object($registeredSubCategory)){
            	$result['isSubCategoryPurchased'] = true;
            }
		}
		$result['alreadyGivenPapers'] = $this->getTestUserScoreByCategoryIdBySubcatIdByPaperIds($catId, $subcatId, $testSubjectPaperIds);
		$result['currentDate'] = date('Y-m-d H:i:s');

        $ratingUsers = [];
        $allRatings = Rating::getRatingsByModuleIdByModuleType($subcatId,Rating::SubCategory);
        if(is_object($allRatings) && false == $allRatings->isEmpty()){
            foreach($allRatings as $rating){
                $result['ratingData'][$rating->module_id]['rating'][$rating->user_id] = [ 'rating' => $rating->rating,'review' => $rating->review, 'review_id' => $rating->id, 'updated_at' => $rating->updated_at->diffForHumans()];
                $ratingUsers[] = $rating->user_id;
            }
            foreach($result['ratingData'] as $dataId => $rating){
                $ratingSum = 0.0;
                foreach($rating as $userRatings){
                    foreach($userRatings as $userId => $userRating){
                        $ratingSum = (double) $ratingSum + (double) $userRating['rating'];
                    }
                    $result['ratingData'][$dataId]['avg']  = $ratingSum/count($userRatings);
                }
            }
        } else {
        	$result['ratingData'] = [];
        }
        if(count($ratingUsers) > 0){
            $users = User::find($ratingUsers);
            if(is_object($users) && false == $users->isEmpty()){
                foreach($users as $user){
                    if(is_file($user->photo) && true == preg_match('/userStorage/',$user->photo)){
                        $isImageExist = 'system';
                    } else if(!empty($user->photo) && false == preg_match('/userStorage/',$user->photo)){
                        $isImageExist = 'other';
                    } else {
                        $isImageExist = 'false';
                    }
                    $result['userNames'][$user->id] = [ 'name' => $user->name,'photo' => $user->photo,'image_exist' => $isImageExist];
                }
            }
        } else {
        	$result['userNames'] = [];
        }
		return $result;
	}

	/**
	 *	return subjects and papers by categoryId by sub categoryId
	 */
	public function getCollegeDataByCatSubCat(Request $request){
		$result= [];
		$testSubjectPaperIds = [];
		$catId = $request->get('cat');
		$subcatId = $request->get('subcat');
		$result['subjects'] = Cache::remember(Session::get('college_user_url').':tests:testSubjects:cat-'.$catId.':subcat-'.$subcatId,30, function() use ($catId, $subcatId) {
            return TestSubject::getCollegeSubjectsByCatIdBySubcatid($catId, $subcatId);
        });
		$result['papers'] = Cache::remember(Session::get('college_user_url').':tests:testSubjectPapers:cat-'.$catId.':subcat-'.$subcatId,30, function() use ($catId, $subcatId) {
            return TestSubjectPaper::getCollegeSubjectPapersByCatIdBySubCatId($catId, $subcatId);
        });
		$result['registeredPaperIds'] = $this->getRegisteredPaperIds();
		if(is_array($result['papers'])){
			foreach($result['papers'] as $testPapers){
				foreach($testPapers as $testPaper){
					$testSubjectPaperIds[] = $testPaper->id;
				}
			}
			$testSubjectPaperIds = array_values($testSubjectPaperIds);
		}

		$result['alreadyGivenPapers'] = $this->getTestUserScoreByCategoryIdBySubcatIdByPaperIds($catId, $subcatId, $testSubjectPaperIds);
		$result['currentDate'] = date('Y-m-d H:i:s');
		return $result;
	}

	/**
	 *	set sessions
	 */
	protected function setSessions(Request $request){
		if($request->ajax()){
			$paper = $request->get('paper');
			$subject = $request->get('subject');
			$category = $request->get('category');
			$subcategory = $request->get('subcategory');

			Session::put('paper', $paper);
			Session::put('subject', $subject);
        	Session::put('category', $category);
        	Session::put('subcategory', $subcategory);
        	return "true";
		} else {
			return view('layouts.home');
		}
	}

	/**
	 *	show instructions
	 */
	public function showInstructions(Request $request){
		$categoryId = Session::get('category');
        $subcategoryId = Session::get('subcategory');
        $subjectId = Session::get('subject');
        $paperId = Session::get('paper');
        $isVerificationCode = false;
        if(!is_object(Auth::user())){
        	return Redirect::to('/');
        }
        if($paperId > 0){
        	$paper = TestSubjectPaper::find($paperId);
        	if(is_object($paper) && $paper->verification_code_count > 0){
        		$isVerificationCode = true;
        	}
        }
        return view('layouts.instructions', compact('categoryId', 'subcategoryId', 'subjectId', 'paperId', 'isVerificationCode'));
    }

    protected function registerPaper(Request $request){
    	$userId = $request->get('user_id');
    	$paperId = $request->get('paper_id');
    	return RegisterPaper::registerTestPaper($userId, $paperId);
    }

    public function getRegisteredPaperIds($returnSubCategoryIds = false){
    	$registeredPaperIds = [];
    	$loginUser = Auth::user();
    	if(is_object($loginUser)){
			$userId = $loginUser->id;
			$registeredPapers = RegisterPaper::getRegisteredPapersByUserId($userId);
			if(false == $registeredPapers->isEmpty()){
				foreach($registeredPapers as $registeredPaper){
					if(true == $returnSubCategoryIds){
						if($registeredPaper->test_sub_category_id > 0){
							$registeredPaperIds[$registeredPaper->test_sub_category_id] = $registeredPaper->test_sub_category_id;
						}
					} else {
						if($registeredPaper->test_subject_paper_id > 0){
							$registeredPaperIds[$registeredPaper->test_subject_paper_id] = $registeredPaper->test_subject_paper_id;
						}
					}
				}
			}
		}
		return $registeredPaperIds;
    }


    protected function showUserTestResult(Request $request){
    	$categoryId = $request->get('category_id');
        $subcatId   = $request->get('subcategory_id');
        $paperId   = $request->get('paper_id');
        $subjectId  = $request->get('subject_id');
        $loginUser = Auth::user();
        $userId = $loginUser->id;
        $collegeId = $loginUser->college_id;
        $totalMarks = 0 ;
        $userAnswers = [];
        $positiveMarks = 0;
        $negativeMarks = 0;

        $score = Score::getUserTestResultByCategoryIdBySubcategoryIdBySubjectIdByPaperId($categoryId,$subcatId,$paperId,$subjectId,$userId);
        if(is_object($score)){
        	$collegeRank =Score::getUserTestRankByCategoryIdBySubcategoryIdBySubjectIdByPaperIdByTestScore($categoryId,$subcatId,$subjectId,$paperId,$score->test_score,$collegeId);
            $collegeTotalRank =Score::getUserTestTotalRankByCategoryIdBySubcategoryIdBySubjectIdByPaperId($categoryId,$subcatId,$subjectId, $paperId,$collegeId);
            $globalRank =Score::getUserTestRankByCategoryIdBySubcategoryIdBySubjectIdByPaperIdByTestScore($categoryId,$subcatId,$subjectId,$paperId,$score->test_score,'all');
            $globalTotalRank =Score::getUserTestTotalRankByCategoryIdBySubcategoryIdBySubjectIdByPaperId($categoryId,$subcatId,$subjectId, $paperId,'all');

            $userSolutions = UserSolution::getUserSolutionsByUserIdByscoreIdByBubjectIdByPaperId($userId, $score->id, $subjectId, $paperId);
            if(is_object($userSolutions) && false == $userSolutions->isEmpty()){
        		foreach($userSolutions as $userSolution){
        			$userAnswers[$userSolution->ques_id] = $userSolution->user_answer;
        		}
        	}

        	$questions = Question::getQuestionsByCategoryIdBySubcategoryIdBySubjectIdByPaperId($categoryId, $subcatId, $subjectId, $paperId);
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
        	$percentile = ceil(((($globalTotalRank + 1) - ($globalRank +1) )/ $globalTotalRank)*100);
            $percentage = ceil(($score->test_score/$totalMarks)*100);
            if(($score->right_answered + $score->wrong_answered) > 0){
                $accuracy =  ceil(($score->right_answered/($score->right_answered + $score->wrong_answered))*100);
            } else {
                $accuracy = 0;
            }
        	return view('tests.user_test_result', compact('score', 'globalRank', 'totalMarks', 'globalTotalRank', 'percentile', 'percentage', 'accuracy', 'collegeRank', 'collegeTotalRank', 'positiveMarks', 'negativeMarks'));
        } else {
    		return Redirect::to('/');
        }
    }

    protected function isTestGiven(Request $request){
    	$categoryId = $request->get('category');
        $subcategoryId = $request->get('subcategory');
        $subjectId = $request->get('subject');
        $paperId = $request->get('paper');
        $userId = Auth::user()->id;

        $score = Score::getUserTestResultByCategoryIdBySubcategoryIdBySubjectIdByPaperId($categoryId,$subcategoryId,$paperId,$subjectId,$userId);
        if(is_object($score)){
        	return 'true';
        }else{
        	return 'false';
        }
    }

    protected function checkVerificationCode(Request $request){
    	$paperId = $request->get('paper_id');
        $checkVerificationCode = $request->get('verification_code');
        DB::beginTransaction();
        try
        {
            if($paperId > 0 && !empty($checkVerificationCode)){
                $isVerificationCodeRemoved = false;
                $remainingVerificationCode = '';
                $paper = TestSubjectPaper::find($paperId);
                if(is_object($paper) && $paper->verification_code_count > 0){
                    $allVerificationCode = explode(',', $paper->verification_code);

                    if(is_array($allVerificationCode)){
                        foreach($allVerificationCode as $verificationCode){
                            if($checkVerificationCode == $verificationCode){
                                $isVerificationCodeRemoved = true;
                            } else {
                                if(empty($remainingVerificationCode)){
                                    $remainingVerificationCode = $verificationCode;
                                } else {
                                    $remainingVerificationCode .= ','.$verificationCode;
                                }
                            }
                        }
                    }
                }
                if(true == $isVerificationCodeRemoved && !empty($remainingVerificationCode)){
                    $paper->verification_code = $remainingVerificationCode;
                    $paper->save();
                    DB::commit();
                    return 'true';
                } else {
                    return 'false';
                }
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return 'false';
        }
        return 'false';
    }

}
