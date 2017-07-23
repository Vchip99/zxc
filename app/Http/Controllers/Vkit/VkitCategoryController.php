<?php

namespace App\Http\Controllers\Vkit;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use App\Models\VkitCategory;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;

class VkitCategoryController extends Controller
{
    /**
     *  check admin have permission or not, if not redirect to admin/home
     */
	public function __construct() {
        $this->middleware(function ($request, $next) {
            $adminUser = Auth::guard('admin')->user();
            if(is_object($adminUser)){
                if($adminUser->hasRole('admin') || $adminUser->hasPermission('manageVkit')){
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
    protected $validateVkitCategory = [
        'category' => 'required|string',
    ];

    /**
     *  show list of vkit category
     */
    protected function show(){
    	$vkitCategories = VkitCategory::all();
    	return view('vkitCategory.list', compact('vkitCategories'));
    }

    /**
     *  show create vkit category UI
     */
    protected function create(){
        $vkitCategory = new VkitCategory;
        return view('vkitCategory.create', compact('vkitCategory'));
    }

    /**
     *  store vkit category
     */
    protected function store(Request $request){
        $v = Validator::make($request->all(), $this->validateVkitCategory);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::beginTransaction();
        try
        {
            $category = VkitCategory::addOrUpdateCategory($request);
            if(is_object($category)){
                DB::commit();
                return Redirect::to('admin/manageVkitCategory')->with('message', 'Category created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('admin/manageVkitCategory');
    }

    /**
     *  edit vkit category
     */
    protected function edit($id){
        $id = InputSanitise::inputInt(json_decode($id));
        if(isset($id)){
            $vkitCategory = VkitCategory::find($id);
            if(is_object($vkitCategory)){
                return view('vkitCategory.create', compact('vkitCategory'));
            }
        }
        return Redirect::to('admin/manageVkitCategory');
    }

    /**
     *  update vkit category
     */
    protected function update(Request $request){
        $categoryId = InputSanitise::inputInt($request->get('category_id'));
        if(isset($categoryId)){
            DB::beginTransaction();
            try
            {
                $category = VkitCategory::addOrUpdateCategory($request, true);
                if(is_object($category)){
                    DB::commit();
                    return Redirect::to('admin/manageVkitCategory')->with('message', 'Category updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return redirect()->back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('admin/manageVkitCategory');
    }

    /**
     *  delete vkit category
     */
    protected function delete(Request $request){
        $categoryId = InputSanitise::inputInt($request->get('category_id'));
        if(isset($categoryId)){
            $vkitCategory = VkitCategory::find($categoryId);
            if(is_object($vkitCategory)){
                DB::beginTransaction();
                try
                {
                    if(true == is_object($vkitCategory->projects) && false == $vkitCategory->projects->isEmpty()){
                        foreach($vkitCategory->projects as $project){
                            $project->deleteCommantsAndSubComments();
                            $project->deleteRegisteredProjects();
                            $project->deleteProjectImageFolder();
                            $project->delete();
                        }
                    }
                    $vkitCategory->delete();
                    Session::put('project_comment_area', 0);
                    DB::commit();
                    return Redirect::to('admin/manageVkitCategory')->with('message', 'Category deleted successfully!');
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return redirect()->back()->withErrors('something went wrong.');
                }
            }
        }
        return Redirect::to('admin/manageVkitCategory');
    }
}