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
        'video' => 'required|string',
        'description' => 'required|string',
        'duration' => 'required|integer',
        'course' => 'required|integer',
        'video_path' => 'required|string'
    ];

    /**
     *  show list of course video
     */
    protected function show(Request $request){
    	$videos = ClientOnlineVideo::showVideos($request);
    	return view('client.onlineCourse.video.list', compact('videos'));
    }

    /**
     *  show create course video UI
     */
    protected function create(Request $request){
        $clientId = Auth::guard('client')->user()->id;
    	$courses = ClientOnlineCourse::where('client_id', $clientId)->get();
    	$video = new ClientOnlineVideo;
    	return view('client.onlineCourse.video.create', compact('instituteCourses','courses', 'video'));
    }

    /**
     *  store course video
     */
    protected function store(Request $request){
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
                $notificationMessage = 'A new course video: <a href="'.$request->root().'/episode/'.$video->id.'">'.$video->name.'</a> has been added.';
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
    protected function edit( $subdomain , $id, Request $request){
    	$id = InputSanitise::inputInt(json_decode($id));
    	if(isset($id)){
    		$video = ClientOnlineVideo::find($id);
    		if(is_object($video)){
    			$courses = ClientOnlineCourse::showCourses($request);
    			return view('client.onlineCourse.video.create', compact('courses', 'video'));
    		}
    	}
    	return Redirect::to('manageOnlineVideo');
    }

    /**
     *  update course video
     */
    protected function update(Request $request){
    	$v = Validator::make($request->all(), $this->validateVideo);
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
    protected function delete(Request $request){
    	$videoId = InputSanitise::inputInt($request->get('video_id'));
    	if(isset($videoId)){
    		$video = ClientOnlineVideo::find($videoId);
    		if(is_object($video)){
                DB::connection('mysql2')->beginTransaction();
                try
                {
                    $video->deleteCommantsAndSubComments();
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

}