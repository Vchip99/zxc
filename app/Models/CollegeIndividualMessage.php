<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB, Auth,File;
use App\Libraries\InputSanitise;

class CollegeIndividualMessage extends Model
{
      /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['college_id','college_dept_id','year','messages','created_by'];

    /**
     *  add/update message
     */
    protected static function addIndividualMessage($allMessagesString,$collegeDeptId,$year){
    	$loginUser = Auth::user();

        $message = new static;
        $message->college_id = $loginUser->college_id;
        $message->college_dept_id = $collegeDeptId;
        $message->year = $year;
        $message->messages = $allMessagesString;
        $message->created_by = $loginUser->id;
        $message->save();
        return $message;
    }

    protected static function getIndividualMessagesByCollegeIdByDate($collegeId,$date){
        return static::where('college_id', $collegeId)->whereDate('created_at', $date)->select('*')->orderBy('created_at','desc')->get();
    }

    protected static function getIndividualMessagesByCollegeIdByDeptIdByYear($collegeId,$deptId,$year){
        return static::where('college_id', $collegeId)->where('college_dept_id', $deptId)->where('year', $year)->select('*')->orderBy('id','desc')->get();
    }
}
