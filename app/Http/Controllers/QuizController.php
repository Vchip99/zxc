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
use Session, Redirect, DB;
use Elibyy\TCPDF\Facades\TCPDF;

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

        if(!empty($categoryId) && !empty($subcategoryId) && !empty($subjectId) && !empty($paperId)){
            $questions = $this->getQuestionsByCategoryIdBySubcategoryIdBySubjectIdByPaperId($categoryId, $subcategoryId, $subjectId, $paperId);

            foreach($questions as $question){
                $results['questions'][$question->section_type][] = $question;
            }

            if(count(array_keys($results['questions'])) > 0){
                $paperSections = PaperSection::where('test_subject_paper_id', $paperId)->get();
                if(is_object($paperSections) && false == $paperSections->isEmpty()){
                    foreach($paperSections as $paperSection){
                        $sections[$paperSection->id] = $paperSection;
                    }
                }
            }

            $paper = $this->getPaperById($paperId);

    	   return view('quiz.questions', compact('results','paper', 'sections'));
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
        $allQuestions = $this->getQuestionsByCategoryIdBySubcategoryIdBySubjectIdByPaperId($categoryId, $subcategoryId, $subjectId, $paperId);

        foreach($allQuestions as $question){
            $questions[$question->section_type][] = $question;
        }
        if(count(array_keys($questions)) > 0){
            $paperSections = PaperSection::where('test_subject_paper_id', $paperId)->get();
            if(is_object($paperSections) && false == $paperSections->isEmpty()){
                foreach($paperSections as $paperSection){
                    $sections[$paperSection->id] = $paperSection;
                }
            }
        }

        return view('quiz.show_questions', compact('questions', 'sections'));
    }

    /**
     *  return question by categoryId by sub categoryId by subjectId by paperId
     */
    protected function getQuestionsByCategoryIdBySubcategoryIdBySubjectIdByPaperId($categoryId, $subcategoryId, $subjectId, $paperId){
        return  Question::where('category_id', $categoryId)
                ->where('subcat_id', $subcategoryId)
                ->where('subject_id', $subjectId)
                ->where('paper_id', $paperId)->get();
    }

    /**
     *  return sub categories by categoryId
     */
    protected function getSubcategoriesByCategoryId($id){
        $subcategories = [];
        $id = json_decode($id);
        if(isset($id)){
            $subcategories = DB::table('test_sub_categories')->select('id','name', 'test_category_id')->where('test_category_id', $id)->get();
        }
        return $subcategories;
    }

    /**
     *  return paper by Id
     */
    protected function getPaperById($id){
        return DB::table('test_subject_papers')->where('id', $id)->first();
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
                $userAnswers = [];
                $questionIds = [];
                $userId = Auth::user()->id;
                $collegeId = Auth::user()->college_id;

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
                $questions = Question::getQuestionsByIds($questionIds);
                foreach($questions as $question){
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
                                        'user_id'     => $userId,
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

                $score = Score::addScore($userId, $result);

                foreach($userAnswers as $ind => $userAnswer){
                    $userAnswers[$ind]['score_id'] = $score->id;
                }
                UserSolution::saveUserAnswers($userAnswers);
                RegisterPaper::registerTestPaper($userId, $paperId);
                DB::commit();
                $rank =Score::getUserTestRankByCategoryIdBySubcategoryIdBySubjectIdByPaperIdByTestScore($categoryId,$subcategoryId,$subjectId,$paperId,$score->test_score,$collegeId);
                $totalRank =Score::getUserTestTotalRankByCategoryIdBySubcategoryIdBySubjectIdByPaperId($categoryId,$subcategoryId,$subjectId, $paperId,$collegeId);

                return view('quiz.quiz-result', compact('result', 'rank', 'totalMarks', 'totalRank', 'score'));
            }
            catch(\Exception $e)
            {
                DB::rollback();
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
        $userId = Auth::user()->id;
        $categoryId = $request->get('category_id');
        $subcategoryId = $request->get('sub_category_id');
        $subjectId = $request->get('subject_id');
        $paperId = $request->get('paper_id');
        $score = score::getUserTestResultByCategoryIdBySubcategoryIdBySubjectIdByPaperId($categoryId,$subcategoryId,$paperId,$subjectId,$userId);

        $questions = $this->getQuestionsByCategoryIdBySubcategoryIdBySubjectIdByPaperId($categoryId, $subcategoryId, $subjectId, $paperId);

        foreach($questions as $question){
            $results['questions'][$question->section_type][] = $question;
        }
        if(count(array_keys($results['questions'])) > 0){
            $paperSections = PaperSection::where('test_subject_paper_id', $paperId)->get();
            if(is_object($paperSections) && false == $paperSections->isEmpty()){
                foreach($paperSections as $paperSection){
                    $sections[$paperSection->id] = $paperSection;
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
            $questions = $this->getQuestionsByCategoryIdBySubcategoryIdBySubjectIdByPaperId($paper->test_category_id, $paper->test_sub_category_id, $paper->test_subject_id, $paper->id);

            foreach($questions as $question){
                $results['questions'][$question->section_type][] = $question;
            }
            if(count(array_keys($results['questions'])) > 0){
                $paperSections = PaperSection::where('test_subject_paper_id', $paper->id)->get();
                if(is_object($paperSections) && false == $paperSections->isEmpty()){
                    foreach($paperSections as $paperSection){
                        $sections[$paperSection->id] = $paperSection;
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
        $allQuestions = $this->getQuestionsByCategoryIdBySubcategoryIdBySubjectIdByPaperId($categoryId, $subcategoryId, $subjectId, $paperId);

        foreach($allQuestions as $question){
            $questions[$question->section_type][] = $question;
        }
        if(count(array_keys($questions)) > 0){
            $paperSections = PaperSection::where('test_subject_paper_id', $paperId)->get();
            if(is_object($paperSections) && false == $paperSections->isEmpty()){
                foreach($paperSections as $paperSection){
                    $sections[$paperSection->id] = $paperSection;
                }
            }
        }
        $html = '';
        $html .= '<style>'.file_get_contents(asset('/css/bootstrap.min.css')).'</style>';
        $html .= '<style>'.file_get_contents(asset('/css/main.css')).'</style>';
        $html .= '<style>.watermark {
            position: absolute;
            opacity: 0.25;
            font-size: 30px;
            width: 50%;
            text-align: center;
            z-index: 1000;
            color: #ddd;
        }
        .answer{
            padding-left: 20px !important;
        }
        img{
            max-width: 100%;
            height: auto;
        }</style>';
        if(count($sections) > 0){
            foreach($sections as $section){
                if(count($questions[$section->id]) > 0){
                    $html .= '<a class="btn btn-primary" style="width:100px;" title="'.$section->name.'">'.$section->name.'</a>';
                    foreach($questions[$section->id] as $index => $question){
                        $number = $index + 1;
                        $html .= '<div class="panel-body"><span class="watermark">Vchip Technology</span>
                                    <div >
                                        <p class="questions" >';
                        if(!empty($question->common_data)){
                            $html .= '<b>Common Data:</b>';
                            $html .= '<span>'.$question->common_data.'</span><hr/>';
                        }
                        $html .= '<span class="btn btn-sq-xs btn-info">'.$number .'.</span> '.$question->name.'</p><p>';
                        if(1 == $question->question_type){
                            $html .= '<div class="row answer">A. '.$question->answer1.'</div>';
                            $html .= '<div class="row answer">B. '.$question->answer2.'</div>';
                            $html .= '<div class="row answer">C. '.$question->answer3.'</div>';
                            $html .= '<div class="row answer">D. '.$question->answer4.'</div>';
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
        $pdf::SetTitle('Vchip Technology');
        $pdf::AddPage();
        $pdf::SetFont('freesans', '', 12);
        $pdf::SetFontSubsetting(true);
        $pdf::writeHTML($html, true, false, true, false, '');
        return $pdf::Output('download_questions.pdf', 'D');
    }

    /**
     *  show result grid
     */
    protected function getAllResults(){
        $this->getUserResults();
        return view('quiz.results-grid');
    }

    /**
     *  show user result grid
     */
    protected function getUserResults(){
        $page = 0;
        $limit = 10;
        // $sidx = 10;
        // $sord = 'asc';

        if(count($_GET)>0){
            $page = $_GET['page']; // get the requested page
            $limit = $_GET['rows']; // get how many rows we want to have into the grid
            $sidx = $_GET['sidx']; // get index row - i.e. user click to sort
            $sord = $_GET['sord']; // get the direction
        }
        $userId = Auth::user()->id;
        $count = \DB::table('scores')->where('user_id', $userId)->count();

        if( $count >0 )
        {
            $total_pages = ceil($count/$limit);
        } else {
            $total_pages = 0;
        }
        if($page > $total_pages){
            $page=$total_pages;
        }
        $start = $limit*$page - $limit; // do not put $limit*($page - 1)

        $results = \DB::table('scores')
                        ->join('subcategory', function($join){
                            $join->on('scores.category_id', '=', 'subcategory.cat_id');
                            $join->on('scores.subcat_id', '=', 'subcategory.id');
                        })
                        ->where('scores.user_id', $userId)
                        ->select('scores.*', 'subcategory.name')
                        ->orderBy('scores.id')
                        ->skip($start)->take($limit)->get();
        $responce = new \stdClass();
        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;
        foreach ($results as $index => $row) {
            $responce->rows[$index]['id']=$row->id;
            $responce->rows[$index]['cell']=array($row->id,$row->name,$row->right_answered,$row->wrong_answered,$row->unanswered);
        }
        return json_encode($responce);
    }

}
