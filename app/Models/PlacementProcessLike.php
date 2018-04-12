<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Auth;


class PlacementProcessLike extends Model
{
    public $timestamps = false;
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['company_id', 'user_id'];

    protected static function getLikePlacementProcess(Request $request){
        $loginUser = Auth::user();
        if(is_object($loginUser)){
            if( 1 == $request->get('dis_like')){
                $likePost = static::where('company_id',$request->get('company_id'))->where('user_id' ,$loginUser->id)->first();
                if(is_object($likePost)){
                    $likePost->delete();
                    return self::getLikeStatus($request);
                }
            } else {
                static::create(['company_id' => $request->get('company_id'), 'user_id' => $loginUser->id]);
                return self::getLikeStatus($request);
            }
        }
        return 'false';
    }

    protected static function getLikesByCompanyId($id){
    	$likesCount = [];
        $likes = static::where('company_id', $id)->get();

        if( false == $likes->isEmpty() ){
            foreach($likes as $like){
                $likesCount[$like->company_id]['user_id'][$like->user_id] = $like->user_id;
                $likesCount[$like->company_id]['like_id'][$like->id] = $like->id;
            }
        }
        return $likesCount;
    }

    protected static function getLikeStatus(Request $request){
        return static::where('company_id',$request->get('company_id'))->get();
    }

    protected static function deletePlacementProcessLikesByUserId($userId){
        $placementProcessLikes = static::where('user_id', $userId)->get();
        if(is_object($placementProcessLikes) && false == $placementProcessLikes->isEmpty()){
            foreach($placementProcessLikes as $placementProcessLike){
                $placementProcessLike->delete();
            }
        }
        return;
    }

    protected static function deletePlacementProcessLikesByCompanyId($companyId){
        $placementProcessLikes = static::where('company_id', $companyId)->get();
        if(is_object($placementProcessLikes) && false == $placementProcessLikes->isEmpty()){
            foreach($placementProcessLikes as $placementProcessLike){
                $placementProcessLike->delete();
            }
        }
        return;
    }
}
