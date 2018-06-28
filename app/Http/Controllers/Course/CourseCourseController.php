<?php

namespace App\Http\Controllers\Course;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use App\Models\CourseSubCategory;
use App\Models\CourseCategory;
use App\Models\CourseCourse;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;

class CourseCourseController extends Controller
{
    /**
     *  check admin have permission or not, if not redirect to admin/home
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
    protected $validateCourseCourse = [
        'category' => 'required|integer',
        'subcategory' => 'required|integer',
        'course' => 'required|string',
        'author' => 'required|string',
        'author_introduction' => 'required|string',
        'description' => 'required|string',
        'price' => 'required',
        'difficulty_level' => 'required|integer',
        'certified' => 'required|integer',
        'release_date' => 'required|date',

    ];

    /**
     *  show list of courses
     */
    protected function show(){
    	$courseCourses = CourseCourse::paginate();
    	return view('courseCourse.list', compact('courseCourses'));
    }

    /**
     *  show create course UI
     */
    protected function create(){
    	$courseCategories   = CourseCategory::all();
		$courseSubCategories = [];
		$course= new CourseCourse;

		return view('courseCourse.create', compact('courseCategories','courseSubCategories','course'));
    }

    /**
     *  store course
     */
    protected function store(Request $request){
    	$v = Validator::make($request->all(), $this->validateCourseCourse);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        InputSanitise::deleteCacheByString('vchip:courses*');
        DB::beginTransaction();
        try
        {
            $course = CourseCourse::addOrUpdateCourse($request);
            if(is_object($course)){
                DB::commit();
            	return Redirect::to('admin/manageCourseCourse')->with('message', 'Course created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('admin/manageCourseCourse');
    }

    /**
     *  edit course
     */
    protected function edit($id){
    	$id = InputSanitise::inputInt(json_decode($id));
    	if(isset($id)){
    		$course = CourseCourse::find($id);
    		if(is_object($course)){
    			$courseCategories   = CourseCategory::all();
				$courseSubCategories = CourseSubCategory::getCourseSubCategoriesByCategoryId($course->course_category_id);
				return view('courseCourse.create', compact('courseCategories','courseSubCategories','course'));
    		}
    	}
    }

    /**
     *  update course
     */
    protected function update(Request $request){
    	$v = Validator::make($request->all(), $this->validateCourseCourse);

        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        InputSanitise::deleteCacheByString('vchip:courses*');
        DB::beginTransaction();
        try
        {
            $course = CourseCourse::addOrUpdateCourse($request, true);

            if(is_object($course)){
                DB::commit();
            	return Redirect::to('admin/manageCourseCourse')->with('message', 'Course updated successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('admin/manageCourseCourse');
    }

    /**
     *  delete course
     */
    protected function delete(Request $request){
        InputSanitise::deleteCacheByString('vchip:courses*');
    	$courseId = InputSanitise::inputInt($request->get('course_id'));
    	if(isset($courseId)){
    		$course = CourseCourse::find($courseId);
    		if(is_object($course)){
                DB::beginTransaction();
                try
                {
                    if(true == is_object($course->videos) && false == $course->videos->isEmpty()){
                        foreach($course->videos as $video){
                            $video->deleteCommantsAndSubComments();
                            if(true == preg_match('/courseVideos/',$video->video_path)){
                                $courseVideoFolder = "courseVideos/".$video->course_id."/".$video->id;
                                if(is_dir($courseVideoFolder)){
                                    InputSanitise::delFolder($courseVideoFolder);
                                }
                            }
                            $video->delete();
                        }
                    }
                    $course->deleteRegisteredCourses();
                    $course->deleteCourseImageFolder();
                    $courseVideoFolder = "courseVideos/".$course->id;
                    if(is_dir($courseVideoFolder)){
                        InputSanitise::delFolder($courseVideoFolder);
                    }
        			$course->delete();
                    DB::commit();
        			return Redirect::to('admin/manageCourseCourse')->with('message', 'Course deleted successfully!');
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return redirect()->back()->withErrors('something went wrong.');
                }
    		}
    	}
    	return Redirect::to('admin/manageCourseCourse');
    }

    /**
     * return course sub categories by categoryId
     */
    protected function getCourseSubCategories(Request $request){
    	$id = InputSanitise::inputInt($request->get('id'));
    	if(isset($id)){
    		return CourseSubCategory::getCourseSubCategoriesByCategoryId($id);
    	}
    }

    protected function isCourseCourseExist(Request $request){
        return CourseCourse::isCourseCourseExist($request);
    }

    protected function getCourseByCatIdBySubCatIdForAdmin(Request $request){
        $categoryId = InputSanitise::inputInt($request->get('category'));
        $subcategoryId = InputSanitise::inputInt($request->get('subcategory'));
        return CourseCourse::getCourseByCatIdBySubCatIdForAdmin($categoryId,$subcategoryId);
    }
}


