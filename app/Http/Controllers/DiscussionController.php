<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DiscussionComment;
use App\Models\DiscussionPost;
use App\Models\User;
use App\Models\DiscussionPostLike;
use App\Models\DiscussionCommentLike;
use App\Models\DiscussionSubComment;
use App\Models\DiscussionSubCommentLike;
use App\Models\DiscussionCategory;
use Auth, DB;
use Validator;
use Redirect, Session;

class DiscussionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected $validatePost = [
            'title' => 'required',
            'question' => 'required',
            'post_category_id' => 'required',
        ];
    protected $validateComment = [
            'comment' => 'required',
            'discussion_post_id' => 'required',
        ];
    protected $validateSubComment = [
            'subcomment' => 'required',
            'discussion_post_id' => 'required',
            'comment_id' => 'required',
        ];

    /**
     *  show list of discussion post
     */
    protected function discussion(){
        $postCategoryIds = [];
        $discussionCategories =DiscussionCategory::all();
        $posts = DiscussionPost::orderBy('id', 'desc')->get();
        if(false == $posts->isEmpty()){
            foreach($posts as $post){
                $postCategoryIds[] = $post->category_id;
            }
            $postCategoryIds = array_unique($postCategoryIds);
        }
        $user = new User;
        $likesCount = DiscussionPostLike::getLikes();
        $commentLikesCount = DiscussionCommentLike::getLiksByPosts($posts);
        $subcommentLikesCount = DiscussionSubCommentLike::getLiksByPosts($posts);
        if(is_object(Auth::user())){
            $currentUser = Auth::user()->id;
        } else {
            $currentUser = 0;
        }
        return view('discussion.discussion', compact('discussionCategories','posts', 'user', 'postCategoryIds', 'likesCount', 'commentLikesCount', 'currentUser', 'subcommentLikesCount'));
    }

    /**
     *  create discussin post
     */
    protected function createPost(Request $request){
        $v = Validator::make($request->all(), $this->validatePost);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }

        DB::beginTransaction();
        try
        {
            $post = DiscussionPost::createPost($request);
            $postModuleId = trim($request->get('all_post_module_id'));
            DB::commit();
            if(is_object($post)){
                if( 1 == $postModuleId){
                    return redirect()->route('myQuestions');
                }
            }
            Session::put('show_comment_area', 0);
            Session::put('show_post_area', $post->id);
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return back()->withErrors('something went wrong.');
        }
        return Redirect::to('discussion');
    }

    /**
     *  create discussin post comment
     */
    protected function createComment(Request $request){
        $v = Validator::make($request->all(), $this->validateComment);

        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::beginTransaction();
        try
        {
            $comment = DiscussionComment::createComment($request);
            $postModuleId = trim($request->get('all_post_module_id'));
            DB::commit();
            Session::put('show_comment_area', 0);
            Session::put('show_post_area', $comment->discussion_post_id);
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return back()->withErrors('something went wrong.');
        }
        return Redirect::to('discussion');
    }

    /**
     *  create discussin post child comment
     */
    protected function createSubComment(Request $request){
        $v = Validator::make($request->all(), $this->validateSubComment);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::beginTransaction();
        try
        {
            $subcomment = DiscussionSubComment::createSubComment($request);

            $postModuleId = trim($request->get('all_post_module_id'));
            DB::commit();
            Session::put('show_comment_area', 0);
            Session::put('show_post_area', $subcomment->discussion_post_id);
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return back()->withErrors('something went wrong.');
        }
        return Redirect::to('discussion');
    }

    /**
     *  return child comments
     */
    protected function getSubComments($subComments,$title){
        $postChildComments = [];
        $user = new user ;
        foreach($subComments as $subComment){
            $postChildComments[$subComment->id]['body'] = $subComment->body;
            $postChildComments[$subComment->id]['id'] = $subComment->id;
            $postChildComments[$subComment->id]['discussion_post_id'] = $subComment->discussion_post_id;
            $postChildComments[$subComment->id]['discussion_comment_id'] = $subComment->discussion_comment_id;
            $postChildComments[$subComment->id]['user_name'] = $user->find($subComment->user_id)->name;
            $postChildComments[$subComment->id]['user_id'] = $subComment->user_id;
            $postChildComments[$subComment->id]['updated_at'] = $subComment->updated_at->diffForHumans();
            $postChildComments[$subComment->id]['title'] = $title;
            if($subComment->children){
                $postChildComments[$subComment->id]['subcomments'] = $this->getSubComments($subComment->children,$title);
            }
        }

        return $postChildComments;
    }

    /**
     *  return post comments
     */
    protected function getComments($comments, $title){
        $postComments = [];
        $commentComments = [];
        $user = new user ;
        foreach($comments as $comment){
            $postComments[$comment->id]['body'] = $comment->body;
            $postComments[$comment->id]['id'] = $comment->id;
            $postComments[$comment->id]['discussion_post_id'] = $comment->discussion_post_id;
            $postComments[$comment->id]['user_id'] = $comment->user_id;
            $postComments[$comment->id]['user_name'] = $user->find($comment->user_id)->name;
            $postComments[$comment->id]['updated_at'] = $comment->updated_at->diffForHumans();
            $postComments[$comment->id]['title'] = $title;
            if($comment->children){
                $postComments[$comment->id]['subcomments'] = $this->getSubComments($comment->children,$title);
            }
        }
        return $postComments;
    }

    /**
     *  return post by categoryId
     */
    protected function getDiscussionPostsByCategoryId(Request $request){
        $allPosts = [];
        $posts = DiscussionPost::where('category_id', $request->get('id'))->orderBy('id', 'desc')->get();
        $user = new user ;
        foreach ($posts as $post) {
            $allPosts['posts'][$post->id]['title'] = $post->title;
            $allPosts['posts'][$post->id]['body'] = $post->body;
            $allPosts['posts'][$post->id]['id'] = $post->id;
            $allPosts['posts'][$post->id]['user_id'] = $post->user_id;
            $allPosts['posts'][$post->id]['user_name'] = $user->find($post->user_id)->name;
            $allPosts['posts'][$post->id]['updated_at'] = $post->updated_at->diffForHumans();

            if($post->descComments){
                $allPosts['posts'][$post->id]['comments'] = $this->getComments($post->descComments,$post->title);
            }
        }
        $allPosts['likesCount'] = DiscussionPostLike::getLikes();
        $allPosts['commentLikesCount'] = DiscussionCommentLike::getLiksByPosts($posts);
        $allPosts['subcommentLikesCount'] = DiscussionSubCommentLike::getLiksByPosts($posts);
        return $allPosts;
    }

    /**
     *  return discussion post by filter criteria
     */
    protected function getDuscussionPostsBySearchArray(Request $request){
        $allPosts = [];
        $posts = DiscussionPost::getDuscussionPostsBySearchArray($request);
        $user = new user ;
        foreach ($posts as $post) {
            $allPosts['posts'][$post->id]['title'] = $post->title;
            $allPosts['posts'][$post->id]['body'] = $post->body;
            $allPosts['posts'][$post->id]['id'] = $post->id;
            $allPosts['posts'][$post->id]['user_name'] = $user->find($post->user_id)->name;
            $allPosts['posts'][$post->id]['updated_at'] = $post->updated_at->diffForHumans();

            if($post->descComments){
                $allPosts['posts'][$post->id]['comments'] = $this->getComments($post->descComments,$post->title);
            }
        }
        $allPosts['likesCount'] = DiscussionPostLike::getLikes();
        $allPosts['commentLikesCount'] = DiscussionCommentLike::getLiksByPosts($posts);
        $allPosts['subcommentLikesCount'] = DiscussionSubCommentLike::getLiksByPosts($posts);
        return $allPosts;
    }

    protected function discussionLikePost(Request $request){
        return DiscussionPostLike::getLikePost($request);
    }

    protected static function discussionLikeComment(Request $request){
        return DiscussionCommentLike::getLikeComment($request);
    }

    protected static function discussionLikeSubComment(Request $request){
        return DiscussionSubCommentLike::getLikeSubComment($request);
    }

    protected function deleteSubComment(Request $request){
        $subcomment = DiscussionSubComment::find(json_decode($request->get('subcomment_id')));
        if(is_object($subcomment)){
            DB::beginTransaction();
            try
            {
                if(is_object($subcomment->deleteLikes) && false == $subcomment->deleteLikes->isEmpty()){
                    foreach($subcomment->deleteLikes as $subcommentLike){
                        $subcommentLike->delete();
                    }
                }
                Session::put('show_comment_area', $subcomment->discussion_comment_id);
                Session::put('show_post_area', 0);
                $subcomment->delete();
                DB::commit();
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('discussion');
    }

    protected function deleteComment(Request $request){
        $comment = DiscussionComment::find(json_decode($request->get('comment_id')));
        if(is_object($comment)){
            DB::beginTransaction();
            try
            {
                if(is_object($comment->children) && false == $comment->children->isEmpty()){
                    foreach($comment->children as $subcomment){
                        if(is_object($subcomment->deleteLikes) && false == $subcomment->deleteLikes->isEmpty()){
                            foreach($subcomment->deleteLikes as $subcommentLike){
                                $subcommentLike->delete();
                            }
                        }
                        $subcomment->delete();
                    }
                }
                if(is_object($comment->commentLikes) && false == $comment->commentLikes->isEmpty()){
                    foreach($comment->commentLikes as $commentLike){
                        $commentLike->delete();
                    }
                }
                Session::put('show_comment_area', 0);
                Session::put('show_post_area', $comment->discussion_post_id);
                $comment->delete();
                DB::commit();
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('discussion');
    }

    protected function deletePost(Request $request){
        $post = DiscussionPost::find(json_decode($request->get('post_id')));

        if(is_object($post)){
            DB::beginTransaction();
            try
            {
                $post->deleteCommantsAndSubComments();
                $post->delete();
                DB::commit();
                Session::put('show_comment_area', 0);
                Session::put('show_post_area', 0);
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('discussion');
    }

    protected function updatePost(Request $request){
        $postId = $request->get('post_id');
        $question = $request->get('update_question');
        if(!empty($postId) && !empty($question)){
            $post = DiscussionPost::find($postId);
            if(is_object($post)){
                DB::beginTransaction();
                try
                {
                    $post->body = $question;
                    $post->save();
                    DB::commit();
                    Session::put('show_comment_area', 0);
                    Session::put('show_post_area', $post->id);
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return back()->withErrors('something went wrong.');
                }
            }
        }
        return Redirect::to('discussion');
    }

    protected function updateComment(Request $request){
        $postId = $request->get('post_id');
        $commentId = $request->get('comment_id');
        $commentBody = $request->get('comment');
        if(!empty($postId) && !empty($commentId) && !empty($commentBody)){
            $comment = DiscussionComment::where('discussion_post_id', $postId)->where('id', $commentId)->first();
            if(is_object($comment)){
                DB::beginTransaction();
                try
                {
                    $comment->body = $commentBody;
                    $comment->discussion_post_id = $postId;
                    $comment->save();
                    DB::commit();
                    Session::put('show_comment_area', 0);
                    Session::put('show_post_area', $comment->discussion_post_id);
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return back()->withErrors('something went wrong.');
                }
            }
        }
        return Redirect::to('discussion');
    }

    protected function updateSubComment(Request $request){
        $postId = $request->get('post_id');
        $commentId = $request->get('comment_id');
        $subcommentId = $request->get('subcomment_id');
        $commentBody = $request->get('comment');
        if(!empty($postId) && !empty($commentId) && !empty($commentBody)){
            $comment = DiscussionSubComment::where('discussion_post_id', $postId)->where('discussion_comment_id', $commentId)->where('id', $subcommentId)->first();
            if(is_object($comment)){
                DB::beginTransaction();
                try
                {
                    $comment->body = $commentBody;
                    $parentSubComment = DiscussionSubComment::find($comment->parent_id);

                    if(is_object($parentSubComment) && $parentSubComment->user_id !== Auth::user()->id){
                        $comment->body = $commentBody;
                        $user = User::find($comment->user_id);
                        if(is_object($user)){
                            $comment->body = '<b>'.$user->name.'</b> '.$commentBody;
                        }
                    } else {
                        $comment->body = $commentBody;
                    }
                    $comment->discussion_post_id = $postId;
                    $comment->discussion_comment_id = $commentId;
                    $comment->save();
                    DB::commit();
                    Session::put('show_comment_area', 0);
                    Session::put('show_post_area', $comment->discussion_post_id);
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return back()->withErrors('something went wrong.');
                }
            }
        }
        return Redirect::to('discussion');
    }

    function goToPost(Request $request){
        Session::put('show_comment_area', 0);
        Session::put('show_post_area', $request->get('post_id'));
        return Redirect::to('discussion');
    }

    function goToComment(Request $request){
        Session::put('show_comment_area', $request->get('comment_id'));
        Session::put('show_post_area', 0);
        return Redirect::to('discussion');
    }
}