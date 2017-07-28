<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class UserSolution extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['ques_id', 'ques_answer', 'user_answer', 'user_id', 'paper_id', 'subject_id', 'score_id'];

    /**
     *  save answer
     */
    protected static function saveUserAnswers($userAnswers){
    	DB::table('user_solutions')->insert($userAnswers);
    }

    /**
     *  return solution by userId by scoreId by subjectId by paperId
     */
    protected static function getUserSolutionsByUserIdByscoreIdByBubjectIdByPaperId($userId, $scoreId, $subjectId, $paperId){
    	return DB::table('user_solutions')->where('user_id', $userId)
    								->where('score_id', $scoreId)
    								->where('subject_id', $subjectId)
    								->where('paper_id', $paperId)
    								->get();
    }

    protected static function deleteUserSolutionsByUserId($userId){
        $userSolutions = static::where('user_id', $userId)->get();
        if(is_object($userSolutions) && false == $userSolutions->isEmpty()){
            foreach($userSolutions as $userSolution){
                $userSolution->delete();
            }
        }
        return;
    }
}
