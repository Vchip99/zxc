<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB, Auth;
use App\Libraries\InputSanitise;

class Payment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['client_plan_id', 'payment_id', 'payment_request_id', 'status'];

    protected static function addPayment($paymentArray){
    	$payment = new static;
    	$payment->client_plan_id = $paymentArray['client_plan_id'];
    	$payment->payment_id = $paymentArray['payment_id'];
    	$payment->payment_request_id = $paymentArray['payment_request_id'];
    	$payment->status = $paymentArray['status'];
    	$payment->save();
    	return $payment;
    }
}
