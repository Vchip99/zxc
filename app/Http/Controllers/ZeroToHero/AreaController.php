<?php

namespace App\Http\Controllers\ZeroToHero;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use App\Models\Designation;
use App\Models\Area;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;
class AreaController extends Controller
{
    /**
     *  check admin have permission or not, if not redirect to home
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
    protected $validateArea = [
        'designation' => 'required|integer',
        'area' => 'required|string',
    ];

    /**
     * show all designations
     */
    protected function show(){
    	$areas = Area::paginate();
    	return view('area.list', compact('areas'));
    }

    /**
     * show all area
     */
    protected function create(){
    	$designations = Designation::all();
    	$area = new Area;
    	return view('area.create', compact('designations', 'area'));
    }

    /**
     *  store area
     */
    protected function store(Request $request){
        $v = Validator::make($request->all(), $this->validateArea);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        InputSanitise::deleteCacheByString('vchip:heros*');
        DB::beginTransaction();
        try
        {
            $area = Area::addOrUpdateArea($request);
            if(is_object($area)){
                DB::commit();
                return Redirect::to('admin/manageArea')->with('message', 'Area created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
		return Redirect::to('admin/manageArea');
    }

    /**
     * edit area
     */
    protected function edit($id){
    	$areaId = InputSanitise::inputInt(json_decode($id));
    	if(isset($areaId)){
    		$area = Area::find($areaId);
    		if(is_object($area)){
    			$designations = Designation::all();
    			return view('area.create', compact('designations','area'));
    		}
    	}
		return Redirect::to('admin/manageArea');
    }
    /**
     * update area
     */
    protected function update(Request $request){
        $v = Validator::make($request->all(), $this->validateArea);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        InputSanitise::deleteCacheByString('vchip:heros*');
        $areaId = InputSanitise::inputInt($request->get('area_id'));
        if(isset($areaId)){
            DB::beginTransaction();
            try
            {
                $area = Area::addOrUpdateArea($request, true);
                if(is_object($area)){
                    DB::commit();
                    return Redirect::to('admin/manageArea')->with('message', 'Area updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('admin/manageArea');
    }

    /**
     *  delete area
     */
    protected function delete(Request $request){
        InputSanitise::deleteCacheByString('vchip:heros*');
        $areaId = InputSanitise::inputInt($request->get('area_id'));
        if(isset($areaId)){
            $area = Area::find($areaId);

            if(is_object($area)){
                DB::beginTransaction();
                try
                {
                    if(is_object($area->heros) && false == $area->heros->isEmpty()){
                        foreach($area->heros as $hero){
                            $hero->delete();
                        }
                    }
                    $area->delete();
                    DB::commit();
                    return Redirect::to('admin/manageArea')->with('message', 'Area deleted successfully!');
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return redirect()->back()->withErrors('something went wrong.');
                }
            }
        }
        return Redirect::to('admin/manageArea');
    }

    protected function getAreasByDesignation(Request $request){
        $designationId   = InputSanitise::inputInt($request->get('designation_id'));
        return Area::getAreasByDesignation($designationId);
    }

    protected function isAreaExist(Request $request){
        return Area::isAreaExist($request);
    }
}
