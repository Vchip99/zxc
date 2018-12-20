<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use App\Models\CourseSubCategory;
use App\Models\CourseCategory;
use App\Models\StudyMaterialSubject;
use App\Models\StudyMaterialPost;
use App\Models\StudyMaterialPostLike;
use App\Models\StudyMaterialComment;
use App\Models\StudyMaterialCommentLike;
use App\Models\StudyMaterialSubComment;
use App\Models\StudyMaterialSubCommentLike;
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
        if(is_object(Auth::user()) && 'ceo@vchiptech.com' == Auth::user()->email){
            return static::join('course_categories', 'course_categories.id','=','study_material_topics.course_category_id')
                ->join('course_sub_categories', 'course_sub_categories.id','=','study_material_topics.course_sub_category_id')
                ->join('study_material_subjects', 'study_material_subjects.id','=','study_material_topics.study_material_subject_id')
                ->select('study_material_topics.course_category_id','study_material_topics.course_sub_category_id','course_categories.name as category','course_sub_categories.name as subcategory','study_material_topics.study_material_subject_id','study_material_subjects.name as subject','study_material_topics.id')
                ->groupBy('study_material_topics.course_category_id','study_material_topics.course_sub_category_id')
                ->get();
        } else {
            return static::join('course_categories', 'course_categories.id','=','study_material_topics.course_category_id')
                ->join('course_sub_categories', 'course_sub_categories.id','=','study_material_topics.course_sub_category_id')
                ->join('study_material_subjects', 'study_material_subjects.id','=','study_material_topics.study_material_subject_id')
                ->where('study_material_subjects.admin_approve', 1)
                ->select('study_material_topics.course_category_id','study_material_topics.course_sub_category_id','course_categories.name as category','course_sub_categories.name as subcategory','study_material_topics.study_material_subject_id','study_material_subjects.name as subject','study_material_topics.id')
                ->groupBy('study_material_topics.course_category_id','study_material_topics.course_sub_category_id')
                ->get();
        }
    }
    protected static function getStudymMaterialTopicsBySubCategoryId($subCategoryId){
        if(is_object(Auth::user()) && 'ceo@vchiptech.com' == Auth::user()->email){
            return static::join('course_categories', 'course_categories.id','=','study_material_topics.course_category_id')
                ->join('course_sub_categories', 'course_sub_categories.id','=','study_material_topics.course_sub_category_id')
                ->join('study_material_subjects', 'study_material_subjects.id','=','study_material_topics.study_material_subject_id')
                ->where('study_material_topics.course_sub_category_id',$subCategoryId)
                ->select('study_material_topics.course_category_id','study_material_topics.course_sub_category_id','course_categories.name as category','course_sub_categories.name as subcategory','study_material_topics.study_material_subject_id','study_material_subjects.name as subject','study_material_topics.id','study_material_topics.name','study_material_topics.content','study_material_subjects.admin_id')
                ->get();
        } else {
            return static::join('course_categories', 'course_categories.id','=','study_material_topics.course_category_id')
                ->join('course_sub_categories', 'course_sub_categories.id','=','study_material_topics.course_sub_category_id')
                ->join('study_material_subjects', 'study_material_subjects.id','=','study_material_topics.study_material_subject_id')
                ->where('study_material_subjects.admin_approve', 1)
                ->where('study_material_topics.course_sub_category_id',$subCategoryId)
                ->select('study_material_topics.course_category_id','study_material_topics.course_sub_category_id','course_categories.name as category','course_sub_categories.name as subcategory','study_material_topics.study_material_subject_id','study_material_subjects.name as subject','study_material_topics.id','study_material_topics.name','study_material_topics.content','study_material_subjects.admin_id')
                ->get();
        }
    }

    protected static function deleteStudyMaterialTopicsBySubjectId($subjectId){
        $topics =  static::where('study_material_subject_id', $subjectId)->get();
        if(is_object($topics) && false == $topics->isEmpty()){
            foreach($topics as $topic){
                StudyMaterialPost::deletePostsByTopicId($topic->id);
                StudyMaterialPostLike::deleteLikesByTopicId($topic->id);
                StudyMaterialComment::deleteCommentsByTopicId($topic->id);
                StudyMaterialCommentLike::deleteLikesByTopicId($topic->id);
                StudyMaterialSubComment::deleteSubCommentsByTopicId($topic->id);
                StudyMaterialSubCommentLike::deleteLikesByTopicId($topic->id);
                $topic->delete();
            }
        }
        return;
    }

    protected static function deleteStudyMaterialTopicsBySubCategoryId($subCategoryId){
        $topics =  static::where('course_sub_category_id', $subCategoryId)->get();
        if(is_object($topics) && false == $topics->isEmpty()){
            foreach($topics as $topic){
                StudyMaterialPost::deletePostsByTopicId($topic->id);
                StudyMaterialPostLike::deleteLikesByTopicId($topic->id);
                StudyMaterialComment::deleteCommentsByTopicId($topic->id);
                StudyMaterialCommentLike::deleteLikesByTopicId($topic->id);
                StudyMaterialSubComment::deleteSubCommentsByTopicId($topic->id);
                StudyMaterialSubCommentLike::deleteLikesByTopicId($topic->id);
                $topic->delete();
            }
        }
        return;
    }

    protected static function deleteStudyMaterialTopicsByCategoryId($categoryId){
        $topics =  static::where('course_category_id', $categoryId)->get();
        if(is_object($topics) && false == $topics->isEmpty()){
            foreach($topics as $topic){
                StudyMaterialPost::deletePostsByTopicId($topic->id);
                StudyMaterialPostLike::deleteLikesByTopicId($topic->id);
                StudyMaterialComment::deleteCommentsByTopicId($topic->id);
                StudyMaterialCommentLike::deleteLikesByTopicId($topic->id);
                StudyMaterialSubComment::deleteSubCommentsByTopicId($topic->id);
                StudyMaterialSubCommentLike::deleteLikesByTopicId($topic->id);
                $topic->delete();
            }
        }
        return;
    }

    protected static function getStudyMaterialTopicsWithPagination(){
        $result = static::join('study_material_subjects', 'study_material_subjects.id','=','study_material_topics.study_material_subject_id')
            ->join('admins','admins.id','=','study_material_subjects.admin_id');
        if(Auth::guard('admin')->user()->hasRole('sub-admin')){
            $result->where('study_material_subjects.admin_id',Auth::guard('admin')->user()->id);
        }
        return $result->select('study_material_topics.*','study_material_subjects.admin_id','admins.name as admin')
                ->groupBy('study_material_topics.id')
                ->paginate();
    }

    protected static function getStudyMaterialTopicsByCategoryIdBySubCategoryIdBySubjectId($categoryId,$subcategoryId,$subjectId){
        return static::where('study_material_topics.course_category_id',$categoryId)->where('study_material_topics.course_sub_category_id',$subcategoryId)->where('study_material_topics.study_material_subject_id',$subjectId)
            ->select('study_material_topics.id','study_material_topics.name')
            ->get();
    }
}
