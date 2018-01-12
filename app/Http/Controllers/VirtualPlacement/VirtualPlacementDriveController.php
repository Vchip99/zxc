<?php

namespace App\Http\Controllers\VirtualPlacement;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use App\Models\VirtualPlacementDrive;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;
use App\Models\User;

class VirtualPlacementDriveController extends Controller
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
    protected $validateVirtualPlacementDrive = [
        'name' => 'required',
        'about' => 'required',
        'online_test' => 'required',
        'hr' => 'required',
        'suggestions' => 'required',
        'advantages' => 'required',
        'gd' => 'required',
        'pi' => 'required',
        'program_arrangement' => 'required'
    ];


    protected function show(){
        $virtualPlacementDrives = VirtualPlacementDrive::paginate();
        return view('virtualPlacementDrive.list', compact('virtualPlacementDrives'));
    }

    protected function create(){
        $virtualPlacementDrive = new VirtualPlacementDrive;
        return view('virtualPlacementDrive.create', compact('virtualPlacementDrive'));
    }

    /**
     *  store
     */
    protected function store(Request $request){
        $v = Validator::make($request->all(), $this->validateVirtualPlacementDrive);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::beginTransaction();
        try
        {
            $virtualPlacementDrive = VirtualPlacementDrive::addOrUpdateVirtualPlacementDrive($request);
            if(is_object($virtualPlacementDrive)){
                DB::commit();
                return Redirect::to('admin/manageVirtualPlacementDrive')->with('message', 'virtual placement drive created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('admin/manageVirtualPlacementDrive');
    }

    /**
     *  edit
     */
    protected function edit($id){
        $id = InputSanitise::inputInt(json_decode($id));
        if(isset($id)){
            $virtualPlacementDrive = VirtualPlacementDrive::find($id);
            if(is_object($virtualPlacementDrive)){
                return view('virtualPlacementDrive.create', compact('virtualPlacementDrive'));
            }
        }
        return Redirect::to('admin/manageVirtualPlacementDrive');
    }

    /**
     *  update
     */
    protected function update(Request $request){
        $placementId = InputSanitise::inputInt($request->get('placement_id'));
        if(isset($placementId)){
            DB::beginTransaction();
            try
            {
                $virtualPlacementDrive = VirtualPlacementDrive::addOrUpdateVirtualPlacementDrive($request, true);
                if(is_object($virtualPlacementDrive)){
                    DB::commit();
                    return Redirect::to('admin/manageVirtualPlacementDrive')->with('message', 'virtual placement drive updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return redirect()->back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('admin/manageVirtualPlacementDrive');
    }

    /**
     *  delete
     */
    protected function delete(Request $request){
        $placementId = InputSanitise::inputInt($request->get('placement_id'));
        if(isset($placementId)){
            $virtualPlacementDrive = VirtualPlacementDrive::find($placementId);
            if(is_object($virtualPlacementDrive)){
                DB::beginTransaction();
                try
                {
                    $virtualPlacementDrive->delete();
                    DB::commit();
                    return Redirect::to('admin/manageVirtualPlacementDrive')->with('message', 'virtual placement drive deleted successfully!');
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return redirect()->back()->withErrors('something went wrong.');
                }
            }
        }
        return Redirect::to('admin/manageVirtualPlacementDrive');
    }
}
