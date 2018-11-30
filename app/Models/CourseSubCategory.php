<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use DB,Auth;
use App\Models\CourseCategory;
use App\Models\CourseCourse;
use App\Models\CollegeCategory;

class CourseSubCategory extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'course_category_id','created_for','created_by','created_by_name'];

    /**
     *  create/update course sub category
     */
    protected static function addOrUpdateCourseSubCategory( Request $request, $isUpdate=false){

    	$categoryId = InputSanitise::inputInt($request->get('category'));
    	$subCategoryId = InputSanitise::inputInt($request->get('subCategory_id'));
    	$subCategoryName = InputSanitise::inputString($request->get('subcategory'));

        if( $isUpdate && isset($subCategoryId)){
            $courseSubcategory = static::find($subCategoryId);
            if(!is_object($courseSubcategory)){
            	return 'false';
            }
        } else{
            $courseSubcategory = new static;
        }
        $courseSubcategory->name = $subCategoryName;
		$courseSubcategory->course_category_id = $categoryId;
        if(is_object(Auth::user()) && Auth::user()->college_id > 0){
            $courseSubcategory->created_for = 0;
            $courseSubcategory->created_by = Auth::user()->id;
            $courseSubcategory->created_by_name = Auth::user()->name;
        }
		$courseSubcategory->save();

        return $courseSubcategory;
    }

    /**
     *  return course sub category by categoryId or by userId
     */
    protected static function getCourseSubCategoriesByCategoryId($categoryId, $userId=NULL){
        $categoryId = InputSanitise::inputInt($categoryId);
        $userId = InputSanitise::inputInt($userId);
        if(empty($userId)){
            /**
             *  display subcategories associated with video
             */
    	    return DB::table('course_sub_categories')
                    ->join('course_categories', 'course_categories.id', '=', 'course_sub_categories.course_category_id')
                    ->where('course_sub_categories.created_for', 1)
                    ->where('course_sub_categories.course_category_id', $categoryId)
                    ->select('course_sub_categories.id', 'course_sub_categories.name')
                    ->groupBy('course_sub_categories.id')
                    ->get();
        } else {
            /**
             *  display registered sub categories
             */
            return DB::table('course_sub_categories')
                    ->join('course_categories', 'course_categories.id', '=', 'course_sub_categories.course_category_id')
                    ->join('course_courses', 'course_courses.course_sub_category_id', '=', 'course_sub_categories.id')
                    ->join('register_online_courses', 'register_online_courses.online_course_id', '=', 'course_courses.id')
                    ->where('course_sub_categories.created_for', 1)
                    ->where('register_online_courses.user_id', $userId)
                    ->where('course_sub_categories.course_category_id', $categoryId)
                    ->select('course_sub_categories.id', 'course_sub_categories.name')
                    ->groupBy('course_sub_categories.id')
                    ->get() ;
        }
    }


    /**
     *  return course sub category by categoryId or by userId
     */
    protected static function getCollegeCourseSubCategoriesByCategoryId($categoryId){
        $categoryId = InputSanitise::inputInt($categoryId);
            return static::join('college_categories', 'college_categories.id', '=', 'course_sub_categories.course_category_id')
                    ->where('course_sub_categories.course_category_id', $categoryId)
                    ->where('course_sub_categories.created_for', 0)
                    ->select('course_sub_categories.id', 'course_sub_categories.name')
                    ->groupBy('course_sub_categories.id')
                    ->get();

    }
    /**
     *  return course sub category by collegeId by deptId
     */
    protected static function getCourseSubCategoriesByCollegeIdByDeptIdWithPagination($collegeId, $deptId=NULL){
        $collegeId = InputSanitise::inputInt($collegeId);
        $deptId = InputSanitise::inputInt($deptId);
        $result = static::join('college_categories', 'college_categories.id', '=', 'course_sub_categories.course_category_id')
                ->where('college_categories.college_id', $collegeId);
        if($deptId != NULL){
            $result->where('college_categories.college_dept_id', $deptId);

        }
        return $result->where('course_sub_categories.created_for', 0)->select('course_sub_categories.id', 'course_sub_categories.name','course_sub_categories.created_by_name','college_categories.college_dept_id','college_categories.name as category')
            ->groupBy('course_sub_categories.id')
            ->paginate();
    }

    /**
     *  return course sub category
     */
    protected static function getCourseSubCategoriesWithPagination(){
        return static::join('course_categories', 'course_categories.id', '=', 'course_sub_categories.course_category_id')
            ->where('course_sub_categories.created_for', 1)
            ->select('course_sub_categories.id', 'course_sub_categories.name','course_categories.name as category')
            ->groupBy('course_sub_categories.id')
            ->paginate();
    }

    /**
     *  get category of sub category
     */
    public function category(){
        return $this->belongsTo(CourseCategory::class, 'course_category_id');
    }

    /**
     *  get category of sub category
     */
    public function collegeCategory(){
        return $this->belongsTo(CollegeCategory::class, 'course_category_id');
    }

    public function courses(){
        return $this->hasMany(CourseCourse::class, 'course_sub_category_id');
    }

    protected static function isCourseSubCategoryExist(Request $request){
        $categoryId = InputSanitise::inputInt($request->get('category'));
        $subCategoryName = InputSanitise::inputString($request->get('subcategory'));
        $subcategoryId = InputSanitise::inputInt($request->get('subcategory_id'));

        $loginUser = Auth::guard('web')->user();
        if(is_object($loginUser)){
            $result = static::join('college_categories', 'college_categories.id','=','course_sub_categories.course_category_id')
                ->where('course_sub_categories.course_category_id', $categoryId)->where('course_sub_categories.name', $subCategoryName);
            if(!empty($subcategoryId)){
                $result->where('course_sub_categories.id', '!=', $subcategoryId);
            }
            $result->where('course_sub_categories.created_for', 0)->where('college_categories.college_id', $loginUser->college_id);
        } else {
            $result = static::join('course_categories', 'course_categories.id','=','course_sub_categories.course_category_id')
                ->where('course_sub_categories.course_category_id', $categoryId)->where('course_sub_categories.name', $subCategoryName)->where('course_sub_categories.created_for', 1);
            if(!empty($subcategoryId)){
                $result->where('course_sub_categories.id', '!=', $subcategoryId);
            }
        }

        $result->first();
        if(is_object($result) && 1 == $result->count()){
            return 'true';
        } else {
            return 'false';
        }
        return 'false';
    }

}
