<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\OfflineWorkshopDetail;
use App\Models\OfflineWorkshopCategory;
use App\Models\OfflineWorkshopComponent;
use App\Mail\WorkshopQuery;
use DB, Auth, Session, Cache;
use Validator, Redirect,Hash;
use App\Libraries\InputSanitise;
use App\Models\Add;

class OfflineWorkshopController extends Controller
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
        $workshops = Cache::remember('vchip:workshops-'.$page,60, function() {
            return OfflineWorkshopDetail::paginate();
        });
        $workshopCategories = Cache::remember('vchip:workshopCategories',60, function() {
            return OfflineWorkshopCategory::getWorkshopCategory();
        });
        $date = date('Y-m-d');
        $ads = Add::getAdds($request->url(),$date);
    	return view('offlineWorkshops.workshops', compact('workshops', 'workshopCategories', 'ads'));
    }

    protected function offlineWorkshopDetails($id){
    	$id = json_decode($id);
        $workshop = Cache::remember('vchip:workshop-'.$id,60, function() use ($id){
            return OfflineWorkshopDetail::find($id);
        });
    	if(is_object($workshop)){
            $workshops = Cache::remember('vchip:workshops',60, function() {
                return OfflineWorkshopDetail::all();
            });
            $components = Cache::remember('vchip:components:workshop-'.$id,60, function() use($id){
                return OfflineWorkshopComponent::where('offline_workshop_id', $id)->get();
            });
    		return view('offlineWorkshops.workshopDetails', compact('workshop', 'workshops', 'components'));
    	}
    	return Redirect::to('workshops');
    }

    protected function workshopQuery(Request $request){
        // send mail to admin
        Mail::to('vchipdesigng8@gmail.com')->send(new WorkshopQuery($request->all()));
        return redirect()->back()->with('message', 'Mail sent successfully. we will reply asap.');
    }

    protected function getOfflineWorkshopsByCategory(Request $request){
        return OfflineWorkshopDetail::getOfflineWorkshopsByCategory($request);
    }

}