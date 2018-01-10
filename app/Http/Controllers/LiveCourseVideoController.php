<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LiveCourse;
use App\Models\LiveVideo;
use App\Models\User;
use App\Models\AllPost;
use App\Models\RegisterLiveCourse;
use App\Models\LiveCourseVideoLike;
use App\Models\LiveCourseComment;
use App\Models\LiveCourseSubComment;
use App\Libraries\InputSanitise;
use Redirect,Validator, Auth, DB, Session;
use App\Models\LiveCourseCommentLike;
use App\Models\LiveCourseSubCommentLike;
use App\Models\ReadNotification;
use App\Models\Notification;
use App\Models\Add;

class LiveCourseVideoController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected $validateLiveCourseComment = [
            'comment' => 'required',
            'video_id' => 'required',
        ];
    protected $validateLiveCourseSubComment = [
            'subcomment' => 'required',
            'video_id' => 'required',
            'comment_id' => 'required',
        ];

    /**
     *  show list of all live courses associated with videos
     */
    protected function show(Request $request){
        $liveCourseCategoryIds = [];
        $liveCourses = LiveCourse::getLiveCoursesAssociatedWithVideos();
        if(false == $liveCourses->isEmpty()){
            foreach($liveCourses as $liveCourse){
                $liveCourseCategoryIds[] = $liveCourse->category_id;
            }
        }
        $date = date('Y-m-d');
        $ads = Add::getAdds($request->url(),$date);
        return view('liveCourses.live_courses', compact('liveCourses', 'liveCourseCategoryIds', 'ads'));
    }

    /**
     *  show live course details by Id
     */
    protected function showLiveCourse($id){
        $liveCourseId = json_decode($id);
        $liveCourse = LiveCourse::find($liveCourseId);
        if(is_object($liveCourse)){
            $liveVideos = LiveVideo::getLiveVideosByLiveCourseId($liveCourseId);
            $isLiveCourseRegistered = $this->isLiveCourseRegistered($liveCourseId);
            return view('liveCourses.live_course_details', compact('liveVideos', 'isLiveCourseRegistered', 'liveCourseId', 'liveCourse'));
        }
        return Redirect::to('liveCourse');
    }

    /**
     *  show live episode by Id
     */
    protected function showLiveEpisode($id,$subcomment=NULL){
        $liveVideo = LiveVideo::find(json_decode($id));
        if(is_object($liveVideo)){
            $liveCourseVideos = LiveVideo::getLiveVideosByLiveCourseId($liveVideo->live_course_id);
            $user = new User;
            $comments = LiveCourseComment::where('live_course_video_id', $liveVideo->id)->orderBy('id', 'desc')->get();
            $likesCount = LiveCourseVideoLike::getLikesByVideoId($liveVideo->id);
            $commentLikesCount = LiveCourseCommentLike::getLikesByVideoId($liveVideo->id);
            $subcommentLikesCount = LiveCourseSubCommentLike::getLikesByVideoId($liveVideo->id);
            if(is_object(Auth::user())){
                $currentUser = Auth::user()->id;
                if($id > 0 || $subcomment > 0){
                    DB::beginTransaction();
                    try
                    {
                        if($id > 0 && $subcomment == NULL){
                            $readNotification = ReadNotification::readNotificationByModuleByModuleIdByUser(Notification::ADMINLIVECOURSEVIDEO,$id,$currentUser);
                            if(is_object($readNotification)){
                                DB::commit();
                            }
                        } else {
                            Session::set('show_subcomment_area', $subcomment);
                        }
                        Session::set('live_course_comment', 0);
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
            return view('liveCourses.live_episode', compact('liveVideo', 'liveCourseVideos', 'comments', 'user', 'likesCount', 'commentLikesCount', 'currentUser', 'subcommentLikesCount'));
        }
        return Redirect::to('liveCourse');
    }

    /**
     * return live courses by categoryID
     */
    protected function getLiveCourseByCatId(Request $request){
        $result = [];
        $catId = $request->get('catId');
        $userId = $request->get('userId');
        if(isset($catId ) && empty($userId)){
            $result['liveCourses'] = LiveCourse::getLiveCourseByCatId($catId);
        } else {
            $result['liveCourses'] = LiveCourse::getRegisteredLiveCourseByUserIdByCatId($userId,$catId);
        }
        return $result;
    }

    /**
     *  show benefits of video course
     */
    protected function saveTimeSecurity(){
        return view('liveCourses.benifits_of_video_course');
    }

    /**
     *  return live courses by filter array
     */
    protected function getLiveCourseBySearchArray(Request $request){
        $result['liveCourses'] = LiveCourse::getLiveCourseBySearchArray($request);
        return $result;
    }

    protected function registerLiveCourse(Request $request){
        return RegisterLiveCourse::registerCourse($request);
    }

    protected function getRegisteredLiveCourseIds(){
        $registeredLiveCourseIds = [];
        if(is_object(Auth::user())){
            $userId = Auth::user()->id;
            $registeredLiveCourses = RegisterLiveCourse::getRegisteredLiveCoursesByUserId($userId);
            if(false == $registeredLiveCourses->isEmpty()){
                foreach($registeredLiveCourses as $registeredLiveCourse){
                    $registeredLiveCourseIds[] = $registeredLiveCourse->live_course_id;
                }
            }
        }
        return $registeredLiveCourseIds;
    }

    protected function isLiveCourseRegistered($liveCourseId){
        if(is_object(Auth::user())){
            $userId = Auth::user()->id;
            $registeredLiveCourses = RegisterLiveCourse::getRegisteredLiveCourseByUserIdByCourseId($userId, $liveCourseId);
            if(false == $registeredLiveCourses->isEmpty()){
                return 'true';
            }
        }
        return 'false';
    }

    protected function createLiveCourseComment(Request $request){
        $v = Validator::make($request->all(), $this->validateLiveCourseComment);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::beginTransaction();
        try
        {
            $courseComment = LiveCourseComment::createComment($request);
            // Session::put('live_course_comment', $courseComment->id);
            DB::commit();
            $videoId = strip_tags(trim($request->get('video_id')));
            // if(0 < $videoId){
            //     return redirect()->route('liveEpisode', ['id' => $videoId]);
            // }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            // return back()->withErrors('something went wrong.');
        }
        return $this->getComments($videoId);
    }

    /**
     *  return comments
     */
    protected function getComments($videoId){
        $comments = LiveCourseComment::where('live_course_video_id', $videoId)->orderBy('id', 'desc')->get();
        $videoComments = [];
        foreach($comments as $comment){
            $videoComments['comments'][$comment->id]['body'] = $comment->body;
            $videoComments['comments'][$comment->id]['id'] = $comment->id;
            $videoComments['comments'][$comment->id]['live_course_video_id'] = $comment->live_course_video_id;
            $videoComments['comments'][$comment->id]['user_id'] = $comment->user_id;
            $videoComments['comments'][$comment->id]['user_name'] = $comment->user->name;
            $videoComments['comments'][$comment->id]['updated_at'] = $comment->updated_at->diffForHumans();
            $videoComments['comments'][$comment->id]['user_image'] = $comment->user->photo;
            $videoComments['comments'][$comment->id]['image_exist'] = is_file($comment->user->photo);
            if(is_object($comment->children) && false == $comment->children->isEmpty()){
                $videoComments['comments'][$comment->id]['subcomments'] = $this->getSubComments($comment->children);
            }
        }
        $videoComments['commentLikesCount'] = LiveCourseCommentLike::getLikesByVideoId($videoId);
        $videoComments['subcommentLikesCount'] = LiveCourseSubCommentLike::getLikesByVideoId($videoId);

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
            $videoChildComments[$subComment->id]['live_course_video_id'] = $subComment->live_course_video_id;
            $videoChildComments[$subComment->id]['live_course_comment_id'] = $subComment->live_course_comment_id;
            $videoChildComments[$subComment->id]['user_name'] = $subComment->user->name;
            $videoChildComments[$subComment->id]['user_id'] = $subComment->user_id;
            $videoChildComments[$subComment->id]['updated_at'] = $subComment->updated_at->diffForHumans();
            $videoChildComments[$subComment->id]['user_image'] = $subComment->user->photo;
            $videoChildComments[$subComment->id]['image_exist'] = is_file($subComment->user->photo);
            if(is_object($subComment->children) && false == $subComment->children->isEmpty()){
                $videoChildComments[$subComment->id]['subcomments'] = $this->getSubComments($subComment->children);
            }
        }

        return $videoChildComments;
    }

    protected function updateLiveCourseComment(Request $request){
        $videoId = InputSanitise::inputInt($request->get('video_id'));
        $commentId = InputSanitise::inputInt($request->get('comment_id'));
        $commentBody = $request->get('comment');
        if(!empty($videoId) && !empty($commentId) && !empty($commentBody)){
            $comment = LiveCourseComment::where('live_course_video_id', $videoId)->where('id', $commentId)->first();
            if(is_object($comment)){
                DB::beginTransaction();
                try
                {
                    $comment->body = $commentBody;
                    $comment->save();
                    DB::commit();
                    // Session::put('live_course_comment', $comment->id);
                    // return redirect()->route('liveEpisode', ['id' => $videoId]);
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

    protected function deleteLiveCourseComment(Request $request){
        $videoId = InputSanitise::inputInt($request->get('video_id'));
        $commentId = InputSanitise::inputInt($request->get('comment_id'));
        if(!empty($videoId) && !empty($commentId)){
            $comment = LiveCourseComment::where('live_course_video_id', $videoId)->where('id', $commentId)->first();
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
                    // Session::put('live_course_comment', 0);
                    $comment->delete();
                    DB::commit();
                    // return redirect()->route('liveEpisode', ['id' => $videoId]);
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

    protected function createLiveCourseSubComment(Request $request){
        $v = Validator::make($request->all(), $this->validateLiveCourseSubComment);

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
            $subComment = LiveCourseSubComment::createSubComment($request);
            if($commentId > 0 && $subcommentId > 0){
                $parentComment = LiveCourseSubComment::where('id',$subcommentId)->where('user_id', '!=', Auth::user()->id)->first();
            } else {
                $parentComment = LiveCourseComment::where('id',$subComment->live_course_comment_id)->first();
            }
            if(is_object($parentComment)){
                $string = (strlen($parentComment->body) > 50) ? substr($parentComment->body,0,50).'...' : $parentComment->body;
                $notificationMessage = '<a href="'.$request->root().'/liveEpisode/'.$videoId.'/'.$subComment->id.'">A reply of your comment: '. trim($string, '<p></p>')  .'</a>';
                Notification::addCommentNotification($notificationMessage, Notification::USERLIVECOURSENOTIFICATION, $subComment->id,$subComment->user_id,$parentComment->user_id);
            }

            // Session::set('live_course_comment', $request->get('comment_id'));
            DB::commit();
            // if(0 < $videoId){
            //     return redirect()->route('liveEpisode', ['id' => $videoId]);
            // }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            // return back()->withErrors('something went wrong.');
        }
        return $this->getComments($videoId);
    }

    protected function updateLiveCourseSubComment(Request $request){
        $videoId = InputSanitise::inputInt($request->get('video_id'));
        $commentId = InputSanitise::inputInt($request->get('comment_id'));
        $subcommentId = InputSanitise::inputInt($request->get('subcomment_id'));
        $commentBody = $request->get('subcomment');
        if(!empty($videoId) && !empty($commentId) && !empty($subcommentId) && !empty($commentBody)){
            $subcomment = LiveCourseSubComment::where('live_course_video_id', $videoId)->where('live_course_comment_id', $commentId)->where('id', $subcommentId)->first();
            if(is_object($subcomment)){
                DB::beginTransaction();
                try
                {
                    $subcomment->body = $commentBody;
                    $subcomment->save();
                    DB::commit();
                    // Session::put('live_course_comment', $commentId);
                    // return redirect()->route('liveEpisode', ['id' => $videoId]);
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

    protected function deleteLiveCourseSubComment(Request $request){
        $videoId = InputSanitise::inputInt($request->get('video_id'));
        $commentId = InputSanitise::inputInt($request->get('comment_id'));
        $subcommentId = InputSanitise::inputInt($request->get('subcomment_id'));
        if(!empty($videoId) && !empty($commentId) && !empty($subcommentId)){
            $subcomment = LiveCourseSubComment::where('live_course_video_id', $videoId)->where('live_course_comment_id', $commentId)->where('id', $subcommentId)->first();
            if(is_object($subcomment)){
                DB::beginTransaction();
                try
                {
                    if(is_object($subcomment->deleteLikes) && false == $subcomment->deleteLikes->isEmpty()){
                        foreach($subcomment->deleteLikes as $deleteLike){
                            $deleteLike->delete();
                        }
                    }
                    // Session::put('live_course_comment', $commentId);
                    $subcomment->delete();
                    DB::commit();
                    // return redirect()->route('liveEpisode', ['id' => $videoId]);
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

    protected function likeLiveVideo(Request $request){
        return LiveCourseVideoLike::getLikeVideo($request);
    }

    protected function likeLiveVideoComment(Request $request){
        return LiveCourseCommentLike::getLikeVideoComment($request);
    }

    protected function likeLiveVideoSubComment(Request $request){
        return LiveCourseSubCommentLike::getLikeVideoSubComment($request);
    }
}