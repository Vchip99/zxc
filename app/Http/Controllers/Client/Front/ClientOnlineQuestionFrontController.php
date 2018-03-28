<?php

namespace App\Http\Controllers\Client\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\Client\ClientHomeController;
use Redirect;
use Validator, Session, Auth, DB, Response,Cache;
use App\Libraries\InputSanitise;
use App\Models\ClientOnlineTestCategory;
use App\Models\ClientOnlineTestSubCategory;
use App\Models\ClientOnlineTestSubject;
use App\Models\ClientOnlineTestSubjectPaper;
use App\Models\ClientOnlineTestQuestion;
use App\Models\ClientScore;
use App\Models\ClientUserSolution;
use App\Models\ClientOnlinePaperSection;
use App\Models\RegisterClientOnlinePaper;

class ClientOnlineQuestionFrontController extends ClientHomeController
{

	 /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        // $this->middleware('clientuser');
        parent::__construct($request);
    }


    /**
     *  show all question  and their results by categoryId by sub categoryId by subjectId by paperId
     */
    protected function getQuestions($subdomainName,Request $request){
        $results = [];
        $sections = [];
        $categoryId = $request->get('category_id');
        $subcategoryId = $request->get('sub_category_id');
        $subjectId = $request->get('subject_id');
        $paperId = $request->get('paper_id');

        if(!empty($categoryId) && !empty($subcategoryId) && !empty($subjectId) && !empty($paperId)){
            $questions = ClientOnlineTestQuestion::getClientQuestionsByCategoryIdBySubcategoryIdBySubjectIdByPaperId($subdomainName,$categoryId, $subcategoryId, $subjectId, $paperId, $request);

            if(is_object($questions) && true == $questions->isEmpty()){
                return Redirect::to('/');
            }
            foreach($questions as $question){
                $results['questions'][$question->section_type][] = $question;
            }

            if(count(array_keys($results['questions'])) > 0){
                $clientUser = Auth::guard('clientuser')->user();
                if(is_object($clientUser)){
                    $clientId = $clientUser->client_id;
                } else {
                    $clientId = 0;
                }
                $paperSections = ClientOnlinePaperSection::paperSectionsByPaperId($paperId, $clientId,$request);
                if(is_object($paperSections) && false == $paperSections->isEmpty()){
                    foreach($paperSections as $paperSection){
                        if(in_array($paperSection->id, array_keys($results['questions']))){
                            $sections[$paperSection->id] = $paperSection;
                        }
                    }
                }
            }
            $paper = ClientOnlineTestSubjectPaper::getOnlineTestSubjectPaperById($paperId, $request);

        	return view('client.front.question.questions', compact('results','paper', 'sections'));
        } else {
            return Redirect::to('/');
        }
    }

    /**
     *  show results of questions
     */
    protected function getResult($subdomainName,Request $request){
    	if(is_array($request->except(['_token']))){
            DB::connection('mysql2')->beginTransaction();
            try
            {
                $rightAnswer=0;
                $wrongAnswer=0;
                $unanswered=0;
                $userAnswer = 0;
                $marks=0;
                $totalMarks=0;
                $positiveMarks = 0;
                $negativeMarks = 0;
                $userAnswers = [];
                $questionIds = [];
                $clientUser = Auth::guard('clientuser')->user();
                if(is_object($clientUser)){
                    $userId = $clientUser->id;
                } else {
                    $userId = 0;
                }

                $categoryId = $request->get('category_id');
                $subcategoryId = $request->get('sub_category_id');
                $subjectId = $request->get('subject_id');
                $paperId = $request->get('paper_id');

                $quesResults = $request->except(['_token', 'category_id', 'sub_category_id', 'subject_id', 'paper_id']);
                foreach($quesResults as $index => $quesResult){
                    if($index > 0){
                        $questionIds[] = $index;
                    }
                }
                $questions = ClientOnlineTestQuestion::getQuestionsByIds($questionIds);

                foreach($questions as $index => $question){
                    if($question->answer == $quesResults[$question->id] && $question->question_type == 1){
                        $rightAnswer++;
                        $marks = $marks + $question->positive_marks;
                        $positiveMarks = (float) $positiveMarks + (float) $question->positive_marks;
                    } else if($quesResults[$question->id] >= $question->min && $quesResults[$question->id] <= $question->max && $question->question_type == 0){
                        $rightAnswer++;
                        $marks = $marks + $question->positive_marks;
                        $positiveMarks = (float) $positiveMarks + (float) $question->positive_marks;
                    } else if($quesResults[$question->id]=='unsolved' || $quesResults[$question->id] =='' ){
                        $unanswered++;
                    } else {
                        $wrongAnswer++;
                        $marks = $marks - $question->negative_marks;
                        $negativeMarks =  (float) $negativeMarks + (float) $question->negative_marks;
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
                $result['category_id'] = (int) $categoryId;
                $result['subcat_id'] = (int) $subcategoryId;
                $result['subject_id'] = (int) $subjectId;
                $result['paper_id'] = (int) $paperId;
                $result['right_answered'] = $rightAnswer;
                $result['wrong_answered'] = $wrongAnswer;
                $result['unanswered'] = $unanswered;
                $result['marks'] = $marks;

                if($userId > 0){
                    $score = ClientScore::getClientUserTestResultByCategoryIdBySubcategoryIdBySubjectIdByPaperId($subdomainName,$categoryId,$subcategoryId,$paperId,$subjectId,$userId);
                    if(!is_object($score)){
                        $score = ClientScore::addScore($userId, $result);
                        foreach($userAnswers as $ind => $userAnswer){
                            $userAnswers[$ind]['client_score_id'] = $score->id;
                        }
                        ClientUserSolution::saveUserAnswers($userAnswers);
                        RegisterClientOnlinePaper::registerTestPaper($userId, $paperId);
                        DB::connection('mysql2')->commit();
                    }
                    $rank =ClientScore::getClientUserTestRankByCategoryIdBySubcategoryIdBySubjectIdByPaperIdByTestScore($subdomainName,$categoryId,$subcategoryId,$subjectId, $paperId,$score->test_score);
                    $percentage = ceil(($score->test_score/$totalMarks)*100);
                    if(($score->right_answered + $score->wrong_answered) > 0){
                        $accuracy =  ceil(($score->right_answered/($score->right_answered + $score->wrong_answered))*100);
                    } else {
                        $accuracy = 0;
                    }
                } else {
                    $rank =ClientScore::getClientUserTestRankByCategoryIdBySubcategoryIdBySubjectIdByPaperIdByTestScore($subdomainName,$categoryId,$subcategoryId,$subjectId, $paperId,$marks);
                    $score = '';
                    $percentage = ceil(($result['marks']/$totalMarks)*100);
                    if(($result['right_answered'] + $result['wrong_answered']) > 0){
                        $accuracy =  ceil(($result['right_answered']/($result['right_answered'] + $result['wrong_answered']))*100);
                    } else {
                        $accuracy = 0;
                    }
                }
                $totalRank =ClientScore::getClientUserTestTotalRankByCategoryIdBySubcategoryIdBySubjectIdByPaperId($subdomainName,$categoryId,$subcategoryId,$subjectId, $paperId);

                if($totalRank > 0){
                    $percentile = ceil(((($totalRank + 1) - ($rank +1) )/ $totalRank)*100);
                } else {
                    $percentile = 0;
                }

            	return view('client.front.question.quiz-result', compact('result', 'rank', 'totalMarks', 'totalRank', 'score', 'percentile', 'percentage', 'accuracy','positiveMarks', 'negativeMarks'));
            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
                return Redirect::to('online-tests')->withErrors('something went wrong.');
            }
        }
        return Redirect::to('/');
    }

    /**
     *  show solution of questions
     */
    protected function getSolutions($subdomainName,Request $request){
        $results     = [];
        $userResults = [];
        $sections = [];
        $userId = Auth::guard('clientuser')->user()->id;
        $categoryId = $request->get('category_id');
        $subcategoryId = $request->get('sub_category_id');
        $subjectId = $request->get('subject_id');
        $paperId = $request->get('paper_id');

        $score = ClientScore::getClientUserTestResultByCategoryIdBySubcategoryIdBySubjectIdByPaperId($subdomainName,$categoryId,$subcategoryId,$paperId,$subjectId,$userId);
        $questions = ClientOnlineTestQuestion::getClientQuestionsByCategoryIdBySubcategoryIdBySubjectIdByPaperId($subdomainName,$categoryId, $subcategoryId, $subjectId, $paperId, $request);

        foreach($questions as $question){
            $results['questions'][$question->section_type][] = $question;
        }
        if(count(array_keys($results['questions'])) > 0){
            $clientId = Auth::guard('clientuser')->user()->client_id;
            $paperSections = ClientOnlinePaperSection::paperSectionsByPaperId($paperId, $clientId,$request);
            if(is_object($paperSections) && false == $paperSections->isEmpty()){
                foreach($paperSections as $paperSection){
                    if(in_array($paperSection->id, array_keys($results['questions']))){
                        $sections[$paperSection->id] = $paperSection;
                    }
                }
            }
        }
        $userSolutions = ClientUserSolution::getClientUserSolutionsByUserIdByscoreIdByBubjectIdByPaperId($subdomainName,$userId, $score->id, $subjectId, $paperId);
        foreach ($userSolutions  as $key => $result) {
            $userResults[$result->ques_id] = $result;
        }
        return view('client.front.question.solutions', compact('results', 'userResults', 'score', 'sections'));
    }

    protected function showUserTestSolution($subdomainName,Request $request){
        $paper = ClientOnlineTestSubjectPaper::find($request->paper_id);
        $sections = [];
        $userResults = [];
        $userId = $request->user_id;
        $scoreId = $request->score_id;
        if(is_object($paper)){
            $questions = ClientOnlineTestQuestion::getClientQuestionsByCategoryIdBySubcategoryIdBySubjectIdByPaperId($subdomainName,$paper->category_id, $paper->sub_category_id, $paper->subject_id, $paper->id, $request);

            foreach($questions as $question){
                $results['questions'][$question->section_type][] = $question;
            }
            if(count(array_keys($results['questions'])) > 0){
                $clientId = Auth::guard('clientuser')->user()->client_id;
                $paperId = $paper->id;
                $paperSections = ClientOnlinePaperSection::paperSectionsByPaperId($paperId, $clientId,$request);
                if(is_object($paperSections) && false == $paperSections->isEmpty()){
                    foreach($paperSections as $paperSection){
                        if(in_array($paperSection->id, array_keys($results['questions']))){
                            $sections[$paperSection->id] = $paperSection;
                        }
                    }
                }
            }
            $userSolutions = ClientUserSolution::getClientUserSolutionsByUserIdByscoreIdByBubjectIdByPaperId($subdomainName,$userId, $scoreId, $paper->subject_id, $paper->id);

            foreach ($userSolutions  as $key => $result) {
                $userResults[$result->ques_id] = $result;
            }

            return view('client.front.question.testSolution', compact('results', 'userResults', 'paper', 'sections'));
        }
    }


    /**
     *  show all question by categoryId by sub categoryId by subjectId by paperId
     */
    protected function getAllQuestions($subdomainName,Request $request){
        $sections = [];
        $categoryId = $request->get('category');
        $subcategoryId = $request->get('subcategory');
        $subjectId = $request->get('subject');
        $paperId = $request->get('paper');
        $allQuestions = ClientOnlineTestQuestion::getClientQuestionsByCategoryIdBySubcategoryIdBySubjectIdByPaperId($subdomainName,$categoryId, $subcategoryId, $subjectId, $paperId, $request);

        foreach($allQuestions as $question){
            $questions[$question->section_type][] = $question;
        }
        if(count(array_keys($questions)) > 0){
            $clientUser = Auth::guard('clientuser')->user();
            if(is_object($clientUser)){
                $clientId = $clientUser->client_id;
            } else {
                $clientId = 0;
            }
            $paperSections = ClientOnlinePaperSection::paperSectionsByPaperId($paperId, $clientId,$request);
            if(is_object($paperSections) && false == $paperSections->isEmpty()){
                foreach($paperSections as $paperSection){
                    if(in_array($paperSection->id, array_keys($questions))){
                        $sections[$paperSection->id] = $paperSection;
                    }
                }
            }
        }
        $clientSubdomain = $request->route()->getParameter('client');
        return view('client.front.question.show_questions', compact('questions', 'clientSubdomain', 'sections'));
    }

    /**
     *  show all question  and their results by categoryId by sub categoryId by subjectId by paperId and download as pdf
     */
    protected function downloadQuestions($subdomainName, $category, $subcategory, $subject, $paper,Request $request){
        $sections = [];
        $categoryId = $category;
        $subcategoryId = $subcategory;
        $subjectId = $subject;
        $paperId = $paper;
        $clientSubdomain = $request->route()->getParameter('client');
        $allQuestions = ClientOnlineTestQuestion::getClientQuestionsByCategoryIdBySubcategoryIdBySubjectIdByPaperId($subdomainName,$categoryId, $subcategoryId, $subjectId, $paperId, $request);

        foreach($allQuestions as $question){
            $questions[$question->section_type][] = $question;
        }
        if(count(array_keys($questions)) > 0){
            $clientId = Auth::guard('clientuser')->user()->client_id;
            $paperSections = ClientOnlinePaperSection::paperSectionsByPaperId($paperId, $clientId,$request);
            if(is_object($paperSections) && false == $paperSections->isEmpty()){
                foreach($paperSections as $paperSection){
                    if(in_array($paperSection->id, array_keys($questions))){
                        $sections[$paperSection->id] = $paperSection;
                    }
                }
            }
        }

        $html = view('client.front.question.show_questions', compact('questions', 'clientSubdomain', 'sections'));
        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8','tempDir' => __DIR__ .'/../../mpdfFont']);
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;
        $mpdf->SetWatermarkText($clientSubdomain, 0.4);
        $mpdf->showWatermarkText = true;
        $bootstrapUrl = asset('css/bootstrap.min.css');
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $bootstrapUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        $stylesheet1 = curl_exec($curl);
        curl_close($curl);

        $mainCssUrl = asset('css/main.css');
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $mainCssUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        $stylesheet2 = curl_exec($curl);
        curl_close($curl);
        $mpdf->WriteHTML($stylesheet1,1);
        $mpdf->WriteHTML($stylesheet2,1);
        $mpdf->WriteHTML($html, 2);
        return  $mpdf->Output("download_questions.pdf", "D");
    }
}