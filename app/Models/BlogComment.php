<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\User;
use App\Models\BlogSubComment;
use App\Models\BlogCommentLike;

class BlogComment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['blog_id', 'user_id', 'body'];

    /**
     *  create comment with assocaited blogId
     */
    protected static function createComment(Request $request){
    	$blogId = $request->get('blog_id');
    	$userComment = $request->get('comment');

    	$comment = new static;
    	$comment->body = $userComment;
    	$comment->blog_id = $blogId;
    	$comment->user_id = \Auth::user()->id;
    	$comment->save();
    	return $comment;
    }

    /**
     *  all associated blogs
     */
    public function blog()
    {
        return $this->belongsTo(Blog::class);
    }

    /**
     *  blog associated children
     */
    public function children()
    {
        return $this->hasMany(BlogSubComment::class);
    }

    /**
     *  blog associated user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function deleteLikes(){
        return $this->hasMany(BlogCommentLike::class);
    }

    protected static function deleteBlogCommentsByUserId($userId){
        $comments = static::where('user_id', $userId)->get();
        if(is_object($comments) && false == $comments->isEmpty()){
            foreach($comments as $comment){
                $comment->delete();
            }
        }
    }
}
