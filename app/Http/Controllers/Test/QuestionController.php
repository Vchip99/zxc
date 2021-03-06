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
use App\Models\PaperSection;
use App\Models\QuestionBankCategory;
use App\Models\QuestionBankQuestion;
use App\Models\QuestionBankSubCategory;
use Intervention\Image\ImageManagerStatic as Image;
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
                if($adminUser->hasRole('admin') || $adminUser->hasRole('sub-admin')){
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
        $testCategories = TestCategory::getAllTestCategories();
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
        if(Session::has('search_selected_paper')){
            $paperSections =  PaperSection::where('test_subject_paper_id', Session::get('search_selected_paper'))->get();
        } else {
            $paperSections = [];
        }
        if(Session::has('search_selected_category') && Session::has('search_selected_subcategory') && Session::has('search_selected_subject') && Session::has('search_selected_paper') && Session::has('search_selected_section')){
    	   $questions = Question::getQuestionsByCategoryIdBySubcategoryIdBySubjectIdByPaperIdBySectionType(Session::get('search_selected_category'),Session::get('search_selected_subcategory'),Session::get('search_selected_subject'), Session::get('search_selected_paper'), Session::get('search_selected_section'));
        } else {
            $questions = [];
        }

    	return view('question.list', compact('testCategories','testSubCategories','testSubjects', 'questions', 'papers', 'paperSections'));
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
            $testCategories = TestCategory::getAllTestCategories();
            $testSubCategories = TestSubCategory::getSubcategoriesByCategoryIdForAdmin($categoryId);
            $testSubjects = TestSubject::getSubjectsByCatIdBySubcatidForAdmin($categoryId,$subcategoryId);
            $papers = TestSubjectPaper::getSubjectPapersByCategoryIdBySubCategoryIdBySubjectIdForAdmin($categoryId,$subcategoryId, $subjectId);
            $paperSections = PaperSection::where('test_subject_paper_id', $paperId)->get();
    		return view('question.list', compact('testCategories','testSubCategories','testSubjects', 'questions', 'papers', 'paperSections'));
    	} else {
    		return Redirect::to('admin/manageQuestions');
    	}
    }

    /**
     *  show UI for create question
     */
    protected function create(Request $request){

        $testCategories = TestCategory::getAllTestCategories();
        if(Session::has('selected_category')){
            $testSubCategories = TestSubCategory::getSubcategoriesByCategoryIdForAdmin(Session::get('selected_category'));
        } else {
            $testSubCategories = [];
        }
        if(Session::has('selected_category') && Session::has('selected_subcategory')){
            $testSubjects = TestSubject::getSubjectsByCatIdBySubcatidForAdmin(Session::get('selected_category'), Session::get('selected_subcategory'));
        } else {
           $testSubjects = [];
        }
        if(Session::has('selected_category') && Session::has('selected_subcategory') && Session::has('selected_subject')){
            $papers = TestSubjectPaper::getSubjectPapersByCategoryIdBySubCategoryIdBySubjectIdForAdmin(Session::get('selected_category'), Session::get('selected_subcategory'), Session::get('selected_subject'));
        } else {
           $papers = [];
        }
        if(Session::has('selected_paper')){
            $paperSections =  PaperSection::where('test_subject_paper_id', Session::get('selected_paper'))->get();
        } else {
            $paperSections = [];
        }

		$testQuestion = new Question;

        $prevQuestionId = Session::get('selected_prev_question');
        $nextQuestionId = 'new';
        $nextQuestionNo = $this->getNextQuestionNo(Session::get('selected_category'),Session::get('selected_subcategory'),Session::get('selected_subject'),Session::get('selected_paper'),Session::get('selected_section'));
        Session::put('next_question_no', $nextQuestionNo);
		return view('question.create', compact('testCategories', 'testSubCategories', 'testSubjects', 'testQuestion', 'papers', 'prevQuestionId', 'nextQuestionId', 'paperSections'));
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
                $commonData = $request->get('common_data');

                Session::put('selected_category', $categoryId);
                Session::put('selected_subcategory', $subcategoryId);
                Session::put('selected_subject', $subjectId);
                Session::put('selected_paper', $paperId);
                Session::put('selected_section', $section_type);
                Session::put('selected_question_type', $testQuestion->question_type);
                Session::put('selected_prev_question', $testQuestion->id);
                if(1 == $request->get('check_common_data') && !empty($commonData)){
                    Session::put('last_common_data', $commonData);
                } else {
                    Session::remove('last_common_data');
                }

                $nextQuestionNo = $this->getNextQuestionNo($categoryId,$subcategoryId,$subjectId,$paperId,$section_type);
                Session::put('next_question_no', $nextQuestionNo);

                // $questionCount = Question::where('category_id', $categoryId)->where('subcat_id', $subcategoryId)->where('subject_id', $subjectId)->where('paper_id', $paperId)->count();
                // if(1 == $questionCount){
                //     InputSanitise::deleteCacheByString('vchip:tests*');
                //     $paper = TestSubjectPaper::find($paperId);
                //     if(is_object($paper)){
                //         $messageBody = '';
                //         $notificationMessage = 'A new test paper: <a href="'.$request->root().'/getTest/'.$subcategoryId.'/'.$subjectId.'/'.$paperId.'" target="_blank">'.$paper->name.'</a> has been added.';
                //         Notification::addNotification($notificationMessage, Notification::ADMINPAPER, $paper->id);

                //         $subscriedUsers = User::where('admin_approve', 1)->where('verified', 1)->select('email')->get();
                //         $allUsers = $subscriedUsers->chunk(100);
                //         set_time_limit(0);
                //         if(count($allUsers) > 0){
                //             foreach($allUsers as $selectedUsers){
                //                 $messageBody .= '<p> Dear User</p>';
                //                 $messageBody .= '<p>'.$notificationMessage.' please have a look once.</p>';
                //                 $messageBody .= '<p><b> Thanks and Regard, </b></p>';
                //                 $messageBody .= '<b><a href="https://vchiptech.com"> Vchip Technology Team </a></b><br/>';
                //                 $messageBody .= '<b> More about us... </b><br/>';
                //                 $messageBody .= '<b><a href="https://vchipedu.com"> Digital Education </a></b><br/>';
                //                 $messageBody .= '<b><a href="mailto:info@vchiptech.com" target="_blank">E-mail</a></b><br/>';
                //                 $mailSubject = 'Vchipedu added a new test paper';
                //                 Mail::bcc($selectedUsers)->queue(new MailToSubscribedUser($messageBody, $mailSubject));
                //             }
                //         }
                //     }
                // }

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
                $testCategory = $testQuestion->category;
                $testSubCategory = $testQuestion->subcategory;
                if(0 == $testCategory->college_id && 0 == $testCategory->user_id && $testSubCategory->created_by == Auth::guard('admin')->user()->id){
                    $testCategories = TestCategory::getAllTestCategories();
                    $testSubCategories = TestSubCategory::getSubcategoriesByCategoryIdForAdmin($testQuestion->category_id);
                    $testSubjects = TestSubject::getSubjectsByCatIdBySubcatidForAdmin($testQuestion->category_id, $testQuestion->subcat_id);
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

                    $paperSections =  PaperSection::where('test_subject_paper_id', $testQuestion->paper_id)->get();
                    return view('question.create', compact('testCategories', 'testSubCategories', 'testSubjects', 'testQuestion', 'papers', 'prevQuestionId', 'nextQuestionId', 'currentQuestionNo', 'paperSections'));
                }
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
                $commonData = $request->get('common_data');

                Session::put('selected_category', $categoryId);
                Session::put('selected_subcategory', $subcategoryId);
                Session::put('selected_subject', $subjectId);
                Session::put('selected_paper', $paperId);
                Session::put('selected_section', $section_type);
                Session::put('selected_question_type', $question_type);
                if(1 == $request->get('check_common_data') && !empty($commonData)){
                    Session::put('last_common_data', $commonData);
                } else {
                    Session::remove('last_common_data');
                }

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
                $testCategory = $testQuestion->category;
                $testSubCategory = $testQuestion->subcategory;
                if(0 == $testCategory->college_id && 0 == $testCategory->user_id && $testSubCategory->created_by == Auth::guard('admin')->user()->id){
                    DB::beginTransaction();
                    try
                    {
                        Session::put('search_selected_category', $testQuestion->category_id);
                        Session::put('search_selected_subcategory', $testQuestion->subcat_id);
                        Session::put('search_selected_subject', $testQuestion->subject_id);
                        Session::put('search_selected_paper', $testQuestion->paper_id);
                        Session::put('search_selected_section', $testQuestion->section_type);
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
        $testCategories = TestCategory::getAllTestCategories();
        $testSubCategories = [];
        $testSubjects = [];
        $papers =[];
        return view('question.uploadQuestions', compact('testCategories','testSubCategories','testSubjects','papers'));
    }

    protected function importQuestions(Request $request){
        if($request->hasFile('questions')){
            $path = $request->file('questions')->getRealPath();
            $questions = \Excel::selectSheetsByIndex(0)->load($path, function($reader) {
                            $reader->formatDates(false);
                        })->get();

            if($questions->count()){
                foreach ($questions as $key => $question) {
                    preg_match_all('/image\[(.*)\]/', $question->question, $questionMatches);
                    if($questionMatches[1] && $questionMatches[1][0]){
                        $ImgTag = '<img src="/templateEditor/kcfinder/upload/images/';
                        $bodytag = str_replace("image[", $ImgTag, $question->question);
                        $questionStr = str_replace("]", '" style="max-width: 100%;max-height: 400px;" />', $bodytag);
                    } else {
                        $questionStr = $question->question;
                    }

                    preg_match_all('/image\[(.*)\]/', $question->option_a, $optionAMatches);
                    if($optionAMatches[1] && $optionAMatches[1][0]){
                        $ImgTag = '<img src="/templateEditor/kcfinder/upload/images/';
                        $bodytag = str_replace("image[", $ImgTag, $question->option_a);
                        $optionAStr = str_replace("]", '" style="max-width: 100%;max-height: 400px;" />', $bodytag);
                    } else {
                        $optionAStr = $question->option_a;
                    }

                    preg_match_all('/image\[(.*)\]/', $question->option_b, $optionBMatches);
                    if($optionBMatches[1] && $optionBMatches[1][0]){
                        $ImgTag = '<img src="/templateEditor/kcfinder/upload/images/';
                        $bodytag = str_replace("image[", $ImgTag, $question->option_b);
                        $optionBStr = str_replace("]", '" style="max-width: 100%;max-height: 400px;" />', $bodytag);
                    } else {
                        $optionBStr = $question->option_b;
                    }

                    preg_match_all('/image\[(.*)\]/', $question->option_c, $optionCMatches);
                    if($optionCMatches[1] && $optionCMatches[1][0]){
                        $ImgTag = '<img src="/templateEditor/kcfinder/upload/images/';
                        $bodytag = str_replace("image[", $ImgTag, $question->option_c);
                        $optionCStr = str_replace("]", '" style="max-width: 100%;max-height: 400px;" />', $bodytag);
                    } else {
                        $optionCStr = $question->option_c;
                    }

                    preg_match_all('/image\[(.*)\]/', $question->option_d, $optionDMatches);
                    if($optionDMatches[1] && $optionDMatches[1][0]){
                        $ImgTag = '<img src="/templateEditor/kcfinder/upload/images/';
                        $bodytag = str_replace("image[", $ImgTag, $question->option_d);
                        $optionDStr = str_replace("]", '" style="max-width: 100%;max-height: 400px;" />', $bodytag);
                    } else {
                        $optionDStr = $question->option_d;
                    }

                    preg_match_all('/image\[(.*)\]/', $question->option_e, $optionEMatches);
                    if($optionEMatches[1] && $optionEMatches[1][0]){
                        $ImgTag = '<img src="/templateEditor/kcfinder/upload/images/';
                        $bodytag = str_replace("image[", $ImgTag, $question->option_e);
                        $optionEStr = str_replace("]", '" style="max-width: 100%;max-height: 400px;" />', $bodytag);
                    } else {
                        $optionEStr = $question->option_e;
                    }

                    preg_match_all('/image\[(.*)\]/', $question->solution, $solutionMatches);
                    if($solutionMatches[1] && $solutionMatches[1][0]){
                        $ImgTag = '<img src="/templateEditor/kcfinder/upload/images/';
                        $bodytag = str_replace("image[", $ImgTag, $question->solution);
                        $solutionStr = str_replace("]", '" style="max-width: 100%;max-height: 400px;" />', $bodytag);
                    } else {
                        $solutionStr = $question->solution;
                    }
                    if((1 == $question->question_type && !empty($questionStr) && !empty($optionAStr) && !empty($optionBStr) && !empty($optionCStr) && !empty($optionDStr) && !empty($question->right_answer)) || (0 == $question->question_type && !empty($question->min) && !empty($question->max) && !empty($question->right_answer)) ){
                        $allQuestions[] = [
                            'name' => $questionStr,
                            'answer1' => $optionAStr,
                            'answer2' => $optionBStr,
                            'answer3' => $optionCStr,
                            'answer4' => $optionDStr,
                            'answer5' => $optionEStr,
                            'answer6' => 0,
                            'answer' => $question->right_answer,
                            'min' => ($question->min)?:0,
                            'max' => ($question->max)?:0,
                            'question_type' => (int) $question->question_type,
                            'solution' => $solutionStr,
                            'positive_marks' => $question->positive_mark,
                            'negative_marks' => $question->negative_mark,
                            'common_data' => ($question->common_data)?:'',
                            'category_id' => $request->get('category'),
                            'subcat_id' =>  $request->get('subcategory'),
                            'subject_id' => $request->get('subject'),
                            'paper_id' => $request->get('paper'),
                            'section_type' => $request->get('section_type'),
                            'created_at' => date('Y-m-d h:i:s'),
                            'updated_at' => date('Y-m-d h:i:s')
                        ];
                    }
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

    /**
     *  show questions associated with subject and paper
     */
    protected function showSession(){
        $testCategories = TestCategory::getAllTestCategories();
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
        if(Session::has('search_selected_paper')){
            $paperSections =  PaperSection::where('test_subject_paper_id', Session::get('search_selected_paper'))->get();
        } else {
            $paperSections = [];
        }
        $questions = new Question;
        return view('question.associateQuestion', compact('testCategories','testSubCategories','testSubjects', 'questions', 'papers', 'paperSections'));
    }

    /**
     *  show all question associated with subject and paper
     */
    protected function associateSession(Request $request){
        $categoryId = InputSanitise::inputInt($request->get('category'));
        $subcategoryId = InputSanitise::inputInt($request->get('subcategory'));
        $subjectId = InputSanitise::inputInt($request->get('subject'));
        $paperId = InputSanitise::inputInt($request->get('paper'));
        $sectionTypeId = InputSanitise::inputInt($request->get('section_type'));

        if(isset($categoryId) && isset($subcategoryId) && isset($subjectId) && isset($paperId)){
            Session::put('search_selected_category', $categoryId);
            Session::put('search_selected_subcategory', $subcategoryId);
            Session::put('search_selected_subject', $subjectId);
            Session::put('search_selected_paper', $paperId);
            Session::put('search_selected_section', $sectionTypeId);

            $questions = Question::getQuestionsForSessionAssociation($categoryId,$subcategoryId,$subjectId, $paperId, $sectionTypeId);
            $testCategories = TestCategory::getAllTestCategories();
            $testSubCategories = TestSubCategory::getSubcategoriesByCategoryIdForAdmin($categoryId);
            $testSubjects = TestSubject::getSubjectsByCatIdBySubcatidForAdmin($categoryId,$subcategoryId);
            $papers = TestSubjectPaper::getSubjectPapersByCategoryIdBySubCategoryIdBySubjectIdForAdmin($categoryId,$subcategoryId, $subjectId);
            $paperSections = PaperSection::where('test_subject_paper_id', $paperId)->get();
            return view('question.associateQuestion', compact('testCategories','testSubCategories','testSubjects', 'questions', 'papers', 'paperSections'));
        } else {
            return Redirect::to('admin/manageQuestions');
        }
    }

    protected function updateQuestionSession(Request $request){
        DB::beginTransaction();
        try
        {
            $questionId = InputSanitise::inputInt($request->get('question_id'));
            $sessionId = InputSanitise::inputInt($request->get('session_id'));
            $testQuestion = Question::find($questionId);

            if(is_object($testQuestion)){
                $testQuestion->section_type = $sessionId;
                $testQuestion->save();
                DB::commit();
                return 'true';
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return 'false';
        }
        return 'false';
    }

    protected function uploadTestImages(Request $request){
        $allowedImageTypes = ['image/png','image/jpeg'];
        if($request->exists('images')){
            foreach($request->file('images') as $file){
                if(in_array($file->getClientMimeType(), $allowedImageTypes)){
                    $imageName = $file->getClientOriginalName();
                    $clientImagesFolder = public_path().'/templateEditor/kcfinder/upload/images';
                    $file->move($clientImagesFolder, $imageName);
                    // open image
                    $img = Image::make($clientImagesFolder."/".$imageName);
                    // enable interlacing
                    $img->interlace(true);
                    // save image interlaced
                    $img->save();
                }
            }
            return Redirect::to('admin/uploadQuestions')->with('message', 'Images uploaded successfully!');
        }
        return Redirect::to('admin/uploadQuestions');
    }

    /**
     *  show questions
     */
    protected function showQuestionBank(){
        $testCategories = TestCategory::getAllTestCategories();
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
        if(Session::has('search_selected_paper')){
            $paperSections =  PaperSection::where('test_subject_paper_id', Session::get('search_selected_paper'))->get();
        } else {
            $paperSections = [];
        }
        $questions = [];
        $bankCategories = QuestionBankCategory::all();
        if(Session::has('search_question_bank_category')){
            $bankSubCategories = QuestionBankSubCategory::getSubcategoriesByCategoryId(Session::get('search_question_bank_category'));
        } else {
            $bankSubCategories = [];
        }
        return view('question.useQuestionBank', compact('testCategories','testSubCategories','testSubjects', 'questions', 'papers', 'paperSections', 'bankCategories', 'bankSubCategories'));
    }

    /**
     *  show questions
     */
    protected function useQuestionBank(Request $request){
        $categoryId = InputSanitise::inputInt($request->get('category'));
        $subcategoryId = InputSanitise::inputInt($request->get('subcategory'));
        $subjectId = InputSanitise::inputInt($request->get('subject'));
        $paperId = InputSanitise::inputInt($request->get('paper'));
        $sectionTypeId = InputSanitise::inputInt($request->get('section_type'));
        $bankCategoryId = InputSanitise::inputInt($request->get('bank_category'));
        $bankSubCategoryId = InputSanitise::inputInt($request->get('bank_sub_category'));

        Session::put('search_selected_category', $categoryId);
        Session::put('search_selected_subcategory', $subcategoryId);
        Session::put('search_selected_subject', $subjectId);
        Session::put('search_selected_paper', $paperId);
        Session::put('search_selected_section', $sectionTypeId);
        Session::put('search_question_bank_category', $bankCategoryId);
        Session::put('search_question_bank_subcategory', $bankSubCategoryId);

        $testCategories = TestCategory::getAllTestCategories();
        $testSubCategories = TestSubCategory::getSubcategoriesByCategoryIdForAdmin($categoryId);
        $testSubjects = TestSubject::getSubjectsByCatIdBySubcatidForAdmin($categoryId,$subcategoryId);
        $papers = TestSubjectPaper::getSubjectPapersByCategoryIdBySubCategoryIdBySubjectIdForAdmin($categoryId,$subcategoryId, $subjectId);
        $paperSections = PaperSection::where('test_subject_paper_id', $paperId)->get();
        $questions = QuestionBankQuestion::getQuestionsByCategoryIdBySubcategoryId($bankCategoryId,$bankSubCategoryId);
        $bankCategories = QuestionBankCategory::all();
        $bankSubCategories = QuestionBankSubCategory::getSubcategoriesByCategoryId($bankCategoryId);
        return view('question.useQuestionBank', compact('testCategories','testSubCategories','testSubjects', 'questions', 'papers', 'paperSections', 'bankCategories', 'bankSubCategories'));
    }

    protected function exportQuestionBank(Request $request){
        $categoryId = InputSanitise::inputInt($request->get('selected_category'));
        $subcategoryId = InputSanitise::inputInt($request->get('selected_subcategory'));
        $subjectId = InputSanitise::inputInt($request->get('selected_subject'));
        $paperId = InputSanitise::inputInt($request->get('selected_paper'));
        $sectionTypeId = InputSanitise::inputInt($request->get('selected_section_type'));
        if(empty($categoryId) || empty($subcategoryId) || empty($subjectId) || empty($paperId) || empty($sectionTypeId)){
            return Redirect::to('admin/showQuestionBank')->withErrors('Please select category, sub category, subject,paper and section.');
        }
        if(empty($request->get('selected'))){
            return Redirect::to('admin/showQuestionBank')->withErrors('Please select question.');
        }
        $selectedQuestions = $request->get('selected');
        if(count($selectedQuestions) > 0){
            DB::beginTransaction();
            try
            {
                $insertedQuestionCount = 0;
                foreach($selectedQuestions as $selectedQuestionId){
                    $question = QuestionBankQuestion::find($selectedQuestionId);
                    if(is_object($question)){
                        $testQuestion = new Question;
                        $testQuestion->name = $question->name;
                        $testQuestion->answer1 = $question->answer1;
                        $testQuestion->answer2 = $question->answer2;
                        $testQuestion->answer3 = $question->answer3;
                        $testQuestion->answer4 = $question->answer4;
                        $testQuestion->answer5 = $question->answer5;
                        $testQuestion->answer6 = 0;
                        $testQuestion->category_id = $categoryId;
                        $testQuestion->subcat_id = $subcategoryId;
                        $testQuestion->answer = $question->answer;
                        $testQuestion->solution = $question->solution;
                        if(0 == $question->question_type){
                            $testQuestion->min = (!empty($question->min))?:0.00;
                            $testQuestion->max = (!empty($question->max))?:0.00;
                        } else {
                            $testQuestion->min = 0.00;
                            $testQuestion->max = 0.00;
                        }
                        $testQuestion->positive_marks = $request->get('positive_'.$question->id);
                        $testQuestion->negative_marks = $request->get('negative_'.$question->id);
                        $testQuestion->section_type = $sectionTypeId;
                        $testQuestion->subject_id = $subjectId;
                        $testQuestion->paper_id = $paperId;
                        $testQuestion->question_type = $question->question_type;
                        $testQuestion->common_data = '';
                        $testQuestion->save();
                        $insertedQuestionCount++;
                    }
                }
                if(count($selectedQuestions) == $insertedQuestionCount){
                    DB::commit();
                    return Redirect::to('admin/showQuestionBank')->with('message', 'You have successfully created questions.');
                } else {
                    DB::rollback();
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return Redirect::to('admin/showQuestionBank')->withErrors('something went wrong while transfering question.');
            }
        }
        return Redirect::to('admin/showQuestionBank');
    }

}