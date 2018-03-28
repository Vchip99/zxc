<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\MotivationalSpeechCategory;
use App\Models\MotivationalSpeechVideo;
use App\Models\MotivationalSpeechDetail;
use App\Mail\MotivationalSpeechQuery;
use DB, Auth, Session, Cache;
use Validator, Redirect,Hash;
use App\Libraries\InputSanitise;
use App\Models\Add;

class MotivationalSpeechController extends Controller
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
        if(empty($request->getQueryString())){
            $page = 'page=1';
        } else {
            $page = $request->getQueryString();
        }
        $motivationalSpeechDetails = Cache::remember('vchip:motivationalSpeechs:motivationalSpeech-'.$page,60, function() {
            return MotivationalSpeechDetail::paginate();
        });
        $motivationalSpeechCategories = Cache::remember('vchip:motivationalSpeechs:motivationalSpeechCategories',60, function() {
            return MotivationalSpeechCategory::all();
        });
        $date = date('Y-m-d');
        $ads = Add::getAdds($request->url(),$date);
    	return view('motivationalSpeech.motivationalSpeeches', compact('motivationalSpeechDetails', 'motivationalSpeechCategories', 'ads'));
    }

    protected function motivationalSpeechDetails($id){
    	$id = json_decode($id);
        $motivationalSpeechDetail = Cache::remember('vchip:motivationalSpeechs:motivationalSpeech-'.$id,60, function() use ($id){
            return MotivationalSpeechDetail::find($id);
        });
    	if(is_object($motivationalSpeechDetail)){
            $motivationalSpeechDetails = Cache::remember('vchip:motivationalSpeechs:motivationalSpeechs',60, function(){
                return MotivationalSpeechDetail::all();
            });
            $videos = Cache::remember('vchip:motivationalSpeechs:videos:speechId-'.$id,60, function() use ($id){
                return MotivationalSpeechVideo::where('motivational_speech_detail_id', $id)->get();
            });
    		return view('motivationalSpeech.motivationalSpeechDetails', compact('motivationalSpeechDetail', 'motivationalSpeechDetails', 'videos'));
    	}
    	return Redirect::to('motivationalspeech');
    }

    protected function getMotivationalSpeechesByCategory(Request $request){
    	return MotivationalSpeechDetail::getMotivationalSpeechesByCategory($request);
    }

    protected function motivationalspeechquery(Request $request){
        // send mail to admin
        Mail::to('vchipdesigng8@gmail.com')->send(new MotivationalSpeechQuery($request->all()));
        return redirect()->back()->with('message', 'Mail sent successfully. we will reply asap.');
    }
}
