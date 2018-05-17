<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PlacementProcessSubComment;
use App\Libraries\InputSanitise;
use App\Models\PlacementProcessCommentLike;
use Auth, Cache;

class PlacementProcessComment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['company_id', 'user_id', 'body'];

    /**
     *  create comment with assocaited company id
     */
    protected static function createPlacementProcessComment(Request $request){
    	$companyId = InputSanitise::inputInt($request->get('company_id'));
    	$commentId = InputSanitise::inputInt($request->get('comment_id'));
    	$userComment = $request->get('comment');
    	if(!empty($commentId) && $commentId > 0 ){
    		$comment = static::find($commentId);
    		if(!is_object($comment)){
    			return Redirect::to('placements');
    		}
    	} else {
    		$comment = new static;
    	}
    	$comment->body = $userComment;
    	$comment->company_id = $companyId;
    	$comment->user_id = Auth::user()->id;
    	$comment->save();
    	return $comment;
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getUser($userId){
        return Cache::remember('vchip:user-'.$userId,30, function() use($userId){
            return User::find($userId);
        });
    }

    public function children(){
    	return $this->hasMany(PlacementProcessSubComment::class);
    }

    public function deleteLikes(){
        return $this->hasMany(PlacementProcessCommentLike::class);
    }

    protected static function deletePlacementProcessCommentsByUserId($userId){
        $comments = static::where('user_id', $userId)->get();
        if(is_object($comments) && false == $comments->isEmpty()){
            foreach($comments as $comment){
                $comment->delete();
            }
        }
        return;
    }

    protected static function deletePlacementProcessCommentsByCompanyId($companyId){
        $comments = static::where('company_id', $companyId)->get();
        if(is_object($comments) && false == $comments->isEmpty()){
            foreach($comments as $comment){
                $comment->delete();
            }
        }
        return;
    }
}
