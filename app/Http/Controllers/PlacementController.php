<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PlacementArea;
use App\Models\PlacementCompany;
use App\Models\PlacementProcess;
use App\Models\CompanyDetails;
use App\Models\PlacementFaq;
use App\Models\ExamPattern;
use App\Models\PlacementExperiance;
use App\Models\PlacementProcessComment;
use App\Models\PlacementProcessSubComment;
use App\Models\PlacementProcessCommentLike;
use App\Models\PlacementProcessSubCommentLike;
use App\Models\PlacementProcessLike;
use App\Models\ApplyJob;
use DB, Auth, Session, Cache;
use Validator, Redirect,Hash;
use App\Libraries\InputSanitise;
use App\Models\Add;

class PlacementController extends Controller
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

    protected function show(Request $request){
        $companyId = Session::get('front_selected_company_id');
        if($companyId > 0){
            $companyDetails = Cache::remember('vchip:placements:companyDetails:company-'.$companyId,60, function() use ($companyId){
                return CompanyDetails::where('placement_company_id', $companyId)->first();
            });
        } else {
            $companyDetails = Cache::remember('vchip:placements:companyDetails:company-first',60, function() use ($companyId){
                return CompanyDetails::first();
            });
        }
        $placementAreas= Cache::remember('vchip:placements:placementAreas',60, function() {
            return PlacementArea::getPlacementAreas();
        });
        if(is_object($companyDetails)){
            $companyId = $companyDetails->placement_company_id;
            $placementProcess = Cache::remember('vchip:placements:placementProcess:company-'.$companyId,60, function() use ($companyId){
                return PlacementProcess::where('placement_company_id', $companyId)->first();
            });
            if(is_object($placementProcess)){
                $placementFaqs = Cache::remember('vchip:placements:placementFaqs:company-'.$companyId,60, function() use ($companyId){
                    return PlacementFaq::where('placement_company_id', $companyId)->orderBy('id', 'desc')->get();
                });
                $examPatterns = Cache::remember('vchip:placements:examPatterns:company-'.$companyId,60, function() use ($companyId){
                    return ExamPattern::where('placement_company_id', $companyId)->get();
                });
                $placementExperiances = Cache::remember('vchip:placements:placementExperiances:company-'.$companyId,60, function() use ($companyId){
                    return PlacementExperiance::where('placement_company_id', $companyId)->orderBy('id', 'desc')->get();
                });
            } else {
                $placementFaqs = [];
                $examPatterns = [];
                $placementExperiances = [];
            }
            $placementCompanies = [];
            $selectedCompany = 0;
            $selectedArea = 0;
            $commentLikesCount = PlacementProcessCommentLike::getLikesByCompanyId($companyDetails->placement_company_id);
            $subcommentLikesCount = PlacementProcessSubCommentLike::getLikesByCompanyId($companyDetails->placement_company_id);
            $likesCount = PlacementProcessLike::getLikesByCompanyId($companyDetails->placement_company_id);
            $comments = PlacementProcessComment::where('company_id', $companyDetails->placement_company_id)->orderBy('id','desc')->get();
            $currentUser = Auth::user();
            if(!is_object($currentUser)){
                $currentUser = NULL;
            }
            $applyJobs = Cache::remember('vchip:placements:applyJobs',60, function() {
                return ApplyJob::orderBy('id', 'desc')->get();
            });
            $date = date('Y-m-d');
            $ads = Add::getAdds($request->url(),$date);
	        return view('placement.placements', compact('placementProcess', 'placementAreas', 'placementCompanies', 'companyDetails', 'selectedCompany', 'selectedArea', 'placementFaqs', 'examPatterns', 'placementExperiances', 'comments', 'commentLikesCount', 'subcommentLikesCount', 'currentUser', 'likesCount', 'applyJobs', 'ads'));
        } else {
            $placementProcess = [];
            $companyDetails = '';
            $placementCompanies = [];
            $selectedCompany = 0;
            $selectedArea = 0;
            $placementFaqs = [];
            $examPatterns = [];
            $placementExperiances = [];
            $comments = [];
            $commentLikesCount = [];
            $subcommentLikesCount = [];
            $likesCount = [];
            $currentUser = Auth::user();
            if(!is_object($currentUser)){
                $currentUser = new user;
            }
            $applyJobs = Cache::remember('vchip:placements:applyJobs',60, function() {
                return ApplyJob::orderBy('id', 'desc')->get();
            });
            $date = date('Y-m-d');
            $ads = Add::getAdds($request->url(),$date);
            return view('placement.placements', compact('placementProcess', 'placementAreas', 'placementCompanies', 'companyDetails', 'selectedCompany', 'selectedArea', 'placementFaqs', 'examPatterns', 'placementExperiances', 'comments', 'commentLikesCount', 'subcommentLikesCount', 'currentUser', 'likesCount', 'applyJobs', 'ads'));
        }
    }

    protected function jobUpdates(Request $request){

        $applyJobs = Cache::remember('vchip:placements:applyJobs',60, function() {
            return ApplyJob::orderBy('id', 'desc')->get();
        });
        $date = date('Y-m-d');
        $ads = Add::getAdds($request->url(),$date);
        return view('placement.jobs', compact('applyJobs', 'ads'));
    }

    protected function showPlacements(Request $request){
        $selectedCompany = $request->get('company_id');
        $companyDetails = Cache::remember('vchip:placements:companyDetails:company-'.$selectedCompany,60, function() use ($selectedCompany){
            return CompanyDetails::where('placement_company_id', $selectedCompany)->first();
        });
        $placementProcess = Cache::remember('vchip:placements:placementProcess:company-'.$selectedCompany,60, function() use ($selectedCompany){
            return PlacementProcess::where('placement_company_id', $selectedCompany)->first();
        });
        $placementAreas= Cache::remember('vchip:placements:placementAreas',60, function() {
            return PlacementArea::getPlacementAreas();
        });
        if(is_object($companyDetails) && is_object($placementProcess)){
            $selectedArea = $companyDetails->placement_area_id;
            $placementCompanies = Cache::remember('vchip:placements:placementCompanies:areaId-'.$selectedArea,60, function() use ($selectedArea){
                return PlacementCompany::where('placement_area_id',$selectedArea)->get();
            });
            $placementFaqs = Cache::remember('vchip:placements:placementFaqs:company-'.$selectedCompany,60, function() use ($selectedCompany){
                return PlacementFaq::where('placement_company_id', $selectedCompany)->orderBy('id', 'desc')->get();
            });
            $examPatterns = Cache::remember('vchip:placements:examPatterns:company-'.$selectedCompany,60, function() use ($selectedCompany){
                return ExamPattern::where('placement_company_id', $selectedCompany)->get();
            });
            $placementExperiances = Cache::remember('vchip:placements:placementExperiances:company-'.$selectedCompany,60, function() use ($selectedCompany){
                return PlacementExperiance::where('placement_company_id', $selectedCompany)->orderBy('id', 'desc')->get();
            });

            $commentLikesCount = PlacementProcessCommentLike::getLikesByCompanyId($selectedCompany);
            $subcommentLikesCount = PlacementProcessSubCommentLike::getLikesByCompanyId($selectedCompany);
            $likesCount = PlacementProcessLike::getLikesByCompanyId($selectedCompany);
            $comments = PlacementProcessComment::where('company_id', $selectedCompany)->orderBy('id','desc')->get();
            $currentUser = Auth::user();
            if(!is_object($currentUser)){
                $currentUser = new user;
            }
            $applyJobs = Cache::remember('vchip:placements:applyJobs',60, function() {
                return ApplyJob::orderBy('id', 'desc')->get();
            });
            $date = date('Y-m-d');
            $ads = DB::table('adds')
                ->where('show_page_id', 8)
                ->whereRaw('"'.$date.'" between `start_date` and `End_date`')
                ->get();
            return view('placement.placements', compact('placementProcess', 'placementAreas', 'placementCompanies', 'companyDetails', 'selectedCompany', 'selectedArea', 'placementFaqs', 'examPatterns', 'placementExperiances', 'comments', 'commentLikesCount', 'subcommentLikesCount', 'currentUser', 'likesCount', 'applyJobs', 'ads'));
        }
        return Redirect::to('placements');
    }

    protected function getPlacementCompaniesByArea(Request $request){
        return PlacementCompany::getPlacementCompaniesByArea($request->id);
    }

    protected function getPlacementCompaniesByAreaForFront(Request $request){
        $areaId = $request->id;
        return Cache::remember('vchip:placements:placementCompanies:areaId-'.$areaId,60, function() use ($areaId){
            return PlacementCompany::getPlacementCompaniesByAreaForFront($areaId);
        });
    }

    protected function createPlacementExperiance(Request $request){
        $companyId   = InputSanitise::inputInt($request->get('company_id'));
        DB::beginTransaction();
        try
        {
            PlacementExperiance::createPlacementExperiance($request);
            DB::commit();
            if($companyId > 0){
                Session::put('front_selected_company_id', $companyId);
            }

        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('placements');
    }

    protected function placementExperiance($id){
        $id = json_decode($id);
        if(isset($id)){
            $placementExperiance = Cache::remember('vchip:placements:placementExperiance-'.$id,60, function() use ($id){
                return PlacementExperiance::find($id);
            });
            if(is_object($placementExperiance)){
                $placementExperiances = Cache::remember('vchip:placements:placementExperiances',60, function() {
                    return placementExperiance::all();
                });
                return view('placement.placementExperiance', compact('placementExperiance', 'placementExperiances'));
            }
        }
        return Redirect::to('placements');
    }

        /**
     *  return comments
     */
    protected function getComments($companyId){
        $comments = PlacementProcessComment::where('company_id', $companyId)->orderBy('id','desc')->get();
        $placementComments = [];
        foreach($comments as $comment){
            $placementComments['comments'][$comment->id]['body'] = $comment->body;
            $placementComments['comments'][$comment->id]['id'] = $comment->id;
            $placementComments['comments'][$comment->id]['company_id'] = $comment->company_id;
            $placementComments['comments'][$comment->id]['user_id'] = $comment->user_id;
            $placementComments['comments'][$comment->id]['user_name'] = $comment->getUser($comment->user_id)->name;
            $placementComments['comments'][$comment->id]['updated_at'] = $comment->updated_at->diffForHumans();
            if(is_file($comment->getUser($comment->user_id)->photo) && true == preg_match('/userStorage/',$comment->getUser($comment->user_id)->photo)){
                $isImageExist = 'system';
            } else if(!empty($comment->getUser($comment->user_id)->photo) && false == preg_match('/userStorage/',$comment->getUser($comment->user_id)->photo)){
                $isImageExist = 'other';
            } else {
                $isImageExist = 'false';
            }
            $placementComments['comments'][$comment->id]['image_exist'] = $isImageExist;
            $placementComments['comments'][$comment->id]['user_image'] = $comment->getUser($comment->user_id)->photo;
            if(is_object($comment->children) && false == $comment->children->isEmpty()){
                $placementComments['comments'][$comment->id]['subcomments'] = $this->getSubComments($comment->children);
            }
        }
        $placementComments['commentLikesCount'] = PlacementProcessCommentLike::getLikesByCompanyId($companyId);
        $placementComments['subcommentLikesCount'] = PlacementProcessSubCommentLike::getLikesByCompanyId($companyId);

        return $placementComments;
    }

    /**
     *  return child comments
     */
    protected function getSubComments($subComments){

        $placementChildComments = [];
        foreach($subComments as $subComment){
            $placementChildComments[$subComment->id]['body'] = $subComment->body;
            $placementChildComments[$subComment->id]['id'] = $subComment->id;
            $placementChildComments[$subComment->id]['company_id'] = $subComment->company_id;
            $placementChildComments[$subComment->id]['placement_process_comment_id'] = $subComment->placement_process_comment_id;
            $placementChildComments[$subComment->id]['user_name'] = $subComment->getUser($subComment->user_id)->name;
            $placementChildComments[$subComment->id]['user_id'] = $subComment->user_id;
            $placementChildComments[$subComment->id]['updated_at'] = $subComment->updated_at->diffForHumans();
            $placementChildComments[$subComment->id]['user_image'] = $subComment->getUser($subComment->user_id)->photo;
            if(is_file($subComment->getUser($subComment->user_id)->photo) && true == preg_match('/userStorage/',$subComment->getUser($subComment->user_id)->photo)){
                $isImageExist = 'system';
            } else if(!empty($subComment->getUser($subComment->user_id)->photo) && false == preg_match('/userStorage/',$subComment->getUser($subComment->user_id)->photo)){
                $isImageExist = 'other';
            } else {
                $isImageExist = 'false';
            }
            $placementChildComments[$subComment->id]['image_exist'] = $isImageExist;
            if(is_object($subComment->children) && false == $subComment->children->isEmpty()){
                $placementChildComments[$subComment->id]['subcomments'] = $this->getSubComments($subComment->children);
            }
        }

        return $placementChildComments;
    }

    protected function createPlacementProcessComment(Request $request){
        $companyId = InputSanitise::inputInt($request->get('company_id'));
        DB::beginTransaction();
        try
        {
            PlacementProcessComment::createPlacementProcessComment($request);
            DB::commit();
            // if($companyId > 0){
            //     Session::put('front_selected_company_id', $companyId);
            // }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            // return redirect()->back()->withErrors('something went wrong.');
        }
        return $this->getComments($companyId);
    }

    protected function createPlacementProcessSubComment(Request $request){
        $userComment = $request->get('subcomment');
        $companyId = InputSanitise::inputInt($request->get('company_id'));
        if(!empty($userComment)){
            DB::beginTransaction();
            try
            {
                PlacementProcessSubComment::createPlacementProcessSubComment($request);
                DB::commit();
                // if($companyId > 0){
                //     Session::put('front_selected_company_id', $companyId);
                // }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                // return redirect()->back()->withErrors('something went wrong.');
            }
        }
        return $this->getComments($companyId);
    }

    protected function updatePlacementProcessSubComment(Request $request){
        $companyId = InputSanitise::inputInt($request->get('company_id'));
        $commentId = InputSanitise::inputInt($request->get('comment_id'));
        $subcommentId = InputSanitise::inputInt($request->get('subcomment_id'));
        $commentBody = $request->get('subcomment');
        if(!empty($companyId) && !empty($commentId) && !empty($subcommentId) && !empty($commentBody)){
            $subcomment = PlacementProcessSubComment::where('company_id', $companyId)->where('placement_process_comment_id', $commentId)->where('id', $subcommentId)->first();
            if(is_object($subcomment)){
                DB::beginTransaction();
                try
                {
                    $subcomment->body = $commentBody;
                    $subcomment->save();
                    DB::commit();
                    // if($companyId > 0){
                    //     Session::put('front_selected_company_id', $companyId);
                    // }
                    // return Redirect::to('placements');
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    // return back()->withErrors('something went wrong.');
                }
            }
        }
        return $this->getComments($companyId);
    }

    protected function deletePlacementProcessSubComment(Request $request){
        $companyId = InputSanitise::inputInt($request->get('company_id'));
        $commentId = InputSanitise::inputInt($request->get('comment_id'));
        $subcommentId = InputSanitise::inputInt($request->get('subcomment_id'));
        if(!empty($companyId) && !empty($commentId) && !empty($subcommentId) ){
            $subcomment = PlacementProcessSubComment::where('company_id', $companyId)->where('placement_process_comment_id', $commentId)->where('id', $subcommentId)->first();
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
                    // if($companyId > 0){
                    //     Session::put('front_selected_company_id', $companyId);
                    // }
                    // return Redirect::to('placements');
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    // return back()->withErrors('something went wrong.');
                }
            }
        }
        return $this->getComments($companyId);
    }

    protected function deletePlacementProcessComment(Request $request){
        $companyId = InputSanitise::inputInt($request->get('company_id'));
        $commentId = InputSanitise::inputInt($request->get('comment_id'));
        $subcommentId = InputSanitise::inputInt($request->get('subcomment_id'));
        if(!empty($companyId) && !empty($commentId)){
            $comment = PlacementProcessComment::where('company_id', $companyId)->where('id', $commentId)->first();
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
                    // if($companyId > 0){
                    //     Session::put('front_selected_company_id', $companyId);
                    // }
                    // return Redirect::to('placements');
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    // return back()->withErrors('something went wrong.');
                }
            }
        }
        return $this->getComments($companyId);
    }


    protected function likePlacementProcessComment(Request $request){
        return PlacementProcessCommentLike::getLikePlacementProcessComment($request);
    }

    protected function likePlacementProcessSubComment(Request $request){
        return PlacementProcessSubCommentLike::getLikePlacementProcessSubComment($request);
    }

    protected function likePlacementProcess(Request $request){
        return PlacementProcessLike::getLikePlacementProcess($request);
    }
}