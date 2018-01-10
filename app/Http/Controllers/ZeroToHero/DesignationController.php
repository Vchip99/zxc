<?php

namespace App\Http\Controllers\ZeroToHero;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use App\Models\Designation;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;

class DesignationController extends Controller
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
    protected $validateDesignation = [
        'designation' => 'required|string',
    ];

    /**
     * show all designations
     */
    protected function show(){
    	$designations = Designation::paginate();
    	return view('designation.list', compact('designations'));
    }

    /**
     * show all designation
     */
    protected function create(){
    	$designation = new Designation;
    	return view('designation.create', compact('designation'));
    }

    /**
     *  store designation
     */
    protected function store(Request $request){
        $v = Validator::make($request->all(), $this->validateDesignation);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::beginTransaction();
        try
        {
            $designation = Designation::addOrUpdateDesignation($request);
            if(is_object($designation)){
                DB::commit();
                return Redirect::to('admin/manageDesignation')->with('message', 'Designation created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
		return Redirect::to('admin/manageDesignation');
    }

    /**
     * edit designation
     */
    protected function edit($id){
    	$designationId = InputSanitise::inputInt(json_decode($id));
    	if(isset($designationId)){
    		$designation = Designation::find($designationId);
    		if(is_object($designation)){
    			return view('designation.create', compact('designation'));
    		}
    	}
		return Redirect::to('admin/manageDesignation');
    }

    /**
     * update designation
     */
    protected function update(Request $request){
        $v = Validator::make($request->all(), $this->validateDesignation);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        $designationId = InputSanitise::inputInt($request->get('designation_id'));
        if(isset($designationId)){
            DB::beginTransaction();
            try
            {
                $designation = Designation::addOrUpdateDesignation($request, true);
                if(is_object($designation)){
                    DB::commit();
                    return Redirect::to('admin/manageDesignation')->with('message', 'Designation updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('admin/manageDesignation');
    }

    /**
     *  delete designation
     */
    protected function delete(Request $request){
        $designationId = InputSanitise::inputInt($request->get('designation_id'));
        if(isset($designationId)){
            $designation = Designation::find($designationId);

            if(is_object($designation)){
                DB::beginTransaction();
                try
                {
                    if(is_object($designation->areas) && false == $designation->areas->isEmpty()){
                        foreach($designation->areas as $area){
                           if(is_object($area->heros) && false == $area->heros->isEmpty()){
                                foreach($area->heros as $hero){
                                    $hero->delete();
                                }
                            }
                            $area->delete();
                        }
                    }
                    $designation->delete();
                    DB::commit();
                    return Redirect::to('admin/manageDesignation')->with('message', 'Designation deleted successfully!');
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return redirect()->back()->withErrors('something went wrong.');
                }
            }
        }
        return Redirect::to('admin/manageDesignation');
    }

    protected function isDesignationExist(Request $request){
        return Designation::isDesignationExist($request);
    }
}
