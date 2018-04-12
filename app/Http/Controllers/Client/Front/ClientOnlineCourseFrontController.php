<?php

namespace App\Http\Controllers\Client\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\Client\ClientHomeController;
use View,DB,Session,Redirect, Auth,Validator,Cache;
use App\Libraries\InputSanitise;
use App\Models\ClientOnlineCourse;
use App\Models\ClientOnlineCategory;
use App\Models\ClientOnlineSubCategory;
use App\Models\ClientOnlineVideo;
use App\Models\Client;
use App\Models\Clientuser;
use App\Models\RegisterClientOnlineCourses;
use App\Models\ClientCourseComment;
use App\Models\ClientOnlineVideoLike;
use App\Models\ClientCourseCommentLike;
use App\Models\ClientHomePage;
use App\Models\ClientCourseSubCommentLike;
use App\Models\ClientCourseSubComment;
use App\Models\ClientNotification;
use App\Models\ClientReadNotification;
use App\Models\ClientUserPurchasedCourse;

class ClientOnlineCourseFrontController extends ClientHomeController
{
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
    protected function courses($subdomainName,Request $request){

        $userPurchasedCourses = [];
        $subdomain = InputSanitise::checkDomain($request);
        if(!is_object($subdomain)){
            if('local' == \Config::get('app.env')){
                return Redirect::away('http://localvchip.com');
            } else {
                return Redirect::away('https://vchipedu.com/');
            }
        }
        $loginUser = Auth::guard('clientuser')->user();
        if(is_object($loginUser)){
            $clientResult = InputSanitise::checkUserClient($request, $loginUser);
            if( !is_object($clientResult)){
                return Redirect::away($clientResult);
            }
        }
        view::share('subdomain', $subdomain);
    	$hostName = InputSanitise::getCurrentClient($request);

        $courseCategories = ClientOnlineCategory::getCategoriesAssocaitedWithVideos($hostName);

        $courses = ClientOnlineCourse::getCourseAssocaitedWithVideos($hostName);

        if(is_object($loginUser)){
            $clientId = $loginUser->client_id;
            $userId = $loginUser->id;
            $userPurchasedCourses = ClientUserPurchasedCourse::getUserPurchasedCourses($clientId, $userId);
        }
        return view('client.front.onlineCourses.courses', compact('courseCategories', 'courses', 'userPurchasedCourses'));
    }

    /**
     *  return courses by categoryId by sub CategoryId or by userId
     */
    protected function getOnlineCourseByCatIdBySubCatId($subdomainName,Request $request){
        $result = [];
        $categoryId = $request->get('catId');
        $subcategoryId = $request->get('subcatId');
        $result['courses'] = ClientOnlineCourse::getOnlineCourseByCatIdBySubCatId($categoryId,$subcategoryId, $request);
        $loginUser = Auth::guard('clientuser')->user();
        if(is_object($loginUser)){
            $clientId = $loginUser->client_id;
            $userId = $loginUser->id;
            $result['userPurchasedCourses'] = ClientUserPurchasedCourse::getUserPurchasedCourses($clientId, $userId);
        }

        return $result;
    }

    /**
     *  return courses by categoryId by sub CategoryId by userId
     */
    protected function getRegisteredOnlineCourseByCatIdBySubCatId($subdomainName,Request $request){
        $result = [];
        $categoryId = $request->get('catId');
        $subcategoryId = $request->get('subcatId');
        $userId = $request->get('userId');
        $result['courses'] = ClientOnlineCourse::getRegisteredOnlineCourseByCatIdBySubCatId($categoryId,$subcategoryId, $userId);
        $clientId = Auth::guard('clientuser')->user()->client_id;
        $result['userPurchasedCourses'] = ClientUserPurchasedCourse::getUserPurchasedCourses($clientId, $userId);
        return $result;
    }

