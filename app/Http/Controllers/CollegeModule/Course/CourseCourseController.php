<?php

namespace App\Http\Controllers\CollegeModule\Course;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use App\Models\CourseSubCategory;
use App\Models\CollegeCategory;
use App\Models\CourseCourse;
use App\Models\User;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;

class CourseCourseController extends Controller
{
    /**
     *  check admin have permission or not, if not redirect to home
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
    protected function show($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $loginUser = Auth::guard('web')->user();

        if(User::Hod == $loginUser->user_type || User::Lecturer == $loginUser->user_type){
            $courseCourses = CourseCourse::getCoursesByCollegeIdByAssignedDeptsWithPagination($loginUser->college_id);
        } else {
            $courseCourses = CourseCourse::getCoursesByCollegeIdByDeptIdWithPagination($loginUser->college_id);
        }

    	return view('collegeModule.course.courseCourse.list', compact('courseCourses'));
    }

    /**
     *  show create course UI
     */
    protected function create($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $loginUser = Auth::guard('web')->user();
    	$courseCategories = CollegeCategory::getCollegeCategoriesByCollegeIdByDeptId($loginUser->college_id);
		$courseSubCategories = [];
		$course= new CourseCourse;
		return view('collegeModule.course.courseCourse.create', compact('courseCategories','courseSubCategories','course'));
    }

    /**
     *  store course
     */
    protected function store($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
    	$v = Validator::make($request->all(), $this->validateCourseCourse);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        // InputSanitise::deleteCacheByString('vchip:courses*');
        DB::beginTransaction();
        try
        {
            $course = CourseCourse::addOrUpdateCourse($request);
            if(is_object($course)){
                DB::commit();
            	return Redirect::to('college/'.$collegeUrl.'/manageCourseCourse')->with('message', 'Course created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('college/'.$collegeUrl.'/manageCourseCourse');
    }

    /**
     *  edit course
     */
    protected function edit($collegeUrl,$id,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
    	$id = InputSanitise::inputInt(json_decode($id));
    	if(isset($id)){
    		$course = CourseCourse::find($id);
    		if(is_object($course)){
    			$loginUser = Auth::guard('web')->user();
                if(is_object($loginUser) && ( $course->created_by == $loginUser->id || (User::Hod ==  $loginUser->user_type || User::Directore ==  $loginUser->user_type))){
                    $courseCategories = CollegeCategory::getCollegeCategoriesByCollegeIdByDeptId($loginUser->college_id);
                    $courseSubCategories = CourseSubCategory::getCollegeCourseSubCategoriesByCategoryId($course->course_category_id);
                    return view('collegeModule.course.courseCourse.create', compact('courseCategories','courseSubCategories','course'));
                }
    		}
    	}
        return Redirect::to('college/'.$collegeUrl.'/manageCourseCourse');
    }

    /**
     *  update course
     */
    protected function update($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
    	$v = Validator::make($request->all(), $this->validateCourseCourse);

        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        // InputSanitise::deleteCacheByString('vchip:courses*');
        DB::beginTransaction();
        try
        {
            $course = CourseCourse::addOrUpdateCourse($request, true);

            if(is_object($course)){
                DB::commit();
            	return Redirect::to('college/'.$collegeUrl.'/manageCourseCourse')->with('message', 'Course updated successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('college/'.$collegeUrl.'/manageCourseCourse');
    }

    /**
     *  delete course
     */
    protected function delete($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        // InputSanitise::deleteCacheByString('vchip:courses*');
    	$courseId = InputSanitise::inputInt($request->get('course_id'));
    	if(isset($courseId)){
    		$course = CourseCourse::find($courseId);
    		if(is_object($course)){
                DB::beginTransaction();
                try
                {
                    $loginUser = Auth::guard('web')->user();
                    if(is_object($loginUser) && ( $course->created_by == $loginUser->id || (User::Hod ==  $loginUser->user_type || User::Directore ==  $loginUser->user_type))){
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
            			return Redirect::to('college/'.$collegeUrl.'/manageCourseCourse')->with('message', 'Course deleted successfully!');
                    }
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return redirect()->back()->withErrors('something went wrong.');
                }
    		}
    	}
    	return Redirect::to('college/'.$collegeUrl.'/manageCourseCourse');
    }

    /**
     * return course sub categories by categoryId
     */
    protected function getCollegeCourseSubCategories(Request $request){
    	$id = InputSanitise::inputInt($request->get('id'));
    	if(isset($id)){
    		return CourseSubCategory::getCollegeCourseSubCategoriesByCategoryId($id);
    	}
    }

    protected function isCourseCourseExist(Request $request){
        return CourseCourse::isCourseCourseExist($request);
    }

    protected function getCourseByCatIdBySubCatIdByUser(Request $request){
        $categoryId = InputSanitise::inputInt($request->get('category'));
        $subcategoryId = InputSanitise::inputInt($request->get('subcategory'));
        return CourseCourse::getCourseByCatIdBySubCatIdByUser($categoryId,$subcategoryId);
    }
}


