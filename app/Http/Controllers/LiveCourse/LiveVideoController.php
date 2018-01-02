<?php

namespace App\Http\Controllers\LiveCourse;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use App\Models\LiveCourse;
use App\Models\LiveVideo;
use App\Models\Notification;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;
use App\Mail\MailToSubscribedUser;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class LiveVideoController extends Controller
{
	public function __construct() {
        $this->middleware(function ($request, $next) {
            $adminUser = Auth::guard('admin')->user();
            if(is_object($adminUser)){
                if($adminUser->hasRole('admin') || $adminUser->hasPermission('manageLiveCourse')){
                    return $next($request);
                }
            }
            return Redirect::to('admin/home');
        });
    }

    /**
     * Define your validation rules in a property in
     * the controller to reuse the rules.
     */
    protected $validateLiveVideo = [
        'video' => 'required|string',
        'description' => 'required|string',
        'duration' => 'required|string',
        'course' => 'required|integer',
        'video_path' => 'required|string',
        'start_date' => 'required|date'
    ];

    /**
     *  show list of videos
     */
    protected function show(){
    	$liveVideos = LiveVideo::paginate();
    	return view('liveVideo.list', compact('liveVideos'));
    }

    /**
     *  show create video UI
     */
    protected function create(){
        $liveVideo = new LiveVideo;
        $liveCourses = LiveCourse::all();
        return view('liveVideo.create', compact('liveVideo', 'liveCourses'));
    }

    /**
     *  store video
     */
    protected function store(Request $request){
        $v = Validator::make($request->all(), $this->validateLiveVideo);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::beginTransaction();
        try
        {
            $video = LiveVideo::addOrUpdateLiveVideo($request);
            if(is_object($video)){
                $messageBody = '';
                $notificationMessage = 'A new live course video: <a href="'.$request->root().'/liveEpisode/'.$video->id.'">'.$video->name.'</a> has been added.';
                Notification::addNotification($notificationMessage, Notification::ADMINLIVECOURSEVIDEO, $video->id);
                DB::commit();
                $subscriedUsers = User::where('admin_approve', 1)->where('verified', 1)->select('email')->get();
                $allUsers = $subscriedUsers->chunk(100);
                set_time_limit(0);
                if(count($allUsers) > 0){
                    foreach($allUsers as $selectedUsers){
                        $messageBody .= '<p> Dear User</p>';
                        $messageBody .= '<p>'.$notificationMessage.' please have a look once.</p>';
                        $messageBody .= '<p><b> Thanks and Regard, </b></p>';
                        $messageBody .= '<b><a href="https://vchiptech.com"> Vchip Technology Team </a></b><br/>';
                        $messageBody .= '<b> More about us... </b><br/>';
                        $messageBody .= '<b><a href="https://vchipedu.com"> Digital Education </a></b><br/>';
                        $messageBody .= '<b><a href="mailto:info@vchiptech.com" target="_blank">E-mail</a></b><br/>';
                        $mailSubject = 'Vchipedu added a new live course video';
                        Mail::bcc($selectedUsers)->queue(new MailToSubscribedUser($messageBody, $mailSubject));
                    }
                }
                return Redirect::to('admin/manageLiveVideo')->with('message', 'Live Video Created Successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('admin/manageLiveVideo');
    }

    /**
     *  edit video
     */
    protected function edit($id){
        $id = InputSanitise::inputInt(json_decode($id));
        if(isset($id)){
            $liveVideo = LiveVideo::find($id);
            if(is_object($liveVideo)){
                $liveCourses = LiveCourse::all();
                return view('liveVideo.create', compact('liveVideo', 'liveCourses'));
            }
        }
        return Redirect::to('admin/manageLiveVideo');
    }

    /**
     *  update video
     */
    protected function update(Request $request){
        $v = Validator::make($request->all(), $this->validateLiveVideo);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::beginTransaction();
        try
        {
            $video = LiveVideo::addOrUpdateLiveVideo($request, true);
            if(is_object($video)){
                DB::commit();
                return Redirect::to('admin/manageLiveVideo')->with('message', 'Live Video Updated Successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('admin/manageLiveVideo');
    }

    /**
     *  delete video
     */
    protected function delete(Request $request){
        $videoId = InputSanitise::inputInt($request->get('live_video_id'));
        if(isset($videoId)){
            $video = LiveVideo::find($videoId);
            if(is_object($video)){
                DB::beginTransaction();
                try
                {
                    $video->deleteCommantsAndSubComments();
                    $video->delete();
                    DB::commit();
                    return Redirect::to('admin/manageLiveVideo')->with('message', 'Live Video deleted successfully!');
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return redirect()->back()->withErrors('something went wrong.');
                }
            }
        }
        return Redirect::to('admin/manageLiveVideo');
    }
}