    protected function courseDetails($subdomainName, $id,Request $request){
	 	$onlineVideoIds = [];
        $isCoursePurchased = 'false';
        $courseId = json_decode(trim($id));
        $loginUser = Auth::guard('clientuser')->user();
        if(is_object($loginUser)){
            $clientResult = InputSanitise::checkUserClient($request, $loginUser);
            if( !is_object($clientResult)){
                return Redirect::away($clientResult);
            }
        }
        $course = ClientOnlineCourse::find($courseId);
        if(is_object($course)){
            $videos = ClientOnlineVideo::getClientCourseVideosByCourseId($courseId, $request);
            $isCourseRegistered = RegisterClientOnlineCourses::isCourseRegistered($courseId);
            if(is_object($loginUser)){
                $clientId = $loginUser->client_id;
                $userId = $loginUser->id;
                $isCoursePurchased = ClientUserPurchasedCourse::isCoursePurchased($clientId, $userId, $courseId);
            }
            return view('client.front.onlineCourses.course_details', compact('videos', 'courseId', 'isCourseRegistered', 'course', 'isCoursePurchased', 'loginUser'));
        }
        return redirect()->back();
    }

    /**
     *  show episode and its details by id
     */
    protected function episode($subdomainName, $id,Request $request,$subcomment=NULL){
        $videoId = json_decode(trim($id));
        $loginUser = Auth::guard('clientuser')->user();
        if(is_object($loginUser)){
            $clientResult = InputSanitise::checkUserClient($request, $loginUser);
            if( !is_object($clientResult)){
                return Redirect::away($clientResult);
            }
        }
        if(isset($videoId)){
            $video = ClientOnlineVideo::find($videoId);
            if(is_object($video)){
                if( false == $video->course->release_date <= date('Y-m-d H:i')){
                    return Redirect::to('online-courses');
                }
                $videoCourse = $video->course;
                $courseId = $videoCourse->id;
                $videoCoursePrice = $videoCourse->price;

                if( 0 == $video->is_free && $videoCourse->price > 0 ){
                    if(is_object($loginUser)){
                        $clientId = $loginUser->client_id;
                        $userId = $loginUser->id;
                        $isCoursePurchased = ClientUserPurchasedCourse::isCoursePurchased($clientId, $userId, $courseId);
                        if( 'false' ==  $isCoursePurchased){
                            return Redirect::to('online-courses');
                        }
                    } else {
                        return Redirect::to('online-courses');
                    }
                }

                $courseVideos = ClientOnlineVideo::getClientCourseVideosByCourseId($courseId, $request);
                $comments = ClientCourseComment::getCommentsByVideoId($video->id, $request);
                $likesCount = ClientOnlineVideoLike::getLikesByVideoId($id, $request);
                $commentLikesCount = ClientCourseCommentLike::getLikesByVideoId($id, $request);
                $subcommentLikesCount = ClientCourseSubCommentLike::getLikesByVideoId($id, $request);
                if(is_object($loginUser)){
                    $currentUser = $loginUser;
                    if($videoId > 0 || $subcomment > 0){
                        DB::beginTransaction();
                        try
                        {
                            if($videoId > 0 && $subcomment == NULL){
                                $readNotification = ClientReadNotification::readNotificationByModuleByModuleIdByUser(ClientNotification::CLIENTCOURSEVIDEO,$videoId,$currentUser->id);
                                if(is_object($readNotification)){
                                    DB::connection('mysql2')->commit();
                                }
                                Session::set('client_subcomment_area', 0);
                            } else {
                                Session::set('client_subcomment_area', $subcomment);
                            }
                            Session::set('client_course_comment', 0);
                        }
                        catch(\Exception $e)
                        {
                            DB::connection('mysql2')->rollback();
                            return redirect()->back()->withErrors('something went wrong.');
                        }
                    } else {
                        Session::set('client_subcomment_area', 0);
                    }
                    $isCoursePurchased = ClientUserPurchasedCourse::isCoursePurchased($loginUser->client_id, $currentUser->id, $courseId);
                } else {
                    $currentUser = NULL;
                    $isCoursePurchased = 'false';
                    if(0 == $video->is_free && $videoCoursePrice > 0){
                        return Redirect::to('online-courses');
                    }
                }
                return view('client.front.onlineCourses.episode', compact('video', 'courseVideos', 'comments', 'likesCount', 'commentLikesCount', 'currentUser', 'subcommentLikesCount', 'isCoursePurchased', 'subdomainName', 'videoCoursePrice'));
            }
        }
        return Redirect::to('online-courses');
    }

