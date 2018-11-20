<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Client\ClientBaseController;
use Redirect;
use Validator, Session, Auth, DB,LRedis;
use App\Libraries\InputSanitise;
use App\Models\ClientDiscussionCategory;
use App\Models\ClientDiscussionPost;
use App\Models\ClientDiscussionComment;
use App\Models\ClientDiscussionSubComment;
use App\Models\ClientDiscussionLike;
use App\Models\Client;

class ClientDiscussionCategoryController extends ClientBaseController
{
    /**
     *  check admin have permission or not, if not redirect to home
     */
	public function __construct(Request $request) {
        parent::__construct($request);
        // $this->middleware('client');
    }

	/**
     * Define your validation rules in a property in
     * the controller to reuse the rules.
     */
    protected $validateCategory = [
        'category' => 'required|string'
    ];

    /**
     *  show list of category
     */
    protected function show($subdomainName,Request $request){
        if(false == InputSanitise::checkDomain($request)){
            return Redirect::to('/');
        }
    	$categories = ClientDiscussionCategory::getCategoriesByClient();
    	return view('client.discussion.category.list', compact('categories','subdomainName'));
    }

    /**
     *  show create category UI
     */
    protected function create($subdomainName,Request $request){
        if(false == InputSanitise::checkDomain($request)){
            return Redirect::to('/');
        }
		$category = new ClientDiscussionCategory;
		return view('client.discussion.category.create', compact('category', 'subdomainName'));
    }

