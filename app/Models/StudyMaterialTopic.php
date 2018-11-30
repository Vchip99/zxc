<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use App\Models\CourseSubCategory;
use App\Models\CourseCategory;
use App\Models\StudyMaterialSubject;
use DB,Auth;

class StudyMaterialTopic extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['course_category_id', 'course_sub_category_id','study_material_subject_id','name','content'];

    /**
     *  add/update topic
     */
    protected static function addOrUpdateStudyMaterialTopic( Request $request, $isUpdate=false){
        $categoryId = InputSanitise::inputInt($request->get('category'));
        $subcategoryId = InputSanitise::inputInt($request->get('subcategory'));
        $subjectId = InputSanitise::inputInt($request->get('subject'));
        $topicName = InputSanitise::inputString($request->get('topic'));
        $topicId = InputSanitise::inputInt($request->get('topic_id'));
        $content = $request->get('content');

        if( $isUpdate && isset($topicId)){
            $topic = static::find($topicId);
            if(!is_object($topic)){
                return 'false';
            }
        } else {
            $topic = new static;
        }

        $topic->name = $topicName;
        $topic->course_category_id = $categoryId;
        $topic->course_sub_category_id = $subcategoryId;
        $topic->study_material_subject_id = $subjectId;
        $topic->content = $content;
        $topic->save();
        return $topic;
    }

    protected static function isStudyMaterialTopicExist($request){
        $categoryId = InputSanitise::inputInt($request->get('category'));
        $subcategoryId = InputSanitise::inputInt($request->get('subcategory'));
        $subjectId = InputSanitise::inputInt($request->get('subject'));
        $topicName = InputSanitise::inputString($request->get('topic'));
        $topicId = InputSanitise::inputInt($request->get('topic_id'));

        $result = static::join('course_categories', 'course_categories.id','=','study_material_topics.course_category_id')
            ->join('course_sub_categories', 'course_sub_categories.id','=','study_material_topics.course_sub_category_id')
            ->join('study_material_subjects', 'study_material_subjects.id','=','study_material_topics.study_material_subject_id')
            ->where('study_material_topics.course_category_id', $categoryId)
            ->where('study_material_topics.course_sub_category_id', $subcategoryId)
            ->where('study_material_topics.study_material_subject_id', $subjectId)
            ->where('study_material_topics.name', $topicName);
        if(!empty($topicId)){
            $result->where('study_material_topics.id', '!=', $topicId);
        }

        $result->first();
        if(is_object($result) && 1 == $result->count()){
            return 'true';
        } else {
            return 'false';
        }
        return 'false';
    }

    /**
     *  get category
     */
    public function category(){
        return $this->belongsTo(CourseCategory::class, 'course_category_id');
    }

    /**
     *  get sub category
     */
    public function subcategory(){
        return $this->belongsTo(CourseSubCategory::class, 'course_sub_category_id');
    }

    /**
     *  get subject
     */
    public function subject(){
        return $this->belongsTo(StudyMaterialSubject::class, 'study_material_subject_id');
    }

    protected static function getCategoriesAndSubcategoriesAssocaitedWithStudyMaterialTopics(){
        return static::join('course_categories', 'course_categories.id','=','study_material_topics.course_category_id')
            ->join('course_sub_categories', 'course_sub_categories.id','=','study_material_topics.course_sub_category_id')
            ->join('study_material_subjects', 'study_material_subjects.id','=','study_material_topics.study_material_subject_id')
            ->select('study_material_topics.course_category_id','study_material_topics.course_sub_category_id','course_categories.name as category','course_sub_categories.name as subcategory','study_material_topics.study_material_subject_id','study_material_subjects.name as subject','study_material_topics.id')
            ->groupBy('study_material_topics.course_category_id','study_material_topics.course_sub_category_id')
            ->get();
    }
    protected static function getStudymMaterialTopicsBySubCategoryId($subCategoryId){
        return static::join('course_categories', 'course_categories.id','=','study_material_topics.course_category_id')
            ->join('course_sub_categories', 'course_sub_categories.id','=','study_material_topics.course_sub_category_id')
            ->join('study_material_subjects', 'study_material_subjects.id','=','study_material_topics.study_material_subject_id')
            ->select('study_material_topics.course_category_id','study_material_topics.course_sub_category_id','course_categories.name as category','course_sub_categories.name as subcategory','study_material_topics.study_material_subject_id','study_material_subjects.name as subject','study_material_topics.id','study_material_topics.name','study_material_topics.content')
            ->where('study_material_topics.course_sub_category_id',$subCategoryId)
            ->get();
    }
}
