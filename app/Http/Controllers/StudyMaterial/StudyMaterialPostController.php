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
use Redirect;
use Validator, Auth, DB;
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
		$posts = StudyMaterialPost::getStudyMaterialPostsWithPagination();
		$adminNames = [];
		$admins = Admin::all();
		if(is_object($admins) && false == $admins->isEmpty()){
			foreach($admins as $admin){
				$adminNames[$admin->id] = $admin->name;
			}
		}
		return view('studyMaterialPost.list', compact('posts','adminNames'));
	}

	/**
	 *	show create UI for post
	 */
	protected function create(){
		$courseCategories = CourseCategory::getCourseCategoriesForAdmin();
		$courseSubCategories = [];
		$subjects = [];
		$topics = [];
		$post = new StudyMaterialPost;
		return view('studyMaterialPost.create', compact('courseCategories','courseSubCategories','subjects','topics','post'));
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
        DB::beginTransaction();
        try
        {
	        $topic = StudyMaterialPost::addOrUpdateStudyMaterialPost($request);
	        if(is_object($topic)){
	        	DB::commit();
	            return Redirect::to('admin/manageStudyMaterialPost')->with('message', 'Post created successfully!');
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
				return view('studyMaterialPost.create', compact('courseCategories','courseSubCategories','subjects','topics','post','postSubject'));
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
		$postId = InputSanitise::inputInt($request->get('post_id'));
		if(isset($postId)){
			DB::beginTransaction();
	        try
	        {
				$post = StudyMaterialPost::addOrUpdateStudyMaterialPost($request, true);
		        if(is_object($post)){
		        	DB::commit();
		            return Redirect::to('admin/manageStudyMaterialPost')->with('message', 'Post updated successfully!');
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

}
