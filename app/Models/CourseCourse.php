<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use DB, Redirect,Auth;
use App\Libraries\InputSanitise;
use App\Models\CourseCategory;
use App\Models\CollegeCategory;
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
    protected $fillable = ['name', 'course_category_id', 'course_sub_category_id', 'author', 'author_introduction', 'author_image', 'description', 'price', 'difficulty_level', 'certified', 'image_path','release_date','created_by','admin_id','admin_approve'];

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
    			return 'false';
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

            if(in_array($request->file('author_image')->getClientMimeType(), ['image/jpg', 'image/jpeg', 'image/png'])){
                // open image
                $img = Image::make($course->author_image);
                // enable interlacing
                $img->interlace(true);
                // save image interlaced
                $img->save();
            }
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
        if(is_object(Auth::user()) && Auth::user()->college_id > 0){
            $course->created_by = Auth::user()->id;
            $course->admin_id = '';
        }
        if(is_object(Auth::guard('admin')->user())){
            $course->created_by = 0;
            $course->admin_id = Auth::guard('admin')->user()->id;
        }
        $course->release_date = $release_date;
    	$course->save();
    	return $course;
    }

    /**
     *  display courses associated with videos
     */
    protected static function getCourseAssocaitedWithVideosWithPagination(){
        if(is_object(Auth::user()) && 'ceo@vchiptech.com' == Auth::user()->email){
            return  DB::table('course_courses')
                ->join('course_videos', 'course_videos.course_id', '=', 'course_courses.id')
                ->join('course_sub_categories', 'course_sub_categories.id', '=', 'course_courses.course_sub_category_id')
                ->join('course_categories', 'course_categories.id', '=', 'course_courses.course_category_id')
                ->where('course_sub_categories.created_for', 1)
                ->select('course_courses.id','course_courses.*', 'course_sub_categories.name as subcategory', 'course_categories.name as category')
                ->groupBy('course_courses.id')
                ->paginate(9);
        } else {
            return  DB::table('course_courses')
                ->join('course_videos', 'course_videos.course_id', '=', 'course_courses.id')
                ->join('course_sub_categories', 'course_sub_categories.id', '=', 'course_courses.course_sub_category_id')
                ->join('course_categories', 'course_categories.id', '=', 'course_courses.course_category_id')
                ->where('course_sub_categories.created_for', 1)
                ->where('course_courses.admin_approve', 1)
                ->select('course_courses.id','course_courses.*', 'course_sub_categories.name as subcategory', 'course_categories.name as category')
                ->groupBy('course_courses.id')
                ->paginate(9);
        }
    }

    /**
     *  couses by collegeId by deptId
     */
    protected static function getCoursesByCollegeIdByDeptIdWithPagination($collegeId,$deptId=NULL){
        $loginUser = Auth::user();
        $result = static::join('users','users.id','=','course_courses.created_by')
                ->join('college_categories', 'college_categories.id', '=', 'course_courses.course_category_id')
                ->join('course_sub_categories', 'course_sub_categories.id', '=', 'course_courses.course_sub_category_id')
                ->where('college_categories.college_id', '=', $collegeId);
        if(NULL != $deptId){
            $result->where('college_categories.college_dept_id', '=', $deptId);
        }
        if(User::TNP == $loginUser->user_type){
            $result->where('course_courses.created_by', $loginUser->id);
        }

        return $result->where('course_sub_categories.created_for', 0)->select('course_courses.id','course_courses.*', 'course_sub_categories.name as subcategory', 'college_categories.name as category','college_categories.college_dept_id','users.name as user')
                ->groupBy('course_courses.id')
                ->paginate();
    }

    protected static function getCoursesByCollegeIdByAssignedDeptsWithPagination($collegeId){
        $loginUser = Auth::user();
        $result = static::join('users','users.id','=','course_courses.created_by')
                ->join('college_categories', 'college_categories.id', '=', 'course_courses.course_category_id')
                ->join('course_sub_categories', 'course_sub_categories.id', '=', 'course_courses.course_sub_category_id')
                ->where('users.college_id', $collegeId);
        if(User::Lecturer == $loginUser->user_type){
            $result->where('course_courses.created_by', $loginUser->id);
        } else {
            $result->where(function($query) use($loginUser){
                $query->where('users.user_type', User::Lecturer);
                $query->orWhere('users.id',$loginUser->id);
            })
            ->where('course_courses.created_by', '>', 0)->whereIn('users.college_dept_id', explode(',',$loginUser->assigned_college_depts));
        }
        return $result->where('course_sub_categories.created_for', 0)->select('course_courses.id','course_courses.*', 'course_sub_categories.name as subcategory', 'college_categories.name as category','college_categories.college_dept_id','users.name as user')
                ->groupBy('course_courses.id')
                ->paginate();
    }

    protected static function getCoursesByCollegeIdByAssignedDepts($collegeId){
        $loginUser = Auth::user();
        $result = static::join('users','users.id','=','course_courses.created_by')
                ->join('college_categories', 'college_categories.id', '=', 'course_courses.course_category_id')
                ->join('course_sub_categories', 'course_sub_categories.id', '=', 'course_courses.course_sub_category_id')
                ->where('users.college_id', $collegeId);
        if(User::Lecturer == $loginUser->user_type){
            $result->where('course_courses.created_by', $loginUser->id);
        } else {
            $result->where(function($query) use($loginUser){
                $query->where('users.user_type', User::Lecturer);
                $query->orWhere('users.id',$loginUser->id);
            })
            ->where('course_courses.created_by', '>', 0)->whereIn('users.college_dept_id', explode(',',$loginUser->assigned_college_depts));
        }
        return $result->where('course_sub_categories.created_for', 0)->select('course_courses.id','course_courses.*', 'course_sub_categories.name as subcategory', 'college_categories.name as category','college_categories.college_dept_id')
                ->groupBy('course_courses.id')
                ->get();
    }

    /**
     *  couses
     */
    protected static function getCoursesWithPaginationForAdmin(){
        $result = static::join('course_categories', 'course_categories.id', '=', 'course_courses.course_category_id')
            ->join('course_sub_categories', 'course_sub_categories.id', '=', 'course_courses.course_sub_category_id')
            ->join('admins','admins.id','=', 'course_courses.admin_id')
            ->where('course_sub_categories.created_for', 1);
        if(is_object(Auth::guard('admin')->user()) && Auth::guard('admin')->user()->hasRole('sub-admin')){
            $result->where('course_courses.admin_id', Auth::guard('admin')->user()->id);
        }
        return $result->select('course_courses.id','course_courses.*', 'course_sub_categories.name as subcategory', 'course_categories.name as category','admins.name as admin')
            ->groupBy('course_courses.id')
            ->paginate();
    }

    /**
     *  couses
     */
    protected static function getCoursesWithPagination(){
        $result = static::join('course_categories', 'course_categories.id', '=', 'course_courses.course_category_id')
            ->join('course_sub_categories', 'course_sub_categories.id', '=', 'course_courses.course_sub_category_id')
            ->where('course_sub_categories.created_for', 1)
            ->where('course_courses.admin_approve', 1);
        if(is_object(Auth::guard('admin')->user()) && Auth::guard('admin')->user()->hasRole('sub-admin')){
            $result->where('course_courses.admin_id', Auth::guard('admin')->user()->id);
        }
        return $result->select('course_courses.id','course_courses.*', 'course_sub_categories.name as subcategory', 'course_categories.name as category')
                ->groupBy('course_courses.id')
                ->paginate();
    }

    /**
     *  purchased couses
     */
    protected static function getPurchasedCourses($adminId = NULL){
        $result = static::join('course_categories', 'course_categories.id', '=', 'course_courses.course_category_id')
                ->join('course_sub_categories', 'course_sub_categories.id', '=', 'course_courses.course_sub_category_id')
                ->join('register_online_courses', 'register_online_courses.online_course_id','=','course_courses.id')
                ->where('course_sub_categories.created_for', 1)
                ->where('register_online_courses.price','>', 0);
        if(is_object(Auth::guard('admin')->user()) && Auth::guard('admin')->user()->hasRole('sub-admin')){
            $result->where('course_courses.admin_id', Auth::guard('admin')->user()->id);
        } else {
            if($adminId > 0){
                $result->where('course_courses.admin_id', $adminId);
            }
        }
        return $result->select('register_online_courses.id','course_courses.name','course_courses.price', 'course_sub_categories.name as subcategory', 'course_categories.name as category','register_online_courses.updated_at','register_online_courses.user_id','course_courses.admin_id')
                ->groupBy('register_online_courses.id')
                ->get();
    }

    /**
     *  purchased couse by id
     */
    protected static function getPurchasedCourseById($courseId){
        $result = static::join('course_categories', 'course_categories.id', '=', 'course_courses.course_category_id')
                ->join('course_sub_categories', 'course_sub_categories.id', '=', 'course_courses.course_sub_category_id')
                ->join('register_online_courses', 'register_online_courses.online_course_id','=','course_courses.id')
                ->where('course_sub_categories.created_for', 1)
                ->where('register_online_courses.price','>', 0)
                ->whereNotNull('register_online_courses.payment_id')
                ->whereNotNull('register_online_courses.payment_request_id')
                ->where('register_online_courses.id',$courseId);
        if(is_object(Auth::guard('admin')->user()) && Auth::guard('admin')->user()->hasRole('sub-admin')){
            $result->where('course_courses.admin_id', Auth::guard('admin')->user()->id);
        }
        return $result->select('register_online_courses.id','course_courses.name','course_courses.price','register_online_courses.updated_at','register_online_courses.user_id')
                ->first();
    }

    /**
     *  couses by collegeId by deptId
     */
    protected static function getCoursesAssociatedWithVideosByCollegeIdByDeptId($collegeId,$deptId=NULL){
        $result = static::join('college_categories', 'college_categories.id', '=', 'course_courses.course_category_id')
                ->join('course_sub_categories', 'course_sub_categories.id', '=', 'course_courses.course_sub_category_id')
                ->join('course_videos', 'course_videos.course_id', '=', 'course_courses.id')
                ->where('college_categories.college_id', '=', $collegeId)
                ->where('course_sub_categories.created_for', 0);
        if(NULL != $deptId){
            $result->where('college_categories.college_dept_id', '=', $deptId);
        }

        return $result->select('course_courses.id','course_courses.*', 'course_sub_categories.name as subcategory', 'college_categories.name as category')
                ->groupBy('course_courses.id')
                ->get();
    }

    /**
     *  couses by collegeId by deptId
     */
    protected static function getCoursesByUserIdByCollegeIdByDeptId($userId,$collegeId,$deptId=NULL){
        $result = static::join('college_categories', 'college_categories.id', '=', 'course_courses.course_category_id')
                ->join('course_sub_categories', 'course_sub_categories.id', '=', 'course_courses.course_sub_category_id')
                ->join('course_videos', 'course_videos.course_id', '=', 'course_courses.id')
                ->where('college_categories.college_id', '=', $collegeId)
                ->where('course_courses.created_by', '=', $userId)
                ->where('course_sub_categories.created_for', 0);
        if(NULL != $deptId){
            $result->where('college_categories.college_dept_id', '=', $deptId);
        }

        return $result->select('course_courses.id','course_courses.name as course', 'course_sub_categories.name as subcategory', 'college_categories.name as category')
                ->groupBy('course_courses.id')
                ->get();
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
            if(is_object(Auth::user()) && 'ceo@vchiptech.com' == Auth::user()->email){
                return DB::table('course_courses')
                    ->join('course_categories', 'course_categories.id', '=', 'course_courses.course_category_id')
                    ->join('course_sub_categories', 'course_sub_categories.id', '=', 'course_courses.course_sub_category_id')
                    ->join('register_online_courses', 'register_online_courses.online_course_id', '=', 'course_courses.id')
                    ->where('course_courses.course_category_id', $categoryId)
                    ->where('course_courses.course_sub_category_id', $subcategoryId)
                    ->where('register_online_courses.user_id', $userId)
                    ->where('course_sub_categories.created_for', 1)
                    ->select('course_courses.*', 'course_sub_categories.name as subcategory', 'course_categories.name as category', 'register_online_courses.grade as grade')
                    ->groupBy('course_courses.id')
                    ->get() ;
            } else {
                return DB::table('course_courses')
                    ->join('course_categories', 'course_categories.id', '=', 'course_courses.course_category_id')
                    ->join('course_sub_categories', 'course_sub_categories.id', '=', 'course_courses.course_sub_category_id')
                    ->join('register_online_courses', 'register_online_courses.online_course_id', '=', 'course_courses.id')
                    ->where('course_courses.course_category_id', $categoryId)
                    ->where('course_courses.course_sub_category_id', $subcategoryId)
                    ->where('register_online_courses.user_id', $userId)
                    ->where('course_sub_categories.created_for', 1)
                    ->where('course_courses.admin_approve', 1)
                    ->select('course_courses.*', 'course_sub_categories.name as subcategory', 'course_categories.name as category', 'register_online_courses.grade as grade')
                    ->groupBy('course_courses.id')
                    ->get() ;
            }
        } else {
            /**
             *  display courses associated with videos by category and sub category
             */
            if(is_object(Auth::user()) && 'ceo@vchiptech.com' == Auth::user()->email){
                return DB::table('course_courses')
                    ->join('course_categories', 'course_categories.id', '=', 'course_courses.course_category_id')
                    ->join('course_sub_categories', 'course_sub_categories.id', '=', 'course_courses.course_sub_category_id')
                    ->join('course_videos', 'course_videos.course_id', '=', 'course_courses.id')
                    ->where('course_courses.course_category_id', $categoryId)
                    ->where('course_courses.course_sub_category_id', $subcategoryId)
                    ->where('course_sub_categories.created_for', 1)
                    ->select('course_courses.id','course_courses.*', 'course_sub_categories.name as subcategory', 'course_categories.name as category')
                    ->groupBy('course_courses.id')
                    ->get() ;
            } else {
                return DB::table('course_courses')
                    ->join('course_categories', 'course_categories.id', '=', 'course_courses.course_category_id')
                    ->join('course_sub_categories', 'course_sub_categories.id', '=', 'course_courses.course_sub_category_id')
                    ->join('course_videos', 'course_videos.course_id', '=', 'course_courses.id')
                    ->where('course_courses.course_category_id', $categoryId)
                    ->where('course_courses.course_sub_category_id', $subcategoryId)
                    ->where('course_sub_categories.created_for', 1)
                    ->where('course_courses.admin_approve', 1)
                    ->select('course_courses.id','course_courses.*', 'course_sub_categories.name as subcategory', 'course_categories.name as category')
                    ->groupBy('course_courses.id')
                    ->get() ;
            }
        }
    }

    /**
     *  return courses by categoryId by sub categoryId
     */
    protected static function getCollegeCourseByCatIdBySubCatId($categoryId,$subcategoryId){
        $categoryId = InputSanitise::inputInt($categoryId);
        $subcategoryId = InputSanitise::inputInt($subcategoryId);

        /**
         *  display courses associated with videos by category and sub category
         */
        return static::join('college_categories', 'college_categories.id', '=', 'course_courses.course_category_id')
                ->join('course_sub_categories', 'course_sub_categories.id', '=', 'course_courses.course_sub_category_id')
                ->join('course_videos', 'course_videos.course_id', '=', 'course_courses.id')
                ->where('course_courses.course_category_id', $categoryId)
                ->where('course_courses.course_sub_category_id', $subcategoryId)
                ->where('course_sub_categories.created_for', 0)
                ->select('course_courses.id','course_courses.*', 'course_sub_categories.name as subcategory', 'college_categories.name as category')
                ->groupBy('course_courses.id')
                ->get();
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
        if(is_object(Auth::user()) && 'ceo@vchiptech.com' == Auth::user()->email){
            $results = DB::table('course_courses')
                    ->join('course_videos', 'course_videos.course_id', '=', 'course_courses.id')
                    ->join('course_categories', 'course_categories.id', '=', 'course_courses.course_category_id')
                    ->join('course_sub_categories', 'course_sub_categories.id', '=', 'course_courses.course_sub_category_id');
        } else {
            $results = DB::table('course_courses')
                    ->join('course_videos', 'course_videos.course_id', '=', 'course_courses.id')
                    ->join('course_categories', 'course_categories.id', '=', 'course_courses.course_category_id')
                    ->join('course_sub_categories', 'course_sub_categories.id', '=', 'course_courses.course_sub_category_id')
                    ->where('course_courses.admin_approve', 1);
        }

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
        $result = DB::table('course_courses')
                ->join('course_videos', 'course_videos.course_id', '=', 'course_courses.id')
                ->join('course_categories', 'course_categories.id', '=', 'course_courses.course_category_id')
                ->join('course_sub_categories', 'course_sub_categories.id', '=', 'course_courses.course_sub_category_id')
                ->where('course_sub_categories.created_for', 1);

        return $result->select('course_courses.*', 'course_categories.name as category', 'course_sub_categories.name as subCategory')->groupBy('course_courses.id')->get();
    }

    /**
     *  get registered online courses for user
     */
    protected static function myVchipFavouriteCourses(){
        $userId = Auth::user()->id;
        return DB::table('course_courses')
                ->join('register_online_courses', 'register_online_courses.online_course_id', '=', 'course_courses.id')
                ->join('course_videos', 'course_videos.course_id', '=', 'course_courses.id')
                ->join('course_categories', 'course_categories.id', '=', 'course_courses.course_category_id')
                ->join('course_sub_categories', 'course_sub_categories.id', '=', 'course_courses.course_sub_category_id')
                ->where('course_sub_categories.created_for', 1)
                ->where('register_online_courses.user_id', $userId)
                ->where('course_courses.admin_approve', 1)
                ->select('course_courses.*', 'course_categories.name as category', 'course_sub_categories.name as subCategory')->groupBy('course_courses.id')->get();
    }

    /**
     *  get registered online courses for user
     */
    protected static function getRegisteredCollegeOnlineCourses($userId){
        $userId = InputSanitise::inputInt($userId);
        $result = DB::table('course_courses')
                ->join('course_videos', 'course_videos.course_id', '=', 'course_courses.id')
                ->join('college_categories', 'college_categories.id', '=', 'course_courses.course_category_id')
                ->join('course_sub_categories', 'course_sub_categories.id', '=', 'course_courses.course_sub_category_id')
                ->where('course_sub_categories.created_for', 0);

        return $result->select('course_courses.*', 'college_categories.name as category', 'course_sub_categories.name as subCategory')->groupBy('course_courses.id')->get();
    }

    /**
     *  get registered online courses for user
     */
    protected static function myCollegeFavouriteCourses(){
        $userId = Auth::user()->id;
        return DB::table('course_courses')
                ->join('register_online_courses', 'register_online_courses.online_course_id', '=', 'course_courses.id')
                ->join('course_videos', 'course_videos.course_id', '=', 'course_courses.id')
                ->join('college_categories', 'college_categories.id', '=', 'course_courses.course_category_id')
                ->join('course_sub_categories', 'course_sub_categories.id', '=', 'course_courses.course_sub_category_id')
                ->where('course_sub_categories.created_for', 0)
                ->where('register_online_courses.user_id', $userId)
                ->select('course_courses.*', 'college_categories.name as category', 'course_sub_categories.name as subCategory')->groupBy('course_courses.id')->get();
    }

    /**
     *  get registered online courses for user by category n sub category
     */
    protected static function getOnlineCoursesByUserIdByCategoryBySubCategory($userId,$category,$subcategory){
        $userId = InputSanitise::inputInt($userId);
        $result = DB::table('course_courses')
                ->join('register_online_courses', 'register_online_courses.online_course_id', '=', 'course_courses.id')
                ->join('course_categories', 'course_categories.id', '=', 'course_courses.course_category_id')
                ->join('course_sub_categories', 'course_sub_categories.id', '=', 'course_courses.course_sub_category_id')
                ->where('course_sub_categories.created_for', 1);

        if($userId > 0)  {
            $result->where('register_online_courses.user_id', $userId);
        }
        if($category > 0)  {
            $result->where('course_categories.id', $category);
        }
        if($subcategory > 0)  {
            $result->where('course_sub_categories.id', $subcategory);
        }
        return $result->select('course_courses.*', 'course_categories.name as category', 'course_sub_categories.name as subCategory', 'register_online_courses.grade as grade')->groupBy('course_courses.id')->get();
    }

    /**
     *  get registered online courses for user by category n sub category
     */
    protected static function getOnlineCollegeCoursesByUserIdByCategoryBySubCategory($userId,$category,$subcategory){
        $userId = InputSanitise::inputInt($userId);
        $result = DB::table('course_courses')
                ->join('register_online_courses', 'register_online_courses.online_course_id', '=', 'course_courses.id')
                ->join('college_categories', 'college_categories.id', '=', 'course_courses.course_category_id')
                ->join('course_sub_categories', 'course_sub_categories.id', '=', 'course_courses.course_sub_category_id')
                ->where('course_sub_categories.created_for', 0);

        if($userId > 0)  {
            $result->where('register_online_courses.user_id', $userId);
        }
        if($category > 0)  {
            $result->where('college_categories.id', $category);
        }
        if($subcategory > 0)  {
            $result->where('course_sub_categories.id', $subcategory);
        }
        return $result->select('course_courses.*', 'college_categories.name as category', 'course_sub_categories.name as subCategory', 'register_online_courses.grade as grade')->groupBy('course_courses.id')->get();
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

    /**
     *  get category of sub category
     */
    public function subcategory(){
        return $this->belongsTo(CourseSubCategory::class, 'course_sub_category_id');
    }

    public function videos(){
        return $this->hasMany(CourseVideo::class, 'course_id');
    }

    /**
     *  get user
     */
    public function getUser(){
        $user = User::find($this->user_id);
        if(is_object($user)){
            return $user->name;
        }
        return;
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

        $loginUser = Auth::guard('web')->user();
        if(is_object($loginUser)){
            $result = static::join('college_categories', 'college_categories.id','=','course_courses.course_category_id')
                ->join('course_sub_categories', 'course_sub_categories.id','=','course_courses.course_sub_category_id')
                ->where('course_courses.course_category_id', $categoryId)
                ->where('course_courses.course_sub_category_id', $subcategoryId)
                ->where('course_courses.name', $courseName);
            if(!empty($courseId)){
                $result->where('course_courses.id', '!=', $courseId);
            }
            $result->where('course_sub_categories.created_for', 0)->where('college_categories.college_id', $loginUser->college_id);
        } else {
            $result = static::join('course_categories', 'course_categories.id','=','course_courses.course_category_id')
                ->join('course_sub_categories', 'course_sub_categories.id','=','course_courses.course_sub_category_id')
                ->where('course_courses.course_category_id', $categoryId)
                ->where('course_courses.course_sub_category_id', $subcategoryId)
                ->where('course_courses.name', $courseName)->where('course_sub_categories.created_for', 1);
            if(!empty($courseId)){
                $result->where('course_courses.id', '!=', $courseId);
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

    protected static function getCourseByCatIdBySubCatIdForAdmin($categoryId,$subcategoryId){
        return static::where('course_category_id', $categoryId)->where('course_sub_category_id', $subcategoryId)->where('admin_id', Auth::guard('admin')->user()->id)->get();
    }

    protected static function getCourseByCatIdBySubCatIdByUser($categoryId,$subcategoryId){
        $loginUser = Auth::guard('web')->user();
        return static::where('course_category_id', $categoryId)->where('course_sub_category_id', $subcategoryId)->where('created_by', $loginUser->id)->get();
    }

    /**
     *  get online courses by course id
     */
    protected static function getOnlineCourseByCourseIdByCollegeId($courseId,$collegeId){
        $courseId = InputSanitise::inputInt($courseId);
        return DB::table('course_courses')
                ->join('college_categories', 'college_categories.id', '=', 'course_courses.course_category_id')
                ->join('course_sub_categories', 'course_sub_categories.id','=','course_courses.course_sub_category_id')
                ->where('course_sub_categories.created_for', 0)
                ->where('course_courses.id', $courseId)
                ->where('college_categories.college_id',$collegeId)->first();
    }

    /**
     *  get online courses by course id
     */
    protected static function getOnlineCourseByCourseId($courseId){
        $courseId = InputSanitise::inputInt($courseId);
        return DB::table('course_courses')
                ->join('course_categories', 'course_categories.id', '=', 'course_courses.course_category_id')
                ->join('course_sub_categories', 'course_sub_categories.id','=','course_courses.course_sub_category_id')
                ->where('course_sub_categories.created_for', 1)
                ->where('course_courses.id', $courseId)
                ->first();
    }

    protected static function deleteCollegeCoursesAndCourseVideosByUserId($userId){
        $courses =  static::where('created_by', $userId)->get();
        if(is_object($courses) && false == $courses->isEmpty()){
            foreach($courses as $course){
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
        return;
    }

    /**
     *  sub admin couses
     */
    protected static function getSubAdminCoursesWithPagination(){
        return static::join('course_categories', 'course_categories.id', '=', 'course_courses.course_category_id')
                ->join('course_sub_categories', 'course_sub_categories.id', '=', 'course_courses.course_sub_category_id')
                ->join('admins','admins.id','=', 'course_courses.admin_id')
                ->where('course_sub_categories.created_for', 1)
                ->where('course_courses.admin_id','!=', 1)
                ->select('course_courses.*', 'course_sub_categories.name as subcategory', 'course_categories.name as category','admins.name as admin')
                ->groupBy('course_courses.id')
                ->orderBy('course_courses.id','desc')
                ->paginate();
    }

    /**
     *  sub admin couses
     */
    protected static function getSubAdminCourses($adminId){
        return static::join('course_categories', 'course_categories.id', '=', 'course_courses.course_category_id')
                ->join('course_sub_categories', 'course_sub_categories.id', '=', 'course_courses.course_sub_category_id')
                ->join('admins','admins.id','=', 'course_courses.admin_id')
                ->where('course_sub_categories.created_for', 1)
                ->where('course_courses.admin_id', $adminId)
                ->select('course_courses.*', 'course_sub_categories.name as subcategory', 'course_categories.name as category','admins.name as admin')
                ->groupBy('course_courses.id')
                ->orderBy('course_courses.id','desc')
                ->get();
    }

    protected static function changeSubAdminCourseApproval($request){
        $courseId = $request->get('course_id');
        $course = static::find($courseId);
        if(is_object($course)){
            if(1 == $course->admin_approve){
                $course->admin_approve = 0;
            } else {
                $course->admin_approve = 1;
            }
            $course->save();
            return 'true';
        }
        return 'false';
    }

    protected static function deleteSubAdminCoursesAndCourseVideosByAdminId($adminId){
        $courses =  static::where('admin_id', $adminId)->get();
        if(is_object($courses) && false == $courses->isEmpty()){
            foreach($courses as $course){
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
        return;
    }
}
