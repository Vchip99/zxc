<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CourseCategory;
use App\Models\CourseSubCategory;
use App\Models\CourseCourse;
use App\Models\CourseVideo;
use App\Libraries\InputSanitise;
use App\Models\CourseComment;
use App\Models\User;
use App\Models\RegisterOnlineCourse;
use DB, Session;
use Validator, Redirect, Auth;
use App\Models\CourseSubComment;
use App\Models\CourseVideoLike;
use App\Models\CourseCommentLike;
use App\Models\CourseSubCommentLike;

class CourseController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        // $this->middleware('auth');
    }

    protected $validateCourseComment = [
            'comment' => 'required',
            'video_id' => 'required',
        ];
    protected $validateCourseSubComment = [
            'subcomment' => 'required',
            'video_id' => 'required',
            'comment_id' => 'required',
        ];

    /**
     *  show all courses associated with videos
     */
    protected function courses(){
        $courseIds = [];
        $courseCategories = CourseCategory::getCategoriesAssocaitedWithVideos();
        $courses = CourseCourse::getCourseAssocaitedWithVideos();
        $courseVideoCount = $this->getVideoCount($courses);
        return view('courses.courses', compact('courseCategories', 'courses', 'courseVideoCount'));
    }

    /**
     *  return courses by categoryId by sub CategoryId or by userId
     */
    protected function getCourseByCatIdBySubCatId(Request $request){
        $result = [];
        $categoryId = $request->get('catId');
        $subcategoryId = $request->get('subcatId');
        $userId = $request->get('userId');
        if(isset($categoryId) && isset($subcategoryId) && empty($userId)){
            $result['courses'] = CourseCourse::getCourseByCatIdBySubCatId($categoryId,$subcategoryId);
            $result['courseVideoCount'] = $this->getVideoCount($result['courses']);
        } else {
            $result['courses'] = CourseCourse::getCourseByCatIdBySubCatId($categoryId,$subcategoryId,$userId);
            $result['courseVideoCount'] = $this->getVideoCount($result['courses']);
        }
        return $result;
    }

    /**
     *  show course details by courseId
     */
    protected function courseDetails($id){
        $courseId = json_decode(trim($id));
        $course = CourseCourse::find($courseId);
        if(is_object($course)){
            $videos = CourseVideo::getCourseVideosByCourseId($courseId);
            $isCourseRegistered = RegisterOnlineCourse::isCourseRegistered($courseId);
            return view('courses.course_details', compact('videos', 'isCourseRegistered', 'courseId', 'course'));
        }
        return Redirect::to('courses');
    }

    /**
     *  show episode and its details by id
     */
    protected function episode($id){
        $videoId = json_decode(trim($id));
        if(isset($videoId)){
            $video = CourseVideo::find($videoId);
            if(is_object($video)){
                $user = new User;
                $courseVideos = CourseVideo::getCourseVideosByCourseId($video->course_id);
                $comments = CourseComment::where('course_video_id', $id)->orderBy('id', 'desc')->get();
                $likesCount = CourseVideoLike::getLikesByVideoId($videoId);
                $commentLikesCount = CourseCommentLike::getLikesByVideoId($videoId);
                $subcommentLikesCount = CourseSubCommentLike::getLikesByVideoId($videoId);
                if(is_object(Auth::user())){
                    $currentUser = Auth::user()->id;
                } else {
                    $currentUser = 0;
                }
                return view('courses.episode', compact('video', 'courseVideos', 'comments', 'user', 'likesCount', 'commentLikesCount', 'currentUser', 'subcommentLikesCount'));
            }
        }
        return Redirect::to('courses');
    }

    /**
     *  return course sub categories by category id
     */
    protected function getCourseSubCategories(Request $request){
        $id = $request->get('id');
        $userid = $request->get('userId');
        if(isset($id) && empty($userid)){
            return CourseSubCategory::getCourseSubCategoriesByCategoryId($id);
        } else {
            return CourseSubCategory::getCourseSubCategoriesByCategoryId($id, $userid);
        }
    }

    /**
     *  return course by search criteria
     */
    protected function getOnlineCourseBySearchArray(Request $request){
        $result['courses'] = CourseCourse::getOnlineCourseBySearchArray($request);
        $result['courseVideoCount'] = $this->getVideoCount($result['courses']);
        return $result;
    }

    protected function courseRegister(Request $request){
        return RegisterOnlineCourse::registerCourse($request);
    }

    protected function getRegisteredCourseIds(){
        $registeredCourseIds = [];
        if(is_object(Auth::user())){
            $userId = Auth::user()->id;
            $registeredCourses = RegisterOnlineCourse::getRegisteredOnlineCoursesByUserId($userId);
            if(false == $registeredCourses->isEmpty()){
                foreach($registeredCourses as $registeredCourse){
                    $registeredCourseIds[] = $registeredCourse->online_course_id;
                }
            }
        }
        return $registeredCourseIds;
    }

    protected function getVideoCount($courses){
        $courseIds = [];
         if(false == $courses->isEmpty()){
            foreach($courses as $course){
                $courseIds[] = $course->id;
            }
            $courseIds = array_unique($courseIds);
        }
        return CourseVideo::getCoursevideoCount($courseIds);
    }

    protected function likeCourseVideo(Request $request){
        return CourseVideoLike::getLikeVideo($request);
    }

    protected function likeCourseVideoComment(Request $request){
        return CourseCommentLike::getLikeVideoComment($request);
    }

    protected function likeCourseVideoSubComment(Request $request){
        return CourseSubCommentLike::getLikeVideoSubComment($request);
    }

    protected function createCourseComment(Request $request){
        $v = Validator::make($request->all(), $this->validateCourseComment);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::beginTransaction();
        try
        {
            $courseComment = CourseComment::createComment($request);
            Session::put('course_comment_area', $courseComment->id);
            DB::commit();
            $videoId = strip_tags(trim($request->get('video_id')));
            if(0 < $videoId){
                return redirect()->route('episode', ['id' => $videoId]);
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return back()->withErrors('something went wrong.');
        }
        return Redirect::to('courses');
    }

    protected function updateCourseComment(Request $request){
        $videoId = InputSanitise::inputInt($request->get('video_id'));
        $commentId = InputSanitise::inputInt($request->get('comment_id'));
        $commentBody = $request->get('comment');
        if(!empty($videoId) && !empty($commentId) && !empty($commentBody)){
            $comment = CourseComment::where('course_video_id', $videoId)->where('id', $commentId)->first();
            if(is_object($comment)){
                DB::beginTransaction();
                try
                {
                    $comment->body = $commentBody;
                    $comment->save();
                    DB::commit();
                    Session::put('course_comment_area', $comment->id);
                    return redirect()->route('episode', ['id' => $videoId]);
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return back()->withErrors('something went wrong.');
                }
            }
        }
        return Redirect::to('courses');
    }

    protected function deleteCourseComment(Request $request){
        $videoId = InputSanitise::inputInt($request->get('video_id'));
        $commentId = InputSanitise::inputInt($request->get('comment_id'));
        if(!empty($videoId) && !empty($commentId)){
            $comment = CourseComment::where('course_video_id', $videoId)->where('id', $commentId)->first();
            if(is_object($comment)){
                DB::beginTransaction();
                try
                {
                    if(is_object($comment->children) && false == $comment->children->isEmpty()){
                        foreach($comment->children as $subComment){
                            if(is_object($subComment->deleteLikes) && false == $subComment->deleteLikes->isEmpty()){
                                foreach($subComment->deleteLikes as $deleteLike){
                                    $deleteLike->delete();
                                }
                            }
                            $subComment->delete();
                        }
                    }
                    if(is_object($comment->deleteLikes) && false == $comment->deleteLikes->isEmpty()){
                        foreach($comment->deleteLikes as $deleteLike){
                            $deleteLike->delete();
                        }
                    }
                    Session::put('course_comment_area', 0);
                    $comment->delete();
                    DB::commit();
                    return redirect()->route('episode', ['id' => $videoId]);
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return back()->withErrors('something went wrong.');
                }
            }
        }
        return Redirect::to('courses');
    }

    protected function createCourseSubComment(Request $request){
        $v = Validator::make($request->all(), $this->validateCourseSubComment);

        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::beginTransaction();
        try
        {
            $subComment = CourseSubComment::createSubComment($request);
            Session::put('course_comment_area', $request->get('comment_id'));
            DB::commit();
            $videoId = strip_tags(trim($request->get('video_id')));
            if(0 < $videoId){
                return redirect()->route('episode', ['id' => $videoId]);
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return back()->withErrors('something went wrong.');
        }
        return Redirect::to('courses');
    }

    protected function updateCourseSubComment(Request $request){
        $videoId = InputSanitise::inputInt($request->get('video_id'));
        $commentId = InputSanitise::inputInt($request->get('comment_id'));
        $subcommentId = InputSanitise::inputInt($request->get('subcomment_id'));
        $commentBody = $request->get('subcomment');
        if(!empty($videoId) && !empty($commentId) && !empty($subcommentId) && !empty($commentBody)){
            $subcomment = CourseSubComment::where('course_video_id', $videoId)->where('course_comment_id', $commentId)->where('id', $subcommentId)->first();
            if(is_object($subcomment)){
                DB::beginTransaction();
                try
                {
                    $subcomment->body = $commentBody;
                    $subcomment->save();
                    DB::commit();
                    Session::put('course_comment_area', $commentId);
                    return redirect()->route('episode', ['id' => $videoId]);
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return back()->withErrors('something went wrong.');
                }
            }
        }
        return Redirect::to('courses');
    }

    protected function deleteCourseSubComment(Request $request){
        $videoId = InputSanitise::inputInt($request->get('video_id'));
        $commentId = InputSanitise::inputInt($request->get('comment_id'));
        $subcommentId = InputSanitise::inputInt($request->get('subcomment_id'));
        if(!empty($videoId) && !empty($commentId) && !empty($subcommentId)){
            $subcomment = CourseSubComment::where('course_video_id', $videoId)->where('course_comment_id', $commentId)->where('id', $subcommentId)->first();
            if(is_object($subcomment)){
                DB::beginTransaction();
                try
                {
                    if(is_object($subcomment->deleteLikes) && false == $subcomment->deleteLikes->isEmpty()){
                        foreach($subcomment->deleteLikes as $deleteLike){
                            $deleteLike->delete();
                        }
                    }
                    Session::put('course_comment_area', $commentId);
                    $subcomment->delete();
                    DB::commit();
                    return redirect()->route('episode', ['id' => $videoId]);
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return back()->withErrors('something went wrong.');
                }
            }
        }
        return Redirect::to('courses');
    }
}