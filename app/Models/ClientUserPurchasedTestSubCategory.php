<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\ClientOnlineTestSubCategory;
use App\Models\Clientuser;
use App\Models\ClientOnlineTestSubjectPaper;
use App\Models\RegisterClientOnlinePaper;
use App\Models\ClientScore;
use App\Models\ClientUserSolution;
use Auth,DB, Session;

class ClientUserPurchasedTestSubCategory extends Model
{
    protected $connection = 'mysql2';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'test_category_id', 'test_sub_category_id' ,'client_id', 'payment_id', 'price', 'test_sub_category'];

    protected static function getClientUserTestSubCategories($clientId){
    	$testSubCategories = [];
    	$results = static::where('client_id', $clientId)->get();
    	if(is_object($results) && false == $results->isEmpty()){
    		foreach($results as $result){
    			$testSubCategories[$result->user_id][] = $result->test_sub_category_id;
    		}
    	}
    	return $testSubCategories;
    }

    protected static function getUserPurchasedTestSubCategories($clientId, $userId){
        $userTestSubCategories = [];
        $results = static::where('client_id', $clientId)->where('user_id', $userId)->get();
        if(is_object($results) && false == $results->isEmpty()){
            foreach($results as $result){
                $userTestSubCategories[$result->test_category_id][$result->test_sub_category_id] = $result->test_sub_category_id;
            }
        }
        return $userTestSubCategories;
    }

    protected static function getClientUserPurchasedTestSubCategories($clientId, $userId){
        $result = static::join('client_user_payments', 'client_user_payments.payment_id', '=', 'client_user_purchased_test_sub_categories.payment_id')
                ->where('client_user_purchased_test_sub_categories.client_id', $clientId);
        if($userId > 0){
            $result->where('client_user_purchased_test_sub_categories.user_id', $userId);
        }
        return $result->select('client_user_purchased_test_sub_categories.*', 'client_user_payments.updated_at')->get();
    }

    protected static function isTestSubCategoryPurchased($clientId, $userId, $testSubCategoryId){
        $testSubCategory = static::where('client_id', $clientId)->where('user_id', $userId)->where('test_sub_category_id', $testSubCategoryId)->first();
        if(is_object($testSubCategory)){
            return 'true';
        }
        return 'false';
    }

    protected static function changeClientUserTestSubCategoryStatus(Request $request){
    	$testSubCategory = static::where('client_id', $request->client_id)->where('user_id', $request->client_user_id)->where('test_category_id', $request->test_category_id)->where('test_sub_category_id', $request->test_sub_category_id)->first();
    	if(false == is_object($testSubCategory)){
    		$newTestSubCategory = new static;
    		$newTestSubCategory->user_id = $request->client_user_id;
    		$newTestSubCategory->test_category_id = $request->test_category_id;
    		$newTestSubCategory->test_sub_category_id = $request->test_sub_category_id;
    		$newTestSubCategory->client_id = $request->client_id;
            $newTestSubCategory->payment_id = '';
            $newTestSubCategory->test_sub_category = $newTestSubCategory->testSubCategory->name;
    		$newTestSubCategory->save();
    		return 'true';
    	}elseif(true == is_object($testSubCategory)){
            $subCategoryPapers = ClientOnlineTestSubjectPaper::getPapersBySubCategoryId($testSubCategory->test_sub_category_id);
            if(is_object($subCategoryPapers) && false == $subCategoryPapers->isEmpty()){
                foreach($subCategoryPapers as $subCategoryPaper){
                    $registeredTestPaper = RegisterClientOnlinePaper::getRegisteredPapersByUserIdByClientIdByPaperId($testSubCategory->user_id,$testSubCategory->client_id,$subCategoryPaper->id);;
                    if(is_object($registeredTestPaper)){
                        $clientUserScore = ClientScore::getClientUserTestResultBySubcategoryIdByPaperIdByUserId($testSubCategory->test_sub_category_id,$subCategoryPaper->id,$testSubCategory->user_id);
                        if(is_object($clientUserScore)){
                            ClientUserSolution::deleteClientUserSolutionsByUserIdPaperIdByScoreId($testSubCategory->user_id,$clientUserScore->id,$subCategoryPaper->id);
                            $clientUserScore->delete();
                        }
                        $registeredTestPaper->delete();
                    }
                }
            }
            $testSubCategory->delete();
    		return 'true';
    	} else {
    		return 'false';
    	}
    }

    public function testSubCategory(){
        return $this->belongsTo(ClientOnlineTestSubCategory::class, 'test_sub_category_id');
    }

    protected static function deleteClientUserTestSubCategories($clientId){
        $results = static::where('client_id', $clientId)->get();
        if(is_object($results) && false == $results->isEmpty()){
            foreach($results as $result){
                $result->delete();
            }
        }
        return;
    }

    public function clientUser(){
        $user = Clientuser::find($this->user_id);
        if(is_object($user)){
            return $user->name;
        } else {
            return 'deleted';
        }
    }
}