    /**
     * return course sub categories by categoryId
     */
    protected function getOnlineSubCategories(Request $request){
        $id = InputSanitise::inputInt($request->get('id'));
        if(isset($id)){
            return ClientOnlineSubCategory::getOnlineSubCategoriesByCategoryId($id, $request);
        }
    }

    /**
     * return course sub categories by categoryId with courses
     */
    protected function getOnlineSubCategoriesWithCourses($subdomainName, Request $request){
        $id = InputSanitise::inputInt($request->get('id'));
        if(isset($id)){
            return ClientOnlineSubCategory::getOnlineSubCategoriesWithCourses($id, $request);
        }
    }

    protected function registerClientUserCourse(Request $request){
        return RegisterClientOnlineCourses::registerCourse($request);
    }

    protected function getVideoCount($courses){
        $courseIds = [];
         if(false == $courses->isEmpty()){
            foreach($courses as $course){
                $courseIds[] = $course->id;
            }
            $courseIds = array_unique($courseIds);
        }
        return ClientOnlineVideo::getCoursevideoCount($courseIds);
    }

    protected function likeClientCourseVideo(Request $request){
        return ClientOnlineVideoLike::getLikeVideo($request);
    }

    protected function likeClientCourseVideoComment(Request $request){
        return ClientCourseCommentLike::getLikeVideoComment($request);
    }

    protected function likeClientCourseVideoSubComment(Request $request){
        return ClientCourseSubCommentLike::getLikeVideoSubComment($request);
    }

