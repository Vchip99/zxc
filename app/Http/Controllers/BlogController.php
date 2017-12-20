<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\BlogComment;
use App\Models\User;
use App\Models\BlogCommentLike;
use App\Models\BlogSubComment;
use App\Models\BlogSubCommentLike;
use App\Models\BlogTag;
use App\Models\BlogCategory;
use App\Models\BlogLikes;
use App\Models\Notification;
use App\Models\ReadNotification;
use DB, Auth, Session;
use Validator, Redirect;
use App\Models\Add;

class BlogController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        // $this->middleware('auth');
    }

    protected $validateBlogComment = [
        'comment' => 'required',
        'blog_id' => 'required',
    ];
    protected $validateBlogChildComment = [
        'comment' => 'required',
        'blog_id' => 'required',
        'comment_id' => 'required',
    ];

    /**
     *  show all blog
     */
    protected function show(Request $request){
        $blogs = Blog::orderBy('id', 'desc')->paginate(5);
        $blogCategories = BlogCategory::all();
        $date = date('Y-m-d');
        $ads = Add::getAdds($request->url(),$date);
        return view('blog.blog', compact('blogs', 'blogCategories', 'ads'));
    }

    /**
     *  show blog comments by blogId
     */
    protected function blogComment($id, $subcomment=NULL){
        $id = json_decode($id);
        if(isset($id)){
            $blog = Blog::find($id);
            if(is_object($blog)){
                $comments = BlogComment::where('blog_id', $id)->orderBy('id', 'desc')->get();
                $user = new User;
                $commentLikesCount = BlogCommentLike::getLikesByBlogId($blog->id);
                $subcommentLikesCount = BlogSubCommentLike::getLikesByBlogId($blog->id);
                $blogs = Blog::orderBy('id', 'desc')->get();
                $blogTags = BlogTag::where('blog_id', $blog->id)->get();
                if(is_object(Auth::user())){
                    $currentUser = Auth::user()->id;
                    if($id > 0 || $subcomment > 0){
                        DB::beginTransaction();
                        try
                        {
                            if($id > 0 && $subcomment == NULL){
                                $readNotification = ReadNotification::readNotificationByModuleByModuleIdByUser(Notification::ADMINBLOG,$id,$currentUser);
                                if(is_object($readNotification)){
                                    DB::commit();
                                }
                            } else {
                                Session::set('show_subcomment_area', $subcomment);
                            }
                            Session::set('blog_comment_area', 0);
                        }
                        catch(\Exception $e)
                        {
                            DB::rollback();
                            return redirect()->back()->withErrors('something went wrong.');
                        }
                    } else {
                        Session::set('show_subcomment_area', 0);
                    }
                } else {
                    $currentUser = 0;
                }
                $likesCount = BlogLikes::getLikesByBlogId($blog->id);

                return view('blog.blog_comment', compact('blog', 'blogs', 'comments', 'user', 'commentLikesCount', 'currentUser', 'subcommentLikesCount', 'blogTags', 'likesCount'));
            }
        }
        return Redirect::to('blog');
    }

    /**
     *  create/store blog comment
     */
    protected function createBlogComment(Request $request){
        $blogId = strip_tags(trim($request->get('blog_id')));
        DB::beginTransaction();
        try
        {
            $blogComment = BlogComment::createComment($request);
            Session::put('blog_comment_area', $blogComment->id);
            DB::commit();
        }
        catch(\Exception $e)
        {
            DB::rollback();
            // return back()->withErrors('something went wrong.');
        }
        return $this->getComments($blogId);
    }

    /**
     *  create/store blog child comment
     */
    protected function createBlogSubComment(Request $request){
        DB::beginTransaction();
        $blogId = strip_tags(trim($request->get('blog_id')));
        try
        {
            $commentId = $request->get('comment_id');
            $subcommentId = $request->get('subcomment_id');
            $blogComment = BlogSubComment::createSubComment($request);
            if($commentId > 0 && $subcommentId > 0){
                $parentComment = BlogSubComment::where('id',$subcommentId)->where('user_id', '!=', Auth::user()->id)->first();
            } else {
                $parentComment = BlogComment::where('id',$blogComment->blog_comment_id)->first();
            }
            if(is_object($parentComment)){
                $string = (strlen($parentComment->body) > 50) ? substr($parentComment->body,0,50).'...' : $parentComment->body;
                $notificationMessage = '<a href="'.$request->root().'/blogComment/'.$blogId.'/'.$blogComment->id.'">A reply of your comment: '. trim($string, '<p></p>')  .'</a>';
                Notification::addCommentNotification($notificationMessage, Notification::USERBLOGNOTIFICATION, $blogComment->id,$blogComment->user_id,$parentComment->user_id);
            }

            DB::commit();
            Session::put('blog_comment_area', $blogComment->blog_comment_id);
        }
        catch(\Exception $e)
        {
            DB::rollback();
        }
        return $this->getComments($blogId);
    }

    /**
     *  return blogs by categoryId
     */
    protected function getBlogsByCategoryId(Request $request){

        $categoryId = $request->get('id');
        if(isset($categoryId)){
            return Blog::getBlogsByCategory($categoryId);
        } else {
            $categoryId = $request->get('category_id');
            $page = $request->get('page');
            $blogs = Blog::getBlogsByCategory($categoryId, $page);
            $blogCategories = BlogCategory::all();
            return view('blog.category_blog', compact('blogs', 'blogCategories', 'categoryId'));
        }
        return [];
    }

    protected function likeBlog(Request $request){
        return BlogLikes::getLikeBlog($request);
    }

    protected function likeBlogComment(Request $request){
        return BlogCommentLike::getLikeBlogComment($request);
    }

    protected function likeBlogSubComment(Request $request){
        return BlogSubCommentLike::getLikeBlogSubComment($request);
    }

    protected function updateBlogComment(Request $request){
        $blogId = $request->get('blog_id');
        $commentId = $request->get('comment_id');
        $commentBody = $request->get('comment');
        if(!empty($blogId) && !empty($commentId) && !empty($commentBody)){
            $comment = BlogComment::where('blog_id', $blogId)->where('id', $commentId)->first();
            if(is_object($comment)){
                DB::beginTransaction();
                try
                {
                    $comment->body = $commentBody;
                    $comment->save();
                    DB::commit();
                    // Session::put('blog_comment_area', $comment->id);
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    // return back()->withErrors('something went wrong.');
                }
            }
        }
        return $this->getComments($blogId);
    }

    protected function deleteBlogComment(Request $request){
        $blogId = $request->get('blog_id');
        $commentId = $request->get('comment_id');
        if(!empty($blogId) && !empty($commentId)){
            $comment = BlogComment::where('blog_id', $blogId)->where('id', $commentId)->first();
            if(is_object($comment)){
                DB::beginTransaction();
                try
                {
                    if(is_object($comment->children) && false == $comment->children->isEmpty()){
                        foreach($comment->children as $subcomment){
                            if(is_object($subcomment->deleteLikes) && false == $subcomment->deleteLikes->isEmpty()){
                                foreach($subcomment->deleteLikes as $deleteLike){
                                    $deleteLike->delete();
                                }
                            }
                            $subcomment->delete();
                        }
                    }
                    if(is_object($comment->deleteLikes) && false == $comment->deleteLikes->isEmpty()){
                        foreach($comment->deleteLikes as $deleteLike){
                            $deleteLike->delete();
                        }
                    }
                    // Session::put('blog_comment_area', 0);
                    $comment->delete();
                    DB::commit();
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                }
            }
        }
        return $this->getComments($blogId);
    }

    protected function updateBlogSubComment(Request $request){
        $blogId = $request->get('blog_id');
        $commentId = $request->get('comment_id');
        $subcommentId = $request->get('subcomment_id');
        $commentBody = $request->get('comment');
        if(!empty($blogId) && !empty($commentId) && !empty($commentBody)){
            $subcomment = BlogSubComment::where('blog_id', $blogId)->where('blog_comment_id', $commentId)->where('id', $subcommentId)->first();
            if(is_object($subcomment)){
                DB::beginTransaction();
                try
                {
                    $subcomment->body = $commentBody;
                    $subcomment->save();
                    DB::commit();
                    // Session::put('blog_comment_area', $commentId);
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                }
            }
        }
        return $this->getComments($blogId);
    }

    protected function deleteBlogSubComment(Request $request){
        $blogId = $request->get('blog_id');
        $commentId = $request->get('comment_id');
        $subcommentId = $request->get('subcomment_id');
        if(!empty($blogId) && !empty($commentId) && !empty($subcommentId)){
            $subcomment = BlogSubComment::where('blog_id', $blogId)->where('blog_comment_id', $commentId)->where('id', $subcommentId)->first();
            if(is_object($subcomment)){
                DB::beginTransaction();
                try
                {   if(is_object($subcomment->deleteLikes) && false == $subcomment->deleteLikes->isEmpty()){
                        foreach($subcomment->deleteLikes as $deleteLike){
                            $deleteLike->delete();
                        }
                    }
                    // Session::put('blog_comment_area', $commentId);
                    $subcomment->delete();
                    DB::commit();
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                }
            }
        }
        return $this->getComments($blogId);
    }

    protected function tagBlogs($id){
        $blogIds = [];
        if(!empty(json_decode($id))){
            $blogTag = BlogTag::find(json_decode($id));
            if(is_object($blogTag)){
                $blogTags = BlogTag::where('name', $blogTag->name)->get();
                if(is_object($blogTags) && false == $blogTags->isEmpty()){
                    foreach($blogTags as $blogTag){
                        $blogIds[] = $blogTag->blog_id;
                    }
                    if(count($blogIds) > 0){
                        $blogs = Blog::whereIn('id', $blogIds)->orderBy('id', 'desc')->get();
                        return view('blog.tagBlogs', compact('blogs'));
                    }
                }
            }
        }
        return Redirect::to('blog');
    }

       /**
     *  return child comments
     */
    protected function getComments($blogId){
        $comments = BlogComment::where('blog_id', $blogId)->orderBy('id', 'desc')->get();
        $blogComments = [];
        foreach($comments as $comment){
            $blogComments['comments'][$comment->id]['body'] = $comment->body;
            $blogComments['comments'][$comment->id]['id'] = $comment->id;
            $blogComments['comments'][$comment->id]['blog_id'] = $comment->blog_id;
            $blogComments['comments'][$comment->id]['user_id'] = $comment->user_id;
            $blogComments['comments'][$comment->id]['user_name'] = $comment->user->name;
            $blogComments['comments'][$comment->id]['updated_at'] = $comment->updated_at->diffForHumans();
            $blogComments['comments'][$comment->id]['user_image'] = $comment->user->photo;
            if(is_object($comment->children) && false == $comment->children->isEmpty()){
                $blogComments['comments'][$comment->id]['subcomments'] = $this->getSubComments($comment->children);
            }
        }
        $blogComments['commentLikesCount'] = BlogCommentLike::getLikesByBlogId($blogId);
        $blogComments['subcommentLikesCount'] = BlogSubCommentLike::getLikesByBlogId($blogId);

        return $blogComments;
    }

    /**
     *  return child comments
     */
    protected function getSubComments($subComments){

        $blogChildComments = [];
        foreach($subComments as $subComment){
            $blogChildComments[$subComment->id]['body'] = $subComment->body;
            $blogChildComments[$subComment->id]['id'] = $subComment->id;
            $blogChildComments[$subComment->id]['blog_id'] = $subComment->blog_id;
            $blogChildComments[$subComment->id]['blog_comment_id'] = $subComment->blog_comment_id;
            $blogChildComments[$subComment->id]['user_name'] = $subComment->user->name;
            $blogChildComments[$subComment->id]['user_id'] = $subComment->user_id;
            $blogChildComments[$subComment->id]['updated_at'] = $subComment->updated_at->diffForHumans();
            $blogChildComments[$subComment->id]['user_image'] = $subComment->user->photo;
            if(is_object($subComment->children) && false == $subComment->children->isEmpty()){
                $blogChildComments[$subComment->id]['subcomments'] = $this->getSubComments($subComment->children);
            }
        }

        return $blogChildComments;
    }
}