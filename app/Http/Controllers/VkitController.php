<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Instamojo;
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
use App\Models\Rating;
use App\Models\Advertisement;
use App\Mail\PaymentReceived;

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
            return VkitProject::getVkitProjectsWithPagination();
        });

        $vkitCategories= Cache::remember('vchip:projects:vkitCategories',60, function() {
            return VkitCategory::getProjectCategoriesAssociatedWithProject();
        });
        $date = date('Y-m-d');
        $ads = Add::getAdds($request->url(),$date);

        $reviewData = [];
        $ratingUsers = [];
        $userNames = [];
        $allRatings = Rating::getRatingsByModuleType(Rating::Vkit);
        if(is_object($allRatings) && false == $allRatings->isEmpty()){
            foreach($allRatings as $rating){
                $reviewData[$rating->module_id]['rating'][$rating->user_id] = [ 'rating' => $rating->rating,'review' => $rating->review, 'review_id' => $rating->id];
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
                    $userNames[$user->id] = $user->name;
                }
            }
        }
        return view('vkits.vkits', compact('projects', 'vkitCategories', 'ads','reviewData','userNames'));
    }

    /**
     *  show vkits project by Id
     */
    protected function vkitproject($id,$subcommentId=NULL){
        $project = Cache::remember('vchip:projects:project-'.$id,60, function() use ($id){
            return VkitProject::getVkitProjectsById(json_decode($id));
        });
        if(is_object($project)){
            $projects = Cache::remember('vchip:projects:projects:'.$project->category_id,60, function() use($project) {
                return VkitProject::getVkitProjectsByCategoryId($project->category_id);
            });
            $comments = VkitProjectComment::where('vkit_project_id', $id)->orderBy('id', 'desc')->get();
            $isPurchasedProjectItems = 'false';
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
                $registeredProject = RegisterProject::getRegisteredVkitProjectByUserIdByProjectId($currentUser->id,$project->id);
                if(is_object($registeredProject)){
                    if(!empty($registeredProject->payment_id) && !empty($registeredProject->payment_request_id) && !empty($registeredProject->price)){
                        $isPurchasedProjectItems = 'true';
                    }
                }
            } else {
                $currentUser = NULL;
            }
            $images = '';
            $advertisements = Advertisement::where('admin_id',$project->created_by)->get();
            if(is_object($advertisements) && false == $advertisements->isEmpty()){
                foreach($advertisements as $index => $advertisement){
                    if(0 == $index){
                        $images = $advertisement->image;
                    } else {
                        $images .= ','.$advertisement->image;
                    }
                }
            }
            $reviewData = [];
            $ratingUsers = [];
            $userNames = [];
            $allRatings = Rating::getRatingsByModuleIdByModuleType($project->id,Rating::Vkit);
            if(is_object($allRatings) && false == $allRatings->isEmpty()){
                foreach($allRatings as $rating){
                    $reviewData[$rating->module_id]['rating'][$rating->user_id] = [ 'rating' => $rating->rating,'review' => $rating->review, 'review_id' => $rating->id];
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
                        $userNames[$user->id] = $user->name;
                    }
                }
            }
            return view('vkits.vkitproject', compact('project', 'projects', 'comments', 'registeredProjectIds', 'likesCount', 'commentLikesCount', 'currentUser', 'subcommentLikesCount','isPurchasedProjectItems','images','reviewData','userNames'));
        }
        return Redirect::to('vkits');
    }

    /**
     *  return vkits project by categoryId
     */
    protected function getVkitProjectsByCategoryId(Request $request){
        $categoryId = $request->get('id');
        $userId = $request->get('userId');
        $rating = $request->get('rating');
        $result = [];
        if(isset($categoryId) && empty($userId)){
            if(true == $rating){
                $result['projects'] = Cache::remember('vchip:projects:projects:cat-'.$categoryId, 60, function() use ($categoryId){
                    return VkitProject::getVkitProjectsByCategoryId($categoryId);
                });
            } else {
                return Cache::remember('vchip:projects:projects:cat-'.$categoryId, 60, function() use ($categoryId){
                    return VkitProject::getVkitProjectsByCategoryId($categoryId);
                });
            }
        } else {
            if(true == $rating){
                $result['projects'] = VkitProject::getRegisteredVkitProjectsByUserIdByCategoryId($userId, $categoryId);
            } else {
                return VkitProject::getRegisteredVkitProjectsByUserIdByCategoryId($userId, $categoryId);
            }
        }
        if(true == $rating){
            $ratingUsers = [];
            $allRatings = Rating::getRatingsByModuleType(Rating::Vkit);
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
            return $result;
        }
    }


     /**
     *  return vkits project by categoryId
     */
    protected function getCollegeVkitProjectsByCategoryId(Request $request){
        $categoryId = $request->get('id');
        return Cache::remember(Session::get('college_user_url').':projects:projects:cat-'.$categoryId, 60, function() use ($categoryId){
            return VkitProject::getCollegeVkitProjectsByCategoryId($categoryId);
        });
    }

    /**
     *  return vkits project by categoryId
     */
    protected function getVchipFavouriteVkitProjectsByUserId(Request $request){
        $userId = $request->get('userId');
        return VkitProject::getVchipFavouriteVkitProjectsByUserId($userId);
    }


    /**
     *  return vkits project by categoryId
     */
    protected function getCollegeFavouriteVkitProjectsByUserId(Request $request){
        $userId = $request->get('userId');
        return VkitProject::getCollegeFavouriteVkitProjectsByUserId($userId);
    }

    /**
     *  return vkits project by filter array
     */
    protected function getVkitProjectsBySearchArray(Request $request){
        $result = [];
        $ratingUsers = [];
        $result['projects'] =  VkitProject::getVkitProjectsBySearchArray($request);
        $allRatings = Rating::getRatingsByModuleType(Rating::Vkit);
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
        return $result;
    }

    protected function registerProject(Request $request){
        return RegisterProject::addFavouriteProject($request);
    }

    protected function getRegisteredProjectIds(){
        $registeredProjectIds = [];
        $loginUser = Auth::user();
        if(is_object($loginUser)){
            $userId = $loginUser->id;
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
                $notificationMessage = '<a href="'.$request->root().'/vkitproject/'.$projectId.'/'.$VkitProjectSubComment->id.'" target="_blank">A reply of your comment: '. trim($string, '<p></p>')  .'</a>';
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

    protected function purchaseVkitComponents(Request $request){
        // Laravel validation
        $projectId = $request->get('project_id');
        if($projectId < 0){
            return redirect()->back()->withErrors(['project is missing']);
        }
        $project = VkitProject::find($projectId);
        if(!is_object($project)){
            return redirect()->back()->withErrors(['something went wrong while project vkit purchase.']);
        }
        $loginUser = Auth::user();
        Session::put('user_id', $loginUser->id);
        Session::put('user_name', $loginUser->name);
        Session::put('purchase_vkit_project_id', $project->id);
        Session::put('purchase_vkit_project_price', $project->price);
        Session::put('purchase_vkit_project_name', $project->name);
        Session::save();

        $price = $project->price;
        $name = $loginUser->name;
        $phone = $loginUser->phone;
        $email = $loginUser->email;
        $purposeStr = substr($project->name.' Purchased', 0, 29) ;

        if('local' == \Config::get('app.env')){
            $api = new Instamojo('4a6718254b142b18f154158d73ec5e51', '370f403cdfc0a5f12eb6395f110b8da9','https://test.instamojo.com/api/1.1/');
        } else {
            $api = new Instamojo('ce4d49e4727024a22fedc93e040ecac6', '1aa2a1f088aa98d264f614a80fa8a248','https://www.instamojo.com/api/1.1/');
        }

        try {
            $response = $api->paymentRequestCreate(array(
                "purpose" => trim($purposeStr),
                "amount" => $price,
                "buyer_name" => $name,
                "phone" => $phone,
                "send_email" => true,
                "send_sms" => false,
                "email" => $email,
                'allow_repeated_payments' => false,
                "redirect_url" => url('thankyouPurchaseVkitComponents'),
                "webhook" => url('webhookPurchaseVkitComponents')
                ));

            $pay_ulr = $response['longurl'];
            header("Location: $pay_ulr");
            exit();
        }
        catch (Exception $e) {
            return redirect()->back()->withErrors([$e->getMessage()]);
        }
    }

    protected function thankyouPurchaseVkitComponents(Request $request){
        if('local' == \Config::get('app.env')){
            $api = new Instamojo('4a6718254b142b18f154158d73ec5e51', '370f403cdfc0a5f12eb6395f110b8da9','https://test.instamojo.com/api/1.1/');
        } else {
            $api = new Instamojo('ce4d49e4727024a22fedc93e040ecac6', '1aa2a1f088aa98d264f614a80fa8a248','https://www.instamojo.com/api/1.1/');
        }

        $payid = $request->get('payment_request_id');

        try {
            $response = $api->paymentRequestStatus($payid);

            if( 'Credit' == $response['payments'][0]['status']){
                // create a client
                $paymentRequestId = $response['id'];
                $paymentId = $response['payments'][0]['payment_id'];
                $email = $response['payments'][0]['buyer_email'];
                $status = $response['payments'][0]['status'];
                $price = $response['payments'][0]['amount'];

                $userId = Session::get('user_id');
                $userName = Session::get('user_name');
                $projectId = Session::get('purchase_vkit_project_id');
                $projectPrice = Session::get('purchase_vkit_project_price');
                $projectName = Session::get('purchase_vkit_project_name');

                DB::beginTransaction();
                try
                {
                    $paymentArray = [
                                        'user_id' => $userId,
                                        'project_id' => $projectId,
                                        'payment_id' => $paymentId,
                                        'payment_request_id' => $paymentRequestId,
                                        'price' => $projectPrice
                                    ];
                    RegisterProject::addPurchasedProject($paymentArray);
                    DB::commit();
                }
                catch(Exception $e)
                {
                    DB::rollback();
                    return redirect()->back()->withErrors([$e->getMessage()]);
                }
                Session::remove('user_id');
                Session::remove('purchase_vkit_project_id');
                Session::remove('purchase_vkit_project_price');
                Session::remove('user_name');
                Session::remove('purchase_vkit_project_name');
                // user email
                $to = $email;
                if('local' == \Config::get('app.env')){
                    $subject = 'Successfully purchased Vkit Project(Items) -' .$projectName.' on local';
                } else {
                    $subject = 'Successfully purchased Vkit Project(Items) -' .$projectName.'';
                }
                $message = 'Dear '.$userName.',<br>';
                $message .= "You have successfully purcahsed a Vkit Project(Items) -".$projectName;
                $message .= "<h1>Payment Details</h1>";
                $message .= "<hr>";
                $message .= '<p><b>Payment Id:</b> '.$paymentId.'</p>';
                $message .= '<p><b>Payment Status:</b> '.$status.'</p>';
                $message .= '<p><b>Amount:</b> '.$price.'</p>';
                $message .= "<p>Thanks and Regards</p><p>Vchipedu</p>";

                // send email
                Mail::to($to)->send(new PaymentReceived($message,$subject));

                $to = 'vchipdesign@gmail.com';
                if('local' == \Config::get('app.env')){
                    $subject = 'Purchased a Vkit Project(Items) By:' .$userName.' on local';
                } else {
                    $subject = 'Purchased a Vkit Project(Items) By:' .$userName.'';
                }
                $message = "<h1>Payment Details</h1>";
                $message .= "<hr>";
                $message .= '<p><b>Payment Id:</b> '.$paymentId.'</p>';
                $message .= '<p><b>Payment Status:</b> '.$status.'</p>';
                $message .= '<p><b>Amount:</b> '.$price.'</p>';
                $message .= "<hr>";
                $message .= '<p><b>Name:</b> '.$userName.'</p>';
                $message .= '<p><b>Email:</b> '.$email.'</p>';
                $message .= '<p><b>Vkit Project:</b> '.$projectName.'</p>';
                $message .= "<hr>";
                // send email
                Mail::to($to)->send(new PaymentReceived($message,$subject));
                return redirect()->back()->with('message', 'your have successfully purchased a Vkit Project(Items).');
            } else {
                return redirect()->back()->with('message', 'Payment is failed.');
            }
        }
        catch (Exception $e) {
            return redirect()->back()->withErrors([$e->getMessage()]);
        }
    }

    public function webhookPurchaseVkitComponents(Request $request){
        return;
    }
}