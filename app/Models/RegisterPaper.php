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
    protected $fillable = ['user_id', 'test_subject_paper_id'];

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
}
