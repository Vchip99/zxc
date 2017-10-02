<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\WorkshopVideo;
use App\Models\WorkshopCategory;
use App\Models\WorkshopDetail;
use DB, Auth, Session;
use Validator, Redirect,Hash;
use App\Libraries\InputSanitise;


class WorkshopController extends Controller
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

    protected function show(){
    	$workshops = WorkshopDetail::paginate();
    	$workshopCategories = WorkshopCategory::all();
    	return view('workshops.workshops', compact('workshops', 'workshopCategories'));
    }

    protected function getWorkshopsByCategory(Request $request){
    	return WorkshopDetail::getWorkshopsByCategory($request->id);
    }

    protected function workshopDetails($id){
    	$id = json_decode($id);
    	$workshop = WorkshopDetail::find($id);
    	if(is_object($workshop)){
    		$videos = WorkshopVideo::where('workshop_details_id', $id)->get();
    		return view('workshops.workshopDetails', compact('workshop', 'videos'));
    	}
    	return Redirect::to('workshops');
    }

    protected function workshopVideo($id){
    	$id = json_decode($id);
    	$video = WorkshopVideo::find($id);
    	if(is_object($video)){
    		$workshopVideos = WorkshopVideo::where('workshop_details_id', $video->workshop_details_id)->get();
    		return view('workshops.workshopVideo', compact('video', 'workshopVideos'));
    	}
    	return Redirect::to('workshops');
    }

}