<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use App\Models\CourseSubCategory;
use App\Models\CourseCategory;
use App\Models\StudyMaterialSubject;
use App\Models\StudyMaterialTopic;
use App\Models\StudyMaterialComment;
use DB,Auth;

class StudyMaterialPost extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['course_category_id', 'course_sub_category_id','study_material_subject_id','study_material_topic_id','body','answer1','answer2','answer3','answer4','answer','solution'];

    /**
     *  add/update post
     */
    protected static function addOrUpdateStudyMaterialPost( Request $request, $isUpdate=false){
        $categoryId = InputSanitise::inputInt($request->get('category'));
        $subcategoryId = InputSanitise::inputInt($request->get('subcategory'));
        $subjectId = InputSanitise::inputInt($request->get('subject'));
        $topicId = InputSanitise::inputInt($request->get('topic'));
        $postId = InputSanitise::inputInt($request->get('post_id'));
        $body = $request->get('body');
        $answer1 = $request->get('answer1');
        $answer2 = $request->get('answer2');
        $answer3 = $request->get('answer3');
        $answer4 = $request->get('answer4');
        $answer = $request->get('answer');
        $solution = $request->get('solution');

        if( $isUpdate && isset($postId)){
            $post = static::find($postId);
            if(!is_object($post)){
                return 'false';
            }
        } else {
            $post = new static;
        }

        $post->course_category_id = $categoryId;
        $post->course_sub_category_id = $subcategoryId;
        $post->study_material_subject_id = $subjectId;
        $post->study_material_topic_id = $topicId;
        $post->body = $body;
        $post->answer1 = $answer1;
        $post->answer2 = $answer2;
        $post->answer3 = $answer3;
        $post->answer4 = $answer4;
        $post->answer = $answer;
        $post->solution = $solution;
        $post->save();
        return $post;
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

    /**
     *  get topic
     */
    public function topic(){
        return $this->belongsTo(StudyMaterialTopic::class, 'study_material_topic_id');
    }

    /**
     *  comments of post
     */
    public function descComments()
    {
        return $this->hasMany(StudyMaterialComment::class, 'study_material_post_id')->orderBy('id','desc');
    }

    protected static function getStudyMaterialPostsWithPagination(){
        if(Auth::guard('admin')->user()->hasRole('sub-admin')){
            return static::join('course_categories', 'course_categories.id','=','study_material_posts.course_category_id')
	            ->join('course_sub_categories', 'course_sub_categories.id','=','study_material_posts.course_sub_category_id')
	            ->join('study_material_subjects', 'study_material_subjects.id','=','study_material_posts.study_material_subject_id')
            	->join('study_material_topics', 'study_material_topics.id','=','study_material_posts.study_material_topic_id')
                ->where('study_material_subjects.admin_id',Auth::guard('admin')->user()->id)
                ->select('study_material_posts.*','study_material_subjects.admin_id','course_categories.name as category','course_sub_categories.name as subcategory','study_material_subjects.name as subject','study_material_topics.name as topic')
                ->groupBy('study_material_posts.id')
                ->paginate();
        } else {
            return static::join('course_categories', 'course_categories.id','=','study_material_posts.course_category_id')
	            ->join('course_sub_categories', 'course_sub_categories.id','=','study_material_posts.course_sub_category_id')
	            ->join('study_material_subjects', 'study_material_subjects.id','=','study_material_posts.study_material_subject_id')
            	->join('study_material_topics', 'study_material_topics.id','=','study_material_posts.study_material_topic_id')
                ->select('study_material_posts.*','study_material_subjects.admin_id','course_categories.name as category','course_sub_categories.name as subcategory','study_material_subjects.name as subject','study_material_topics.name as topic')
                ->groupBy('study_material_posts.id')
                ->paginate();
        }
    }

    protected static function getPostsByTopicId($topicId){
        return static::where('study_material_topic_id',$topicId)->get();
    }

    protected static function deletePostsByTopicId($topicId){
        $posts = static::where('study_material_topic_id',$topicId)->get();
        if(is_object($posts) && false == $posts->isEmpty()){
            foreach($posts as $post){
                $post->delete();
            }
        }
    }
}
