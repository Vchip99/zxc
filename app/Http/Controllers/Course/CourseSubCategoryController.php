<?php

namespace App\Http\Controllers\Course;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use App\Models\CourseSubCategory;
use App\Models\CourseCategory;
use App\Models\CourseCourse;
use App\Models\StudyMaterialSubject;
use App\Models\StudyMaterialTopic;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;

class CourseSubCategoryController extends Controller
{
    /**
     *  check admin have permission or not, if not redirect to admin/home
     */
 	public function __construct() {
        $this->middleware(function ($request, $next) {
            $adminUser = Auth::guard('admin')->user();
            if(is_object($adminUser)){
                if($adminUser->hasRole('admin')){
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
    protected $validateCourseSubcategory = [
        'category' => 'required|integer',
        'subcategory' => 'required|string',
    ];

    /**
     *  show list of course sub category
     */
    protected function show(){
    	$courseSubCategories = CourseSubCategory::getCourseSubCategoriesWithPagination();
    	return view('courseSubcategory.list', compact('courseSubCategories'));
    }

    /**
     *  show create category UI
     */
    protected function create(){
    	$courseCategories = CourseCategory::getCourseCategoriesForAdmin();
    	$courseSubcategory = new CourseSubCategory;
    	return view('courseSubcategory.create', compact('courseSubcategory', 'courseCategories'));
    }

    /**
     *  store sub category
     */
    protected function store(Request $request){
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
        		return Redirect::to('admin/manageCourseSubCategory')->with('message', 'Course sub category created successfully!');
        	}
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return back()->withErrors('something went wrong.');
        }
	   return Redirect::to('admin/manageCourseSubCategory');
    }

    /**
     *  edit sub category
     */
    protected function edit($id){
    	$id = InputSanitise::inputInt(json_decode($id));
    	if(isset($id)){
    		$courseSubcategory = CourseSubCategory::find($id);
    		if(is_object($courseSubcategory)){
                $courseCategories = CourseCategory::getCourseCategoriesForAdmin();
                return view('courseSubcategory.create', compact('courseSubcategory', 'courseCategories'));
    		}
    	}
    	return Redirect::to('admin/manageCourseSubCategory');
    }

    /**
     *  update sub category
     */
    protected function update(Request $request){
    	$v = Validator::make($request->all(), $this->validateCourseSubcategory);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        InputSanitise::deleteCacheByString('vchip:courses*');
        $subCategoryId = InputSanitise::inputInt($request->get('subCategory_id'));
    	if(isset($subCategoryId)){
            DB::beginTransaction();
            try
            {
        		$subcategory = CourseSubCategory::addOrUpdateCourseSubCategory($request, true);
                if(is_object($subcategory)){
                    DB::commit();
                    return Redirect::to('admin/manageCourseSubCategory')->with('message', 'Course sub category updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return back()->withErrors('something went wrong.');
            }
    	}
    	return Redirect::to('admin/manageCourseSubCategory');
    }

    /**
     *  delete sub category
     */
    protected function delete(Request $request){
        InputSanitise::deleteCacheByString('vchip:courses*');
    	$subCategoryId = InputSanitise::inputInt($request->get('subCategory_id'));
    	if(isset($subCategoryId)){
    		$courseSubcategory = CourseSubCategory::find($subCategoryId);
    		if(is_object($courseSubcategory)){
                DB::beginTransaction();
                try
                {
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
                    StudyMaterialSubject::deleteStudyMaterialSubjectsBySubCategoryId($courseSubcategory->id);
                    StudyMaterialTopic::deleteStudyMaterialTopicsBySubCategoryId($courseSubcategory->id);
        			$courseSubcategory->delete();
                    DB::commit();
        			return Redirect::to('admin/manageCourseSubCategory')->with('message', 'Course sub category deleted successfully!');
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return back()->withErrors('something went wrong.');
                }
    		}
    	}
    	return Redirect::to('admin/manageCourseSubCategory');
    }

    protected function isCourseSubCategoryExist(Request $request){
        return CourseSubCategory::isCourseSubCategoryExist($request);
    }
}