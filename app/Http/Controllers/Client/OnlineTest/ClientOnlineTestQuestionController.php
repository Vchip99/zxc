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
use App\Models\ClientNotification;
use App\Models\ClientUserSolution;
use App\Models\ClientOnlinePaperSection;
use App\Models\QuestionBankCategory;
use App\Models\QuestionBankQuestion;
use App\Models\QuestionBankSubCategory;
use Intervention\Image\ImageManagerStatic as Image;
use Excel;

class ClientOnlineTestQuestionController extends ClientBaseController
{
	/**
     *  check admin have permission or not, if not redirect to admin/home
     */
    public function __construct(Request $request) {
        parent::__construct($request);
        // $this->middleware('client');
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
    protected function index($subdomainName,Request $request){
        if(false == InputSanitise::checkDomain($request)){
            return Redirect::to('/');
        }
        if(false == InputSanitise::getCurrentGuard()){
            return Redirect::to('/');
        }
        $loginUser = InputSanitise::getLoginUserByGuardForClient();
        if(!is_object($loginUser)){
            return Redirect::to('/');
        } elseif(is_object($loginUser) && 'clientuser' == InputSanitise::getCurrentGuard() && 2 != $loginUser->user_type){
            return Redirect::to('/');
        }
        $resultArr = InputSanitise::getClientIdAndCretedBy();
        $clientId = $resultArr[0];
        $testCategories = ClientOnlineTestCategory::showCategories($request);

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
        if(Session::has('client_search_selected_paper')){
            $sessions = ClientOnlinePaperSection::paperSectionsByPaperId(Session::get('client_search_selected_paper'), $clientId);
        } else {
            $sessions = [];
        }

    	return view('client.onlineTest.question.list', compact('instituteCourses', 'testCategories', 'testSubCategories', 'testSubjects', 'questions', 'papers', 'sessions', 'subdomainName','loginUser'));
    }

    /**
     *  show all question associated with subject and paper
     */
    protected function show($subdomainName,Request $request){
        if(false == InputSanitise::checkDomain($request)){
            return Redirect::to('/');
        }
        if(false == InputSanitise::getCurrentGuard()){
            return Redirect::to('/');
        }
        $loginUser = InputSanitise::getLoginUserByGuardForClient();
        if(!is_object($loginUser)){
            return Redirect::to('/');
        } elseif(is_object($loginUser) && 'clientuser' == InputSanitise::getCurrentGuard() && 2 != $loginUser->user_type){
            return Redirect::to('/');
        }

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
            Session::put('client_search_selected_category', $categoryId);
            Session::put('client_search_selected_subcategory', $subcategoryId);
            Session::put('client_search_selected_subject', $subjectId);
            Session::put('client_search_selected_paper', $paperId);
            Session::put('client_search_selected_section', $sectionType);

            $resultArr = InputSanitise::getClientIdAndCretedBy();
            $clientId = $resultArr[0];

            $questions = ClientOnlineTestQuestion::getClientQuestionsByCategoryIdBySubcategoryIdBySubjectIdByPaperIdBySectionType($categoryId,$subcategoryId,$subjectId,$paperId,$sectionType);
            $testCategories = ClientOnlineTestCategory::showCategories($request);
            $testSubCategories = ClientOnlineTestSubCategory::getOnlineTestSubcategoriesByCategoryId(Session::get('client_search_selected_category'), $request);
            $testSubjects = ClientOnlineTestSubject::getOnlineSubjectsByCatIdBySubcatId(Session::get('client_search_selected_category'), Session::get('client_search_selected_subcategory'), $request);
            $papers = ClientOnlineTestSubjectPaper::getOnlineSubjectPapersByCategoryIdBySubCategoryIdBySubjectId(Session::get('client_search_selected_category'), Session::get('client_search_selected_subcategory'), Session::get('client_search_selected_subject'));
            $sessions = ClientOnlinePaperSection::paperSectionsByPaperId($paperId, $clientId);
    		return view('client.onlineTest.question.list', compact('testCategories', 'testSubCategories', 'testSubjects', 'questions', 'papers', 'sessions', 'subdomainName','loginUser'));
    	} else {
    		return Redirect::to('manageOnlineTestQuestion');
    	}
    }

