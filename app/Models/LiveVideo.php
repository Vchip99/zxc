<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use App\Models\LiveCourse;
use App\Models\LiveCourseComment;
use App\Models\LiveCourseVideoLike;

class LiveVideo extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'description', 'duration', 'live_course_id', 'start_date'];

    /**
     *  create/update live video
     */
    protected static function addOrUpdateLiveVideo($request, $isUpdate = false){
    	$videoName = InputSanitise::inputString($request->get('video'));
    	$description = InputSanitise::inputString($request->get('description'));
    	$duration = strip_tags(trim($request->get('duration')));
    	$course = InputSanitise::inputInt($request->get('course'));
    	$videoPath = trim($request->get('video_path'));
    	$startDate = trim($request->get('start_date'));
    	$videoId = InputSanitise::inputInt($request->get('live_video_id'));

    	if( $isUpdate && isset($videoId)){
    		$video = LiveVideo::find($videoId);
    		if(!is_object($video)){
    			return 'false';
    		}
    	} else {
    		$video = new LiveVideo;
    	}

    	$video->name = $videoName;
    	$video->description = $description;
    	$video->duration = $duration;
    	$video->video_path = $videoPath;
    	$video->live_course_id = $course;
    	$video->start_date = $startDate;
    	$video->save();
    	return $video;
    }

    /**
     *  return live videos by live course Id
     */
    protected static function getLiveVideosByLiveCourseId($liveCourseId){
        $liveCourseId = InputSanitise::inputInt($liveCourseId);
        return LiveVideo::where('live_course_id', $liveCourseId)->get();
    }

    /**
     *  get category of sub category
     */
    public function course(){
        return $this->belongsTo(LiveCourse::class, 'live_course_id');
    }

    public function comments(){
        return $this->hasMany(LiveCourseComment::class, 'live_course_video_id');
    }

    public function deleteLikes(){
        return $this->hasMany(LiveCourseVideoLike::class, 'live_course_video_id');
    }

    public function deleteCommantsAndSubComments(){
        if(is_object($this->comments) && false == $this->comments->isEmpty()){
            foreach($this->comments as $comment){
                if(is_object($comment->children) && false == $comment->children->isEmpty()){
                    foreach($comment->children as $subcomment){
                        if(is_object($subcomment->deleteLikes) && false == $subcomment->deleteLikes->isEmpty()){
                            foreach($subcomment->deleteLikes as $subcommentLike){
                                $subcommentLike->delete();
                            }
                        }
                        $subcomment->delete();
                    }
                }
                if(is_object($comment->deleteLikes) && false == $comment->deleteLikes->isEmpty()){
                    foreach($comment->deleteLikes as $commentLike){
                        $commentLike->delete();
                    }
                }
                $comment->delete();
            }
        }
        if(is_object($this->deleteLikes) && false == $this->deleteLikes->isEmpty()){
            foreach($this->deleteLikes as $videoLike){
                $videoLike->delete();
            }
        }
    }

    protected static function isLiveCourseVideoExist($request){
        $courseId = InputSanitise::inputInt($request->get('course'));
        $videoName = InputSanitise::inputString($request->get('video'));
        $videoId = InputSanitise::inputInt($request->get('live_video_id'));
        $result = static::where('live_course_id', $courseId)->where('name', $videoName);
        if(!empty($videoId)){
            $result->where('id', '!=', $videoId);
        }
        $result->first();
        if(is_object($result) && 1 == $result->count()){
            return 'true';
        } else {
            return 'false';
        }
        return 'false';
    }
}
