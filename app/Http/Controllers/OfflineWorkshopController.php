<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\OfflineWorkshopDetail;
use App\Models\OfflineWorkshopCategory;
use App\Models\OfflineWorkshopComponent;
use App\Mail\WorkshopQuery;
use DB, Auth, Session;
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
    	$workshops = OfflineWorkshopDetail::paginate();
    	$workshopCategories = OfflineWorkshopCategory::getWorkshopCategory();
        $date = date('Y-m-d');
        $ads = Add::getAdds($request->url(),$date);
    	return view('offlineWorkshops.workshops', compact('workshops', 'workshopCategories', 'ads'));
    }

    protected function offlineWorkshopDetails($id){
    	$id = json_decode($id);
    	$workshop = OfflineWorkshopDetail::find($id);
    	if(is_object($workshop)){
            $workshops = OfflineWorkshopDetail::all();
            $components = OfflineWorkshopComponent::where('offline_workshop_id', $workshop->id)->get();
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