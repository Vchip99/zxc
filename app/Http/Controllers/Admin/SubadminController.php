<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use App\Models\Admin;
use App\Models\SubDomainHome;
use App\Models\CourseCourse;
use App\Models\TestSubCategory;
use App\Models\StudyMaterialSubject;
use App\Models\VkitProject;
use Validator, Session, Auth, DB;
use Illuminate\Support\Facades\Route;
use App\Libraries\InputSanitise;

class SubadminController extends Controller
{
    /**
     * check user is admin or not, if not then redirect to admin/home
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
    protected $validateCreateSubAdmin = [
        'name' => 'required|max:255',
        'email' => 'required|email|max:255|unique:admins',
        'password' => 'required',
        'confirm_password' => 'required|same:password'
    ];

    protected $validateUpdateSubAdmin = [
        'name' => 'required|max:255',
        'email' => 'required|email|max:255'
    ];

    /**
     * show all admins
     */
    protected function show(){
        $subadmins = Admin::getSubAdminsWithPagination();
        return view('subadmin.list', compact('subadmins'));
    }

    /**
     * show create admin UI with all permissions
     */
    protected function create(){
        $subadmin = new Admin;
        return view('subadmin.create', compact('subadmin'));
    }

    /**
     * store admin with assigned permissions
     */
    protected function store(Request $request){
        $v = Validator::make($request->all(), $this->validateCreateSubAdmin);
        if($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::beginTransaction();
        try
        {
            $subadmin = Admin::createOrUpdateSubAdmin($request);
            if(is_object($subadmin)){
                DB::commit();
                return Redirect::to('admin/manageSubadminUser')->with('message', 'Sub Admin create successfully.');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('admin/manageSubadminUser');
    }

    /**
     * edit admin with assigned permissions
     */
    protected function edit($id){
        $id = json_decode($id);
        if(isset($id)){
            $subadmin = Admin::find($id);
            if(is_object($subadmin)){
                return view('subadmin.create', compact('subadmin'));
            }
        }
        return Redirect::to('admin/manageSubadminUser');
    }

    /**
     * update admin with assigned permissions
     */
    protected function update(Request $request){
        $v = Validator::make($request->all(), $this->validateUpdateSubAdmin);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        $subadminId = strip_tags(trim($request->get('subadmin_id')));
        if(isset($subadminId)){
            DB::beginTransaction();
            try
            {
                $subadmin = Admin::createOrUpdateSubAdmin($request, true);
                if(is_object($subadmin)){
                    DB::commit();
                    return Redirect::to('admin/manageSubadminUser')->with('message', 'Sub Admin updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return redirect()->back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('admin/manageSubadminUser');
    }

    /**
     *  delete sub admin
     */
    protected function delete(Request $request){
        InputSanitise::deleteCacheByString('vchip:courses*');
        InputSanitise::deleteCacheByString('vchip:tests*');
        InputSanitise::deleteCacheByString('vchip:studyMaterial*');
        InputSanitise::deleteCacheByString('vchip:projects*');
        $adminId = InputSanitise::inputInt($request->get('subadmin_id'));
        if(isset($adminId)){
            $admin = Admin::find($adminId);
            if(is_object($admin) && 1 != $admin->id){
                DB::beginTransaction();
                try
                {
                    // delete courses,videos and purchased courses
                    CourseCourse::deleteSubAdminCoursesAndCourseVideosByAdminId($admin->id);
                    // delete sub category, subjects, papers, user purchased paper/test,delete questions
                    TestSubCategory::deleteSubAdminSubCategoriesAndSubjectsAndPapersAndQuestionsByAdminId($admin->id);
                    // delete study material subjects,topics,comments ( compelete comment module)
                    StudyMaterialSubject::deleteSubAdminStudyMaterialSubjectsByAdminId($admin->id);
                    // delete vkit projects && purchased items(vkit) - from user purchased table
                    VkitProject::deleteSubAdminProjectsByAdminId($admin->id);
                    $admin->delete();
                    DB::commit();
                    return Redirect::to('admin/manageSubadminUser')->with('message', 'Sub Admin deleted successfully!');
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return redirect()->back()->withErrors('something went wrong.');
                }
            }
        }
        return Redirect::to('admin/manageSubadminUser');
    }

    protected function showSubAdminHome(){
        $subdomain = SubDomainHome::where('subdomain', 'vchip')->first();
        return view('subDomain.home', compact('subdomain'));
    }

    protected function updateSubAdminHome(Request $request){
        SubDomainHome::addSubDomainHome($request);
        return Redirect::to('admin/manageSubAdminHome');
    }
}