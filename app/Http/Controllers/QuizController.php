<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Score;
use App\Models\Question;
use App\Models\UserSolution;
use App\Models\TestSubjectPaper;
use App\Models\PaperSection;
use App\Models\RegisterPaper;
use Session, Redirect, DB, Cache;

class QuizController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        // $this->middleware('auth');

    }

    protected function startQuiz(Request $request){
        if(!Auth::user()){
            return redirect('/home');
        } else {
            return view('quiz.start_quiz');
        }
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
        $checkVerificationCode = $request->get('verification_code');

        if(!empty($categoryId) && !empty($subcategoryId) && !empty($subjectId) && !empty($paperId)){
            $questions = Question::getQuestionsByCategoryIdBySubcategoryIdBySubjectIdByPaperId($categoryId, $subcategoryId, $subjectId, $paperId);
            foreach($questions->shuffle() as $question){
                $results['questions'][$question->section_type][] = $question;
            }

            if(count(array_keys($results['questions'])) > 0){
                $paperSections = Cache::remember('vchip:tests:paperSections:paperId-'.$paperId,30, function() use ($paperId) {
                    return PaperSection::where('test_subject_paper_id', $paperId)->get();
                });

                if(is_object($paperSections) && false == $paperSections->isEmpty()){
                    foreach($paperSections as $paperSection){
                        if(in_array($paperSection->id, array_keys($results['questions']))){
                            $sections[$paperSection->id] = $paperSection;
                        }
                    }
                }
            }

            $paper = $this->getPaperById($paperId);

    	   return view('quiz.questions', compact('results','paper', 'sections', 'checkVerificationCode'));
        } else {
            return Redirect::to('/');
        }
    }

    /**
     *  show all question by categoryId by sub categoryId by subjectId by paperId
     */
    protected function getAllQuestions(Request $request){
        $questions = [];
        $sections = [];
        $categoryId = $request->get('category');
        $subcategoryId = $request->get('subcategory');
        $subjectId = $request->get('subject');
        $paperId = $request->get('paper');
        $allQuestions = Question::getQuestionsByCategoryIdBySubcategoryIdBySubjectIdByPaperId($categoryId, $subcategoryId, $subjectId, $paperId);

        foreach($allQuestions as $question){
            $questions[$question->section_type][] = $question;
        }
        if(count(array_keys($questions)) > 0){
            $paperSections = Cache::remember('vchip:tests:paperSections:paperId-'.$paperId,30, function() use ($paperId) {
                return PaperSection::where('test_subject_paper_id', $paperId)->get();
            });
            if(is_object($paperSections) && false == $paperSections->isEmpty()){
                foreach($paperSections as $paperSection){
                    if(in_array($paperSection->id, array_keys($questions))){
                        $sections[$paperSection->id] = $paperSection;
                    }
                }
            }
        }

        return view('quiz.show_questions', compact('questions', 'sections'));
    }

    // /**
    //  *  return question by categoryId by sub categoryId by subjectId by paperId
    //  */
    // protected function getQuestionsByCategoryIdBySubcategoryIdBySubjectIdByPaperId($categoryId, $subcategoryId, $subjectId, $paperId){
    //     return Cache::remember('vchip:Questions:cat-'.$catId.':subcat-'.$subcatId.':subj-'.$subjectId.':paper-'.$paperId,30, function() use ($catId, $subcatId,$subjectId,$paperId) {
    //             return  Question::where('category_id', $categoryId)
    //             ->where('subcat_id', $subcategoryId)
    //             ->where('subject_id', $subjectId)
    //             ->where('paper_id', $paperId)->get();
    //         });
    // }

    /**
     *  return sub categories by categoryId
     */
    protected function getSubcategoriesByCategoryId($id){
        $subcategories = [];
        $id = json_decode($id);
        if(isset($id)){
            $subcategories = Cache::remember('vchip:tests:subcategories:cat-'.$id,30, function() use ($id) {
                return DB::table('test_sub_categories')->select('id','name', 'test_category_id')->where('test_category_id', $id)->get();
            });
        }
        return $subcategories;
    }

    /**
     *  return paper by Id
     */
    protected function getPaperById($id){
        return Cache::remember('vchip:tests:paper:id-'.$id,30, function() use ($id) {
            return DB::table('test_subject_papers')->where('id', $id)->first();
        });
    }

    /**
     *  show results of questions
     */
    protected function getResult(Request $request){
        if(is_array($request->except('_token'))){
            DB::beginTransaction();
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
                $loginUser = Auth::user();
                $userId = $loginUser->id;
                $collegeId = $loginUser->college_id;

                $categoryId = $request->get('category_id');
                $subcategoryId = $request->get('sub_category_id');
                $subjectId = $request->get('subject_id');
                $paperId = $request->get('paper_id');
                $verificationCode = $request->get('verification_code');

                $quesResults = $request->except(['_token', 'category_id', 'sub_category_id', 'subject_id', 'paper_id', 'verification_code']);
                foreach($quesResults as $index => $quesResult){
                    if($index > 0){
                        $questionIds[] = $index;
                    }
                }
                $questions = Question::getQuestionsByIds($questionIds);
                foreach($questions as $question){
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
                                        'user_id'     => $userId,
                                        'paper_id'    => $paperId,
                                        'subject_id'  => $subjectId,
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'updated_at' => date('Y-m-d H:i:s')
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
                $result['verification_code'] = $verificationCode;
                $score = Score::getUserTestResultByCategoryIdBySubcategoryIdBySubjectIdByPaperId($categoryId,$subcategoryId,$paperId,$subjectId,$userId);
                if(!is_object($score)){
                    $score = Score::addScore($userId, $result);
                    foreach($userAnswers as $ind => $userAnswer){
                        $userAnswers[$ind]['score_id'] = $score->id;
                    }
                    UserSolution::saveUserAnswers($userAnswers);
                    RegisterPaper::registerTestPaper($userId, $paperId);
                    DB::commit();
                }
                $collegeRank =Score::getUserTestRankByCategoryIdBySubcategoryIdBySubjectIdByPaperIdByTestScore($categoryId,$subcategoryId,$subjectId,$paperId,$score->test_score,$collegeId);
                $collegeTotalRank =Score::getUserTestTotalRankByCategoryIdBySubcategoryIdBySubjectIdByPaperId($categoryId,$subcategoryId,$subjectId, $paperId,$collegeId);
                $globalRank =Score::getUserTestRankByCategoryIdBySubcategoryIdBySubjectIdByPaperIdByTestScore($categoryId,$subcategoryId,$subjectId,$paperId,$score->test_score,'all');
                $globalTotalRank =Score::getUserTestTotalRankByCategoryIdBySubcategoryIdBySubjectIdByPaperId($categoryId,$subcategoryId,$subjectId, $paperId,'all');
                $percentile = ceil(((($globalTotalRank + 1) - ($globalRank +1) )/ $globalTotalRank)*100);
                $percentage = ceil(($score->test_score/$totalMarks)*100);
                if(($score->right_answered + $score->wrong_answered) > 0){
                    $accuracy =  ceil(($score->right_answered/($score->right_answered + $score->wrong_answered))*100);
                } else {
                    $accuracy = 0;
                }
                return view('quiz.quiz-result', compact('result', 'collegeRank', 'totalMarks', 'collegeTotalRank', 'score', 'percentile', 'percentage', 'accuracy', 'globalRank', 'globalTotalRank', 'positiveMarks', 'negativeMarks'));
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return Redirect::to('/')->withErrors('something went wrong.');
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
        $userId = Auth::user()->id;
        $categoryId = $request->get('category_id');
        $subcategoryId = $request->get('sub_category_id');
        $subjectId = $request->get('subject_id');
        $paperId = $request->get('paper_id');
        $score = score::getUserTestResultByCategoryIdBySubcategoryIdBySubjectIdByPaperId($categoryId,$subcategoryId,$paperId,$subjectId,$userId);

        $questions = Question::getQuestionsByCategoryIdBySubcategoryIdBySubjectIdByPaperId($categoryId, $subcategoryId, $subjectId, $paperId);

        foreach($questions as $question){
            $results['questions'][$question->section_type][] = $question;
        }
        if(count(array_keys($results['questions'])) > 0){
            $paperSections = PaperSection::where('test_subject_paper_id', $paperId)->get();
            if(is_object($paperSections) && false == $paperSections->isEmpty()){
                foreach($paperSections as $paperSection){
                    if(in_array($paperSection->id, array_keys($results['questions']))){
                        $sections[$paperSection->id] = $paperSection;
                    }
                }
            }
        }
        $userSolutions = UserSolution::getUserSolutionsByUserIdByscoreIdByBubjectIdByPaperId($userId, $score->id, $subjectId, $paperId);

        foreach ($userSolutions  as $key => $result) {
            $userResults[$result->ques_id] = $result;
        }

        return view('quiz.solutions', compact('results', 'userResults', 'score', 'sections'));
    }

    protected function showUserTestSolution(Request $request){
        $sections = [];
        $paper = TestSubjectPaper::find($request->paper_id);
        $userId = $request->user_id;
        $scoreId = $request->score_id;
        if(is_object($paper)){
            $questions = Question::getQuestionsByCategoryIdBySubcategoryIdBySubjectIdByPaperId($paper->test_category_id, $paper->test_sub_category_id, $paper->test_subject_id, $paper->id);

            foreach($questions as $question){
                $results['questions'][$question->section_type][] = $question;
            }
            if(count(array_keys($results['questions'])) > 0){
                $paperId = $paper->id;
                $paperSections = Cache::remember('vchip:tests:paperSections:paperId-'.$paperId,30, function() use ($paperId) {
                    return PaperSection::where('test_subject_paper_id', $paperId)->get();
                });
                if(is_object($paperSections) && false == $paperSections->isEmpty()){
                    foreach($paperSections as $paperSection){
                        if(in_array($paperSection->id, array_keys($results['questions']))){
                            $sections[$paperSection->id] = $paperSection;
                        }
                    }
                }
            }

            $userSolutions = UserSolution::getUserSolutionsByUserIdByscoreIdByBubjectIdByPaperId($userId, $scoreId, $paper->test_subject_id, $paper->id);
            foreach ($userSolutions  as $key => $result) {
                $userResults[$result->ques_id] = $result;
            }

            return view('quiz.testSolution', compact('results', 'userResults', 'paper', 'sections'));
        }
    }

    /**
     *  show all question by categoryId by sub categoryId by subjectId by paperId
     */
    protected function downloadQuestions($category, $subcategory, $subject, $paper,Request $request){
        $sections = [];
        $categoryId = $category;
        $subcategoryId = $subcategory;
        $subjectId = $subject;
        $paperId = $paper;
        $allQuestions = Question::getQuestionsByCategoryIdBySubcategoryIdBySubjectIdByPaperId($categoryId, $subcategoryId, $subjectId, $paperId);

        foreach($allQuestions as $question){
            $questions[$question->section_type][] = $question;
        }
        if(count(array_keys($questions)) > 0){
            $paperSections = Cache::remember('vchip:tests:paperSections:paperId-'.$paperId,30, function() use ($paperId) {
                return PaperSection::where('test_subject_paper_id', $paperId)->get();
            });
            if(is_object($paperSections) && false == $paperSections->isEmpty()){
                foreach($paperSections as $paperSection){
                    if(in_array($paperSection->id, array_keys($questions))){
                        $sections[$paperSection->id] = $paperSection;
                    }
                }
            }
        }

        $html = view('quiz.show_questions', compact('questions', 'sections'));
        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8','tempDir' => __DIR__ . '/mpdfFont']);
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;
        $mpdf->SetWatermarkText('Vchip Technology', 0.4);
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
