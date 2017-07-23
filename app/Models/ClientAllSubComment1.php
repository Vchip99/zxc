<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\ClientAllPost;
use App\Models\Clientuser;
use Auth;

class ClientAllSubComment extends Model
{
    protected $connection = 'mysql2';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['client_all_post_id', 'client_all_comment_id','user_id', 'client_id', 'parent_id', 'body'];

    /**
     *  create comment with post module
     */
    protected static function createSubComment(Request $request){
    	$postId = $request->get('all_post_id');
    	$commentd = $request->get('all_comment_id');
    	$userComment = $request->get('subcomment');
    	$parentId = $request->get('parent_id');

    	$subcomment = new static;
    	$subcomment->body = $userComment;
    	$subcomment->client_all_post_id = $postId;
    	$subcomment->client_all_comment_id = $commentd;
    	$subcomment->parent_id = $parentId?:0;
    	$subcomment->user_id = Auth::guard('clientuser')->user()->id;
    	$subcomment->client_id = Auth::guard('clientuser')->user()->client_id;
    	$subcomment->save();
    	return $subcomment;
    }
}
