<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\LiveCourseSubComment;
use App\Libraries\InputSanitise;
use App\Models\LiveCourseCommentLike;

class LiveCourseComment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['live_course_video_id', 'user_id', 'body'];

    /**
     *  create comment with assocaited vkit project Id
     */
    protected static function createComment(Request $request){
    	$videoId = InputSanitise::inputInt($request->get('video_id'));
    	$userComment = $request->get('comment');

    	$comment = new static;
    	$comment->body = $userComment;
    	$comment->live_course_video_id = $videoId;
    	$comment->user_id = \Auth::user()->id;
    	$comment->save();
    	return $comment;
    }

    public function children(){
    	return $this->hasMany(LiveCourseSubComment::class)->orderby('id', 'desc');
    }

    public function deleteLikes(){
        return $this->hasMany(LiveCourseCommentLike::class);
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    protected static function deleteLiveCourseCommentsByUserId($userId){
        $comments = static::where('user_id', $userId)->get();
        if(is_object($comments) && false == $comments->isEmpty()){
            foreach($comments as $comment){
                $comment->delete();
            }
        }
    }
}
