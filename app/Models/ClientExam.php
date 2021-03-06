<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use App\Models\Clientuser;
use App\Models\ClientBatch;
use DB,Auth;

class ClientExam extends Model
{
    protected $connection = 'mysql2';

    protected $fillable = ['client_batch_id', 'name', 'subject', 'topic', 'date', 'from_time', 'to_time', 'client_id','marks','exam_type'];

    /**
     *  add/update class
     */
    protected static function addOrUpdateClientExam( Request $request, $isUpdate=false){

    	$examId = InputSanitise::inputInt($request->get('exam_id'));
        $clientBatchId = InputSanitise::inputInt($request->get('batch'));
        $examName  = InputSanitise::inputString($request->get('name'));
        $subject  = InputSanitise::inputString($request->get('subject'));
        $topic  = InputSanitise::inputString($request->get('topic'));
        $marks  = InputSanitise::inputString($request->get('marks'));
        $examType  = InputSanitise::inputString($request->get('exam_type'));
        $date  = $request->get('date');
        $fromTime  = $request->get('from_time');
        $toTime  = $request->get('to_time');
        if( $isUpdate && isset($examId)){
            $exam = static::find($examId);
            if(!is_object($exam)){
            	return 'false';
            }
        } else{
            $exam = new static;
        }
        $exam->client_batch_id = $clientBatchId;
        $exam->name = $examName;
        $exam->subject = $subject;
        $exam->topic = $topic;
        $exam->date = $date;
        $exam->from_time = $fromTime;
        $exam->to_time = $toTime;
        $exam->client_id = Auth::guard('client')->user()->id;
        $exam->marks = $marks;
        $exam->exam_type = $examType;
        $exam->save();
        return $exam;
    }

    public function batch(){
        return $this->belongsTo(ClientBatch::class, 'client_batch_id');
    }

    protected static function deleteClientExamsByBtachIdByClientId($batchId,$clientId){
        return static::where('client_batch_id', $batchId)->where('client_id', $clientId)->delete();
    }

    protected static function getClientExamsByBatchId($clientBatchId){
        $resultArr = InputSanitise::getClientIdAndCretedBy();
        $clientId = $resultArr[0];
        if($clientBatchId > 0){
            return static::where('client_id', $clientId)->where('client_batch_id', $clientBatchId)->where('exam_type', 0)->get();
        } else {
            return static::where('client_id', $clientId)->where('client_batch_id','<=',$clientBatchId)->where('exam_type', 0)->get();
        }
    }

    protected static function deleteClientExamsByClientId($clientId){
        $exams = static::where('client_id', $clientId)->get();
        if(is_object($exams) && false == $exams->isEmpty()){
            foreach($exams as $exam){
                $exam->delete();
            }
        }
        return;
    }
}
