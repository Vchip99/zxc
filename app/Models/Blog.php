<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use DB,Cache;
use App\Models\BlogComment;
use App\Models\BlogCommentLike;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use App\Models\User;

class Blog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'author', 'blog_category_id','content'];

    /**
     *  create/update blog
     */
    protected static function addOrUpdateBlog( Request $request, $isUpdate=false){

        $blogTitle = InputSanitise::inputString($request->get('title'));
        $blogAuthor = InputSanitise::inputString($request->get('author'));
        $blogCategoryId = InputSanitise::inputInt($request->get('category_id'));
        $blogContent = $request->get('content');

        $blogId = InputSanitise::inputInt($request->get('blog_id'));
        if( $isUpdate && isset($blogId)){
            $blog = Blog::find($blogId);
            if(!is_object($blog)){
            	return Redirect::to('admin/manageBlog');
            }
        } else{
            $blog = new Blog;
        }
        $blog->title = $blogTitle;
        $blog->author = $blogAuthor;
        $blog->content = $blogContent;
        $blog->blog_category_id = $blogCategoryId;
        $blog->save();
        return $blog;

    }

    /**
     *  return blogs by categoryId
     */
    protected static function getBlogsByCategory($categoryId, $page = NULL){
        $categoryId = InputSanitise::inputInt($categoryId);
        $limit = 5;
        if(!empty($page)){
            $skip = $limit * ($page -1);
            return Blog::where('blog_category_id', $categoryId)->skip($skip)->orderBy('id', 'desc')->paginate($limit);
        } else {
            return Blog::where('blog_category_id', $categoryId)->orderBy('id', 'desc')->paginate($limit);
        }
    }

    public function comments(){
        return $this->hasMany(BlogComment::class, 'blog_id');
    }

    public function commentLikes(){
        return $this->hasMany(BlogCommentLike::class, 'blog_id');
    }

    public function category(){
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public function deleteLikes(){
        return $this->hasMany(BlogLikes::class, 'blog_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getUser($userId){
        return Cache::remember('vchip:user-'.$userId,30, function() use($userId){
            return User::find($userId);
        });
    }

    public function deleteCommantsAndSubComments(){
        if(is_object($this->comments) && false == $this->comments->isEmpty()){
            foreach($this->comments as $comment){
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
            }
        }
        if(is_object($this->deleteLikes) && false == $this->deleteLikes->isEmpty()){
            foreach($this->deleteLikes as $blogLike){
                $blogLike->delete();
            }
        }
    }

    public function deleteBlogTags(){
        $blogTags = BlogTag::where('blog_id', $this->id)->get();
        if(is_object($blogTags) && false == $blogTags->isEmpty()){
            foreach($blogTags as $blogTag){
                $blogTag->delete();
            }
        }
    }

    protected static function isBlogExist(Request $request){
        $category = InputSanitise::inputInt($request->get('category'));
        $blog = InputSanitise::inputString($request->get('blog'));
        $blogId   = InputSanitise::inputInt($request->get('blog_id'));
        $result = static::where('blog_category_id', $category)->where('title', '=',$blog);
        if(!empty($blogId)){
            $result->where('id', '!=', $blogId);
        }
        $result->first();
        if(is_object($result) && 1 == $result->count()){
            return 'true';
        } else {
            return 'false';
        }
    }
}

