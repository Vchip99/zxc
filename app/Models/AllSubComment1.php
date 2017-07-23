<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\AllPost;
use App\Models\User;

class AllSubComment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['all_post_id', 'all_comment_id', 'user_id', 'parent_id', 'body'];

    /**
     *  create comment with post module
     */
    protected static function createSubComment(Request $request){
    	$postId = $request->get('all_post_id');
    	$commentId = $request->get('all_comment_id');
    	$userComment = $request->get('subcomment');

    	$subcomment = new static;
    	$subcomment->body = $userComment;
    	$subcomment->all_post_id = $postId;
    	$subcomment->all_comment_id = $commentId;
    	$subcomment->parent_id = 0;
    	$subcomment->user_id = \Auth::user()->id;
    	$subcomment->save();
    	return $subcomment;
    }


}
