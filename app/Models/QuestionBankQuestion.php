<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use DB,Cache;
use App\Models\TestSubjectPaper;

class QuestionBankQuestion extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'answer1', 'answer2', 'answer3', 'answer4', 'answer5', 'answer', 'category_id', 'subcat_id', 'question_type','solution', 'min','max'];

    /**
     *  add/update question
     */
    protected static function addOrUpdateQuestion( Request $request, $isUpdate=false){
        $categoryId = InputSanitise::inputInt($request->get('category'));
        $subcategoryId = InputSanitise::inputInt($request->get('subcategory'));
        $questionId = InputSanitise::inputInt($request->get('question_id'));
        $ans1 = '';
        $ans2 = '';
        $ans3 = '';
        $ans4 = '';
        $ans5 = '';
        $solution = '';
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
        $question_type = $request->get('question_type');
        $max = trim($request->get('max'));
        $min = trim($request->get('min'));

        if( $isUpdate && isset($questionId)){
            $testQuestion = static::find($questionId);
            if(!is_object($testQuestion)){
                return 'false';
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
        $testQuestion->category_id = $categoryId;
        $testQuestion->subcat_id = $subcategoryId;
        $testQuestion->answer = $answer;
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
        $testQuestion->solution = $solution;
        $testQuestion->question_type = $question_type;
        $testQuestion->save();
        return $testQuestion;
    }

    /**
     *  return questions by categoryId by sub categoryId
     */
    protected static function getQuestionsByCategoryIdBySubcategoryId($categoryId, $subcatId){

    	return static::where('category_id', $categoryId)->where('subcat_id', $subcatId)->get();
    }

    /**
     *  return question count
     */
    protected static function getNextQuestionNoByCategoryIdBySubcategoryId($categoryId,$subcategoryId){
        return static::where('category_id', $categoryId)->where('subcat_id', $subcategoryId)->count();
    }

    protected static function getCurrentQuestionNoByCategoryIdBySubcategoryId($categoryId,$subcategoryId,$questionId){
        return static::where('category_id', $categoryId)->where('subcat_id', $subcategoryId)->where('id', '<=', $questionId)->count();
    }

    protected static function getPrevQuestionByCategoryIdBySubcategoryId($categoryId,$subcategoryId,$questionId){
        $query = DB::table('question_bank_questions')
            ->where('category_id', $categoryId)
            ->where('subcat_id', $subcategoryId);
            if($questionId > 0){
                $query->where('id', '<', $questionId);
            }
        return $query->orderBy('id','desc')->first();
    }

    protected static function getNextQuestionByCategoryIdBySubcategoryId($categoryId,$subcategoryId,$questionId){
        $query = DB::table('question_bank_questions')
            ->where('category_id', $categoryId)
            ->where('subcat_id', $subcategoryId);
            if($questionId > 0){
                $query->where('id', '>', $questionId);
            }
        return $query->first();
    }
}
