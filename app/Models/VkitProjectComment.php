<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\VkitProjectSubComment;
use App\Libraries\InputSanitise;
use App\Models\VkitProjectCommentLike;
use Cache;

class VkitProjectComment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['vkit_project_id', 'user_id', 'body'];

    /**
     *  create comment with assocaited vkit project Id
     */
    protected static function createComment(Request $request){
    	$projectId = InputSanitise::inputInt($request->get('project_id'));
    	$userComment = $request->get('comment');

    	$comment = new static;
    	$comment->body = $userComment;
    	$comment->vkit_project_id = $projectId;
    	$comment->user_id = \Auth::user()->id;
    	$comment->save();
    	return $comment;
    }

    public function children(){
    	return $this->hasMany(VkitProjectSubComment::class);
    }

    public function deleteLikes(){
        return $this->hasMany(VkitProjectCommentLike::class);
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getUser($userId){
        return Cache::remember('vchip:user-'.$userId,30, function() use($userId){
            return User::find($userId);
        });
    }

    protected static function deleteVkitProjectCommentsByUserId($userId){
        $comments = static::where('user_id', $userId)->get();
        if(is_object($comments) && false == $comments->isEmpty()){
            foreach($comments as $comment){
                $comment->delete();
            }
        }
    }
}
