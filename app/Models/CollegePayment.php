<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB, Auth;
use App\Libraries\InputSanitise;

class CollegePayment extends Model
{
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['college_id','user_id','user_name','note','payment_id','payment_request_id','price'];

    protected static function addCollegePurchasedSms($paymentArray){
    	$loginUser = Auth::user();
    	$payment = new static;
    	$payment->college_id = $loginUser->college_id;
    	$payment->user_id = $loginUser->id;
    	$payment->user_name = $loginUser->name;
    	$payment->note = $paymentArray['note'];
    	$payment->payment_id = $paymentArray['payment_id'];
    	$payment->payment_request_id = $paymentArray['payment_request_id'];
    	$payment->price = $paymentArray['price'];
    	$payment->save();
    	return $payment;
    }
}
