<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use DB;
use App\Models\LiveVideo;
use App\Models\RegisterLiveCourse;

class LiveCourse extends Model
{
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'category_id', 'author', 'author_introduction', 'author_image', 'image_path', 'description',  'price', 'difficulty_level', 'certified', 'on_demand', 'start_date', 'end_date'];

    /**
     *  create/update live course
     */
    protected static function addOrUpdateLiveCourse(Request $request, $isUpdate = false){
        $courseName = InputSanitise::inputString($request->get('course'));
        $author = InputSanitise::inputString($request->get('author'));
        $certified = InputSanitise::inputInt($request->get('certified'));
    	$categoryId = InputSanitise::inputInt($request->get('category'));
        $onDemand = InputSanitise::inputInt($request->get('on_demand'));
        $price = strip_tags(trim($request->get('price')));
        $difficultyLevel = InputSanitise::inputInt($request->get('difficulty_level'));
        $start_date = strip_tags(trim($request->get('start_date')));
        $end_date = strip_tags(trim($request->get('end_date')));
        $courseId = InputSanitise::inputInt($request->get('live_course_id'));
        $authorIntroduction = InputSanitise::inputString($request->get('author_introduction'));
        $description = InputSanitise::inputString($request->get('description'));

    	if( $isUpdate && isset($courseId)){
    		$course = static::find($courseId);
    		if(!is_object($course)){
    			return Redirect::to('admin/manageLiveCourse');
    		}
    	} else {
    		$course = new static;
    	}
    	$course->name = $courseName;
    	$course->category_id = $categoryId;
    	$course->author = $author;
    	$course->certified = $certified;

        $course->on_demand = $onDemand;
        $course->price = $price;
        $course->difficulty_level = $difficultyLevel;

        $course->author_introduction = $authorIntroduction;
        $course->description = $description;

        if($request->exists('author_image')){
            $authorImage = $request->file('author_image')->getClientOriginalName();
            $courseImageFolder = "LiveCourseImages/";

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
        }
        if($request->exists('image_path')){
            $imagePath = $request->file('image_path')->getClientOriginalName();
            $courseImageFolder = "LiveCourseImages/";

            $courseFolderPath = $courseImageFolder.str_replace(' ', '_', $courseName);
            if(!is_dir($courseFolderPath)){
                mkdir($courseFolderPath, 0755, true);
            }
            $LiveCourseImagePath = $courseFolderPath ."/". $imagePath;
            if(file_exists($LiveCourseImagePath)){
                unlink($LiveCourseImagePath);
            } elseif(!empty($course->id) && file_exists($course->image_path)){
                unlink($course->image_path);
            }
            $request->file('image_path')->move($courseFolderPath, $imagePath);
            $course->image_path = $LiveCourseImagePath;
        }
    	$course->start_date = $start_date;
    	$course->end_date = $end_date;
    	$course->save();
    	return $course;
    }

    /**
     *  return live courses by categoryId
     */
    protected static function getLiveCourseByCatId($catId){
        return DB::table('live_courses')
                ->join('live_videos', 'live_videos.live_course_id', '=', 'live_courses.id')
                ->where('live_courses.category_id', $catId)
                ->select('live_courses.id', 'live_courses.*')
                ->groupBy('live_courses.id')
                ->get();
    }

    /**
     *  return live courses by filter array
     */
    protected static function getLiveCourseBySearchArray($request){
        $searchFilter = json_decode($request->get('arr'),true);
        $difficulty = $searchFilter['difficulty'];
        $certified = $searchFilter['certified'];
        $onDemand = $searchFilter['onDemand'];
        $fees = $searchFilter['fees'];
        $categoryId = InputSanitise::inputInt($searchFilter['categoryId']);

        $results = DB::table('live_courses')
                    ->join('live_videos', 'live_videos.live_course_id', '=', 'live_courses.id');

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
        if(count($onDemand) > 0){
            $results->whereIn('on_demand', $onDemand);
        }
        if(!empty($categoryId)){
            $results->where('category_id', $categoryId);
        }
        return $results->select('live_courses.id', 'live_courses.*')
                ->groupBy('live_courses.id')
                ->get();
    }

    protected static function getRegisteredLiveCourseByUserIdByCatId($userId,$catId){
        return DB::table('live_courses')
                ->join('register_live_courses', 'register_live_courses.live_course_id', '=', 'live_courses.id')
                ->where('register_live_courses.user_id', $userId)
                ->where('live_courses.category_id', $catId)
                ->select('live_courses.*')
                ->get();
    }

    protected static function getLiveCoursesAssociatedWithVideos(){
        return DB::table('live_courses')
                ->join('live_videos', 'live_videos.live_course_id', '=', 'live_courses.id')
                ->select('live_courses.id', 'live_courses.*')
                ->groupBy('live_courses.id')
                ->paginate(9);
    }

    public function videos(){
        return $this->hasMany(LiveVideo::class, 'live_course_id');
    }

    public function deleteLiveCourseImageFolder(){
        $courseImageFolder = "LiveCourseImages/".str_replace(' ', '_', $this->name);
        if(is_dir($courseImageFolder)){
            InputSanitise::delFolder($courseImageFolder);
        }
    }

    public function deleteRegisteredLiveCourses(){
        $registeredCourses = RegisterLiveCourse::where('live_course_id', $this->id)->get();
        if(is_object($registeredCourses) && false == $registeredCourses->isEmpty()){
            foreach($registeredCourses as $registeredCourse){
                $registeredCourse->delete();
            }
        }
    }

}