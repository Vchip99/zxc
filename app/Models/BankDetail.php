<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use DB, Auth;

class BankDetail extends Model
{
    protected $connection = 'mysql2';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'client_id', 'account_holder_name', 'account_number','ifsc_code'
    ];

    protected static function updateBankDetails(Request $request){
    	$name = $request->account_holder_name;
    	$acoountNumber = $request->account_number;
    	$ifscCode = $request->ifsc_code;
    	$bankDetailId = $request->bank_detail_id;
    	if(!empty($bankDetailId)){
    		$bankDetail = static::find($bankDetailId);
    		if(!is_object($bankDetail)){
    			return redirect('manageBankDetails');
    		}
    	} else {
    		$bankDetail = new static;
    	}
    	$bankDetail->client_id = Auth::guard('client')->user()->id;
    	$bankDetail->account_holder_name = $name;
    	$bankDetail->account_number = $acoountNumber;
    	$bankDetail->ifsc_code = $ifscCode;
    	$bankDetail->save();
    	return;
    }

    protected static function deleteBankDetails($clientId){
        $result = static::where('client_id', $clientId)->first();
        if(is_object($result)){
            $result->delete();
        }
        return;
    }
}
