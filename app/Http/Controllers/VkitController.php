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
use DB, Auth, Session, Cache;
use Validator, Redirect,Hash;
use App\Libraries\InputSanitise;
use App\Models\VkitProjectLike;
use App\Models\Notification;
use App\Models\ReadNotification;
use App\Models\Add;

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
    protected function show(Request $request){
        if(empty($request->getQueryString())){
            $page = 'page=1';
        } else {
            $page = $request->getQueryString();
        }
        $projects = Cache::remember('vchip:projects:projects-'.$page,60, function() {
            return VkitProject::paginate(12);
        });

        $vkitCategories= Cache::remember('vchip:projects:vkitCategories',60, function() {
            return VkitCategory::getProjectCategoriesAssociatedWithProject();
        });
        $date = date('Y-m-d');
        $ads = Add::getAdds($request->url(),$date);
        return view('vkits.vkits', compact('projects', 'vkitCategories', 'ads'));
    }

    /**
     *  show vkits project by Id
     */
    protected function vkitproject($id,$subcommentId=NULL){
        $project = Cache::remember('vchip:projects:project-'.$id,60, function() use ($id){
            return VkitProject::find(json_decode($id));
        });
        if(is_object($project)){
            $projects = Cache::remember('vchip:projects:projects',60, function() {
                return VkitProject::all();
            });
            $comments = VkitProjectComment::where('vkit_project_id', $id)->orderBy('id', 'desc')->get();

            $registeredProjectIds = $this->getRegisteredProjectIds();
            $likesCount = VkitProjectLike::getLikesByVkitProjectId($id);
            $commentLikesCount = VkitProjectCommentLike::getLikesByVkitProjectId($id);
            $subcommentLikesCount = VkitProjectSubCommentLike::getLikesByVkitProjectId($id);
            $currentUser = Auth::user();
            if(is_object($currentUser)){
                if($id > 0 || $subcommentId > 0){
                    DB::beginTransaction();
                    try
                    {
                        if($id > 0 && $subcommentId == NULL){
                            $readNotification = ReadNotification::readNotificationByModuleByModuleIdByUser(Notification::ADMINVKITPROJECT,$id,$currentUser->id);
                            if(is_object($readNotification)){
                                DB::commit();
                            }
                        } else {
                            Session::set('show_subcomment_area', $subcommentId);
                        }
                        Session::set('project_comment_area', 0);
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
                $currentUser = NULL;
            }
            return view('vkits.vkitproject', compact('project', 'projects', 'comments', 'registeredProjectIds', 'likesCount', 'commentLikesCount', 'currentUser', 'subcommentLikesCount'));
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
            return Cache::remember('vchip:projects:projects:cat-'.$categoryId, 60, function() use ($categoryId){
                return VkitProject::getVkitProjectsByCategoryId($categoryId);
            });
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
        $projectId = strip_tags(trim($request->get('project_id')));
        DB::beginTransaction();
        try
        {
            $VkitProjectComment = VkitProjectComment::createComment($request);
            DB::commit();
        }
        catch(\Exception $e)
        {
            DB::rollback();
        }
        return $this->getComments($projectId);
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
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                }
            }
        }
        return $this->getComments($projectId);
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
                    $comment->delete();
                    DB::commit();
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                }
            }
        }
        return $this->getComments($projectId);
    }

    protected function createVkitProjectSubComment(Request $request){
        $projectId = InputSanitise::inputInt($request->get('project_id'));
        DB::beginTransaction();
        try
        {
            $VkitProjectSubComment = VkitProjectSubComment::createSubComment($request);
            $commentId = InputSanitise::inputInt($request->get('comment_id'));
            $subcommentId = InputSanitise::inputInt($request->get('subcomment_id'));

            if($commentId > 0 && $subcommentId > 0){
                $parentComment = VkitProjectSubComment::where('id',$subcommentId)->where('user_id', '!=', Auth::user()->id)->first();
            } else {
                $parentComment = VkitProjectComment::where('id',$VkitProjectSubComment->vkit_project_comment_id)->first();
            }

            if(is_object($parentComment)){
                $string = (strlen($parentComment->body) > 50) ? substr($parentComment->body,0,50).'...' : $parentComment->body;
                $notificationMessage = '<a href="'.$request->root().'/vkitproject/'.$projectId.'/'.$VkitProjectSubComment->id.'">A reply of your comment: '. trim($string, '<p></p>')  .'</a>';
                Notification::addCommentNotification($notificationMessage, Notification::USERVKITPROJECTNOTIFICATION, $VkitProjectSubComment->id,$VkitProjectSubComment->user_id,$parentComment->user_id);
            }
            DB::commit();
        }
        catch(\Exception $e)
        {
            DB::rollback();
        }
        return $this->getComments($projectId);
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
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                }
            }
        }
        return $this->getComments($projectId);
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
                    $subcomment->delete();
                    DB::commit();
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                }
            }
        }
        return $this->getComments($projectId);
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

       /**
     *  return comments
     */
    protected function getComments($projectId){
        $comments = VkitProjectComment::where('vkit_project_id', $projectId)->orderBy('id', 'desc')->get();
        $videoComments = [];
        foreach($comments as $comment){
            $videoComments['comments'][$comment->id]['body'] = $comment->body;
            $videoComments['comments'][$comment->id]['id'] = $comment->id;
            $videoComments['comments'][$comment->id]['vkit_project_id'] = $comment->vkit_project_id;
            $videoComments['comments'][$comment->id]['user_id'] = $comment->user_id;
            $videoComments['comments'][$comment->id]['user_name'] = $comment->getUser($comment->user_id)->name;
            $videoComments['comments'][$comment->id]['updated_at'] = $comment->updated_at->diffForHumans();
            $videoComments['comments'][$comment->id]['user_image'] = $comment->getUser($comment->user_id)->photo;
            if(is_file($comment->getUser($comment->user_id)->photo) && true == preg_match('/userStorage/',$comment->getUser($comment->user_id)->photo)){
                $isImageExist = 'system';
            } else if(!empty($comment->getUser($comment->user_id)->photo) && false == preg_match('/userStorage/',$comment->getUser($comment->user_id)->photo)){
                $isImageExist = 'other';
            } else {
                $isImageExist = 'false';
            }
            $videoComments['comments'][$comment->id]['image_exist'] = $isImageExist;
            if(is_object($comment->children) && false == $comment->children->isEmpty()){
                $videoComments['comments'][$comment->id]['subcomments'] = $this->getSubComments($comment->children);
            }
        }
        $videoComments['commentLikesCount'] = VkitProjectCommentLike::getLikesByVkitProjectId($projectId);
        $videoComments['subcommentLikesCount'] = VkitProjectSubCommentLike::getLikesByVkitProjectId($projectId);

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
            $videoChildComments[$subComment->id]['vkit_project_id'] = $subComment->vkit_project_id;
            $videoChildComments[$subComment->id]['vkit_project_comment_id'] = $subComment->vkit_project_comment_id;
            $videoChildComments[$subComment->id]['user_name'] = $subComment->getUser($subComment->user_id)->name;
            $videoChildComments[$subComment->id]['user_id'] = $subComment->user_id;
            $videoChildComments[$subComment->id]['updated_at'] = $subComment->updated_at->diffForHumans();
            $videoChildComments[$subComment->id]['user_image'] = $subComment->getUser($subComment->user_id)->photo;
            if(is_file($subComment->getUser($subComment->user_id)->photo) && true == preg_match('/userStorage/',$subComment->getUser($subComment->user_id)->photo)){
                $isImageExist = 'system';
            } else if(!empty($subComment->getUser($subComment->user_id)->photo) && false == preg_match('/userStorage/',$subComment->getUser($subComment->user_id)->photo)){
                $isImageExist = 'other';
            } else {
                $isImageExist = 'false';
            }
            $videoChildComments[$subComment->id]['image_exist'] = $isImageExist;
            if(is_object($subComment->children) && false == $subComment->children->isEmpty()){
                $videoChildComments[$subComment->id]['subcomments'] = $this->getSubComments($subComment->children);
            }
        }

        return $videoChildComments;
    }
}