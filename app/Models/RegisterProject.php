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
    protected $fillable = ['user_id', 'project_id','payment_id','payment_request_id','price'];

    protected static function addFavouriteProject(Request $request){
    	$userId = $request->get('user_id');
    	$projectId = $request->get('project_id');
    	if(isset($userId) && isset($projectId)){
    		$registeredProject = static::firstOrNew(['user_id' => $userId, 'project_id' => $projectId]);
    		if(is_object($registeredProject) && empty($registeredProject->id)){
    			$registeredProject->save();
                return 'true';
    		} else {
                if(empty($registeredProject->payment_id) && empty($registeredProject->payment_request_id) && empty($registeredProject->price)){
                    $registeredProject->delete();
                    return 'false';
                }
            }
    	}
        return 'true';
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

    protected static function getRegisteredVkitProjectByUserIdByProjectId($userId,$projectId){
        return static::where('user_id', $userId)->where('project_id',$projectId)->first();
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

    protected static function addPurchasedProject($paymentArray){
        $purchasedProject = static::firstOrNew(['user_id' => $paymentArray['user_id'], 'project_id' => $paymentArray['project_id']]);
        if(is_object($purchasedProject) && empty($purchasedProject->id)){
            $purchasedProject->payment_id = $paymentArray['payment_id'];
            $purchasedProject->payment_request_id = $paymentArray['payment_request_id'];
            $purchasedProject->price = $paymentArray['price'];
            $purchasedProject->save();
        } else {
            $purchasedProject->payment_id = $paymentArray['payment_id'];
            $purchasedProject->payment_request_id = $paymentArray['payment_request_id'];
            $purchasedProject->price = $paymentArray['price'];
            $purchasedProject->save();
        }
        return $purchasedProject;
    }

    protected static function getPurchasedProjectsByUserIdForPayments($userId){
        return static::join('vkit_projects','vkit_projects.id','=','register_projects.project_id')
            ->whereNotNull('register_projects.payment_id')
            ->whereNotNull('register_projects.payment_request_id')
            ->where('register_projects.price', '>', 0)
            ->whereNotNull('register_projects.payment_id')
            ->whereNotNull('register_projects.payment_request_id')
            ->where('register_projects.user_id', $userId)
            ->select('register_projects.id','register_projects.updated_at','register_projects.price','vkit_projects.name')
            ->groupBy('register_projects.id')->get();
    }
}
