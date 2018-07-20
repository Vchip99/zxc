<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB, Auth;
use App\Libraries\InputSanitise;
use App\Models\ClientBatch;

class ClientOfflinePaperMark extends Model
{
    protected $connection = 'mysql2';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['client_batch_id','client_offline_paper_id','clientuser_id','marks','total_marks','client_id' ];

    protected static function getOfflinePaperMarksByBatchIdByPaperId(Request $request){
    	$paperId   = InputSanitise::inputInt($request->get('paper_id'));
        $clientBatchId = InputSanitise::inputInt($request->get('batch_id'));
        return static::where('client_batch_id', $clientBatchId)->where('client_offline_paper_id', $paperId)->where('client_id', Auth::guard('client')->user()->id)->get();
    }

    protected static function assignOfflinePaperMarks($request){
    	$paperId   = InputSanitise::inputInt($request->get('paper'));
        $clientBatchId = InputSanitise::inputInt($request->get('batch'));
        $totalMarks   = InputSanitise::inputInt($request->get('total_marks'));
        $studentMarks = $request->except('_token','paper','batch','total_marks');
        $loginUser =  Auth::guard('client')->user();

        if(count($studentMarks) > 0){
        	foreach($studentMarks as $studentId => $studentMark){
     			$student = static::where('client_batch_id', $clientBatchId)->where('client_offline_paper_id', $paperId)->where('clientuser_id', $studentId)->where('client_id',$loginUser->id)->first();
     			if(!is_object($student)){
     				$student = new static;
     			}
     			$student->client_batch_id = $clientBatchId;
     			$student->client_offline_paper_id = $paperId;
     			$student->clientuser_id = $studentId;
     			$student->marks = $studentMark;
     			$student->total_marks = $totalMarks;
     			$student->client_id = $loginUser->id;
     			$student->save();
        	}
        }
    }

    protected static function deleteClientOfflinePaperMarkByBatchIdByPaperIdByClientId($clientBatchId,$paperId,$clientId){
    	return static::where('client_batch_id', $clientBatchId)->where('client_offline_paper_id', $paperId)->where('client_id', $clientId)->delete();
    }

    protected static function deleteClientOfflinePaperMarkByBatchIdByClientId($clientBatchId,$clientId){
        return static::where('client_batch_id', $clientBatchId)->where('client_id', $clientId)->delete();
    }
}