<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CourseCategory;
use App\Models\CourseCourse;
use App\Models\CourseVideo;
use App\Models\AllPost;
use App\Models\AllComment;
use App\Models\AllSubComment;
use App\Models\User;
use DB;
use Validator, Redirect, Session;

class PostCommentController extends Controller
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

    protected $validateAllPost = [
            'title' => 'required',
            'question' => 'required',
            'all_post_module_id' => 'required',
        ];
    protected $validateAllComment = [
            'comment' => 'required',
            'all_post_id' => 'required',
        ];
    protected $validateAllSubComment = [
            'subcomment' => 'required',
            'all_post_id' => 'required',
            'all_comment_id' => 'required',
        ];

    /**
     *  create post
     */
    protected function createAllPost(Request $request){
        $v = Validator::make($request->all(), $this->validateAllPost);

        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::beginTransaction();
        try
        {
            $post = AllPost::createPost($request);
            DB::commit();
            Session::put('show_all_comment_area', 0);
            Session::put('show_all_post_area', $post->id);
            $postModuleId = strip_tags(trim($request->get('all_post_module_id')));

            $episodeId = strip_tags(trim($request->get('episode_id')));
            if( 1 == $postModuleId && 0 < $episodeId){
                return redirect()->route('episode', ['id' => $episodeId]);
            }
            if( 2 == $postModuleId && 0 < $episodeId){
                return redirect()->route('liveEpisode', ['id' => $episodeId]);
            }
            $projectId = strip_tags(trim($request->get('project_id')));
            if( 3 == $postModuleId && 0 < $projectId){
                return redirect()->route('project', ['id' => $projectId]);
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
    }

    /**
     *  create post comment
     */
    protected function createAllPostComment(Request $request){
        $v = Validator::make($request->all(), $this->validateAllComment);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::beginTransaction();
        try
        {
            $comment = AllComment::createComment($request);
            DB::commit();
            Session::put('show_all_comment_area', 0);
            Session::put('show_all_post_area', $comment->all_post_id);
            $postModuleId = strip_tags(trim($request->get('all_post_module_id')));

            $episodeId = strip_tags(trim($request->get('episode_id')));
            if( 1 == $postModuleId && 0 < $episodeId){
                return redirect()->route('episode', ['id' => $episodeId]);
            }
            if( 2 == $postModuleId && 0 < $episodeId){
                return redirect()->route('liveEpisode', ['id' => $episodeId]);
            }
            $projectId = strip_tags(trim($request->get('project_id')));
            if( 3 == $postModuleId && 0 < $projectId){
                return redirect()->route('project', ['id' => $projectId]);
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
    }

    /**
     *  create post child comment
     */
    protected function createAllSubComment(Request $request){
        $v = Validator::make($request->all(), $this->validateAllSubComment);

        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::beginTransaction();
        try
        {
            $subcomment = AllSubComment::createSubComment($request);
            DB::commit();
            Session::put('show_all_comment_area', 0);
            Session::put('show_all_post_area', $subcomment->all_post_id);
            $postModuleId = strip_tags(trim($request->get('all_post_module_id')));

            $episodeId = strip_tags(trim($request->get('episode_id')));
            if( 1 == $postModuleId && 0 < $episodeId){
                return redirect()->route('episode', ['id' => $episodeId]);
            }
            if( 2 == $postModuleId && 0 < $episodeId){
                return redirect()->route('liveEpisode', ['id' => $episodeId]);
            }
            $projectId = strip_tags(trim($request->get('project_id')));
            if( 3 == $postModuleId && 0 < $projectId){
                return redirect()->route('project', ['id' => $projectId]);
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
    }

     protected function updateAllPost(Request $request){

        $postId = $request->get('all_post_id');
        $question = $request->get('update_question');
        if(!empty($postId) && !empty($question)){
            $post = AllPost::find($postId);

            if(is_object($post)){
                DB::beginTransaction();
                try
                {
                    $post->body = $question;
                    $post->save();
                    DB::commit();
                    Session::put('show_all_comment_area', 0);
                    Session::put('show_all_post_area', $post->id);
                    $postModuleId = strip_tags(trim($request->get('all_post_module_id')));

                    $episodeId = strip_tags(trim($request->get('episode_id')));
                    if( 1 == $postModuleId && 0 < $episodeId){
                        return redirect()->route('episode', ['id' => $episodeId]);
                    }
                    if( 2 == $postModuleId && 0 < $episodeId){
                        return redirect()->route('liveEpisode', ['id' => $episodeId]);
                    }
                    $projectId = strip_tags(trim($request->get('project_id')));
                    if( 3 == $postModuleId && 0 < $projectId){
                        return redirect()->route('project', ['id' => $projectId]);
                    }
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return back()->withErrors('something went wrong.');
                }
            }
        }
        return Redirect::to('/');
    }

    protected function deleteAllPost(Request $request){
        $post = AllPost::find(json_decode($request->get('all_post_id')));

        if(is_object($post)){
            DB::beginTransaction();
            try
            {
                if(is_object($post->deleteComments) && false == $post->deleteComments->isEmpty()){
                    foreach($post->deleteComments as $comment){
                        if(is_object($comment->children) && false == $comment->children->isEmpty()){
                            foreach($comment->children as $subComment){
                                $subComment->delete();
                            }
                        }
                        $comment->delete();
                    }
                }
                $post->delete();
                DB::commit();
                Session::put('show_all_comment_area', 0);
                Session::put('show_all_post_area', 0);
                $postModuleId = strip_tags(trim($request->get('all_post_module_id')));
                $episodeId = strip_tags(trim($request->get('episode_id')));
                if( 1 == $postModuleId && 0 < $episodeId){
                    return redirect()->route('episode', ['id' => $episodeId]);
                }
                if( 2 == $postModuleId && 0 < $episodeId){
                    return redirect()->route('liveEpisode', ['id' => $episodeId]);
                }
                $projectId = strip_tags(trim($request->get('project_id')));
                if( 3 == $postModuleId && 0 < $projectId){
                    return redirect()->route('project', ['id' => $projectId]);
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('/');
    }

    protected function updateAllComment(Request $request){
        $postId = $request->get('all_post_id');
        $commentId = $request->get('all_comment_id');
        $commentBody = $request->get('comment');
        if(!empty($postId) && !empty($commentId) && !empty($commentBody)){
            $comment = AllComment::where('all_post_id', $postId)->where('id', $commentId)->first();
            if(is_object($comment)){
                DB::beginTransaction();
                try
                {
                    $comment->body = $commentBody;
                    $comment->all_post_id = $postId;
                    $comment->save();
                    DB::commit();
                    Session::put('show_all_comment_area', 0);
                    Session::put('show_all_post_area', $comment->all_post_id);
                    $postModuleId = strip_tags(trim($request->get('all_post_module_id')));

                    $episodeId = strip_tags(trim($request->get('episode_id')));
                    if( 1 == $postModuleId && 0 < $episodeId){
                        return redirect()->route('episode', ['id' => $episodeId]);
                    }
                    if( 2 == $postModuleId && 0 < $episodeId){
                        return redirect()->route('liveEpisode', ['id' => $episodeId]);
                    }
                    $projectId = strip_tags(trim($request->get('project_id')));
                    if( 3 == $postModuleId && 0 < $projectId){
                        return redirect()->route('project', ['id' => $projectId]);
                    }
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return back()->withErrors('something went wrong.');
                }
            }
        }
        return Redirect::to('/');
    }

    protected function deleteAllComment(Request $request){
        $comment = AllComment::find(json_decode($request->get('all_comment_id')));
        if(is_object($comment)){
            DB::beginTransaction();
            try
            {
                if(is_object($comment->children) && false == $comment->children->isEmpty()){
                    foreach($comment->children as $subComment){
                        $subComment->delete();
                    }
                }
                Session::put('show_all_comment_area', 0);
                Session::put('show_all_post_area', $comment->all_post_id);
                $comment->delete();
                DB::commit();
                $postModuleId = strip_tags(trim($request->get('all_post_module_id')));
                $episodeId = strip_tags(trim($request->get('episode_id')));
                if( 1 == $postModuleId && 0 < $episodeId){
                    return redirect()->route('episode', ['id' => $episodeId]);
                }
                if( 2 == $postModuleId && 0 < $episodeId){
                    return redirect()->route('liveEpisode', ['id' => $episodeId]);
                }
                $projectId = strip_tags(trim($request->get('project_id')));
                if( 3 == $postModuleId && 0 < $projectId){
                    return redirect()->route('project', ['id' => $projectId]);
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('/');
    }

    protected function updateAllSubComment(Request $request){
        $postId = $request->get('all_post_id');
        $commentId = $request->get('all_comment_id');
        $subcommentId = $request->get('all_subcomment_id');
        $commentBody = $request->get('comment');
        if(!empty($postId) && !empty($commentId) && !empty($commentBody)){
            $comment = AllSubComment::where('all_post_id', $postId)->where('all_comment_id', $commentId)->where('id', $subcommentId)->first();
            if(is_object($comment)){
                DB::beginTransaction();
                try
                {
                    $comment->body = $commentBody;
                    $parentSubComment = AllSubComment::find($comment->parent_id);

                    if(is_object($parentSubComment) && $parentSubComment->user_id !== Auth::user()->id){
                        $comment->body = $commentBody;
                        $user = User::find($comment->user_id);
                        if(is_object($user)){
                            $comment->body = '<b>'.$user->name.'</b> '.$commentBody;
                        }
                    } else {
                        $comment->body = $commentBody;
                    }
                    $comment->all_post_id = $postId;
                    $comment->all_comment_id = $commentId;
                    $comment->save();
                    DB::commit();
                    Session::put('show_all_comment_area', 0);
                    Session::put('show_all_post_area', $comment->all_post_id);

                    $postModuleId = strip_tags(trim($request->get('all_post_module_id')));
                    $episodeId = strip_tags(trim($request->get('episode_id')));
                    if( 1 == $postModuleId && 0 < $episodeId){
                        return redirect()->route('episode', ['id' => $episodeId]);
                    }
                    if( 2 == $postModuleId && 0 < $episodeId){
                        return redirect()->route('liveEpisode', ['id' => $episodeId]);
                    }
                    $projectId = strip_tags(trim($request->get('project_id')));
                    if( 3 == $postModuleId && 0 < $projectId){
                        return redirect()->route('project', ['id' => $projectId]);
                    }
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return back()->withErrors('something went wrong.');
                }
            }
        }
        return Redirect::to('/');
    }

    protected function deleteAllSubComment(Request $request){
        $subComment = AllSubComment::find(json_decode($request->get('subcomment_id')));
        if(is_object($subComment)){
            DB::beginTransaction();
            try
            {
                Session::put('show_all_comment_area', 0);
                Session::put('show_all_post_area', $subComment->all_post_id);
                $subComment->delete();
                DB::commit();

                $postModuleId = strip_tags(trim($request->get('all_post_module_id')));
                $episodeId = strip_tags(trim($request->get('episode_id')));
                if( 1 == $postModuleId && 0 < $episodeId){
                    return redirect()->route('episode', ['id' => $episodeId]);
                }
                if( 2 == $postModuleId && 0 < $episodeId){
                    return redirect()->route('liveEpisode', ['id' => $episodeId]);
                }
                $projectId = strip_tags(trim($request->get('project_id')));
                if( 3 == $postModuleId && 0 < $projectId){
                    return redirect()->route('project', ['id' => $projectId]);
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('discussion');
    }

}