<?php

namespace App\Http\Controllers\Workshop;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use App\Models\WorkshopCategory;
use App\Models\WorkshopDetail;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;
use App\Models\User;

class WorkshopDetailsController extends Controller
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
        'author' => 'required',
        'author_introduction' => 'required',
        'description' => 'required',
        'start_date' => 'required',
        'end_date' => 'required',
    ];

    protected function show(){
        $workshopDetails = WorkshopDetail::paginate();
        return view('workshopDetails.list', compact('workshopDetails'));
    }

    protected function create(){
        $workshopDetail = new WorkshopDetail;
        $workshopCategories = WorkshopCategory::all();
        return view('workshopDetails.create', compact('workshopDetail', 'workshopCategories'));
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
            $workshopDetails = WorkshopDetail::addOrUpdateWorkshopDetails($request);
            if(is_object($workshopDetails)){
                DB::commit();
                return Redirect::to('admin/manageWorkshopDetails')->with('message', 'Workshop Details created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('admin/manageWorkshopDetails');
    }

    /**
     *  edit workshop Details
     */
    protected function edit($id){
        $id = InputSanitise::inputInt(json_decode($id));
        if(isset($id)){
            $workshopDetail = WorkshopDetail::find($id);
            if(is_object($workshopDetail)){
                $workshopCategories = WorkshopCategory::all();
                return view('workshopDetails.create', compact('workshopDetail', 'workshopCategories'));
            }
        }
        return Redirect::to('admin/manageWorkshopDetails');
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
                $WorkshopDetail = WorkshopDetail::addOrUpdateWorkshopDetails($request, true);
                if(is_object($WorkshopDetail)){
                    DB::commit();
                    return Redirect::to('admin/manageWorkshopDetails')->with('message', 'Workshop Details updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return redirect()->back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('admin/manageWorkshopDetails');
    }
}