<?php

namespace App\Http\Controllers\MotivationalSpeech;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use App\Models\MotivationalSpeechVideo;
use App\Models\MotivationalSpeechDetail;
use App\Models\MotivationalSpeechCategory;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;
use App\Models\User;

class MotivationalSpeechVideoController extends Controller
{
    /**
     *  check admin have permission or not, if not redirect to admin/home
     */
    public function __construct() {
        $this->middleware(function ($request, $next) {
            $adminUser = Auth::guard('admin')->user();
            if(is_object($adminUser)){
                if($adminUser->hasRole('admin')){
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
    protected $validateMotivationalSpeechVideo = [
        'motivational_speech_category_id' => 'required',
        'motivational_speech_detail_id' => 'required',
        'name' => 'required',
        'video_path' => 'required',
    ];


    protected function show(){
        $motivationalSpeechVideos = MotivationalSpeechVideo::paginate();
        return view('motivationalSpeechVideo.list', compact('motivationalSpeechVideos'));
    }

    protected function create(){
        $motivationalSpeechVideo = new MotivationalSpeechVideo;
        $motivationalSpeechCategories = MotivationalSpeechCategory::all();
        $motivationalSpeechDetails = [];//MotivationalSpeechDetail::all();
        return view('motivationalSpeechVideo.create', compact('motivationalSpeechVideo', 'motivationalSpeechDetails', 'motivationalSpeechCategories'));
    }

    /**
     *  store  motivationalSpeechVideo
     */
    protected function store(Request $request){
        $v = Validator::make($request->all(), $this->validateMotivationalSpeechVideo);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        InputSanitise::deleteCacheByString('vchip:motivationalSpeechs*');
        DB::beginTransaction();
        try
        {
            $motivationalSpeechVideo = MotivationalSpeechVideo::addOrUpdatemotivationalSpeechVideo($request);
            if(is_object($motivationalSpeechVideo)){
                DB::commit();
                return Redirect::to('admin/manageMotivationalSpeechVideos')->with('message', 'Motivational Video created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('admin/manageMotivationalSpeechVideos');
    }

    /**
     *  edit  motivationalSpeechVideo
     */
    protected function edit($id){
        $id = InputSanitise::inputInt(json_decode($id));
        if(isset($id)){
            $motivationalSpeechVideo = MotivationalSpeechVideo::find($id);
            if(is_object($motivationalSpeechVideo)){
                $motivationalSpeechCategories = MotivationalSpeechCategory::all();
            	$motivationalSpeechDetails = MotivationalSpeechDetail::getMotivationalSpeechesByCategoryByAdmin($motivationalSpeechVideo->motivational_speech_category_id);
                return view('motivationalSpeechVideo.create', compact('motivationalSpeechVideo', 'motivationalSpeechDetails', 'motivationalSpeechCategories'));
            }
        }
        return Redirect::to('admin/manageMotivationalSpeechVideos');
    }

    /**
     *  update  motivationalSpeechVideo
     */
    protected function update(Request $request){
        InputSanitise::deleteCacheByString('vchip:motivationalSpeechs*');
        $videoId = InputSanitise::inputInt($request->get('motivational_video_id'));
        if(isset($videoId)){
            DB::beginTransaction();
            try
            {
                $motivationalSpeechVideo = MotivationalSpeechVideo::addOrUpdatemotivationalSpeechVideo($request, true);
                if(is_object($motivationalSpeechVideo)){
                    DB::commit();
                    return Redirect::to('admin/manageMotivationalSpeechVideos')->with('message', 'Motivational Video updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return redirect()->back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('admin/manageMotivationalSpeechVideos');
    }

    /**
     *  delete  motivationalSpeechVideo
     */
    protected function delete(Request $request){
        InputSanitise::deleteCacheByString('vchip:motivationalSpeechs*');
        $videoId = InputSanitise::inputInt($request->get('video_id'));
        if(isset($videoId)){
            $motivationalSpeechVideo = MotivationalSpeechVideo::find($videoId);
            if(is_object($motivationalSpeechVideo)){
                DB::beginTransaction();
                try
                {
                    $motivationalSpeechVideo->delete();
                    DB::commit();
                    return Redirect::to('admin/manageMotivationalSpeechVideos')->with('message', 'Motivational Video deleted successfully!');
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return redirect()->back()->withErrors('something went wrong.');
                }
            }
        }
        return Redirect::to('admin/manageMotivationalSpeechVideos');
    }

    protected function isMotivationalSpeechVideoExist(Request $request){
        return MotivationalSpeechVideo::isMotivationalSpeechVideoExist($request);
    }
}
