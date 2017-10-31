<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use DB;
use App\Models\TestSubjectPaper;

class Question extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'answer1', 'answer2', 'answer3', 'answer4', 'answer5', 'answer6', 'answer', 'category_id', 'subcat_id', 'section_type', 'question_type','solution', 'positive_marks', 'negative_marks', 'min', 'max', 'subject_id', 'paper_id', 'common_data'];

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
        $newInstance = new static;

        if(1 == $request->get('check_common_data') && !empty($request->get('common_data'))){
            $commonData = $newInstance->changeSrc($request->get('common_data'));
        }
        $question = $newInstance->changeSrc($request->get('question'));
        if(!empty($request->get('ans1'))){
            $ans1 = $newInstance->changeSrc($request->get('ans1'));
        }
        if(!empty($request->get('ans2'))){
            $ans2 = $newInstance->changeSrc($request->get('ans2'));
        }
        if(!empty($request->get('ans3'))){
            $ans3 = $newInstance->changeSrc($request->get('ans3'));
        }
        if(!empty($request->get('ans4'))){
            $ans4 = $newInstance->changeSrc($request->get('ans4'));
        }
        if(!empty($request->get('ans5'))){
            $ans5 = $newInstance->changeSrc($request->get('ans5'));
        }
        if(!empty($request->get('solution'))){
            $solution = $newInstance->changeSrc($request->get('solution'));
        }
        $answer = InputSanitise::inputString($request->get('answer'));
        $question_type = InputSanitise::inputInt($request->get('question_type'));
        $section_type = InputSanitise::inputInt($request->get('section_type'));
        $pos_marks = trim($request->get('pos_marks'));
        $neg_marks = trim($request->get('neg_marks'));
        $max = trim($request->get('max'));
        $min = trim($request->get('min'));


        if( $isUpdate && isset($questionId)){
            $testQuestion = Question::find($questionId);
            if(!is_object($testQuestion)){
                return Redirect::to('admin/manageQuestions');
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
        $testQuestion->common_data = $commonData;
        $testQuestion->save();
        return $testQuestion;
    }

    protected function changeSrc($question){
        $formatedQuestion = '';
        if(preg_match('/src=\"/',$question)){
            $contents   = explode("src=\"" , $question);
            if(count($contents) > 0){
                foreach($contents as  $index => $content) {
                    if(strstr($content, '/templateEditor') && !strstr($content, asset(''))){
                        $formatedQuestion .= 'src="'.rtrim(asset(''),'/') . $content;
                    } else {
                        if( 0 == $index && strstr($content, '<img alt=""')){
                            $formatedQuestion .= $content;
                        } else {
                            $formatedQuestion .= 'src="'.$content;
                        }
                    }
                }
            }
        } else {
            $formatedQuestion = $question;
        }
        return $formatedQuestion;
    }

    /**
     *  return questions by categoryId by sub categoryId
     */
    protected static function getQuestionsByCategoryIdBySubcatId($categoryId, $subcatId){

    	$questions = DB::table('questions')->select('id')->where('category_id', 1)->where('subcat_id', 0)->get();
    	return $questions;
    }

    /**
     *  return questions by questions Ids
     */
    protected function getQuestionsByIds($ids){

        return $questions = DB::table('questions')->select('id','answer', 'question_type', 'positive_marks', 'negative_marks', 'min', 'max')->whereIn('id', $ids)->orderBy('id')->get();
    }

    /**
     *  return questions by subjectId by paperId
     */
    protected static function getQuestionsByCategoryIdBySubcategoryIdBySubjectIdByPaperIdBySectionType($categoryId,$subcategoryId,$subjectId, $paperId, $sectionTypeId){
        $subjectId = InputSanitise::inputInt($subjectId);
        $paperId = InputSanitise::inputInt($paperId);
        $sectionTypeId = InputSanitise::inputInt($sectionTypeId);
        return DB::table('questions')
            ->where('category_id', $categoryId)
            ->where('subcat_id', $subcategoryId)
            ->where('subject_id', $subjectId)
            ->where('paper_id', $paperId)
            ->where('section_type', $sectionTypeId)
            ->select('questions.*')
            ->get();
    }

        /**
     *  return questions by subjectId by paperId
     */
    protected static function getQuestionsForSessionAssociation($categoryId,$subcategoryId,$subjectId, $paperId, $sectionTypeId){
        $subjectId = InputSanitise::inputInt($subjectId);
        $paperId = InputSanitise::inputInt($paperId);
        $sectionTypeId = InputSanitise::inputInt($sectionTypeId);
        return DB::table('questions')
            ->where('category_id', $categoryId)
            ->where('subcat_id', $subcategoryId)
            ->where('subject_id', $subjectId)
            ->where('paper_id', $paperId)
            // ->where('section_type', $sectionTypeId)
            ->select('questions.*')
            ->get();
    }

    /**
     *  return question count by subjectId by paperId by section_type
     */
    protected static function getNextQuestionNoByCategoryIdBySubcategoryIdBySubjectIdByPaperIdBySectionType($categoryId,$subcategoryId,$subjectId,$paperId,$section_type){
        return DB::table('questions')
            ->where('category_id', $categoryId)
            ->where('subcat_id', $subcategoryId)
            ->where('subject_id', $subjectId)
            ->where('paper_id', $paperId)
            ->where('section_type', $section_type)
            ->count();
    }

    protected static function getCurrentQuestionNoByCategoryIdBySubcategoryIdBySubjectIdByPaperIdBySectionType($categoryId,$subcategoryId,$subjectId,$paperId,$section_type,$questionId){
        return DB::table('questions')
            ->where('category_id', $categoryId)
            ->where('subcat_id', $subcategoryId)
            ->where('subject_id', $subjectId)
            ->where('paper_id', $paperId)
            ->where('section_type', $section_type)
            ->where('id', '<=', $questionId)
            ->count();
    }

    protected static function getPrevQuestionByCategoryIdBySubcategoryIdBySubjectIdByPaperIdBySectionType($categoryId,$subcategoryId,$subjectId,$paperId,$section_type,$questionId){
        $query = DB::table('questions')
            ->where('category_id', $categoryId)
            ->where('subcat_id', $subcategoryId)
            ->where('subject_id', $subjectId)
            ->where('paper_id', $paperId)
            ->where('section_type', $section_type);
            if($questionId > 0){
                $query->where('id', '<', $questionId);
            }
        return $query->orderBy('id','desc')->first();
    }

    protected static function getNextQuestionByCategoryIdBySubcategoryIdBySubjectIdByPaperIdBySectionType($categoryId,$subcategoryId,$subjectId,$paperId,$section_type,$questionId){
        $query = DB::table('questions')
            ->where('category_id', $categoryId)
            ->where('subcat_id', $subcategoryId)
            ->where('subject_id', $subjectId)
            ->where('paper_id', $paperId)
            ->where('section_type', $section_type);
            if($questionId > 0){
                $query->where('id', '>', $questionId);
            }
        return $query->first();
    }

    protected static function getQuestionsByCategoryIdBySubcategoryIdBySubjectIdByPaperId($categoryId, $subcategoryId, $subjectId, $paperId){
        return DB::table('questions')
            ->where('category_id', $categoryId)
            ->where('subcat_id', $subcategoryId)
            ->where('subject_id', $subjectId)
            ->where('paper_id', $paperId)
            ->select('questions.*')
            ->get();
    }

    /**
     *  get paper
     */
    public function paper(){
        return $this->belongsTo(TestSubjectPaper::class, 'paper_id');
    }
}
