<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB, Auth;
use App\Libraries\InputSanitise;
use App\Models\ClientBatch;

class ClientOfflinePaper extends Model
{
    protected $connection = 'mysql2';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['client_batch_id','name','marks','client_id' ];

    /**
     *  add/update offline paper
     */
    protected static function addOrUpdateOfflinePaper( Request $request, $isUpdate=false){

        $paperName = InputSanitise::inputString($request->get('name'));
        $paperId   = InputSanitise::inputInt($request->get('paper_id'));
        $clientBatchId = InputSanitise::inputInt($request->get('batch'));
        $marks  = InputSanitise::inputString($request->get('marks'));
        if( $isUpdate && isset($paperId)){
            $paper = static::find($paperId);
            if(!is_object($paper)){
            	return 'false';
            }
        } else{
            $paper = new static;
        }
        $paper->name = $paperName;
        $paper->client_id = Auth::guard('client')->user()->id;
        $paper->client_batch_id = $clientBatchId;
        $paper->marks = $marks;
        $paper->save();
        return $paper;
    }

    protected static function getOfflinePapersByBatchId($clientBatchId){
        $loginClient = Auth::guard('client')->user();
        if($clientBatchId > 0){
            return static::where('client_id', $loginClient->id)->where('client_batch_id', $clientBatchId)->get();
        } else {
            return static::where('client_id', $loginClient->id)->where('client_batch_id','<=',$clientBatchId)->get();
        }
    }

    public function batch(){
        return $this->belongsTo(ClientBatch::class, 'client_batch_id');
    }

    protected static function deleteOfflinePaperseByBtachIdByClientId($batchId,$clientId){
        return static::where('client_batch_id', $batchId)->where('client_id', $clientId)->delete();
    }
}
