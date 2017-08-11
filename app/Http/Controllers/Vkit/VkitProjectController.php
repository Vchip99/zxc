<?php

namespace App\Http\Controllers\Vkit;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use App\Models\VkitProject;
use App\Models\VkitCategory;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;

class VkitProjectController extends Controller
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
    protected $validateVkitProject = [
        'project' => 'required',
        'author' => 'required',
        'introduction' => 'required',
        'gateway' => 'required',
        'microcontroller' => 'required',
        'front_image' => 'required',
        'header_image' => 'required',
        'pdf' => 'required',
        'date' => 'required',
        'description' => 'required'
    ];

    protected $validateUpdateVkitProject = [
        'project' => 'required',
        'author' => 'required',
        'introduction' => 'required',
        'gateway' => 'required',
        'microcontroller' => 'required',
        'date' => 'required',
        'description' => 'required'
    ];

    /**
     *  show list of vkit projects
     */
    protected function show(){
        $projects = VkitProject::paginate();
        return view('vkitProject.list', compact('projects'));
    }

    /**
     *  show create vkit project UI
     */
    protected function create(){
        $project = new VkitProject;
        $vkitCategories = VkitCategory::all();
        return view('vkitProject.create', compact('project', 'vkitCategories'));
    }

    /**
     *  store vkit project
     */
    protected function store(Request $request){
        $v = Validator::make($request->all(), $this->validateVkitProject);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::beginTransaction();
        try
        {
            $project = VkitProject::addOrUpdateProject($request);
            if(is_object($project)){
                DB::commit();
                return Redirect::to('admin/manageVkitProject')->with('message', 'Project created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('admin/manageVkitProject');
    }

    /**
     *  edit vkit project
     */
    protected function edit($id){
        $id = InputSanitise::inputInt(json_decode($id));
        if(isset($id)){
            $project = VkitProject::find($id);
            $vkitCategories = VkitCategory::all();
            return view('vkitProject.create', compact('project', 'vkitCategories'));
        }
    }

    /**
     *  update vkit project
     */
    protected function update(Request $request){
        $v = Validator::make($request->all(), $this->validateUpdateVkitProject);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        $projectId = InputSanitise::inputInt($request->get('project_id'));
        if(isset($projectId)){
            DB::beginTransaction();
            try
            {
                $project = VkitProject::addOrUpdateProject($request, true);
                if(is_object($project)){
                    DB::commit();
                    return Redirect::to('admin/manageVkitProject')->with('message', 'Project updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return redirect()->back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('admin/manageVkitProject');
    }

    /**
     *  delete vkit project
     */
    protected function delete(Request $request){
        $arrPostIds = [];
        $projectId = InputSanitise::inputInt($request->get('project_id'));
        if(isset($projectId)){
            $project = VkitProject::find($projectId);
            if(is_object($project)){
                DB::beginTransaction();
                try
                {
                    $project->deleteCommantsAndSubComments();
                    $project->deleteRegisteredProjects();
                    $project->deleteProjectImageFolder();
                    $project->delete();
                    DB::commit();
                    Session::put('project_comment_area', 0);
                    return Redirect::to('admin/manageVkitProject')->with('message', 'Project deletedd successfully!');
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return redirect()->back()->withErrors('something went wrong.');
                }
            }
        }
        return Redirect::to('admin/manageVkitProject');
    }
}