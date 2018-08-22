<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use DB,Auth;

class ClientNotification extends Model
{
	protected $connection = 'mysql2';
    public $timestamps = false;

    /**
     * Notification modules
     */
    const CLIENTCOURSEVIDEO = 1;
    const CLIENTPAPER = 2;
    const USERCOURSEVIDEONOTIFICATION = 3;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['message', 'client_id', 'notification_module','created_module_id', 'created_by', 'created_to', 'is_seen', 'created_at'];

    protected static function addNotification($notificationMessage, $notificationModule, $createdModuleId){
        $resultArr = InputSanitise::getClientIdAndCretedBy();
        $clientId = $resultArr[0];
    	$notification = new static;
    	$notification->message = $notificationMessage;
    	$notification->client_id = $clientId;
        $notification->notification_module = $notificationModule;
        $notification->created_module_id = $createdModuleId;
    	$notification->created_by = 0;
    	$notification->created_to = 0;
        $notification->created_at = date('Y-m-d');
        $notification->is_seen = 0;
    	$notification->save();
    	return $notification;
    }

    protected static function addCommentNotification($notificationMessage, $notificationModule, $createdModuleId, $createdBy,$createdTo){
        $notification = new static;
        $notification->message = $notificationMessage;
        $notification->client_id = Auth::guard('clientuser')->user()->client_id;;
        $notification->notification_module = $notificationModule;
        $notification->created_module_id = $createdModuleId;
        $notification->created_by = $createdBy;
        $notification->created_to = $createdTo;
        $notification->created_at = date('Y-m-d');
        $notification->is_seen = 0;
        $notification->save();
    }

    protected static function readUserNotifications($userId){
        return static::where('client_id', Auth::guard('clientuser')->user()->client_id)->where('created_to', $userId)->where('is_seen', 0)->update(['is_seen' => 1]);
    }

    protected static function deleteClientNotification($clientId){
        $results = static::where('client_id', $clientId)->get();
        if(is_object($results) && false == $results->isEmpty()){
            foreach($results as $result){
                $result->delete();
            }
        }
        return;
    }
}
