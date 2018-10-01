<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use App\Models\Clientuser;
use App\Models\ClientBatch;
use DB,Auth;

class ClientHoliday extends Model
{
    protected $connection = 'mysql2';

    protected $fillable = ['client_batch_id', 'date', 'note', 'client_id'];

    /**
     *  add/update holiday
     */
    protected static function addOrUpdateClientHoliday( Request $request, $isUpdate=false){

    	$holidayId = InputSanitise::inputInt($request->get('holiday_id'));
        $clientBatchId = InputSanitise::inputInt($request->get('batch'));
        $note  = InputSanitise::inputString($request->get('note'));
        $date  = $request->get('date');
        if( $isUpdate && isset($holidayId)){
            $holiday = static::find($holidayId);
            if(!is_object($holiday)){
            	return 'false';
            }
        } else{
            $holiday = new static;
        }
        $holiday->client_batch_id = $clientBatchId;
        $holiday->date = $date;
        $holiday->note = $note;
        $holiday->client_id = Auth::guard('client')->user()->id;
        $holiday->save();
        return $holiday;
    }

    public function batch(){
        return $this->belongsTo(ClientBatch::class, 'client_batch_id');
    }

    protected static function deleteClientHolidaysByBtachIdByClientId($batchId,$clientId){
        return static::where('client_batch_id', $batchId)->where('client_id', $clientId)->delete();
    }
}
