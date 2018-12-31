<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB, Auth,File;
use App\Libraries\InputSanitise;

class ClientLoginActivity extends Model
{
    protected $connection = 'mysql2';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['client_id','session_id','login_time','logout_time'];

    /**
     *  add/update
     */
    protected static function addOrUpdateClientLoginActivity($sessionId, $isUpdate = false){
    	$loginUser = Auth::guard('client')->user();

    	if(true == $isUpdate){
    		$activity = static::where('session_id',$sessionId)->where('client_id',$loginUser->id)->first();
    		$activity->logout_time = date('Y-m-d H:i:s');
    	} else {
        	$activity = new static;
        	$activity->login_time = date('Y-m-d H:i:s');
    	}
        $activity->client_id = $loginUser->id;
        $activity->session_id = $sessionId;
        $activity->save();
        return $activity;
    }

    protected static function deleteClientLoginActivitiesByClientId($clientId){
        $activities = static::where('client_id', $clientId)->get();
        if(is_object($activities) && false == $activities->isEmpty()){
            foreach($activities as $activity){
                $activity->delete();
            }
        }
        return;
    }
}
