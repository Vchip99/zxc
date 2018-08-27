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

class TestController extends Controller
{
	/**
	 *	show test sub categories by categoryId
	 */
	public function index(Request $request){
		$testCategories = Cache::remember('vchip:tests:testCategoriesWithQuestions',60, function() {
            return TestCategory::getTestCategoriesAssociatedWithQuestion();
        });
		$testSubCategories = Cache::remember('vchip:tests:testSubCategories',60, function() {
            return TestSubCategory::getTestSubCategoriesAssociatedWithQuestion();
        });
		$catId = 0;
		$date = date('Y-m-d');
        $ads = Add::getAdds($request->url(),$date);
		return view('tests.test_info', compact('catId','testCategories', 'testSubCategories', 'ads'));
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
				$testSubCategories = Cache::remember('vchip:tests:testSubCategories:cat-'.$catId,30, function() use ($catId) {
		            return TestSubCategory::getSubcategoriesByCategoryId($catId);
		        });
				$date = date('Y-m-d');
        		$ads = Add::getAdds($request->url(),$date);
				return view('tests.test_info', compact('catId','testCategories', 'testSubCategories', 'ads'));
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
		if(isset($subcatId)){
			$subcategory = Cache::remember('vchip:tests:testSubCategory-'.$subcatId,30, function() use ($subcatId) {
	            return TestSubCategory::find($subcatId);
	        });
			if(is_object($subcategory)){
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
                }
				$currentDate = date('Y-m-d H:i:s');
				return view('tests.show_tests', compact('catId', 'subcatId', 'testCategories','testSubCategories', 'testSubjects','testSubjectPapers', 'registeredPaperIds', 'alreadyGivenPapers', 'currentDate', 'subject', 'paper'));
			}
		}
		return Redirect::to('/');
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
			$categoryId = $request->get('id');
			$userId = $request->get('userId');
			if(isset($categoryId) && empty($userId)){
				return Cache::remember('vchip:tests:testSubCategories:cat-'.$categoryId,30, function() use ($categoryId) {
		            return TestSubCategory::getSubcategoriesByCategoryId($categoryId);
		        });
			} else {
				return $subCategories = TestSubCategory::getTestSubCategoriesByRegisteredSubjectPapersByCategoryIdByUserId($categoryId,$userId);
			}
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
		$userId = $request->get('userId');
		if(empty($userId)){
			$result['subjects'] = Cache::remember('vchip:tests:testSubjects:cat-'.$catId.':subcat-'.$subcatId,30, function() use ($catId, $subcatId) {
	            return TestSubject::getSubjectsByCatIdBySubcatid($catId, $subcatId);
	        });
			$result['papers'] = Cache::remember('vchip:tests:testSubjectPapers:cat-'.$catId.':subcat-'.$subcatId,30, function() use ($catId, $subcatId) {
	            return TestSubjectPaper::getSubjectPapersByCatIdBySubCatId($catId, $subcatId);
	        });
			$result['registeredPaperIds'] = $this->getRegisteredPaperIds();
		} else {
			$result['subjects'] = TestSubject::getRegisteredSubjectsByCatIdBySubcatIdByUserId($catId, $subcatId,$userId);
			$result['papers'] = TestSubjectPaper::getRegisteredSubjectPapersByCatIdBySubCatIdByUserId($catId, $subcatId,$userId);
		}
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

    protected function getRegisteredPaperIds(){
    	$registeredPaperIds = [];
    	$loginUser = Auth::user();
    	if(is_object($loginUser)){
			$userId = $loginUser->id;
			$registeredPapers = RegisterPaper::getRegisteredPapersByUserId($userId);
			if(false == $registeredPapers->isEmpty()){
				foreach($registeredPapers as $registeredPaper){
					$registeredPaperIds[] = $registeredPaper->test_subject_paper_id;
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
