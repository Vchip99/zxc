<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use App\Models\Notification;
use DB,Auth;

class ReadNotification extends Model
{
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['notification_id', 'notification_module','created_module_id', 'user_id', 'created_at'];

    protected static function readNotificationByModuleByModuleIdByUser($notificationModuleId, $createdModuleId,$currentUser){
    	$readNotitication = static::where('notification_module',$notificationModuleId)->where('created_module_id', $createdModuleId)->where('user_id', $currentUser)->first();
    	if(! is_object($readNotitication)){
    		$notitication = Notification::where('notification_module',$notificationModuleId)->where('created_module_id', $createdModuleId)->first();
    		if(is_object($notitication)){
    			$objReadNotification = new static;
    			$objReadNotification->notification_id = $notitication->id;
    			$objReadNotification->notification_module = $notificationModuleId;
    			$objReadNotification->created_module_id = $createdModuleId;
    			$objReadNotification->user_id = $currentUser;
                $objReadNotification->created_at =$notitication->created_at;
    			$objReadNotification->save();
    			return $objReadNotification;
    		}
    	}
    	return 'false';
    }

    protected static function getReadNotificationIdsByUser($selectedYear=NULL,$selectedMonth=NULL){
    	$ids = [];
    	$resultQuery = static::where('user_id', Auth::user()->id);
        if($selectedYear > 0){
            $resultQuery->whereYear('created_at', $selectedYear);
        }
        if($selectedMonth > 0){
            $resultQuery->whereMonth('created_at', $selectedMonth);
        }
        $results = $resultQuery->select('notification_id')->get();
    	if(is_object($results) && false == $results->isEmpty()){
    		foreach($results as $result){
    			$ids[] = $result->notification_id;
    		}
    	}
    	return $ids;
    }
}
