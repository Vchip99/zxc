<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Auth;

class PlacementProcessSubCommentLike extends Model
{
    public $timestamps = false;
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['company_id', 'placement_process_comment_id', 'placement_process_sub_comment_id', 'user_id'];

     protected static function getLikePlacementProcessSubComment(Request $request){
        if(is_object(Auth::user())){
            if( 1 == $request->get('dis_like')){
                $likePlacementProcessComment = static::where('company_id',$request->get('company_id'))->where('user_id' ,Auth::user()->id)->where('placement_process_comment_id', $request->get('comment_id'))->where('placement_process_sub_comment_id', $request->get('sub_comment_id'))->first();
                if(is_object($likePlacementProcessComment)){
                    $likePlacementProcessComment->delete();
                    return self::getLikeStatus($request);
                }
            } else {
                static::create(['company_id' => $request->get('company_id'), 'user_id' => Auth::user()->id, 'placement_process_comment_id' => $request->get('comment_id'),'placement_process_sub_comment_id' => $request->get('sub_comment_id')]);
                return self::getLikeStatus($request);
            }
        }
        return 'false';
    }

    protected static function getLikeStatus($request){
    	return static::where('company_id',$request->get('company_id'))->where('placement_process_comment_id', $request->get('comment_id'))->where('placement_process_sub_comment_id', $request->get('sub_comment_id'))->get();
    }

    protected static function getLikesByCompanyId($id){
        $commentLikesCount = [];

        if($id > 0){
            $likes = static::where('company_id', $id)->get();
            if( false == $likes->isEmpty() ){
                foreach($likes as $like){
                    $commentLikesCount[$like->placement_process_sub_comment_id]['user_id'][$like->user_id] = $like->user_id;
                    $commentLikesCount[$like->placement_process_sub_comment_id]['like_id'][$like->id] = $like->id;
                }
            }
        }
        return $commentLikesCount;
    }

    protected static function deletePlacementProcessSubCommentLikesByUserId($userId){
        $subcommentLikes = static::where('user_id', $userId)->get();
        if(is_object($subcommentLikes) && false == $subcommentLikes->isEmpty()){
            foreach($subcommentLikes as $subcommentLike){
                $subcommentLike->delete();
            }
        }
        return;
    }

    protected static function deletePlacementProcessSubCommentLikesByCompanyId($companyId){
        $subcommentLikes = static::where('company_id', $companyId)->get();
        if(is_object($subcommentLikes) && false == $subcommentLikes->isEmpty()){
            foreach($subcommentLikes as $subcommentLike){
                $subcommentLike->delete();
            }
        }
        return;
    }

}
