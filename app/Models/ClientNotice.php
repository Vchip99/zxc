<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use App\Models\Clientuser;
use App\Models\ClientBatch;
use DB,Auth;

class ClientNotice extends Model
{
    protected $connection = 'mysql2';

    protected $fillable = ['client_batch_id', 'date', 'notice', 'is_emergency','client_id'];

    /**
     *  add/update notice
     */
    protected static function addOrUpdateClientNotice( Request $request, $isUpdate=false){

    	$noticeId = InputSanitise::inputInt($request->get('notice_id'));
        $clientBatchId = InputSanitise::inputInt($request->get('batch'));
        $isEmergency = InputSanitise::inputInt($request->get('is_emergency'));
        $notice  = InputSanitise::inputString($request->get('notice'));
        $date  = $request->get('date');
        if( $isUpdate && isset($noticeId)){
            $clientNotice = static::find($noticeId);
            if(!is_object($clientNotice)){
            	return 'false';
            }
        } else{
            $clientNotice = new static;
        }
        $clientNotice->client_batch_id = $clientBatchId;
        $clientNotice->date = $date;
        $clientNotice->notice = $notice;
        $clientNotice->is_emergency = $isEmergency;
        $clientNotice->client_id = Auth::guard('client')->user()->id;
        $clientNotice->save();
        return $clientNotice;
    }

    public function batch(){
        return $this->belongsTo(ClientBatch::class, 'client_batch_id');
    }

    protected static function deleteClientNoticesByBtachIdByClientId($batchId,$clientId){
        return static::where('client_batch_id', $batchId)->where('client_id', $clientId)->delete();
    }
}
