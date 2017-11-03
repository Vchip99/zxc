<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Auth;
use DB, Session;

class ClientUserPurchasedTestSubCategory extends Model
{
    public $timestamps = false;
    protected $connection = 'mysql2';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'test_category_id', 'test_sub_category_id' ,'client_id'];

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
                $userTestSubCategories[] = $result->test_sub_category_id;
            }
        }
        return $userTestSubCategories;
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
    		$newTestSubCategory->save();
    		return 'true';
    	}elseif(true == is_object($testSubCategory)){
    		$testSubCategory->delete();
    		return 'true';
    	} else {
    		return 'false';
    	}
    }
}
