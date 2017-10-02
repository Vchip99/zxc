<?php

namespace App\Http\Controllers\Placement;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use App\Models\PlacementArea;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;
use App\Models\User;

class PlacementAreaController extends Controller
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
    protected $validatePlacementArea = [
        'area' => 'required',
    ];


    protected function show(){
        $placementAreas = PlacementArea::paginate();
        return view('placementArea.list', compact('placementAreas'));
    }

    protected function create(){
        $placementArea = new PlacementArea;
        return view('placementArea.create', compact('placementArea'));
    }

    /**
     *  store PlacementArea
     */
    protected function store(Request $request){
        $v = Validator::make($request->all(), $this->validatePlacementArea);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::beginTransaction();
        try
        {
            $placementArea = PlacementArea::addOrUpdatePlacementArea($request);
            if(is_object($placementArea)){
                DB::commit();
                return Redirect::to('admin/managePlacementArea')->with('message', 'Placement Area created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('admin/managePlacementArea');
    }

    /**
     *  edit PlacementArea
     */
    protected function edit($id){
        $id = InputSanitise::inputInt(json_decode($id));
        if(isset($id)){
            $placementArea = PlacementArea::find($id);
            if(is_object($placementArea)){
                return view('placementArea.create', compact('placementArea'));
            }
        }
        return Redirect::to('admin/managePlacementArea');
    }

    /**
     *  update PlacementArea
     */
    protected function update(Request $request){
        $categoryId = InputSanitise::inputInt($request->get('category_id'));
        if(isset($categoryId)){
            DB::beginTransaction();
            try
            {
                $placementArea = PlacementArea::addOrUpdatePlacementArea($request, true);
                if(is_object($placementArea)){
                    DB::commit();
                    return Redirect::to('admin/managePlacementArea')->with('message', 'Placement Area updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return redirect()->back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('admin/managePlacementArea');
    }
}