    /**
     *  show UI for create question
     */
    protected function create($subdomainName,Request $request){
        if($subdomainName){
            InputSanitise::checkClientImagesDirForCkeditor($subdomainName);
        }

        if(false == InputSanitise::checkDomain($request)){
            return Redirect::to('/');
        }
        if(false == InputSanitise::getCurrentGuard()){
            return Redirect::to('/');
        }
        $loginUser = InputSanitise::getLoginUserByGuardForClient();
        if(!is_object($loginUser)){
            return Redirect::to('/');
        } elseif(is_object($loginUser) && 'clientuser' == InputSanitise::getCurrentGuard() && 2 != $loginUser->user_type){
            return Redirect::to('/');
        }
        $resultArr = InputSanitise::getClientIdAndCretedBy();
        $clientId = $resultArr[0];
        $testCategories = ClientOnlineTestCategory::showCategories($request);

        if(Session::has('client_selected_category')){
            $testSubCategories = ClientOnlineTestSubCategory::getOnlineTestSubcategoriesByCategoryId(Session::get('client_selected_category'), $request);
        } else {
            $testSubCategories = [];
        }
        if(Session::has('client_selected_category') && Session::has('client_selected_subcategory')){
            $testSubjects = ClientOnlineTestSubject::getOnlineSubjectsByCatIdBySubcatId(Session::get('client_selected_category'), Session::get('client_selected_subcategory'), $request);
        } else {
            $testSubjects = [];
        }
        if(Session::has('client_selected_category') && Session::has('client_selected_subcategory') && Session::has('client_selected_subject')){
            $papers = ClientOnlineTestSubjectPaper::getOnlineSubjectPapersByCategoryIdBySubCategoryIdBySubjectId(Session::get('client_selected_category'), Session::get('client_selected_subcategory'), Session::get('client_selected_subject'));
        } else {
           $papers = [];
        }

        if(Session::has('client_selected_paper')){
            $sessions = ClientOnlinePaperSection::paperSectionsByPaperId(Session::get('client_selected_paper'), $clientId);
        } else {
            $sessions = [];
        }

		$testQuestion = new ClientOnlineTestQuestion;
        $prevQuestionId =  Session::get('client_selected_prev_question');
        $nextQuestionId = 'new';
        $nextQuestionNo = $this->getNextQuestionNo(Session::get('client_selected_category'), Session::get('client_selected_subcategory'), Session::get('client_selected_subject'),Session::get('client_selected_paper'),Session::get('client_selected_section'));
        Session::put('client_next_question_no', $nextQuestionNo);
		return view('client.onlineTest.question.create', compact('testCategories', 'testSubCategories', 'testSubjects', 'testQuestion', 'papers', 'prevQuestionId', 'nextQuestionId', 'sessions', 'subdomainName','loginUser'));
    }

