<?php

namespace App\Http\Controllers\Workshop;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use App\Models\WorkshopCategory;
use App\Models\WorkshopDetail;
use App\Models\WorkshopVideo;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;
use App\Models\User;

class WorkshopVideosController extends Controller
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
    protected $validateWorkshopVideos = [
        'category' => 'required',
        'workshop' => 'required',
        'name' => 'required',
        'description' => 'required',
        'duration' => 'required',
        'video_path' => 'required',
        'date' => 'required',
    ];

    protected function show(){
        $workshopVideos = WorkshopVideo::paginate();
        return view('workshopVideos.list', compact('workshopVideos'));
    }

    protected function create(){
        $workshopVideo = new WorkshopVideo;
        $workshopCategories = WorkshopCategory::all();
        $workshopDetails = [];
        return view('workshopVideos.create', compact('workshopVideo', 'workshopCategories', 'workshopDetails'));
    }

    /**
     *  store workshop Video
     */
    protected function store(Request $request){
        $v = Validator::make($request->all(), $this->validateWorkshopVideos);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::beginTransaction();
        try
        {
            $workshopVideo = WorkshopVideo::addOrUpdateWorkshopVideo($request);
            if(is_object($workshopVideo)){
                DB::commit();
                return Redirect::to('admin/manageWorkshopVideos')->with('message', 'Workshop Video created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('admin/manageWorkshopVideos');
    }

    /**
     *  edit workshop Video
     */
    protected function edit($id){
        $id = InputSanitise::inputInt(json_decode($id));
        if(isset($id)){
            $workshopVideo = WorkshopVideo::find($id);
            if(is_object($workshopVideo)){
                $workshopCategories = WorkshopCategory::all();
                $workshopDetails = WorkshopDetail::getWorkshopsByCategory($workshopVideo->workshop_category_id);
                return view('workshopVideos.create', compact('workshopVideo', 'workshopCategories', 'workshopDetails'));
            }
        }
        return Redirect::to('admin/manageWorkshopVideos');
    }

    /**
     *  update workshop Video
     */
    protected function update(Request $request){
        $videoId = InputSanitise::inputInt($request->get('video_id'));
        if(isset($videoId)){
            DB::beginTransaction();
            try
            {
                $workshopVideo = WorkshopVideo::addOrUpdateWorkshopVideo($request, true);
                if(is_object($workshopVideo)){
                    DB::commit();
                    return Redirect::to('admin/manageWorkshopVideos')->with('message', 'Workshop Video updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return redirect()->back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('admin/manageWorkshopVideos');
    }

    protected function getWorkshopsByCategory(Request $request){
        return WorkshopDetail::getWorkshopsByCategory($request->id);
    }
}