<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB, Cache;

class ClientUserSolution extends Model
{
	protected $connection = 'mysql2';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['ques_id', 'ques_answer', 'user_answer', 'client_user_id', 'paper_id', 'subject_id', 'client_score_id'];

    /**
     *  save answer
     */
    protected static function saveUserAnswers($userAnswers){
    	DB::connection('mysql2')->table('client_user_solutions')->insert($userAnswers);
    }

    /**
     *  return solution by userId by scoreId by subjectId by paperId
     */
    protected static function getClientUserSolutionsByUserIdByscoreIdByBubjectIdByPaperId($userId, $scoreId, $subjectId, $paperId){
            return DB::connection('mysql2')->table('client_user_solutions')->where('client_user_id', $userId)
				->where('client_score_id', $scoreId)
				->where('subject_id', $subjectId)
				->where('paper_id', $paperId)
				->get();
    }

    protected static function deleteClientUserSolutions($userId){
        $solutions = static::where('client_user_id', $userId)->get();
        if(is_object($solutions) && false == $solutions->isEmpty()){
            foreach($solutions as $solution){
                $solution->delete();
            }
        }
        return;
    }

    protected static function deleteClientUserSolutionsByQuestionId($questionId){
        $solutions = static::where('ques_id', $questionId)->get();
        if(is_object($solutions) && false == $solutions->isEmpty()){
            foreach($solutions as $solution){
                $solution->delete();
            }
        }
        return;
    }

    protected static function deleteClientUserSolutionsByUserIdPaperIdByScoreId($userId,$scoreId,$paperId){
        $solutions = static::where('client_user_id', $userId)->where('client_score_id', $scoreId)->where('paper_id', $paperId)->get();
        if(is_object($solutions) && false == $solutions->isEmpty()){
            foreach($solutions as $solution){
                $solution->delete();
            }
        }
        return;
    }
}
