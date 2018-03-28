<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\Clientuser;
use App\Libraries\InputSanitise;
use App\Models\ClientCourseSubCommentLike;
use Auth,Cache;

class ClientCourseSubComment extends Model
{
    protected $connection = 'mysql2';
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['client_online_video_id', 'client_course_comment_id', 'parent_id', 'user_id', 'client_id', 'body'];

    protected static function createSubComment(Request $request){
		$parentSubComment = new static;
    	$videoId = $request->get('video_id');
    	$userComment = $request->get('subcomment');
    	$commentId = $request->get('comment_id');
    	$subcommentId = $request->get('subcomment_id');

    	$subcomment = new static;
        if($subcommentId > 0){
        	$parentSubComment = static::find($subcommentId);
    	}

        if( is_object($parentSubComment) && $parentSubComment->user_id !== Auth::guard('clientuser')->user()->id ){
            $subcomment->body = $userComment;
            $user = Clientuser::find($parentSubComment->user_id);
            if(is_object($user)){
                $changedName = '<b>'.$user->name.'</b>';
                $subcomment->body = str_replace($user->name, $changedName, $userComment);
            }
        } else {
            $subcomment->body = $userComment;
        }
    	$subcomment->client_online_video_id = $videoId;
    	$subcomment->client_course_comment_id = $commentId;
    	$subcomment->parent_id = $subcommentId?:0;
    	$subcomment->user_id = Auth::guard('clientuser')->user()->id;
    	$subcomment->client_id = Auth::guard('clientuser')->user()->client_id;
    	$subcomment->save();
    	return $subcomment;
    }

    public function deleteLikes(){
        return $this->hasMany(ClientCourseSubCommentLike::class);
    }

    public function user(){
        return $this->belongsTo(Clientuser::class, 'user_id');
    }

    public function getClientUser($subdomainName,$userId){
        return Cache::remember($subdomainName.':user-'.$userId,30, function() use($userId){
            return Clientuser::find($userId);
        });
    }

    protected static function deleteClientCourseSubCommentsByUserId($clientId){
        $subcomments = static::where('client_id', $clientId)->get();
        if(is_object($subcomments) && false == $subcomments->isEmpty()){
            foreach($subcomments as $subcomment){
                $subcomment->delete();
            }
        }
    }
}
