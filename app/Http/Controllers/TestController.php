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
use Session, Redirect, Auth;

class TestController extends Controller
{
	/**
	 *	show test sub categories by categoryId
	 */
	public function index(){
		$testCategories = TestCategory::getTestCategoriesAssociatedWithQuestion();
		$testSubCategories = TestSubCategory::getTestSubCategoriesAssociatedWithQuestion();
		$catId = 0;
		return view('tests.test_info', compact('catId','testCategories', 'testSubCategories'));
	}

	/**
	 *	show test info by categoryId
	 */
	protected function showTest($id){
		$catId = json_decode($id);
		if(isset($catId)){
			$category = TestCategory::find($catId);
			if(is_object($category)){
				$testCategories = TestCategory::getTestCategoriesAssociatedWithQuestion();
				$testSubCategories = TestSubCategory::getSubcategoriesByCategoryId($catId);
				return view('tests.test_info', compact('catId','testCategories', 'testSubCategories'));
			}
		}
		return Redirect::to('/');
	}

	/**
	 *	show tests by categoryId by sub categoryId
	 */
	protected function getTest($id){
		$subcatId = json_decode($id);
		$testSubjectPaperIds = [];
		if(isset($subcatId)){
			$subcategory = TestSubCategory::find($subcatId);
			if(is_object($subcategory)){
				$catId = $subcategory->test_category_id;
				$testCategories = TestCategory::getTestCategoriesAssociatedWithQuestion();
				$testSubCategories = TestSubCategory::getSubcategoriesByCategoryId($catId);
				$testSubjects = TestSubject::getSubjectsByCatIdBySubcatid($catId, $subcatId);
				$testSubjectPapers = TestSubjectPaper::getSubjectPapersByCatIdBySubCatId($catId, $subcatId);
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

				$currentDate = date('Y-m-d');
				return view('tests.show_tests', compact('catId', 'subcatId', 'testCategories','testSubCategories', 'testSubjects','testSubjectPapers', 'registeredPaperIds', 'alreadyGivenPapers', 'currentDate'));
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
			$testSubCategories = TestSubCategory::getSubcategoriesByCategoryId($catId);
			$testSubjects = TestSubject::getSubjectsByCatIdBySubcatid($catId, $subcatId);
			$testSubjectPapers = TestSubjectPaper::getSubjectPapersByCatIdBySubCatId($catId, $subcatId);
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
				return $subCategories = TestSubCategory::getSubcategoriesByCategoryId($categoryId);
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
			$result['subjects'] = TestSubject::getSubjectsByCatIdBySubcatid($catId, $subcatId);
			$result['papers'] = TestSubjectPaper::getSubjectPapersByCatIdBySubCatId($catId, $subcatId);
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
		$result['currentDate'] = date('Y-m-d');
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
	public function showInstructions(){
		$categoryId = Session::get('category');
        $subcategoryId = Session::get('subcategory');
        $subjectId = Session::get('subject');
        $paperId = Session::get('paper');
        return view('layouts.instructions', compact('categoryId', 'subcategoryId', 'subjectId', 'paperId'));
    }

    protected function registerPaper(Request $request){
    	return RegisterPaper::registerTestPaper($request);
    }

    protected function getRegisteredPaperIds(){
    	$registeredPaperIds = [];
    	if(is_object(Auth::user())){
			$userId = Auth::user()->id;
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
        $userId = Auth::user()->id;
        $collegeId = Auth::user()->college_id;
        $totalMarks = 0 ;
        $score = Score::getUserTestResultByCategoryIdBySubcategoryIdBySubjectIdByPaperId($categoryId,$subcatId,$paperId,$subjectId,$userId);
        if(is_object($score)){
        	$rank =Score::getUserTestRankByCategoryIdBySubcategoryIdBySubjectIdByPaperIdByTestScore($categoryId,$subcatId,$subjectId,$paperId,$score->test_score,$collegeId);
        	$totalRank =Score::getUserTestTotalRankByCategoryIdBySubcategoryIdBySubjectIdByPaperId($categoryId,$subcatId,$subjectId, $paperId,$collegeId);
        	$questions = Question::getQuestionsByCategoryIdBySubcategoryIdBySubjectIdByPaperId($categoryId, $subcatId, $subjectId, $paperId);
        	foreach($questions as $question){
        		$totalMarks += $question->positive_marks;
        	}
        	return view('tests.user_test_result', compact('score', 'rank', 'totalMarks', 'totalRank'));
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

}
