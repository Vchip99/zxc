<?php

namespace App\Http\Controllers\CollegeModule\Vkit;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use App\Models\VkitProject;
use App\Models\CollegeCategory;
use App\Models\Notification;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;
use App\Mail\MailToSubscribedUser;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class VkitProjectController extends Controller
{
    /**
     *  check admin have permission or not, if not redirect to home
     */
    public function __construct() {
        $this->middleware(function ($request, $next) {
            $loginUser = Auth::guard('web')->user();
            if(is_object($loginUser)){
                return $next($request);
            }
            return Redirect::to('/');
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
    protected function show($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $loginUser = Auth::guard('web')->user();
        if(User::Hod == $loginUser->user_type || User::Lecturer == $loginUser->user_type){
            $projects = VkitProject::getVkitProjectsByCollegeIdByAssignedDeptsWithPagination($loginUser->college_id);
        } else {
            $projects = VkitProject::getVkitProjectsByCollegeIdByDeptIdWithPagination($loginUser->college_id);
        }

        return view('collegeModule.vkit.vkitProject.list', compact('projects'));
    }

    /**
     *  show create vkit project UI
     */
    protected function create($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $project = new VkitProject;
        $loginUser = Auth::guard('web')->user();
        $vkitCategories = CollegeCategory::getCollegeCategoriesByCollegeIdByDeptId($loginUser->college_id);
        return view('collegeModule.vkit.vkitProject.create', compact('project', 'vkitCategories'));
    }

    /**
     *  store vkit project
     */
    protected function store($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $v = Validator::make($request->all(), $this->validateVkitProject);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        // InputSanitise::deleteCacheByString('vchip:projects*');
        DB::beginTransaction();
        try
        {
            $project = VkitProject::addOrUpdateProject($request);
            if(is_object($project)){
                DB::commit();
                return Redirect::to('college/'.$collegeUrl.'/manageVkitProject')->with('message', 'Project created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('college/'.$collegeUrl.'/manageVkitProject');
    }

    /**
     *  edit vkit project
     */
    protected function edit($collegeUrl,$id,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $id = InputSanitise::inputInt(json_decode($id));
        if(isset($id)){
            $project = VkitProject::find($id);
            if(is_object($project)){
                $loginUser = Auth::guard('web')->user();
                if(is_object($loginUser) && ($project->created_by == $loginUser->id || (User::Hod ==  $loginUser->user_type || User::Directore ==  $loginUser->user_type))){
                    $vkitCategories = CollegeCategory::getCollegeCategoriesByCollegeIdByDeptId($loginUser->college_id);
                    return view('collegeModule.vkit.vkitProject.create', compact('project', 'vkitCategories'));
                }
            }
        }
        return Redirect::to('college/'.$collegeUrl.'/manageVkitProject');
    }

    /**
     *  update vkit project
     */
    protected function update($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $v = Validator::make($request->all(), $this->validateUpdateVkitProject);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        // InputSanitise::deleteCacheByString('vchip:projects*');
        $projectId = InputSanitise::inputInt($request->get('project_id'));
        if(isset($projectId)){
            DB::beginTransaction();
            try
            {
                $project = VkitProject::addOrUpdateProject($request, true);
                if(is_object($project)){
                    DB::commit();
                    return Redirect::to('college/'.$collegeUrl.'/manageVkitProject')->with('message', 'Project updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return redirect()->back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('college/'.$collegeUrl.'/manageVkitProject');
    }

    /**
     *  delete vkit project
     */
    protected function delete($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        // InputSanitise::deleteCacheByString('vchip:projects*');
        $projectId = InputSanitise::inputInt($request->get('project_id'));
        if(isset($projectId)){
            $project = VkitProject::find($projectId);
            if(is_object($project)){
                DB::beginTransaction();
                try
                {
                    $loginUser = Auth::guard('web')->user();
                    if(is_object($loginUser) && ($project->created_by == $loginUser->id || (User::Hod ==  $loginUser->user_type || User::Directore ==  $loginUser->user_type))){
                        $project->deleteCommantsAndSubComments();
                        $project->deleteRegisteredProjects();
                        $project->deleteProjectImageFolder();
                        $project->delete();
                        DB::commit();
                        Session::put('project_comment_area', 0);
                        return Redirect::to('college/'.$collegeUrl.'/manageVkitProject')->with('message', 'Project deletedd successfully!');
                    }
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return redirect()->back()->withErrors('something went wrong.');
                }
            }
        }
        return Redirect::to('college/'.$collegeUrl.'/manageVkitProject');
    }

    protected function isVkitProjectExist(Request $request){
        return VkitProject::isVkitProjectExist($request);
    }
}