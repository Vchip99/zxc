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
use App\Models\ClientOnlinePaperSection;
use App\Models\RegisterClientOnlinePaper;
use Elibyy\TCPDF\Facades\TCPDF;

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
    protected function getQuestions(Request $request){
        $results = [];
        $sections = [];
        $categoryId = $request->get('category_id');
        $subcategoryId = $request->get('sub_category_id');
        $subjectId = $request->get('subject_id');
        $paperId = $request->get('paper_id');

        if(!empty($categoryId) && !empty($subcategoryId) && !empty($subjectId) && !empty($paperId)){
            $questions = ClientOnlineTestQuestion::getClientQuestionsByCategoryIdBySubcategoryIdBySubjectIdByPaperId($categoryId, $subcategoryId, $subjectId, $paperId, $request);
            foreach($questions as $question){
                $results['questions'][$question->section_type][] = $question;
            }

            if(count(array_keys($results['questions'])) > 0){
                if(is_object(Auth::guard('clientuser')->user())){
                    $clientId = Auth::guard('clientuser')->user()->client_id;
                } else {
                    $clientId = 0;
                }
                $paperSections = ClientOnlinePaperSection::paperSectionsByPaperId($paperId, $clientId,$request);
                // dd(DB::connection('mysql2')->getQueryLog());
                if(is_object($paperSections) && false == $paperSections->isEmpty()){
                    foreach($paperSections as $paperSection){
                        if(in_array($paperSection->id, array_keys($results['questions']))){
                            $sections[$paperSection->id] = $paperSection;
                        }
                    }
                }
            }
// dd($sections);
            $paper = ClientOnlineTestSubjectPaper::getOnlineTestSubjectPaperById($paperId, $request);

        	return view('client.front.question.questions', compact('results','paper', 'sections'));
        } else {
            return Redirect::to('/');
        }
    }

    /**
     *  show results of questions
     */
    protected function getResult(Request $request){
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
                $userAnswers = [];
                $questionIds = [];
                if(is_object(Auth::guard('clientuser')->user())){
                    $userId = Auth::guard('clientuser')->user()->id;
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
                $result['category_id'] = (int) $categoryId;
                $result['subcat_id'] = (int) $subcategoryId;
                $result['subject_id'] = (int) $subjectId;
                $result['paper_id'] = (int) $paperId;
                $result['right_answered'] = $rightAnswer;
                $result['wrong_answered'] = $wrongAnswer;
                $result['unanswered'] = $unanswered;
                $result['marks'] = $marks;
                if($userId > 0){
                    $score = ClientScore::addScore($userId, $result);
                    foreach($userAnswers as $ind => $userAnswer){
                        $userAnswers[$ind]['client_score_id'] = $score->id;
                    }

                    ClientUserSolution::saveUserAnswers($userAnswers);
                    RegisterClientOnlinePaper::registerTestPaper($userId, $paperId);
                    DB::connection('mysql2')->commit();
                    $rank =ClientScore::getClientUserTestRankByCategoryIdBySubcategoryIdBySubjectIdByPaperIdByTestScore($categoryId,$subcategoryId,$subjectId, $paperId,$score->test_score);
                } else {
                    $rank =ClientScore::getClientUserTestRankByCategoryIdBySubcategoryIdBySubjectIdByPaperIdByTestScore($categoryId,$subcategoryId,$subjectId, $paperId,$marks);
                    $score = '';
                }
                $totalRank =ClientScore::getClientUserTestTotalRankByCategoryIdBySubcategoryIdBySubjectIdByPaperId($categoryId,$subcategoryId,$subjectId, $paperId);
            	return view('client.front.question.quiz-result', compact('result', 'rank', 'totalMarks', 'totalRank', 'score'));
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
        $sections = [];
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
        if(count(array_keys($results['questions'])) > 0){
            $clientId = Auth::guard('clientuser')->user()->client_id;
            $paperSections = ClientOnlinePaperSection::paperSectionsByPaperId($paperId, $clientId);
            if(is_object($paperSections) && false == $paperSections->isEmpty()){
                foreach($paperSections as $paperSection){
                    if(in_array($paperSection->id, array_keys($results['questions']))){
                        $sections[$paperSection->id] = $paperSection;
                    }
                }
            }
        }
        $userSolutions = ClientUserSolution::getClientUserSolutionsByUserIdByscoreIdByBubjectIdByPaperId($userId, $score->id, $subjectId, $paperId);
        foreach ($userSolutions  as $key => $result) {
            $userResults[$result->ques_id] = $result;
        }
        return view('client.front.question.solutions', compact('results', 'userResults', 'score', 'sections'));
    }

    protected function showUserTestSolution(Request $request){
        $paper = ClientOnlineTestSubjectPaper::find($request->paper_id);
        $sections = [];
        $userResults = [];
        $userId = $request->user_id;
        $scoreId = $request->score_id;
        if(is_object($paper)){
            $questions = ClientOnlineTestQuestion::getClientQuestionsByCategoryIdBySubcategoryIdBySubjectIdByPaperId($paper->category_id, $paper->sub_category_id, $paper->subject_id, $paper->id, $request);

            foreach($questions as $question){
                $results['questions'][$question->section_type][] = $question;
            }
            if(count(array_keys($results['questions'])) > 0){
                $clientId = Auth::guard('clientuser')->user()->client_id;
                $paperSections = ClientOnlinePaperSection::paperSectionsByPaperId($paper->id, $clientId);
                if(is_object($paperSections) && false == $paperSections->isEmpty()){
                    foreach($paperSections as $paperSection){
                        if(in_array($paperSection->id, array_keys($results['questions']))){
                            $sections[$paperSection->id] = $paperSection;
                        }
                    }
                }
            }
            $userSolutions = ClientUserSolution::getClientUserSolutionsByUserIdByscoreIdByBubjectIdByPaperId($userId, $scoreId, $paper->subject_id, $paper->id);

            foreach ($userSolutions  as $key => $result) {
                $userResults[$result->ques_id] = $result;
            }
            return view('client.front.question.testSolution', compact('results', 'userResults', 'paper', 'sections'));
        }
    }


    /**
     *  show all question by categoryId by sub categoryId by subjectId by paperId
     */
    protected function getAllQuestions(Request $request){
        $sections = [];
        $categoryId = $request->get('category');
        $subcategoryId = $request->get('subcategory');
        $subjectId = $request->get('subject');
        $paperId = $request->get('paper');
        $allQuestions = ClientOnlineTestQuestion::getClientQuestionsByCategoryIdBySubcategoryIdBySubjectIdByPaperId($categoryId, $subcategoryId, $subjectId, $paperId, $request);

        foreach($allQuestions as $question){
            $questions[$question->section_type][] = $question;
        }
        if(count(array_keys($questions)) > 0){
            if(is_object(Auth::guard('clientuser')->user())){
                $clientId = Auth::guard('clientuser')->user()->client_id;
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
    protected function downloadQuestions($subdomain, $category, $subcategory, $subject, $paper,Request $request){
        $sections = [];
        $categoryId = $category;
        $subcategoryId = $subcategory;
        $subjectId = $subject;
        $paperId = $paper;
        $clientSubdomain = $request->route()->getParameter('client');
        $allQuestions = ClientOnlineTestQuestion::getClientQuestionsByCategoryIdBySubcategoryIdBySubjectIdByPaperId($categoryId, $subcategoryId, $subjectId, $paperId, $request);

        foreach($allQuestions as $question){
            $questions[$question->section_type][] = $question;
        }
        if(count(array_keys($questions)) > 0){
            $clientId = Auth::guard('clientuser')->user()->client_id;
            $paperSections = ClientOnlinePaperSection::paperSectionsByPaperId($paperId, $clientId);
            if(is_object($paperSections) && false == $paperSections->isEmpty()){
                foreach($paperSections as $paperSection){
                    if(in_array($paperSection->id, array_keys($questions))){
                        $sections[$paperSection->id] = $paperSection;
                    }
                }
            }
        }

        $html = '';
        $html .= '<style>'.file_get_contents(asset('/css/bootstrap.min.css')).'</style>';
        $html .= '<style>'.file_get_contents(asset('/css/main.css')).'</style>';
        $html .= '<style>.watermark {
            position: absolute;
            opacity: 0.25;
            font-size: 50px;
            width: 30%;
            text-align: center;
            z-index: 1000;
            color: #ddd;
        }
        .answer{
            padding-left: 20px !important;
        }</style>';

        if(count($sections) > 0){
            foreach($sections as $section){
                if(count($questions[$section->id]) > 0){
                    $html .= '<a class="btn btn-primary" style="width:100px;" title="'.$section->name.'">'.$section->name.'</a>';
                    foreach($questions[$section->id] as $index => $question){
                        $number = $index + 1;
                        $html .= '<div class="panel-body">
                                    <div ><span class="watermark">'.$clientSubdomain.'</span>
                                        <p class="questions" >';
                        if(!empty($question->common_data)){
                            $html .= '<b>Common Data:</b>';
                            $html .= '<span>'.$question->common_data.'</span><hr/>';
                        }
                        $html .= '<span class="btn btn-sq-xs btn-info">'.$number .'.</span> '.$question->name.'</p><p>';
                        // $html .= '<p>';
                        if(1 == $question->question_type){
                            $html .= '<div class="row">A. '.$question->answer1.'</div>';
                            $html .= '<div class="row">B. '.$question->answer2.'</div>';
                            $html .= '<div class="row">C. '.$question->answer3.'</div>';
                            $html .= '<div class="row">D. '.$question->answer4.'</div>';
                            if(!empty($question->answer5)){
                                $html .= '<div class="row">E. '.$question->answer5.'</div>';
                            }
                        } else {
                            $html .= '<div class="panel panel-default"><div class="panel-body">Enter a number </div></div>';
                        }
                        $html .= '</p></div></div>';
                    }
                }
            }
        }

        $pdf = new TCPDF();
        $pdf::SetTitle($clientSubdomain);
        $pdf::AddPage();
        $pdf::SetFont('freesans', '', 12);
        $pdf::SetFontSubsetting(true);
        $pdf::writeHTML($html, true, false, true, false, '');
        return $pdf::Output('download_questions.pdf', 'D');
    }

}