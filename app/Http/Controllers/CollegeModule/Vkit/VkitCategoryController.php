<?php

namespace App\Http\Controllers\CollegeModule\Vkit;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use App\Models\VkitCategory;
use App\Models\User;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;

class VkitCategoryController extends Controller
{
    /**
     *  check admin have permission or not, if not redirect to home
     */
	public function __construct() {
        $this->middleware(function ($request, $next) {
            $loginUser = Auth::guard('web')->user();
            if(is_object($loginUser) && (User::Lecturer == $loginUser->user_type || User::Hod == $loginUser->user_type)){
                return $next($request);
            }
            return Redirect::to('/');
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
    protected function show($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $loginUser = Auth::guard('web')->user();
    	$vkitCategories = VkitCategory::getProjectCategoriesByCollegeIdByDeptIdWithPagination($loginUser->college_id);
    	return view('collegeModule.vkit.vkitCategory.list', compact('vkitCategories'));
    }

    /**
     *  show create vkit category UI
     */
    protected function create($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $vkitCategory = new VkitCategory;
        return view('collegeModule.vkit.vkitCategory.create', compact('vkitCategory'));
    }

    /**
     *  store vkit category
     */
    protected function store($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $v = Validator::make($request->all(), $this->validateVkitCategory);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        // InputSanitise::deleteCacheByString('vchip:projects*');
        DB::beginTransaction();
        try
        {
            $category = VkitCategory::addOrUpdateCategory($request);
            if(is_object($category)){
                DB::commit();
                return Redirect::to('college/'.$collegeUrl.'/manageVkitCategory')->with('message', 'Category created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('college/'.$collegeUrl.'/manageVkitCategory');
    }

    /**
     *  edit vkit category
     */
    protected function edit($collegeUrl,$id,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $id = InputSanitise::inputInt(json_decode($id));
        if(isset($id)){
            $vkitCategory = VkitCategory::find($id);
            if(is_object($vkitCategory)){
                $loginUser = Auth::guard('web')->user();
                if(is_object($loginUser) && $vkitCategory->college_id == $loginUser->college_id && $vkitCategory->user_id == $loginUser->id){
                    return view('collegeModule.vkit.vkitCategory.create', compact('vkitCategory'));
                }
            }
        }
        return Redirect::to('college/'.$collegeUrl.'/manageVkitCategory');
    }

    /**
     *  update vkit category
     */
    protected function update($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        // InputSanitise::deleteCacheByString('vchip:projects*');
        $categoryId = InputSanitise::inputInt($request->get('category_id'));
        if(isset($categoryId)){
            DB::beginTransaction();
            try
            {
                $category = VkitCategory::addOrUpdateCategory($request, true);
                if(is_object($category)){
                    DB::commit();
                    return Redirect::to('college/'.$collegeUrl.'/manageVkitCategory')->with('message', 'Category updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return redirect()->back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('college/'.$collegeUrl.'/manageVkitCategory');
    }

    /**
     *  delete vkit category
     */
    protected function delete($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        // InputSanitise::deleteCacheByString('vchip:projects*');
        $categoryId = InputSanitise::inputInt($request->get('category_id'));
        if(isset($categoryId)){
            $vkitCategory = VkitCategory::find($categoryId);
            if(is_object($vkitCategory)){
                DB::beginTransaction();
                try
                {
                    $loginUser = Auth::guard('web')->user();
                    if(is_object($loginUser) && $vkitCategory->college_id == $loginUser->college_id && $vkitCategory->user_id == $loginUser->id){
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
                        return Redirect::to('college/'.$collegeUrl.'/manageVkitCategory')->with('message', 'Category deleted successfully!');
                    }
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return redirect()->back()->withErrors('something went wrong.');
                }
            }
        }
        return Redirect::to('college/'.$collegeUrl.'/manageVkitCategory');
    }

    protected function isVkitCategoryExist(Request $request){
        return VkitCategory::isVkitCategoryExist($request);
    }
}