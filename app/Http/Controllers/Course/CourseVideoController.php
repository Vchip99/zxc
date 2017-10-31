<?php

namespace App\Http\Controllers\Course;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use App\Models\CourseCourse;
use App\Models\CourseVideo;
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
        'video' => 'required|string',
        'description' => 'required|string',
        'duration' => 'required|integer',
        'course' => 'required|integer',
        'video_path' => 'required|string'
    ];

    /**
     *  show list of course video
     */
    protected function show(){
    	$courseVideos = CourseVideo::paginate();
    	return view('courseVideo.list', compact('courseVideos'));
    }

    /**
     *  show create course video UI
     */
    protected function create(){
    	$courseCourses = CourseCourse::all();
    	$video = new CourseVideo;
    	return view('courseVideo.create', compact('courseCourses', 'video'));
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
        DB::beginTransaction();
        try
        {
        	$video = CourseVideo::addOrUpdateVideo($request);
            if(is_object($video)){
                $messageBody = '';
                $notificationMessage = 'A new course video: <a href="'.$request->root().'/episode/'.$video->id.'">'.$video->name.'</a> has been added.';
                Notification::addNotification($notificationMessage, Notification::ADMINCOURSEVIDEO, $video->id);
                DB::commit();

                // $subscriedUsers = User::where('admin_approve', 1)->where('verified', 1)->select('email')->get()->toArray();
                // $allUsers = array_chunk($subscriedUsers, 100);
                // if(count($allUsers) > 0){
                //     foreach($allUsers as $selectedUsers){
                //         foreach($selectedUsers as $user){
                //             $user = User::where('email', $user)->first();
                //             $messageBody .= '<p> Hello '.$user->name.'</p>';
                //             $messageBody .= '<p>'.$notificationMessage.' please have a look once.</p>';
                //             $messageBody .= '<p><b> Thanks and Regard, </b></p>';
                //             $messageBody .= '<b><a href="https://vchiptech.com"> Vchip Technology Team </a></b><br/>';
                //             $messageBody .= '<b> More about us... </b><br/>';
                //             $messageBody .= '<b><a href="https://vchipedu.com"> Digital Education </a></b><br/>';
                //             $messageBody .= '<b><a href="mailto:info@vchiptech.com" target="_blank">E-mail</a></b><br/>';
                //             $mailSubject = 'Vchipedu added a new course video';
                //             Mail::to($user)->queue(new MailToSubscribedUser($messageBody, $mailSubject));
                //         }
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
    			$courseCourses = CourseCourse::all();
    			return view('courseVideo.create', compact('courseCourses', 'video'));
    		}
    	}
    	return Redirect::to('admin/manageCourseVideo');
    }

    /**
     *  update course video
     */
    protected function update(Request $request){
    	$v = Validator::make($request->all(), $this->validateCourseVideo);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
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
    	$videoId = InputSanitise::inputInt($request->get('video_id'));
    	if(isset($videoId)){
    		$video = CourseVideo::find($videoId);
    		if(is_object($video)){
                DB::beginTransaction();
                try
                {
                    $video->deleteCommantsAndSubComments();
        			$video->delete();
                    DB::commit();
        			return Redirect::to('admin/manageCourseVideo')->with('message', 'Video deleted successfully!');
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
}
