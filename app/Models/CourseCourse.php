<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use DB, Redirect;
use App\Libraries\InputSanitise;
use App\Models\CourseCategory;
use App\Models\CourseSubCategory;
use App\Models\CourseVideo;
use App\Models\RegisterOnlineCourse;
use Intervention\Image\ImageManagerStatic as Image;

class CourseCourse extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'course_category_id', 'course_sub_category_id', 'author', 'author_introduction', 'author_image', 'description', 'price', 'difficulty_level', 'certified', 'image_path','release_date'];

    /**
     *  create/update course
     */
    protected static function addOrUpdateCourse(Request $request, $isUpdate = false){
    	$categoryId = InputSanitise::inputInt($request->get('category'));
        $subcategoryId = InputSanitise::inputInt($request->get('subcategory'));
        $courseName = InputSanitise::inputString($request->get('course'));
        $author = InputSanitise::inputString($request->get('author'));
        $authorIntroduction = InputSanitise::inputString($request->get('author_introduction'));
        $description = InputSanitise::inputString($request->get('description'));
        $price = trim(strip_tags($request->get('price')));
        $difficultyLevel = InputSanitise::inputString($request->get('difficulty_level'));
        $certified = InputSanitise::inputString($request->get('certified'));
        $release_date = trim(strip_tags($request->get('release_date')));
        $courseId = InputSanitise::inputString($request->get('course_id'));

    	if( $isUpdate && !empty($courseId)){
    		$course = static::find($courseId);
    		if(!is_object($course)){
    			return Redirect::to('admin/manageCourseCourse');
    		}
    	} else {
    		$course = new static;
    	}
    	$course->name = $courseName;
    	$course->course_category_id = $categoryId;
    	$course->course_sub_category_id = $subcategoryId;
    	$course->author = $author;
        $course->author_introduction = $authorIntroduction;
    	$course->price = $price;
    	$course->description = $description;
    	$course->difficulty_level = $difficultyLevel;
        $course->certified = $certified;
        if($request->exists('author_image')){
            $authorImage = $request->file('author_image')->getClientOriginalName();
            $courseImageFolder = "courseImages/";

            $courseFolderPath = $courseImageFolder.str_replace(' ', '_', $courseName);
            if(!is_dir($courseFolderPath)){
                mkdir($courseFolderPath, 0755, true);
            }
            $authorImagePath = $courseFolderPath ."/". $authorImage;
            if(file_exists($authorImagePath)){
                unlink($authorImagePath);
            } elseif(!empty($course->id) && file_exists($course->author_image)){
                unlink($course->author_image);
            }
            $request->file('author_image')->move($courseFolderPath, $authorImage);
            $course->author_image = $authorImagePath;
            // open image
            $img = Image::make($course->author_image);
            // enable interlacing
            $img->interlace(true);
            // save image interlaced
            $img->save();
        }

        if($request->exists('image_path')){
            $courseImage = $request->file('image_path')->getClientOriginalName();
            $courseImageFolder = "courseImages/";

            $courseFolderPath = $courseImageFolder.str_replace(' ', '_', $courseName);
            if(!is_dir($courseFolderPath)){
                mkdir($courseFolderPath, 0755, true);
            }
            $courseImagePath = $courseFolderPath ."/". $courseImage;
            if(file_exists($courseImagePath)){
                unlink($courseImagePath);
            } elseif(!empty($course->id) && file_exists($course->image_path)){
                unlink($course->image_path);
            }
            $request->file('image_path')->move($courseFolderPath, $courseImage);
            $course->image_path = $courseImagePath;
             // open image
            $img = Image::make($course->image_path);
            // enable interlacing
            $img->interlace(true);
            // save image interlaced
            $img->save();
        }

        $course->release_date = $release_date;
    	$course->save();
    	return $course;

    }

    /**
     *  display courses associated with videos
     */
    protected static function getCourseAssocaitedWithVideos(){
        return DB::table('course_courses')
                ->join('course_videos', 'course_videos.course_id', '=', 'course_courses.id')
                ->join('course_sub_categories', 'course_sub_categories.id', '=', 'course_courses.course_sub_category_id')
                ->join('course_categories', 'course_categories.id', '=', 'course_courses.course_category_id')
                ->select('course_courses.id','course_courses.*', 'course_sub_categories.name as subcategory', 'course_categories.name as category')
                ->groupBy('course_courses.id')
                ->paginate(9);
    }

    /**
     *  return courses by categoryId by sub categoryId
     */
    protected static function getCourseByCatIdBySubCatId($categoryId,$subcategoryId,$userId=NULL){
        $categoryId = InputSanitise::inputInt($categoryId);
        $subcategoryId = InputSanitise::inputInt($subcategoryId);
        $userId = InputSanitise::inputInt($userId);
        if(!empty($userId)){
            /**
             *  return registered courses by category and sub category
             */
            return DB::table('course_courses')
                    ->join('course_categories', 'course_categories.id', '=', 'course_courses.course_category_id')
                    ->join('course_sub_categories', 'course_sub_categories.id', '=', 'course_courses.course_sub_category_id')
                    ->join('register_online_courses', 'register_online_courses.online_course_id', '=', 'course_courses.id')
                    ->where('course_courses.course_category_id', $categoryId)
                    ->where('course_courses.course_sub_category_id', $subcategoryId)
                    ->where('register_online_courses.user_id', $userId)
                    ->select('course_courses.*', 'course_sub_categories.name as subcategory', 'course_categories.name as category', 'register_online_courses.grade as grade')
                    ->groupBy('course_courses.id')
                    ->get() ;
        } else {
            /**
             *  display courses associated with videos by category and sub category
             */
            return DB::table('course_courses')
                    ->join('course_categories', 'course_categories.id', '=', 'course_courses.course_category_id')
                    ->join('course_sub_categories', 'course_sub_categories.id', '=', 'course_courses.course_sub_category_id')
                    ->join('course_videos', 'course_videos.course_id', '=', 'course_courses.id')
                    ->where('course_courses.course_category_id', $categoryId)
                    ->where('course_courses.course_sub_category_id', $subcategoryId)
                    ->select('course_courses.id','course_courses.*', 'course_sub_categories.name as subcategory', 'course_categories.name as category')
                    ->groupBy('course_courses.id')
                    ->get() ;
        }
    }

    /**
     *  return courses associated with videos by filter array
     */
    protected static function getOnlineCourseBySearchArray(Request $request){
        $searchFilter = json_decode($request->get('arr'),true);
        $difficulty = $searchFilter['difficulty'];
        $certified = $searchFilter['certified'];
        $fees = $searchFilter['fees'];
        $startingsoon = InputSanitise::inputInt($searchFilter['startingsoon']);
        $latest = InputSanitise::inputInt($searchFilter['latest']);
        $categoryId = InputSanitise::inputInt($searchFilter['categoryId']);
        $subcategoryId = InputSanitise::inputInt($searchFilter['subcategoryId']);

        $results = DB::table('course_courses')
                    ->join('course_videos', 'course_videos.course_id', '=', 'course_courses.id')
                    ->join('course_categories', 'course_categories.id', '=', 'course_courses.course_category_id')
                    ->join('course_sub_categories', 'course_sub_categories.id', '=', 'course_courses.course_sub_category_id');

        if(count($difficulty) > 0){
            $results->whereIn('difficulty_level', $difficulty);
        }
        if(count($certified) > 0){
            $results->whereIn('certified', $certified);
        }
        if(count($fees) > 0 && isset($fees[0])){
            if(1 == $fees[0]){
                $results->where('price', '>', 0);
            } else {
                $results->where('price', '=', 0);
            }
        }
        if( 1 == $startingsoon){
            $currentDate = date('Y-m-d H:i:s');
            $nextDate = date('Y-m-d H:i:s', strtotime("+30 days"));
            $results->whereBetween('release_date',[$currentDate,$nextDate]);
        }
        if( 1 == $latest ){
            $currentDate = date('Y-m-d H:i:s');
            $previousDate = date('Y-m-d H:i:s', strtotime("-30 days"));
            $results->whereBetween('release_date',[$previousDate, $currentDate]);
        }
        if(!empty($categoryId)){
            $results->where('course_courses.course_category_id', $categoryId);
        }
        if(!empty($subcategoryId)){
            $results->where('course_courses.course_sub_category_id', $subcategoryId);
        }
        return $results->select('course_courses.id','course_courses.*', 'course_sub_categories.name As subcategory', 'course_categories.name as category')
                ->groupBy('course_courses.id')
                ->get();
    }

    /**
     *  get registered online courses for user
     */
    protected static function getRegisteredOnlineCourses($userId){
        $userId = InputSanitise::inputInt($userId);
        return DB::table('course_courses')
                ->join('register_online_courses', 'register_online_courses.online_course_id', '=', 'course_courses.id')
                ->join('course_categories', 'course_categories.id', '=', 'course_courses.course_category_id')
                ->join('course_sub_categories', 'course_sub_categories.id', '=', 'course_courses.course_sub_category_id')
                ->where('register_online_courses.user_id', $userId)
                ->select('course_courses.*', 'course_categories.name as category', 'course_sub_categories.name as subCategory', 'register_online_courses.grade as grade')
                ->get();
    }

    /**
     *  get registered online courses for user by category n sub category
     */
    protected static function getOnlineCoursesByUserIdByCategoryBySubCategory($userId,$category,$subcategory){
        $userId = InputSanitise::inputInt($userId);
        $result = DB::table('course_courses')
                ->join('register_online_courses', 'register_online_courses.online_course_id', '=', 'course_courses.id')
                ->join('course_categories', 'course_categories.id', '=', 'course_courses.course_category_id')
                ->join('course_sub_categories', 'course_sub_categories.id', '=', 'course_courses.course_sub_category_id');
        if($userId > 0)  {
            $result->where('register_online_courses.user_id', $userId);
        }
        if($category > 0)  {
            $result->where('course_categories.id', $category);
        }
        if($subcategory > 0)  {
            $result->where('course_sub_categories.id', $subcategory);
        }
        return $result->select('course_courses.*', 'course_categories.name as category', 'course_sub_categories.name as subCategory', 'register_online_courses.grade as grade')->get();
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
    public function subcategory(){
        return $this->belongsTo(CourseSubCategory::class, 'course_sub_category_id');
    }

    public function videos(){
        return $this->hasMany(CourseVideo::class, 'course_id');
    }

    public function deleteRegisteredCourses(){
        $registeredCourses = RegisterOnlineCourse::where('online_course_id', $this->id)->get();
        if(is_object($registeredCourses) && false == $registeredCourses->isEmpty()){
            foreach($registeredCourses as $registeredCourse){
                $registeredCourse->delete();
            }
        }
    }

    public function deleteCourseImageFolder(){
        $courseImageFolder = "courseImages/".str_replace(' ', '_', $this->name);
        if(is_dir($courseImageFolder)){
            InputSanitise::delFolder($courseImageFolder);
        }
    }

    protected static function isCourseCourseExist($request){
        $categoryId = InputSanitise::inputInt($request->get('category'));
        $subcategoryId = InputSanitise::inputInt($request->get('subcategory'));
        $courseName = InputSanitise::inputString($request->get('course'));
        $courseId = InputSanitise::inputInt($request->get('course_id'));
        $result = static::where('course_category_id', $categoryId)->where('course_sub_category_id', $subcategoryId)->where('name', $courseName);
        if(!empty($courseId)){
            $result->where('id', '!=', $courseId);
        }
        $result->first();
        if(is_object($result) && 1 == $result->count()){
            return 'true';
        } else {
            return 'false';
        }
        return 'false';
    }

    protected static function getCourseByCatIdBySubCatIdForAdmin($categoryId,$subcategoryId){
        return static::where('course_category_id', $categoryId)->where('course_sub_category_id', $subcategoryId)->get();
    }

}
