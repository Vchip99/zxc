<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB,Cache,Auth;
use App\Libraries\InputSanitise;

class MockInterviewReview extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_data_id', 'rating','review', 'user_id'];

    /**
     *  create/update
     */
    protected static function addOrUpdateMockInterviewReview(Request $request){
        $userDataId = $request->get('user_data_id');
        $rating = (empty($request->get('input-'.$userDataId)))?1:$request->get('input-'.$userDataId);
        $reviewText = $request->get('review-text');
        $reviewId = $request->get('review_id');

    	if($reviewId > 0){
    		$mockInterviewReview = static::find($reviewId);
    		if(!is_object($mockInterviewReview)){
    			return 'false';
    		}
    	} else {
    		$mockInterviewReview = new static;
    	}
    	$mockInterviewReview->user_data_id = $userDataId;
    	$mockInterviewReview->rating = ceil($rating);
    	$mockInterviewReview->review = $reviewText;
    	$mockInterviewReview->user_id = Auth::user()->id;
    	$mockInterviewReview->save();
    	return $mockInterviewReview;
    }
}
