<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use App\Models\User;
use Auth;
use App\Models\VkitProjectSubCommentLike;


class VkitProjectSubComment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['vkit_project_id', 'vkit_project_comment_id', 'parent_id', 'user_id', 'body'];

     /**
     *  create comment with assocaited vkit project Id
     */
    protected static function createSubComment(Request $request){
    	$parentSubComment = new static;
    	$projectId = InputSanitise::inputInt($request->get('project_id'));
    	$commentId = InputSanitise::inputInt($request->get('comment_id'));
    	$subcommentId = InputSanitise::inputInt($request->get('subcomment_id'));
    	$userComment = $request->get('subcomment');

    	$subcomment = new static;
    	if($subcommentId > 0){
        	$parentSubComment = static::find($subcommentId);
    	}

        if( is_object($parentSubComment) && $parentSubComment->user_id !== Auth::user()->id ){
            $subcomment->body = $userComment;
            $user = User::find($parentSubComment->user_id);
            if(is_object($user)){
                $changedName = '<b>'.$user->name.'</b>';
                $subcomment->body = str_replace($user->name, $changedName, $userComment);
            }
        } else {
            $subcomment->body = $userComment;
        }
    	$subcomment->vkit_project_id = $projectId;
    	$subcomment->vkit_project_comment_id = $commentId;
    	$subcomment->parent_id = $subcommentId?:0;
    	$subcomment->user_id = Auth::user()->id;
    	$subcomment->save();
    	return $subcomment;
    }

    public function deleteLikes(){
    	return $this->hasMany(VkitProjectSubCommentLike::class);
    }

    protected static function deleteVkitProjectSubCommentsByUserId($userId){
        $subcomments = static::where('user_id', $userId)->get();
        if(is_object($subcomments) && false == $subcomments->isEmpty()){
            foreach($subcomments as $subcomment){
                $subcomment->delete();
            }
        }
    }
}
