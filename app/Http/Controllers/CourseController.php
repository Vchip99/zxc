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
use DB, Session, Cache;
use Validator, Redirect, Auth;
use App\Models\CourseSubComment;
use App\Models\CourseVideoLike;
use App\Models\CourseCommentLike;
use App\Models\CourseSubCommentLike;
use App\Models\Notification;
use App\Models\ReadNotification;
use App\Models\Add;
use App\Models\Rating;

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
    protected function courses(Request $request){
        $courseCategories = Cache::remember('vchip:courses:courseCats',60, function() {
            return CourseCategory::getCategoriesAssocaitedWithVideos();
        });
        if(empty($request->getQueryString())){
            $page = 'page=1';
        } else {
            $page = $request->getQueryString();
        }
        if(is_object(Auth::user()) && 'ceo@vchiptech.com' == Auth::user()->email){
            $courses = CourseCourse::getCourseAssocaitedWithVideosWithPagination();
        } else {
            $courses = Cache::remember('vchip:courses:courses-'.$page,60, function() {
                return CourseCourse::getCourseAssocaitedWithVideosWithPagination();
            });
        }
        $date = date('Y-m-d');
        $ads = Add::getAdds($request->url(),$date);
        $userPurchasedCourses = $this->getRegisteredCourseIds();

        $reviewData = [];
        $ratingUsers = [];
        $userNames = [];
        $allRatings = Rating::getRatingsByModuleType(Rating::Course);
        if(is_object($allRatings) && false == $allRatings->isEmpty()){
            foreach($allRatings as $rating){
                $reviewData[$rating->module_id]['rating'][$rating->user_id] = [ 'rating' => $rating->rating,'review' => $rating->review, 'review_id' => $rating->id, 'updated_at' => $rating->updated_at->diffForHumans()];
                $ratingUsers[] = $rating->user_id;
            }
            foreach($reviewData as $dataId => $rating){
                $ratingSum = 0.0;
                foreach($rating as $userRatings){
                    foreach($userRatings as $userId => $userRating){
                        $ratingSum = (double) $ratingSum + (double) $userRating['rating'];
                    }
                    $reviewData[$dataId]['avg']  = $ratingSum/count($userRatings);
                }
            }
        }
        if(count($ratingUsers) > 0){
            $users = User::find($ratingUsers);
            if(is_object($users) && false == $users->isEmpty()){
                foreach($users as $user){
                    $userNames[$user->id] = [ 'name' => $user->name,'photo' => $user->photo];
                }
            }
        }
        return view('courses.courses', compact('courseCategories', 'courses', 'ads','userPurchasedCourses','reviewData','userNames'));
    }

    /**
     *  return courses by categoryId by sub CategoryId or by userId
     */
    protected function getCourseByCatIdBySubCatId(Request $request){
        $result = [];
        $categoryId = $request->get('catId');
        $subcategoryId = $request->get('subcatId');
        $userId = $request->get('userId');
        $rating = $request->get('rating');
        if(isset($categoryId) && isset($subcategoryId) && empty($userId)){
            if(is_object(Auth::user()) && 'ceo@vchiptech.com' == Auth::user()->email){
                $result['courses'] = CourseCourse::getCourseByCatIdBySubCatId($categoryId,$subcategoryId);
            } else {
                $result['courses'] = Cache::remember('vchip:courses:courses:cat-'.$categoryId.':subcat-'.$subcategoryId,30, function() use ($categoryId,$subcategoryId){
                    return CourseCourse::getCourseByCatIdBySubCatId($categoryId,$subcategoryId);
                });
            }
            $result['userPurchasedCourses'] = $this->getRegisteredCourseIds();
        } else {
            $result['courses'] = CourseCourse::getCourseByCatIdBySubCatId($categoryId,$subcategoryId,$userId);
            $result['userPurchasedCourses'] = $this->getRegisteredCourseIds();
        }
        if(true == $rating){
            $ratingUsers = [];
            $allRatings = Rating::getRatingsByModuleType(Rating::Course);
            if(is_object($allRatings) && false == $allRatings->isEmpty()){
                foreach($allRatings as $rating){
                    $result['ratingData'][$rating->module_id]['rating'][$rating->user_id] = [ 'rating' => $rating->rating,'review' => $rating->review, 'review_id' => $rating->id, 'updated_at' => $rating->updated_at->diffForHumans()];
                    $ratingUsers[] = $rating->user_id;
                }
                foreach($result['ratingData'] as $dataId => $rating){
                    $ratingSum = 0.0;
                    foreach($rating as $userRatings){
                        foreach($userRatings as $userId => $userRating){
                            $ratingSum = (double) $ratingSum + (double) $userRating['rating'];
                        }
                        $result['ratingData'][$dataId]['avg']  = $ratingSum/count($userRatings);
                    }
                }
            } else {
                $result['ratingData'] = [];
            }
            if(count($ratingUsers) > 0){
                $users = User::find($ratingUsers);
                if(is_object($users) && false == $users->isEmpty()){
                    foreach($users as $user){
                        if(is_file($user->photo) && true == preg_match('/userStorage/',$user->photo)){
                            $isImageExist = 'system';
                        } else if(!empty($user->photo) && false == preg_match('/userStorage/',$user->photo)){
                            $isImageExist = 'other';
                        } else {
                            $isImageExist = 'false';
                        }
                        $result['userNames'][$user->id] = [ 'name' => $user->name,'photo' => $user->photo,'image_exist' => $isImageExist];
                    }
                }
            } else {
                $result['userNames'] = [];
            }
        }
        return $result;
    }


    /**
     *  return courses by categoryId by sub CategoryId or by userId
     */
    protected function getCollegeCourseByCatIdBySubCatId(Request $request){
        $result = [];
        $categoryId = $request->get('catId');
        $subcategoryId = $request->get('subcatId');
        if(isset($categoryId) && isset($subcategoryId)){
            $result['courses'] = Cache::remember('vchip:'.Session::get('college_user_url').':courses:cat-'.$categoryId.':subcat-'.$subcategoryId,30, function() use ($categoryId,$subcategoryId){
                return CourseCourse::getCollegeCourseByCatIdBySubCatId($categoryId,$subcategoryId);
            });
        }
        return $result;
    }

    /**
     *  show course details by courseId
     */
    protected function courseDetails($id){
        $courseId = json_decode(trim($id));
        if(is_object(Auth::user()) && 'ceo@vchiptech.com' == Auth::user()->email){
            $course = CourseCourse::find($courseId);
        } else {
            $course = Cache::remember('vchip:courses:Course-'.$courseId,30, function() use ($courseId){
                return CourseCourse::find($courseId);
            });
        }
        if(is_object($course)){
            if(is_object(Auth::user())){
                if('ceo@vchiptech.com' != Auth::user()->email && 0 == $course->admin_approve){
                    return Redirect::to('courses');
                }
            } else {
                if(0 == $course->admin_approve){
                    return Redirect::to('courses');
                }
            }
            if(is_object(Auth::user()) && 'ceo@vchiptech.com' == Auth::user()->email){
                $videos = CourseVideo::getCourseVideosByCourseId($courseId);
            } else {
                $videos = Cache::remember('vchip:courses:videos:courseId-'.$courseId,30, function() use ($courseId){
                    return CourseVideo::getCourseVideosByCourseId($courseId);
                });
            }
            $isCourseRegistered = RegisterOnlineCourse::isCourseRegistered($courseId);

            $reviewData = [];
            $ratingUsers = [];
            $userNames = [];
            $allRatings = Rating::getRatingsByModuleIdByModuleType($courseId,Rating::Course);
            if(is_object($allRatings) && false == $allRatings->isEmpty()){
                foreach($allRatings as $rating){
                    $reviewData[$rating->module_id]['rating'][$rating->user_id] = [ 'rating' => $rating->rating,'review' => $rating->review, 'review_id' => $rating->id, 'updated_at' => $rating->updated_at->diffForHumans()];
                    $ratingUsers[] = $rating->user_id;
                }
                foreach($reviewData as $dataId => $rating){
                    $ratingSum = 0.0;
                    foreach($rating as $userRatings){
                        foreach($userRatings as $userId => $userRating){
                            $ratingSum = (double) $ratingSum + (double) $userRating['rating'];
                        }
                        $reviewData[$dataId]['avg']  = $ratingSum/count($userRatings);
                    }
                }
            }
            if(count($ratingUsers) > 0){
                $users = User::find($ratingUsers);
                if(is_object($users) && false == $users->isEmpty()){
                    foreach($users as $user){
                        $userNames[$user->id] = [ 'name' => $user->name,'photo' => $user->photo];
                    }
                }
            }
            return view('courses.course_details', compact('videos', 'isCourseRegistered', 'courseId', 'course','reviewData','userNames'));
        }
        return Redirect::to('courses');
    }

    /**
     *  show episode and its details by id
     */
    protected function episode($id,$subcomment=NULL){
        $currentUser = Auth::user();
        $videoId = json_decode(trim($id));
        if(isset($videoId)){
            if(is_object(Auth::user()) && 'ceo@vchiptech.com' == Auth::user()->email){
                $video = CourseVideo::find($videoId);
            } else {
                $video = Cache::remember('vchip:courses:video-'.$videoId,30, function() use ($videoId){
                    return CourseVideo::find($videoId);
                });
            }
            if(is_object($video)){
                $videoCourse = $video->videoCourse;
                if(is_object($videoCourse)){
                    if(is_object(Auth::user())){
                        if('ceo@vchiptech.com' != Auth::user()->email && 0 == $videoCourse->admin_approve){
                            return Redirect::to('courses');
                        }
                    } else {
                        if(0 == $videoCourse->admin_approve){
                            return Redirect::to('courses');
                        }
                    }
                    $courseId = $videoCourse->id;
                    $videoCoursePrice = $videoCourse->price;
                    if(0 == $video->is_free && $videoCoursePrice > 0 ){
                        if(is_object($currentUser)){
                            if('ceo@vchiptech.com' != Auth::user()->email){
                                $isCoursePurchased = RegisterOnlineCourse::isCourseRegistered($courseId);
                                if( 'false' ==  $isCoursePurchased){
                                    return Redirect::to('courses');
                                }
                            }
                        } else {
                            return Redirect::to('courses');
                        }
                    }
                    $courseVideos = Cache::remember('vchip:courses:videos:courseId-'.$courseId,30, function() use ($courseId){
                        return CourseVideo::getCourseVideosByCourseId($courseId);
                    });
                    $comments = CourseComment::where('course_video_id', $id)->orderBy('id', 'desc')->get();
                    $likesCount = CourseVideoLike::getLikesByVideoId($videoId);
                    $commentLikesCount = CourseCommentLike::getLikesByVideoId($videoId);
                    $subcommentLikesCount = CourseSubCommentLike::getLikesByVideoId($videoId);

                    if(is_object($currentUser)){
                        if($videoId > 0 || $subcomment > 0){
                            DB::beginTransaction();
                            try
                            {
                                if($videoId > 0 && $subcomment == NULL){
                                    $readNotification = ReadNotification::readNotificationByModuleByModuleIdByUser(Notification::ADMINCOURSEVIDEO,$videoId,$currentUser->id);
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
                        $isCoursePurchased = RegisterOnlineCourse::isCourseRegistered($courseId);
                    } else {
                        $currentUser = NULL;
                        $isCoursePurchased = 'false';
                        if(0 == $video->is_free && $videoCoursePrice > 0){
                            return Redirect::to('courses');
                        }
                    }
                    return view('courses.episode', compact('video', 'courseVideos', 'comments', 'likesCount', 'commentLikesCount', 'currentUser', 'subcommentLikesCount','isCoursePurchased','videoCoursePrice'));
                }
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
            return Cache::remember('vchip:courses:coursesubcat:cat-'.$id,30, function() use ($id){
                return CourseSubCategory::getCourseSubCategoriesByCategoryId($id);
            });
        } else {
            return CourseSubCategory::getCourseSubCategoriesByCategoryId($id, $userid);
        }
    }

    /**
     *  return course by search criteria
     */
    protected function getOnlineCourseBySearchArray(Request $request){
        $searchFilter = json_decode($request->get('arr'),true);
        $rating = $searchFilter['rating'];
        $result['courses'] = CourseCourse::getOnlineCourseBySearchArray($request);
        if(true == $rating){
            $reviewData = [];
            $ratingUsers = [];
            $userNames = [];
            $allRatings = Rating::getRatingsByModuleType(Rating::Course);
            if(is_object($allRatings) && false == $allRatings->isEmpty()){
                foreach($allRatings as $rating){
                    $result['ratingData'][$rating->module_id]['rating'][$rating->user_id] = [ 'rating' => $rating->rating,'review' => $rating->review, 'review_id' => $rating->id];
                    $ratingUsers[] = $rating->user_id;
                }
                foreach($result['ratingData'] as $dataId => $rating){
                    $ratingSum = 0.0;
                    foreach($rating as $userRatings){
                        foreach($userRatings as $userId => $userRating){
                            $ratingSum = (double) $ratingSum + (double) $userRating['rating'];
                        }
                        $result['ratingData'][$dataId]['avg']  = $ratingSum/count($userRatings);
                    }
                }
            }
            if(count($ratingUsers) > 0){
                $users = User::find($ratingUsers);
                if(is_object($users) && false == $users->isEmpty()){
                    foreach($users as $user){
                        $result['userNames'][$user->id] = $user->name;
                    }
                }
            }
        }
        return $result;
    }

    protected function courseRegister(Request $request){
        return RegisterOnlineCourse::registerCourse($request);
    }

    public function getRegisteredCourseIds(){
        $registeredCourseIds = [];
        $loginUser = Auth::user();
        if(is_object($loginUser)){
            $userId = $loginUser->id;
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
            return CourseVideo::getCoursevideoCount($courseIds);
        }
        return;
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
        $courseComment = [];
        foreach($comments as $comment){
            $courseComment['comments'][$comment->id]['body'] = $comment->body;
            $courseComment['comments'][$comment->id]['id'] = $comment->id;
            $courseComment['comments'][$comment->id]['course_video_id'] = $comment->course_video_id;
            $courseComment['comments'][$comment->id]['user_id'] = $comment->user_id;
            $courseComment['comments'][$comment->id]['user_name'] = $comment->getUser($comment->user_id)->name;
            $courseComment['comments'][$comment->id]['updated_at'] = $comment->updated_at->diffForHumans();
            $courseComment['comments'][$comment->id]['user_image'] = $comment->getUser($comment->user_id)->photo;
            if(is_file($comment->getUser($comment->user_id)->photo) && true == preg_match('/userStorage/',$comment->getUser($comment->user_id)->photo)){
                $isImageExist = 'system';
            } else if(!empty($comment->getUser($comment->user_id)->photo) && false == preg_match('/userStorage/',$comment->getUser($comment->user_id)->photo)){
                $isImageExist = 'other';
            } else {
                $isImageExist = 'false';
            }
            $courseComment['comments'][$comment->id]['image_exist'] = $isImageExist;
            if(is_object($comment->children) && false == $comment->children->isEmpty()){
                $courseComment['comments'][$comment->id]['subcomments'] = $this->getSubComments($comment->children);
            }
        }
        $courseComment['commentLikesCount'] = CourseCommentLike::getLikesByVideoId($videoId);
        $courseComment['subcommentLikesCount'] = CourseSubCommentLike::getLikesByVideoId($videoId);

        return $courseComment;
    }

    /**
     *  return child comments
     */
    protected function getSubComments($subComments){

        $courseChildComments = [];
        foreach($subComments as $subComment){
            $courseChildComments[$subComment->id]['body'] = $subComment->body;
            $courseChildComments[$subComment->id]['id'] = $subComment->id;
            $courseChildComments[$subComment->id]['course_video_id'] = $subComment->course_video_id;
            $courseChildComments[$subComment->id]['course_comment_id'] = $subComment->course_comment_id;
            $courseChildComments[$subComment->id]['user_name'] = $subComment->getUser($subComment->user_id)->name;
            $courseChildComments[$subComment->id]['user_id'] = $subComment->user_id;
            $courseChildComments[$subComment->id]['updated_at'] = $subComment->updated_at->diffForHumans();
            $courseChildComments[$subComment->id]['user_image'] = $subComment->getUser($subComment->user_id)->photo;
            if(is_file($subComment->getUser($subComment->user_id)->photo) && true == preg_match('/userStorage/',$subComment->getUser($subComment->user_id)->photo)){
                $isImageExist = 'system';
            } else if(!empty($subComment->getUser($subComment->user_id)->photo) && false == preg_match('/userStorage/',$subComment->getUser($subComment->user_id)->photo)){
                $isImageExist = 'other';
            } else {
                $isImageExist = 'false';
            }
            $courseChildComments[$subComment->id]['image_exist'] = $isImageExist;
            if(is_object($subComment->children) && false == $subComment->children->isEmpty()){
                $courseChildComments[$subComment->id]['subcomments'] = $this->getSubComments($subComment->children);
            }
        }

        return $courseChildComments;
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
                $notificationMessage = '<a href="'.$request->root().'/episode/'.$videoId.'/'.$subComment->id.'" target="_blank">A reply of your comment: '. trim($string, '<p></p>')  .'</a>';

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