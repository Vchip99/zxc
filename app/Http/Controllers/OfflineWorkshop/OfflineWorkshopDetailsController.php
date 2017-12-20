<?php

namespace App\Http\Controllers\OfflineWorkshop;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use App\Models\OfflineWorkshopCategory;
use App\Models\OfflineWorkshopDetail;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;
use App\Models\User;
use App\Models\OfflineWorkshopComponent;

class OfflineWorkshopDetailsController extends Controller
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
    protected $validateWorkshopDetails = [
        'category' => 'required',
        'workshop' => 'required',
        'about' => 'required',
        'about_image' => 'required',
        'benefits' => 'required',
        'benefits_image' => 'required',
        'duration' => 'required',
        'topics' => 'required',
        'projects' => 'required',
        'prerequisite' => 'required',
        'attendees' => 'required',
        'learn_reason' => 'required',
    ];

    protected function show(){
        $workshopDetails = OfflineWorkshopDetail::paginate();
        return view('offlineWorkshopDetails.list', compact('workshopDetails'));
    }

    protected function create(){
        $workshopDetail = new OfflineWorkshopDetail;
        $workshopCategories = OfflineWorkshopCategory::all();
        $components = [];
        return view('offlineWorkshopDetails.create', compact('workshopDetail', 'workshopCategories', 'components'));
    }

    /**
     *  store workshop Details
     */
    protected function store(Request $request){
        $v = Validator::make($request->all(), $this->validateWorkshopDetails);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }

        DB::beginTransaction();
        try
        {
            $workshopDetails = OfflineWorkshopDetail::addOrUpdateWorkshopDetails($request);
            if(is_object($workshopDetails)){
                DB::commit();
                return Redirect::to('admin/manageOfflineWorkshopDetails')->with('message', 'Workshop Details created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('admin/manageOfflineWorkshopDetails');
    }

    /**
     *  edit workshop Details
     */
    protected function edit($id){
        $id = InputSanitise::inputInt(json_decode($id));
        if(isset($id)){
            $workshopDetail = OfflineWorkshopDetail::find($id);
            if(is_object($workshopDetail)){
                $workshopCategories = OfflineWorkshopCategory::all();
                $components = OfflineWorkshopComponent::where('offline_workshop_id', $workshopDetail->id)->get();
                return view('offlineWorkshopDetails.create', compact('workshopDetail', 'workshopCategories', 'components'));
            }
        }
        return Redirect::to('admin/manageOfflineWorkshopDetails');
    }

    /**
     *  update workshop Details
     */
    protected function update(Request $request){
        $workshopId = InputSanitise::inputInt($request->get('workshop_id'));
        if(isset($workshopId)){
            DB::beginTransaction();
            try
            {
                $workshopDetail = OfflineWorkshopDetail::addOrUpdateWorkshopDetails($request, true);
                if(is_object($workshopDetail)){
                    DB::commit();
                    return Redirect::to('admin/manageOfflineWorkshopDetails')->with('message', 'Workshop Details updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return redirect()->back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('admin/manageOfflineWorkshopDetails');
    }

    /**
     *  delete workshop
     */
    protected function delete(Request $request){
        $workshopId = InputSanitise::inputInt($request->get('workshop_id'));
        if(isset($workshopId)){
            $workshopDetail = OfflineWorkshopDetail::find($workshopId);
            if(is_object($workshopDetail)){
                DB::beginTransaction();
                try
                {
                    $components = OfflineWorkshopComponent::where('offline_workshop_id', $workshopDetail->id)->get();
                    if(is_object($components) && false == $components->isEmpty()){
                        foreach($components as $component){
                            $component->delete();
                        }
                    }
                    $workshopImageFolder = "offlineWorkshopImages/".str_replace(' ', '_', $workshopDetail->name);
                    if(is_dir($workshopImageFolder)){
                        InputSanitise::delFolder($workshopImageFolder);
                    }
                    $workshopDetail->delete();
                    DB::commit();
                    return Redirect::to('admin/manageOfflineWorkshopDetails')->with('message', 'Workshop deleted successfully!');
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return redirect()->back()->withErrors('something went wrong.');
                }
            }
        }
        return Redirect::to('admin/manageOfflineWorkshopDetails');
    }
}