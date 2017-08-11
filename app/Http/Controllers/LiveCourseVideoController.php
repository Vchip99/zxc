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
    protected function show(){
        $liveCourseCategoryIds = [];
        $liveCourses = LiveCourse::getLiveCoursesAssociatedWithVideos();
        if(false == $liveCourses->isEmpty()){
            foreach($liveCourses as $liveCourse){
                $liveCourseCategoryIds[] = $liveCourse->category_id;
            }
        }
        return view('liveCourses.live_courses', compact('liveCourses', 'liveCourseCategoryIds'));
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
    protected function showLiveEpisode($id){
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
            Session::put('live_course_comment', $courseComment->id);
            DB::commit();
            $videoId = strip_tags(trim($request->get('video_id')));
            if(0 < $videoId){
                return redirect()->route('liveEpisode', ['id' => $videoId]);
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return back()->withErrors('something went wrong.');
        }
        return Redirect::to('liveCourse');
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
                    Session::put('live_course_comment', $comment->id);
                    return redirect()->route('liveEpisode', ['id' => $videoId]);
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return back()->withErrors('something went wrong.');
                }
            }
        }
        return Redirect::to('liveCourse');
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
                    Session::put('live_course_comment', 0);
                    $comment->delete();
                    DB::commit();
                    return redirect()->route('liveEpisode', ['id' => $videoId]);
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return back()->withErrors('something went wrong.');
                }
            }
        }
        return Redirect::to('liveCourse');
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
            $subComment = LiveCourseSubComment::createSubComment($request);
            Session::put('live_course_comment', $request->get('comment_id'));
            DB::commit();
            $videoId = strip_tags(trim($request->get('video_id')));
            if(0 < $videoId){
                return redirect()->route('liveEpisode', ['id' => $videoId]);
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return back()->withErrors('something went wrong.');
        }
        return Redirect::to('liveCourse');
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
                    Session::put('live_course_comment', $commentId);
                    return redirect()->route('liveEpisode', ['id' => $videoId]);
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return back()->withErrors('something went wrong.');
                }
            }
        }
        return Redirect::to('liveCourse');
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
                    Session::put('live_course_comment', $commentId);
                    $subcomment->delete();
                    DB::commit();
                    return redirect()->route('liveEpisode', ['id' => $videoId]);
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return back()->withErrors('something went wrong.');
                }
            }
        }
        return Redirect::to('liveCourse');
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