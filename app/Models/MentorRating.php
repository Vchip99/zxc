<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB,Cache,Auth;
use App\Libraries\InputSanitise;

class MentorRating extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['mentor_id','rating','review','user_id'];


    /**
     *  create/update
     */
    protected static function addOrUpdateMentorRating(Request $request){
        $mentorId = $request->get('mentor_id');
        $rating = (empty($request->get('input-'.$mentorId)))?1:$request->get('input-'.$mentorId);
        $reviewText = $request->get('review-text');
        $ratingId = $request->get('rating_id');

    	if($ratingId > 0){
    		$reviewRating = static::find($ratingId);
    		if(!is_object($reviewRating)){
    			return 'false';
    		}
    	} else {
    		$reviewRating = new static;
    	}
    	$reviewRating->mentor_id = $mentorId;
    	$reviewRating->rating = ceil($rating);
    	$reviewRating->review = $reviewText;
    	$reviewRating->user_id = Auth::user()->id;
    	$reviewRating->save();
    	return $reviewRating;
    }
}
