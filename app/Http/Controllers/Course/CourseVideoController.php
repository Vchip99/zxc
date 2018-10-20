<?php

namespace App\Http\Controllers\Course;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use App\Models\CourseCourse;
use App\Models\CourseVideo;
use App\Models\CourseSubCategory;
use App\Models\CourseCategory;
use App\Models\Notification;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;
use App\Mail\MailToSubscribedUser;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class CourseVideoController extends Controller
{
    /**
     *  check admin have permission or not, if not redirect admin/home
     */
    public function __construct() {
        $this->middleware(function ($request, $next) {
            $adminUser = Auth::guard('admin')->user();
            if(is_object($adminUser)){
                if($adminUser->hasRole('admin') || $adminUser->hasPermission('manageOnlineCourse')){
                    return $next($request);
                }
            }
            return Redirect::to('admin/home');
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
    protected function show(){
    	$courseVideos = CourseVideo::getCourseVideosWithPagination();
    	return view('courseVideo.list', compact('courseVideos'));
    }

    /**
     *  show create course video UI
     */
    protected function create(){
        $courseCategories = CourseCategory::getCourseCategoriesForAdmin();
        $courseSubCategories = [];
        $courseCourses= [];
    	$video = new CourseVideo;
    	return view('courseVideo.create', compact('courseCategories','courseSubCategories','courseCourses', 'video'));
    }

    /**
     *  store course video
     */
    protected function store(Request $request){
    	$v = Validator::make($request->all(), $this->validateCourseVideo);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        InputSanitise::deleteCacheByString('vchip:courses*');
        DB::beginTransaction();
        try
        {
        	$video = CourseVideo::addOrUpdateVideo($request);
            if(is_object($video)){
                $messageBody = '';
                $notificationMessage = 'A new course video: <a href="'.$request->root().'/episode/'.$video->id.'" target="_blank">'.$video->name.'</a> has been added.';
                Notification::addNotification($notificationMessage, Notification::ADMINCOURSEVIDEO, $video->id);
                DB::commit();
                // $subscriedUsers = User::where('admin_approve', 1)->where('verified', 1)->select('email')->get();
                // $allUsers = $subscriedUsers->chunk(100);
                // set_time_limit(0);
                // if(false == $allUsers->isEmpty()){
                //     foreach($allUsers as $selectedUsers){
                //         $messageBody .= '<p> Dear User</p>';
                //         $messageBody .= '<p>'.$notificationMessage.' please have a look once.</p>';
                //         $messageBody .= '<p><b> Thanks and Regard, </b></p>';
                //         $messageBody .= '<b><a href="https://vchiptech.com"> Vchip Technology Team </a></b><br/>';
                //         $messageBody .= '<b> More about us... </b><br/>';
                //         $messageBody .= '<b><a href="https://vchipedu.com"> Digital Education </a></b><br/>';
                //         $messageBody .= '<b><a href="mailto:info@vchiptech.com" target="_blank">E-mail</a></b><br/>';
                //         $mailSubject = 'Vchipedu added a new course video';
                //         Mail::bcc($selectedUsers)->queue(new MailToSubscribedUser($messageBody, $mailSubject));
                //     }
                // }
            	return Redirect::to('admin/manageCourseVideo')->with('message', 'Video created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('admin/manageCourseVideo');
    }

    /**
     *  edit course video
     */
    protected function edit($id){
    	$id = InputSanitise::inputInt(json_decode($id));
    	if(isset($id)){
    		$video = CourseVideo::find($id);
    		if(is_object($video)){
    			$courseCategories = CourseCategory::getCourseCategoriesForAdmin();
                $courseSubCategories = CourseSubCategory::getCourseSubCategoriesByCategoryId($video->course_category_id);
                $courseCourses= CourseCourse::getCourseByCatIdBySubCatIdForAdmin($video->course_category_id,$video->course_sub_category_id);
    			return view('courseVideo.create', compact('courseCategories','courseSubCategories','courseCourses', 'video'));

    		}
    	}
    	return Redirect::to('admin/manageCourseVideo');
    }

    /**
     *  update course video
     */
    protected function update(Request $request){
    	$v = Validator::make($request->all(), $this->updateValidateCourseVideo);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        InputSanitise::deleteCacheByString('vchip:courses*');
        DB::beginTransaction();
        try
        {
        	$video = CourseVideo::addOrUpdateVideo($request, true);
            if(is_object($video)){
                DB::commit();
            	return Redirect::to('admin/manageCourseVideo')->with('message', 'Video updated successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('admin/manageCourseVideo');
    }

    /**
     *  delete course video
     */
    protected function delete(Request $request){
        InputSanitise::deleteCacheByString('vchip:courses*');
    	$videoId = InputSanitise::inputInt($request->get('video_id'));
    	if(isset($videoId)){
    		$video = CourseVideo::find($videoId);
    		if(is_object($video)){
                DB::beginTransaction();
                try
                {
                    $videoCategory = $video->videoCategory;
                    if(0 == $videoCategory->college_id && 0 == $videoCategory->user_id){
                        $video->deleteCommantsAndSubComments();
                        if(true == preg_match('/courseVideos/',$video->video_path)){
                            $courseVideoFolder = "courseVideos/".$video->course_id."/".$video->id;
                            if(is_dir($courseVideoFolder)){
                                InputSanitise::delFolder($courseVideoFolder);
                            }
                        }
            			$video->delete();
                        DB::commit();
            			return Redirect::to('admin/manageCourseVideo')->with('message', 'Video deleted successfully!');
                    }
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return redirect()->back()->withErrors('something went wrong.');
                }
    		}
    	}
    	return Redirect::to('admin/manageCourseVideo');
    }

    protected function isCourseVideoExist(Request $request){
        return CourseVideo::isCourseVideoExist($request);
    }
}
