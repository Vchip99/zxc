<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Auth;

class RegisterClientOnlinePaper extends Model
{
	protected $connection = 'mysql2';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['client_user_id', 'client_paper_id', 'client_id'];

    protected static function registerTestPaper($userId, $paperId){
    	if(isset($userId) && isset($paperId)){
    		$registeredTestPaper = static::firstOrNew(['client_user_id' => $userId, 'client_paper_id' => $paperId, 'client_id' =>  Auth::guard('clientuser')->user()->client_id]);
    		if(is_object($registeredTestPaper) && empty($registeredTestPaper->id)){
    			$registeredTestPaper->save();
    		}
    		return $registeredTestPaper;
    	}
        return;
    }

    protected static function getRegisteredPapersByUserId($userId, $clientId){
        return static::where('client_user_id', $userId)->where('client_id', $clientId)->get();
    }

    protected static function deleteRegisteredPapersByUserId($userId,$clientId){
        $papers = static::where('client_user_id', $userId)->where('client_id', $clientId)->get();
        if(is_object($papers) && false == $papers->isEmpty()){
            foreach($papers as $paper){
                $paper->delete();
            }
        }
        return;
    }

    protected static function deleteRegisteredPapersClientId($clientId){
        $papers = static::where('client_id', $clientId)->get();
        if(is_object($papers) && false == $papers->isEmpty()){
            foreach($papers as $paper){
                $paper->delete();
            }
        }
        return;
    }

    protected static function deleteRegisteredPapersByClientIdByPaperId($clientId, $paperId){
        $papers = static::where('client_id', $clientId)->where('client_paper_id', $paperId)->get();
        if(is_object($papers) && false == $papers->isEmpty()){
            foreach($papers as $paper){
                $paper->delete();
            }
        }
        return;
    }


}
