<?php

namespace App\Http\Controllers\QuestionBank;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\QuestionBankCategory;
use App\Models\QuestionBankSubCategory;
use App\Models\QuestionBankQuestion;
use Redirect,Validator,Session,Auth,DB;
use App\Libraries\InputSanitise;
use Illuminate\Support\Facades\Mail;
use Intervention\Image\ImageManagerStatic as Image;
use Excel;

class QuestionBankQuestionController extends Controller
{
    /**
     *  check admin have permission or not, if not redirect to admin/home
     */
    public function __construct() {
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
    	'category' => 'required|integer',
        'subcategory' => 'required|integer'
    ];

    protected $validateCreateQuestion = [
        'category' => 'required|integer',
        'subcategory' => 'required|integer',
        'question' => 'required|string',
        'solution' => 'required|string',
    ];

    /**
     *  show questions associated cat & sub cat
     */
    protected function index(){
        $testCategories = QuestionBankCategory::all();
        if(Session::has('search_question_bank_category')){
            $testSubCategories = QuestionBankSubCategory::getSubcategoriesByCategoryId(Session::get('search_question_bank_category'));
        } else {
            $testSubCategories = [];
        }
    	$questions = [];
    	return view('questionBank.question.list', compact('testCategories','testSubCategories','questions'));
    }

    /**
     *  show all question associated with subject and paper
     */
    protected function show(Request $request){
        $categoryId = InputSanitise::inputInt($request->get('category'));
        $subcategoryId = InputSanitise::inputInt($request->get('subcategory'));
    	$v = Validator::make($request->all(), $this->validateShowQuestions);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }

