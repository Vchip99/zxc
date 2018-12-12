<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB,Cache,Auth;
use App\Libraries\InputSanitise;

class Rating extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['module_id','rating','review','user_id','module_type'];

    const Course = 1;
    const SubCategory = 2;
    const Vkit = 3;
    const StudyMaterial = 4;
    const MockInterview = 5;

    /**
     *  create/update
     */
    protected static function addOrUpdateRating(Request $request){
        $moduleType = $request->get('module_type');
        $moduleId = $request->get('module_id');
        $rating = (empty($request->get('input-'.$moduleId)))?1:$request->get('input-'.$moduleId);
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
    	$reviewRating->module_id = $moduleId;
    	$reviewRating->rating = ceil($rating);
    	$reviewRating->review = $reviewText;
    	$reviewRating->user_id = Auth::user()->id;
    	$reviewRating->module_type = $moduleType;
    	$reviewRating->save();
    	return $reviewRating;
    }

    protected static function getRatingsByModuleIdByModuleType($moduleId,$moduleType){
        return static::where('module_id',$moduleId)->where('module_type',$moduleType)->get();
    }

    protected static function getRatingsByModuleType($moduleType){
        return static::where('module_type',$moduleType)->get();
    }
}
