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

class CourseAllController extends Controller
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
    protected $validateCollegeCategory = [
        'category' => 'required|string',
    ];

    protected $validateCourseSubcategory = [
        'category' => 'required|integer',
        'subcategory' => 'required|string',
    ];

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
     *  show create course category UI
     */
    protected function showAll($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $loginUser = Auth::guard('web')->user();
        $courseCategories = CollegeCategory::getCollegeCategoriesByCollegeIdByDeptId($loginUser->college_id);
        return view('collegeModule.course.courseAll.create', compact('courseCategories'));
    }

    /**
     *  store course category
     */
    protected function storeCategory($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
    	$v = Validator::make($request->all(), $this->validateCollegeCategory);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        // InputSanitise::deleteCacheByString('vchip:courses*');
        DB::beginTransaction();
        try
        {
            $category = CollegeCategory::addOrUpdateCollegeCategory($request);
            if(is_object($category)){
                DB::commit();
                return Redirect::to('college/'.$collegeUrl.'/manageCourseAll')->with('message', 'College category created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return back()->withErrors('something went wrong.');
        }
        return Redirect::to('college/'.$collegeUrl.'/manageCourseAll');
    }

    /**
     *  store sub category
     */
    protected function storeSubCategory($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $v = Validator::make($request->all(), $this->validateCourseSubcategory);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        // InputSanitise::deleteCacheByString('vchip:courses*');
        DB::beginTransaction();
        try
        {
            $subcategory = CourseSubCategory::addOrUpdateCourseSubCategory($request);
            if(is_object($subcategory)){
                 DB::commit();
                return Redirect::to('college/'.$collegeUrl.'/manageCourseAll')->with('message', 'Course sub category created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return back()->withErrors('something went wrong.');
        }
       return Redirect::to('college/'.$collegeUrl.'/manageCourseAll');
    }

    /**
     *  store course
     */
    protected function storeCourse($collegeUrl,Request $request){
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
                return Redirect::to('college/'.$collegeUrl.'/manageCourseAll')->with('message', 'Course created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('college/'.$collegeUrl.'/manageCourseAll');
    }
}
