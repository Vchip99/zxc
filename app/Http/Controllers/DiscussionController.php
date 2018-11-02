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
use App\Models\Notification;
use App\Models\ReadNotification;
use Auth,DB,Validator,Redirect,Session,Cache;
use App\Models\Add;

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
    protected function discussion(Request $request,$commentId=NULL, $subcommentId=NULL ){
        $postCategoryIds = [];
        $discussionCategories = Cache::remember('vchip:discussions:discussionCategories',60, function() {
            return DiscussionCategory::all();
        });
        $posts = DiscussionPost::orderBy('id', 'desc')->get();
        if(is_object($posts) && false == $posts->isEmpty()){
            foreach($posts as $post){
                $postCategoryIds[] = $post->category_id;
            }
            $postCategoryIds = array_unique($postCategoryIds);
        }
        $user = new User;
        $likesCount = DiscussionPostLike::getLikes();
        $commentLikesCount = DiscussionCommentLike::getLiksByPosts($posts);
        $subcommentLikesCount = DiscussionSubCommentLike::getLiksByPosts($posts);
        $currentUser = Auth::user();
        if(is_object($currentUser)){
            if($commentId > 0 || ($commentId > 0 && $subcommentId > 0)){
                Session::set('show_post_area', 0);
                DB::beginTransaction();
                try
                {
                    if($commentId > 0  && $subcommentId > 0){
                        $readNotification = ReadNotification::readNotificationByModuleByModuleIdByUser(Notification::USERDISCUSSIONSUBCOMMENTNOTIFICATION,$subcommentId,$currentUser->id);
                        if(is_object($readNotification)){
                            DB::commit();
                        }
                        Session::set('show_subcomment_area', $subcommentId);
                        Session::set('show_comment_area', 0);
                    } else {
                        Session::set('show_comment_area', $commentId);
                        Session::set('show_subcomment_area', 0);
                    }

                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return redirect()->back()->withErrors('something went wrong.');
                }
            } else {
                Session::set('show_post_area', 0);
                Session::set('show_comment_area', 0);
                Session::set('show_subcomment_area', 0);
            }
        } else {
            $currentUser = NULL;
        }
        $date = date('Y-m-d');
        $ads = Add::getAdds($request->url(),$date);
        return view('discussion.discussion', compact('discussionCategories','posts', 'postCategoryIds', 'likesCount', 'commentLikesCount', 'currentUser', 'subcommentLikesCount', 'ads'));
    }

    /**
     *  create discussin post
     */
    protected function createPost(Request $request){
        DB::beginTransaction();
        try
        {
            $post = DiscussionPost::createPost($request);
            if(is_object($post)){
                DB::commit();
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return back()->withErrors('something went wrong.');
        }
        return $this->getPosts();
    }

    /**
     *  create discussin post
     */
    protected function createMyPost(Request $request){
        DB::beginTransaction();
        try
        {
            $post = DiscussionPost::createPost($request);
            if(is_object($post)){
                DB::commit();
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return back()->withErrors('something went wrong.');
        }
        return $this->getMyPosts();
    }

    /**
     *  create discussin post comment
     */
    protected function createComment(Request $request){
        DB::beginTransaction();
        try
        {
            $comment = DiscussionComment::createComment($request);
            $post = DiscussionPost::find($comment->discussion_post_id);
            if(is_object($post) && is_object($comment)){
                $string = (strlen($post->body) > 50) ? substr($post->body,0,50).'...' : $post->body;
                $notificationMessage = '<a href="'.$request->root().'/discussion/'.$comment->id.' target="_blank"">A reply of your post: '. trim($string, '<p></p>')  .'</a>';
                Notification::addCommentNotification($notificationMessage, Notification::USERDISCUSSIONCOMMENTNOTIFICATION, $comment->id,$comment->user_id,$post->user_id);
            }
            DB::commit();
        }
        catch(\Exception $e)
        {
            DB::rollback();
        }
        return $this->getPosts();
    }

    /**
     *  create discussin post child comment
     */
    protected function createSubComment(Request $request){
        DB::beginTransaction();
        try
        {
            $commentId = $request->get('comment_id');
            $subcommentId = $request->get('parent_id');
            $subcomment = DiscussionSubComment::createSubComment($request);
            if($commentId > 0 && $subcommentId > 0){
                $parentComment = DiscussionSubComment::where('id',$subcommentId)->where('user_id', '!=', Auth::user()->id)->first();
            } else {
                $parentComment = DiscussionComment::where('id',$subcomment->discussion_comment_id)->first();
            }
            if(is_object($parentComment)){
                $string = (strlen($parentComment->body) > 50) ? substr($parentComment->body,0,50).'...' : $parentComment->body;
                $notificationMessage = '<a href="'.$request->root().'/discussion/'.$parentComment->id.'/'.$subcomment->id.'" target="_blank">A reply of your comment: '. trim($string, '<p></p>')  .'</a>';
                Notification::addCommentNotification($notificationMessage, Notification::USERDISCUSSIONSUBCOMMENTNOTIFICATION, $subcomment->id,$subcomment->user_id,$parentComment->user_id);
            }
            DB::commit();
        }
        catch(\Exception $e)
        {
            DB::rollback();
        }
        return $this->getPosts();
    }

    /**
     *  return child comments
     */
    protected function getSubComments($subComments,$title){
        $postChildComments = [];
        foreach($subComments as $subComment){
            $postChildComments[$subComment->id]['body'] = $subComment->body;
            $postChildComments[$subComment->id]['id'] = $subComment->id;
            $postChildComments[$subComment->id]['discussion_post_id'] = $subComment->discussion_post_id;
            $postChildComments[$subComment->id]['discussion_comment_id'] = $subComment->discussion_comment_id;
            $postChildComments[$subComment->id]['user_name'] = $subComment->getUser($subComment->user_id)->name;
            $postChildComments[$subComment->id]['user_id'] = $subComment->user_id;
            $postChildComments[$subComment->id]['updated_at'] = $subComment->updated_at->diffForHumans();
            $postChildComments[$subComment->id]['title'] = $title;
            $postChildComments[$subComment->id]['user_image'] = $subComment->getUser($subComment->user_id)->photo;
            if(is_file($subComment->getUser($subComment->user_id)->photo) && true == preg_match('/userStorage/',$subComment->getUser($subComment->user_id)->photo)){
                $isImageExist = 'system';
            } else if(!empty($subComment->getUser($subComment->user_id)->photo) && false == preg_match('/userStorage/',$subComment->getUser($subComment->user_id)->photo)){
                $isImageExist = 'other';
            } else {
                $isImageExist = 'false';
            }
            $postChildComments[$subComment->id]['image_exist'] = $isImageExist;
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
        foreach($comments as $comment){
            $postComments[$comment->id]['body'] = $comment->body;
            $postComments[$comment->id]['id'] = $comment->id;
            $postComments[$comment->id]['discussion_post_id'] = $comment->discussion_post_id;
            $postComments[$comment->id]['user_id'] = $comment->user_id;
            $postComments[$comment->id]['user_name'] = $comment->getUser($comment->user_id)->name;
            $postComments[$comment->id]['updated_at'] = $comment->updated_at->diffForHumans();
            $postComments[$comment->id]['title'] = $title;
            $postComments[$comment->id]['user_image'] = $comment->getUser($comment->user_id)->photo;
            if(is_file($comment->getUser($comment->user_id)->photo) && true == preg_match('/userStorage/',$comment->getUser($comment->user_id)->photo)){
                $isImageExist = 'system';
            } else if(!empty($comment->getUser($comment->user_id)->photo) && false == preg_match('/userStorage/',$comment->getUser($comment->user_id)->photo)){
                $isImageExist = 'other';
            } else {
                $isImageExist = 'false';
            }
            $postComments[$comment->id]['image_exist'] = $isImageExist;

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
        if(is_object($posts) && false == $posts->isEmpty()){
            foreach ($posts as $post) {
                $allPosts['posts'][$post->id]['title'] = $post->title;
                $allPosts['posts'][$post->id]['body'] = $post->body;
                $allPosts['posts'][$post->id]['id'] = $post->id;
                $allPosts['posts'][$post->id]['user_id'] = $post->user_id;
                $allPosts['posts'][$post->id]['answer1'] = $post->answer1;
                $allPosts['posts'][$post->id]['answer2'] = $post->answer2;
                $allPosts['posts'][$post->id]['answer3'] = $post->answer3;
                $allPosts['posts'][$post->id]['answer4'] = $post->answer4;
                $allPosts['posts'][$post->id]['answer'] = $post->answer;
                $allPosts['posts'][$post->id]['solution'] = $post->solution;
                $allPosts['posts'][$post->id]['user_name'] = $post->getUser($post->user_id)->name;
                $allPosts['posts'][$post->id]['updated_at'] = $post->updated_at->diffForHumans();
                $allPosts['posts'][$post->id]['user_image'] = $post->getUser($post->user_id)->photo;
                if(is_file($post->getUser($post->user_id)->photo) && true == preg_match('/userStorage/',$post->getUser($post->user_id)->photo)){
                    $isImageExist = 'system';
                } else if(!empty($post->getUser($post->user_id)->photo) && false == preg_match('/userStorage/',$post->getUser($post->user_id)->photo)){
                    $isImageExist = 'other';
                } else {
                    $isImageExist = 'false';
                }
                $allPosts['posts'][$post->id]['image_exist'] = $isImageExist;
                if($post->descComments){
                    $allPosts['posts'][$post->id]['comments'] = $this->getComments($post->descComments,$post->title);
                }
            }
        }
        $allPosts['likesCount'] = DiscussionPostLike::getLikes();
        $allPosts['commentLikesCount'] = DiscussionCommentLike::getLiksByPosts($posts);
        $allPosts['subcommentLikesCount'] = DiscussionSubCommentLike::getLiksByPosts($posts);
        return $allPosts;
    }

    /**
     *  return posts
     */
    protected function getPosts(){
        Session::set('show_post_area', 0);
        Session::set('show_comment_area', 0);
        Session::set('show_subcomment_area', 0);
        $allPosts = [];
        $posts = DiscussionPost::orderBy('id', 'desc')->get();
        if(is_object($posts) && false == $posts->isEmpty()){
            foreach ($posts as $post) {
                $allPosts['posts'][$post->id]['title'] = $post->title;
                $allPosts['posts'][$post->id]['body'] = $post->body;
                $allPosts['posts'][$post->id]['id'] = $post->id;
                $allPosts['posts'][$post->id]['user_id'] = $post->user_id;
                $allPosts['posts'][$post->id]['answer1'] = $post->answer1;
                $allPosts['posts'][$post->id]['answer2'] = $post->answer2;
                $allPosts['posts'][$post->id]['answer3'] = $post->answer3;
                $allPosts['posts'][$post->id]['answer4'] = $post->answer4;
                $allPosts['posts'][$post->id]['answer'] = $post->answer;
                $allPosts['posts'][$post->id]['solution'] = $post->solution;
                $allPosts['posts'][$post->id]['user_name'] = $post->getUser($post->user_id)->name;
                $allPosts['posts'][$post->id]['updated_at'] = $post->updated_at->diffForHumans();
                $allPosts['posts'][$post->id]['user_image'] = $post->getUser($post->user_id)->photo;
                if(is_file($post->getUser($post->user_id)->photo) && true == preg_match('/userStorage/',$post->getUser($post->user_id)->photo)){
                    $isImageExist = 'system';
                } else if(!empty($post->getUser($post->user_id)->photo) && false == preg_match('/userStorage/',$post->getUser($post->user_id)->photo)){
                    $isImageExist = 'other';
                } else {
                    $isImageExist = 'false';
                }
                $allPosts['posts'][$post->id]['image_exist'] = $isImageExist;

                if($post->descComments){
                    $allPosts['posts'][$post->id]['comments'] = $this->getComments($post->descComments,$post->title);
                }
            }
        }
        $allPosts['likesCount'] = DiscussionPostLike::getLikes();
        $allPosts['commentLikesCount'] = DiscussionCommentLike::getLiksByPosts($posts);
        $allPosts['subcommentLikesCount'] = DiscussionSubCommentLike::getLiksByPosts($posts);
        return $allPosts;
    }

    /**
     *  return posts
     */
    protected function getMyPosts(){
        Session::set('show_post_area', 0);
        Session::set('show_comment_area', 0);
        Session::set('show_subcomment_area', 0);
        $allPosts = [];
        $currentUser = Auth::user();
        $posts = DiscussionPost::where('user_id', $currentUser->id)->orderBy('discussion_posts.id', 'desc')->get();
        if(is_object($posts) && false == $posts->isEmpty()){
            foreach ($posts as $post) {
                $allPosts['posts'][$post->id]['title'] = $post->title;
                $allPosts['posts'][$post->id]['body'] = $post->body;
                $allPosts['posts'][$post->id]['id'] = $post->id;
                $allPosts['posts'][$post->id]['user_id'] = $post->user_id;
                $allPosts['posts'][$post->id]['answer1'] = $post->answer1;
                $allPosts['posts'][$post->id]['answer2'] = $post->answer2;
                $allPosts['posts'][$post->id]['answer3'] = $post->answer3;
                $allPosts['posts'][$post->id]['answer4'] = $post->answer4;
                $allPosts['posts'][$post->id]['answer'] = $post->answer;
                $allPosts['posts'][$post->id]['solution'] = $post->solution;
                $allPosts['posts'][$post->id]['user_name'] = $post->getUser($post->user_id)->name;
                $allPosts['posts'][$post->id]['updated_at'] = $post->updated_at->diffForHumans();
                $allPosts['posts'][$post->id]['user_image'] = $post->getUser($post->user_id)->photo;
                if(is_file($post->getUser($post->user_id)->photo) && true == preg_match('/userStorage/',$post->getUser($post->user_id)->photo)){
                    $isImageExist = 'system';
                } else if(!empty($post->getUser($post->user_id)->photo) && false == preg_match('/userStorage/',$post->getUser($post->user_id)->photo)){
                    $isImageExist = 'other';
                } else {
                    $isImageExist = 'false';
                }
                $allPosts['posts'][$post->id]['image_exist'] = $isImageExist;

                if($post->descComments){
                    $allPosts['posts'][$post->id]['comments'] = $this->getComments($post->descComments,$post->title);
                }
            }
        }
        $allPosts['likesCount'] = DiscussionPostLike::getLikes();
        return $allPosts;
    }

    /**
     *  return discussion post by filter criteria
     */
    protected function getDuscussionPostsBySearchArray(Request $request){
        $allPosts = [];
        $posts = DiscussionPost::getDuscussionPostsBySearchArray($request);
        if(is_object($posts) && false == $posts->isEmpty()){
            foreach ($posts as $post) {
                $allPosts['posts'][$post->id]['title'] = $post->title;
                $allPosts['posts'][$post->id]['body'] = $post->body;
                $allPosts['posts'][$post->id]['id'] = $post->id;
                $allPosts['posts'][$post->id]['user_id'] = $post->user_id;
                $allPosts['posts'][$post->id]['answer1'] = $post->answer1;
                $allPosts['posts'][$post->id]['answer2'] = $post->answer2;
                $allPosts['posts'][$post->id]['answer3'] = $post->answer3;
                $allPosts['posts'][$post->id]['answer4'] = $post->answer4;
                $allPosts['posts'][$post->id]['answer'] = $post->answer;
                $allPosts['posts'][$post->id]['solution'] = $post->solution;
                $allPosts['posts'][$post->id]['user_name'] = $post->getUser($post->user_id)->name;
                $allPosts['posts'][$post->id]['updated_at'] = $post->updated_at->diffForHumans();
                $allPosts['posts'][$post->id]['user_image'] = $post->getUser($post->user_id)->photo;
                if(is_file($post->getUser($post->user_id)->photo) && true == preg_match('/userStorage/',$post->getUser($post->user_id)->photo)){
                    $isImageExist = 'system';
                } else if(!empty($post->getUser($post->user_id)->photo) && false == preg_match('/userStorage/',$post->getUser($post->user_id)->photo)){
                    $isImageExist = 'other';
                } else {
                    $isImageExist = 'false';
                }
                $allPosts['posts'][$post->id]['image_exist'] = $isImageExist;
                if($post->descComments){
                    $allPosts['posts'][$post->id]['comments'] = $this->getComments($post->descComments,$post->title);
                }
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
                $subcomment->delete();
                DB::commit();
            }
            catch(\Exception $e)
            {
                DB::rollback();
            }
        }
        return $this->getPosts();
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
                $comment->delete();
                DB::commit();
            }
            catch(\Exception $e)
            {
                DB::rollback();
            }
        }
        return $this->getPosts();
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
            }
            catch(\Exception $e)
            {
                DB::rollback();
            }
        }
        return $this->getPosts();
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
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                }
            }
        }
        return $this->getPosts();
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
                }
                catch(\Exception $e)
                {
                    DB::rollback();
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
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                }
            }
        }
        return $this->getPosts();
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