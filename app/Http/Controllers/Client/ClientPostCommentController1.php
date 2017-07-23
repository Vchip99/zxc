<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CourseCategory;
use App\Models\CourseCourse;
use App\Models\CourseVideo;
use App\Models\ClientAllPost;
use App\Models\ClientAllComment;
use App\Models\Clientuser;
use App\Models\ClientAllSubComment;
use DB, Session;
use Validator, Redirect;

class ClientPostCommentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('clientuser');
    }

    protected $validateAllPost = [
            'title' => 'required',
            'question' => 'required',
            'all_post_module_id' => 'required',
            'client_id' => 'required',
        ];
    protected $validateUpdateAllPost = [
            'update_question' => 'required',
            'all_post_module_id' => 'required',
            'client_id' => 'required',
        ];
    protected $validateAllComment = [
            'comment' => 'required',
            'client_all_post_id' => 'required',
            'client_id' => 'required',
        ];
    protected $validateAllChildComment = [
            'subcomment' => 'required',
            'all_post_id' => 'required',
            'all_comment_id' => 'required',
            'client_id' => 'required',
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
        $post = ClientAllPost::createPost($request);
        $postModuleId = strip_tags(trim($request->get('all_post_module_id')));
        $episodeId = strip_tags(trim($request->get('episode_id')));
        $subdomain = explode('.',$request->getHost());
        if( 1 == $postModuleId && 0 < $episodeId){
            return Redirect::to(route('client.episode', ['subdomain' => $subdomain[0],'id' => $episodeId]));
        } else {
            return redirect()->back();
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
        $comment = ClientAllComment::createComment($request);
        $postModuleId = strip_tags(trim($request->get('all_post_module_id')));

        $episodeId = strip_tags(trim($request->get('episode_id')));
        $subdomain = explode('.',$request->getHost());
        if( 1 == $postModuleId && 0 < $episodeId){
            return Redirect::to(route('client.episode', ['subdomain' => $subdomain[0],'id' => $episodeId]));
        } else {
            return redirect()->back();
        }
    }

    /**
     *  create sub comment
     */
    protected function createClientAllSubComment(Request $request){
        $v = Validator::make($request->all(), $this->validateAllChildComment);

        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $subcomment = ClientAllSubComment::createSubComment($request);
            if(is_object($subcomment)){
                DB::connection('mysql2')->commit();
                Session::put('client_all_comment_area', 0);
                Session::put('client_all_post_area', $subcomment->all_post_id);
                $postModuleId = strip_tags(trim($request->get('all_post_module_id')));
                $episodeId = strip_tags(trim($request->get('episode_id')));
                $subdomain = explode('.',$request->getHost());

                if( 1 == $postModuleId && 0 < $episodeId){
                    return Redirect::to(route('client.episode', ['subdomain' => $subdomain[0],'id' => $episodeId]));
                }
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return back()->withErrors('something went wrong.');
        }
        return redirect()->back();

    }

   /**
     *  update post
     */
    protected function updateClientAllPost(Request $request){
        $postId = $request->get('all_post_id');
        $question = $request->get('update_question');
        if(!empty($postId) && !empty($question)){
            $post = ClientAllPost::find($postId);
            if(is_object($post)){
                DB::connection('mysql2')->beginTransaction();
                try
                {
                    $post->body = $question;
                    $post->save();
                    DB::connection('mysql2')->commit();
                    Session::put('client_all_comment_area', 0);
                    Session::put('client_all_post_area', $post->id);
                    $postModuleId = strip_tags(trim($request->get('all_post_module_id')));
                    $episodeId = strip_tags(trim($request->get('episode_id')));
                    $subdomain = explode('.',$request->getHost());
                    if( 1 == $postModuleId && 0 < $episodeId){
                        return Redirect::to(route('client.episode', ['subdomain' => $subdomain[0],'id' => $episodeId]));
                    }
                }
                catch(\Exception $e)
                {
                    DB::connection('mysql2')->rollback();
                    return back()->withErrors('something went wrong.');
                }
            }
        }
        return redirect()->back();
    }

    protected function deleteClientAllPost(Request $request){
        $post = ClientAllPost::find(json_decode($request->get('all_post_id')));

        if(is_object($post)){
            DB::connection('mysql2')->beginTransaction();
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
                DB::connection('mysql2')->commit();
                Session::put('client_all_comment_area', 0);
                Session::put('client_all_post_area', 0);
                $postModuleId = strip_tags(trim($request->get('all_post_module_id')));
                $episodeId = strip_tags(trim($request->get('episode_id')));
                $subdomain = explode('.',$request->getHost());
                if( 1 == $postModuleId && 0 < $episodeId){
                    return Redirect::to(route('client.episode', ['subdomain' => $subdomain[0],'id' => $episodeId]));
                }
            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
                return back()->withErrors('something went wrong.');
            }
        }
         return redirect()->back();
    }

    protected function updateClientAllSubComment(Request $request){

        $postId = $request->get('all_post_id');
        $commentId = $request->get('all_comment_id');
        $subcommentId = $request->get('all_subcomment_id');
        $commentBody = $request->get('comment');
        $clientId = $request->get('client_id');
        if(!empty($postId) && !empty($commentId) && !empty($commentBody) && !empty($subcommentId) && !empty($clientId)){

            $subcomment = ClientAllSubComment::where('client_all_post_id', $postId)->where('client_all_comment_id', $commentId)->where('client_id', $clientId)->where('id', $subcommentId)->first();

            if(is_object($subcomment)){
                DB::connection('mysql2')->beginTransaction();
                try
                {
                    $subcomment->body = $commentBody;
                    $parentSubComment = ClientAllSubComment::find($subcomment->parent_id);

                    if(is_object($parentSubComment) && $parentSubComment->user_id !== Auth::user()->id){
                        $subcomment->body = $commentBody;
                        $user = User::find($subcomment->user_id);
                        if(is_object($user)){
                            $subcomment->body = '<b>'.$user->name.'</b> '.$commentBody;
                        }
                    } else {
                        $subcomment->body = $commentBody;
                    }
                    $subcomment->client_all_post_id = $postId;
                    $subcomment->client_all_comment_id = $commentId;
                    $subcomment->save();
                    DB::connection('mysql2')->commit();
                    Session::put('client_all_comment_area', 0);
                    Session::put('client_all_post_area', $subcomment->client_all_post_id);
                    $postModuleId = strip_tags(trim($request->get('all_post_module_id')));
                    $episodeId = strip_tags(trim($request->get('episode_id')));
                    $subdomain = explode('.',$request->getHost());

                    if( 1 == $postModuleId && 0 < $episodeId){
                        return Redirect::to(route('client.episode', ['subdomain' => $subdomain[0],'id' => $episodeId]));
                    }
                }
                catch(\Exception $e)
                {
                    DB::connection('mysql2')->rollback();
                    return back()->withErrors('something went wrong.');
                }
            }
        }
        return redirect()->back();
    }

    protected function deleteClientAllSubComment(Request $request){
        $postId = $request->get('all_post_id');
        $commentId = $request->get('all_comment_id');
        $subcommentId = $request->get('all_subcomment_id');
        $clientId = $request->get('client_id');
        if(!empty($postId) && !empty($commentId) && !empty($clientId) && !empty($subcommentId) ){
            $subcomment = ClientAllSubComment::where('client_all_post_id', $postId)->where('client_all_comment_id', $commentId)->where('client_id', $clientId)->where('id', $subcommentId)->first();

            if(is_object($subcomment)){
                DB::connection('mysql2')->beginTransaction();
                try
                {
                    Session::put('client_all_comment_area', 0);
                    Session::put('client_all_post_area', $subcomment->client_all_post_id);
                    $subcomment->delete();
                    DB::connection('mysql2')->commit();

                    $postModuleId = strip_tags(trim($request->get('all_post_module_id')));
                    $episodeId = strip_tags(trim($request->get('episode_id')));
                    $subdomain = explode('.',$request->getHost());

                    if( 1 == $postModuleId && 0 < $episodeId){
                        return Redirect::to(route('client.episode', ['subdomain' => $subdomain[0],'id' => $episodeId]));
                    }
                }
                catch(\Exception $e)
                {
                    DB::connection('mysql2')->rollback();
                    return back()->withErrors('something went wrong.');
                }
            }
        }
        return redirect()->back();
    }


    /**
     *  update comment
     */
    protected function updateClientAllComment(Request $request){

        $postId = $request->get('all_post_id');
        $commentId = $request->get('all_comment_id');
        $commentBody = $request->get('comment');
        $clientId = $request->get('client_id');

        if(!empty($postId) && !empty($commentId) && !empty($clientId) && !empty($commentBody) ){
            $subcomment = ClientAllComment::where('client_all_post_id', $postId)->where('client_id', $clientId)->where('id', $commentId)->first();

            if(is_object($subcomment)){
                DB::connection('mysql2')->beginTransaction();
                try
                {
                    $subcomment->body = $commentBody;
                    $subcomment->save();
                    DB::connection('mysql2')->commit();
                    Session::put('client_all_comment_area', 0);
                    Session::put('client_all_post_area', $subcomment->client_all_post_id);
                    $postModuleId = strip_tags(trim($request->get('all_post_module_id')));
                    $episodeId = strip_tags(trim($request->get('episode_id')));
                    $subdomain = explode('.',$request->getHost());
                    if( 1 == $postModuleId && 0 < $episodeId){
                        return Redirect::to(route('client.episode', ['subdomain' => $subdomain[0],'id' => $episodeId]));
                    }
                }
                catch(\Exception $e)
                {
                    DB::connection('mysql2')->rollback();
                    return back()->withErrors('something went wrong.');
                }
            }
        }

        return redirect()->back();
    }

    protected function deleteClientAllComment(Request $request){
        $postId = $request->get('all_post_id');
        $commentId = $request->get('all_comment_id');
        $clientId = $request->get('client_id');
        if(!empty($postId) && !empty($commentId) && !empty($clientId)){
            $comment = ClientAllComment::where('client_all_post_id', $postId)->where('client_id', $clientId)->where('id', $commentId)->first();

            if(is_object($comment)){
                DB::connection('mysql2')->beginTransaction();
                try
                {
                    if(is_object($comment->children) && false == $comment->children->isEmpty()){
                        foreach($comment->children as $subComment){
                            $subComment->delete();
                        }
                    }
                    Session::put('client_all_comment_area', 0);
                    Session::put('client_all_post_area', $comment->client_all_post_id);
                    $comment->delete();
                    DB::connection('mysql2')->commit();

                    $postModuleId = strip_tags(trim($request->get('all_post_module_id')));
                    $episodeId = strip_tags(trim($request->get('episode_id')));
                    $subdomain = explode('.',$request->getHost());

                    if( 1 == $postModuleId && 0 < $episodeId){
                        return Redirect::to(route('client.episode', ['subdomain' => $subdomain[0],'id' => $episodeId]));
                    }
                }
                catch(\Exception $e)
                {
                    DB::connection('mysql2')->rollback();
                    return back()->withErrors('something went wrong.');
                }
            }
        }
        return redirect()->back();
    }

}