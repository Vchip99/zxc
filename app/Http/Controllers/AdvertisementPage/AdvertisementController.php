<?php

namespace App\Http\Controllers\AdvertisementPage;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use App\Models\Advertisement;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;

class AdvertisementController extends Controller
{
	/**
     *  check admin have permission or not, if not redirect to admin/home
     */
    public function __construct() {
        $this->middleware(function ($request, $next) {
            $adminUser = Auth::guard('admin')->user();
            if(is_object($adminUser)){
                if($adminUser->hasRole('admin') || $adminUser->hasRole('sub-admin')){
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
    protected $validateCreateAdvertisement = [
        'image' => 'dimensions:width=600,height=100'
    ];

	protected function show(){
		$advertisements = Advertisement::getAdvertisements();
    	return view('advertisement.list', compact('advertisements'));
	}

	/**
     * show UI for create
     */
    protected function create(){
    	$advertisement = new Advertisement;
    	return view('advertisement.create', compact('advertisement'));
    }

    /**
     *  store advertisement
     */
    protected function store(Request $request){
        $v = Validator::make($request->all(), $this->validateCreateAdvertisement,['image.dimensions' => 'please upload image have width(600px) and height(100px) ']);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::beginTransaction();
        try
        {
            $advertisement = Advertisement::addOrUpdateAdvertisement($request);
            if(is_object($advertisement)){
                DB::commit();
                return Redirect::to('admin/manageAdvertisements')->with('message', 'Advertisement created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
		return Redirect::to('admin/manageAdvertisements');
    }

       /**
     * edit advertisement
     */
    protected function edit($id){
    	$id = InputSanitise::inputInt(json_decode($id));
    	if(isset($id)){
    		$advertisement = Advertisement::find($id);
    		if(is_object($advertisement)){
    			return view('advertisement.create', compact('advertisement'));
    		}
    	}
		return Redirect::to('admin/manageAdvertisements');
    }

        /**
     * update advertisement
     */
    protected function update(Request $request){
        if($request->exists('image')){
            $v = Validator::make($request->all(), $this->validateCreateAdvertisement);
            if ($v->fails())
            {
                return redirect()->back()->withErrors($v->errors());
            }
        }
        $id = InputSanitise::inputInt($request->get('advertisement_id'));
        if(isset($id)){
            DB::beginTransaction();
            try
            {
                $advertisement = Advertisement::addOrUpdateAdvertisement($request, true);
                if(is_object($advertisement)){
                    DB::commit();
                    return Redirect::to('admin/manageAdvertisements')->with('message', 'Advertisement updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('admin/manageAdvertisements');
    }

      /**
     * delete advertisement
     */
    protected function delete(Request $request){
    	$id = InputSanitise::inputInt($request->get('advertisement_id'));
    	if(isset($id)){
    		$advertisement = Advertisement::find($id);
    		if(is_object($advertisement)){
                DB::beginTransaction();
                try
                {
                    if($advertisement->admin_id == Auth::guard('admin')->user()->id){
                        $dir = "adminAds/".$advertisement->id;
                        InputSanitise::delFolder($dir);
            			$advertisement->delete();
                        DB::commit();
                        return Redirect::to('admin/manageAdvertisements')->with('message', 'Advertisement deleted successfully!');
                    }
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return back()->withErrors('something went wrong.');
                }
            }
        }
		return Redirect::to('admin/manageAdvertisements');
    }
}
