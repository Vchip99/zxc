<?php

namespace App\Http\Controllers\ZeroToHero;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use App\Models\ZeroToHero;
use App\Models\Designation;
use App\Models\Area;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;

class ZeroToHeroController extends Controller
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
    protected $validateZeroToHero = [
        'hero' => 'required|string',
        'designation' => 'required|integer',
        'area' => 'required|integer',
        'url' => 'required|string',
        'release_date' => 'required|date',
    ];

    /**
     * show all hero
     */
    protected function show(){
    	$heros = ZeroToHero::paginate();
    	return view('zerotohero.list', compact('heros'));
    }

     /**
     * show all hero
     */
    protected function create(){
    	$designations = Designation::all();
    	$hero = new ZeroToHero;
    	$areas = [];
    	return view('zerotohero.create', compact('designations', 'hero', 'areas'));
    }

    /**
     *  store hero
     */
    protected function store(Request $request){
        $v = Validator::make($request->all(), $this->validateZeroToHero);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::beginTransaction();
        try
        {
            $hero = ZeroToHero::addOrUpdateZeroToHero($request);
            if(is_object($hero)){
                DB::commit();
                return Redirect::to('admin/manageZeroToHero')->with('message', 'Zero To Hero created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
		return Redirect::to('admin/manageZeroToHero');
    }

    /**
     * edit hero
     */
    protected function edit($id){
    	$heroId = InputSanitise::inputInt(json_decode($id));
    	if(isset($heroId)){
    		$hero = ZeroToHero::find($heroId);
    		if(is_object($hero)){
    			$designations = Designation::all();
    			$areas = Area::getAreasByDesignation($hero->designation_id);

    			return view('zerotohero.create', compact('designations', 'hero', 'areas'));
    		}
    	}
		return Redirect::to('admin/manageZeroToHero');
    }

    /**
     * update hero
     */
    protected function update(Request $request){
        $v = Validator::make($request->all(), $this->validateZeroToHero);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        $heroId = InputSanitise::inputInt($request->get('hero_id'));
        if(isset($heroId)){
            DB::beginTransaction();
            try
            {
                $hero = ZeroToHero::addOrUpdateZeroToHero($request, true);
                if(is_object($hero)){
                    DB::commit();
                    return Redirect::to('admin/manageZeroToHero')->with('message', 'Zero To Hero updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('admin/manageZeroToHero');
    }

    /**
     *  delete hero
     */
    protected function delete(Request $request){
    	$heroId = InputSanitise::inputInt($request->get('hero_id'));
    	if(isset($heroId)){
    		$hero = ZeroToHero::find($heroId);
    		if(is_object($hero)){
                DB::beginTransaction();
                try
                {
        			$hero->delete();
                    DB::commit();
                    return Redirect::to('admin/manageZeroToHero')->with('message', 'Zero To Hero deleted successfully!');
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return redirect()->back()->withErrors('something went wrong.');
                }
    		}
    	}
		return Redirect::to('admin/manageZeroToHero');
    }

}
