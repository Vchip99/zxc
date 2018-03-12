<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use DB;
use App\Models\CourseCategory;
use App\Models\CourseCourse;

class CourseSubCategory extends Model
{
	public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'course_category_id'];

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
            	return Redirect::to('admin/manageCourseSubCategory');
            }
        } else{
            $courseSubcategory = new static;
        }
        $courseSubcategory->name = $subCategoryName;
		$courseSubcategory->course_category_id = $categoryId;
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
                    ->where('register_online_courses.user_id', $userId)
                    ->where('course_sub_categories.course_category_id', $categoryId)
                    ->select('course_sub_categories.id', 'course_sub_categories.name')
                    ->groupBy('course_sub_categories.id')
                    ->get() ;
        }
    }

    /**
     *  get category of sub category
     */
    public function category(){
        return $this->belongsTo(CourseCategory::class, 'course_category_id');
    }

    public function courses(){
        return $this->hasMany(CourseCourse::class, 'course_sub_category_id');
    }

    protected static function isCourseSubCategoryExist(Request $request){
        $categoryId = InputSanitise::inputInt($request->get('category'));
        $subCategoryName = InputSanitise::inputString($request->get('subcategory'));
        $subcategoryId = InputSanitise::inputInt($request->get('subcategory_id'));
        $result = static::where('course_category_id', $categoryId)->where('name', $subCategoryName);
        if(!empty($subcategoryId)){
            $result->where('id', '!=', $subcategoryId);
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
