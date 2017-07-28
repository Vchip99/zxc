<?php

namespace App\Http\Controllers\Client\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\Client\ClientHomeController;
use Redirect;
use Validator, Session, Auth, DB, Response;
use App\Libraries\InputSanitise;
use App\Models\ClientOnlineTestCategory;
use App\Models\ClientOnlineTestSubCategory;
use App\Models\ClientOnlineTestSubject;
use App\Models\ClientOnlineTestSubjectPaper;
use App\Models\ClientOnlineTestQuestion;
use App\Models\ClientScore;
use App\Models\ClientUserSolution;
use \PDF;

class ClientOnlineQuestionFrontController extends ClientHomeController
{

	 /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->middleware('clientuser');
        parent::__construct($request);
    }


    /**
     *  show all question  and their results by categoryId by sub categoryId by subjectId by paperId
     */
    protected function getQuestions(Request $request){
        $results = [];
        $categoryId = $request->get('category_id');
        $subcategoryId = $request->get('sub_category_id');
        $subjectId = $request->get('subject_id');
        $paperId = $request->get('paper_id');

        if(!empty($categoryId) && !empty($subcategoryId) && !empty($subjectId) && !empty($paperId)){
            $questions = ClientOnlineTestQuestion::getClientQuestionsByCategoryIdBySubcategoryIdBySubjectIdByPaperId($categoryId, $subcategoryId, $subjectId, $paperId, $request);
            foreach($questions as $question){
                $results['questions'][$question->section_type][] = $question;
            }

            $paper = ClientOnlineTestSubjectPaper::getOnlineTestSubjectPaperById($paperId, $request);

        	return view('client.front.question.questions', compact('results','paper'));
        } else {
            return Redirect::to('/');
        }
    }

    /**
     *  show results of questions
     */
    protected function getResult(Request $request){
    	if(is_array($request->except(['_token', 'show-tech', 'show-apt']))){
            DB::connection('mysql2')->beginTransaction();
            try
            {
                $rightAnswer=0;
                $wrongAnswer=0;
                $unanswered=0;
                $userAnswer = 0;
                $marks=0;
                $totalMarks=0;
                $instituteCourseId = 0;
                $userAnswers = [];
                $userId = Auth::guard('clientuser')->user()->id;

                $categoryId = $request->get('category_id');
                $subcategoryId = $request->get('sub_category_id');
                $subjectId = $request->get('subject_id');
                $paperId = $request->get('paper_id');

                $quesResults = $request->except(['_token', 'show-tech', 'show-apt']);
                $ids = array_keys($quesResults);
                $questions = ClientOnlineTestQuestion::getQuestionsByIds($ids);

                foreach($questions as $index => $question){
                    if( 0 == $index){
                        $instituteCourseId = $question->client_institute_course_id;
                    }
                    if($question->answer == $quesResults[$question->id] && $question->question_type == 1){
                        $rightAnswer++;
                        $marks = $marks + $question->positive_marks;
                    } else if($quesResults[$question->id] >= $question->min && $quesResults[$question->id] <= $question->max && $question->question_type == 0){
                        $rightAnswer++;
                        $marks = $marks + $question->positive_marks;
                    } else if($quesResults[$question->id]=='unsolved' || $quesResults[$question->id] =='' ){
                        $unanswered++;
                    } else {
                        $wrongAnswer++;
                        $marks = $marks - $question->negative_marks;
                    }
                    if($quesResults[$question->id]=='unsolved' || $quesResults[$question->id] ==''){
                        $userAnswer = "unsolved";
                    } else  {
                        $userAnswer =  (float) $quesResults[$question->id];
                    }
                    if($question->question_type == 1){
                        $questionAnswer = $question->answer;
                    } else {
                        $questionAnswer =  $question->min . " to " . $question->max ;
                    }
                    $totalMarks += $question->positive_marks;
                    $userAnswers[] = [
                                        'ques_id'     => $question->id,
                                        'ques_answer' => $questionAnswer,
                                        'user_answer' => $userAnswer,
                                        'client_user_id'     => $userId,
                                        'paper_id'    => $paperId,
                                        'subject_id'  => $subjectId
                                    ];
                }

                $result = [];
                $result['client_institute_course_id'] = (int) $instituteCourseId;
                $result['category_id'] = (int) $categoryId;
                $result['subcat_id'] = (int) $subcategoryId;
                $result['subject_id'] = (int) $subjectId;
                $result['paper_id'] = (int) $paperId;
                $result['right_answered'] = $rightAnswer;
                $result['wrong_answered'] = $wrongAnswer;
                $result['unanswered'] = $unanswered;
                $result['marks'] = $marks;

                $score = ClientScore::addScore($userId, $result);

                foreach($userAnswers as $ind => $userAnswer){
                    $userAnswers[$ind]['client_score_id'] = $score->id;
                }

                ClientUserSolution::saveUserAnswers($userAnswers);
                $rank =ClientScore::getClientUserTestRankByCategoryIdBySubcategoryIdBySubjectIdByPaperIdByTestScore($categoryId,$subcategoryId,$subjectId, $paperId,$score->test_score);
                $totalRank =ClientScore::getClientUserTestTotalRankByCategoryIdBySubcategoryIdBySubjectIdByPaperId($categoryId,$subcategoryId,$subjectId, $paperId);
                 DB::connection('mysql2')->commit();
            	return view('client.front.question.quiz-result', compact('result', 'rank', 'totalMarks', 'totalRank'));
            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
                return back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('/');
    }

    /**
     *  show solution of questions
     */
    protected function getSolutions(Request $request){
        $results     = [];
        $userResults = [];
        $userId = Auth::guard('clientuser')->user()->id;
        $categoryId = $request->get('category_id');
        $subcategoryId = $request->get('sub_category_id');
        $subjectId = $request->get('subject_id');
        $paperId = $request->get('paper_id');

        $score = ClientScore::getClientUserTestResultByCategoryIdBySubcategoryIdBySubjectIdByPaperId($categoryId,$subcategoryId,$paperId,$subjectId,$userId);
        $questions = ClientOnlineTestQuestion::getClientQuestionsByCategoryIdBySubcategoryIdBySubjectIdByPaperId($categoryId, $subcategoryId, $subjectId, $paperId, $request);

        foreach($questions as $question){
            $results['questions'][$question->section_type][] = $question;
        }
        $userSolutions = ClientUserSolution::getClientUserSolutionsByUserIdByscoreIdByBubjectIdByPaperId($userId, $score->id, $subjectId, $paperId);
        foreach ($userSolutions  as $key => $result) {
            $userResults[$result->ques_id] = $result;
        }
        return view('client.front.question.solutions', compact('results', 'userResults', 'score'));
    }

    protected function showUserTestSolution(Request $request){
        $paper = ClientOnlineTestSubjectPaper::find($request->paper_id);

        $userId = $request->user_id;
        $scoreId = $request->score_id;
        if(is_object($paper)){
            $questions = ClientOnlineTestQuestion::getClientQuestionsByCategoryIdBySubcategoryIdBySubjectIdByPaperId($paper->category_id, $paper->sub_category_id, $paper->subject_id, $paper->id, $request);

            foreach($questions as $question){
                $results['questions'][$question->section_type][] = $question;
            }

            $userSolutions = ClientUserSolution::getClientUserSolutionsByUserIdByscoreIdByBubjectIdByPaperId($userId, $scoreId, $paper->subject_id, $paper->id);

            foreach ($userSolutions  as $key => $result) {
                $userResults[$result->ques_id] = $result;
            }

            return view('client.front.question.testSolution', compact('results', 'userResults', 'paper'));
        }
    }


    /**
     *  show all question by categoryId by sub categoryId by subjectId by paperId
     */
    protected function getAllQuestions(Request $request){

        $categoryId = $request->get('category');
        $subcategoryId = $request->get('subcategory');
        $subjectId = $request->get('subject');
        $paperId = $request->get('paper');
        $allQuestions = ClientOnlineTestQuestion::getClientQuestionsByCategoryIdBySubcategoryIdBySubjectIdByPaperId($categoryId, $subcategoryId, $subjectId, $paperId, $request);

        foreach($allQuestions as $question){
            $questions[$question->section_type][] = $question;
        }
        return view('client.front.question.show_questions', compact('questions'));
    }

        /**
     *  show all question  and their results by categoryId by sub categoryId by subjectId by paperId and download as pdf
     */
    protected function downloadQuestions($subdomain, $category, $subcategory, $subject, $paper,Request $request){
        $categoryId = $category;
        $subcategoryId = $subcategory;
        $subjectId = $subject;
        $paperId = $paper;
        $allQuestions = ClientOnlineTestQuestion::getClientQuestionsByCategoryIdBySubcategoryIdBySubjectIdByPaperId($categoryId, $subcategoryId, $subjectId, $paperId, $request);

        foreach($allQuestions as $question){
            $questions[$question->section_type][] = $question;
        }
        $pdf = \PDF::loadView('client.front.question.download_questions', compact('questions'));
        return $pdf->download('questions.pdf');

    }

}