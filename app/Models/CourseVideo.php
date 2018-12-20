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
use App\Models\CollegeCategory;
use File,Auth;

class CourseVideo extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'description', 'duration', 'video_path','course_id', 'course_category_id', 'course_sub_category_id','is_free'];

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
        $isFree = InputSanitise::inputString($request->get('is_free'));

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
        $video->is_free = $isFree;
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
     *  return course video by collegeId by deptId
     */
    protected static function getCourseVideosByCollegeIdByDeptIdWithPagination($collegeId, $deptId=NULL){
        $loginUser = Auth::user();
        $collegeId = InputSanitise::inputInt($collegeId);
        $deptId = InputSanitise::inputInt($deptId);
        $result = static::join('college_categories', 'college_categories.id', '=', 'course_videos.course_category_id')
            ->join('course_sub_categories', 'course_sub_categories.id', '=', 'course_videos.course_sub_category_id')
            ->join('course_courses', 'course_courses.id', '=', 'course_videos.course_id')
            ->join('users','users.id','=','course_courses.created_by')
            ->where('college_categories.college_id', $collegeId)->where('course_sub_categories.created_for', 0);
        if(NULL != $deptId){
            $result->where('college_categories.college_dept_id', '=', $deptId);
        }

        if(User::TNP == $loginUser->user_type){
            $result->where('course_courses.created_by', $loginUser->id);
        }
        return $result->select('course_videos.id', 'course_videos.name', 'college_categories.name as category', 'course_sub_categories.name as subcategory', 'course_courses.name as course','college_categories.college_dept_id', 'course_courses.created_by', 'course_videos.course_id','users.name as user')
            ->groupBy('course_videos.id')
            ->paginate();
    }

    protected static function getCourseVideosByCollegeIdByAssignedDeptsWithPagination($collegeId){
        $loginUser = Auth::user();
        $result = static::join('college_categories', 'college_categories.id', '=', 'course_videos.course_category_id')
                ->join('course_sub_categories', 'course_sub_categories.id', '=', 'course_videos.course_sub_category_id')
                ->join('course_courses', 'course_courses.id', '=', 'course_videos.course_id')
                ->join('users','users.id','=','course_courses.created_by')
                ->where('users.college_id', $collegeId);
        if(User::Lecturer == $loginUser->user_type){
            $result->where('course_courses.created_by', $loginUser->id);
        } else {
            $result->where(function($query) use($loginUser){
                $query->where('users.user_type', User::Lecturer);
                $query->orWhere('users.id',$loginUser->id);
            })
            ->where('course_courses.created_by', '>', 0)->whereIn('users.college_dept_id', explode(',',$loginUser->assigned_college_depts));
        }
        return $result->where('course_sub_categories.created_for', 0)->select('course_videos.id', 'course_videos.name', 'college_categories.name as category', 'course_sub_categories.name as subcategory', 'course_courses.name as course','college_categories.college_dept_id', 'course_courses.created_by', 'course_videos.course_id','users.name as user')
            ->groupBy('course_videos.id')
            ->paginate();
    }

    /**
     *  return course video by collegeId by deptId
     */
    protected static function getCourseVideosWithPagination(){
        $result = static::join('course_categories', 'course_categories.id', '=', 'course_videos.course_category_id')
            ->join('course_sub_categories', 'course_sub_categories.id', '=', 'course_videos.course_sub_category_id')
            ->join('course_courses', 'course_courses.id', '=', 'course_videos.course_id')
            ->join('admins','admins.id','=', 'course_courses.admin_id')
            ->where('course_sub_categories.created_for', 1);
        if(Auth::guard('admin')->user()->hasRole('sub-admin')){
            $result->where('course_courses.admin_id', Auth::guard('admin')->user()->id);
        }
        return $result->select('course_videos.id', 'course_videos.name', 'course_categories.name as category', 'course_sub_categories.name as subcategory', 'course_courses.name as course','course_courses.admin_id','admins.name as admin')
            ->groupBy('course_videos.id')
            ->paginate();
    }

    /**
     *  return course video by collegeId by videoId
     */
    protected static function getCourseVideoByCollegeIdByVideoId($collegeId, $videoId){
        $collegeId = InputSanitise::inputInt($collegeId);
        $videoId = InputSanitise::inputInt($videoId);
        return static::join('course_courses', 'course_courses.id', '=', 'course_videos.course_id')
            ->join('college_categories', 'college_categories.id', '=', 'course_courses.course_category_id')
            ->join('course_sub_categories', 'course_sub_categories.id','=','course_videos.course_sub_category_id')
            ->where('course_sub_categories.created_for', 0)
            ->where('college_categories.college_id', $collegeId)
            ->where('course_videos.id', $videoId)
            ->select('course_videos.*')
            ->first();
    }

    /**
     *  return course video by videoId
     */
    protected static function getCourseVideoByVideoId($videoId){
        $videoId = InputSanitise::inputInt($videoId);
        // return static::join('course_courses', 'course_courses.id', '=', 'course_videos.course_id')
        //     ->join('course_categories', 'course_categories.id', '=', 'course_courses.course_category_id')
        //     ->join('course_sub_categories', 'course_sub_categories.id','=','course_videos.course_sub_category_id')
        //     ->where('course_sub_categories.created_for', 1)
        //     ->where('course_videos.id', $videoId)
        //     ->select('course_videos.*')
        //     ->first();
        return static::where('course_videos.id', $videoId)
            ->select('course_videos.*')
            ->first();
    }

    /**
     *  get course
     */
    public function course(){
        $course = CourseCourse::find($this->course_id);
        if(is_object($course)){
            return $course->name;
        } else {
            return '';
        }
    }

    /**
     *  get category of sub category
     */
    public function collegeCourse(){
        return $this->belongsTo(CourseCourse::class, 'course_id');
    }

    /**
     *  get category of sub category
     */
    public function videoCourse(){
        return $this->belongsTo(CourseCourse::class, 'course_id');
    }

    /**
     *  get category of sub category
     */
    public function videoCategory(){
        return $this->belongsTo(CourseCategory::class, 'course_category_id');
    }

    // /**
    //  *  get category of sub category
    //  */
    // public function collegeCategory(){
    //     return $this->belongsTo(CollegeCategory::class, 'course_category_id');
    // }

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

        $loginUser = Auth::guard('web')->user();
        if(is_object($loginUser)){
            $result = static::join('college_categories', 'college_categories.id','=','course_videos.course_category_id')
                ->join('course_sub_categories', 'course_sub_categories.id', '=', 'course_videos.course_sub_category_id')
                ->join('course_courses', 'course_courses.id', '=', 'course_videos.course_id')
                ->where('course_sub_categories.created_for', 0)
                ->where('course_videos.course_id', $courseId)
                ->where('course_videos.name', $videoName);
            if(!empty($videoId)){
                $result->where('course_videos.id', '!=', $videoId);
            }
            $result->where('college_categories.college_id', $loginUser->college_id);
        } else {
            $result = static::join('course_categories', 'course_categories.id','=','course_videos.course_category_id')
                ->join('course_sub_categories', 'course_sub_categories.id', '=', 'course_videos.course_sub_category_id')
                ->join('course_courses', 'course_courses.id', '=', 'course_videos.course_id')
                ->where('course_sub_categories.created_for', 1)
                ->where('course_videos.course_id', $courseId)
                ->where('course_videos.name', $videoName);
            if(!empty($videoId)){
                $result->where('course_videos.id', '!=', $videoId);
            }
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