    protected function createClientCourseComment($subdomainName, Request $request){

        $videoId = strip_tags(trim($request->get('video_id')));
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $courseComment = ClientCourseComment::createComment($request);
            Session::put('client_course_comment', $courseComment->id);
            DB::connection('mysql2')->commit();
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
        }
        return $this->getComments($subdomainName, $videoId,$request);
    }

    protected function updateClientCourseComment($subdomainName, Request $request){
        $videoId = InputSanitise::inputInt($request->get('video_id'));
        $commentId = InputSanitise::inputInt($request->get('comment_id'));
        $commentBody = $request->get('comment');

        if(!empty($videoId) && !empty($commentId) && !empty($commentBody)){
            $comment = ClientCourseComment::where('client_online_video_id', $videoId)->where('client_id', Auth::guard('clientuser')->user()->client_id)->where('id', $commentId)->first();
            if(is_object($comment)){
                DB::connection('mysql2')->beginTransaction();
                try
                {
                    $comment->body = $commentBody;
                    $comment->save();
                    DB::connection('mysql2')->commit();
                    // Session::put('client_course_comment', $comment->id);
                    // $subdomain = explode('.',$request->getHost());
                    // return Redirect::to(route('client.episode', ['subdomain' => $subdomain[0],'id' => $videoId]));
                }
                catch(\Exception $e)
                {
                    DB::connection('mysql2')->rollback();
                }
            }
        }
        return $this->getComments($subdomainName, $videoId,$request);
    }

    protected function deleteClientCourseComment($subdomainName, Request $request){
        $videoId = InputSanitise::inputInt($request->get('video_id'));
        $commentId = InputSanitise::inputInt($request->get('comment_id'));
        if(!empty($videoId) && !empty($commentId)){
            $comment = ClientCourseComment::where('client_online_video_id', $videoId)->where('client_id', Auth::guard('clientuser')->user()->client_id)->where('id', $commentId)->first();
            if(is_object($comment)){
                DB::connection('mysql2')->beginTransaction();
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
                    // Session::put('client_course_comment', 0);
                    $comment->delete();
                    DB::connection('mysql2')->commit();
                    // return Redirect::to(route('client.episode', ['subdomain' => $subdomain[0],'id' => $videoId]));
                }
                catch(\Exception $e)
                {
                    DB::connection('mysql2')->rollback();
                }
            }
        }
        return $this->getComments($subdomainName, $videoId,$request);
    }

    protected function createClientCourseSubComment($subdomainName, Request $request){
        $videoId = strip_tags(trim($request->get('video_id')));
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $subComment = ClientCourseSubComment::createSubComment($request);
            Session::put('client_course_comment', $subComment->client_course_comment_id);

            $subdomain = explode('.',$request->getHost());
            $commentId = $request->get('comment_id');
            $subcommentId = $request->get('subcomment_id');
            $loginUser = Auth::guard('clientuser')->user();
            if($commentId > 0 && $subcommentId > 0){
                $parentComment = ClientCourseSubComment::where('id',$subcommentId)->where('client_id', $loginUser->client_id)->where('user_id', '!=', $loginUser->id)->first();
            } else {
                $parentComment = ClientCourseComment::where('id',$subComment->client_course_comment_id)->where('client_id', $loginUser->client_id)->where('user_id', '!=', $loginUser->id)->first();
            }
            if(is_object($parentComment)){
                $string = (strlen($parentComment->body) > 50) ? substr($parentComment->body,0,50).'...' : $parentComment->body;
                $notificationMessage = '<a href="'.$request->root().'/episode/'.$videoId.'/'.$subComment->id.'">A reply of your comment: '. trim($string, '<p></p>')  .'</a>';
                ClientNotification::addCommentNotification($notificationMessage, ClientNotification::USERCOURSEVIDEONOTIFICATION, $subComment->id,$subComment->user_id,$parentComment->user_id);
            }
            DB::connection('mysql2')->commit();
            // if(0 < $videoId){
            //     return Redirect::to(route('client.episode', ['subdomain' => $subdomain[0],'id' => $videoId]));
            // }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
        }
        return $this->getComments($subdomainName, $videoId,$request);
    }

    protected function updateClientCourseSubComment($subdomainName, Request $request){
        $videoId = InputSanitise::inputInt($request->get('video_id'));
        $commentId = InputSanitise::inputInt($request->get('comment_id'));
        $subcommentId = InputSanitise::inputInt($request->get('subcomment_id'));
        $commentBody = $request->get('subcomment');
        if(!empty($videoId) && !empty($commentId) && !empty($subcommentId) && !empty($commentBody)){
            $subcomment = ClientCourseSubComment::where('client_online_video_id', $videoId)->where('client_course_comment_id', $commentId)->where('client_id', Auth::guard('clientuser')->user()->client_id)->where('id', $subcommentId)->first();
            if(is_object($subcomment)){
                DB::connection('mysql2')->beginTransaction();
                try
                {
                    $subcomment->body = $commentBody;
                    $subcomment->save();
                    DB::connection('mysql2')->commit();
                    // Session::put('client_course_comment', $commentId);
                    // return Redirect::to(route('client.episode', ['subdomain' => $subdomain[0],'id' => $videoId]));
                }
                catch(\Exception $e)
                {
                    DB::connection('mysql2')->rollback();
                }
            }
        }
        return $this->getComments($subdomainName, $videoId,$request);
    }

    protected function deleteClientCourseSubComment($subdomainName, Request $request){
        $videoId = InputSanitise::inputInt($request->get('video_id'));
        $commentId = InputSanitise::inputInt($request->get('comment_id'));
        $subcommentId = InputSanitise::inputInt($request->get('subcomment_id'));
        if(!empty($videoId) && !empty($commentId) && !empty($subcommentId)){
            $subcomment = ClientCourseSubComment::where('client_online_video_id', $videoId)->where('client_course_comment_id', $commentId)->where('client_id', Auth::guard('clientuser')->user()->client_id)->where('id', $subcommentId)->first();
            if(is_object($subcomment)){
                DB::connection('mysql2')->beginTransaction();
                try
                {
                    if(is_object($subcomment->deleteLikes) && false == $subcomment->deleteLikes->isEmpty()){
                        foreach($subcomment->deleteLikes as $deleteLike){
                            $deleteLike->delete();
                        }
                    }
                    // Session::put('client_course_comment', $commentId);
                    $subcomment->delete();
                    DB::connection('mysql2')->commit();
                }
                catch(\Exception $e)
                {
                    DB::connection('mysql2')->rollback();
                }
            }
        }
        return $this->getComments($subdomainName, $videoId,$request);
    }

        /**
     *  return comments
     */
    protected function getComments($subdomainName, $videoId, Request $request){
        $comments = ClientCourseComment::getCommentsByVideoId($videoId, $request);
        $courseComment = [];
        foreach($comments as $comment){
            $courseComment['comments'][$comment->id]['body'] = $comment->body;
            $courseComment['comments'][$comment->id]['id'] = $comment->id;
            $courseComment['comments'][$comment->id]['client_online_video_id'] = $comment->client_online_video_id;
            $courseComment['comments'][$comment->id]['user_id'] = $comment->user_id;
            $courseComment['comments'][$comment->id]['user_name'] = $comment->getClientUser($subdomainName, $comment->user_id)->name;
            $courseComment['comments'][$comment->id]['updated_at'] = $comment->updated_at->diffForHumans();
            $courseComment['comments'][$comment->id]['user_image'] = $comment->getClientUser($subdomainName, $comment->user_id)->photo;
            if(is_file($comment->getClientUser($subdomainName, $comment->user_id)->photo) && true == preg_match('/clientUserStorage/',$comment->getClientUser($subdomainName, $comment->user_id)->photo)){
                $isImageExist = 'system';
            } else if(!empty($comment->getClientUser($subdomainName, $comment->user_id)->photo) && false == preg_match('/clientUserStorage/',$comment->getClientUser($subdomainName, $comment->user_id)->photo)){
                $isImageExist = 'other';
            } else {
                $isImageExist = 'false';
            }
            $courseComment['comments'][$comment->id]['image_exist'] = $isImageExist;
            if(is_object($comment->children) && false == $comment->children->isEmpty()){
                $courseComment['comments'][$comment->id]['subcomments'] = $this->getSubComments($subdomainName,$comment->children);
            }
        }
        $courseComment['commentLikesCount'] = ClientCourseCommentLike::getLikesByVideoId($videoId, $request);
        $courseComment['subcommentLikesCount'] = ClientCourseSubCommentLike::getLikesByVideoId($videoId, $request);

        return $courseComment;
    }

    /**
     *  return child comments
     */
    protected function getSubComments($subdomainName,$subComments){

        $videoChildComments = [];
        foreach($subComments as $subComment){
            $videoChildComments[$subComment->id]['body'] = $subComment->body;
            $videoChildComments[$subComment->id]['id'] = $subComment->id;
            $videoChildComments[$subComment->id]['client_online_video_id'] = $subComment->client_online_video_id;
            $videoChildComments[$subComment->id]['client_course_comment_id'] = $subComment->client_course_comment_id;
            $videoChildComments[$subComment->id]['user_name'] = $subComment->getClientUser($subdomainName, $subComment->user_id)->name;
            $videoChildComments[$subComment->id]['user_id'] = $subComment->user_id;
            $videoChildComments[$subComment->id]['updated_at'] = $subComment->updated_at->diffForHumans();
            $videoChildComments[$subComment->id]['user_image'] = $subComment->getClientUser($subdomainName, $subComment->user_id)->photo;
            if(is_file($subComment->getClientUser($subdomainName, $subComment->user_id)->photo) && true == preg_match('/clientUserStorage/',$subComment->getClientUser($subdomainName, $subComment->user_id)->photo)){
                $isImageExist = 'system';
            } else if(!empty($subComment->getClientUser($subdomainName, $subComment->user_id)->photo) && false == preg_match('/clientUserStorage/',$subComment->getClientUser($subdomainName, $subComment->user_id)->photo)){
                $isImageExist = 'other';
            } else {
                $isImageExist = 'false';
            }
            $videoChildComments[$subComment->id]['image_exist'] = $isImageExist;
            if(is_object($subComment->children) && false == $subComment->children->isEmpty()){
                $videoChildComments[$subComment->id]['subcomments'] = $this->getSubComments($subdomainName,$subComment->children);
            }
        }

        return $videoChildComments;
    }

}