<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB;
use App\Libraries\InputSanitise;

class WebdevelopmentPayment extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'domains', 'phone', 'payment_id', 'payment_request_id', 'status', 'price'];

    protected static function addPayment($paymentArray){
    	$payment = new static;
    	$payment->name = $paymentArray['name'];
    	$payment->email = $paymentArray['email'];
    	$payment->domains = $paymentArray['domains'];
    	$payment->phone = $paymentArray['phone'];
    	$payment->payment_id = $paymentArray['payment_id'];
    	$payment->payment_request_id = $paymentArray['payment_request_id'];
    	$payment->status = $paymentArray['status'];
        $payment->price = $paymentArray['price'];
    	$payment->save();
    	return $payment;
    }
}
