<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB, Auth;
use App\Libraries\InputSanitise;
use App\Models\ClientBatch;

class ClientUserAttendance extends Model
{
    protected $connection = 'mysql2';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['attendance_date', 'client_batch_id', 'student_ids', 'client_id'];

    /**
     *  add/update attendance
     */
    protected static function addOrUpdateClientUserAttendance(Request $request){
        $date = InputSanitise::inputString($request->get('attendance_date'));
        $batchId   = InputSanitise::inputInt($request->get('batch'));
        if($request->get('students')){
            $students = $request->get('students');
        } else {
            $students = [];
        }
        if($request->get('all_users')){
            $allUsers = explode(',', $request->get('all_users'));
        } else {
            $allUsers = [];
        }

        $markAttendance = InputSanitise::inputInt($request->get('mark_attendance'));

        $attendance = static::where('attendance_date', $date)->where('client_batch_id', $batchId)->first();
        if(!is_object($attendance)){
            $attendance = new static;
        }
        $attendance->attendance_date = $date;
        $attendance->client_batch_id = $batchId;
        $attendance->client_id = Auth::guard('client')->user()->id;
        if(1 == $markAttendance){
        	$attendance->student_ids = implode(',',$students);
        } else {
            if(count(array_diff($allUsers, $students)) > 0){
        	   $attendance->student_ids = implode(',', array_diff($allUsers, $students));
            } else {
                $attendance->student_ids = '';
            }
        }
        $attendance->save();
        return $attendance;
    }

    protected static function getBatchStudentAttendancebyBatchId(Request $request){
    	$date = InputSanitise::inputString($request->get('attendance_date'));
        $batchId   = InputSanitise::inputInt($request->get('batch_id'));
    	return static::where('attendance_date', $date)->where('client_batch_id', $batchId)->first();
    }

    protected static function deleteAttendanceByBtachIdByClientId($batchId,$clientId){
        return static::where('client_batch_id', $batchId)->where('client_id', $clientId)->delete();
    }

    public function batch(){
        return $this->belongsTo(ClientBatch::class, 'client_batch_id');
    }
}
