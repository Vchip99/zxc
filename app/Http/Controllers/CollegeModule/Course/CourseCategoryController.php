<?php

namespace App\Http\Controllers\CollegeModule\Course;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use App\Models\CourseCategory;
use App\Models\User;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;

class CourseCategoryController extends Controller
{
    /**
     *  check admin have permission or not, if not redirect to home
     */
	public function __construct() {
        $this->middleware(function ($request, $next) {
            $loginUser = Auth::guard('web')->user();
            if(is_object($loginUser) && (User::Lecturer == $loginUser->user_type || User::Hod == $loginUser->user_type)){
                return $next($request);
            }
            return Redirect::to('/');
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
    protected function show($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $loginUser = Auth::guard('web')->user();
    	$courseCategories = CourseCategory::getCourseCategoriesByCollegeIdByDeptIdWithPagination($loginUser->college_id);
    	return view('collegeModule.course.courseCategory.list', compact('courseCategories'));
    }

    /**
     *  show create course category UI
     */
    protected function create($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
		$courseCategory = new CourseCategory;
		return view('collegeModule.course.courseCategory.create', compact('courseCategory'));
    }

    /**
     *  store course category
     */
    protected function store($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
    	$v = Validator::make($request->all(), $this->validateCourseCategory);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        // InputSanitise::deleteCacheByString('vchip:courses*');
        DB::beginTransaction();
        try
        {
            $category = CourseCategory::addOrUpdateCourseCategory($request);
            if(is_object($category)){
                DB::commit();
                return Redirect::to('college/'.$collegeUrl.'/manageCourseCategory')->with('message', 'Course category created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return back()->withErrors('something went wrong.');
        }
        return Redirect::to('college/'.$collegeUrl.'/manageCourseCategory');
    }

    /**
     *  edit course category
     */
    protected function edit($collegeUrl,$id,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
    	$id = InputSanitise::inputInt(json_decode($id));
    	if(isset($id)){
    		$courseCategory = CourseCategory::find($id);
    		if(is_object($courseCategory)){
                $loginUser = Auth::guard('web')->user();
                if(is_object($loginUser) && $courseCategory->college_id == $loginUser->college_id && $courseCategory->user_id == $loginUser->id){
                    return view('collegeModule.course.courseCategory.create', compact('courseCategory'));
                }
    		}
    	}
    	return Redirect::to('college/'.$collegeUrl.'/manageCourseCategory');
    }

    /**
     *  update course category
     */
    protected function update($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $v = Validator::make($request->all(), $this->validateCourseCategory);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        // InputSanitise::deleteCacheByString('vchip:courses*');
    	$categoryId = InputSanitise::inputInt($request->get('category_id'));
    	if(isset($categoryId)){
            DB::beginTransaction();
            try
            {
                $category = CourseCategory::addOrUpdateCourseCategory($request, true);
                if(is_object($category)){
                    DB::commit();
                    return Redirect::to('college/'.$collegeUrl.'/manageCourseCategory')->with('message', 'Course category updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return back()->withErrors('something went wrong.');
            }
    	}
    	return Redirect::to('college/'.$collegeUrl.'/manageCourseCategory');
    }

    /**
     *  delete course category
     */
    protected function delete($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        // InputSanitise::deleteCacheByString('vchip:courses*');
    	$categoryId = InputSanitise::inputInt($request->get('category_id'));
    	if(isset($categoryId)){
    		$courseCategory = CourseCategory::find($categoryId);
    		if(is_object($courseCategory)){
                DB::beginTransaction();
                try
                {
                    $loginUser = Auth::guard('web')->user();
                    if(is_object($loginUser) && $courseCategory->college_id == $loginUser->college_id && $courseCategory->user_id == $loginUser->id){
                        if(true == is_object($courseCategory->subcategory) && false == $courseCategory->subcategory->isEmpty() ){
                            foreach($courseCategory->subcategory as $subcategory){
                                if(true == is_object($subcategory->courses) && false == $subcategory->courses->isEmpty()){
                                    foreach($subcategory->courses as $course){
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
                                    }
                                }
                                $subcategory->delete();
                            }
                        }
            			$courseCategory->delete();
                        DB::commit();
            			return Redirect::to('college/'.$collegeUrl.'/manageCourseCategory')->with('message', 'Course category deleted successfully!');
                    }
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return back()->withErrors('something went wrong.');
                }
    		}
    	}
    	return Redirect::to('college/'.$collegeUrl.'/manageCourseCategory');
    }

    protected function isCourseCategoryExist(Request $request){
        return CourseCategory::isCourseCategoryExist($request);
    }
}