<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\CourseSubComment;
use App\Libraries\InputSanitise;
use App\Models\CourseCommentLike;
use Auth;

class CourseComment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['course_video_id', 'user_id', 'body'];

    /**
     *  create comment with assocaited vkit project Id
     */
    protected static function createComment(Request $request){
    	$videoId = InputSanitise::inputInt($request->get('video_id'));
    	$userComment = $request->get('comment');
    	$comment = new static;
    	$comment->body = $userComment;
    	$comment->course_video_id = $videoId;
    	$comment->user_id = Auth::user()->id;
    	$comment->save();
    	return $comment;
    }

    public function children(){
    	return $this->hasMany(CourseSubComment::class)->orderby('id', 'desc');
    }

    public function deleteLikes(){
        return $this->hasMany(CourseCommentLike::class);
    }
}
