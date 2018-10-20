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

class CourseSubCategoryController extends Controller
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
    protected $validateCourseSubcategory = [
        'category' => 'required|integer',
        'subcategory' => 'required|string',
    ];

    /**
     *  show list of course sub category
     */
    protected function show($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $loginUser = Auth::guard('web')->user();
    	$courseSubCategories = CourseSubCategory::getCourseSubCategoriesByCollegeIdByDeptIdWithPagination($loginUser->college_id);
    	return view('collegeModule.course.courseSubcategory.list', compact('courseSubCategories'));
    }

    /**
     *  show create category UI
     */
    protected function create($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
    	$loginUser = Auth::guard('web')->user();
        $courseCategories = CollegeCategory::getCollegeCategoriesByCollegeIdByDeptId($loginUser->college_id);
    	$courseSubcategory = new CourseSubCategory;
    	return view('collegeModule.course.courseSubcategory.create', compact('courseSubcategory', 'courseCategories'));
    }

    /**
     *  store sub category
     */
    protected function store($collegeUrl,Request $request){
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
        		return Redirect::to('college/'.$collegeUrl.'/manageCourseSubCategory')->with('message', 'Course sub category created successfully!');
        	}
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return back()->withErrors('something went wrong.');
        }
	   return Redirect::to('college/'.$collegeUrl.'/manageCourseSubCategory');
    }

    /**
     *  edit sub category
     */
    protected function edit($collegeUrl,$id,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
    	$id = InputSanitise::inputInt(json_decode($id));
    	if(isset($id)){
    		$courseSubcategory = CourseSubCategory::find($id);
    		if(is_object($courseSubcategory)){
    			$loginUser = Auth::guard('web')->user();
                if(is_object($loginUser) && ($courseSubcategory->created_by == $loginUser->id || (User::Hod ==  Auth::User()->user_type || User::Directore ==  Auth::User()->user_type))){
                    $courseCategories = CollegeCategory::getCollegeCategoriesByCollegeIdByDeptId($loginUser->college_id);
    	    		return view('collegeModule.course.courseSubcategory.create', compact('courseSubcategory', 'courseCategories'));
                }
    		}
    	}
    	return Redirect::to('college/'.$collegeUrl.'/manageCourseSubCategory');
    }

    /**
     *  update sub category
     */
    protected function update($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
    	$v = Validator::make($request->all(), $this->validateCourseSubcategory);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        // InputSanitise::deleteCacheByString('vchip:courses*');
        $subCategoryId = InputSanitise::inputInt($request->get('subCategory_id'));
    	if(isset($subCategoryId)){
            DB::beginTransaction();
            try
            {
        		$subcategory = CourseSubCategory::addOrUpdateCourseSubCategory($request, true);
                if(is_object($subcategory)){
                    DB::commit();
                    return Redirect::to('college/'.$collegeUrl.'/manageCourseSubCategory')->with('message', 'Course sub category updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return back()->withErrors('something went wrong.');
            }
    	}
    	return Redirect::to('college/'.$collegeUrl.'/manageCourseSubCategory');
    }

    /**
     *  delete sub category
     */
    protected function delete($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        // InputSanitise::deleteCacheByString('vchip:courses*');
    	$subCategoryId = InputSanitise::inputInt($request->get('subCategory_id'));
    	if(isset($subCategoryId)){
    		$courseSubcategory = CourseSubCategory::find($subCategoryId);
    		if(is_object($courseSubcategory)){
                DB::beginTransaction();
                try
                {
                    $loginUser = Auth::guard('web')->user();
                    if(is_object($loginUser) && ($courseSubcategory->created_by == $loginUser->id || (User::Hod ==  Auth::User()->user_type || User::Directore ==  Auth::User()->user_type))){
                        if(true == is_object($courseSubcategory->courses) && false == $courseSubcategory->courses->isEmpty()){
                            foreach($courseSubcategory->courses as $course){
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
            			$courseSubcategory->delete();
                        DB::commit();
            			return Redirect::to('college/'.$collegeUrl.'/manageCourseSubCategory')->with('message', 'Course sub category deleted successfully!');
                    }
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return back()->withErrors('something went wrong.');
                }
    		}
    	}
    	return Redirect::to('college/'.$collegeUrl.'/manageCourseSubCategory');
    }

    protected function isCourseSubCategoryExist(Request $request){
        return CourseSubCategory::isCourseSubCategoryExist($request);
    }
}