    	if(isset($categoryId) && isset($subcategoryId)){
            Session::put('search_question_bank_category', $categoryId);
            Session::put('search_question_bank_subcategory', $subcategoryId);
    		$questions = QuestionBankQuestion::getQuestionsByCategoryIdBySubcategoryId($categoryId,$subcategoryId);
            $testCategories = QuestionBankCategory::all();
            $testSubCategories = QuestionBankSubCategory::getSubcategoriesByCategoryId($categoryId);
    		return view('questionBank.question.list', compact('testCategories','testSubCategories','questions'));
    	} else {
    		return Redirect::to('admin/manageQuestionBankQuestions');
    	}
    }

    /**
     *  show UI for create question
     */
    protected function create(Request $request){

        $testCategories = QuestionBankCategory::all();
        if(Session::has('selected_question_bank_category')){
            $testSubCategories = QuestionBankSubCategory::getSubcategoriesByCategoryId(Session::get('selected_question_bank_category'));
        } else {
            $testSubCategories = [];
        }
		$testQuestion = new QuestionBankQuestion;
        $prevQuestionId = Session::get('selected_bank_prev_question');
        $nextQuestionId = 'new';
        $nextQuestionNo = $this->getNextQuestionNo(Session::get('selected_question_bank_category'),Session::get('selected_question_bank_subcategory'));
        Session::put('question_bank_next_question_no', $nextQuestionNo);
		return view('questionBank.question.create', compact('testCategories', 'testSubCategories', 'testQuestion','prevQuestionId', 'nextQuestionId'));
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
            $testQuestion = QuestionBankQuestion::addOrUpdateQuestion($request);
            if(is_object($testQuestion)){
                $categoryId = InputSanitise::inputInt($request->get('category'));
                $subcategoryId = InputSanitise::inputInt($request->get('subcategory'));
                $question_type = InputSanitise::inputInt($request->get('question_type'));

                Session::put('selected_question_bank_category', $categoryId);
                Session::put('selected_question_bank_subcategory', $subcategoryId);
                Session::put('selected_question_bank_question_type', $question_type);
                Session::put('selected_bank_prev_question', $testQuestion->id);

                $nextQuestionNo = $this->getNextQuestionNo($categoryId,$subcategoryId);
                Session::put('question_bank_next_question_no', $nextQuestionNo);
                DB::commit();
                return Redirect::to("admin/createQuestionBankQuestion")->with('message', 'Question created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('admin/manageQuestionBankQuestions');
    }

    /**
     *  edit question
     */
    protected function edit($id){
    	$id = InputSanitise::inputInt(json_decode($id));
    	if(isset($id)){
    		$testQuestion = QuestionBankQuestion::find($id);
    		if(is_object($testQuestion)){
                $testCategories = QuestionBankCategory::all();
                $testSubCategories = QuestionBankSubCategory::getSubcategoriesByCategoryId($testQuestion->category_id);
                $currentQuestionNo = $this->getCurrentQuestionNo($testQuestion->category_id,$testQuestion->subcat_id,$testQuestion->id);

                $prevQuestionId = $this->getPrevQuestionIdWithQuestionId($testQuestion->category_id,$testQuestion->subcat_id,$testQuestion->id);
                $nextQuestionId = $this->getNextQuestionIdWithQuestionId($testQuestion->category_id,$testQuestion->subcat_id,$testQuestion->id);

                Session::put('selected_bank_prev_question', $testQuestion->id);
                Session::put('selected_question_bank_category', $testQuestion->category_id);
                Session::put('selected_question_bank_subcategory', $testQuestion->subcat_id);
                Session::put('selected_question_bank_question_type', $testQuestion->question_type);
                $nextQuestionNo = $this->getNextQuestionNo($testQuestion->category_id,$testQuestion->subcat_id);
                Session::put('question_bank_next_question_no', $nextQuestionNo);
                return view('questionBank.question.create', compact('testCategories', 'testSubCategories', 'testQuestion', 'currentQuestionNo','prevQuestionId', 'nextQuestionId'));
    		}
    	}
		return Redirect::to('admin/manageQuestionBankQuestions');
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
            $testQuestion = QuestionBankQuestion::addOrUpdateQuestion($request, true);
            if(is_object($testQuestion)){
                $categoryId = InputSanitise::inputInt($request->get('category'));
                $subcategoryId = InputSanitise::inputInt($request->get('subcategory'));
                $question_type = InputSanitise::inputInt($request->get('question_type'));

                Session::put('selected_question_bank_category', $categoryId);
                Session::put('selected_question_bank_subcategory', $subcategoryId);
                Session::put('selected_question_bank_question_type', $question_type);

                $nextQuestionNo = $this->getNextQuestionNo($categoryId,$subcategoryId);
                Session::put('question_bank_next_question_no', $nextQuestionNo);
                DB::commit();
                return Redirect::to("admin/questionBankQuestion/".$testQuestion->id."/edit")->with('message', 'Question updated successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('admin/manageQuestionBankQuestions');
    }

    /**
     *  delete question
     */
    protected function delete(Request $request){
    	$questionId = InputSanitise::inputInt($request->get('question_id'));
    	if(isset($questionId)){
    		$testQuestion = QuestionBankQuestion::find($questionId);
    		if(is_object($testQuestion)){
                DB::beginTransaction();
                try
                {
        			$testQuestion->delete();
                    DB::commit();
                    return Redirect::to('admin/manageQuestionBankQuestions')->with('message', 'Question deleted successfully!');
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return redirect()->back()->withErrors('something went wrong.');
                }
    		}
    	}
		return Redirect::to('admin/manageQuestionBankQuestions');
    }

    /**
     *  return question count
     */
    protected function getNextQuestionBankQuestionCount(Request $request){
        $categoryId = InputSanitise::inputInt($request->get('categoryId'));
        $subcategoryId = InputSanitise::inputInt($request->get('subcategoryId'));
        return $this->getNextQuestionNo($categoryId,$subcategoryId);
    }

    /**
     *  return next question no
     */
    protected function getNextQuestionNo($categoryId,$subcategoryId){
        $categoryId = InputSanitise::inputInt($categoryId);
        $subcategoryId = InputSanitise::inputInt($subcategoryId);
        $totalQuestions = QuestionBankQuestion::getNextQuestionNoByCategoryIdBySubcategoryId($categoryId,$subcategoryId);
        return (int) $totalQuestions + 1;
    }

    protected function getCurrentQuestionCount(Request $request){
        $categoryId = InputSanitise::inputInt($request->get('categoryId'));
        $subcategoryId = InputSanitise::inputInt($request->get('subcategoryId'));
        $questionId = InputSanitise::inputInt($request->get('questionId'));
        return $this->getCurrentQuestionNo($categoryId,$subcategoryId,$questionId);
    }

    protected function getCurrentQuestionNo($categoryId,$subcategoryId,$questionId){
        return QuestionBankQuestion::getCurrentQuestionNoByCategoryIdBySubcategoryId($categoryId,$subcategoryId,$questionId);
    }

    protected function uploadQuestions(){
        $testCategories = QuestionBankCategory::all();
        $testSubCategories = [];
        return view('questionBank.question.uploadQuestions', compact('testCategories','testSubCategories'));
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

                    $allQuestions[] = [
                        'name' => $questionStr,
                        'answer1' => $optionAStr,
                        'answer2' => $optionBStr,
                        'answer3' => $optionCStr,
                        'answer4' => $optionDStr,
                        'answer5' => $optionEStr,
                        'answer' => $question->right_answer,
                        'question_type' => (int) $question->question_type,
                        'solution' => $solutionStr,
                        'min' => $question->min,
                        'max' => $question->max,
                        'category_id' => $request->get('category'),
                        'subcat_id' =>  $request->get('subcategory'),
                        'created_at' => date('Y-m-d h:i:s'),
                        'updated_at' => date('Y-m-d h:i:s')
                    ];
                }

                if(!empty($allQuestions)){
                    DB::beginTransaction();
                    try
                    {
                        DB::table('question_bank_questions')->insert($allQuestions);
                        DB::commit();
                        return Redirect::to('admin/uploadQuestionBankQuestions')->with('message', 'Questions added successfully!');
                    }
                    catch(\Exception $e)
                    {
                        DB::rollback();
                        return redirect()->back()->withErrors('something went wrong.');
                    }
                }
            }
        }
        return Redirect::to('admin/uploadQuestionBankQuestions');
    }

    protected function uploadQuestionBankImages(Request $request){
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
            return Redirect::to('admin/uploadQuestionBankQuestions')->with('message', 'Images uploaded successfully!');
        }
        return Redirect::to('admin/uploadQuestionBankQuestions');
    }

    protected function getPrevQuestionIdWithQuestionId($categoryId,$subcategoryId,$questionId){
         $testQuestion = QuestionBankQuestion::getPrevQuestionByCategoryIdBySubcategoryId($categoryId,$subcategoryId,$questionId);
        if(is_object($testQuestion)){
            return $testQuestion->id;
        }
        return;
    }

    protected function getNextQuestionIdWithQuestionId($categoryId,$subcategoryId,$questionId){
        $testQuestion = QuestionBankQuestion::getNextQuestionByCategoryIdBySubcategoryId($categoryId,$subcategoryId,$questionId);
        if(is_object($testQuestion)){
            return $testQuestion->id;
        }
        return;
    }
}
