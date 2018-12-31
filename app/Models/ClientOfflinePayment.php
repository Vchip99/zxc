<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB, Auth;
use App\Libraries\InputSanitise;
use App\Models\ClientBatch;
use App\Models\Clientuser;
use App\Models\Client;

class ClientOfflinePayment extends Model
{
    protected $connection = 'mysql2';
    /**
     * The attributes that are mass assignable.
     *
     * @var array     */
    protected $fillable = ['client_batch_id','clientuser_id','amount','comment','due_date','client_id' ];

    /**
     *  add offline payment
     */
    protected static function addOrUpdateOfflinePayment(Request $request, $isUpdate=false){

        $clientBatchId = InputSanitise::inputInt($request->get('batch'));
        $userId   = InputSanitise::inputInt($request->get('user'));
        $amount = InputSanitise::inputString($request->get('amount'));
        $comment  = InputSanitise::inputString($request->get('comment'));
        $patmentId = InputSanitise::inputInt($request->get('payment_id'));
        $dueDate  = $request->get('due_date');
        if( $isUpdate && isset($patmentId)){
            $payment = static::find($patmentId);
            if(!is_object($payment)){
            	return 'false';
            }
        } else{
        	$payment = new static;
        }
        $payment->client_batch_id = $clientBatchId;
        $payment->clientuser_id = $userId;
        $payment->amount = $amount;
        $payment->comment = $comment;
        $payment->due_date = $dueDate;
        $payment->client_id = Auth::guard('client')->user()->id;
        $payment->save();
        return $payment;
    }

    public function batch(){
        return $this->belongsTo(ClientBatch::class, 'client_batch_id');
    }

    public function user(){
        return $this->belongsTo(Clientuser::class, 'clientuser_id');
    }

    public function client(){
        return $this->belongsTo(Client::class, 'client_id');
    }

    protected static function getPaymentsByClientId($clientId){
    	return static::where('client_id',$clientId)->get();
    }

    protected static function getTotalPaidByBatchIdByUserId($request){
    	$batchId = InputSanitise::inputInt($request->get('batch_id'));
    	$userId = InputSanitise::inputInt($request->get('user_id'));
    	$clientId = Auth::guard('client')->user()->id;
    	return static::where('client_id',$clientId)->where('client_batch_id',$batchId)->where('clientuser_id',$userId)->get();
    }

    protected static function deleteClientOfflinePaymentByBatchIdsByClientId($batchId,$clientId){
        $results = static::where('client_id', $clientId)->where('client_batch_id', $batchId)->get();
        if(is_object($results) && false == $results->isEmpty()){
            foreach($results as $result){
                $result->delete();
            }
        }
    }

    protected static function updateDueDateByClientIdByBatchIdByUserId($clientId,$batchId,$userId,$id){
        $results = static::where('client_id', $clientId)->where('client_batch_id', $batchId)->where('clientuser_id', $userId)->where('id','!=', $id)->get();
        if(is_object($results) && false == $results->isEmpty()){
            foreach($results as $result){
                $result->due_date = '';
                $result->save();
            }
        }
    }

    protected static function deleteClientOfflinePaymentsByClientId($clientId){
        $payments = static::where('client_id', $clientId)->get();
        if(is_object($payments) && false == $payments->isEmpty()){
            foreach($payments as $payment){
                $payment->delete();
            }
        }
        return;
    }
}
