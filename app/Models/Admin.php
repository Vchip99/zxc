<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use GeniusTS\Roles\Traits\HasRoleAndPermission;
use GeniusTS\Roles\Contracts\HasRoleAndPermission as HasRoleAndPermissionContract;
use GeniusTS\Roles\Models\Role;
use GeniusTS\Roles\Models\Permission;
use Illuminate\Http\Request;
use DB;

class Admin extends Authenticatable
{
     use Notifiable,HasRoleAndPermission;

     /*
     * Role profile to get value from ntrust config file.
     */
    protected static $roleProfile = 'admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','email', 'password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     *  create/update sub admin and assingn permissions
     */
    protected static function createOrUpdateSubAdmin(Request $request, $isUpdate = false){
        $name = $request->get('name');
        $email = $request->get('email');
        $password = $request->get('password');
        $subadminId = $request->get('subadmin_id');
        if($isUpdate && !empty($subadminId)){
            $subadmin = Admin::find($subadminId);
            if(!empty($password)){
                $subadmin->password = bcrypt($password);
            }
            if(!is_object($subadmin)){
                return 'false';
            }
        } else {
            $subadmin = new Admin;
            $subadmin->password = bcrypt($password);
        }
        $subadmin->name = $name;
        $subadmin->email = $email;
        $subadmin->save();
        $subadmin->attachRole(2);
        return $subadmin;
    }

    /**
     *  return all permissions
     */
    protected static function getAllPermissions(){
        return DB::table('permissions')->get();
    }

    /**
     *  return permissions by sub admin Id
     */
    protected static function getSubAdminPermissions($id){
        return Permission::join('admin_permission', 'admin_permission.permission_id', '=', 'permissions.id')
                ->where('admin_permission.admin_id', $id)
                ->select('permissions.id')
                ->get()->toArray();
    }

    protected static function getSubAdmins(){
        return static::where('id','!=',1)->get();
    }

    protected static function getSubAdminsWithPagination(){
        return static::where('id','!=',1)->paginate();
    }
}
