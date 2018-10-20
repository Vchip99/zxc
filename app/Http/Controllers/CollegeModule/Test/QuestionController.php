<?php

namespace App\Http\Controllers\CollegeModule\Test;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CollegeCategory;
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
            $loginUser = Auth::guard('web')->user();
            if(is_object($loginUser)){
                return $next($request);
            }
            return Redirect::to('/');
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
    protected function index($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $loginUser = Auth::guard('web')->user();
        $testCategories = CollegeCategory::getTestCategoriesByCollegeIdByDeptIdAssociatedWithPapers($loginUser->college_id);
        if(Session::has('search_college_selected_category')){
            $testSubCategories = TestSubCategory::getCollegeSubcategoriesByCategoryIdForAdmin(Session::get('search_college_selected_category'));
        } else {
            $testSubCategories = [];
        }
        if(Session::has('search_college_selected_category') && Session::has('search_college_selected_subcategory')){
            if(User::Hod == $loginUser->user_type || User::Lecturer == $loginUser->user_type){
                $testSubjects = TestSubject::getSubjectsByCollegeIdByAssignedDeptsByCategoryIdBySubCategoryId($loginUser->college_id,Session::get('search_college_selected_category'), Session::get('search_college_selected_subcategory'));
            } else {
                if(User::TNP == $loginUser->user_type){
                    $testSubjects = TestSubject::getCollegeSubjectsByCatIdBySubcatIdByUser(Session::get('search_college_selected_category'), Session::get('search_college_selected_subcategory'));
                } else {
                    $testSubjects = TestSubject::getCollegeSubjectsByCatIdBySubcatidForAdmin(Session::get('search_college_selected_category'), Session::get('search_college_selected_subcategory'));
                }
            }
        } else {
           $testSubjects = [];
        }
        if(Session::has('search_college_selected_category') && Session::has('search_college_selected_subcategory') && Session::has('search_college_selected_subject')){
            $papers = TestSubjectPaper::getCollegeSubjectPapersByCategoryIdBySubCategoryIdBySubjectIdForAdmin(Session::get('search_college_selected_category'), Session::get('search_college_selected_subcategory'), Session::get('search_college_selected_subject'));
        } else {
           $papers =[];
        }
        if(Session::has('search_college_selected_paper')){
            $paperSections =  PaperSection::where('test_subject_paper_id', Session::get('search_college_selected_paper'))->get();
        } else {
            $paperSections = [];
        }
    	$questions = [];

    	return view('collegeModule.test.question.list', compact('testCategories','testSubCategories','testSubjects', 'questions', 'papers', 'paperSections'));
    }

    /**
     * return papers by subject
     */
    protected function getCollegePapersBySubjectId(Request $request){
    	if($request->ajax()){
    		$subjectId = InputSanitise::inputInt($request->get('subjectId'));
    		if(isset($subjectId)){
				return TestSubjectPaper::getSubjectPapersBySubjectId($subjectId);
    		}
    	}
    	return;
    }

    /**
     *  show all question associated with subject and paper
     */
    protected function show($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
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
            Session::put('search_college_selected_category', $categoryId);
            Session::put('search_college_selected_subcategory', $subcategoryId);
            Session::put('search_college_selected_subject', $subjectId);
            Session::put('search_college_selected_paper', $paperId);
            Session::put('search_college_selected_section', $sectionTypeId);

    		$questions = Question::getCollegeQuestionsByCategoryIdBySubcategoryIdBySubjectIdByPaperIdBySectionType($categoryId,$subcategoryId,$subjectId, $paperId, $sectionTypeId);
            $loginUser = Auth::guard('web')->user();
            $testCategories = CollegeCategory::getTestCategoriesByCollegeIdByDeptIdAssociatedWithPapers($loginUser->college_id);
            $testSubCategories = TestSubCategory::getCollegeSubcategoriesByCategoryIdForAdmin($categoryId);

            if(User::Hod == $loginUser->user_type || User::Lecturer == $loginUser->user_type){
                $testSubjects = TestSubject::getSubjectsByCollegeIdByAssignedDeptsByCategoryIdBySubCategoryId($loginUser->college_id,$categoryId,$subcategoryId);
            } else {
                if(User::TNP == $loginUser->user_type){
                    $testSubjects = TestSubject::getCollegeSubjectsByCatIdBySubcatIdByUser($categoryId,$subcategoryId);
                } else {
                    $testSubjects = TestSubject::getCollegeSubjectsByCatIdBySubcatidForAdmin($categoryId,$subcategoryId);
                }
            }

            $papers = TestSubjectPaper::getCollegeSubjectPapersByCategoryIdBySubCategoryIdBySubjectIdForAdmin($categoryId,$subcategoryId, $subjectId);
            $paperSections = PaperSection::where('test_subject_paper_id', $paperId)->get();
    		return view('collegeModule.test.question.list', compact('testCategories','testSubCategories','testSubjects', 'questions', 'papers', 'paperSections'));
    	} else {
    		return Redirect::to('college/'.$collegeUrl.'/manageQuestions');
    	}
    }

    /**
     *  show UI for create question
     */
    protected function create($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $loginUser = Auth::guard('web')->user();
        $testCategories = CollegeCategory::getCollegeCategoriesByCollegeIdByDeptId($loginUser->college_id);
        if(Session::has('selected_college_category')){
            $testSubCategories = TestSubCategory::getCollegeSubcategoriesByCategoryIdForAdmin(Session::get('selected_college_category'));
        } else {
            $testSubCategories = [];
        }
        if(Session::has('selected_college_category') && Session::has('selected_college_subcategory')){
            if(User::Hod == $loginUser->user_type || User::Lecturer == $loginUser->user_type){
                $testSubjects = TestSubject::getSubjectsByCollegeIdByAssignedDeptsByCategoryIdBySubCategoryId($loginUser->college_id,Session::get('selected_college_category'), Session::get('selected_college_subcategory'));
            } else {
               $testSubjects = TestSubject::getCollegeSubjectsByCatIdBySubcatidForAdmin(Session::get('selected_college_category'), Session::get('selected_college_subcategory'));
            }
        } else {
           $testSubjects = [];
        }
        if(Session::has('selected_college_category') && Session::has('selected_college_subcategory') && Session::has('selected_college_subject')){
            $papers = TestSubjectPaper::getCollegeSubjectPapersByCategoryIdBySubCategoryIdBySubjectIdForAdmin(Session::get('selected_college_category'), Session::get('selected_college_subcategory'), Session::get('selected_college_subject'));
        } else {
           $papers = [];
        }
        if(Session::has('selected_college_paper')){
            $paperSections =  PaperSection::where('test_subject_paper_id', Session::get('selected_college_paper'))->get();
        } else {
            $paperSections = [];
        }

		$testQuestion = new Question;

        $prevQuestionId = Session::get('selected_college_prev_question');
        $nextQuestionId = 'new';
        $nextQuestionNo = $this->getNextQuestionNo(Session::get('selected_college_category'),Session::get('selected_college_subcategory'),Session::get('selected_college_subject'),Session::get('selected_college_paper'),Session::get('selected_college_section'));
        Session::put('next_college_question_no', $nextQuestionNo);
		return view('collegeModule.test.question.create', compact('testCategories', 'testSubCategories', 'testSubjects', 'testQuestion', 'papers', 'prevQuestionId', 'nextQuestionId', 'paperSections'));
    }

    /**
     *  store question
     */
    protected function store($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
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

                Session::put('selected_college_category', $categoryId);
                Session::put('selected_college_subcategory', $subcategoryId);
                Session::put('selected_college_subject', $subjectId);
                Session::put('selected_college_paper', $paperId);
                Session::put('selected_college_section', $section_type);
                Session::put('selected_college_question_type', $testQuestion->question_type);
                Session::put('selected_college_prev_question', $testQuestion->id);
                if(1 == $request->get('check_common_data') && !empty($commonData)){
                    Session::put('last_college_common_data', $commonData);
                } else {
                    Session::remove('last_college_common_data');
                }

                $nextQuestionNo = $this->getNextQuestionNo($categoryId,$subcategoryId,$subjectId,$paperId,$section_type);
                Session::put('next_college_question_no', $nextQuestionNo);
                DB::commit();
                return Redirect::to('college/'.$collegeUrl.'/createQuestion')->with('message', 'Question created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('college/'.$collegeUrl.'/manageQuestions');
    }

    /**
     *  edit question
     */
    protected function edit($collegeUrl,$id,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
    	$id = InputSanitise::inputInt(json_decode($id));
    	if(isset($id)){
    		$testQuestion = Question::find($id);
    		if(is_object($testQuestion)){
                $loginUser = Auth::guard('web')->user();
                $testSubject = $testQuestion->subject;
                if(is_object($loginUser) && ($testSubject->created_by == $loginUser->id || (User::Hod ==  $loginUser->user_type || User::Directore ==  $loginUser->user_type))){
                    $testCategories = CollegeCategory::getCollegeCategoriesByCollegeIdByDeptId($loginUser->college_id);
                    $testSubCategories = TestSubCategory::getCollegeSubcategoriesByCategoryIdForAdmin($testQuestion->category_id);
                    if(User::Hod == $loginUser->user_type || User::Lecturer == $loginUser->user_type){
                        $testSubjects = TestSubject::getSubjectsByCollegeIdByAssignedDeptsByCategoryIdBySubCategoryId($loginUser->college_id,$testQuestion->category_id, $testQuestion->subcat_id);
                    } else {
                       $testSubjects = TestSubject::getCollegeSubjectsByCatIdBySubcatidForAdmin($testQuestion->category_id, $testQuestion->subcat_id);
                    }

                    $papers = TestSubjectPaper::getSubjectPapersBySubjectId($testQuestion->subject_id);
                    $prevQuestionId = $this->getPrevQuestionIdWithQuestionId($testQuestion->category_id,$testQuestion->subcat_id,$testQuestion->subject_id,$testQuestion->paper_id,$testQuestion->section_type, $testQuestion->id);
                    $nextQuestionId = $this->getNextQuestionIdWithQuestionId($testQuestion->category_id,$testQuestion->subcat_id,$testQuestion->subject_id,$testQuestion->paper_id,$testQuestion->section_type, $testQuestion->id);
                    $currentQuestionNo = $this->getCurrentQuestionNo($testQuestion->category_id,$testQuestion->subcat_id,$testQuestion->subject_id,$testQuestion->paper_id,$testQuestion->section_type, $testQuestion->id);
                    Session::put('selected_college_category', $testQuestion->category_id);
                    Session::put('selected_college_subcategory', $testQuestion->subcat_id);
                    Session::put('selected_college_subject', $testQuestion->subject_id);
                    Session::put('selected_college_paper', $testQuestion->paper_id);
                    Session::put('selected_college_section', $testQuestion->section_type);
                    Session::put('selected_college_question_type', $testQuestion->question_type);
                    Session::put('selected_college_prev_question', $testQuestion->id);
                    $nextQuestionNo = $this->getNextQuestionNo($testQuestion->category_id,$testQuestion->subcat_id,$testQuestion->subject_id,$testQuestion->paper_id,$testQuestion->section_type);
                    Session::put('next_college_question_no', $nextQuestionNo);

                    $paperSections =  PaperSection::where('test_subject_paper_id', $testQuestion->paper_id)->get();
                    return view('collegeModule.test.question.create', compact('testCategories', 'testSubCategories', 'testSubjects', 'testQuestion', 'papers', 'prevQuestionId', 'nextQuestionId', 'currentQuestionNo', 'paperSections'));
                }
    		}
    	}
		return Redirect::to('college/'.$collegeUrl.'/manageQuestions');
    }

    /**
     *  update question
     */
    protected function update($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
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

                Session::put('selected_college_category', $categoryId);
                Session::put('selected_college_subcategory', $subcategoryId);
                Session::put('selected_college_subject', $subjectId);
                Session::put('selected_college_paper', $paperId);
                Session::put('selected_college_section', $section_type);
                Session::put('selected_college_question_type', $question_type);
                if(1 == $request->get('check_common_data') && !empty($commonData)){
                    Session::put('last_college_common_data', $commonData);
                } else {
                    Session::remove('last_college_common_data');
                }

                $nextQuestionNo = $this->getNextQuestionNo($categoryId,$subcategoryId,$subjectId,$paperId,$section_type);
                Session::put('next_college_question_no', $nextQuestionNo);
                DB::commit();
                return Redirect::to('college/'.$collegeUrl.'/question/'.$testQuestion->id.'/edit')->with('message', 'Question updated successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('college/'.$collegeUrl.'/manageQuestions');
    }

    /**
     *  delete question
     */
    protected function delete($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
    	$questionId = InputSanitise::inputInt($request->get('question_id'));
    	if(isset($questionId)){
    		$testQuestion = Question::find($questionId);
    		if(is_object($testQuestion)){
                DB::beginTransaction();
                try
                {
                    $loginUser = Auth::guard('web')->user();
                    $testSubject = $testQuestion->subject;
                    if(is_object($loginUser) && ($testSubject->created_by == $loginUser->id || (User::Hod ==  $loginUser->user_type || User::Directore ==  $loginUser->user_type))){
                        UserSolution::deleteUserSolutionsByQuestionId($testQuestion->id);
            			$testQuestion->delete();
                        DB::commit();
                        return Redirect::to('college/'.$collegeUrl.'/manageQuestions')->with('message', 'Question deleted successfully!');
                    }
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return redirect()->back()->withErrors('something went wrong.');
                }
    		}
    	}
		return Redirect::to('college/'.$collegeUrl.'/manageQuestions');
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

    protected function uploadQuestions($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $loginUser = Auth::guard('web')->user();
        $testCategories = CollegeCategory::getCollegeCategoriesByCollegeIdByDeptId($loginUser->college_id);
        $testSubCategories = [];
        $testSubjects = [];
        $papers =[];
        return view('collegeModule.test.question.uploadQuestions', compact('testCategories','testSubCategories','testSubjects','papers'));
    }

    protected function importQuestions($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
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

                    $allQuestions[] = [
                        'name' => $questionStr,
                        'answer1' => $optionAStr,
                        'answer2' => $optionBStr,
                        'answer3' => $optionCStr,
                        'answer4' => $optionDStr,
                        'answer5' => $optionEStr,
                        'answer6' => 0,
                        'answer' => $question->right_answer,
                        'min' => (!empty($question->min))?:0,
                        'max' => (!empty($question->max))?:0,
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

                if(!empty($allQuestions)){
                    DB::beginTransaction();
                    try
                    {
                        DB::table('questions')->insert($allQuestions);
                        DB::commit();
                        return Redirect::to('college/'.$collegeUrl.'/uploadCollegeQuestions')->with('message', 'Questions added successfully!');
                    }
                    catch(\Exception $e)
                    {
                        DB::rollback();
                        return redirect()->back()->withErrors('something went wrong.');
                    }
                }
            }
        }
        return Redirect::to('college/'.$collegeUrl.'/uploadCollegeQuestions');
    }

    protected function uploadTestImages($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
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
            return Redirect::to('college/'.$collegeUrl.'/uploadCollegeQuestions')->with('message', 'Images uploaded successfully!');
        }
        return Redirect::to('college/'.$collegeUrl.'/uploadCollegeQuestions');
    }

    /**
     *  show questions
     */
    protected function showQuestionBank($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $loginUser = Auth::guard('web')->user();
        $testCategories = CollegeCategory::getCollegeCategoriesByCollegeIdByDeptId($loginUser->college_id);
        if(Session::has('search_college_selected_category')){
            $testSubCategories = TestSubCategory::getCollegeSubcategoriesByCategoryIdForAdmin(Session::get('search_college_selected_category'));
        } else {
            $testSubCategories = [];
        }
        if(Session::has('search_college_selected_category') && Session::has('search_college_selected_subcategory')){
            $testSubjects = TestSubject::getCollegeSubjectsByCatIdBySubcatIdByUser(Session::get('search_college_selected_category'), Session::get('search_college_selected_subcategory'));
        } else {
           $testSubjects = [];
        }
        if(Session::has('search_college_selected_category') && Session::has('search_college_selected_subcategory') && Session::has('search_college_selected_subject')){
            $papers = TestSubjectPaper::getCollegeSubjectPapersByCategoryIdBySubCategoryIdBySubjectIdForAdmin(Session::get('search_college_selected_category'), Session::get('search_college_selected_subcategory'), Session::get('search_college_selected_subject'));
        } else {
           $papers =[];
        }
        if(Session::has('search_college_selected_paper')){
            $paperSections =  PaperSection::where('test_subject_paper_id', Session::get('search_college_selected_paper'))->get();
        } else {
            $paperSections = [];
        }
        $questions = [];
        $bankCategories = QuestionBankCategory::all();
        if(Session::has('search_college_question_bank_category')){
            $bankSubCategories = QuestionBankSubCategory::getSubcategoriesByCategoryId(Session::get('search_college_question_bank_category'));
        } else {
            $bankSubCategories = [];
        }
        return view('collegeModule.test.question.useQuestionBank', compact('testCategories','testSubCategories','testSubjects', 'questions', 'papers', 'paperSections', 'bankCategories', 'bankSubCategories'));
    }

    /**
     *  show questions
     */
    protected function useCollegeQuestionBank($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $categoryId = InputSanitise::inputInt($request->get('category'));
        $subcategoryId = InputSanitise::inputInt($request->get('subcategory'));
        $subjectId = InputSanitise::inputInt($request->get('subject'));
        $paperId = InputSanitise::inputInt($request->get('paper'));
        $sectionTypeId = InputSanitise::inputInt($request->get('section_type'));
        $bankCategoryId = InputSanitise::inputInt($request->get('bank_category'));
        $bankSubCategoryId = InputSanitise::inputInt($request->get('bank_sub_category'));

        Session::put('search_college_selected_category', $categoryId);
        Session::put('search_college_selected_subcategory', $subcategoryId);
        Session::put('search_college_selected_subject', $subjectId);
        Session::put('search_college_selected_paper', $paperId);
        Session::put('search_college_selected_section', $sectionTypeId);
        Session::put('search_college_question_bank_category', $bankCategoryId);
        Session::put('search_college_question_bank_subcategory', $bankSubCategoryId);

        $loginUser = Auth::guard('web')->user();
        $testCategories = CollegeCategory::getCollegeCategoriesByCollegeIdByDeptId($loginUser->college_id);
        $testSubCategories = TestSubCategory::getCollegeSubcategoriesByCategoryIdForAdmin($categoryId);
        $testSubjects = TestSubject::getCollegeSubjectsByCatIdBySubcatIdByUser($categoryId,$subcategoryId);
        $papers = TestSubjectPaper::getCollegeSubjectPapersByCategoryIdBySubCategoryIdBySubjectIdForAdmin($categoryId,$subcategoryId, $subjectId);
        $paperSections = PaperSection::where('test_subject_paper_id', $paperId)->get();
        $questions = QuestionBankQuestion::getQuestionsByCategoryIdBySubcategoryId($bankCategoryId,$bankSubCategoryId);
        $bankCategories = QuestionBankCategory::all();
        $bankSubCategories = QuestionBankSubCategory::getSubcategoriesByCategoryId($bankCategoryId);
        return view('collegeModule.test.question.useQuestionBank', compact('testCategories','testSubCategories','testSubjects', 'questions', 'papers', 'paperSections', 'bankCategories', 'bankSubCategories'));
    }

    protected function exportCollegeQuestionBank($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $categoryId = InputSanitise::inputInt($request->get('selected_college_category'));
        $subcategoryId = InputSanitise::inputInt($request->get('selected_college_subcategory'));
        $subjectId = InputSanitise::inputInt($request->get('selected_college_subject'));
        $paperId = InputSanitise::inputInt($request->get('selected_college_paper'));
        $sectionTypeId = InputSanitise::inputInt($request->get('selected_section_type'));
        if(empty($categoryId) || empty($subcategoryId) || empty($subjectId) || empty($paperId) || empty($sectionTypeId)){
            return Redirect::to('college/'.$collegeUrl.'/showQuestionBank')->withErrors('Please select category, sub category, subject,paper and section.');
        }
        if(empty($request->get('selected'))){
            return Redirect::to('college/'.$collegeUrl.'/showQuestionBank')->withErrors('Please select question.');
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
                    return Redirect::to('college/'.$collegeUrl.'/showQuestionBank')->with('message', 'You have successfully created questions.');
                } else {
                    DB::rollback();
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return Redirect::to('college/'.$collegeUrl.'/showQuestionBank')->withErrors('something went wrong while transfering question.');
            }
        }
        return Redirect::to('college/'.$collegeUrl.'/showQuestionBank');
    }

    /**
     *  return sub categories by categoryId
     */
    public function getCollegeQuestionBankSubCategories(Request $request){
        if($request->ajax()){
            $id = InputSanitise::inputInt($request->get('id'));
            return QuestionBankSubCategory::getSubcategoriesByCategoryId($id);
        }
    }
}