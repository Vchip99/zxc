<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use App\Models\Clientuser;
use App\Models\ClientBatch;
use DB,Auth;

class ClientClass extends Model
{
    protected $connection = 'mysql2';

    protected $fillable = ['client_batch_id', 'clientuser_id', 'subject', 'topic', 'date', 'from_time', 'to_time', 'client_id'];

    /**
     *  add/update class
     */
    protected static function addOrUpdateClientClass( Request $request, $isUpdate=false){

    	$classId = InputSanitise::inputInt($request->get('class_id'));
    	$teacherId = InputSanitise::inputInt($request->get('teacher'));
        $clientBatchId = InputSanitise::inputInt($request->get('batch'));
        $subject  = InputSanitise::inputString($request->get('subject'));
        $topic  = InputSanitise::inputString($request->get('topic'));
        $date  = $request->get('date');
        $fromTime  = $request->get('from_time');
        $toTime  = $request->get('to_time');
        if( $isUpdate && isset($classId)){
            $clientClass = static::find($classId);
            if(!is_object($clientClass)){
            	return 'false';
            }
        } else{
            $clientClass = new static;
        }
        $clientClass->client_batch_id = $clientBatchId;
        $clientClass->clientuser_id = $teacherId;
        $clientClass->subject = $subject;
        $clientClass->topic = $topic;
        $clientClass->date = $date;
        $clientClass->from_time = $fromTime;
        $clientClass->to_time = $toTime;
        $clientClass->client_id = Auth::guard('client')->user()->id;
        $clientClass->save();
        return $clientClass;
    }

    public function batch(){
        return $this->belongsTo(ClientBatch::class, 'client_batch_id');
    }

    public function user(){
        return $this->belongsTo(Clientuser::class, 'clientuser_id');
    }

    protected static function assignClientClassesToClientByClientIdByTeacherId($clientId,$teacherId){
        $classes = static::where('client_id', $clientId)->where('clientuser_id', $teacherId)->get();
        if(is_object($classes) && false == $classes->isEmpty()){
            foreach($classes as $class){
                $class->clientuser_id = 0;
                $class->save();
            }
        }
    }

    protected static function deleteClientClassesByBtachIdByClientId($batchId,$clientId){
        return static::where('client_batch_id', $batchId)->where('client_id', $clientId)->delete();
    }
}
