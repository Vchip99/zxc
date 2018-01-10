<?php

namespace App\Http\Controllers\Course;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use App\Models\CourseCategory;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;

class CourseCategoryController extends Controller
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

    /**
     *  show list of course category
     */
    protected function show(){
    	$courseCategories = CourseCategory::paginate();
    	return view('courseCategory.list', compact('courseCategories'));
    }

    /**
     *  show create course category UI
     */
    protected function create(){
		$courseCategory = new CourseCategory;
		return view('courseCategory.create', compact('courseCategory'));
    }

    /**
     *  store course category
     */
    protected function store(Request $request){
    	$v = Validator::make($request->all(), $this->validateCourseCategory);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::beginTransaction();
        try
        {
            $category = CourseCategory::addOrUpdateCourseCategory($request);
            if(is_object($category)){
                DB::commit();
                return Redirect::to('admin/manageCourseCategory')->with('message', 'Course category created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return back()->withErrors('something went wrong.');
        }
        return Redirect::to('admin/manageCourseCategory');
    }

    /**
     *  edit course category
     */
    protected function edit($id){
    	$id = InputSanitise::inputInt(json_decode($id));
    	if(isset($id)){
    		$courseCategory = CourseCategory::find($id);
    		if(is_object($courseCategory)){
    			return view('courseCategory.create', compact('courseCategory'));
    		}
    	}
    	return Redirect::to('admin/manageCourseCategory');
    }

    /**
     *  update course category
     */
    protected function update(Request $request){
        $v = Validator::make($request->all(), $this->validateCourseCategory);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }

    	$categoryId = InputSanitise::inputInt($request->get('category_id'));
    	if(isset($categoryId)){
            DB::beginTransaction();
            try
            {
                $category = CourseCategory::addOrUpdateCourseCategory($request, true);
                if(is_object($category)){
                    DB::commit();
                    return Redirect::to('admin/manageCourseCategory')->with('message', 'Course category updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return back()->withErrors('something went wrong.');
            }
    	}
    	return Redirect::to('admin/manageCourseCategory');
    }

    /**
     *  delete course category
     */
    protected function delete(Request $request){
    	$categoryId = InputSanitise::inputInt($request->get('category_id'));
    	if(isset($categoryId)){
    		$courseCategory = CourseCategory::find($categoryId);
    		if(is_object($courseCategory)){
                DB::beginTransaction();
                try
                {
                    if(true == is_object($courseCategory->subcategory) && false == $courseCategory->subcategory->isEmpty() ){
                        foreach($courseCategory->subcategory as $subcategory){
                            if(true == is_object($subcategory->courses) && false == $subcategory->courses->isEmpty()){
                                foreach($subcategory->courses as $course){
                                    if(true == is_object($course->videos) && false == $course->videos->isEmpty()){
                                        foreach($course->videos as $video){
                                            $video->deleteCommantsAndSubComments();
                                            $video->delete();
                                        }
                                    }
                                    $course->deleteRegisteredCourses();
                                    $course->deleteCourseImageFolder();
                                    $course->delete();
                                }
                            }
                            $subcategory->delete();
                        }
                    }
        			$courseCategory->delete();
                    DB::commit();
        			return Redirect::to('admin/manageCourseCategory')->with('message', 'Course category deleted successfully!');
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return back()->withErrors('something went wrong.');
                }
    		}
    	}
    	return Redirect::to('admin/manageCourseCategory');
    }

    protected function isCourseCategoryExist(Request $request){
        return CourseCategory::isCourseCategoryExist($request);
    }
}
