<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB, Auth;
use App\Libraries\InputSanitise;

class ClientBatch extends Model
{
    protected $connection = 'mysql2';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'client_id', 'student_ids','created_by'];

    /**
     *  add/update batch
     */
    protected static function addOrUpdateClientBatch( Request $request, $isUpdate=false){
        $batchName = InputSanitise::inputString($request->get('name'));
        $batchId   = InputSanitise::inputInt($request->get('batch_id'));
        $resultArr = InputSanitise::getClientIdAndCretedBy();
        $clientId = $resultArr[0];
        $createdBy = $resultArr[1];
        if( $isUpdate && isset($batchId)){
            $batch = static::find($batchId);
            if(!is_object($batch)){
            	return 'false';
            }
        } else{
            $batch = new static;
        }
        $batch->name = $batchName;
        $batch->client_id = $clientId;
        $batch->student_ids = ' ';
        $batch->created_by = $createdBy;
        $batch->save();
        return $batch;
    }

    protected static function getBatchesByClientId($clientId){
    	return static::where('client_id', $clientId)->get();
    }

    protected static function associateBatchStudents(Request $request){
    	$batchId   = InputSanitise::inputInt($request->get('batch'));
        if(!empty($request->get('students'))){
    	   $students = implode(',', $request->get('students'));
        } else {
            $students = '';
        }
        $resultArr = InputSanitise::getClientIdAndCretedBy();
        $clientId = $resultArr[0];
        $createdBy = $resultArr[1];
    	$batch = static::find($batchId);
    	if(is_object($batch)){
    		$batch->student_ids = $students;
            $batch->created_by = $createdBy;
    		$batch->save();
    	}
    	return $batch;
    }

    protected static function getBatchById($id){
    	return static::find($id);
    }

    protected static function assignClientBatchesToClientByClientIdByTeacherId($clientId,$teacherId){
        $batches = static::where('client_id', $clientId)->where('created_by', $teacherId)->get();
        if(is_object($batches) && false == $batches->isEmpty()){
            foreach($batches as $batch){
                $batch->created_by = 0;
                $batch->save();
            }
        }
    }

}
