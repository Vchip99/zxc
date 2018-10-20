<?php

namespace App\Http\Controllers\CollegeModule\Course;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use App\Models\CourseCourse;
use App\Models\CourseVideo;
use App\Models\CourseSubCategory;
use App\Models\CollegeCategory;
use App\Models\Notification;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;
use App\Mail\MailToSubscribedUser;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class CourseVideoController extends Controller
{
    /**
     *  check admin have permission or not, if not redirect home
     */
    public function __construct() {
        $this->middleware(function ($request, $next) {
            $loginUser = Auth::guard('web')->user();
            if(is_object($loginUser)){
                return $next($request);
            }
            return Redirect::to('/');
        });
    }


    /**
     * Define your validation rules in a property in
     * the controller to reuse the rules.
     */
    protected $validateCourseVideo = [
        'category' => 'required|integer',
        'subcategory' => 'required|integer',
        'course' => 'required|integer',
        'video' => 'required|string',
        'description' => 'required|string',
        'duration' => 'required|integer',
        'video_path' => 'required'
    ];

    protected $updateValidateCourseVideo = [
        'category' => 'required|integer',
        'subcategory' => 'required|integer',
        'course' => 'required|integer',
        'video' => 'required|string',
        'description' => 'required|string',
        'duration' => 'required|integer'
    ];

    /**
     *  show list of course video
     */
    protected function show($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $loginUser = Auth::guard('web')->user();
        if(User::Hod == $loginUser->user_type || User::Lecturer == $loginUser->user_type){
            $courseVideos = CourseVideo::getCourseVideosByCollegeIdByAssignedDeptsWithPagination($loginUser->college_id);
        } else {
            $courseVideos = CourseVideo::getCourseVideosByCollegeIdByDeptIdWithPagination($loginUser->college_id);
        }

    	return view('collegeModule.course.courseVideo.list', compact('courseVideos'));
    }

    /**
     *  show create course video UI
     */
    protected function create($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $loginUser = Auth::guard('web')->user();
        $courseCategories = CollegeCategory::getCollegeCategoriesByCollegeIdByDeptId($loginUser->college_id);
        $courseSubCategories = [];
        $courseCourses= [];
    	$video = new CourseVideo;
    	return view('collegeModule.course.courseVideo.create', compact('courseCategories','courseSubCategories','courseCourses', 'video'));
    }

    /**
     *  store course video
     */
    protected function store($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
    	$v = Validator::make($request->all(), $this->validateCourseVideo);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        // InputSanitise::deleteCacheByString('vchip:courses*');
        DB::beginTransaction();
        try
        {
        	$video = CourseVideo::addOrUpdateVideo($request);
            if(is_object($video)){
                DB::commit();
            	return Redirect::to('college/'.$collegeUrl.'/manageCourseVideo')->with('message', 'Video created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('college/'.$collegeUrl.'/manageCourseVideo');
    }

    /**
     *  edit course video
     */
    protected function edit($collegeUrl,$id,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
    	$id = InputSanitise::inputInt(json_decode($id));
    	if(isset($id)){
    		$video = CourseVideo::find($id);
    		if(is_object($video)){
    			$loginUser = Auth::guard('web')->user();
                $videoCourse = $video->collegeCourse;
                if(is_object($loginUser) && $videoCourse->created_by == $loginUser->id || (User::Hod ==  $loginUser->user_type || User::Directore ==  $loginUser->user_type)){
                    $courseCategories = CollegeCategory::getCollegeCategoriesByCollegeIdByDeptId($loginUser->college_id);
                    $courseSubCategories = CourseSubCategory::getCollegeCourseSubCategoriesByCategoryId($video->course_category_id);
                    if(User::Hod == $loginUser->user_type || User::Lecturer == $loginUser->user_type){
                        $courseCourses = CourseCourse::getCoursesByCollegeIdByAssignedDepts($loginUser->college_id);
                    } else {
                        if(User::TNP == $loginUser->user_type){
                            $courseCourses= CourseCourse::getCourseByCatIdBySubCatIdByUser($video->course_category_id,$video->course_sub_category_id);
                        } else {
                            $courseCourses= CourseCourse::getCourseByCatIdBySubCatIdForAdmin($video->course_category_id,$video->course_sub_category_id);
                        }
                    }
        			return view('collegeModule.course.courseVideo.create', compact('courseCategories','courseSubCategories','courseCourses', 'video'));
                }
    		}
    	}
    	return Redirect::to('college/'.$collegeUrl.'/manageCourseVideo');
    }

    /**
     *  update course video
     */
    protected function update($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
    	$v = Validator::make($request->all(), $this->updateValidateCourseVideo);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        // InputSanitise::deleteCacheByString('vchip:courses*');
        DB::beginTransaction();
        try
        {
        	$video = CourseVideo::addOrUpdateVideo($request, true);
            if(is_object($video)){
                DB::commit();
            	return Redirect::to('college/'.$collegeUrl.'/manageCourseVideo')->with('message', 'Video updated successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('college/'.$collegeUrl.'/manageCourseVideo');
    }

    /**
     *  delete course video
     */
    protected function delete($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        // InputSanitise::deleteCacheByString('vchip:courses*');
    	$videoId = InputSanitise::inputInt($request->get('video_id'));
    	if(isset($videoId)){
    		$video = CourseVideo::find($videoId);
    		if(is_object($video)){
                DB::beginTransaction();
                try
                {
                    $loginUser = Auth::guard('web')->user();
                    $videoCourse = $video->collegeCourse;
                    if(is_object($loginUser) && $videoCourse->created_by == $loginUser->id || (User::Hod ==  $loginUser->user_type || User::Directore ==  $loginUser->user_type)){
                        $video->deleteCommantsAndSubComments();
                        if(true == preg_match('/courseVideos/',$video->video_path)){
                            $courseVideoFolder = "courseVideos/".$video->course_id."/".$video->id;
                            if(is_dir($courseVideoFolder)){
                                InputSanitise::delFolder($courseVideoFolder);
                            }
                        }
            			$video->delete();
                        DB::commit();
            			return Redirect::to('college/'.$collegeUrl.'/manageCourseVideo')->with('message', 'Video deleted successfully!');
                    }
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return redirect()->back()->withErrors('something went wrong.');
                }
    		}
    	}
    	return Redirect::to('college/'.$collegeUrl.'/manageCourseVideo');
    }

    protected function isCourseVideoExist(Request $request){
        return CourseVideo::isCourseVideoExist($request);
    }
}
