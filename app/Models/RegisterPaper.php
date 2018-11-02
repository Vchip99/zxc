<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class RegisterPaper extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'test_subject_paper_id','payment_id','payment_request_id','price'];

    protected static function registerTestPaper($userId, $paperId){
    	if(isset($userId) && isset($paperId)){
    		$registeredTestPaper = static::firstOrNew(['user_id' => $userId, 'test_subject_paper_id' => $paperId]);
    		if(is_object($registeredTestPaper) && empty($registeredTestPaper->id)){
    			$registeredTestPaper->save();
    		}
    		return $registeredTestPaper;
    	}
        return;
    }

    protected static function getRegisteredPapersByUserId($userId){
        return static::where('user_id', $userId)->get();
    }

    protected static function deleteRegisteredPapersByUserId($userId){
        $papers = static::where('user_id', $userId)->get();
        if(is_object($papers) && false == $papers->isEmpty()){
            foreach($papers as $paper){
                $paper->delete();
            }
        }
        return;
    }

    protected static function addPurchasedPaper($paymentArray){
        $purchasedPaper = new static;
        $purchasedPaper->user_id = $paymentArray['user_id'];
        $purchasedPaper->test_subject_paper_id = $paymentArray['test_subject_paper_id'];
        $purchasedPaper->payment_id = $paymentArray['payment_id'];
        $purchasedPaper->payment_request_id = $paymentArray['payment_request_id'];
        $purchasedPaper->price = $paymentArray['price'];
        $purchasedPaper->save();
        return $purchasedPaper;
    }
}
