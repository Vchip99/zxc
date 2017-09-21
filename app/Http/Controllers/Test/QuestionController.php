<?php

namespace App\Http\Controllers\Test;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TestCategory;
use App\Models\TestSubCategory;
use App\Models\TestSubject;
use App\Models\TestSubjectPaper;
use App\Models\Question;
use App\Models\Notification;
use Redirect,Validator,Session,Auth,DB;
use App\Libraries\InputSanitise;
use App\Mail\MailToSubscribedUser;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\UserSolution;
use Excel;

class QuestionController extends Controller
{
    /**
     *  check admin have permission or not, if not redirect to admin/home
     */
    public function __construct() {
        $this->middleware(function ($request, $next) {
            $adminUser = Auth::guard('admin')->user();
            if(is_object($adminUser)){
                if($adminUser->hasRole('admin') || $adminUser->hasPermission('manageOnlineTest')){
                    return $next($request);
                }
            }
            return Redirect::to('admin/home');
        });
    }

	/**
     * Define your validation rules in a property in
     * the controller to reuse the rules.
     */
    protected $validateShowQuestions = [
            'subject' => 'required',
            'paper' => 'required',
            'section_type' => 'required',
    ];

    protected $validateCreateQuestion = [
    	'category' => 'required|integer',
        'subcategory' => 'required|integer',
        'subject' => 'required|integer',
        'paper' => 'required|integer',
        'section_type' => 'required|integer',
        'question' => 'required|string',
        'solution' => 'required|string',
        'pos_marks' => 'required',
        'neg_marks' => 'required',
    ];

    /**
     *  show questions associated with subject and paper
     */
    protected function index(){
        $testCategories = TestCategory::getTestCategoriesAssociatedWithPapers();
        if(Session::has('search_selected_category')){
            $testSubCategories = TestSubCategory::getSubcategoriesByCategoryIdForAdmin(Session::get('search_selected_category'));
        } else {
            $testSubCategories = [];
        }
        if(Session::has('search_selected_category') && Session::has('search_selected_subcategory')){
            $testSubjects = TestSubject::getSubjectsByCatIdBySubcatidForAdmin(Session::get('search_selected_category'), Session::get('search_selected_subcategory'));
        } else {
           $testSubjects = [];
        }
        if(Session::has('search_selected_category') && Session::has('search_selected_subcategory') && Session::has('search_selected_subject')){
            $papers = TestSubjectPaper::getSubjectPapersByCategoryIdBySubCategoryIdBySubjectIdForAdmin(Session::get('search_selected_category'), Session::get('search_selected_subcategory'), Session::get('search_selected_subject'));
        } else {
           $papers =[];
        }

    	$questions = new Question;
    	return view('question.list', compact('testCategories','testSubCategories','testSubjects', 'questions', 'papers'));
    }

    /**
     * return papers by subject
     */
    protected function getPapersBySubjectId(Request $request){
    	if($request->ajax()){
    		$subjectId = InputSanitise::inputInt($request->get('subjectId'));
    		if(isset($subjectId)){
				return TestSubjectPaper::getSubjectPapersBySubjectId($subjectId);
    		}
    	}
    	return Redirect::to('admin/manageQuestions');
    }

    /**
     *  show all question associated with subject and paper
     */
    protected function show(Request $request){
        $categoryId = InputSanitise::inputInt($request->get('category'));
        $subcategoryId = InputSanitise::inputInt($request->get('subcategory'));
    	$subjectId = InputSanitise::inputInt($request->get('subject'));
    	$paperId = InputSanitise::inputInt($request->get('paper'));
        $sectionTypeId = InputSanitise::inputInt($request->get('section_type'));
    	$v = Validator::make($request->all(), $this->validateShowQuestions);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }

    	if(isset($categoryId) && isset($subcategoryId) && isset($subjectId) && isset($paperId)){
            Session::put('search_selected_category', $categoryId);
            Session::put('search_selected_subcategory', $subcategoryId);
            Session::put('search_selected_subject', $subjectId);
            Session::put('search_selected_paper', $paperId);
            Session::put('search_selected_section', $sectionTypeId);

    		$questions = Question::getQuestionsByCategoryIdBySubcategoryIdBySubjectIdByPaperIdBySectionType($categoryId,$subcategoryId,$subjectId, $paperId, $sectionTypeId);
            $testCategories = TestCategory::getTestCategoriesAssociatedWithPapers();
            $testSubCategories = TestSubCategory::getSubcategoriesByCategoryIdForAdmin($categoryId);
            $testSubjects = TestSubject::getSubjectsByCatIdBySubcatidForAdmin($categoryId,$subcategoryId);
            $papers = TestSubjectPaper::getSubjectPapersByCategoryIdBySubCategoryIdBySubjectIdForAdmin($categoryId,$subcategoryId, $subjectId);
    		return view('question.list', compact('testCategories','testSubCategories','testSubjects', 'questions', 'papers'));
    	} else {
    		return Redirect::to('admin/manageQuestions');
    	}
    }

    /**
     *  show UI for create question
     */
    protected function create(){
        $testCategories = TestCategory::getTestCategoriesAssociatedWithPapers();
        if(Session::has('selected_category')){
            $testSubCategories = TestSubCategory::getSubcategoriesByCategoryIdForAdmin(Session::get('selected_category'));
        } else {
            $testSubCategories = TestSubCategory::all();
        }
        if(Session::has('selected_category') && Session::has('selected_subcategory')){
            $testSubjects = TestSubject::getSubjectsByCatIdBySubcatidForAdmin(Session::get('selected_category'), Session::get('selected_subcategory'));
        } else {
           $testSubjects = TestSubject::all();
        }
        if(Session::has('selected_category') && Session::has('selected_subcategory') && Session::has('selected_subject')){
            $papers = TestSubjectPaper::getSubjectPapersByCategoryIdBySubCategoryIdBySubjectIdForAdmin(Session::get('selected_category'), Session::get('selected_subcategory'), Session::get('selected_subject'));
        } else {
           $papers = TestSubjectPaper::all();
        }

		$testQuestion = new Question;

        $prevQuestionId = Session::get('selected_prev_question');
        $nextQuestionId = 'new';
        $nextQuestionNo = $this->getNextQuestionNo(Session::get('selected_category'),Session::get('selected_subcategory'),Session::get('selected_subject'),Session::get('selected_paper'),Session::get('selected_section'));
        Session::put('next_question_no', $nextQuestionNo);
		return view('question.create', compact('testCategories', 'testSubCategories', 'testSubjects', 'testQuestion', 'papers', 'prevQuestionId', 'nextQuestionId'));
    }

    /**
     *  store question
     */
    protected function store(Request $request){
        $v = Validator::make($request->all(), $this->validateCreateQuestion);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::beginTransaction();
        try
        {
            $testQuestion = Question::addOrUpdateQuestion($request);
            if(is_object($testQuestion)){
                $categoryId = InputSanitise::inputInt($request->get('category'));
                $subcategoryId = InputSanitise::inputInt($request->get('subcategory'));
                $subjectId = InputSanitise::inputInt($request->get('subject'));
                $paperId = InputSanitise::inputInt($request->get('paper'));
                $question_type = InputSanitise::inputInt($request->get('question_type'));
                $section_type = InputSanitise::inputInt($request->get('section_type'));

                Session::put('selected_category', $categoryId);
                Session::put('selected_subcategory', $subcategoryId);
                Session::put('selected_subject', $subjectId);
                Session::put('selected_paper', $paperId);
                Session::put('selected_section', $section_type);
                Session::put('selected_question_type', $testQuestion->question_type);
                Session::put('selected_prev_question', $testQuestion->id);
                $nextQuestionNo = $this->getNextQuestionNo($categoryId,$subcategoryId,$subjectId,$paperId,$section_type);
                Session::put('next_question_no', $nextQuestionNo);

                $questionCount = Question::where('category_id', $categoryId)->where('subcat_id', $subcategoryId)->where('subject_id', $subjectId)->where('paper_id', $paperId)->count();
                if(1 == $questionCount){
                    $paper = TestSubjectPaper::find($paperId);
                    if(is_object($paper)){
                        $messageBody = '';
                        $notificationMessage = 'A new test paper: <a href="'.$request->root().'/getTest/'.$subcategoryId.'/'.$subjectId.'/'.$paperId.'">'.$paper->name.'</a> has been added.';
                        Notification::addNotification($notificationMessage, Notification::ADMINPAPER, $paper->id);
                        $subscriedUsers = User::where('admin_approve', 1)->where('verified', 1)->select('email')->get()->toArray();
                        $allUsers = array_chunk($subscriedUsers, 100);
                        if(count($allUsers) > 0){
                            foreach($allUsers as $selectedUsers){
                                foreach($selectedUsers as $user){
                                    $user = User::where('email', $user)->first();
                                    $messageBody .= '<p> Hello '.$user->name.'</p>';
                                    $messageBody .= '<p>'.$notificationMessage.' please have a look once.</p>';
                                    $messageBody .= '<p><b> Thanks and Regard, </b></p>';
                                    $messageBody .= '<b><a href="https://vchiptech.com"> Vchip Technology Team </a></b><br/>';
                                    $messageBody .= '<b> More about us... </b><br/>';
                                    $messageBody .= '<b><a href="https://vchipedu.com"> Digital Education </a></b><br/>';
                                    $messageBody .= '<b><a href="mailto:info@vchiptech.com" target="_blank">E-mail</a></b><br/>';
                                    $mailSubject = 'Vchipedu added a new test paper';
                                    Mail::to($user)->queue(new MailToSubscribedUser($messageBody, $mailSubject));
                                }
                            }
                        }
                    }
                }

                DB::commit();
                return Redirect::to("admin/createQuestion")->with('message', 'Question created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('admin/manageQuestions');
    }

    /**
     *  edit question
     */
    protected function edit($id){
    	$id = InputSanitise::inputInt(json_decode($id));
    	if(isset($id)){
    		$testQuestion = Question::find($id);
    		if(is_object($testQuestion)){
                $testCategories = TestCategory::all();
                $testSubCategories = TestSubCategory::getSubcategoriesByCategoryId($testQuestion->category_id);
                $testSubjects = TestSubject::getSubjectsByCatIdBySubcatid($testQuestion->category_id, $testQuestion->subcat_id);
                $papers = TestSubjectPaper::getSubjectPapersBySubjectId($testQuestion->subject_id);
                $prevQuestionId = $this->getPrevQuestionIdWithQuestionId($testQuestion->category_id,$testQuestion->subcat_id,$testQuestion->subject_id,$testQuestion->paper_id,$testQuestion->section_type, $testQuestion->id);
                $nextQuestionId = $this->getNextQuestionIdWithQuestionId($testQuestion->category_id,$testQuestion->subcat_id,$testQuestion->subject_id,$testQuestion->paper_id,$testQuestion->section_type, $testQuestion->id);
                $currentQuestionNo = $this->getCurrentQuestionNo($testQuestion->category_id,$testQuestion->subcat_id,$testQuestion->subject_id,$testQuestion->paper_id,$testQuestion->section_type, $testQuestion->id);
                Session::put('selected_category', $testQuestion->category_id);
                Session::put('selected_subcategory', $testQuestion->subcat_id);
                Session::put('selected_subject', $testQuestion->subject_id);
                Session::put('selected_paper', $testQuestion->paper_id);
                Session::put('selected_section', $testQuestion->section_type);
                Session::put('selected_question_type', $testQuestion->question_type);
                Session::put('selected_prev_question', $testQuestion->id);
                $nextQuestionNo = $this->getNextQuestionNo($testQuestion->category_id,$testQuestion->subcat_id,$testQuestion->subject_id,$testQuestion->paper_id,$testQuestion->section_type);
                Session::put('next_question_no', $nextQuestionNo);
                return view('question.create', compact('testCategories', 'testSubCategories', 'testSubjects', 'testQuestion', 'papers', 'prevQuestionId', 'nextQuestionId', 'currentQuestionNo'));
    		}
    	}
		return Redirect::to('admin/manageQuestions');
    }

    /**
     *  update question
     */
    protected function update(Request $request){
        $v = Validator::make($request->all(), $this->validateCreateQuestion);

        if($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::beginTransaction();
        try
        {
            $testQuestion = Question::addOrUpdateQuestion($request, true);

            if(is_object($testQuestion)){
                $categoryId = InputSanitise::inputInt($request->get('category'));
                $subcategoryId = InputSanitise::inputInt($request->get('subcategory'));
                $subjectId = InputSanitise::inputInt($request->get('subject'));
                $paperId = InputSanitise::inputInt($request->get('paper'));
                $question_type = InputSanitise::inputInt($request->get('question_type'));
                $section_type = InputSanitise::inputInt($request->get('section_type'));

                Session::put('selected_category', $categoryId);
                Session::put('selected_subcategory', $subcategoryId);
                Session::put('selected_subject', $subjectId);
                Session::put('selected_paper', $paperId);
                Session::put('selected_section', $section_type);
                Session::put('selected_question_type', $question_type);
                $nextQuestionNo = $this->getNextQuestionNo($categoryId,$subcategoryId,$subjectId,$paperId,$section_type);
                Session::put('next_question_no', $nextQuestionNo);
                DB::commit();
                return Redirect::to("admin/question/$testQuestion->id/edit")->with('message', 'Question updated successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('admin/manageQuestions');
    }

    /**
     *  delete question
     */
    protected function delete(Request $request){
    	$questionId = InputSanitise::inputInt($request->get('question_id'));
    	if(isset($questionId)){
    		$testQuestion = Question::find($questionId);
    		if(is_object($testQuestion)){
                DB::beginTransaction();
                try
                {
                    UserSolution::deleteUserSolutionsByQuestionId($testQuestion->id);
        			$testQuestion->delete();
                    DB::commit();
                    return Redirect::to('admin/manageQuestions')->with('message', 'Question deleted successfully!');
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return redirect()->back()->withErrors('something went wrong.');
                }
    		}
    	}
		return Redirect::to('admin/manageQuestions');
    }

    /**
     *  return question count by subjectIs, by peperId, By section_type
     */
    protected function getNextQuestionCount(Request $request){
        $categoryId = InputSanitise::inputInt($request->get('categoryId'));
        $subcategoryId = InputSanitise::inputInt($request->get('subcategoryId'));
        $subjectId = InputSanitise::inputInt($request->get('subjectId'));
        $paperId = InputSanitise::inputInt($request->get('paperId'));
        $section_type = InputSanitise::inputInt($request->get('section_type'));
        return $this->getNextQuestionNo($categoryId,$subcategoryId,$subjectId,$paperId,$section_type);
    }

    /**
     *  return next question no by subjectId, by peperId, By section_type
     */
    protected function getNextQuestionNo($categoryId,$subcategoryId,$subjectId,$paperId,$section_type){
        $categoryId = InputSanitise::inputInt($categoryId);
        $subcategoryId = InputSanitise::inputInt($subcategoryId);
        $subjectId = InputSanitise::inputInt($subjectId);
        $paperId = InputSanitise::inputInt($paperId);
        $section_type = InputSanitise::inputInt($section_type);
        $totalQuestions = Question::getNextQuestionNoByCategoryIdBySubcategoryIdBySubjectIdByPaperIdBySectionType($categoryId,$subcategoryId,$subjectId,$paperId,$section_type);
        return (int) $totalQuestions + 1;
    }

    protected function getPrevQuestion(Request $request){
        $categoryId = InputSanitise::inputInt($request->get('categoryId'));
        $subcategoryId = InputSanitise::inputInt($request->get('subcategoryId'));
        $subjectId = InputSanitise::inputInt($request->get('subjectId'));
        $paperId = InputSanitise::inputInt($request->get('paperId'));
        $section_type = InputSanitise::inputInt($request->get('section_type'));
        $questionId = 0;
        return $this->getPrevQuestionIdWithQuestionId($categoryId,$subcategoryId,$subjectId,$paperId,$section_type,$questionId);
    }

    protected function getPrevQuestionIdWithQuestionId($categoryId,$subcategoryId,$subjectId,$paperId,$section_type,$questionId){
         $testQuestion = Question::getPrevQuestionByCategoryIdBySubcategoryIdBySubjectIdByPaperIdBySectionType($categoryId,$subcategoryId,$subjectId,$paperId,$section_type,$questionId);

        if(is_object($testQuestion)){
            return $testQuestion->id;
        }
        return;
    }

    protected function getNextQuestionIdWithQuestionId($categoryId,$subcategoryId,$subjectId,$paperId,$section_type,$questionId){
        $testQuestion = Question::getNextQuestionByCategoryIdBySubcategoryIdBySubjectIdByPaperIdBySectionType($categoryId,$subcategoryId,$subjectId,$paperId,$section_type,$questionId);
        if(is_object($testQuestion)){
            return $testQuestion->id;
        }
        return;
    }

    protected function getCurrentQuestionCount(Request $request){
        $categoryId = InputSanitise::inputInt($request->get('categoryId'));
        $subcategoryId = InputSanitise::inputInt($request->get('subcategoryId'));
        $subjectId = InputSanitise::inputInt($request->get('subjectId'));
        $paperId = InputSanitise::inputInt($request->get('paperId'));
        $section_type = InputSanitise::inputInt($request->get('section_type'));
        $questionId = InputSanitise::inputInt($request->get('questionId'));
        return $this->getCurrentQuestionNo($categoryId,$subcategoryId,$subjectId,$paperId,$section_type,$questionId);
    }

    protected function getCurrentQuestionNo($categoryId,$subcategoryId,$subjectId,$paperId,$section_type,$questionId){
        return Question::getCurrentQuestionNoByCategoryIdBySubcategoryIdBySubjectIdByPaperIdBySectionType($categoryId,$subcategoryId,$subjectId,$paperId,$section_type,$questionId);
    }

    protected function uploadQuestions(){
        $testCategories = TestCategory::getTestCategoriesAssociatedWithPapers();
        $testSubCategories = [];
        $testSubjects = [];
        $papers =[];
        return view('question.uploadQuestions', compact('testCategories','testSubCategories','testSubjects','papers'));
    }

    protected function importQuestions(Request $request){
        if($request->hasFile('questions')){
            $path = $request->file('questions')->getRealPath();
            $questions = \Excel::selectSheetsByIndex(0)->load($path)->get();

            if($questions->count()){
                foreach ($questions as $key => $question) {
                    $allQuestions[] = [
                        'name' => $question->question,
                        'answer1' => ($question->option_a)?:'',
                        'answer2' => ($question->option_b)?:'',
                        'answer3' => ($question->option_c)?:'',
                        'answer4' => ($question->option_d)?:'',
                        'answer5' => 0,
                        'answer6' => 0,
                        'answer' => $question->right_answer,
                        'min' => ($question->min)?:0,
                        'max' => ($question->max)?:0,
                        'section_type' => (int) $question->section_type,
                        'question_type' => (int) $question->question_type,
                        'solution' => $question->solution,
                        'positive_marks' => $question->positive_mark,
                        'negative_marks' => $question->negative_mark,
                        'category_id' => $request->get('category'),
                        'subcat_id' =>  $request->get('subcategory'),
                        'subject_id' => $request->get('subject'),
                        'paper_id' => $request->get('paper'),
                    ];
                }

                if(!empty($allQuestions)){
                    DB::beginTransaction();
                    try
                    {
                        DB::table('questions')->insert($allQuestions);
                        DB::commit();
                        return Redirect::to('admin/uploadQuestions')->with('message', 'Questions added successfully!');
                    }
                    catch(\Exception $e)
                    {
                        DB::rollback();
                        return redirect()->back()->withErrors('something went wrong.');
                    }
                }
            }
        }
        return Redirect::to('admin/uploadQuestions');
    }
}