    /**
     *  store question
     */
    protected function store($subdomain,Request $request){
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
                $commonData = $request->get('common_data');

                Session::put('client_selected_category', $categoryId);
                Session::put('client_selected_subcategory', $subcategoryId);
                Session::put('client_selected_subject', $subjectId);
                Session::put('client_selected_paper', $paperId);
                Session::put('client_selected_section', $section_type);
                Session::put('client_selected_question_type', $question_type);
                Session::put('client_selected_prev_question', $testQuestion->id);
                if(1 == $request->get('check_common_data') && !empty($commonData)){
                    Session::put('last_common_data', $commonData);
                } else {
                    Session::remove('last_common_data');
                }

                $nextQuestionNo = $this->getNextQuestionNo($categoryId,$subcategoryId,$subjectId,$paperId,$section_type);
                Session::put('client_next_question_no', $nextQuestionNo);
                $resultArr = InputSanitise::getClientIdAndCretedBy();
                $clientId = $resultArr[0];
                $questionCount = ClientOnlineTestQuestion::where('category_id', $categoryId)->where('subcat_id', $subcategoryId)->where('subject_id', $subjectId)->where('paper_id', $paperId)->where('client_id', $clientId)->count();
                if(1 == $questionCount){
                    $paper = ClientOnlineTestSubjectPaper::find($paperId);
                    if(is_object($paper)){
                        $notificationMessage = 'A new test paper: <a href="'.$request->root().'/getTest/'.$subcategoryId.'/'.$subjectId.'/'.$paperId.'" target="_blank">'.$paper->name.'</a> has been added.';
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
    protected function edit( $subdomainName, $id, Request $request){
        if($subdomainName){
            InputSanitise::checkClientImagesDirForCkeditor($subdomainName);
        }
        if(false == InputSanitise::checkDomain($request)){
            return Redirect::to('/');
        }
        if(false == InputSanitise::getCurrentGuard()){
            return Redirect::to('/');
        }
        $loginUser = InputSanitise::getLoginUserByGuardForClient();
        if(!is_object($loginUser)){
            return Redirect::to('/');
        } elseif(is_object($loginUser) && 'clientuser' == InputSanitise::getCurrentGuard() && 2 != $loginUser->user_type){
            return Redirect::to('/');
        }
    	$id = InputSanitise::inputInt(json_decode($id));
    	if(isset($id)){
    		$testQuestion = ClientOnlineTestQuestion::find($id);
    		if(is_object($testQuestion)){
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

                $prevQuestionId = $this->getClientPrevQuestionIdWithQuestionId($testQuestion->category_id,$testQuestion->subcat_id,$testQuestion->subject_id,$testQuestion->paper_id,$testQuestion->section_type, $testQuestion->id);
                $nextQuestionId = $this->getClientNextQuestionIdWithQuestionId($testQuestion->category_id,$testQuestion->subcat_id,$testQuestion->subject_id,$testQuestion->paper_id,$testQuestion->section_type, $testQuestion->id);
                $currentQuestionNo = $this->getClientCurrentQuestionNo($testQuestion->category_id,$testQuestion->subcat_id,$testQuestion->subject_id,$testQuestion->paper_id,$testQuestion->section_type, $testQuestion->id);
                $nextQuestionNo = $this->getNextQuestionNo($testQuestion->category_id,$testQuestion->subcat_id,$testQuestion->subject_id,$testQuestion->paper_id,$testQuestion->section_type);
                Session::put('client_next_question_no', $nextQuestionNo);
                $sessions = ClientOnlinePaperSection::paperSectionsByPaperId($testQuestion->paper_id, $testQuestion->client_id);
                return view('client.onlineTest.question.create', compact('testCategories', 'testSubCategories', 'testSubjects', 'testQuestion', 'papers', 'prevQuestionId', 'nextQuestionId', 'currentQuestionNo', 'sessions', 'subdomainName','loginUser'));
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
                $commonData = $request->get('common_data');

                Session::put('client_selected_category', $categoryId);
                Session::put('client_selected_subcategory', $subcategoryId);
                Session::put('client_selected_subject', $subjectId);
                Session::put('client_selected_paper', $paperId);
                Session::put('client_selected_section', $section_type);
                Session::put('client_selected_question_type', $question_type);
                Session::put('client_selected_prev_question', $testQuestion->id);
                if(1 == $request->get('check_common_data') && !empty($commonData)){
                    Session::put('last_common_data', $commonData);
                } else {
                    Session::remove('last_common_data');
                }

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
                    ClientUserSolution::deleteClientUserSolutionsByQuestionId($questionId);
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

     /**
     *  show questions associated with subject and paper
     */
    protected function uploadQuestions($subdomainName,Request $request){
        if(false == InputSanitise::checkDomain($request)){
            return Redirect::to('/');
        }
        if(false == InputSanitise::getCurrentGuard()){
            return Redirect::to('/');
        }
        $loginUser = InputSanitise::getLoginUserByGuardForClient();
        if(!is_object($loginUser)){
            return Redirect::to('/');
        } elseif(is_object($loginUser) && 'clientuser' == InputSanitise::getCurrentGuard() && 2 != $loginUser->user_type){
            return Redirect::to('/');
        }
        $testCategories = ClientOnlineTestCategory::showCategories($request);
        $testSubCategories = [];
        $testSubjects = [];
        $papers = [];

        return view('client.onlineTest.question.uploadQuestions', compact('testCategories', 'testSubCategories', 'testSubjects', 'papers', 'subdomainName','loginUser'));
    }

    protected function importQuestions($subdomain, Request $request){
        if($request->hasFile('questions')){
            $path = $request->file('questions')->getRealPath();
            $questions = \Excel::selectSheetsByIndex(0)->load($path, function($reader) {
                            $reader->formatDates(false);
                        })->get();
            $resultArr = InputSanitise::getClientIdAndCretedBy();
            $clientId = $resultArr[0];
            $createdBy = $resultArr[1];
            if($questions->count()){
                foreach ($questions as $key => $question) {
                    preg_match_all('/image\[(.*)\]/', $question->question, $questionMatches);
                    if($questionMatches[1] && $questionMatches[1][0]){
                        $ImgTag = '<img src="/templateEditor/kcfinder/upload/images/'.$subdomain.'/';
                        $bodytag = str_replace("image[", $ImgTag, $question->question);
                        $questionStr = str_replace("]", '" style="max-width: 100%;max-height: 400px;" />', $bodytag);
                    } else {
                        $questionStr = $question->question;
                    }

                    preg_match_all('/image\[(.*)\]/', $question->option_a, $optionAMatches);
                    if($optionAMatches[1] && $optionAMatches[1][0]){
                        $ImgTag = '<img src="/templateEditor/kcfinder/upload/images/'.$subdomain.'/';
                        $bodytag = str_replace("image[", $ImgTag, $question->option_a);
                        $optionAStr = str_replace("]", '" style="max-width: 100%;max-height: 400px;" />', $bodytag);
                    } else {
                        $optionAStr = $question->option_a;
                    }

                    preg_match_all('/image\[(.*)\]/', $question->option_b, $optionBMatches);
                    if($optionBMatches[1] && $optionBMatches[1][0]){
                        $ImgTag = '<img src="/templateEditor/kcfinder/upload/images/'.$subdomain.'/';
                        $bodytag = str_replace("image[", $ImgTag, $question->option_b);
                        $optionBStr = str_replace("]", '" style="max-width: 100%;max-height: 400px;" />', $bodytag);
                    } else {
                        $optionBStr = $question->option_b;
                    }

                    preg_match_all('/image\[(.*)\]/', $question->option_c, $optionCMatches);
                    if($optionCMatches[1] && $optionCMatches[1][0]){
                        $ImgTag = '<img src="/templateEditor/kcfinder/upload/images/'.$subdomain.'/';
                        $bodytag = str_replace("image[", $ImgTag, $question->option_c);
                        $optionCStr = str_replace("]", '" style="max-width: 100%;max-height: 400px;" />', $bodytag);
                    } else {
                        $optionCStr = $question->option_c;
                    }

                    preg_match_all('/image\[(.*)\]/', $question->option_d, $optionDMatches);
                    if($optionDMatches[1] && $optionDMatches[1][0]){
                        $ImgTag = '<img src="/templateEditor/kcfinder/upload/images/'.$subdomain.'/';
                        $bodytag = str_replace("image[", $ImgTag, $question->option_d);
                        $optionDStr = str_replace("]", '" style="max-width: 100%;max-height: 400px;" />', $bodytag);
                    } else {
                        $optionDStr = $question->option_d;
                    }

                    preg_match_all('/image\[(.*)\]/', $question->option_e, $optionEMatches);
                    if($optionEMatches[1] && $optionEMatches[1][0]){
                        $ImgTag = '<img src="/templateEditor/kcfinder/upload/images/'.$subdomain.'/';
                        $bodytag = str_replace("image[", $ImgTag, $question->option_e);
                        $optionEStr = str_replace("]", '" style="max-width: 100%;max-height: 400px;" />', $bodytag);
                    } else {
                        $optionEStr = $question->option_e;
                    }

                    preg_match_all('/image\[(.*)\]/', $question->solution, $solutionMatches);
                    if($solutionMatches[1] && $solutionMatches[1][0]){
                        $ImgTag = '<img src="/templateEditor/kcfinder/upload/images/'.$subdomain.'/';
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
                        'client_id' => $clientId,
                        'created_by' => $createdBy,
                    ];
                }
                if(!empty($allQuestions)){
                    DB::connection('mysql2')->beginTransaction();
                    try
                    {
                        DB::connection('mysql2')->table('client_online_test_questions')->insert($allQuestions);
                        DB::connection('mysql2')->commit();
                        return Redirect::to('manageUploadQuestions')->with('message', 'Questions added successfully!');
                    }
                    catch(\Exception $e)
                    {
                        DB::connection('mysql2')->rollback();
                        return redirect()->back()->withErrors('something went wrong.');
                    }
                }
            }
        }
        return Redirect::to('manageUploadQuestions');
    }

    protected function uploadClientTestImages($subdomainName, Request $request){
        $allowedImageTypes = ['image/png','image/jpeg','image/jpg'];
        if($request->exists('images')){
            foreach($request->file('images') as $file){
                if(in_array($file->getClientMimeType(), $allowedImageTypes)){
                    $imageName = $file->getClientOriginalName();
                    $clientImagesFolder = public_path().'/templateEditor/kcfinder/upload/images/'. $subdomainName;
                    $file->move($clientImagesFolder, $imageName);
                    // open image
                    $img = Image::make($clientImagesFolder."/".$imageName);
                    // enable interlacing
                    $img->interlace(true);
                    // save image interlaced
                    $img->save();
                }
            }
            return Redirect::to('manageUploadQuestions')->with('message', 'Images uploaded successfully!');
        }
        return Redirect::to('manageUploadQuestions');
    }

    /**
     *  question bank
     */
    protected function manageQuestionBank($subdomainName,Request $request){
        if(false == InputSanitise::checkDomain($request)){
            return Redirect::to('/');
        }
        if(false == InputSanitise::getCurrentGuard()){
            return Redirect::to('/');
        }
        $loginUser = InputSanitise::getLoginUserByGuardForClient();
        if(!is_object($loginUser)){
            return Redirect::to('/');
        } elseif(is_object($loginUser) && 'clientuser' == InputSanitise::getCurrentGuard() && 2 != $loginUser->user_type){
            return Redirect::to('/');
        }
        $resultArr = InputSanitise::getClientIdAndCretedBy();
        $clientId = $resultArr[0];
        $testCategories = ClientOnlineTestCategory::showCategories($request);

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
        $questions = [];
        if(Session::has('client_search_selected_paper')){
            $sessions = ClientOnlinePaperSection::paperSectionsByPaperId(Session::get('client_search_selected_paper'), $clientId);
        } else {
            $sessions = [];
        }
        $bankCategories = QuestionBankCategory::all();
        if(Session::has('client_search_question_bank_category')){
            $bankSubCategories = QuestionBankSubCategory::getSubcategoriesByCategoryId(Session::get('client_search_question_bank_category'));
        } else {
            $bankSubCategories = [];
        }
        return view('client.onlineTest.question.useQuestionBank', compact('testCategories', 'testSubCategories', 'testSubjects', 'questions', 'papers', 'sessions', 'subdomainName', 'bankCategories', 'bankSubCategories','loginUser'));
    }

    /**
     *  show all question
     */
    protected function useQuestionBank($subdomainName,Request $request){
        $categoryId = InputSanitise::inputInt($request->get('category'));
        $subcategoryId = InputSanitise::inputInt($request->get('subcategory'));
        $subjectId = InputSanitise::inputInt($request->get('subject'));
        $paperId = InputSanitise::inputInt($request->get('paper'));
        $sectionType = InputSanitise::inputInt($request->get('section_type'));
        $bankCategoryId = InputSanitise::inputInt($request->get('bank_category'));
        $bankSubCategoryId = InputSanitise::inputInt($request->get('bank_sub_category'));

        Session::put('client_search_selected_category', $categoryId);
        Session::put('client_search_selected_subcategory', $subcategoryId);
        Session::put('client_search_selected_subject', $subjectId);
        Session::put('client_search_selected_paper', $paperId);
        Session::put('client_search_selected_section', $sectionType);
        Session::put('client_search_question_bank_category', $bankCategoryId);
        Session::put('client_search_question_bank_subcategory', $bankSubCategoryId);

        if(false == InputSanitise::checkDomain($request)){
            return Redirect::to('/');
        }
        if(false == InputSanitise::getCurrentGuard()){
            return Redirect::to('/');
        }
        $loginUser = InputSanitise::getLoginUserByGuardForClient();
        if(!is_object($loginUser)){
            return Redirect::to('/');
        } elseif(is_object($loginUser) && 'clientuser' == InputSanitise::getCurrentGuard() && 2 != $loginUser->user_type){
            return Redirect::to('/');
        }
        $resultArr = InputSanitise::getClientIdAndCretedBy();
        $clientId = $resultArr[0];
        $testCategories = ClientOnlineTestCategory::showCategories($request);
        $testSubCategories = ClientOnlineTestSubCategory::getOnlineTestSubcategoriesByCategoryId($categoryId, $request);
        $testSubjects = ClientOnlineTestSubject::getOnlineSubjectsByCatIdBySubcatId($categoryId, $subcategoryId, $request);
        $papers = ClientOnlineTestSubjectPaper::getOnlineSubjectPapersByCategoryIdBySubCategoryIdBySubjectId($categoryId, $subcategoryId, $subjectId);
        $sessions = ClientOnlinePaperSection::paperSectionsByPaperId($paperId, $clientId);
        $questions = QuestionBankQuestion::getQuestionsByCategoryIdBySubcategoryId($bankCategoryId,$bankSubCategoryId);
        $bankCategories = QuestionBankCategory::all();
        $bankSubCategories = QuestionBankSubCategory::getSubcategoriesByCategoryId($bankCategoryId);
        return view('client.onlineTest.question.useQuestionBank', compact('testCategories', 'testSubCategories', 'testSubjects', 'questions', 'papers', 'sessions', 'subdomainName', 'bankCategories', 'bankSubCategories','loginUser'));

    }

    /**
     *  return sub categories by categoryId
     */
    public function getQuestionBankSubCategories(Request $request){
        if($request->ajax()){
            $id = InputSanitise::inputInt($request->get('id'));
            return QuestionBankSubCategory::getSubcategoriesByCategoryId($id);
        }
    }

    protected function exportQuestionBank(Request $request){
        $categoryId = InputSanitise::inputInt($request->get('selected_category'));
        $subcategoryId = InputSanitise::inputInt($request->get('selected_subcategory'));
        $subjectId = InputSanitise::inputInt($request->get('selected_subject'));
        $paperId = InputSanitise::inputInt($request->get('selected_paper'));
        $sectionTypeId = InputSanitise::inputInt($request->get('selected_section_type'));
        if(empty($categoryId) || empty($subcategoryId) || empty($subjectId) || empty($paperId) || empty($sectionTypeId)){
            return Redirect::to('manageQuestionBank')->withErrors('Please select category, sub category, subject,paper and section.');
        }
        if(empty($request->get('selected'))){
            return Redirect::to('manageQuestionBank')->withErrors('Please select question.');
        }
        $selectedQuestions = $request->get('selected');
        if(count($selectedQuestions) > 0){
            DB::connection('mysql2')->beginTransaction();
            try
            {
                $insertedQuestionCount = 0;
                $resultArr = InputSanitise::getClientIdAndCretedBy();
                $clientId = $resultArr[0];
                $createdBy = $resultArr[1];
                foreach($selectedQuestions as $selectedQuestionId){
                    $question = QuestionBankQuestion::find($selectedQuestionId);
                    if(is_object($question)){
                        $testQuestion = new ClientOnlineTestQuestion;
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
                            $testQuestion->min = (!empty($question->min))?$question->min:0.00;
                            $testQuestion->max = (!empty($question->max))?$question->min:0.00;
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
                        $testQuestion->client_id = $clientId;
                        $testQuestion->common_data = '';
                        $testQuestion->created_by = $createdBy;
                        $testQuestion->save();
                        $insertedQuestionCount++;
                    }
                }
                if(count($selectedQuestions) == $insertedQuestionCount){
                    DB::connection('mysql2')->commit();
                    return Redirect::to('manageQuestionBank')->with('message', 'You have successfully created questions.');
                } else {
                    DB::connection('mysql2')->rollback();
                }
            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
                return Redirect::to('manageQuestionBank')->withErrors('something went wrong while transfering question.');
            }
        }
        return Redirect::to('manageQuestionBank')->withErrors('something went wrong.');
    }

}