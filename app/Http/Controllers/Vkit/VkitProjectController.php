<?php

namespace App\Http\Controllers\Vkit;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use App\Models\VkitProject;
use App\Models\VkitCategory;
use App\Models\Notification;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;
use App\Mail\MailToSubscribedUser;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

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
        $projects = VkitProject::getVkitProjectsWithPagination();
        return view('vkitProject.list', compact('projects'));
    }

    /**
     *  show create vkit project UI
     */
    protected function create(){
        $project = new VkitProject;
        $vkitCategories = VkitCategory::getProjectCategories();
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
        InputSanitise::deleteCacheByString('vchip:projects*');
        DB::beginTransaction();
        try
        {
            $project = VkitProject::addOrUpdateProject($request);
            if(is_object($project)){
                $messageBody = '';
                $notificationMessage = 'A new project: <a href="'.$request->root().'/vkitproject/'.$project->id.'" target="_blank">'.$project->name.'</a> has been added.';
                Notification::addNotification($notificationMessage, Notification::ADMINVKITPROJECT, $project->id);
                DB::commit();
                // $subscriedUsers = User::where('admin_approve', 1)->where('verified', 1)->select('email')->get();
                // $allUsers = $subscriedUsers->chunk(100);
                // set_time_limit(0);
                // if(count($allUsers) > 0){
                //     foreach($allUsers as $selectedUsers){
                //         $messageBody .= '<p> Dear User</p>';
                //         $messageBody .= '<p>'.$notificationMessage.' please have a look once.</p>';
                //         $messageBody .= '<p><b> Thanks and Regard, </b></p>';
                //         $messageBody .= '<b><a href="https://vchiptech.com"> Vchip Technology Team </a></b><br/>';
                //         $messageBody .= '<b> More about us... </b><br/>';
                //         $messageBody .= '<b><a href="https://vchipedu.com"> Digital Education </a></b><br/>';
                //         $messageBody .= '<b><a href="mailto:info@vchiptech.com" target="_blank">E-mail</a></b><br/>';
                //         $mailSubject = 'Vchipedu added a new project';
                //         Mail::bcc($selectedUsers)->queue(new MailToSubscribedUser($messageBody, $mailSubject));
                //     }
                // }
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
            if(is_object($project)){
                $vkitCategories = VkitCategory::getProjectCategories();
                return view('vkitProject.create', compact('project', 'vkitCategories'));
            }
        }
        return Redirect::to('admin/manageVkitProject');
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
        InputSanitise::deleteCacheByString('vchip:projects*');
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
        InputSanitise::deleteCacheByString('vchip:projects*');
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

    protected function isVkitProjectExist(Request $request){
        return VkitProject::isVkitProjectExist($request);
    }
}