<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\MotivationalSpeechCategory;
use App\Models\MotivationalSpeechVideo;
use App\Models\MotivationalSpeechDetail;
use App\Mail\MotivationalSpeechQuery;
use DB, Auth, Session;
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
    	$motivationalSpeechDetails = MotivationalSpeechDetail::paginate();
    	$motivationalSpeechCategories = MotivationalSpeechCategory::all();
        $date = date('Y-m-d');
        $ads = Add::getAdds($request->url(),$date);
    	return view('motivationalSpeech.motivationalSpeeches', compact('motivationalSpeechDetails', 'motivationalSpeechCategories', 'ads'));
    }

    protected function motivationalSpeechDetails($id){
    	$id = json_decode($id);
    	$motivationalSpeechDetail = MotivationalSpeechDetail::find($id);
    	if(is_object($motivationalSpeechDetail)){
            $motivationalSpeechDetails = MotivationalSpeechDetail::all();
            $videos = MotivationalSpeechVideo::where('motivational_speech_detail_id', $motivationalSpeechDetail->id)->get();
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
