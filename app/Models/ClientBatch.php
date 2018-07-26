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
    protected $fillable = ['name', 'client_id', 'student_ids'];

    /**
     *  add/update batch
     */
    protected static function addOrUpdateClientBatch( Request $request, $isUpdate=false){
        $batchName = InputSanitise::inputString($request->get('name'));
        $batchId   = InputSanitise::inputInt($request->get('batch_id'));

        if( $isUpdate && isset($batchId)){
            $batch = static::find($batchId);
            if(!is_object($batch)){
            	return 'false';
            }
        } else{
            $batch = new static;
        }
        $batch->name = $batchName;
        $batch->client_id = Auth::guard('client')->user()->id;
        $batch->student_ids = ' ';
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
    	$batch = static::find($batchId);
    	if(is_object($batch)){
    		$batch->student_ids = $students;
    		$batch->save();
    	}
    	return $batch;
    }

    protected static function getBatchById($id){
    	return static::find($id);
    }

}
