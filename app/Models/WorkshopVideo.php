<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB;
use App\Libraries\InputSanitise;
use App\Models\WorkshopCategory;
use App\Models\WorkshopDetail;

class WorkshopVideo extends Model
{
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'workshop_category_id','workshop_details_id', 'description', 'duration', 'video_path', 'date'];

    /**
     *  create/update WorkshopVideo
     */
    protected static function addOrUpdateWorkshopVideo(Request $request, $isUpdate = false){
    	$categoryId = InputSanitise::inputInt($request->get('category'));
        $workshopId = InputSanitise::inputInt($request->get('workshop'));
        $name = InputSanitise::inputString($request->get('name'));
        $description = InputSanitise::inputString($request->get('description'));
        $duration = InputSanitise::inputString($request->get('duration'));
        $videoPath = $request->get('video_path');
        $date = $request->get('date');
        $videoId = InputSanitise::inputInt($request->get('video_id'));

    	if( $isUpdate && !empty($videoId)){
    		$workshopVideo = static::find($videoId);
    		if(!is_object($workshopVideo)){
    			return Redirect::to('admin/manageWorkshopVideos');
    		}
    	} else {
    		$workshopVideo = new static;
    	}
    	$workshopVideo->name = $name;
    	$workshopVideo->workshop_category_id = $categoryId;
    	$workshopVideo->workshop_details_id = $workshopId;
    	$workshopVideo->description = $description;
    	$workshopVideo->duration = $duration;
        $workshopVideo->video_path = $videoPath;
        $workshopVideo->date = $date;
    	$workshopVideo->save();
    	return $workshopVideo;
    }

    public function workshop(){
    	return $this->belongsTo(WorkshopDetail::class, 'workshop_details_id');
    }

    public function category(){
    	return $this->belongsTo(WorkshopCategory::class, 'workshop_category_id');
    }

    public function getVideoTimeSpan(){
    	date_default_timezone_set('Asia/Calcutta');
    	$currentTime = date("Y-m-d H:i:s");
		$videoTime = date("Y-m-d H:i:s", (strtotime(date($this->date)) + $this->duration));
		if($currentTime >= $videoTime){
			return 'true';
		}
		return 'false';
    }
}
