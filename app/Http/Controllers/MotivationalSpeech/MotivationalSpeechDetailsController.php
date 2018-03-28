<?php

namespace App\Http\Controllers\MotivationalSpeech;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use App\Models\MotivationalSpeechCategory;
use App\Models\MotivationalSpeechDetail;
use App\Models\MotivationalSpeechVideo;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;
use App\Models\User;

class MotivationalSpeechDetailsController extends Controller
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
    protected $validateMotivationalSpeechDetails = [
        'category' => 'required',
        'name' => 'required',
        'about' => 'required',
        'about_image' => 'required',
        'topics' => 'required',
        'program_details' => 'required'
    ];

    protected function show(){
        $motivationalSpeechDetails = MotivationalSpeechDetail::paginate();
        return view('motivationalSpeechDetail.list', compact('motivationalSpeechDetails'));
    }

    protected function create(){
        $motivationalSpeechDetail = new MotivationalSpeechDetail;
        $motivationalSpeechCategories = MotivationalSpeechCategory::all();
        return view('motivationalSpeechDetail.create', compact('motivationalSpeechDetail', 'motivationalSpeechCategories'));
    }

    /**
     *  store workshop Details
     */
    protected function store(Request $request){
        $v = Validator::make($request->all(), $this->validateMotivationalSpeechDetails);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        InputSanitise::deleteCacheByString('vchip:motivationalSpeechs*');
        DB::beginTransaction();
        try
        {
            $motivationalSpeechDetail = MotivationalSpeechDetail::addOrUpdateMotivationalSpeechDetails($request);
            if(is_object($motivationalSpeechDetail)){
                DB::commit();
                return Redirect::to('admin/manageMotivationalSpeechDetails')->with('message', 'Motivational Speech Details created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('admin/manageMotivationalSpeechDetails');
    }

    /**
     *  edit workshop Details
     */
    protected function edit($id){
        $id = InputSanitise::inputInt(json_decode($id));
        if(isset($id)){
            $motivationalSpeechDetail = MotivationalSpeechDetail::find($id);
            if(is_object($motivationalSpeechDetail)){
                $motivationalSpeechCategories = MotivationalSpeechCategory::all();
                return view('motivationalSpeechDetail.create', compact('motivationalSpeechDetail', 'motivationalSpeechCategories'));
            }
        }
        return Redirect::to('admin/manageMotivationalSpeechDetails');
    }

    /**
     *  update workshop Details
     */
    protected function update(Request $request){
        InputSanitise::deleteCacheByString('vchip:motivationalSpeechs*');
        $motivationalSpeechId = InputSanitise::inputInt($request->get('motivational_speech_id'));
        if(isset($motivationalSpeechId)){
            DB::beginTransaction();
            try
            {
                $motivationalSpeechDetail = MotivationalSpeechDetail::addOrUpdateMotivationalSpeechDetails($request, true);
                if(is_object($motivationalSpeechDetail)){
                    DB::commit();
                    return Redirect::to('admin/manageMotivationalSpeechDetails')->with('message', 'Motivational Speech Details updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return redirect()->back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('admin/manageMotivationalSpeechDetails');
    }

    /**
     *  delete workshop
     */
    protected function delete(Request $request){
        InputSanitise::deleteCacheByString('vchip:motivationalSpeechs*');
        $speechId = InputSanitise::inputInt($request->get('motivational_speech_details_id'));
        if(isset($speechId)){
            $motivationalSpeechDetail = MotivationalSpeechDetail::find($speechId);
            if(is_object($motivationalSpeechDetail)){
                DB::beginTransaction();
                try
                {
                    $videos = MotivationalSpeechVideo::where('motivational_speech_detail_id', $motivationalSpeechDetail->id)->get();
                    if(is_object($videos) && false == $videos->isEmpty()){
                        foreach($videos as $video){
                            $video->delete();
                        }
                    }
                    $motivationalSpeechDetailImageFolder = "motivationalSpeechDetailsImages/".str_replace(' ', '_', $motivationalSpeechDetail->name);
                    if(is_dir($motivationalSpeechDetailImageFolder)){
                        InputSanitise::delFolder($motivationalSpeechDetailImageFolder);
                    }
                    $motivationalSpeechDetail->delete();
                    DB::commit();
                    return Redirect::to('admin/manageMotivationalSpeechDetails')->with('message', 'Motivational Speech deleted successfully!');
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return redirect()->back()->withErrors('something went wrong.');
                }
            }
        }
        return Redirect::to('admin/manageMotivationalSpeechDetails');
    }

    protected function isMotivationalSpeechExist(Request $request){
        return MotivationalSpeechDetail::isMotivationalSpeechExist($request);
    }


    protected function getMotivationalSpeechesByCategoryByAdmin(Request $request){
        $category   = InputSanitise::inputInt($request->get('category'));
        return MotivationalSpeechDetail::getMotivationalSpeechesByCategoryByAdmin($category);
    }
}
