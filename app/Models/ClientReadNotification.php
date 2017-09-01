<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use App\Models\ClientNotification;
use DB,Auth;

class ClientReadNotification extends Model
{
	protected $connection = 'mysql2';
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['client_notification_id', 'notification_module','created_module_id', 'client_id', 'client_user_id','created_at'];

    protected static function readNotificationByModuleByModuleIdByUser($notificationModuleId, $createdModuleId,$currentUser){
    	$readNotitication = static::where('notification_module',$notificationModuleId)->where('created_module_id', $createdModuleId)->where('client_id',Auth::guard('clientuser')->user()->client_id)->where('client_user_id', $currentUser)->first();

    	if(! is_object($readNotitication)){
    		$notitication = ClientNotification::where('notification_module',$notificationModuleId)->where('created_module_id', $createdModuleId)->where('client_id',Auth::guard('clientuser')->user()->client_id)->first();
    		if(is_object($notitication)){
    			$objReadNotification = new static;
    			$objReadNotification->client_notification_id = $notitication->id;
    			$objReadNotification->notification_module = $notificationModuleId;
    			$objReadNotification->created_module_id = $createdModuleId;
    			$objReadNotification->client_id = Auth::guard('clientuser')->user()->client_id;
    			$objReadNotification->client_user_id = $currentUser;
                $objReadNotification->created_at = $notitication->created_at;
    			$objReadNotification->save();
    			return $objReadNotification;
    		}
    	}
    	return 'false';
    }

    protected static function getReadNotificationIdsByUser($selectedYear=NULL,$selectedMonth=NULL){
    	$ids = [];
    	$resultQuery = static::where('client_id', Auth::guard('clientuser')->user()->client_id)
    				->where('client_user_id', Auth::guard('clientuser')->user()->id);
        if($selectedYear > 0){
            $resultQuery->whereYear('created_at', $selectedYear);
        }
        if($selectedMonth > 0){
            $resultQuery->whereMonth('created_at', $selectedMonth);
        }
		$results = $resultQuery->select('client_notification_id')->get();

    	if(is_object($results) && false == $results->isEmpty()){
    		foreach($results as $result){
    			$ids[] = $result->client_notification_id;
    		}
    	}
    	return $ids;
    }
}