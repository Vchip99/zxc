<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use DB;

class RegisterProject extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'project_id'];

    protected static function addFavouriteProject(Request $request){
    	$userId = $request->get('user_id');
    	$projectId = $request->get('project_id');
    	if(isset($userId) && isset($projectId)){
    		$registeredProject = static::firstOrNew(['user_id' => $userId, 'project_id' => $projectId]);
    		if(is_object($registeredProject) && empty($registeredProject->id)){
    			$registeredProject->save();
                return 'true';
    		} else {
                $registeredProject->delete();
                return 'false';
            }
    	}
    }

    protected static function getRegisteredProjectsByUserId($userId){
    	return DB::table('vkit_projects')
    			->join('register_projects', 'register_projects.project_id', '=', 'vkit_projects.id')
                ->where('vkit_projects.created_for', 1)
                ->where('register_projects.user_id', $userId)
    			->select('vkit_projects.*')
    			->get();
    }

    protected static function getRegisteredCategoriesByUserId($userId){
        return DB::table('vkit_projects')
                ->join('register_projects', 'register_projects.project_id', '=', 'vkit_projects.id')
                ->join('vkit_categories', 'vkit_categories.id', '=', 'vkit_projects.category_id')
                ->where('register_projects.user_id', $userId)
                ->select('vkit_categories.id', 'vkit_categories.name')
                ->groupBy('vkit_categories.id')
                ->get();
    }

    protected static function getRegisteredVkitProjectsByUserId($userId){
        return static::where('user_id', $userId)->get();
    }

    protected static function deleteRegisteredVkitProjectsByUserId($userId){
        $projects = static::where('user_id', $userId)->get();
        if(is_object($projects) && false == $projects->isEmpty()){
            foreach($projects as $project){
                $project->delete();
            }
        }
        return;
    }
}
