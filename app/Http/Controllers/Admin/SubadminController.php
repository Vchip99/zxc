<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use App\Models\Admin;
use App\Models\SubDomainHome;
use Validator, Session, Auth, DB;
use Illuminate\Support\Facades\Route;

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
        $subadmins = Admin::paginate();
        return view('subadmin.list', compact('subadmins'));
    }

    /**
     * show create admin UI with all permissions
     */
    protected function create(){
        $subadminPermissions = [];
        $subadmin = new Admin;
        $allPermissions = Admin::getAllPermissions();
        return view('subadmin.create', compact('subadmin', 'allPermissions', 'subadminPermissions'));
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
        $subadminPermissions = [];
        if(isset($id)){
            $subadmin = Admin::find($id);
            if(is_object($subadmin)){
                $allPermissions = Admin::getAllPermissions();
                $subadminPermissionsArray = Admin::getSubAdminPermissions($id);
                foreach($subadminPermissionsArray as $subadminPermission){
                    $subadminPermissions[] =  $subadminPermission['id'];
                }
                return view('subadmin.create', compact('subadmin','allPermissions', 'subadminPermissions'));
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


    protected function showSubAdminHome(){
        $subdomain = SubDomainHome::where('subdomain', 'vchip')->first();
        return view('subDomain.home', compact('subdomain'));
    }

    protected function updateSubAdminHome(Request $request){
        SubDomainHome::addSubDomainHome($request);
        return Redirect::to('admin/manageSubAdminHome');
    }
}