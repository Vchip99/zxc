<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\User;
use Auth,Cache;
use App\Models\CourseSubCommentLike;

class CourseSubComment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['course_video_id', 'course_comment_id', 'user_id', 'parent_id', 'body'];

    /**
     *  create child comment with assocaited blogId
     */
	protected static function createSubComment(Request $request){
		$parentSubComment = new static;
    	$videoId = $request->get('video_id');
    	$userComment = $request->get('subcomment');
    	$commentId = $request->get('comment_id');
    	$subcommentId = $request->get('subcomment_id');
        $loginUser = Auth::user();

    	$subcomment = new static;
        if($subcommentId > 0){
        	$parentSubComment = static::find($subcommentId);
    	}

        if( is_object($parentSubComment) && $parentSubComment->user_id !== $loginUser->id ){
            $subcomment->body = $userComment;
            $user = User::find($parentSubComment->user_id);
            if(is_object($user)){
                $changedName = '<b>'.$user->name.'</b>';
                $subcomment->body = str_replace($user->name, $changedName, $userComment);
            }
        } else {
            $subcomment->body = $userComment;
        }
    	$subcomment->course_video_id = $videoId;
    	$subcomment->course_comment_id = $commentId;
    	$subcomment->parent_id = $subcommentId?:0;
    	$subcomment->user_id = $loginUser->id;
    	$subcomment->save();
    	return $subcomment;
    }

    public function deleteLikes(){
        return $this->hasMany(CourseSubCommentLike::class);
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getUser($userId){
        return Cache::remember('vchip:user-'.$userId,30, function() use($userId){
            return User::find($userId);
        });
    }

    protected static function deleteCourseSubCommentsByUserId($userId){
        $subcomments = static::where('user_id', $userId)->get();
        if(is_object($subcomments) && false == $subcomments->isEmpty()){
            foreach($subcomments as $subcomment){
                $subcomment->delete();
            }
        }
    }
}
