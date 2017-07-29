<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB;
use App\Libraries\InputSanitise;

class CourseCategory extends Model
{
	public $timestamps = false;
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
            	return Redirect::to('admin/manageCategory');
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
                    ->join('course_courses', 'course_courses.course_category_id', '=', 'course_categories.id')
                    ->join('course_videos', 'course_videos.course_id', '=', 'course_courses.id')
                    ->select('course_categories.id', 'course_categories.name')
                    ->groupBy('course_categories.id')
                    ->get();
    }

    public function subcategory(){
        return $this->hasMany(CourseSubCategory::class, 'course_category_id');
    }
}