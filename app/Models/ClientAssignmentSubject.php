<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB, Auth;
use App\Libraries\InputSanitise;
use App\Models\ClientBatch;

class ClientAssignmentSubject extends Model
{
    protected $connection = 'mysql2';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'client_id' ,'client_batch_id','created_by'];

    /**
     *  add/update assignment subject
     */
    protected static function addOrUpdateAssignmentSubject( Request $request, $isUpdate=false){

        $subjectName = InputSanitise::inputString($request->get('subject'));
        $subjectId   = InputSanitise::inputInt($request->get('subject_id'));
        $clientBatchId = InputSanitise::inputInt($request->get('batch'));
        $resultArr = InputSanitise::getClientIdAndCretedBy();
        $clientId = $resultArr[0];
        $createdBy = $resultArr[1];
        if( $isUpdate && isset($subjectId)){
            $subject = static::find($subjectId);
            if(!is_object($subject)){
            	return 'false';
            }
        } else{
            $subject = new static;
        }
        $subject->name = $subjectName;
        $subject->client_id = $clientId;
        $subject->client_batch_id = $clientBatchId;
        $subject->created_by = $createdBy;
        $subject->save();
        return $subject;
    }

    protected static function getAssignmentSubjectsByClient(){
        $loginClient = Auth::guard('client')->user();
        if(is_object($loginClient)){
    	   return static::where('client_id', $loginClient->id)->get();
        } else {
            return static::where('client_id', Auth::guard('clientuser')->user()->client_id)->get();
        }
    }

    protected static function deleteClientAssignmentSubjectByClientId($clientId){
        $subjects = static::where('client_id', $clientId)->get();
        if(is_object($subjects) && false == $subjects->isEmpty()){
            foreach($subjects as $subject){
                $subject->delete();
            }
        }
        return;
    }

    public function batch(){
        return $this->belongsTo(ClientBatch::class, 'client_batch_id');
    }

    protected static function getAssignmentSubjectsByBatchId($clientBatchId){
        $resultArr = InputSanitise::getClientIdAndCretedBy();
        $clientId = $resultArr[0];
        if($clientBatchId > 0){
            return static::where('client_id', $clientId)->where('client_batch_id', $clientBatchId)->get();
        } else {
            return static::where('client_id', $clientId)->where('client_batch_id','<=',0)->get();
        }
    }

    protected static function deleteAssignmentSubjectsByBatchIdByClientId($clientBatchId,$clientId){
        return static::where('client_batch_id', $clientBatchId)->where('client_id', $clientId)->delete();
    }

    protected static function assignClientAssignmentSubjectsToClientByClientIdByTeacherId($clientId,$teacherId){
        $subjects = static::where('client_id', $clientId)->where('created_by', $teacherId)->get();
        if(is_object($subjects) && false == $subjects->isEmpty()){
            foreach($subjects as $subject){
                $subject->created_by = 0;
                $subject->save();
            }
        }
    }
}
