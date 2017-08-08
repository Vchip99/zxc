<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB, Auth;
use App\Libraries\InputSanitise;
use App\Models\ClientOnlineCourse;
use App\Models\ClientCourseComment;
use App\Models\ClientOnlineVideoLike;
use App\Models\ClientInstituteCourse;

class ClientOnlineVideo extends Model
{
    protected $connection = 'mysql2';

    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'description', 'duration', 'video_path','course_id', 'client_id', 'client_institute_course_id'];

    /**
     *  create/update video
     */
    protected static function addOrUpdateVideo(Request $request, $isUpdate = false){
        $instituteCourseId   = InputSanitise::inputInt($request->get('institute_course'));
    	$videoName = InputSanitise::inputString($request->get('video'));
    	$description = InputSanitise::inputString($request->get('description'));
    	$duration = InputSanitise::inputInt($request->get('duration'));
    	$course = InputSanitise::inputInt($request->get('course'));
    	$videoPath = trim($request->get('video_path'));
    	$videoId = InputSanitise::inputInt($request->get('video_id'));

    	if( $isUpdate && isset($videoId)){
    		$video = static::find($videoId);
    		if(!is_object($video)){
    			return Redirect::to('admin/manageCourseVideo');
    		}
    	} else {
    		$video = new static;
    	}

    	$video->name = $videoName;
    	$video->description = $description;
    	$video->duration = $duration;
    	$video->video_path = $videoPath;
    	$video->course_id = $course;
    	$video->client_id = Auth::guard('client')->user()->id;
        $video->client_institute_course_id = $instituteCourseId;
    	$video->save();
    	return $video;
    }


    /**
     *  get course of video
     */
    public function course(){
        return $this->belongsTo(ClientOnlineCourse::class, 'course_id');
    }

    public function instituteCourse(){
        return $this->belongsTo(ClientInstituteCourse::class, 'client_institute_course_id');
    }

    public static function getClientCourseVideosByCourseId($courseId, Request $request){
        $courseId = InputSanitise::inputString($courseId);
        if(is_object(Auth::guard('client')->user())){
            $clientId = Auth::guard('client')->user()->id;
        } else{
            $client = InputSanitise::getCurrentClient($request);
        }
        $result = DB::connection('mysql2')->table('client_online_videos');
            if( $courseId > 2){
                $result->join('clients', function($join){
                    $join->on('clients.id', '=', 'client_online_videos.client_id');
                });
                if(!empty($clientId)){
                    $result->where('clients.id', $clientId);
                } else {
                    $result->where('clients.subdomain', $client);
                }
            }
            return  $result->where('client_online_videos.course_id', $courseId)
                ->select('client_online_videos.*')
                ->get();
    }

    protected static function showVideos(Request $request){
        if(is_object(Auth::guard('client')->user())){
            $clientId = Auth::guard('client')->user()->id;
        } else{
            $client = InputSanitise::getCurrentClient($request);
        }

        $result = static::join('clients', 'clients.id', '=', 'client_online_videos.client_id')->with('course');
        if(!empty($clientId)){
            $result->where('clients.id', $clientId);
        } else {
            $result->where('clients.subdomain', $client);
        }
        return  $result->select('client_online_videos.*')->get();
    }

    protected static function getCoursevideoCount($courseIds){
        $returnCourseIds = [];
        $results = static::whereIn('course_id', $courseIds)->get();

        if(false == $results->isEmpty()){
            foreach($results as $result){
                $returnCourseIds[$result->course_id][] = $result->id;
            }
        }
        return $returnCourseIds;
    }

    public function comments(){
        return $this->hasMany(ClientCourseComment::class);
    }

    public function deleteLikes(){
        return $this->hasMany(ClientOnlineVideoLike::class);
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

    protected static function deleteClientOnlineVideosByClientId($clientId){
        $videos = static::where('client_id', $clientId)->get();
        if(is_object($videos) && false == $videos->isEmpty()){
            foreach($videos as $video){
                $video->delete();
            }
        }
    }

}
