<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB,Auth;
use App\Libraries\InputSanitise;

class CourseCategory extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

    /**
     *  add/update course category
     */
    protected static function addOrUpdateCourseCategory( Request $request, $isUpdate=false){
        $categoryName = InputSanitise::inputString($request->get('category'));
        $categoryId   = InputSanitise::inputInt($request->get('category_id'));
        if( $isUpdate && isset($categoryId)){
            $category = static::find($categoryId);
            if(!is_object($category)){
            	return 'false';
            }
        } else{
            $category = new static;
        }
        $category->name = $categoryName;
        $category->save();
        return $category;
    }

    /**
     *  display only categories who have videos / video associated categories
     */
    protected static function getCategoriesAssocaitedWithVideos(){
        return DB::table('course_categories')
                    ->join('course_sub_categories', 'course_sub_categories.course_category_id','=','course_categories.id')
                    ->join('course_courses', 'course_courses.course_category_id', '=', 'course_categories.id')
                    ->join('course_videos', 'course_videos.course_id', '=', 'course_courses.id')
                    ->where('course_sub_categories.created_for', 1)
                    ->select('course_categories.id', 'course_categories.name')
                    ->groupBy('course_categories.id')
                    ->get();
    }

    /**
     *  get categories by college n dept
     */
    protected static function getCourseCategoriesWithPagination(){
        return static::select('course_categories.id', 'course_categories.name')->paginate();
    }

    /**
     *  get categories by college n dept
     */
    protected static function getCourseCategoriesForAdmin(){
        return static::get();
    }

    public function subcategory(){
        return $this->hasMany(CourseSubCategory::class, 'course_category_id');
    }

    protected static function isCourseCategoryExist(Request $request){
        $category = InputSanitise::inputString($request->get('category'));
        $categoryId   = InputSanitise::inputInt($request->get('category_id'));
        $result = static::where('name', '=',$category);
        if(!empty($categoryId)){
            $result->where('id', '!=', $categoryId);
        }
        $result->first();
        if(is_object($result) && 1 == $result->count()){
            return 'true';
        } else {
            return 'false';
        }
    }
}
