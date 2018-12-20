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
    protected $fillable = ['user_id', 'test_subject_paper_id','payment_id','payment_request_id','price','test_sub_category_id'];

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

    protected static function getRegisteredSubCategoryByUserIdBySubCategoryId($userId,$subCategoryId){
        return static::where('user_id', $userId)->where('test_sub_category_id',$subCategoryId)->first();
    }

    protected static function getRegisteredPapers(){
        return static::all();
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
        $purchasedPaper->test_sub_category_id = $paymentArray['test_sub_category_id'];
        $purchasedPaper->save();
        return $purchasedPaper;
    }

    protected static function getRegisteredPapersByUserIdForPayments($userId){
        return static::join('test_subject_papers','test_subject_papers.id','=','register_papers.test_subject_paper_id')
            ->whereNotNull('register_papers.payment_id')
            ->whereNotNull('register_papers.payment_request_id')
            ->where('register_papers.price', '>', 0)
            ->whereNotNull('register_papers.payment_id')
            ->whereNotNull('register_papers.payment_request_id')
            ->where('register_papers.user_id', $userId)
            ->select('register_papers.id','register_papers.updated_at','register_papers.price','test_subject_papers.name')
            ->groupBy('register_papers.id')->get();
    }
}
