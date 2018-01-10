<?php

namespace App\Http\Controllers\LiveCourse;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use App\Models\LiveCourse;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;

class LiveCourseController extends Controller
{
    /**
     *  check admin have permission or not, if not redirect to admin/home
     */
	public function __construct() {
        $this->middleware(function ($request, $next) {
            $adminUser = Auth::guard('admin')->user();
            if(is_object($adminUser)){
                if($adminUser->hasRole('admin') || $adminUser->hasPermission('manageLiveCourse')){
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
    protected $validateLiveCourse = [
        'course' => 'required|string',
        'author' => 'required|string',
        'author_introduction' => 'required|string',
        'description' => 'required|string',
        'category' => 'required|integer',
        'certified' => 'required|integer',
        'on_demand' => 'required|integer',
        'price' => 'required',
        'difficulty_level' => 'required|integer',
        'start_date' => 'required|date',
        'end_date' => 'required|date',
    ];

    /**
     *  show list of courses
     */
    protected function show(){
    	$liveCourses = LiveCourse::paginate();
    	return view('liveCourses.list', compact('liveCourses'));
    }

    /**
     *  show create course UI
     */
    protected function create(){
        $liveCourse = new LiveCourse;
        return view('liveCourses.create', compact('liveCourse'));
    }

    /**
     *  store course
     */
    protected function store(Request $request){
        $v = Validator::make($request->all(), $this->validateLiveCourse);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::beginTransaction();
        try
        {
            $course = LiveCourse::addOrUpdateLiveCourse($request);
            if(is_object($course)){
                DB::commit();
                return Redirect::to('admin/manageLiveCourse')->with('message', 'Live Course Created Successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return back()->withErrors('something went wrong.');
        }
        return Redirect::to('admin/manageLiveCourse');
    }

    /**
     *  edit course
     */
    protected function edit($id){
        $id = InputSanitise::inputInt(json_decode($id));
        if(isset($id)){
            $liveCourse = LiveCourse::find($id);
            if(is_object($liveCourse)){
                return view('liveCourses.create', compact('liveCourse'));
            }
        }
        return Redirect::to('admin/manageLiveCourse');
    }

    /**
     *update course
     */
    protected function update(Request $request){
        $v = Validator::make($request->all(), $this->validateLiveCourse);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::beginTransaction();
        try
        {
            $course = LiveCourse::addOrUpdateLiveCourse($request, true);
            if(is_object($course)){
                DB::commit();
                return Redirect::to('admin/manageLiveCourse')->with('message', 'Live Course Updated Successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return back()->withErrors('something went wrong.');
        }
        return Redirect::to('admin/manageLiveCourse');
    }

    /**
     *  delete course
     */
    protected function delete(Request $request){
        $courseId = InputSanitise::inputInt($request->get('live_course_id'));
        if(isset($courseId)){
            $liveCourse = LiveCourse::find($courseId);
            if(is_object($liveCourse)){
                DB::beginTransaction();
                try
                {
                    if(true == is_object($liveCourse->videos) && false == $liveCourse->videos->isEmpty()){
                        foreach($liveCourse->videos as $video){
                            $video->deleteCommantsAndSubComments();
                            $video->delete();
                        }
                    }
                    $liveCourse->deleteRegisteredLiveCourses();
                    $liveCourse->deleteLiveCourseImageFolder();
                    $liveCourse->delete();
                    DB::commit();
                    return Redirect::to('admin/manageLiveCourse')->with('message', 'Live Course Deleted Successfully!');
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return back()->withErrors('something went wrong.');
                }
            }
        }
        return Redirect::to('admin/manageLiveCourse');
    }

    protected function isLiveCourseExist(Request $request){
        return LiveCourse::isLiveCourseExist($request);
    }

}