<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use App\Models\ReadNotification;
use DB,Auth;

class Notification extends Model
{
    public $timestamps = false;

    /**
     * Notification modules
     */
    const USERCOURSENOTIFICATION = 1;
    const USERLIVECOURSENOTIFICATION = 2;
    const USERVKITPROJECTNOTIFICATION = 3;
    const USERDISCUSSIONCOMMENTNOTIFICATION = 4;
    const USERDISCUSSIONSUBCOMMENTNOTIFICATION = 5;
    const USERBLOGNOTIFICATION = 6;
    const ADMINCOURSEVIDEO = 7;
    const ADMINLIVECOURSEVIDEO = 8;
    const ADMINVKITPROJECT = 9;
    const ADMINDOCUMENT = 10;
    const ADMINBLOG = 11;
    const ADMINZEROTOHERO = 12;
    const ADMINPAPER = 13;
    const ADMINCOMPANYJOB = 14;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['message', 'admin_id', 'notification_module','created_module_id', 'created_by', 'created_to', 'is_seen', 'created_at'];

    protected static function addNotification($notificationMessage, $notificationModule, $createdModuleId){
    	$notification = new static;
    	$notification->message = $notificationMessage;
    	$notification->admin_id = Auth::guard('admin')->user()->id;
        $notification->notification_module = $notificationModule;
        $notification->created_module_id = $createdModuleId;
    	$notification->created_by = 0;
    	$notification->created_to = 0;
        $notification->is_seen = 0;
        $notification->created_at = date('Y-m-d');
    	$notification->save();
    	return $notification;
    }

    protected static function addCommentNotification($notificationMessage, $notificationModule, $createdModuleId, $createdBy,$createdTo){
        $notification = new static;
        $notification->message = $notificationMessage;
        $notification->admin_id = 0;
        $notification->notification_module = $notificationModule;
        $notification->created_module_id = $createdModuleId;
        $notification->created_by = $createdBy;
        $notification->created_to = $createdTo;
        $notification->is_seen = 0;
        $notification->created_at = date('Y-m-d');
        $notification->save();
    }

    protected static function readUserNotifications($userId){
        return static::where('created_to', $userId)->where('is_seen', 0)->update(['is_seen' => 1]);
    }
}
