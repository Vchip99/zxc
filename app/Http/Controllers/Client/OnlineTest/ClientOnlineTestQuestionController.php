<?php

namespace App\Http\Controllers\Client\OnlineTest;

use Illuminate\Http\Request;
use App\Http\Controllers\Client\ClientBaseController;
use Redirect;
use Validator, Session, Auth, DB,Input;
use App\Libraries\InputSanitise;
use App\Models\ClientOnlineTestCategory;
use App\Models\ClientOnlineTestSubCategory;
use App\Models\ClientOnlineTestSubject;
use App\Models\ClientOnlineTestSubjectPaper;
use App\Models\ClientOnlineTestQuestion;
use App\Models\ClientInstituteCourse;
use App\Models\ClientNotification;

class ClientOnlineTestQuestionController extends ClientBaseController
{
	/**
     *  check admin have permission or not, if not redirect to admin/home
     */
    public function __construct(Request $request) {
        parent::__construct($request);
        $this->middleware('client');
    }

	/**
     * Define your validation rules in a property in
     * the controller to reuse the rules.
     */
    protected $validateShowQuestions = [
            'subject' => 'required',
            'paper' => 'required',
    ];

    protected $validateCreateQuestion = [
        'institute_course' => 'required|integer',
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
    protected function index(Request $request){
        $coursePermission = InputSanitise::checkModulePermission($request, 'test');
        if('false' == $coursePermission){
            return Redirect::to('manageClientHome');
        }
        $clientId = Auth::guard('client')->user()->id;
        $instituteCourses = ClientInstituteCourse::where('client_id', $clientId)->get();

        $testCategories = ClientOnlineTestCategory::showCategories($request);
        // $testCategories = new ClientOnlineTestCategory;

        if(Session::has('client_search_selected_category')){
            $testSubCategories = ClientOnlineTestSubCategory::getOnlineTestSubcategoriesByCategoryId(Session::get('client_search_selected_category'), $request);
        } else {
            $testSubCategories = [];
        }
        if(Session::has('client_search_selected_category') && Session::has('client_search_selected_subcategory')){
            $testSubjects = ClientOnlineTestSubject::getOnlineSubjectsByCatIdBySubcatId(Session::get('client_search_selected_category'), Session::get('client_search_selected_subcategory'), $request);
        } else {
           $testSubjects = [];
        }
        if(Session::has('client_search_selected_category') && Session::has('client_search_selected_subcategory') && Session::has('client_search_selected_subject')){
            $papers = ClientOnlineTestSubjectPaper::getOnlineSubjectPapersByCategoryIdBySubCategoryIdBySubjectId(Session::get('client_search_selected_category'), Session::get('client_search_selected_subcategory'), Session::get('client_search_selected_subject'));

        } else {
           $papers = [];
        }
        $questions = new ClientOnlineTestQuestion();

    	return view('client.onlineTest.question.list', compact('instituteCourses', 'testCategories', 'testSubCategories', 'testSubjects', 'questions', 'papers'));
    }

    /**
     *  show all question associated with subject and paper
     */
    protected function show(Request $request){
        $coursePermission = InputSanitise::checkModulePermission($request, 'test');
        if('false' == $coursePermission){
            return Redirect::to('manageClientHome');
        }
        $instituteCourseId = InputSanitise::inputInt($request->get('institute_course'));
        $categoryId = InputSanitise::inputInt($request->get('category'));
        $subcategoryId = InputSanitise::inputInt($request->get('subcategory'));
    	$subjectId = InputSanitise::inputInt($request->get('subject'));
    	$paperId = InputSanitise::inputInt($request->get('paper'));
        $sectionType = InputSanitise::inputInt($request->get('section_type'));
    	$v = Validator::make($request->all(), $this->validateShowQuestions);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }

    	if(isset($categoryId) && isset($subcategoryId) && isset($subjectId) && isset($paperId)){
            Session::put('client_search_selected_institute_category', $instituteCourseId);
            Session::put('client_search_selected_category', $categoryId);
            Session::put('client_search_selected_subcategory', $subcategoryId);
            Session::put('client_search_selected_subject', $subjectId);
            Session::put('client_search_selected_paper', $paperId);
            Session::put('client_search_selected_section', $sectionType);

            $clientId = Auth::guard('client')->user()->id;
            $instituteCourses = ClientInstituteCourse::where('client_id', $clientId)->get();

            $questions = ClientOnlineTestQuestion::getClientQuestionsByCategoryIdBySubcategoryIdBySubjectIdByPaperIdBySectionType($categoryId,$subcategoryId,$subjectId,$paperId,$sectionType);
            $testCategories = ClientOnlineTestCategory::showCategories($request);
            $testSubCategories = ClientOnlineTestSubCategory::getOnlineTestSubcategoriesByCategoryId(Session::get('client_search_selected_category'), $request);
            $testSubjects = ClientOnlineTestSubject::getOnlineSubjectsByCatIdBySubcatId(Session::get('client_search_selected_category'), Session::get('client_search_selected_subcategory'), $request);
            $papers = ClientOnlineTestSubjectPaper::getOnlineSubjectPapersByCategoryIdBySubCategoryIdBySubjectId(Session::get('client_search_selected_category'), Session::get('client_search_selected_subcategory'), Session::get('client_search_selected_subject'));
    		return view('client.onlineTest.question.list', compact('instituteCourses','testCategories', 'testSubCategories', 'testSubjects', 'questions', 'papers'));
    	} else {
    		return Redirect::to('manageOnlineTestQuestion');
    	}
    }

