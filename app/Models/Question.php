<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use DB,Cache;
use App\Models\TestSubjectPaper;
use App\Models\TestCategory;
use App\Models\TestSubCategory;
use App\Models\CollegeCategory;

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
            $testQuestion = Question::find($questionId);
            if(!is_object($testQuestion)){
                return 'false';
            }
        } else{
            $testQuestion = new static;
        }
        $testQuestion->name = trim($question);
        $testQuestion->answer1 = trim($ans1);
        $testQuestion->answer2 = trim($ans2);
        $testQuestion->answer3 = trim($ans3);
        $testQuestion->answer4 = trim($ans4);
        $testQuestion->answer5 = trim($ans5);
        $testQuestion->answer6 = 0;
        $testQuestion->category_id = $categoryId;
        $testQuestion->subcat_id = $subcategoryId;
        $testQuestion->answer = trim($answer);
        $testQuestion->solution = trim($solution);
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
        $testQuestion->positive_marks = trim($pos_marks);
        $testQuestion->negative_marks = trim($neg_marks);
        $testQuestion->subject_id = $subjectId;
        $testQuestion->paper_id = $paperId;
        $testQuestion->question_type = $question_type;
        $testQuestion->common_data = trim($commonData);
        $testQuestion->save();
        return $testQuestion;
    }

    /**
     *  return questions by questions Ids
     */
    protected function getQuestionsByIds($ids){

        return DB::table('questions')->select('id','answer', 'question_type', 'positive_marks', 'negative_marks', 'min', 'max')->whereIn('id', $ids)->orderBy('id')->get();
    }

    /**
     *  return questions by subjectId by paperId
     */
    protected static function getQuestionsByCategoryIdBySubcategoryIdBySubjectIdByPaperIdBySectionType($categoryId,$subcategoryId,$subjectId, $paperId, $sectionTypeId){
        $subjectId = InputSanitise::inputInt($subjectId);
        $paperId = InputSanitise::inputInt($paperId);
        $sectionTypeId = InputSanitise::inputInt($sectionTypeId);
        // return DB::table('questions')
        //     ->where('category_id', $categoryId)
        //     ->where('subcat_id', $subcategoryId)
        //     ->where('subject_id', $subjectId)
        //     ->where('paper_id', $paperId)
        //     ->where('section_type', $sectionTypeId)
        //     ->select('questions.*')
        //     ->get();
        return DB::table('questions')->join('test_categories', 'test_categories.id', '=', 'questions.category_id')
            ->join('test_sub_categories', 'test_sub_categories.id', '=', 'questions.subcat_id')
            ->join('test_subjects', 'test_subjects.id', '=', 'questions.subject_id')
            ->join('test_subject_papers', 'test_subject_papers.id', '=', 'questions.paper_id')
            ->where('test_sub_categories.created_for', 1)
            ->where('questions.category_id', $categoryId)
            ->where('questions.subcat_id', $subcategoryId)
            ->where('questions.subject_id', $subjectId)
            ->where('questions.paper_id', $paperId)
            ->where('questions.section_type', $sectionTypeId)
            ->select('questions.*')
            ->get();
    }

    /**
     *  return questions by subjectId by paperId
     */
    protected static function getCollegeQuestionsByCategoryIdBySubcategoryIdBySubjectIdByPaperIdBySectionType($categoryId,$subcategoryId,$subjectId, $paperId, $sectionTypeId){
        $subjectId = InputSanitise::inputInt($subjectId);
        $paperId = InputSanitise::inputInt($paperId);
        $sectionTypeId = InputSanitise::inputInt($sectionTypeId);
        return DB::table('questions')->join('college_categories', 'college_categories.id', '=', 'questions.category_id')
            ->join('test_sub_categories', 'test_sub_categories.id', '=', 'questions.subcat_id')
            ->join('test_subjects', 'test_subjects.id', '=', 'questions.subject_id')
            ->join('test_subject_papers', 'test_subject_papers.id', '=', 'questions.paper_id')
            ->where('test_sub_categories.created_for', 0)
            ->where('questions.category_id', $categoryId)
            ->where('questions.subcat_id', $subcategoryId)
            ->where('questions.subject_id', $subjectId)
            ->where('questions.paper_id', $paperId)
            ->where('questions.section_type', $sectionTypeId)
            ->select('questions.*','test_subjects.created_by')
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
        // return DB::table('questions')
        //     ->where('category_id', $categoryId)
        //     ->where('subcat_id', $subcategoryId)
        //     ->where('subject_id', $subjectId)
        //     ->where('paper_id', $paperId)
        //     ->select('questions.*')
        //     ->get();
        return Cache::remember('vchip:tests:Questions:cat-'.$categoryId.':subcat-'.$subcategoryId.':subj-'.$subjectId.':paper-'.$paperId,30, function() use ($categoryId, $subcategoryId,$subjectId,$paperId) {
                return  static::where('category_id', $categoryId)
                ->where('subcat_id', $subcategoryId)
                ->where('subject_id', $subjectId)
                ->where('paper_id', $paperId)->get();
            });
    }

    /**
     *  get paper
     */
    public function paper(){
        return $this->belongsTo(TestSubjectPaper::class, 'paper_id');
    }

    /**
     *  get category of question
     */
    public function category(){
        return $this->belongsTo(TestCategory::class, 'category_id');
    }

    /**
     *  get sub category of question
     */
    public function subcategory(){
        return $this->belongsTo(TestSubCategory::class, 'subcat_id');
    }

    /**
     *  get category of paper
     */
    public function subject(){
        return $this->belongsTo(TestSubject::class, 'subject_id');
    }
}
