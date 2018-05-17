<?php

namespace App\Http\Controllers\Client\OnlineCourse;

use Illuminate\Http\Request;
use App\Http\Controllers\Client\ClientBaseController;
use Redirect;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;
use App\Models\ClientOnlineVideo;
use App\Models\ClientOnlineCourse;
use App\Models\ClientNotification;
use App\Models\ClientOnlineCategory;
use App\Models\ClientOnlineSubCategory;

class ClientOnlineVideoController extends ClientBaseController
{
    /**
     *  check admin have permission or not, if not redirect admin/home
     */
    public function __construct(Request $request) {
        parent::__construct($request);
        $this->middleware('client');
    }

    /**
     * Define your validation rules in a property in
     * the controller to reuse the rules.
     */
    protected $validateVideo = [
        'category' => 'required|integer',
        'subcategory' => 'required|integer',
        'course' => 'required|integer',
        'video' => 'required|string',
        'description' => 'required|string',
        'duration' => 'required|integer',
        'video_path' => 'required'
    ];

    protected $updateValidateVideo = [
        'category' => 'required|integer',
        'subcategory' => 'required|integer',
        'course' => 'required|integer',
        'video' => 'required|string',
        'description' => 'required|string',
        'duration' => 'required|integer',
    ];

    /**
     *  show list of course video
     */
    protected function show($subdomainName,Request $request){
    	$videos = ClientOnlineVideo::showVideos($request);
    	return view('client.onlineCourse.video.list', compact('videos', 'subdomainName'));
    }

    /**
     *  show create course video UI
     */
    protected function create($subdomainName,Request $request){
        $clientId = Auth::guard('client')->user()->id;
        $categories   = ClientOnlineCategory::where('client_id', $clientId)->get();
        $subCategories = new ClientOnlineSubCategory;
        $courses = new ClientOnlineCourse;
    	$video = new ClientOnlineVideo;
    	return view('client.onlineCourse.video.create', compact('categories','subCategories','courses', 'video', 'subdomainName'));
    }

    /**
     *  store course video
     */
    protected function store($subdomain,Request $request){
    	$v = Validator::make($request->all(), $this->validateVideo);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::connection('mysql2')->beginTransaction();
        try
        {
        	$video = ClientOnlineVideo::addOrUpdateVideo($request);
            if(is_object($video)){
                $notificationMessage = 'A new course video: <a href="'.$request->root().'/episode/'.$video->id.'" target="_blank">'.$video->name.'</a> has been added.';
                ClientNotification::addNotification($notificationMessage, ClientNotification::CLIENTCOURSEVIDEO, $video->id);
                DB::connection('mysql2')->commit();
            	return Redirect::to('manageOnlineVideo')->with('message', 'Video created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('manageOnlineVideo');
    }

    /**
     *  edit course video
     */
    protected function edit( $subdomainName , $id, Request $request){
    	$id = InputSanitise::inputInt(json_decode($id));
    	if(isset($id)){
    		$video = ClientOnlineVideo::find($id);
    		if(is_object($video)){
    			$courses = ClientOnlineCourse::showCourses($request);
                $categories = ClientOnlineCategory::where('client_id', $video->client_id)->get();
                $subCategories = ClientOnlineSubCategory::getOnlineSubCategoriesByCategoryId($video->category_id, $request);
                $courses = ClientOnlineCourse::getOnlineCourseByCatIdBySubCatIdForClient($video->category_id,$video->sub_category_id);
    			return view('client.onlineCourse.video.create', compact('categories','subCategories','courses', 'video', 'subdomainName'));
    		}
    	}
    	return Redirect::to('manageOnlineVideo');
    }

    /**
     *  update course video
     */
    protected function update($subdomain,Request $request){
    	$v = Validator::make($request->all(), $this->updateValidateVideo);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::connection('mysql2')->beginTransaction();
        try
        {
        	$video = ClientOnlineVideo::addOrUpdateVideo($request, true);
            if(is_object($video)){
                DB::connection('mysql2')->commit();
            	return Redirect::to('manageOnlineVideo')->with('message', 'Video updated successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('manageOnlineVideo');
    }

    /**
     *  delete course video
     */
    protected function delete($subdomain,Request $request){
    	$videoId = InputSanitise::inputInt($request->get('video_id'));
    	if(isset($videoId)){
    		$video = ClientOnlineVideo::find($videoId);
    		if(is_object($video)){
                DB::connection('mysql2')->beginTransaction();
                try
                {
                    $video->deleteCommantsAndSubComments();
                    $loginUser = Auth::guard('client')->user();
                    $subdomainArr = explode('.', $loginUser->subdomain);
                    $clientName = $subdomainArr[0];
                    if(true == preg_match('/clientCourseVideos/',$video->video_path)){
                        $courseVideoFolder = "clientCourseVideos/".$clientName."/".$video->course_id."/".$video->id;
                        if(is_dir($courseVideoFolder)){
                            InputSanitise::delFolder($courseVideoFolder);
                        }
                    }
        			$video->delete();
                    DB::connection('mysql2')->commit();
        			return Redirect::to('manageOnlineVideo')->with('message', 'Video deleted successfully!');
                }
                catch(\Exception $e)
                {
                    DB::connection('mysql2')->rollback();
                    return redirect()->back()->withErrors('something went wrong.');
                }
    		}
    	}
    	return Redirect::to('manageOnlineVideo');
    }

    protected function isClientCourseVideoExist(Request $request){
        return ClientOnlineVideo::isClientCourseVideoExist($request);
    }

}