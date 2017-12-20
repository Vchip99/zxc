<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB;
use App\Libraries\InputSanitise;

class AdvertisementPayment extends Model
{
      /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['add_id', 'email', 'payment_id', 'payment_request_id', 'status'];

    protected static function addPayment($paymentArray){
    	$payment = new static;
    	$payment->add_id = $paymentArray['add_id'];
    	$payment->email = $paymentArray['email'];
    	$payment->payment_id = $paymentArray['payment_id'];
    	$payment->payment_request_id = $paymentArray['payment_request_id'];
    	$payment->status = $paymentArray['status'];
    	$payment->save();
    	return $payment;
    }
}
