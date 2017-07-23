<?php

namespace App\Http\Controllers\Course;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use App\Models\CourseCourse;
use App\Models\CourseVideo;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;

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
                DB::commit();
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
