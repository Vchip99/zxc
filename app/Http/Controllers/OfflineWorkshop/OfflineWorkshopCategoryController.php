<?php

namespace App\Http\Controllers\OfflineWorkshop;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use App\Models\OfflineWorkshopCategory;
use App\Models\OfflineWorkshopDetail;
use App\Models\OfflineWorkshopComponent;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;
use App\Models\User;

class OfflineWorkshopCategoryController extends Controller
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
    protected $validateWorkshopCategory = [
        'category' => 'required|string',
    ];


    protected function show(){
        $workshopCategories = OfflineWorkshopCategory::paginate();
        return view('offlineWorkshopCategory.list', compact('workshopCategories'));
    }

    protected function create(){
        $workshopCategory = new OfflineWorkshopCategory;
        return view('offlineWorkshopCategory.create', compact('workshopCategory'));
    }

    /**
     *  store workshop category
     */
    protected function store(Request $request){
        $v = Validator::make($request->all(), $this->validateWorkshopCategory);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::beginTransaction();
        try
        {
            $category = OfflineWorkshopCategory::addOrUpdateWorkshopCategory($request);
            if(is_object($category)){
                DB::commit();
                return Redirect::to('admin/manageOfflineWorkshopCategory')->with('message', 'Category created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('admin/manageOfflineWorkshopCategory');
    }

    /**
     *  edit workshop category
     */
    protected function edit($id){
        $id = InputSanitise::inputInt(json_decode($id));
        if(isset($id)){
            $workshopCategory = OfflineWorkshopCategory::find($id);
            if(is_object($workshopCategory)){
                return view('offlineWorkshopCategory.create', compact('workshopCategory'));
            }
        }
        return Redirect::to('admin/manageOfflineWorkshopCategory');
    }

    /**
     *  update workshop category
     */
    protected function update(Request $request){
        $categoryId = InputSanitise::inputInt($request->get('category_id'));
        if(isset($categoryId)){
            DB::beginTransaction();
            try
            {
                $category = OfflineWorkshopCategory::addOrUpdateWorkshopCategory($request, true);
                if(is_object($category)){
                    DB::commit();
                    return Redirect::to('admin/manageOfflineWorkshopCategory')->with('message', 'Category updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return redirect()->back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('admin/manageOfflineWorkshopCategory');
    }

    /**
     *  delete workshop category
     */
    protected function delete(Request $request){
        $categoryId = InputSanitise::inputInt($request->get('category_id'));
        if(isset($categoryId)){
            $workshopCategory = OfflineWorkshopCategory::find($categoryId);
            if(is_object($workshopCategory)){
                DB::beginTransaction();
                try
                {
                    $offlineWorkshopDetails = OfflineWorkshopDetail::where('offline_workshop_category_id', $workshopCategory->id)->get();
                    if(is_object($offlineWorkshopDetails) && false == $offlineWorkshopDetails->isEmpty()){
                        foreach($offlineWorkshopDetails as $workshopDetail){
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
                        }
                    }
                    $workshopCategory->delete();
                    DB::commit();
                    return Redirect::to('admin/manageOfflineWorkshopCategory')->with('message', 'Workshop category deleted successfully!');
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return redirect()->back()->withErrors('something went wrong.');
                }
            }
        }
        return Redirect::to('admin/manageOfflineWorkshopCategory');
    }
}
