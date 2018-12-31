<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Auth;
use DB, Session;

class ClientUserPayment extends Model
{
    protected $connection = 'mysql2';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['client_id', 'clientuser_id' ,'payment_request_id', 'payment_id'];

    protected static function deleteClientUserPaymentsByClientId($clientId){
        $payments = static::where('client_id', $clientId)->get();
        if(is_object($payments) && false == $payments->isEmpty()){
            foreach($payments as $payment){
                $payment->delete();
            }
        }
        return;
    }
}
