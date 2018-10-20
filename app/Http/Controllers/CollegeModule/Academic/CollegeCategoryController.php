<?php
namespace App\Http\Controllers\CollegeModule\Academic;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use App\Models\CollegeCategory;
use App\Models\User;
use App\Models\TestSubCategory;
use App\Models\UserSolution;
use App\Models\Score;
use App\Models\PaperSection;
use App\Models\CourseSubCategory;
use App\Models\VkitProject;
use App\Models\CourseCourse;
use App\Models\CourseVideo;
use App\Models\TestSubject;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;

class CollegeCategoryController extends Controller
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

    /**
     *  show list of college category
     */
    protected function show($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $loginUser = Auth::guard('web')->user();
    	$collegeCategories = CollegeCategory::getCollegeCategoriesByCollegeIdByDeptIdWithPagination($loginUser->college_id);
    	return view('collegeModule.collegeCategory.list', compact('collegeCategories'));
    }

    /**
     *  show create college category UI
     */
    protected function create($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
		$collegeCategory = new CollegeCategory;
		return view('collegeModule.collegeCategory.create', compact('collegeCategory'));
    }

    /**
     *  store college category
     */
    protected function store($collegeUrl,Request $request){
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
                return Redirect::to('college/'.$collegeUrl.'/manageCollegeCategory')->with('message', 'Category created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return back()->withErrors('something went wrong.');
        }
        return Redirect::to('college/'.$collegeUrl.'/manageCollegeCategory');
    }

    /**
     *  edit college category
     */
    protected function edit($collegeUrl,$id,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
    	$id = InputSanitise::inputInt(json_decode($id));
    	if(isset($id)){
    		$collegeCategory = CollegeCategory::find($id);
    		if(is_object($collegeCategory)){
                $loginUser = Auth::guard('web')->user();
                if(is_object($loginUser) && ($collegeCategory->user_id == $loginUser->id || (User::Hod == $loginUser->user_type || User::Directore == $loginUser->user_type))){
                    return view('collegeModule.collegeCategory.create', compact('collegeCategory'));
                }
    		}
    	}
    	return Redirect::to('college/'.$collegeUrl.'/manageCollegeCategory');
    }

    /**
     *  update college category
     */
    protected function update($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $v = Validator::make($request->all(), $this->validateCollegeCategory);
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
                $category = CollegeCategory::addOrUpdateCollegeCategory($request, true);
                if(is_object($category)){
                    DB::commit();
                    return Redirect::to('college/'.$collegeUrl.'/manageCollegeCategory')->with('message', 'Category updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return back()->withErrors('something went wrong.');
            }
    	}
    	return Redirect::to('college/'.$collegeUrl.'/manageCollegeCategory');
    }

    /**
     *  delete college category
     */
    protected function delete($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $categoryId = InputSanitise::inputInt($request->get('category_id'));

    	if(isset($categoryId)){
    		$collegeCategory = CollegeCategory::find($categoryId);
    		if(is_object($collegeCategory)){
                DB::beginTransaction();
                try
                {
                    $loginUser = Auth::guard('web')->user();
                    if(is_object($loginUser) && ($collegeCategory->user_id == $loginUser->id || (User::Hod == $loginUser->user_type || User::Directore == $loginUser->user_type))){
                        // delete related to course
                        $courseSubCategories = CourseSubCategory::getCollegeCourseSubCategoriesByCategoryId($collegeCategory->id);
                        if(true == is_object($courseSubCategories) && false == $courseSubCategories->isEmpty() ){
                            foreach($courseSubCategories as $subcategory){
                                $subcategoryCourses = CourseCourse::getCollegeCourseByCatIdBySubCatId($collegeCategory->id,$subcategory->id);
                                if(true == is_object($subcategoryCourses) && false == $subcategoryCourses->isEmpty()){
                                    foreach($subcategoryCourses as $course){
                                        $courseVideos = CourseVideo::getCourseVideosByCourseId($course->id);
                                        if(true == is_object($courseVideos) && false == $courseVideos->isEmpty()){
                                            foreach($courseVideos as $video){
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
                        // delete realted to test
                        $testSubCategories = TestSubCategory::getCollegeSubcategoriesByCategoryIdForAdmin($collegeCategory->id);
                        if(true == is_object($testSubCategories) && false == $testSubCategories->isEmpty()){
                            foreach($testSubCategories as $subcategory){
                                $subcategorySubjects = TestSubject::getCollegeSubjectsByCatIdBySubcatid($collegeCategory->id, $subcategory->id);
                                if(true == is_object($subcategorySubjects) && false == $subcategorySubjects->isEmpty()){
                                    foreach($subcategorySubjects as $subject){
                                        if(true == is_object($subject->papers) && false == $subject->papers->isEmpty()){
                                            foreach($subject->papers as $paper){
                                                if(true == is_object($paper->questions) && false == $paper->questions->isEmpty()){
                                                    foreach($paper->questions as $question){
                                                        UserSolution::deleteUserSolutionsByQuestionId($question->id);
                                                        $question->delete();
                                                    }
                                                }
                                                Score::deleteUserScoresByPaperId($paper->id);
                                                PaperSection::deletePaperSectionsByPaperId($paper->id);
                                                $paper->deleteRegisteredPaper();
                                                $paper->delete();
                                            }
                                        }
                                        $subject->delete();
                                    }
                                }
                                $subcategory->deleteSubCategoryImageFolder();
                                $subcategory->delete();
                            }
                        }
                        // delete related to vkit projects
                        $vkitProjects = VkitProject::getCollegeVkitProjectsByCategoryId($collegeCategory->id);
                        if(true == is_object($vkitProjects) && false == $vkitProjects->isEmpty()){
                            foreach($vkitProjects as $project){
                                $project->deleteCommantsAndSubComments();
                                $project->deleteRegisteredProjects();
                                $project->deleteProjectImageFolder();
                                $project->delete();
                            }
                        }
                        Session::put('project_comment_area', 0);
            			$collegeCategory->delete();
                        DB::commit();
            			return Redirect::to('college/'.$collegeUrl.'/manageCollegeCategory')->with('message', 'Category deleted successfully!');
                    }
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return back()->withErrors('something went wrong.');
                }
    		}
    	}
    	return Redirect::to('college/'.$collegeUrl.'/manageCollegeCategory');
    }

    protected function isCollegeCategoryExist(Request $request){
        return CollegeCategory::isCollegeCategoryExist($request);
    }
}