    /**
     *  store category
     */
    protected function store($subdomainName,Request $request){
        $v = Validator::make($request->all(), $this->validateCategory);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $category = ClientDiscussionCategory::addOrUpdateDiscussionCategory($request);
            if(is_object($category)){
                DB::connection('mysql2')->commit();
                return Redirect::to('manageDiscussionCategory')->with('message', 'category created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('manageDiscussionCategory');
    }

    /**
     *  edit category
     */
    protected function edit($subdomainName, $id,Request $request){
        if(false == InputSanitise::checkDomain($request)){
            return Redirect::to('/');
        }
        $id = InputSanitise::inputInt(json_decode($id));
    	if(isset($id)){
    		$category = ClientDiscussionCategory::find($id);
    		if(is_object($category)){
    			return view('client.discussion.category.create', compact('category', 'subdomainName'));
    		}
    	}
    	return Redirect::to('manageDiscussionCategory');
    }

    /**
     *  update category
     */
    protected function update($subdomainName,Request $request){
        $v = Validator::make($request->all(), $this->validateCategory);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
    	$categoryId = InputSanitise::inputInt($request->get('category_id'));
    	if(isset($categoryId)){
            DB::connection('mysql2')->beginTransaction();
            try
            {
                $category = ClientDiscussionCategory::addOrUpdateDiscussionCategory($request, true);
                if(is_object($category)){
                    DB::connection('mysql2')->commit();
                    return Redirect::to('manageDiscussionCategory')->with('message', 'category updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
                return redirect()->back()->withErrors('something went wrong.');
            }
    	}
    	return Redirect::to('manageDiscussionCategory');
    }

    /**
     *  delete category
     */
    protected function delete($subdomainName,Request $request){
    	$categoryId = InputSanitise::inputInt($request->get('category_id'));
        $client = Auth::guard('client')->user();
    	if(isset($categoryId)){
            DB::connection('mysql2')->beginTransaction();
            try
            {
        		$courseCategory = ClientDiscussionCategory::find($categoryId);
        		if(is_object($courseCategory)){
                    $posts = ClientDiscussionPost::where('client_discussion_category_id',$categoryId)->where('client_id',$client->id)->get();
                    if(is_object($posts) && false == $posts->isEmpty()){
                        foreach($posts as $post){
                            if(is_object($post->comments) && false == $post->comments->isEmpty()){
                                foreach($post->comments as $comment){
                                    if(is_object($comment->children) && false == $comment->children->isEmpty()){
                                        foreach($comment->children as $subcomment){
                                            $subcomment->delete();
                                        }
                                    }
                                    $comment->delete();
                                }
                            }
                            $likes = ClientDiscussionLike::where('client_discussion_post_id',$post->id)->where('client_id',$client->id)->get();
                            if(is_object($likes) && false == $likes->isEmpty()){
                                foreach($likes as $like){
                                    $like->delete();
                                }
                            }
                            $post->delete();
                        }
                    }
                }
                $courseCategory->delete();
                DB::connection('mysql2')->commit();
                return Redirect::to('manageDiscussionCategory')->with('message', 'category deleted successfully!');
            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
                return redirect()->back()->withErrors('something went wrong.');
            }
    	}
    	return Redirect::to('manageDiscussionCategory');
    }

    protected function isClientDiscussionCategoryExist(Request $request){
        return ClientDiscussionCategory::isClientDiscussionCategoryExist($request);
    }

    protected function manageDiscussion($subdomainName,Request $request){
        if(false == InputSanitise::checkDomain($request)){
            return Redirect::to('/');
        }
        $discussionCategories = ClientDiscussionCategory::getCategoriesByClient();
        $posts = ClientDiscussionPost::getPostsByClient();
        $currentUser = Auth::guard('client')->user();
        $isClient = 1;
        $likesCount = ClientDiscussionLike::getPostLikes();
        $commentLikesCount = ClientDiscussionLike::getCommentLikes();
        $subcommentLikesCount = ClientDiscussionLike::getSubCommentLikes();
        return view('client.discussion.discussion', compact('subdomainName','posts','discussionCategories','currentUser','isClient','likesCount','commentLikesCount','subcommentLikesCount'));
    }

    /**
     *  create discussin post
     */
    protected function createPost($subdomainName,Request $request){
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $post = ClientDiscussionPost::createPost($request);
            if(is_object($post)){
                DB::connection('mysql2')->commit();
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return back()->withErrors('something went wrong.');
        }
        return $this->getPosts();
    }

    /**
     *  return posts
     */
    protected function getPosts(){
        $allPosts = [];
        if(Auth::guard('client')->user()){
            $clientId = Auth::guard('client')->user()->id;
        } else {
            $clientId = Auth::guard('clientuser')->user()->client_id;
        }
        $posts = ClientDiscussionPost::where('client_id', $clientId)->orderBy('id', 'desc')->get();
        if(is_object($posts) && false == $posts->isEmpty()){
            foreach ($posts as $post) {
                $allPosts['posts'][$post->id]['title'] = $post->title;
                $allPosts['posts'][$post->id]['body'] = $post->body;
                $allPosts['posts'][$post->id]['id'] = $post->id;
                $allPosts['posts'][$post->id]['answer1'] = $post->answer1;
                $allPosts['posts'][$post->id]['answer2'] = $post->answer2;
                $allPosts['posts'][$post->id]['answer3'] = $post->answer3;
                $allPosts['posts'][$post->id]['answer4'] = $post->answer4;
                $allPosts['posts'][$post->id]['answer'] = $post->answer;
                $allPosts['posts'][$post->id]['solution'] = $post->solution;
                $allPosts['posts'][$post->id]['updated_at'] = $post->updated_at->diffForHumans();
                $allPosts['posts'][$post->id]['client_id'] = $post->client_id;
                if($post->clientuser_id > 0){
                    $allPosts['posts'][$post->id]['is_user'] = true;
                    $allPosts['posts'][$post->id]['user_id'] = $post->clientuser_id;
                    $allPosts['posts'][$post->id]['user_name'] = $post->getUser($post->clientuser_id)->name;
                    $allPosts['posts'][$post->id]['user_image'] = $post->getUser($post->clientuser_id)->photo;
                    if(is_file($post->getUser($post->clientuser_id)->photo) && true == preg_match('/clientUserStorage/',$post->getUser($post->clientuser_id)->photo)){
                        $isImageExist = 'system';
                    } else if(!empty($post->getUser($post->clientuser_id)->photo) && false == preg_match('/clientUserStorage/',$post->getUser($post->clientuser_id)->photo)){
                        $isImageExist = 'other';
                    } else {
                        $isImageExist = 'false';
                    }
                } else {
                    $allPosts['posts'][$post->id]['is_user'] = false;
                    $allPosts['posts'][$post->id]['user_id'] = $post->client_id;
                    $allPosts['posts'][$post->id]['user_name'] = $post->getClient($post->client_id)->name;
                    $allPosts['posts'][$post->id]['user_image'] = $post->getClient($post->client_id)->photo;
                    if(is_file($post->getClient($post->client_id)->photo) && true == preg_match('/client_images/',$post->getClient($post->client_id)->photo)){
                        $isImageExist = 'system';
                    } else if(!empty($post->getClient($post->client_id)->photo) && false == preg_match('/client_images/',$post->getClient($post->client_id)->photo)){
                        $isImageExist = 'other';
                    } else {
                        $isImageExist = 'false';
                    }
                }
                $allPosts['posts'][$post->id]['image_exist'] = $isImageExist;

                if($post->descComments){
                    $allPosts['posts'][$post->id]['comments'] = $this->getComments($post->descComments,$post->title);
                }
            }
        }
        $allPosts['likesCount'] = ClientDiscussionLike::getPostLikes();
        $allPosts['commentLikesCount'] = ClientDiscussionLike::getCommentLikes();
        $allPosts['subcommentLikesCount'] = ClientDiscussionLike::getSubCommentLikes();
        return $allPosts;
    }

    protected function updatePost(Request $request){
        $postId = $request->get('post_id');
        $question = $request->get('update_question');
        if(!empty($postId) && !empty($question)){
            $post = ClientDiscussionPost::find($postId);
            if(is_object($post)){
                DB::connection('mysql2')->beginTransaction();
                try
                {
                    $post->body = $question;
                    $post->save();
                    DB::connection('mysql2')->commit();
                }
                catch(\Exception $e)
                {
                    DB::connection('mysql2')->rollback();
                }
            }
        }
        return $this->getPosts();
    }

    protected function deletePost(Request $request){
        $post = ClientDiscussionPost::find(json_decode($request->get('post_id')));
        if(is_object($post)){
            DB::connection('mysql2')->beginTransaction();
            try
            {
                $post->deleteCommantsAndSubComments();
                $post->delete();
                DB::connection('mysql2')->commit();
            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
            }
        }
        return $this->getPosts();
    }

    /**
     *  create discussin post comment
     */
    protected function createComment(Request $request){
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $comment = ClientDiscussionComment::createComment($request);
            DB::connection('mysql2')->commit();
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
        }
        return $this->getPosts();
    }

    /**
     *  create discussin post child comment
     */
    protected function createSubComment(Request $request){
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $subcomment = ClientDiscussionSubComment::createSubComment($request);
            if(is_object($subcomment)){
                DB::connection('mysql2')->commit();
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
        }
        return $this->getPosts();
    }

    /**
     *  return post comments
     */
    protected function getComments($comments, $title){
        $postComments = [];
        $commentComments = [];
        foreach($comments as $comment){
            $postComments[$comment->id]['body'] = $comment->body;
            $postComments[$comment->id]['id'] = $comment->id;
            $postComments[$comment->id]['discussion_post_id'] = $comment->client_discussion_post_id;
            $postComments[$comment->id]['updated_at'] = $comment->updated_at->diffForHumans();
            $postComments[$comment->id]['title'] = $title;
            $postComments[$comment->id]['client_id'] = $comment->client_id;
            if($comment->clientuser_id > 0){
                $postComments[$comment->id]['is_user'] = true;
                $postComments[$comment->id]['user_id'] = $comment->clientuser_id;
                $postComments[$comment->id]['user_name'] = $comment->getUser($comment->clientuser_id)->name;
                $postComments[$comment->id]['user_image'] = $comment->getUser($comment->clientuser_id)->photo;
                if(is_file($comment->getUser($comment->clientuser_id)->photo) && true == preg_match('/clientUserStorage/',$comment->getUser($comment->clientuser_id)->photo)){
                    $isImageExist = 'system';
                } else if(!empty($comment->getUser($comment->clientuser_id)->photo) && false == preg_match('/clientUserStorage/',$comment->getUser($comment->clientuser_id)->photo)){
                    $isImageExist = 'other';
                } else {
                    $isImageExist = 'false';
                }
            } else {
                $postComments[$comment->id]['is_user'] = false;
                $postComments[$comment->id]['user_id'] = $comment->client_id;
                $postComments[$comment->id]['user_name'] = $comment->getClient($comment->client_id)->name;
                $postComments[$comment->id]['user_image'] = $comment->getClient($comment->client_id)->photo;
                if(is_file($comment->getClient($comment->client_id)->photo) && true == preg_match('/client_images/',$comment->getClient($comment->client_id)->photo)){
                    $isImageExist = 'system';
                } else if(!empty($comment->getClient($comment->client_id)->photo) && false == preg_match('/client_images/',$comment->getClient($comment->client_id)->photo)){
                    $isImageExist = 'other';
                } else {
                    $isImageExist = 'false';
                }
            }
            $postComments[$comment->id]['image_exist'] = $isImageExist;
            if($comment->children){
                $postComments[$comment->id]['subcomments'] = $this->getSubComments($comment->children,$title);
            }
        }
        return $postComments;
    }

        /**
     *  return child comments
     */
    protected function getSubComments($subComments,$title){
        $postChildComments = [];
        foreach($subComments as $subComment){
            $postChildComments[$subComment->id]['body'] = $subComment->body;
            $postChildComments[$subComment->id]['id'] = $subComment->id;
            $postChildComments[$subComment->id]['discussion_post_id'] = $subComment->client_discussion_post_id;
            $postChildComments[$subComment->id]['discussion_comment_id'] = $subComment->client_discussion_comment_id;
            $postChildComments[$subComment->id]['updated_at'] = $subComment->updated_at->diffForHumans();
            $postChildComments[$subComment->id]['title'] = $title;
            $postChildComments[$subComment->id]['client_id'] = $subComment->client_id;
            $postChildComments[$subComment->id]['client_name'] = $subComment->getClient($subComment->client_id)->name;
            if($subComment->clientuser_id > 0){
                $postChildComments[$subComment->id]['is_user'] = true;
                $postChildComments[$subComment->id]['user_id'] = $subComment->clientuser_id;
                $postChildComments[$subComment->id]['user_name'] = $subComment->getUser($subComment->clientuser_id)->name;
                $postChildComments[$subComment->id]['user_image'] = $subComment->getUser($subComment->clientuser_id)->photo;
                if(is_file($subComment->getUser($subComment->clientuser_id)->photo) && true == preg_match('/clientUserStorage/',$subComment->getUser($subComment->clientuser_id)->photo)){
                    $isImageExist = 'system';
                } else if(!empty($subComment->getUser($subComment->clientuser_id)->photo) && false == preg_match('/clientUserStorage/',$subComment->getUser($subComment->clientuser_id)->photo)){
                    $isImageExist = 'other';
                } else {
                    $isImageExist = 'false';
                }
            } else {
                $postChildComments[$subComment->id]['is_user'] = false;
                $postChildComments[$subComment->id]['user_id'] = $subComment->client_id;
                $postChildComments[$subComment->id]['user_name'] = $subComment->getClient($subComment->client_id)->name;
                $postChildComments[$subComment->id]['user_image'] = $subComment->getClient($subComment->client_id)->photo;
                if(is_file($subComment->getClient($subComment->client_id)->photo) && true == preg_match('/client_images/',$subComment->getClient($subComment->client_id)->photo)){
                    $isImageExist = 'system';
                } else if(!empty($subComment->getClient($subComment->client_id)->photo) && false == preg_match('/client_images/',$subComment->getClient($subComment->client_id)->photo)){
                    $isImageExist = 'other';
                } else {
                    $isImageExist = 'false';
                }
            }
            $postChildComments[$subComment->id]['image_exist'] = $isImageExist;
            if($subComment->children){
                $postChildComments[$subComment->id]['subcomments'] = $this->getSubComments($subComment->children,$title);
            }
        }
        return $postChildComments;
    }

      /**
     *  return post by categoryId
     */
    protected function getDiscussionPostsByCategoryId(Request $request){
        $allPosts = [];
        if(Auth::guard('client')->user()){
            $clientId = Auth::guard('client')->user()->id;
        } else {
            $clientId = Auth::guard('clientuser')->user()->client_id;
        }
        $posts = ClientDiscussionPost::where('client_id', $clientId)->where('client_discussion_category_id', $request->get('id'))->orderBy('id', 'desc')->get();
        if(is_object($posts) && false == $posts->isEmpty($posts)){
            foreach ($posts as $post) {
                $allPosts['posts'][$post->id]['title'] = $post->title;
                $allPosts['posts'][$post->id]['body'] = $post->body;
                $allPosts['posts'][$post->id]['id'] = $post->id;
                $allPosts['posts'][$post->id]['answer1'] = $post->answer1;
                $allPosts['posts'][$post->id]['answer2'] = $post->answer2;
                $allPosts['posts'][$post->id]['answer3'] = $post->answer3;
                $allPosts['posts'][$post->id]['answer4'] = $post->answer4;
                $allPosts['posts'][$post->id]['answer'] = $post->answer;
                $allPosts['posts'][$post->id]['solution'] = $post->solution;
                $allPosts['posts'][$post->id]['updated_at'] = $post->updated_at->diffForHumans();
                $allPosts['posts'][$post->id]['client_id'] = $post->client_id;
                if($post->clientuser_id > 0){
                    $allPosts['posts'][$post->id]['is_user'] = true;
                    $allPosts['posts'][$post->id]['user_id'] = $post->clientuser_id;
                    $allPosts['posts'][$post->id]['user_name'] = $post->getUser($post->clientuser_id)->name;
                    $allPosts['posts'][$post->id]['user_image'] = $post->getUser($post->clientuser_id)->photo;
                    if(is_file($post->getUser($post->clientuser_id)->photo) && true == preg_match('/clientUserStorage/',$post->getUser($post->clientuser_id)->photo)){
                        $isImageExist = 'system';
                    } else if(!empty($post->getUser($post->clientuser_id)->photo) && false == preg_match('/clientUserStorage/',$post->getUser($post->clientuser_id)->photo)){
                        $isImageExist = 'other';
                    } else {
                        $isImageExist = 'false';
                    }
                } else {
                    $allPosts['posts'][$post->id]['is_user'] = false;
                    $allPosts['posts'][$post->id]['user_id'] = $post->client_id;
                    $allPosts['posts'][$post->id]['user_name'] = $post->getClient($post->client_id)->name;
                    $allPosts['posts'][$post->id]['user_image'] = $post->getClient($post->client_id)->photo;
                    if(is_file($post->getClient($post->client_id)->photo) && true == preg_match('/client_images/',$post->getClient($post->client_id)->photo)){
                        $isImageExist = 'system';
                    } else if(!empty($post->getClient($post->client_id)->photo) && false == preg_match('/client_images/',$post->getClient($post->client_id)->photo)){
                        $isImageExist = 'other';
                    } else {
                        $isImageExist = 'false';
                    }
                }
                $allPosts['posts'][$post->id]['image_exist'] = $isImageExist;

                if($post->descComments){
                    $allPosts['posts'][$post->id]['comments'] = $this->getComments($post->descComments,$post->title);
                }
            }
        }
        $allPosts['likesCount'] = ClientDiscussionLike::getPostLikes();
        $allPosts['commentLikesCount'] = ClientDiscussionLike::getCommentLikes();
        $allPosts['subcommentLikesCount'] = ClientDiscussionLike::getSubCommentLikes();
        return $allPosts;
    }

    protected function updateComment(Request $request){
        $postId = $request->get('post_id');
        $commentId = $request->get('comment_id');
        $commentBody = $request->get('comment');
        if(!empty($postId) && !empty($commentId) && !empty($commentBody)){
            $comment = ClientDiscussionComment::where('client_discussion_post_id', $postId)->where('id', $commentId)->first();
            if(is_object($comment)){
                DB::connection('mysql2')->beginTransaction();
                try
                {
                    $comment->body = $commentBody;
                    $comment->client_discussion_post_id = $postId;
                    $comment->save();
                    DB::connection('mysql2')->commit();
                }
                catch(\Exception $e)
                {
                    DB::connection('mysql2')->rollback();
                }
            }
        }
        return $this->getPosts();
    }

    protected function updateSubComment(Request $request){
        $postId = $request->get('post_id');
        $commentId = $request->get('comment_id');
        $subcommentId = $request->get('subcomment_id');
        $commentBody = $request->get('comment');
        if(!empty($postId) && !empty($commentId) && !empty($commentBody)){
            $comment = ClientDiscussionSubComment::where('client_discussion_post_id', $postId)->where('client_discussion_comment_id', $commentId)->where('id', $subcommentId)->first();
            if(is_object($comment)){
                DB::connection('mysql2')->beginTransaction();
                try
                {
                    $comment->body = $commentBody;
                    $parentSubComment = ClientDiscussionSubComment::find($comment->parent_id);

                    if(is_object($parentSubComment)){
                        $comment->body = $commentBody;
                        if(Auth::guard('client')->user()){
                            $clientId = Auth::guard('client')->user()->id;
                            $userId = 0;
                            $changedName = '<b>'.Auth::guard('client')->user()->name.'</b>';
                            $comment->body = str_replace(Auth::guard('client')->user()->name, $changedName, $commentBody);
                        } else {
                            $clientId = Auth::guard('clientuser')->user()->client_id;
                            $userId = Auth::guard('clientuser')->user()->id;
                            $changedName = '<b>'.Auth::guard('clientuser')->user()->name.'</b>';
                            $comment->body = str_replace(Auth::guard('clientuser')->user()->name, $changedName, $commentBody);
                        }
                    } else {
                        $comment->body = $commentBody;
                        if(Auth::guard('client')->user()){
                            $clientId = Auth::guard('client')->user()->id;
                            $userId = 0;
                        } else {
                            $clientId = Auth::guard('clientuser')->user()->client_id;
                            $userId = Auth::guard('clientuser')->user()->id;
                        }
                    }
                    $comment->client_discussion_post_id = $postId;
                    $comment->client_discussion_comment_id = $commentId;
                    $comment->save();
                    DB::connection('mysql2')->commit();
                }
                catch(\Exception $e)
                {
                    DB::connection('mysql2')->rollback();
                }
            }
        }
        return $this->getPosts();
    }

    protected function deleteSubComment(Request $request){
        $subcomment = ClientDiscussionSubComment::find(json_decode($request->get('subcomment_id')));
        if(is_object($subcomment)){
            DB::connection('mysql2')->beginTransaction();
            try
            {
                ClientDiscussionLike::deleteSubCommentLikeById($subcomment->id);
                $subcomment->delete();
                DB::connection('mysql2')->commit();
            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
            }
        }
        return $this->getPosts();
    }

    protected function deleteComment(Request $request){
        $comment = ClientDiscussionComment::find(json_decode($request->get('comment_id')));
        if(is_object($comment)){
            DB::connection('mysql2')->beginTransaction();
            try
            {
                if(is_object($comment->children) && false == $comment->children->isEmpty()){
                    foreach($comment->children as $subcomment){
                        ClientDiscussionLike::deleteSubCommentLikeById($subcomment->id);
                        $subcomment->delete();
                    }
                }
                ClientDiscussionLike::deleteCommentLikeById($comment->id);
                $comment->delete();
                DB::connection('mysql2')->commit();
            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
            }
        }
        return $this->getPosts();
    }

    protected function manageQuestions($subdomainName,Request $request){
        if(false == InputSanitise::checkDomain($request)){
            return Redirect::to('/');
        }
        $currentUser = Auth::guard('client')->user();
        $posts = ClientDiscussionPost::where('client_id',$currentUser->id)->where('clientuser_id',0)->orderBy('id','desc')->get();
        $discussionCategories = ClientDiscussionCategory::getCategoriesByClient();
        $likesCount = ClientDiscussionLike::getPostLikes();
        $isClient = 1;
        return view('client.discussion.myQuestions', compact('subdomainName','posts','currentUser','discussionCategories','likesCount','isClient'));
    }

     /**
     *  create discussin post
     */
    protected function createMyPost($subdomainName,Request $request){
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $post = ClientDiscussionPost::createPost($request);
            if(is_object($post)){
                DB::connection('mysql2')->commit();
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return back()->withErrors('something went wrong.');
        }
        return $this->getMyPosts();
    }

    /**
     *  return posts
     */
    protected function getMyPosts(){
        $allPosts = [];
        if(Auth::guard('client')->user()){
            $clientId = Auth::guard('client')->user()->id;
            $posts = ClientDiscussionPost::where('client_id', $clientId)->where('clientuser_id',0)->orderBy('id', 'desc')->get();
        } else {
            $userId = Auth::guard('clientuser')->user()->id;
            $clientId = Auth::guard('clientuser')->user()->client_id;
            $posts = ClientDiscussionPost::where('client_id', $clientId)->where('clientuser_id',$userId)->orderBy('id', 'desc')->get();
        }
        if(is_object($posts) && false == $posts->isEmpty()){
            foreach ($posts as $post) {
                $allPosts['posts'][$post->id]['title'] = $post->title;
                $allPosts['posts'][$post->id]['body'] = $post->body;
                $allPosts['posts'][$post->id]['id'] = $post->id;
                $allPosts['posts'][$post->id]['answer1'] = $post->answer1;
                $allPosts['posts'][$post->id]['answer2'] = $post->answer2;
                $allPosts['posts'][$post->id]['answer3'] = $post->answer3;
                $allPosts['posts'][$post->id]['answer4'] = $post->answer4;
                $allPosts['posts'][$post->id]['answer'] = $post->answer;
                $allPosts['posts'][$post->id]['solution'] = $post->solution;
                $allPosts['posts'][$post->id]['updated_at'] = $post->updated_at->diffForHumans();
                $allPosts['posts'][$post->id]['client_id'] = $post->client_id;
                if($post->clientuser_id > 0){
                    $allPosts['posts'][$post->id]['is_user'] = true;
                    $allPosts['posts'][$post->id]['user_id'] = $post->clientuser_id;
                    $allPosts['posts'][$post->id]['user_name'] = $post->getUser($post->clientuser_id)->name;
                    $allPosts['posts'][$post->id]['user_image'] = $post->getUser($post->clientuser_id)->photo;
                    if(is_file($post->getUser($post->clientuser_id)->photo) && true == preg_match('/clientUserStorage/',$post->getUser($post->clientuser_id)->photo)){
                        $isImageExist = 'system';
                    } else if(!empty($post->getUser($post->clientuser_id)->photo) && false == preg_match('/clientUserStorage/',$post->getUser($post->clientuser_id)->photo)){
                        $isImageExist = 'other';
                    } else {
                        $isImageExist = 'false';
                    }
                } else {
                    $allPosts['posts'][$post->id]['is_user'] = false;
                    $allPosts['posts'][$post->id]['user_id'] = $post->client_id;
                    $allPosts['posts'][$post->id]['user_name'] = $post->getClient($post->client_id)->name;
                    $allPosts['posts'][$post->id]['user_image'] = $post->getClient($post->client_id)->photo;
                    if(is_file($post->getClient($post->client_id)->photo) && true == preg_match('/client_images/',$post->getClient($post->client_id)->photo)){
                        $isImageExist = 'system';
                    } else if(!empty($post->getClient($post->client_id)->photo) && false == preg_match('/client_images/',$post->getClient($post->client_id)->photo)){
                        $isImageExist = 'other';
                    } else {
                        $isImageExist = 'false';
                    }
                }
                $allPosts['posts'][$post->id]['image_exist'] = $isImageExist;
            }
        }
        $allPosts['likesCount'] = ClientDiscussionLike::getPostLikes();
        return $allPosts;
    }

    protected function updateMyPost(Request $request){
        $postId = $request->get('post_id');
        $question = $request->get('update_question');
        $solution = $request->get('updated_solution');
        $answer1 = $request->get('updated_answer1');
        $answer2 = $request->get('updated_answer2');
        $answer3 = $request->get('updated_answer3');
        $answer4 = $request->get('updated_answer4');
        $answer = $request->get('updated_answer');
        $isUpdatedFromDiscussion = $request->get('isUpdatedFromDiscussion');

        if(!empty($postId) && !empty($question)){
            $post = ClientDiscussionPost::find($postId);
            if(is_object($post)){
                DB::connection('mysql2')->beginTransaction();
                try
                {
                    $post->body = $question;
                    $post->answer1 = $answer1;
                    $post->answer2 = $answer2;
                    $post->answer3 = $answer3;
                    $post->answer4 = $answer4;
                    $post->answer = $answer;
                    $post->solution = $solution;
                    $post->save();
                    DB::connection('mysql2')->commit();
                }
                catch(\Exception $e)
                {
                    DB::connection('mysql2')->rollback();
                }
            }
        }

        if('true' == $isUpdatedFromDiscussion){
            return $this->getPosts();
        } else {
            return $this->getMyPosts();
        }
    }

    protected function deleteMyPost(Request $request){
        $post = ClientDiscussionPost::find(json_decode($request->get('post_id')));
        if(is_object($post)){
            DB::connection('mysql2')->beginTransaction();
            try
            {
                $post->deleteCommantsAndSubComments();
                ClientDiscussionLike::deletePostLikeById($post->id);
                $post->delete();
                DB::connection('mysql2')->commit();
            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
            }
        }
        return $this->getMyPosts();
    }

    protected function manageReplies($subdomainName,Request $request){
        if(false == InputSanitise::checkDomain($request)){
            return Redirect::to('/');
        }
        $currentUser = Auth::guard('client')->user();
        $postIds = [];
        $discussionComments = ClientDiscussionComment::where('client_id',$currentUser->id)->where('clientuser_id',0)->select('Client_discussion_post_id')->get();
        if(false == $discussionComments->isEmpty()){
            foreach($discussionComments as $discussionComment){
                $postIds[]= $discussionComment->Client_discussion_post_id;
            }
            $postIds = array_unique($postIds);
        }

        $posts = ClientDiscussionPost::where('client_id',$currentUser->id)->whereIn('id', $postIds)->orderBy('id','desc')->get();
        $isClient = 1;
        $likesCount = ClientDiscussionLike::getPostLikes();
        $commentLikesCount = ClientDiscussionLike::getCommentLikes();
        $subcommentLikesCount = ClientDiscussionLike::getSubCommentLikes();
        return view('client.discussion.myReplies', compact('subdomainName','posts','currentUser','isClient','likesCount','commentLikesCount','subcommentLikesCount'));
    }

    protected function discussionLikePost($subdomainName,Request $request){
        return ClientDiscussionLike::getLikePost($request);
    }

    protected function discussionLikeComment($subdomainName,Request $request){
        return ClientDiscussionLike::getLikeComment($request);
    }

    protected function discussionLikeSubComment($subdomainName,Request $request){
        return ClientDiscussionLike::getLikeSubComment($request);
    }
}
