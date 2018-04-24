<?php

namespace App\Http\Controllers\PayableTest;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect, Validator, Session, Auth, DB,Input;
use App\Libraries\InputSanitise;
use App\Models\ClientOnlineTestCategory;
use App\Models\ClientOnlineTestSubCategory;
use App\Models\ClientOnlineTestSubject;
use App\Models\ClientOnlineTestSubjectPaper;
use App\Models\ClientOnlineTestQuestion;
use App\Models\ClientNotification;
use App\Models\ClientUserSolution;
use App\Models\ClientOnlinePaperSection;
use Intervention\Image\ImageManagerStatic as Image;
use Excel;

class PayableQuestionController extends Controller
{
	/**
     *  check admin have permission or not, if not redirect to admin/home
     */
    public function __construct(Request $request) {
        $this->middleware(function ($request, $next) {
            $adminUser = Auth::guard('admin')->user();
            if(is_object($adminUser)){
                if($adminUser->hasRole('admin')){
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
    ];

    protected $validateCreateQuestion = [
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
        $testSubCategories = ClientOnlineTestSubCategory::showPayableSubCategories();

        if(Session::has('payable_search_selected_subcategory')){
            $testSubjects = ClientOnlineTestSubject::getPayableSubjectsBySubcatId(Session::get('payable_search_selected_subcategory'));
        } else {
           $testSubjects = [];
        }
        if(Session::has('payable_search_selected_subcategory') && Session::has('payable_search_selected_subject')){
            $papers = ClientOnlineTestSubjectPaper::getPayablePapersBySubCategoryIdBySubjectId(Session::get('payable_search_selected_subcategory'), Session::get('payable_search_selected_subject'));

        } else {
            $papers = [];
        }
        $questions = new ClientOnlineTestQuestion();
        if(Session::has('payable_search_selected_paper')){
            $sessions = ClientOnlinePaperSection::payablePaperSectionsByPaperId(Session::get('payable_search_selected_paper'));
        } else {
            $sessions = [];
        }

    	return view('payableTest.question.list', compact('testSubCategories', 'testSubjects', 'questions', 'papers', 'sessions'));
    }

    /**
     *  show all question associated with subject and paper
     */
    protected function show(Request $request){
        $subcategoryId = InputSanitise::inputInt($request->get('subcategory'));
    	$subjectId = InputSanitise::inputInt($request->get('subject'));
    	$paperId = InputSanitise::inputInt($request->get('paper'));
        $sectionType = InputSanitise::inputInt($request->get('section_type'));
    	$v = Validator::make($request->all(), $this->validateShowQuestions);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
    	if(isset($subcategoryId) && isset($subjectId) && isset($paperId)){
            Session::put('payable_search_selected_subcategory', $subcategoryId);
            Session::put('payable_search_selected_subject', $subjectId);
            Session::put('payable_search_selected_paper', $paperId);
            Session::put('payable_search_selected_section', $sectionType);

            $questions = ClientOnlineTestQuestion::getPayableQuestionsBySubcategoryIdBySubjectIdByPaperIdBySectionType($subcategoryId,$subjectId,$paperId,$sectionType);
            $testSubCategories = ClientOnlineTestSubCategory::showPayableSubCategories();
            $testSubjects = ClientOnlineTestSubject::getPayableSubjectsBySubcatId(Session::get('payable_search_selected_subcategory'));
            $papers = ClientOnlineTestSubjectPaper::getPayablePapersBySubCategoryIdBySubjectId(Session::get('payable_search_selected_subcategory'), Session::get('payable_search_selected_subject'));
            $sessions = ClientOnlinePaperSection::payablePaperSectionsByPaperId(Session::get('payable_search_selected_paper'));
    		return view('payableTest.question.list', compact('testSubCategories', 'testSubjects', 'questions', 'papers', 'sessions'));
    	} else {
    		return Redirect::to('admin/managePayableQuestions');
    	}
    }

    /**
     *  show UI for create question
     */
    protected function create(Request $request){

        $testSubCategories = ClientOnlineTestSubCategory::showPayableSubCategories();
        if(Session::has('payable_selected_subcategory')){
            $testSubjects = ClientOnlineTestSubject::getPayableSubjectsBySubcatId(Session::get('payable_selected_subcategory'));
        } else {
            $testSubjects = [];
        }
        if(Session::has('payable_selected_subcategory') && Session::has('payable_selected_subject')){
            $papers = ClientOnlineTestSubjectPaper::getPayablePapersBySubCategoryIdBySubjectId(Session::get('payable_selected_subcategory'), Session::get('payable_selected_subject'));
        } else {
           $papers = [];
        }

        if(Session::has('payable_selected_paper')){
            $sessions = ClientOnlinePaperSection::payablePaperSectionsByPaperId(Session::get('payable_selected_paper'));
        } else {
            $sessions = [];
        }

		$testQuestion = new ClientOnlineTestQuestion;
        $prevQuestionId =  Session::get('payable_selected_prev_question');
        $nextQuestionId = 'new';
        $nextQuestionNo = $this->getPayableNextQuestionNo(0,Session::get('payable_selected_subcategory'), Session::get('payable_selected_subject'),Session::get('payable_selected_paper'),Session::get('payable_selected_section'));
        Session::put('payable_next_question_no', $nextQuestionNo);
		return view('payableTest.question.create', compact('testSubCategories', 'testSubjects', 'testQuestion', 'papers', 'prevQuestionId', 'nextQuestionId', 'sessions'));
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
            $testQuestion = ClientOnlineTestQuestion::addOrUpdatePayableQuestion($request);
            if(is_object($testQuestion)){
                $subcategoryId = InputSanitise::inputInt($request->get('subcategory'));
                $subjectId = InputSanitise::inputInt($request->get('subject'));
                $paperId = InputSanitise::inputInt($request->get('paper'));
                $question_type = InputSanitise::inputInt($request->get('question_type'));
                $section_type = InputSanitise::inputInt($request->get('section_type'));
                $commonData = $request->get('common_data');

                Session::put('payable_selected_subcategory', $subcategoryId);
                Session::put('payable_selected_subject', $subjectId);
                Session::put('payable_selected_paper', $paperId);
                Session::put('payable_selected_section', $section_type);
                Session::put('payable_selected_question_type', $question_type);
                Session::put('payable_selected_prev_question', $testQuestion->id);
                if(1 == $request->get('check_common_data') && !empty($commonData)){
                    Session::put('payable_last_common_data', $commonData);
                } else {
                    Session::remove('payable_last_common_data');
                }

                $nextQuestionNo = $this->getPayableNextQuestionNo(0,$subcategoryId,$subjectId,$paperId,$section_type);
                Session::put('payable_next_question_no', $nextQuestionNo);

                DB::connection('mysql2')->commit();
                return Redirect::to("admin/createPayableQuestion")->with('message', 'Question created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('admin/managePayableQuestions');
    }

    /**
     *  edit question
     */
    protected function edit($id, Request $request){
    	$id = InputSanitise::inputInt(json_decode($id));
    	if(isset($id)){
    		$testQuestion = ClientOnlineTestQuestion::find($id);
    		if(is_object($testQuestion)){
                $testSubCategories = ClientOnlineTestSubCategory::showPayableSubCategories();
                $testSubjects = ClientOnlineTestSubject::getPayableSubjectsBySubcatId($testQuestion->subcat_id);
                $papers = ClientOnlineTestSubjectPaper::getPayablePapersBySubCategoryIdBySubjectId($testQuestion->subcat_id,$testQuestion->subject_id);

                Session::put('client_selected_subcategory', $testQuestion->subcat_id);
                Session::put('client_selected_subject', $testQuestion->subject_id);
                Session::put('client_selected_paper', $testQuestion->paper_id);
                Session::put('client_selected_section', $testQuestion->section_type);
                Session::put('client_selected_question_type', $testQuestion->question_type);

                $prevQuestionId = $this->getpayablePrevQuestionIdWithQuestionId($testQuestion->category_id,$testQuestion->subcat_id,$testQuestion->subject_id,$testQuestion->paper_id,$testQuestion->section_type, $testQuestion->id);
                $nextQuestionId = $this->getPayableNextQuestionIdWithQuestionId($testQuestion->category_id,$testQuestion->subcat_id,$testQuestion->subject_id,$testQuestion->paper_id,$testQuestion->section_type, $testQuestion->id);
                $currentQuestionNo = $this->getPayableCurrentQuestionNo($testQuestion->category_id,$testQuestion->subcat_id,$testQuestion->subject_id,$testQuestion->paper_id,$testQuestion->section_type, $testQuestion->id);
                $nextQuestionNo = $this->getPayableNextQuestionNo($testQuestion->category_id,$testQuestion->subcat_id,$testQuestion->subject_id,$testQuestion->paper_id,$testQuestion->section_type);
                Session::put('client_next_question_no', $nextQuestionNo);
                $sessions = ClientOnlinePaperSection::payablePaperSectionsByPaperId($testQuestion->paper_id);
                return view('payableTest.question.create', compact('testSubCategories', 'testSubjects', 'testQuestion', 'papers', 'prevQuestionId', 'nextQuestionId', 'currentQuestionNo', 'sessions'));
    		}
    	}
		return Redirect::to('admin/managePayableQuestions');
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
            $testQuestion = ClientOnlineTestQuestion::addOrUpdatePayableQuestion($request, true);
            if(is_object($testQuestion)){
                $categoryId = 0;
                $subcategoryId = InputSanitise::inputInt($request->get('subcategory'));
                $subjectId = InputSanitise::inputInt($request->get('subject'));
                $paperId = InputSanitise::inputInt($request->get('paper'));
                $question_type = InputSanitise::inputInt($request->get('question_type'));
                $section_type = InputSanitise::inputInt($request->get('section_type'));
                $commonData = $request->get('common_data');

                Session::put('payable_selected_subcategory', $subcategoryId);
                Session::put('payable_selected_subject', $subjectId);
                Session::put('payable_selected_paper', $paperId);
                Session::put('payable_selected_section', $section_type);
                Session::put('payable_selected_question_type', $question_type);
                Session::put('payable_selected_prev_question', $testQuestion->id);
                if(1 == $request->get('check_common_data') && !empty($commonData)){
                    Session::put('payable_last_common_data', $commonData);
                } else {
                    Session::remove('payable_last_common_data');
                }

                $nextQuestionNo = $this->getPayableNextQuestionNo($categoryId,$subcategoryId,$subjectId,$paperId,$section_type);
                Session::put('payable_next_question_no', $nextQuestionNo);
                DB::connection('mysql2')->commit();
                return Redirect::to("admin/payableQuestion/$testQuestion->id/edit")->with('message', 'Question updated successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('admin/managePayableQuestions');
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
                    return Redirect::to('admin/managePayableQuestions')->with('message', 'Question deleted successfully!');
        		}
            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
                return redirect()->back()->withErrors('something went wrong.');
            }
    	}
		return Redirect::to('admin/managePayableQuestions');
    }

    /**
     *  return question count by subjectIs, by peperId, By section_type
     */
    protected function getPayableNextQuestionCount(Request $request){
        $categoryId = 0;
        $subcategoryId = InputSanitise::inputInt($request->get('subcategoryId'));
        $subjectId = InputSanitise::inputInt($request->get('subjectId'));
        $paperId = InputSanitise::inputInt($request->get('paperId'));
        $section_type = InputSanitise::inputInt($request->get('section_type'));
        return $this->getPayableNextQuestionNo($categoryId,$subcategoryId,$subjectId,$paperId,$section_type);
    }

    /**
     *  return next question no by subjectId, by peperId, By section_type
     */
    protected function getPayableNextQuestionNo($categoryId,$subcategoryId,$subjectId,$paperId,$section_type){
        $subcategoryId = InputSanitise::inputInt($subcategoryId);
        $subjectId = InputSanitise::inputInt($subjectId);
        $paperId = InputSanitise::inputInt($paperId);
        $section_type = InputSanitise::inputInt($section_type);

        $totalQuestions = ClientOnlineTestQuestion::getPayableQuestionNoByCategoryIdBySubcategoryIdBySubjectIdByPaperIdBySectionType($categoryId,$subcategoryId,$subjectId,$paperId,$section_type);
        return (int) $totalQuestions + 1;
    }

    protected function getPayablePrevQuestionCount(Request $request){
        $categoryId = 0;
        $subcategoryId = InputSanitise::inputInt($request->get('subcategoryId'));
        $subjectId = InputSanitise::inputInt($request->get('subjectId'));
        $paperId = InputSanitise::inputInt($request->get('paperId'));
        $section_type = InputSanitise::inputInt($request->get('section_type'));
        $questionId = 0;
        return $this->getPayablePrevQuestionIdWithQuestionId($categoryId,$subcategoryId,$subjectId,$paperId,$section_type,$questionId);
    }

    protected function getPayablePrevQuestionIdWithQuestionId($categoryId,$subcategoryId,$subjectId,$paperId,$section_type,$questionId){
         $testQuestion = ClientOnlineTestQuestion::getPayablePrevQuestionByCategoryIdBySubcategoryIdBySubjectIdByPaperIdBySectionType($categoryId,$subcategoryId,$subjectId,$paperId,$section_type,$questionId);

        if(is_object($testQuestion)){
            return $testQuestion->id;
        }
        return;
    }

    protected function getPayableNextQuestionIdWithQuestionId($categoryId,$subcategoryId,$subjectId,$paperId,$section_type,$questionId){
        $testQuestion = ClientOnlineTestQuestion::getPayableNextQuestionByCategoryIdBySubcategoryIdBySubjectIdByPaperIdBySectionType($categoryId,$subcategoryId,$subjectId,$paperId,$section_type,$questionId);
        if(is_object($testQuestion)){
            return $testQuestion->id;
        }
        return;
    }

    protected function getPayableCurrentQuestionCount(Request $request){
        $categoryId = 0;
        $subcategoryId = InputSanitise::inputInt($request->get('subcategoryId'));
        $subjectId = InputSanitise::inputInt($request->get('subjectId'));
        $paperId = InputSanitise::inputInt($request->get('paperId'));
        $section_type = InputSanitise::inputInt($request->get('section_type'));
        $questionId = InputSanitise::inputInt($request->get('questionId'));
        return $this->getPayableCurrentQuestionNo($categoryId,$subcategoryId,$subjectId,$paperId,$section_type,$questionId);
    }

    protected function getPayableCurrentQuestionNo($categoryId,$subcategoryId,$subjectId,$paperId,$section_type,$questionId){
        return ClientOnlineTestQuestion::getPayableCurrentQuestionNoByCategoryIdBySubcategoryIdBySubjectIdByPaperIdBySectionType($categoryId,$subcategoryId,$subjectId,$paperId,$section_type,$questionId);
    }

     /**
     *  show questions associated with subject and paper
     */
    protected function uploadPayableQuestions(){
        $testSubCategories = ClientOnlineTestSubCategory::showPayableSubCategories();
        $testSubjects = [];
        $papers = [];

        return view('payableTest.question.uploadQuestions', compact('testSubCategories', 'testSubjects', 'papers'));
    }

    protected function importPayableQuestions(Request $request){
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
                        'min' => ($question->min)?:0,
                        'max' => ($question->max)?:0,
                        'question_type' => (int) $question->question_type,
                        'solution' => $solutionStr,
                        'positive_marks' => $question->positive_mark,
                        'negative_marks' => $question->negative_mark,
                        'common_data' => ($question->common_data)?:'',
                        'category_id' => 0,
                        'subcat_id' =>  $request->get('subcategory'),
                        'subject_id' => $request->get('subject'),
                        'paper_id' => $request->get('paper'),
                        'section_type' => $request->get('section_type'),
                        'client_id' => 0
                    ];
                }
                if(!empty($allQuestions)){
                    DB::connection('mysql2')->beginTransaction();
                    try
                    {
                        DB::connection('mysql2')->table('client_online_test_questions')->insert($allQuestions);
                        DB::connection('mysql2')->commit();
                        return Redirect::to('admin/uploadPayableQuestions')->with('message', 'Questions added successfully!');
                    }
                    catch(\Exception $e)
                    {
                        DB::connection('mysql2')->rollback();
                        return redirect()->back()->withErrors('something went wrong.');
                    }
                }
            }
        }
        return Redirect::to('admin/uploadPayableQuestions');
    }

    protected function uploadPayableImages(Request $request){
        $allowedImageTypes = ['image/png','image/jpeg'];
        if($request->exists('images')){
            foreach($request->file('images') as $file){
                if(in_array($file->getClientMimeType(), $allowedImageTypes)){
                    $imageName = $file->getClientOriginalName();
                    $imagesFolder = public_path().'/templateEditor/kcfinder/upload/images/';
                    $file->move($imagesFolder, $imageName);
                    // open image
                    $img = Image::make($imagesFolder."/".$imageName);
                    // enable interlacing
                    $img->interlace(true);
                    // save image interlaced
                    $img->save();
                }
            }
            return Redirect::to('admin/uploadPayableQuestions')->with('message', 'Images uploaded successfully!');
        }
        return Redirect::to('admin/uploadPayableQuestions');
    }

}