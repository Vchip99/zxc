<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB, Auth;
use App\Libraries\InputSanitise;

class ClientAssignmentSubject extends Model
{
    protected $connection = 'mysql2';
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'client_id'];

    /**
     *  add/update course category
     */
    protected static function addOrUpdateAssignmentSubject( Request $request, $isUpdate=false){
        $subjectName = InputSanitise::inputString($request->get('subject'));
        $subjectId   = InputSanitise::inputInt($request->get('subject_id'));

        if( $isUpdate && isset($subjectId)){
            $subject = static::find($subjectId);
            if(!is_object($subject)){
            	return Redirect::to('manageAssignmentSubject');
            }
        } else{
            $subject = new static;
        }
        $subject->name = $subjectName;
        $subject->client_id = Auth::guard('client')->user()->id;
        $subject->save();
        return $subject;
    }

    protected static function getAssignmentSubjectsByClient(){
        if(is_object(Auth::guard('client')->user())){
    	   return static::where('client_id', Auth::guard('client')->user()->id)->get();
        } else {
            return static::where('client_id', Auth::guard('clientuser')->user()->client_id)->get();
        }
    }
}
