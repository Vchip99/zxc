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
use App\Models\Notification;
use App\Models\ReadNotification;

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
    protected function episode($id,$subcomment=NULL){
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
                    if($videoId > 0 || $subcomment > 0){
                        DB::beginTransaction();
                        try
                        {
                            if($videoId > 0 && $subcomment == NULL){
                                $readNotification = ReadNotification::readNotificationByModuleByModuleIdByUser(Notification::ADMINCOURSEVIDEO,$videoId,$currentUser);
                                if(is_object($readNotification)){
                                    DB::commit();
                                }
                            } else {
                                Session::set('show_subcomment_area', $subcomment);
                            }
                            Session::set('course_comment_area', 0);
                        }
                        catch(\Exception $e)
                        {
                            DB::rollback();
                            return redirect()->back()->withErrors('something went wrong.');
                        }
                    } else {
                        Session::set('show_subcomment_area', 0);
                    }
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
        // $v = Validator::make($request->all(), $this->validateCourseComment);
        // if ($v->fails())
        // {
        //     return redirect()->back()->withErrors($v->errors());
        // }
        $videoId = strip_tags(trim($request->get('video_id')));
        DB::beginTransaction();
        try
        {
            $courseComment = CourseComment::createComment($request);
            // Session::put('course_comment_area', $courseComment->id);
            DB::commit();
            // if(0 < $videoId){
            //     return redirect()->route('episode', ['id' => $videoId]);
            // }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            // return back()->withErrors('something went wrong.');
        }
        // return Redirect::to('courses');

        return $this->getComments($videoId);

    }

    /**
     *  return comments
     */
    protected function getComments($videoId){
        $comments = CourseComment::where('course_video_id', $videoId)->orderBy('id', 'desc')->get();
        $videoComments = [];
        foreach($comments as $comment){
            $videoComments['comments'][$comment->id]['body'] = $comment->body;
            $videoComments['comments'][$comment->id]['id'] = $comment->id;
            $videoComments['comments'][$comment->id]['course_video_id'] = $comment->course_video_id;
            $videoComments['comments'][$comment->id]['user_id'] = $comment->user_id;
            $videoComments['comments'][$comment->id]['user_name'] = $comment->user->name;
            $videoComments['comments'][$comment->id]['updated_at'] = $comment->updated_at->diffForHumans();
            $videoComments['comments'][$comment->id]['user_image'] = $comment->user->photo;
            if(is_object($comment->children) && false == $comment->children->isEmpty()){
                $videoComments['comments'][$comment->id]['subcomments'] = $this->getSubComments($comment->children);
            }
        }
        $videoComments['commentLikesCount'] = CourseCommentLike::getLikesByVideoId($videoId);
        $videoComments['subcommentLikesCount'] = CourseSubCommentLike::getLikesByVideoId($videoId);

        return $videoComments;
    }

    /**
     *  return child comments
     */
    protected function getSubComments($subComments){

        $videoChildComments = [];
        foreach($subComments as $subComment){
            $videoChildComments[$subComment->id]['body'] = $subComment->body;
            $videoChildComments[$subComment->id]['id'] = $subComment->id;
            $videoChildComments[$subComment->id]['course_video_id'] = $subComment->course_video_id;
            $videoChildComments[$subComment->id]['course_comment_id'] = $subComment->course_comment_id;
            $videoChildComments[$subComment->id]['user_name'] = $subComment->user->name;
            $videoChildComments[$subComment->id]['user_id'] = $subComment->user_id;
            $videoChildComments[$subComment->id]['updated_at'] = $subComment->updated_at->diffForHumans();
            $videoChildComments[$subComment->id]['user_image'] = $subComment->user->photo;
            if(is_object($subComment->children) && false == $subComment->children->isEmpty()){
                $videoChildComments[$subComment->id]['subcomments'] = $this->getSubComments($subComment->children);
            }
        }

        return $videoChildComments;
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
                    // Session::put('course_comment_area', $comment->id);
                    // return redirect()->route('episode', ['id' => $videoId]);
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    // return back()->withErrors('something went wrong.');
                }
            }
        }
        return $this->getComments($videoId);
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
                    // Session::put('course_comment_area', 0);
                    $comment->delete();
                    DB::commit();
                    // return redirect()->route('episode', ['id' => $videoId]);
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    // return back()->withErrors('something went wrong.');
                }
            }
        }
        return $this->getComments($videoId);
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
            $videoId = strip_tags(trim($request->get('video_id')));
            $commentId = $request->get('comment_id');
            $subcommentId = $request->get('subcomment_id');

            $subComment = CourseSubComment::createSubComment($request);
            if($commentId > 0 && $subcommentId > 0){
                $parentComment = CourseSubComment::where('id',$subcommentId)->where('user_id', '!=', Auth::user()->id)->first();
            } else {
                $parentComment = CourseComment::where('id',$subComment->course_comment_id)->first();
            }

            if(is_object($parentComment)){
                $string = (strlen($parentComment->body) > 50) ? substr($parentComment->body,0,50).'...' : $parentComment->body;
                $notificationMessage = '<a href="'.$request->root().'/episode/'.$videoId.'/'.$subComment->id.'">A reply of your comment: '. trim($string, '<p></p>')  .'</a>';

                Notification::addCommentNotification($notificationMessage, Notification::USERCOURSENOTIFICATION, $subComment->id,$subComment->user_id,$parentComment->user_id);
            }

            // Session::put('course_comment_area', $request->get('comment_id'));
            DB::commit();
            // if(0 < $videoId){
            //     return redirect()->route('episode', ['id' => $videoId]);
            // }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            // return back()->withErrors('something went wrong.');
        }
        return $this->getComments($videoId);
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
                    // Session::put('course_comment_area', $commentId);
                    // return redirect()->route('episode', ['id' => $videoId]);
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    // return back()->withErrors('something went wrong.');
                }
            }
        }
        return $this->getComments($videoId);
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
                    // Session::put('course_comment_area', $commentId);
                    $subcomment->delete();
                    DB::commit();
                    // return redirect()->route('episode', ['id' => $videoId]);
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    // return back()->withErrors('something went wrong.');
                }
            }
        }
        return $this->getComments($videoId);
    }
}