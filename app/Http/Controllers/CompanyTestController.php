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
use App\Models\User;
use App\Models\ReadNotification;
use App\Models\UserSolution;
use App\Models\UserData;
use App\Models\Skill;
use Session, Redirect, Auth, DB,Cache;
use App\Models\Add;
use App\Models\Rating;

class CompanyTestController extends Controller
{
	/**
	 *	show test sub categories by categoryId
	 */
	public function index($id=NULL){
		$testScores = [];
		$testPaperNames = [];
		$scoreUserIds = [];
		$testUsers = [];
		$companyPaper = '';
		$alreadyGivenPapers = [];
		$registeredPaperIds = [];
		if(json_decode($id) > 0){
			$companyPaper = TestSubjectPaper::getTestPaperAssociatedWithQuestionById(json_decode($id));
			if(!is_object($companyPaper)){
				return Redirect::to('/');
			}
			$alreadyGivenPapers = Score::getTestUserScoreByCategoryIdBySubcatIdByPaperIds($companyPaper->test_category_id, $companyPaper->test_sub_category_id, [$companyPaper->id]);
			$registeredPaperIds = $this->getRegisteredPaperIds();
		}
		$currentDate = date('Y-m-d H:i:s');
		$allTestPapers = TestSubjectPaper::getAllCompanyTestPaperAssociatedWithQuestion();
		if(is_object($allTestPapers) && false == $allTestPapers->isEmpty()){
			foreach($allTestPapers as $allTestPaper){
				$testPaperNames[$allTestPaper->id] = $allTestPaper->name;
			}
		}
		$allCompanyScores = Score::getAllCompanyTestResults();
		if(is_object($allCompanyScores) && false == $allCompanyScores->isEmpty()){
			foreach($allCompanyScores as $score){
				$testScores[$score->paper_id][] = [
					'user' => $score->user_id,
					'test_score' => $score->test_score,
					'rank' => $score->rank(0),
				];
				if(!isset($scoreUserIds[$score->user_id])){
					$scoreUserIds[$score->user_id] = $score->user_id;
				}
			}
		}
		if(count($scoreUserIds) > 0){
			$users = User::find($scoreUserIds);
			if(is_object($users) && false == $users->isEmpty()){
				foreach($users as $user){
					$testUsers[$user->id] = $user;
				}
			}
		}
		$selectedUserResults = [];
		$completedPaperIds = [];
		$userDatas = UserData::all();
		if(is_object($userDatas) && false == $userDatas->isEmpty()){
			foreach($userDatas as $userData){
				$selectedUserResults[$userData->paper_id][] = $userData;
				if(!isset($completedPaperIds[$userData->paper_id])){
					$completedPaperIds[$userData->paper_id] = $userData->paper_id;
				}
			}
			sort($completedPaperIds);
		}
		$userSkills = [];
		$completedPapers = [];
		$allSkills = Skill::all();
		if(is_object($allSkills) && false == $allSkills->isEmpty()){
			foreach($allSkills as $allSkill){
				$userSkills[$allSkill->id] = $allSkill->name;
			}
		}
		if(count($completedPaperIds) > 0){
			$completedPapers = TestSubjectPaper::whereIn('id',$completedPaperIds)->orderBy('id', 'desc')->get();
		}
		return view('companyTest.show_tests', compact('companyPaper','currentDate','alreadyGivenPapers','registeredPaperIds','allTestPapers','testScores','testUsers','testPaperNames','selectedUserResults','userSkills','completedPapers'));
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

    protected function mockInterview(Request $request){
    	$testUserIds = [];
    	$testUsers = [];
    	$userSkills = [];
    	$userDatas = UserData::all();
    	if(is_object($userDatas) && false == $userDatas->isEmpty()){
			foreach($userDatas as $userData){
				if(!isset($testUserIds[$userData->user_id])){
					$testUserIds[$userData->user_id] = $userData->user_id;
				}
			}
		}
		if(count($testUserIds) > 0){
			$users = User::find($testUserIds);
			if(is_object($users) && false == $users->isEmpty()){
				foreach($users as $user){
					$testUsers[$user->id] = $user;
				}
			}
		}
    	$allSkills = Skill::all();
    	if(is_object($allSkills) && false == $allSkills->isEmpty()){
			foreach($allSkills as $allSkill){
				$userSkills[$allSkill->id] = $allSkill;
			}
		}
    	$date = date('Y-m-d');
        $ads = Add::getAdds($request->url(),$date);
        $reviewData = [];
        $ratingUsers = [];
        $userNames = [];
        $allRatings = Rating::getRatingsByModuleType(Rating::MockInterview);
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
    	return view('companyTest.mock_interview', compact('userDatas','userSkills','testUsers','ads','reviewData','userNames'));
    }

    protected function getSelectedStudentBySkillId(Request $request){
    	$testUserIds = [];
    	$testUsers = [];
    	$userSkills = [];
    	$results = [];
    	$userDataIds = [];
    	$reviewData = [];
        $skillId = $request->get('skill_id');
        $userDatas = UserData::getSelectedStudentBySkillId($skillId);
        if(is_object($userDatas) && false == $userDatas->isEmpty()){
			foreach($userDatas as $userData){
				$testUserIds[] = $userData->user_id;
				$userDataIds[] = $userData->id;
			}
		}
		if(count($testUserIds) > 0){
			$users = User::find(array_unique($testUserIds));
			if(is_object($users) && false == $users->isEmpty()){
				foreach($users as $user){
					$testUsers[$user->id] = $user;
				}
			}
		}
		if(count($userDataIds) > 0){
	        $allReviews = Rating::whereIn('module_id',$userDataIds)->where('module_type',Rating::MockInterview)->get();
	        if(is_object($allReviews) && false == $allReviews->isEmpty()){
	        	foreach($allReviews as $review){
	        		$reviewData[$review->module_id]['rating'][$review->user_id] = [ 'rating' => $review->rating,'review' => $review->review, 'review_id' => $review->id, 'updated_at' => $review->updated_at->diffForHumans()];
	        		$testUserIds[] = $review->user_id;
	        	}
	        	if(count($testUserIds) > 0){
					$users = User::find(array_unique($testUserIds));
					if(is_object($users) && false == $users->isEmpty()){
						foreach($users as $user){
							$testUsers[$user->id] = $user;
						}
					}
				}
	        	foreach($reviewData as $dataId => $rating){
	        		$ratingSum = 0.0;
	        		foreach($rating as $userRatings){
	        			foreach($userRatings as $userId => $userRating){
	        				$ratingSum = (double) $ratingSum + (double) $userRating['rating'];
	        				$reviewData[$dataId]['rating'][$userId]['user_name'] =  $testUsers[$userId]->name;
	        				$reviewData[$dataId]['rating'][$userId]['user_photo'] =  $testUsers[$userId]->photo;
	        				if(is_file($testUsers[$userId]->photo) && true == preg_match('/userStorage/',$testUsers[$userId]->photo)){
	                            $isImageExist = 'system';
	                        } else if(!empty($testUsers[$userId]->photo) && false == preg_match('/userStorage/',$testUsers[$userId]->photo)){
	                            $isImageExist = 'other';
	                        } else {
	                            $isImageExist = 'false';
	                        }
	                        $reviewData[$dataId]['rating'][$userId]['image_exist'] = $isImageExist;
	        			}
	        			$reviewData[$dataId]['avg']  = $ratingSum/count($userRatings);
	        		}
	        	}
	        }
		}
    	$allSkills = Skill::all();
    	if(is_object($allSkills) && false == $allSkills->isEmpty()){
			foreach($allSkills as $allSkill){
				$userSkills[$allSkill->id] = $allSkill;
			}
		}

        if(is_object($userDatas) && false == $userDatas->isEmpty()){
        	foreach($userDatas as $userData){
        		$strSkills = '';
        		if(!empty($testUsers[$userData->user_id]->photo) && is_file($testUsers[$userData->user_id]->photo)){
        			$results[$userData->id]['is_file_photo'] =  true;
        			$results[$userData->id]['photo'] =  asset($testUsers[$userData->user_id]->photo);
        		} else {
        			$results[$userData->id]['is_file_photo'] =  false;
        			$results[$userData->id]['photo'] =  asset('images/user/user1.png');
        		}
        		if(!empty($userData->resume) && is_file($userData->resume)){
					$results[$userData->id]['is_file_resume'] =  true;
        			$results[$userData->id]['resume'] =  asset($userData->resume);
        		} else {
        			$results[$userData->id]['is_file_resume'] =  false;
        		}
        		if(!empty($userData->youtube)){
        			$results[$userData->id]['youtube'] =  $userData->youtube;
        		}
        		$results[$userData->id]['name'] = $testUsers[$userData->user_id]->name;
        		$expArr = explode(',',$userData->experiance);
                $skillArr = explode(',',$userData->skill_ids);

                $results[$userData->id]['experience'] = $expArr[0].' yr '.$expArr[1].' month';
                $results[$userData->id]['company'] = $userData->company;
                $results[$userData->id]['education'] = $userData->education;
                if(count($skillArr) > 0){
                    foreach($skillArr as $skill){
                    	$strSkills .= ' #'.$userSkills[$skill]->name;
                  	}
              	 	$results[$userData->id]['skill'] = $strSkills;
                }
                $results[$userData->id]['twitter'] = $userData->twitter;
                $results[$userData->id]['google'] = $userData->google;
                $results[$userData->id]['facebook'] = $userData->facebook;
                if(isset($reviewData[$userData->id])){
                	$results[$userData->id]['ratingData'] = $reviewData[$userData->id];
                }
        	}
        }
        return $results;
    }

    protected function giveRating(Request $request){
        DB::connection('mysql')->beginTransaction();
        try {
            $rating = Rating::addOrUpdateRating($request);
            if(is_object($rating)){
                DB::commit();
                return redirect()->back()->with('message', 'Rating given successfully.');;
            }
        }
        catch(Exception $e)
        {
            DB::connection('mysql')->rollback();
            return redirect()->back()->withErrors([$e->getMessage()]);
        }
        return redirect()->back();
    }

}
