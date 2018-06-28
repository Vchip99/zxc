<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\CourseCourse;
use App\Libraries\InputSanitise;
use App\Models\CourseComment;
use App\Models\CourseVideoLike;
use App\Models\CourseSubCategory;
use App\Models\CourseCategory;
use File;

class CourseVideo extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'description', 'duration', 'video_path','course_id', 'course_category_id', 'course_sub_category_id'];

    /**
     *  create/update video
     */
    protected static function addOrUpdateVideo($request, $isUpdate = false){
    	$videoName = InputSanitise::inputString($request->get('video'));
    	$description = InputSanitise::inputString($request->get('description'));
    	$duration = InputSanitise::inputInt($request->get('duration'));
    	$course = InputSanitise::inputInt($request->get('course'));
        $categoryId = InputSanitise::inputInt($request->get('category'));
        $subcategoryId = InputSanitise::inputInt($request->get('subcategory'));
    	$videoPath = trim($request->get('video_path'));
    	$videoId = InputSanitise::inputInt($request->get('video_id'));
        $videoSource = InputSanitise::inputString($request->get('video_source'));

    	if( $isUpdate && isset($videoId)){
    		$video = static::find($videoId);
    		if(!is_object($video)){
    			return 'false';
    		}
    	} else {
    		$video = new static;
    	}

    	$video->name = $videoName;
    	$video->description = $description;
    	$video->duration = $duration;
        if('youtube' == $videoSource){
    	   $video->video_path = $videoPath;
        } else if(empty($video->id)){
            $video->video_path = '';
        }
    	$video->course_id = $course;
        $video->course_category_id = $categoryId;
        $video->course_sub_category_id = $subcategoryId;
    	$video->save();
        if('system' == $videoSource && is_object($request->file('video_path')) && !empty($video->id)){
            $originalVideoName = $request->file('video_path')->getClientOriginalName();
            $courseVideoFolder = "courseVideos/".$course."/".$video->id;
            if(!is_dir($courseVideoFolder)){
                File::makeDirectory($courseVideoFolder, $mode = 0777, true, true);
            }
            $systemVideoPath = $courseVideoFolder ."/". $originalVideoName;
            if(file_exists($systemVideoPath)){
                unlink($systemVideoPath);
            } elseif(file_exists($video->video_path)){
                unlink($video->video_path);
            }
            $request->file('video_path')->move($courseVideoFolder, $originalVideoName);
            $video->video_path = $systemVideoPath;
            $video->save();
        } else {
            $courseVideoFolder = "courseVideos/".$course."/".$video->id;
            if(is_dir($courseVideoFolder)){
                InputSanitise::delFolder($courseVideoFolder);
            }
        }
    	return $video;
    }

    /**
     *  return course video by courseId
     */
    protected static function getCourseVideosByCourseId($courseId){
        $courseId = InputSanitise::inputString($courseId);
        return CourseVideo::where('course_id', $courseId)->get();
    }

    /**
     *  get category of sub category
     */
    public function course(){
        return $this->belongsTo(CourseCourse::class, 'course_id');
    }

    /**
     *  get category of video
     */
    public function category(){
        $category = CourseCategory::find($this->course_category_id);
        if(is_object($category)){
            return $category->name;
        } else {
            return '';
        }
    }

    /**
     *  get subcategory of video
     */
    public function subcategory(){
        $subcategory = CourseSubCategory::find($this->course_sub_category_id);
        if(is_object($subcategory)){
            return $subcategory->name;
        } else {
            return '';
        }
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
        return $this->hasMany(CourseComment::class);
    }

    public function deleteLikes(){
        return $this->hasMany(CourseVideoLike::class);
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

    protected static function isCourseVideoExist($request){
        $courseId = InputSanitise::inputInt($request->get('course'));
        $videoName = InputSanitise::inputString($request->get('video'));
        $videoId = InputSanitise::inputInt($request->get('video_id'));
        $result = static::where('course_id', $courseId)->where('name', $videoName);
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
