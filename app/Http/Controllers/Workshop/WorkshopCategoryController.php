<?php

namespace App\Http\Controllers\Workshop;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use App\Models\WorkshopCategory;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;
use App\Models\User;

class WorkshopCategoryController extends Controller
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
        $workshopCategories = WorkshopCategory::paginate();
        return view('workshopCategory.list', compact('workshopCategories'));
    }

    protected function create(){
        $workshopCategory = new WorkshopCategory;
        return view('workshopCategory.create', compact('workshopCategory'));
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
            $category = WorkshopCategory::addOrUpdateWorkshopCategory($request);
            if(is_object($category)){
                DB::commit();
                return Redirect::to('admin/manageWorkshopCategory')->with('message', 'Category created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('admin/manageWorkshopCategory');
    }

    /**
     *  edit workshop category
     */
    protected function edit($id){
        $id = InputSanitise::inputInt(json_decode($id));
        if(isset($id)){
            $workshopCategory = WorkshopCategory::find($id);
            if(is_object($workshopCategory)){
                return view('workshopCategory.create', compact('workshopCategory'));
            }
        }
        return Redirect::to('admin/manageWorkshopCategory');
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
                $category = WorkshopCategory::addOrUpdateWorkshopCategory($request, true);
                if(is_object($category)){
                    DB::commit();
                    return Redirect::to('admin/manageWorkshopCategory')->with('message', 'Category updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return redirect()->back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('admin/manageWorkshopCategory');
    }

    /**
     *  delete workshop category
     */
    protected function delete(Request $request){
        $categoryId = InputSanitise::inputInt($request->get('category_id'));
        if(isset($categoryId)){
            $workshopCategory = WorkshopCategory::find($categoryId);
            if(is_object($workshopCategory)){
                DB::beginTransaction();
                try
                {
                    if(is_object($workshopCategory->workshops) && false == $workshopCategory->workshops->isEmpty()){
                        foreach($workshopCategory->workshops as $workshopDetail){
                            if(is_object($workshopDetail->workshopVideos) && false == $workshopDetail->workshopVideos->isEmpty()){
                                foreach($workshopDetail->workshopVideos as $workshopVideo){
                                    $workshopVideo->delete();
                                }
                            }
                            $workshopDetail->delete();
                        }
                    }
                    $workshopCategory->delete();
                    DB::commit();
                    return Redirect::to('admin/manageWorkshopCategory')->with('message', 'Workshop category deleted successfully!');
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return redirect()->back()->withErrors('something went wrong.');
                }
            }
        }
        return Redirect::to('admin/manageWorkshopCategory');
    }
}