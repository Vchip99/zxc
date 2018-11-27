<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB, Auth,File;
use App\Libraries\InputSanitise;
use App\Models\ClientBatch;

class ClientIndividualMessage extends Model
{
    protected $connection = 'mysql2';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['client_batch_id','messages','client_id'];

    /**
     *  add/update client message
     */
    protected static function addClientIndividualMessage($allMessagesString,$batchId){
    	$loginUser = Auth::guard('client')->user();

        $message = new static;
        $message->client_batch_id = $batchId;
        $message->messages = $allMessagesString;
        $message->client_id = $loginUser->id;
        $message->save();
        return $message;
    }

    public function batch(){
        return $this->belongsTo(ClientBatch::class, 'client_batch_id');
    }

    protected static function getIndividualMessagesByClientIdByDate($clientId,$date){
    	return static::where('client_id', $clientId)->whereDate('created_at', $date)->select('*')->orderBy('created_at','desc')->get();
    }

    protected static function getIndividualMessagesByClientIdByBatchIds($clientId,$batchIds){
    	return static::where('client_id', $clientId)->whereIn('client_batch_id', $batchIds)->orderBy('id','desc')->get();
    }
}
