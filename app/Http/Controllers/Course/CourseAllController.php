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

class CourseAllController extends Controller
{
    /**
     *  check admin have permission or not, if not redirect to home
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
    protected $validateCourseCategory = [
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
    protected function showAll(){
        $courseCategories = CourseCategory::all();
        return view('courseAll.create', compact('courseCategories'));
    }

    /**
     *  store course category
     */
    protected function storeCategory(Request $request){
    	$v = Validator::make($request->all(), $this->validateCourseCategory);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        InputSanitise::deleteCacheByString('vchip:courses*');
        DB::beginTransaction();
        try
        {
            $category = CourseCategory::addOrUpdateCourseCategory($request);
            if(is_object($category)){
                DB::commit();
                return Redirect::to('admin/manageCourseAll')->with('message', 'Course category created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return back()->withErrors('something went wrong.');
        }
        return Redirect::to('admin/manageCourseAll');
    }

    /**
     *  store sub category
     */
    protected function storeSubCategory(Request $request){
        $v = Validator::make($request->all(), $this->validateCourseSubcategory);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        InputSanitise::deleteCacheByString('vchip:courses*');
        DB::beginTransaction();
        try
        {
            $subcategory = CourseSubCategory::addOrUpdateCourseSubCategory($request);
            if(is_object($subcategory)){
                 DB::commit();
                return Redirect::to('admin/manageCourseAll')->with('message', 'Course sub category created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return back()->withErrors('something went wrong.');
        }
       return Redirect::to('admin/manageCourseAll');
    }

    /**
     *  store course
     */
    protected function storeCourse(Request $request){
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
                return Redirect::to('admin/manageCourseAll')->with('message', 'Course created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('admin/manageCourseAll');
    }
}
