<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB, Auth;
use App\Libraries\InputSanitise;
use App\Models\ClientOnlineCategory;
use App\Models\ClientOnlineSubCategory;
use App\Models\RegisterClientOnlineCourses;
use App\Models\ClientOnlineVideo;
use Intervention\Image\ImageManagerStatic as Image;

class ClientOnlineCourse extends Model
{
    protected $connection = 'mysql2';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'category_id', 'sub_category_id', 'author', 'author_introduction', 'author_image', 'description', 'price', 'difficulty_level', 'certified', 'image_path', 'release_date', 'client_id'];

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
        $loginUser = Auth::guard('client')->user();

    	if( $isUpdate && !empty($courseId)){
    		$course = static::find($courseId);
    		if(!is_object($course)){
    			return 'false';
    		}
    	} else {
    		$course = new static;
    	}
    	$course->name = $courseName;
    	$course->category_id = $categoryId;
    	$course->sub_category_id = $subcategoryId;
    	$course->author = $author;
    	$course->price = $price;
    	$course->description = $description;
    	$course->difficulty_level = $difficultyLevel;
        $course->author_introduction = $authorIntroduction;
        $course->certified = $certified;
        $subdomainArr = explode('.', $loginUser->subdomain);
        $clientName = $subdomainArr[0];

        if($request->exists('author_image')){
            $authorImage = $request->file('author_image')->getClientOriginalName();
            $courseImageFolder = "client_images/".$clientName."/"."courseImages/";

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
            $courseImageFolder = "client_images/".$clientName."/"."courseImages/";

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
    	$course->client_id = $loginUser->id;
    	$course->save();
    	return $course;

    }

    /**
     *  get category of course
     */
    public function category(){
        return $this->belongsTo(ClientOnlineCategory::class, 'category_id');
    }

    /**
     *  get sub category of course
     */
    public function subcategory(){
        return $this->belongsTo(ClientOnlineSubCategory::class, 'sub_category_id');
    }

    public function videos(){
        return $this->hasMany(ClientOnlineVideo::class, 'course_id');
    }

    protected static function getCourseAssocaitedWithVideos($subdomain=NULL){
        $query = DB::connection('mysql2')->table('client_online_courses')
                    ->join('client_online_videos', 'client_online_videos.course_id', '=', 'client_online_courses.id')
                    ->join('client_online_categories', 'client_online_categories.id', '=', 'client_online_courses.category_id')
                    ->join('client_online_sub_categories', 'client_online_sub_categories.id', '=', 'client_online_courses.sub_category_id')
                    ->join('clients', function($join){
                        $join->on('clients.id', '=', 'client_online_courses.client_id');
                        $join->on('clients.id', '=', 'client_online_videos.client_id');
                        $join->on('clients.id', '=', 'client_online_sub_categories.client_id');
                    });
        $loginUser = Auth::guard('client')->user();
        if(is_object($loginUser)){
            $query->where('clients.id', $loginUser->id);
        } else if(!empty($subdomain)) {
            $query->where('clients.subdomain', $subdomain);
        }
        return $query->where('client_online_courses.release_date','<=', date('Y-m-d H:i'))->select('client_online_courses.*', 'client_online_sub_categories.name as subcategory', 'client_online_categories.name as category')->groupBy('client_online_courses.id')->get();
    }

    /**
     *  return courses by categoryId by sub categoryId
     */
    protected static function getOnlineCourseByCatIdBySubCatId($categoryId,$subcategoryId,Request $request){
        $categoryId = InputSanitise::inputInt($categoryId);
        $subcategoryId = InputSanitise::inputInt($subcategoryId);
        $client = InputSanitise::getCurrentClient($request);

        /**
         *  display courses associated with videos by category and sub category
         */
        $result = DB::connection('mysql2')->table('client_online_courses')
                ->join('client_online_categories', 'client_online_categories.id', '=', 'client_online_courses.category_id')
                ->join('client_online_sub_categories', 'client_online_sub_categories.id', '=', 'client_online_courses.sub_category_id')
                ->join('client_online_videos', 'client_online_videos.course_id', '=', 'client_online_courses.id')
                ->join('clients', function($join){
                    $join->on('clients.id', '=', 'client_online_courses.client_id');
                    $join->on('clients.id', '=', 'client_online_videos.client_id');
                    $join->on('clients.id', '=', 'client_online_sub_categories.client_id');
                });

        return  $result->where('clients.subdomain', $client)
            ->where('client_online_courses.category_id', $categoryId)
            ->where('client_online_courses.sub_category_id', $subcategoryId)
            ->where('client_online_courses.release_date','<=', date('Y-m-d H:i:s'))
            ->select('client_online_courses.id','client_online_courses.*', 'client_online_sub_categories.name as subcategory', 'client_online_categories.name as category')
            ->groupBy('client_online_courses.id')
            ->get();
    }

    protected static function showCourses(Request $request, $withVideo=false){
        $loginUser = Auth::guard('client')->user();
        if(is_object($loginUser)){
            $clientId = $loginUser->id;
        } else{
            $client = InputSanitise::getCurrentClient($request);
        }

        $result = static::join('client_online_categories', 'client_online_categories.id', '=', 'client_online_courses.category_id')
                ->join('client_online_sub_categories', 'client_online_sub_categories.id', '=', 'client_online_courses.sub_category_id');
        if($withVideo == true){
            $result->join('client_online_videos', 'client_online_videos.course_id', '=', 'client_online_courses.id');
        }
        $result->join('clients', function($join) use ($withVideo){
            $join->on('clients.id', '=', 'client_online_courses.client_id');
            if($withVideo == true){
                $join->on('clients.id', '=', 'client_online_videos.client_id');
            }
            $join->on('clients.id', '=', 'client_online_sub_categories.client_id');
        });

        if(!empty($clientId)){
            $result->where('clients.id', $clientId);
        } else {
            $result->where('clients.subdomain', $client);
        }
        return  $result->select('client_online_courses.*', 'client_online_categories.name as category', 'client_online_sub_categories.name as subcategory', 'client_online_categories.name as category')
            ->groupBy('client_online_courses.id')->get();
    }

    /**
     *  get registered online courses for user
     */
    protected static function getRegisteredOnlineCourses($userId){
        $userId = InputSanitise::inputInt($userId);
        $result = DB::connection('mysql2')->table('client_online_courses')
                ->join('clients', 'clients.id', '=', 'client_online_courses.client_id')
                ->join('clientusers', 'clientusers.client_id', '=', 'clients.id')
                ->join('register_client_online_courses', 'register_client_online_courses.client_online_course_id', '=', 'client_online_courses.id')
                ->join('client_online_sub_categories', 'client_online_sub_categories.id', '=', 'client_online_courses.sub_category_id')
                ->join('client_online_categories', 'client_online_categories.id', '=', 'client_online_courses.category_id')
                ->where('register_client_online_courses.client_user_id', $userId);
        return $result->select('client_online_courses.id','client_online_courses.*', 'client_online_sub_categories.name as subCategory', 'client_online_categories.name as category')
                ->groupBy('client_online_courses.id')
                ->get();
    }

      /**
     *  get registered online courses for user
     */
    protected static function getRegisteredOnlineCourseByCatIdBySubCatId($categoryId,$subcategoryId,$userId){

        return DB::connection('mysql2')->table('client_online_courses')
                ->join('clients', 'clients.id', '=', 'client_online_courses.client_id')
                ->join('clientusers', 'clientusers.client_id', '=', 'clients.id')
                ->join('register_client_online_courses', 'register_client_online_courses.client_online_course_id', '=', 'client_online_courses.id')
                ->join('client_online_sub_categories', 'client_online_sub_categories.id', '=', 'client_online_courses.sub_category_id')
                ->join('client_online_categories', 'client_online_categories.id', '=', 'client_online_courses.category_id')
                ->where('register_client_online_courses.client_user_id', $userId)
                ->where('client_online_sub_categories.id', $subcategoryId)
                ->where('client_online_categories.id', $categoryId)
                ->select('client_online_courses.id','client_online_courses.*', 'client_online_sub_categories.name as subCategory', 'client_online_categories.name as category')
                ->groupBy('client_online_courses.id')
                ->get();
    }


    protected static function getCurrentCoursesByClient($subdomain){
        return DB::connection('mysql2')->table('client_online_courses')
                ->join('clients', 'clients.id', '=', 'client_online_courses.client_id')
                ->join('client_online_videos', 'client_online_videos.course_id', '=', 'client_online_courses.id')
                ->where('clients.subdomain', $subdomain)
                ->select('client_online_courses.*')
                ->groupBy('client_online_courses.id')
                ->orderBy('id', 'desc')->take(2)->get();
    }

    public function deleteRegisteredOnlineCourses(){
        $registeredOnlineCourses = RegisterClientOnlineCourses::where('client_online_course_id', $this->id)->where('client_id', Auth::guard('client')->user()->id)->get();
        if(is_object($registeredOnlineCourses) && false == $registeredOnlineCourses->isEmpty()){
            foreach($registeredOnlineCourses as $registeredOnlineCourse){
                $registeredOnlineCourse->delete();
            }
        }
    }

    public function deleteCourseImageFolder($request){
        $subdomain = explode('.',$request->getHost());
        $courseImageFolder = "client_images/".$subdomain[0]."/"."courseImages/".str_replace(' ', '_', $this->name);
        if(is_dir($courseImageFolder)){
            InputSanitise::delFolder($courseImageFolder);
        }
    }

    protected static function getRegisteredOnlineCoursesByUserId($userId){
        return static::join('register_client_online_courses', 'register_client_online_courses.client_online_course_id', '=', 'client_online_courses.id')
            ->where('register_client_online_courses.client_user_id', $userId)
            ->get();
    }

    protected function getOnlineCourseByCatIdBySubCatIdForClient($categoryId,$subCategoryId){
        $client = Auth::guard('client')->user();
        return static::where('client_id', $client->id)->where('category_id', $categoryId)->where('sub_category_id', $subCategoryId)->get();
    }

    protected static function deleteClientOnlineCoursesByClientId($clientId){
        $courses = static::where('client_id', $clientId)->get();
        if(is_object($courses) && false == $courses->isEmpty()){
            foreach($courses as $course){
                $course->delete();
            }
        }
    }

    protected static function isClientOnlineCourseExist(Request $request){
        $clientId = Auth::guard('client')->user()->id;
        $categoryId = InputSanitise::inputInt($request->get('category'));
        $subCategoryId = InputSanitise::inputInt($request->get('subcategory'));
        $courseName = InputSanitise::inputString($request->get('course'));
        $courseId = InputSanitise::inputInt($request->get('course_id'));
        $result = static::where('client_id', $clientId)->where('category_id', $categoryId)->where('sub_category_id', $subCategoryId)->where('name', '=',$courseName);
        if(!empty($courseId)){
            $result->where('id', '!=', $courseId);
        }
        $result->first();
        if(is_object($result) && 1 == $result->count()){
            return 'true';
        } else {
            return 'false';
        }
    }
}
