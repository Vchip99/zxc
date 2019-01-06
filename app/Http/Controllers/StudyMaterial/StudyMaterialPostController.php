<?php

namespace App\Http\Controllers\StudyMaterial;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CourseSubCategory;
use App\Models\CourseCategory;
use App\Models\StudyMaterialSubject;
use App\Models\StudyMaterialTopic;
use App\Models\StudyMaterialPost;
use App\Models\StudyMaterialPostLike;
use App\Models\StudyMaterialComment;
use App\Models\StudyMaterialCommentLike;
use App\Models\StudyMaterialSubComment;
use App\Models\StudyMaterialSubCommentLike;
use App\Models\Admin;
use Redirect,Validator, Auth, DB,Session;
use App\Libraries\InputSanitise;

class StudyMaterialPostController extends Controller
{
	/**
     * check admin have permission or not, if not redirect to admin/home
     */
	public function __construct() {
        $this->middleware(function ($request, $next) {
            $adminUser = Auth::guard('admin')->user();
            if(is_object($adminUser)){
                if($adminUser->hasRole('admin') || $adminUser->hasRole('sub-admin')){
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
    protected $validateCreatePost = [
        'category' => 'required|integer',
        'subcategory' => 'required|integer',
        'subject' => 'required|integer',
        'topic' => 'required|integer',
        'body' => 'required|string',
        'answer1' => 'required|string',
        'answer2' => 'required|string',
        'answer' => 'required|string',
        'solution' => 'required|string',
    ];

    /**
     *	show all posts
     */
	public function show(){
		$adminNames = [];
		$courseCategories = CourseCategory::getCourseCategoriesForAdmin();
		if(Session::has('selected_post_category')){
			$courseSubCategories = CourseSubCategory::getCourseSubCategoriesByCategoryId(Session::get('selected_post_category'));
		} else {
			$courseSubCategories = [];
		}
		if(Session::has('selected_post_category') && Session::has('selected_post_subcategory')){
			$subjects = StudyMaterialSubject::getStudyMaterialSubjectsByCategoryIdBySubCategoryIdForList(Session::get('selected_post_category'),Session::get('selected_post_subcategory'));
		} else {
			$subjects = [];
		}
		if(Session::has('selected_post_category') && Session::has('selected_post_subcategory') && Session::has('selected_post_subject')){
			$topics = StudyMaterialTopic::getStudyMaterialTopicsByCategoryIdBySubCategoryIdBySubjectId(Session::get('selected_post_category'),Session::get('selected_post_subcategory'),Session::get('selected_post_subject'));
		} else {
			$topics = [];
		}
		if(Session::has('selected_post_category') && Session::has('selected_post_subcategory') && Session::has('selected_post_subject') && Session::has('selected_post_topic')){
			$posts = StudyMaterialPost::getPostsByCategoryIdBySubcategoryIdBySubjectIdByTopicId(Session::get('selected_post_category'),Session::get('selected_post_subcategory'),Session::get('selected_post_subject'),Session::get('selected_post_topic'));
			$admins = Admin::all();
			if(is_object($admins) && false == $admins->isEmpty()){
				foreach($admins as $admin){
					$adminNames[$admin->id] = $admin->name;
				}
			}
		} else {
			$posts = [];
		}
		return view('studyMaterialPost.list', compact('courseCategories','courseSubCategories','subjects','topics','posts','adminNames'));
	}

	/**
     *	show all posts
     */
	public function showPosts(Request $request){
		$categoryId = InputSanitise::inputInt($request->get('category'));
        $subcategoryId = InputSanitise::inputInt($request->get('subcategory'));
    	$subjectId = InputSanitise::inputInt($request->get('subject'));
    	$topicId = InputSanitise::inputInt($request->get('topic'));
    	if(isset($categoryId) && isset($subcategoryId) && isset($subjectId) && isset($topicId)){
			$courseCategories = CourseCategory::getCourseCategoriesForAdmin();
			$courseSubCategories = CourseSubCategory::getCourseSubCategoriesByCategoryId($categoryId);
			$subjects = StudyMaterialSubject::getStudyMaterialSubjectsByCategoryIdBySubCategoryIdForList($categoryId,$subcategoryId);
			$topics = StudyMaterialTopic::getStudyMaterialTopicsByCategoryIdBySubCategoryIdBySubjectId($categoryId,$subcategoryId,$subjectId);

			$posts = StudyMaterialPost::getPostsByCategoryIdBySubcategoryIdBySubjectIdByTopicId($categoryId,$subcategoryId,$subjectId,$topicId);
			$adminNames = [];
			$admins = Admin::all();
			if(is_object($admins) && false == $admins->isEmpty()){
				foreach($admins as $admin){
					$adminNames[$admin->id] = $admin->name;
				}
			}
			Session::put('selected_post_category', $categoryId);
            Session::put('selected_post_subcategory', $subcategoryId);
            Session::put('selected_post_subject', $subjectId);
            Session::put('selected_post_topic', $topicId);
			return view('studyMaterialPost.list', compact('courseCategories','courseSubCategories','subjects','topics','posts','adminNames'));
		}
		return Redirect::to('admin/manageStudyMaterialPost');
	}

	/**
	 *	show create UI for post
	 */
	protected function create(){
		$courseCategories = CourseCategory::getCourseCategoriesForAdmin();
		if(Session::has('selected_post_category')){
			$courseSubCategories = CourseSubCategory::getCourseSubCategoriesByCategoryId(Session::get('selected_post_category'));
		} else {
			$courseSubCategories = [];
		}
		if(Session::has('selected_post_category') && Session::has('selected_post_subcategory')){
			$subjects = StudyMaterialSubject::getStudyMaterialSubjectsByCategoryIdBySubCategoryIdForList(Session::get('selected_post_category'),Session::get('selected_post_subcategory'));
		} else {
			$subjects = [];
		}
		if(Session::has('selected_post_category') && Session::has('selected_post_subcategory') && Session::has('selected_post_subject')){
			$topics = StudyMaterialTopic::getStudyMaterialTopicsByCategoryIdBySubCategoryIdBySubjectId(Session::get('selected_post_category'),Session::get('selected_post_subcategory'),Session::get('selected_post_subject'));
		} else {
			$topics = [];
		}
		$post = new StudyMaterialPost;
		$prevPostId = Session::get('selected_prev_post');
        $nextPostId = 'new';
		return view('studyMaterialPost.create', compact('courseCategories','courseSubCategories','subjects','topics','post','prevPostId','nextPostId'));
	}

	/**
	 *	store post
	 */
	protected function store(Request $request){

		$v = Validator::make($request->all(), $this->validateCreatePost);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        InputSanitise::deleteCacheByString('vchip:studyMaterial');
        DB::beginTransaction();
        try
        {
	        $post = StudyMaterialPost::addOrUpdateStudyMaterialPost($request);
	        if(is_object($post)){
	        	Session::put('selected_post_category', $post->course_category_id);
                Session::put('selected_post_subcategory', $post->course_sub_category_id);
                Session::put('selected_post_subject', $post->study_material_subject_id);
                Session::put('selected_post_topic', $post->study_material_topic_id);
	        	Session::put('selected_prev_post', $post->id);
	        	DB::commit();
	            return Redirect::to('admin/createStudyMaterialPost')->with('message', 'Post created successfully!');
	        }
	    }
        catch(\Exception $e)
        {
            DB::rollback();
            return back()->withErrors('something went wrong.');
        }
        return Redirect::to('admin/manageStudyMaterialPost');
	}

	/**
	 *	edit post
	 */
	protected function edit($id){
		$id = InputSanitise::inputInt(json_decode($id));
		if(isset($id)){
			$post = StudyMaterialPost::find($id);
			if(is_object($post)){
				$courseCategories = CourseCategory::getCourseCategoriesForAdmin();
				$courseSubCategories = CourseSubCategory::getCourseSubCategoriesByCategoryId($post->course_category_id);
				$subjects = StudyMaterialSubject::getStudyMaterialSubjectsByCategoryIdBySubCategoryIdForList($post->course_category_id,$post->course_sub_category_id);
				$topics = StudyMaterialTopic::getStudyMaterialTopicsByCategoryIdBySubCategoryIdBySubjectId($post->course_category_id,$post->course_sub_category_id,$post->study_material_subject_id);
				$postSubject = $post->subject;
				Session::put('selected_post_category', $post->course_category_id);
                Session::put('selected_post_subcategory', $post->course_sub_category_id);
                Session::put('selected_post_subject', $post->study_material_subject_id);
                Session::put('selected_post_topic', $post->study_material_topic_id);
				Session::put('selected_prev_post', $post->id);
				$prevPostId = $this->getPrevPostIdWithPostId($post->course_category_id,$post->course_sub_category_id,$post->study_material_subject_id,$post->study_material_topic_id,$post->id);
                $nextPostId = $this->getNextPostIdWithPostId($post->course_category_id,$post->course_sub_category_id,$post->study_material_subject_id,$post->study_material_topic_id,$post->id);
				return view('studyMaterialPost.create', compact('courseCategories','courseSubCategories','subjects','topics','post','postSubject','prevPostId','nextPostId'));
			}
		}
		return Redirect::to('admin/manageStudyMaterialPost');
	}

	/**
	 *	update post
	 */
	protected function update(Request $request){
		$v = Validator::make($request->all(), $this->validateCreatePost);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        InputSanitise::deleteCacheByString('vchip:studyMaterial');
		$postId = InputSanitise::inputInt($request->get('post_id'));
		if(isset($postId)){
			DB::beginTransaction();
	        try
	        {
				$post = StudyMaterialPost::addOrUpdateStudyMaterialPost($request, true);
		        if(is_object($post)){
		        	DB::commit();
		        	Session::put('selected_post_category', $post->course_category_id);
	                Session::put('selected_post_subcategory', $post->course_sub_category_id);
	                Session::put('selected_post_subject', $post->study_material_subject_id);
	                Session::put('selected_post_topic', $post->study_material_topic_id);
					Session::put('selected_prev_post', $post->id);
		            return Redirect::to("admin/studyMaterialPost/$post->id/edit")->with('message', 'Post updated successfully!');
		        }
		    }
	        catch(\Exception $e)
	        {
	            DB::rollback();
	            return back()->withErrors('something went wrong.');
	        }
		}
		return Redirect::to('admin/manageStudyMaterialPost');
	}

	/**
	 *	delete post
	 */
	protected function delete(Request $request){
		$postId = InputSanitise::inputInt($request->get('post_id'));
		InputSanitise::deleteCacheByString('vchip:studyMaterial');
		if(isset($postId)){
			$post = StudyMaterialPost::find($postId);
			if(is_object($post)){
				DB::beginTransaction();
		        try
		        {
					StudyMaterialPostLike::deleteLikesByPostId($post->id);
					StudyMaterialComment::deleteCommentsByPostId($post->id);
					StudyMaterialCommentLike::deleteLikesByPostId($post->id);
					StudyMaterialSubComment::deleteSubCommentsByPostId($post->id);
					StudyMaterialSubCommentLike::deleteLikesByPostId($post->id);

					$post->delete();
					DB::commit();
					return Redirect::to('admin/manageStudyMaterialPost')->with('message', 'Post deleted successfully!');
				}
		        catch(\Exception $e)
		        {
		            DB::rollback();
		            return back()->withErrors('something went wrong.');
		        }
			}
		}
		return Redirect::to('admin/manageStudyMaterialPost');
	}

	protected function getPrevPostIdWithPostId($categoryId,$subcategoryId,$subjectId,$TopicId,$postId){
         $post = StudyMaterialPost::getPrevPostByCategoryIdBySubcategoryIdBySubjectIdByTopicId($categoryId,$subcategoryId,$subjectId,$TopicId,$postId);
        if(is_object($post)){
            return $post->id;
        }
        return;
    }

    protected function getNextPostIdWithPostId($categoryId,$subcategoryId,$subjectId,$TopicId,$postId){
         $post = StudyMaterialPost::getNextPostByCategoryIdBySubcategoryIdBySubjectIdByTopicId($categoryId,$subcategoryId,$subjectId,$TopicId,$postId);
        if(is_object($post)){
            return $post->id;
        }
        return;
    }

}
