<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB, Auth,Cache;
use App\Libraries\InputSanitise;
use App\Models\ClientOnlineTestCategory;
use App\Models\ClientOnlineTestSubCategory;
use App\Models\ClientOnlineTestSubjectPaper;

class ClientOnlineTestQuestion extends Model
{
    protected $connection = 'mysql2';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'answer1', 'answer2', 'answer3', 'answer4', 'answer5', 'answer6', 'answer', 'category_id', 'subcat_id', 'section_type', 'question_type','solution', 'positive_marks', 'negative_marks', 'min', 'max', 'subject_id', 'paper_id', 'client_id', 'common_data'];

    /**
     *  add/update question
     */
    protected static function addOrUpdateQuestion( Request $request, $isUpdate=false){
        $categoryId = InputSanitise::inputInt($request->get('category'));
        $subcategoryId = InputSanitise::inputInt($request->get('subcategory'));
        $questionId = InputSanitise::inputInt($request->get('question_id'));
        $subjectId = InputSanitise::inputInt($request->get('subject'));
        $paperId = InputSanitise::inputInt($request->get('paper'));
        $ans1 = '';
        $ans2 = '';
        $ans3 = '';
        $ans4 = '';
        $ans5 = '';
        $solution = '';
        $commonData = '';

        if(1 == $request->get('check_common_data') && !empty($request->get('common_data'))){
            $commonData = $request->get('common_data');
        }
        $question = $request->get('question');
        if(!empty($request->get('ans1')) || 0 == $request->get('ans1')){
            $ans1 = $request->get('ans1');
        }
        if(!empty($request->get('ans2')) || 0 == $request->get('ans2')){
            $ans2 = $request->get('ans2');
        }
        if(!empty($request->get('ans3')) || 0 == $request->get('ans3')){
            $ans3 = $request->get('ans3');
        }
        if(!empty($request->get('ans4')) || 0 == $request->get('ans4')){
            $ans4 = $request->get('ans4');
        }
        if(!empty($request->get('ans5')) || 0 == $request->get('ans5')){
            $ans5 = $request->get('ans5');
        }
        if(!empty($request->get('solution')) || 0 == $request->get('solution')){
            $solution = $request->get('solution');
        }

        $answer = $request->get('answer');
        $question_type = InputSanitise::inputInt($request->get('question_type'));
        $section_type = InputSanitise::inputInt($request->get('section_type'));
        $pos_marks = trim($request->get('pos_marks'));
        $neg_marks = trim($request->get('neg_marks'));
        $max = trim($request->get('max'));
        $min = trim($request->get('min'));

        if( $isUpdate && isset($questionId)){
            $testQuestion = static::find($questionId);
            if(!is_object($testQuestion)){
                return Redirect::to('manageOnlineTestQuestion');
            }
        } else{
            $testQuestion = new static;
        }
        $testQuestion->name = $question;
        $testQuestion->answer1 = $ans1;
        $testQuestion->answer2 = $ans2;
        $testQuestion->answer3 = $ans3;
        $testQuestion->answer4 = $ans4;
        $testQuestion->answer5 = $ans5;
        $testQuestion->answer6 = 0;
        $testQuestion->category_id = $categoryId;
        $testQuestion->subcat_id = $subcategoryId;
        $testQuestion->answer = $answer;
        $testQuestion->solution = $solution;
        if(empty($min)){
            $testQuestion->min = 0.00;
        } else {
            $testQuestion->min = $min;
        }
        if(empty($max)){
            $testQuestion->max = 0.00 ;
        } else {
            $testQuestion->max = $max ;
        }
        $testQuestion->section_type = $section_type;
        $testQuestion->positive_marks = $pos_marks;
        $testQuestion->negative_marks = $neg_marks;
        $testQuestion->subject_id = $subjectId;
        $testQuestion->paper_id = $paperId;
        $testQuestion->question_type = $question_type;
        $testQuestion->client_id = Auth::guard('client')->user()->id;
        $testQuestion->common_data = $commonData;
        $testQuestion->save();
        return $testQuestion;
    }

    /**
     *  add/update question
     */
    protected static function addOrUpdatePayableQuestion(Request $request, $isUpdate=false){
        $subcategoryId = InputSanitise::inputInt($request->get('subcategory'));
        $questionId = InputSanitise::inputInt($request->get('question_id'));
        $subjectId = InputSanitise::inputInt($request->get('subject'));
        $paperId = InputSanitise::inputInt($request->get('paper'));
        $ans1 = '';
        $ans2 = '';
        $ans3 = '';
        $ans4 = '';
        $ans5 = '';
        $solution = '';
        $commonData = '';

        if(1 == $request->get('check_common_data') && !empty($request->get('common_data'))){
            $commonData = $request->get('common_data');
        }
        $question = $request->get('question');
        if(!empty($request->get('ans1')) || 0 == $request->get('ans1')){
            $ans1 = $request->get('ans1');
        }
        if(!empty($request->get('ans2')) || 0 == $request->get('ans2')){
            $ans2 = $request->get('ans2');
        }
        if(!empty($request->get('ans3')) || 0 == $request->get('ans3')){
            $ans3 = $request->get('ans3');
        }
        if(!empty($request->get('ans4')) || 0 == $request->get('ans4')){
            $ans4 = $request->get('ans4');
        }
        if(!empty($request->get('ans5')) || 0 == $request->get('ans5')){
            $ans5 = $request->get('ans5');
        }
        if(!empty($request->get('solution')) || 0 == $request->get('solution')){
            $solution = $request->get('solution');
        }

        $answer = $request->get('answer');
        $question_type = InputSanitise::inputInt($request->get('question_type'));
        $section_type = InputSanitise::inputInt($request->get('section_type'));
        $pos_marks = trim($request->get('pos_marks'));
        $neg_marks = trim($request->get('neg_marks'));
        $max = trim($request->get('max'));
        $min = trim($request->get('min'));

        if( $isUpdate && isset($questionId)){
            $testQuestion = static::find($questionId);
            if(!is_object($testQuestion)){
                return Redirect::to('admin/managePayableQuestions');
            }
        } else{
            $testQuestion = new static;
        }
        $testQuestion->name = $question;
        $testQuestion->answer1 = $ans1;
        $testQuestion->answer2 = $ans2;
        $testQuestion->answer3 = $ans3;
        $testQuestion->answer4 = $ans4;
        $testQuestion->answer5 = $ans5;
        $testQuestion->answer6 = 0;
        $testQuestion->category_id = 0;
        $testQuestion->subcat_id = $subcategoryId;
        $testQuestion->answer = $answer;
        $testQuestion->solution = $solution;
        if(empty($min)){
            $testQuestion->min = 0.00;
        } else {
            $testQuestion->min = $min;
        }
        if(empty($max)){
            $testQuestion->max = 0.00 ;
        } else {
            $testQuestion->max = $max ;
        }
        $testQuestion->section_type = $section_type;
        $testQuestion->positive_marks = $pos_marks;
        $testQuestion->negative_marks = $neg_marks;
        $testQuestion->subject_id = $subjectId;
        $testQuestion->paper_id = $paperId;
        $testQuestion->question_type = $question_type;
        $testQuestion->client_id = 0;
        $testQuestion->common_data = $commonData;
        $testQuestion->save();
        return $testQuestion;
    }

    protected static function getClientQuestionsByCategoryIdBySubcategoryIdBySubjectIdByPaperIdBySectionType($categoryId,$subcategoryId,$subjectId,$paperId,$section_type){
    	return DB::connection('mysql2')->table('client_online_test_questions')
                    ->where('category_id', $categoryId)
                    ->where('subcat_id', $subcategoryId)
                    ->where('subject_id', $subjectId)
                    ->where('paper_id', $paperId)
                    ->where('section_type', $section_type)
                    ->where('client_id', Auth::guard('client')->user()->id)
                    ->get();
    }

    /**
     * get payable questions
     */
    protected static function getPayableQuestionsBySubcategoryIdBySubjectIdByPaperIdBySectionType($subcategoryId,$subjectId,$paperId,$section_type){
        return DB::connection('mysql2')->table('client_online_test_questions')
                    ->where('category_id', 0)
                    ->where('subcat_id', $subcategoryId)
                    ->where('subject_id', $subjectId)
                    ->where('paper_id', $paperId)
                    ->where('section_type', $section_type)
                    ->where('client_id', 0)
                    ->get();
    }

    /**
     * get payable questions count by sub category id
     */
    protected static function getPayableQuestionsCountBySubcategoryId($subcategoryId){
        $questionCount = [];
        $results = DB::connection('mysql2')->table('client_online_test_questions')
                    ->where('category_id', 0)
                    ->where('subcat_id', $subcategoryId)
                    ->where('client_id', 0)
                    ->select('paper_id', DB::raw('count(DISTINCT id) as count'))
                    ->groupBy('paper_id')
                    ->get();
        if(is_object($results) && false == $results->isEmpty()){
            foreach($results as $result){
                $questionCount[$result->paper_id] = $result->count;
            }
        }
        return $questionCount;
    }

     /**
     *  return question count by subjectId by paperId by section_type
     */

    protected static function getClientQuestionNoByCategoryIdBySubcategoryIdBySubjectIdByPaperIdBySectionType($categoryId,$subcategoryId,$subjectId,$paperId,$section_type){
        return DB::connection('mysql2')->table('client_online_test_questions')
            ->where('category_id', $categoryId)
            ->where('subcat_id', $subcategoryId)
            ->where('subject_id', $subjectId)
            ->where('paper_id', $paperId)
            ->where('section_type', $section_type)
            ->where('client_id', Auth::guard('client')->user()->id)
            ->count();
    }

    /**
     *  return payable question count by subjectId by paperId by section_type
     */

    protected static function getPayableQuestionNoByCategoryIdBySubcategoryIdBySubjectIdByPaperIdBySectionType($categoryId,$subcategoryId,$subjectId,$paperId,$section_type){
        return DB::connection('mysql2')->table('client_online_test_questions')
            ->where('category_id', $categoryId)
            ->where('subcat_id', $subcategoryId)
            ->where('subject_id', $subjectId)
            ->where('paper_id', $paperId)
            ->where('section_type', $section_type)
            ->where('client_id', 0)
            ->count();
    }


    protected static function getClientQuestionsByCategoryIdBySubcategoryIdBySubjectIdByPaperId($categoryId, $subcategoryId, $subjectId, $paperId, $request){
        if(0 == $categoryId){
            return DB::connection('mysql2')->table('client_online_test_questions')
                ->where('client_online_test_questions.category_id', $categoryId)
                ->where('client_online_test_questions.subcat_id', $subcategoryId)
                ->where('client_online_test_questions.subject_id', $subjectId)
                ->where('client_online_test_questions.paper_id', $paperId)
                ->select('client_online_test_questions.*')->get();
        } else {
            return DB::connection('mysql2')->table('client_online_test_questions')
                ->join('clients', 'clients.id', '=', 'client_online_test_questions.client_id')
                ->where('client_online_test_questions.category_id', $categoryId)
                ->where('client_online_test_questions.subcat_id', $subcategoryId)
                ->where('client_online_test_questions.subject_id', $subjectId)
                ->where('client_online_test_questions.paper_id', $paperId)
                ->where('clients.subdomain', InputSanitise::getCurrentClient($request))
                ->select('client_online_test_questions.*')->get();
        }
    }

    /**
     *  return questions by questions Ids
     */
    protected static function getQuestionsByIds($ids){
        return DB::connection('mysql2')->table('client_online_test_questions')->select('id','answer', 'question_type', 'min', 'max', 'positive_marks', 'negative_marks')->whereIn('id', $ids)->orderBy('id')->get();
    }

    protected static function getClientCurrentQuestionNoByCategoryIdBySubcategoryIdBySubjectIdByPaperIdBySectionType($categoryId,$subcategoryId,$subjectId,$paperId,$section_type,$questionId){
        return DB::connection('mysql2')->table('client_online_test_questions')
            ->where('category_id', $categoryId)
            ->where('subcat_id', $subcategoryId)
            ->where('subject_id', $subjectId)
            ->where('paper_id', $paperId)
            ->where('section_type', $section_type)
            ->where('id', '<=', $questionId)
            ->where('client_id', Auth::guard('client')->user()->id)
            ->count();
    }

    protected static function getPayableCurrentQuestionNoByCategoryIdBySubcategoryIdBySubjectIdByPaperIdBySectionType($categoryId,$subcategoryId,$subjectId,$paperId,$section_type,$questionId){
        return DB::connection('mysql2')->table('client_online_test_questions')
            ->where('category_id', $categoryId)
            ->where('subcat_id', $subcategoryId)
            ->where('subject_id', $subjectId)
            ->where('paper_id', $paperId)
            ->where('section_type', $section_type)
            ->where('id', '<=', $questionId)
            ->where('client_id', 0)
            ->count();
    }

    protected static function getClientPrevQuestionByCategoryIdBySubcategoryIdBySubjectIdByPaperIdBySectionType($categoryId,$subcategoryId,$subjectId,$paperId,$section_type,$questionId){
        $query = DB::connection('mysql2')->table('client_online_test_questions')
            ->where('category_id', $categoryId)
            ->where('subcat_id', $subcategoryId)
            ->where('subject_id', $subjectId)
            ->where('paper_id', $paperId)
            ->where('section_type', $section_type)
            ->where('client_id', Auth::guard('client')->user()->id);
            if($questionId > 0){
                $query->where('id', '<', $questionId);
            }
        return $query->orderBy('id','desc')->first();
    }

    protected static function getPayablePrevQuestionByCategoryIdBySubcategoryIdBySubjectIdByPaperIdBySectionType($categoryId,$subcategoryId,$subjectId,$paperId,$section_type,$questionId){
        $query = DB::connection('mysql2')->table('client_online_test_questions')
            ->where('category_id', $categoryId)
            ->where('subcat_id', $subcategoryId)
            ->where('subject_id', $subjectId)
            ->where('paper_id', $paperId)
            ->where('section_type', $section_type)
            ->where('client_id', 0);
            if($questionId > 0){
                $query->where('id', '<', $questionId);
            }
        return $query->orderBy('id','desc')->first();
    }

    protected static function getClientNextQuestionByCategoryIdBySubcategoryIdBySubjectIdByPaperIdBySectionType($categoryId,$subcategoryId,$subjectId,$paperId,$section_type,$questionId){
        $query = DB::connection('mysql2')->table('client_online_test_questions')
            ->where('category_id', $categoryId)
            ->where('subcat_id', $subcategoryId)
            ->where('subject_id', $subjectId)
            ->where('paper_id', $paperId)
            ->where('section_type', $section_type)
            ->where('client_id', Auth::guard('client')->user()->id);
            if($questionId > 0){
                $query->where('id', '>', $questionId);
            }
        return $query->first();
    }

    protected static function getPayableNextQuestionByCategoryIdBySubcategoryIdBySubjectIdByPaperIdBySectionType($categoryId,$subcategoryId,$subjectId,$paperId,$section_type,$questionId){
        $query = DB::connection('mysql2')->table('client_online_test_questions')
            ->where('category_id', $categoryId)
            ->where('subcat_id', $subcategoryId)
            ->where('subject_id', $subjectId)
            ->where('paper_id', $paperId)
            ->where('section_type', $section_type)
            ->where('client_id', 0);
            if($questionId > 0){
                $query->where('id', '>', $questionId);
            }
        return $query->first();
    }

    protected static function getClientQuestionsByCategoryIdBySubcategoryIdBySubjectIdByPaperIdByClientId($categoryId,$subcategoryId,$subjectId,$paperId,$clientId){
        return DB::connection('mysql2')->table('client_online_test_questions')
                    ->where('category_id', $categoryId)
                    ->where('subcat_id', $subcategoryId)
                    ->where('subject_id', $subjectId)
                    ->where('paper_id', $paperId)
                    ->where('client_id', $clientId)
                    ->get();
    }

    protected static function deleteClientOnlineTestQuestionsByClientId($clientId){
        $questions = static::where('client_id', $clientId)->get();
        if(is_object($questions) && false == $questions->isEmpty()){
            foreach($questions as $question){
                $question->delete();
            }
        }
    }

    /**
     *  get paper of question
     */
    public function paper(){
        return $this->belongsTo(ClientOnlineTestSubjectPaper::class, 'paper_id');
    }
}