    /**
     *  show UI for create question
     */
    protected function create(Request $request){
        $clientId = Auth::guard('client')->user()->id;
        $instituteCourses = ClientInstituteCourse::where('client_id', $clientId)->get();

        $testCategories = ClientOnlineTestCategory::showCategories($request);

        if(Session::has('client_selected_category')){
            $testSubCategories = ClientOnlineTestSubCategory::getOnlineTestSubcategoriesByCategoryId(Session::get('client_selected_category'), $request);
        } else {
            $testSubCategories = ClientOnlineTestSubCategory::where('client_id', Auth::guard('client')->user()->id)
                    ->get();
        }
        if(Session::has('client_selected_category') && Session::has('client_selected_subcategory')){
            $testSubjects = ClientOnlineTestSubject::getOnlineSubjectsByCatIdBySubcatId(Session::get('client_selected_category'), Session::get('client_selected_subcategory'), $request);
        } else {
           $testSubjects = ClientOnlineTestSubject::where('client_id', Auth::guard('client')->user()->id)
                    ->get();
        }
        if(Session::has('client_selected_category') && Session::has('client_selected_subcategory') && Session::has('client_selected_subject')){
            $papers = ClientOnlineTestSubjectPaper::getOnlineSubjectPapersByCategoryIdBySubCategoryIdBySubjectId(Session::get('client_selected_category'), Session::get('client_selected_subcategory'), Session::get('client_selected_subject'));
        } else {
           $papers = ClientOnlineTestSubjectPaper::where('client_id', Auth::guard('client')->user()->id)
                    ->get();
        }

		$testQuestion = new ClientOnlineTestQuestion;
        $prevQuestionId =  Session::get('client_selected_prev_question');
        $nextQuestionId = 'new';
        $nextQuestionNo = $this->getNextQuestionNo(Session::get('client_selected_category'), Session::get('client_selected_subcategory'), Session::get('client_selected_subject'),Session::get('client_selected_paper'),Session::get('client_selected_section'));
        Session::put('client_next_question_no', $nextQuestionNo);
		return view('client.onlineTest.question.create', compact('instituteCourses','testCategories', 'testSubCategories', 'testSubjects', 'testQuestion', 'papers', 'prevQuestionId', 'nextQuestionId'));
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
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $testQuestion = ClientOnlineTestQuestion::addOrUpdateQuestion($request);
            if(is_object($testQuestion)){
                $categoryId = InputSanitise::inputInt($request->get('category'));
                $subcategoryId = InputSanitise::inputInt($request->get('subcategory'));
                $subjectId = InputSanitise::inputInt($request->get('subject'));
                $paperId = InputSanitise::inputInt($request->get('paper'));
                $question_type = InputSanitise::inputInt($request->get('question_type'));
                $section_type = InputSanitise::inputInt($request->get('section_type'));
                $instituteCourseId   = InputSanitise::inputInt($request->get('institute_course'));

                Session::put('client_selected_institute_category', $instituteCourseId);
                Session::put('client_selected_category', $categoryId);
                Session::put('client_selected_subcategory', $subcategoryId);
                Session::put('client_selected_subject', $subjectId);
                Session::put('client_selected_paper', $paperId);
                Session::put('client_selected_section', $section_type);
                Session::put('client_selected_question_type', $question_type);
                Session::put('client_selected_prev_question', $testQuestion->id);
                $nextQuestionNo = $this->getNextQuestionNo($categoryId,$subcategoryId,$subjectId,$paperId,$section_type);
                Session::put('client_next_question_no', $nextQuestionNo);

                $questionCount = ClientOnlineTestQuestion::where('category_id', $categoryId)->where('subcat_id', $subcategoryId)->where('subject_id', $subjectId)->where('paper_id', $paperId)->where('client_id', Auth::guard('client')->user()->id)->count();
                if(1 == $questionCount){
                    $paper = ClientOnlineTestSubjectPaper::find($paperId);
                    if(is_object($paper)){
                        $notificationMessage = 'A new test paper: <a href="'.$request->root().'/getTest/'.$subcategoryId.'/'.$subjectId.'/'.$paperId.'">'.$paper->name.'</a> has been added.';
                        ClientNotification::addNotification($notificationMessage, ClientNotification::CLIENTPAPER, $paperId,$testQuestion->client_institute_course_id);
                    }
                }

                DB::connection('mysql2')->commit();
                return Redirect::to("createOnlineTestQuestion")->with('message', 'Question created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('manageOnlineTestQuestion');
    }

    /**
     *  edit question
     */
    protected function edit( $subdomain, $id, Request $request){
    	$id = InputSanitise::inputInt(json_decode($id));
    	if(isset($id)){
    		$testQuestion = ClientOnlineTestQuestion::find($id);
    		if(is_object($testQuestion)){
                $instituteCourses = ClientInstituteCourse::where('client_id', $testQuestion->client_institute_course_id)->get();

                $testCategories = ClientOnlineTestCategory::showCategories($request);
                $testSubCategories = ClientOnlineTestSubCategory::getOnlineTestSubcategoriesByCategoryId($testQuestion->category_id, $request);
                $testSubjects = ClientOnlineTestSubject::getOnlineSubjectsByCatIdBySubcatId($testQuestion->category_id, $testQuestion->subcat_id,$request);
                $papers = ClientOnlineTestSubjectPaper::getOnlinePapersBySubjectId($testQuestion->subject_id);

                Session::put('client_selected_category', $testQuestion->category_id);
                Session::put('client_selected_subcategory', $testQuestion->subcat_id);
                Session::put('client_selected_subject', $testQuestion->subject_id);
                Session::put('client_selected_paper', $testQuestion->paper_id);
                Session::put('client_selected_section', $testQuestion->section_type);
                Session::put('client_selected_question_type', $testQuestion->question_type);
                Session::put('client_selected_institute_category', $testQuestion->client_institute_course_id);

                $prevQuestionId = $this->getClientPrevQuestionIdWithQuestionId($testQuestion->category_id,$testQuestion->subcat_id,$testQuestion->subject_id,$testQuestion->paper_id,$testQuestion->section_type, $testQuestion->id);
                $nextQuestionId = $this->getClientNextQuestionIdWithQuestionId($testQuestion->category_id,$testQuestion->subcat_id,$testQuestion->subject_id,$testQuestion->paper_id,$testQuestion->section_type, $testQuestion->id);
                $currentQuestionNo = $this->getClientCurrentQuestionNo($testQuestion->category_id,$testQuestion->subcat_id,$testQuestion->subject_id,$testQuestion->paper_id,$testQuestion->section_type, $testQuestion->id);
                $nextQuestionNo = $this->getNextQuestionNo($testQuestion->category_id,$testQuestion->subcat_id,$testQuestion->subject_id,$testQuestion->paper_id,$testQuestion->section_type);
                Session::put('client_next_question_no', $nextQuestionNo);
                return view('client.onlineTest.question.create', compact('instituteCourses','testCategories', 'testSubCategories', 'testSubjects', 'testQuestion', 'papers', 'prevQuestionId', 'nextQuestionId', 'currentQuestionNo'));
    		}
    	}
		return Redirect::to('manageOnlineTestQuestion');
    }

    /**
     *  update question
     */
    protected function update(Request $request){
        $v = Validator::make($request->all(), $this->validateCreateQuestion);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $testQuestion = ClientOnlineTestQuestion::addOrUpdateQuestion($request, true);
            if(is_object($testQuestion)){
                $categoryId = InputSanitise::inputInt($request->get('category'));
                $subcategoryId = InputSanitise::inputInt($request->get('subcategory'));
                $subjectId = InputSanitise::inputInt($request->get('subject'));
                $paperId = InputSanitise::inputInt($request->get('paper'));
                $question_type = InputSanitise::inputInt($request->get('question_type'));
                $section_type = InputSanitise::inputInt($request->get('section_type'));

                Session::put('client_selected_category', $categoryId);
                Session::put('client_selected_subcategory', $subcategoryId);
                Session::put('client_selected_subject', $subjectId);
                Session::put('client_selected_paper', $paperId);
                Session::put('client_selected_section', $section_type);
                Session::put('client_selected_question_type', $question_type);
                Session::put('client_selected_prev_question', $testQuestion->id);
                $nextQuestionNo = $this->getNextQuestionNo($categoryId,$subcategoryId,$subjectId,$paperId,$section_type);
                Session::put('client_next_question_no', $nextQuestionNo);
                DB::connection('mysql2')->commit();
                return Redirect::to("onlinetestquestion/$testQuestion->id/edit")->with('message', 'Question updated successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('manageOnlineTestQuestion');
    }

    /**
     *  delete question
     */
    protected function delete(Request $request){
    	$questionId = InputSanitise::inputInt($request->get('question_id'));
    	if(isset($questionId)){
            DB::connection('mysql2')->beginTransaction();
            try
            {
        		$testQuestion = ClientOnlineTestQuestion::find($questionId);
        		if(is_object($testQuestion)){
        			$testQuestion->delete();
                    DB::connection('mysql2')->commit();
                    return Redirect::to('manageOnlineTestQuestion')->with('message', 'Question deleted successfully!');
        		}
            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
                return redirect()->back()->withErrors('something went wrong.');
            }
    	}
		return Redirect::to('manageOnlineTestQuestion');
    }

    /**
     *  return question count by subjectIs, by peperId, By section_type
     */
    protected function getClientNextQuestionCount(Request $request){
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

        $totalQuestions = ClientOnlineTestQuestion::getClientQuestionNoByCategoryIdBySubcategoryIdBySubjectIdByPaperIdBySectionType($categoryId,$subcategoryId,$subjectId,$paperId,$section_type);
        return (int) $totalQuestions + 1;
    }

    protected function getClientPrevQuestion(Request $request){
        $categoryId = InputSanitise::inputInt($request->get('categoryId'));
        $subcategoryId = InputSanitise::inputInt($request->get('subcategoryId'));
        $subjectId = InputSanitise::inputInt($request->get('subjectId'));
        $paperId = InputSanitise::inputInt($request->get('paperId'));
        $section_type = InputSanitise::inputInt($request->get('section_type'));
        $questionId = 0;
        return $this->getClientPrevQuestionIdWithQuestionId($categoryId,$subcategoryId,$subjectId,$paperId,$section_type,$questionId);
    }

    protected function getClientPrevQuestionIdWithQuestionId($categoryId,$subcategoryId,$subjectId,$paperId,$section_type,$questionId){
         $testQuestion = ClientOnlineTestQuestion::getClientPrevQuestionByCategoryIdBySubcategoryIdBySubjectIdByPaperIdBySectionType($categoryId,$subcategoryId,$subjectId,$paperId,$section_type,$questionId);

        if(is_object($testQuestion)){
            return $testQuestion->id;
        }
        return;
    }

    protected function getClientNextQuestionIdWithQuestionId($categoryId,$subcategoryId,$subjectId,$paperId,$section_type,$questionId){
        $testQuestion = ClientOnlineTestQuestion::getClientNextQuestionByCategoryIdBySubcategoryIdBySubjectIdByPaperIdBySectionType($categoryId,$subcategoryId,$subjectId,$paperId,$section_type,$questionId);
        if(is_object($testQuestion)){
            return $testQuestion->id;
        }
        return;
    }

    protected function getClientCurrentQuestionCount(Request $request){
        $categoryId = InputSanitise::inputInt($request->get('categoryId'));
        $subcategoryId = InputSanitise::inputInt($request->get('subcategoryId'));
        $subjectId = InputSanitise::inputInt($request->get('subjectId'));
        $paperId = InputSanitise::inputInt($request->get('paperId'));
        $section_type = InputSanitise::inputInt($request->get('section_type'));
        $questionId = InputSanitise::inputInt($request->get('questionId'));
        return $this->getClientCurrentQuestionNo($categoryId,$subcategoryId,$subjectId,$paperId,$section_type,$questionId);
    }

    protected function getClientCurrentQuestionNo($categoryId,$subcategoryId,$subjectId,$paperId,$section_type,$questionId){
        return ClientOnlineTestQuestion::getClientCurrentQuestionNoByCategoryIdBySubcategoryIdBySubjectIdByPaperIdBySectionType($categoryId,$subcategoryId,$subjectId,$paperId,$section_type,$questionId);
    }

}