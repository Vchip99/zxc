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
		// dd($selectedUserResults);
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
		// dd($selectedUserResults[17]);
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

    protected function mockInterview(){
    	$testUserIds = [];
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
    	// dd($userDatas);
    	$allSkills = Skill::all();
    	$ads = [];
    	return view('companyTest.mock_interview', compact('userDatas','allSkills','testUsers','ads'));
    }

}
