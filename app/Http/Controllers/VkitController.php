<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VkitProject;
use App\Models\VkitCategory;
use App\Models\User;
use App\Models\VkitProjectSubComment;
use App\Models\VkitProjectComment;
use App\Models\RegisterProject;
use App\Models\VkitProjectSubCommentLike;
use App\Models\VkitProjectCommentLike;
use App\Models\AllSubCommentLike;
use DB, Auth, Session;
use Validator, Redirect,Hash;
use App\Libraries\InputSanitise;
use App\Models\VkitProjectLike;

class VkitController extends Controller
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

    protected $validateProjectComment = [
        'comment' => 'required',
        'project_id' => 'required',
    ];

    protected $validateProjectSubComment = [
        'subcomment' => 'required',
        'project_id' => 'required',
        'comment_id' => 'required',
    ];

    /**
     *  show vkits projects
     */
    protected function show(){
        $projects = VkitProject::all();
        $vkitCategories = VkitCategory::getProjectCategoriesAssociatedWithProject();
        return view('vkits.vkits', compact('projects', 'vkitCategories'));
    }

    /**
     *  show vkits project by Id
     */
    protected function vkitproject($id){
        $project = VkitProject::find(json_decode($id));
        if(is_object($project)){
            $projects = VkitProject::all();
            $user = new User;
            $comments = VkitProjectComment::where('vkit_project_id', $id)->orderBy('id', 'desc')->get();

            $registeredProjectIds = $this->getRegisteredProjectIds();
            $likesCount = VkitProjectLike::getLikesByVkitProjectId($id);
            $commentLikesCount = VkitProjectCommentLike::getLikesByVkitProjectId($id);
            $subcommentLikesCount = VkitProjectSubCommentLike::getLikesByVkitProjectId($id);
            if(is_object(Auth::user())){
                $currentUser = Auth::user()->id;
            } else {
                $currentUser = 0;
            }
            return view('vkits.vkitproject', compact('project', 'projects', 'comments', 'user', 'registeredProjectIds', 'likesCount', 'commentLikesCount', 'currentUser', 'subcommentLikesCount'));
        }
        return Redirect::to('vkits');
    }

    /**
     *  return vkits project by categoryId
     */
    protected function getVkitProjectsByCategoryId(Request $request){
        $categoryId = $request->get('id');
        $userId = $request->get('userId');
        if(isset($categoryId) && empty($userId)){
            return VkitProject::getVkitProjectsByCategoryId($categoryId);
        } else {
            return VkitProject::getRegisteredVkitProjectsByUserIdByCategoryId($userId, $categoryId);
        }
    }

    /**
     *  return vkits project by filter array
     */
    protected function getVkitProjectsBySearchArray(Request $request){
        return VkitProject::getVkitProjectsBySearchArray($request);
    }

    protected function registerProject(Request $request){
        return RegisterProject::addFavouriteProject($request);
    }

    protected function getRegisteredProjectIds(){
        $registeredProjectIds = [];
        if(is_object(Auth::user())){
            $userId = Auth::user()->id;
            $registeredProjects = RegisterProject::getRegisteredVkitProjectsByUserId($userId);
            if(false == $registeredProjects->isEmpty()){
                foreach($registeredProjects as $registeredProject){
                    $registeredProjectIds[] = $registeredProject->project_id;
                }
            }
        }
        return $registeredProjectIds;
    }

    protected function createProjectComment(Request $request){
        $v = Validator::make($request->all(), $this->validateProjectComment);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::beginTransaction();
        try
        {
            $VkitProjectComment = VkitProjectComment::createComment($request);
            Session::put('project_comment_area', $VkitProjectComment->id);
            DB::commit();
            $projectId = strip_tags(trim($request->get('project_id')));
            if(0 < $projectId){
                return redirect()->route('vkitproject', ['id' => $projectId]);
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return back()->withErrors('something went wrong.');
        }
        return Redirect::to('vkits');
    }

    protected function updateVkitProjectComment(Request $request){
        $projectId = InputSanitise::inputInt($request->get('project_id'));
        $commentId = InputSanitise::inputInt($request->get('comment_id'));
        $commentBody = $request->get('comment');
        if(!empty($projectId) && !empty($commentId) && !empty($commentBody)){
            $comment = VkitProjectComment::where('vkit_project_id', $projectId)->where('id', $commentId)->first();
            if(is_object($comment)){
                DB::beginTransaction();
                try
                {
                    $comment->body = $commentBody;
                    $comment->save();
                    DB::commit();
                    Session::put('project_comment_area', $comment->id);
                    return redirect()->route('vkitproject', ['id' => $projectId]);
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return back()->withErrors('something went wrong.');
                }
            }
        }
        return Redirect::to('vkits');
    }

    protected function deleteVkitProjectComment(Request $request){
        $projectId = InputSanitise::inputInt($request->get('project_id'));
        $commentId = InputSanitise::inputInt($request->get('comment_id'));
        if(!empty($projectId) && !empty($commentId)){
            $comment = VkitProjectComment::where('vkit_project_id', $projectId)->where('id', $commentId)->first();

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
                    Session::put('project_comment_area', 0);
                    $comment->delete();
                    DB::commit();
                    return redirect()->route('vkitproject', ['id' => $projectId]);
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return back()->withErrors('something went wrong.');
                }
            }
        }
        return Redirect::to('vkits');
    }

    protected function createVkitProjectSubComment(Request $request){
        $v = Validator::make($request->all(), $this->validateProjectSubComment);

        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::beginTransaction();
        try
        {
            $VkitProjectSubComment = VkitProjectSubComment::createSubComment($request);
            Session::put('project_comment_area', $request->get('comment_id'));
            DB::commit();
            $projectId = strip_tags(trim($request->get('project_id')));
            if(0 < $projectId){
                return redirect()->route('vkitproject', ['id' => $projectId]);
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return back()->withErrors('something went wrong.');
        }
        return Redirect::to('vkits');
    }

    protected function updateVkitProjectSubComment(Request $request){
        $projectId = InputSanitise::inputInt($request->get('project_id'));
        $commentId = InputSanitise::inputInt($request->get('comment_id'));
        $subcommentId = InputSanitise::inputInt($request->get('subcomment_id'));
        $commentBody = $request->get('subcomment');
        if(!empty($projectId) && !empty($commentId) && !empty($subcommentId) && !empty($commentBody)){
            $subcomment = VkitProjectSubComment::where('vkit_project_id', $projectId)->where('vkit_project_comment_id', $commentId)->where('id', $subcommentId)->first();
            if(is_object($subcomment)){
                DB::beginTransaction();
                try
                {
                    $subcomment->body = $commentBody;
                    $subcomment->save();
                    DB::commit();
                    Session::put('project_comment_area', $commentId);
                    return redirect()->route('vkitproject', ['id' => $projectId]);
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return back()->withErrors('something went wrong.');
                }
            }
        }
        return Redirect::to('vkits');
    }

    protected function deleteVkitProjectSubComment(Request $request){
        $projectId = InputSanitise::inputInt($request->get('project_id'));
        $commentId = InputSanitise::inputInt($request->get('comment_id'));
        $subcommentId = InputSanitise::inputInt($request->get('subcomment_id'));
        if(!empty($projectId) && !empty($commentId) && !empty($subcommentId)){
            $subcomment = VkitProjectSubComment::where('vkit_project_id', $projectId)->where('vkit_project_comment_id', $commentId)->where('id', $subcommentId)->first();
            if(is_object($subcomment)){
                DB::beginTransaction();
                try
                {
                    if(is_object($subcomment->deleteLikes) && false == $subcomment->deleteLikes->isEmpty()){
                        foreach($subcomment->deleteLikes as $deleteLike){
                            $deleteLike->delete();
                        }
                    }
                    Session::put('project_comment_area', $commentId);
                    $subcomment->delete();
                    DB::commit();
                    return redirect()->route('vkitproject', ['id' => $projectId]);
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return back()->withErrors('something went wrong.');
                }
            }
        }
        return Redirect::to('vkits');
    }

    protected function likeVkitProject(Request $request){
        return VkitProjectLike::getLikeVkitProject($request);
    }

    protected function likeVkitProjectComment(Request $request){
        return VkitProjectCommentLike::getLikeVkitProject($request);
    }

    protected function likekitProjectSubComment(Request $request){
        return VkitProjectSubCommentLike::getLikeVkitProject($request);
    }
}