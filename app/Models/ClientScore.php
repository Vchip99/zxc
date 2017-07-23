<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Auth;
use DB, Session;

class ClientScore extends Model
{
	protected $connection = 'mysql2';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['client_user_id', 'category_id', 'subcat_id','subject_id', 'paper_id', 'right_answered', 'wrong_answered', 'unanswered', 'test_score'];

    /**
     *  add score
     */
    protected static function addScore($userId, $result){
        $userId     = Auth::guard('clientuser')->user()->id;

    	$score = new static();
    	$score->client_user_id = $userId;
    	$score->category_id = $result['category_id'];
    	$score->subcat_id = $result['subcat_id'];
        $score->paper_id = $result['paper_id'];
        $score->subject_id = $result['subject_id'];
        $score->right_answered = $result['right_answered'];
        $score->wrong_answered = $result['wrong_answered'];
        $score->unanswered     = $result['unanswered'];
        $score->test_score     = $result['marks'];
    	$score->save();

    	return $score;
    }

    // /**
    //  *  update score by userId by scoreId
    //  */
    // protected function updateScoreByUserIdByScoreId($userId, $scoreId, $result){
    //     $score = DB::connection('mysql2')->table('client_scores')
    //         ->where('id', $scoreId)
    //         ->where('client_user_id', $userId)
    //         ->update([ 'category_id' => $result['category_id'] ,'subcat_id' => $result['subcat_id'],'subject_id' => $result['subject_id'],'paper_id' => $result['paper_id'] , 'right_answered' => $result['right_answered'], 'wrong_answered' => $result['wrong_answered'], 'unanswered' => $result['unanswered'], 'is_test_given' => $result['is_test_given'], 'test_score' => $result['marks'] ]);
    //     return $score;
    // }

    protected static function getClientTestUserScoreByCategoryIdBySubcatIdByPaperIds($catId, $subcatId, $testSubjectPaperIds){
        $paperIds = [];
        if(is_object(Auth::guard('clientuser')->user())){
            $scores = static::where('category_id', $catId)
                    ->where('subcat_id', $subcatId)
                    ->whereIn('paper_id', $testSubjectPaperIds)
                    ->where('client_user_id', Auth::guard('clientuser')->user()->id)->get();
            if(is_object($scores) && false == $scores->isEmpty()){
                foreach($scores as $score){
                    $paperIds[] = $score->paper_id;
                }
            }
        }
        return $paperIds;
    }

    protected static function getClientUserTestResultByCategoryIdBySubcategoryIdBySubjectIdByPaperId($categoryId,$subcategoryId,$paperId,$subjectId,$userId){
        return static::where('category_id', $categoryId)
                ->where('subcat_id', $subcategoryId)
                ->where('paper_id', $paperId)
                ->where('subject_id', $subjectId)
                ->where('client_user_id', $userId)
                ->first();
    }

    protected static function getClientUserTestScoreBySubjectIdsByPaperIdsByUserId($testSubjectIds, $testSubjectPaperIds, $userId){
        $paperIds = [];
        if(count($testSubjectIds) > 0 && count($testSubjectPaperIds) > 0 && !empty($userId)) {
            $scores = static::whereIn('paper_id', $testSubjectPaperIds)
                    ->whereIn('subject_id', $testSubjectIds)
                    ->where('client_user_id', $userId)
                    ->get();
            if(is_object($scores) && false == $scores->isEmpty()){
                foreach($scores as $score){
                    $paperIds[] = $score->paper_id;
                }
            }
        }
        return $paperIds;
    }

       protected static function getClientUserTestRankByCategoryIdBySubcategoryIdBySubjectIdByPaperIdByTestScore($categoryId,$subcategoryId,$subjectId, $paperId,$testScore){
        if(is_object(Auth::guard('clientuser')->user())){
        return static::where('category_id', $categoryId)
                ->where('subcat_id', $subcategoryId)
                ->where('paper_id', $paperId)
                ->where('subject_id', $subjectId)
                ->where('test_score', '>', $testScore)
                ->where('client_user_id', Auth::guard('clientuser')->user()->id)
                ->count();
        }
        return;
    }

    protected static function getClientUserTestTotalRankByCategoryIdBySubcategoryIdBySubjectIdByPaperId($categoryId,$subcategoryId,$subjectId, $paperId){
        if(is_object(Auth::guard('clientuser')->user())){
        return static::where('category_id', $categoryId)
                ->where('subcat_id', $subcategoryId)
                ->where('paper_id', $paperId)
                ->where('subject_id', $subjectId)
                ->where('client_user_id', Auth::guard('clientuser')->user()->id)
                ->count();
        }
        return;
    }
}
