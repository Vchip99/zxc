<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\ClientOnlineTestSubject;
use App\Models\ClientOnlineTestSubjectPaper;
use App\Models\ClientOnlineTestQuestion;
use App\Models\Clientuser;
use App\Models\ClientOnlineTestSubCategory;
use App\Models\ClientUserSolution;
use App\Models\RegisterClientOnlinePaper;
use App\Libraries\InputSanitise;
use DB, Session, Auth, Cache;

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

    protected static function getClientTestUserScoreByCategoryIdBySubcatIdByPaperIds($catId, $subcatId, $testSubjectPaperIds){
        $paperIds = [];
        $loginUser = Auth::guard('clientuser')->user();
        if(is_object($loginUser)){
            $clientUserId = $loginUser->id;
            $scores = static::where('category_id', $catId)
                ->where('subcat_id', $subcatId)
                ->whereIn('paper_id', $testSubjectPaperIds)
                ->where('client_user_id', $clientUserId)->get();
            if(is_object($scores) && false == $scores->isEmpty()){
                foreach($scores as $score){
                    $paperIds[] = $score->paper_id;
                }
            }
            return $paperIds;
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

    protected static function getClientUserTestResultBySubcategoryIdByPaperIdByUserId($subcategoryId,$paperId,$userId){
            return static::where('subcat_id', $subcategoryId)
                ->where('paper_id', $paperId)
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
        return static::where('category_id', $categoryId)
                ->where('subcat_id', $subcategoryId)
                ->where('paper_id', $paperId)
                ->where('subject_id', $subjectId)
                ->where('test_score', '>', DB::raw($testScore))
                ->count();
    }

    protected static function getClientUserTestTotalRankByCategoryIdBySubcategoryIdBySubjectIdByPaperId($categoryId,$subcategoryId,$subjectId, $paperId){
        return static::where('category_id', $categoryId)
            ->where('subcat_id', $subcategoryId)
            ->where('paper_id', $paperId)
            ->where('subject_id', $subjectId)
            ->count();
    }

    protected static function deleteClientUserScores($userId){
        $scores = static::where('client_user_id', $userId)->get();
        if(is_object($scores) && false == $scores->isEmpty()){
            foreach($scores as $score){
                $score->delete();
            }
        }
        return;
    }

    protected static function deleteScoresByPaperId($paperId){
        $scores = static::where('paper_id', $paperId)->get();
        if(is_object($scores) && false == $scores->isEmpty()){
            foreach($scores as $score){
                $score->delete();
            }
        }
        return;
    }

    public function solutions(){
        return $this->hasMany(ClientUserSolution::class, 'id');
    }

    public function subject(){
        return $this->belongsTo(ClientOnlineTestSubject::class, 'subject_id');
    }

    public function paper(){
        return $this->belongsTo(ClientOnlineTestSubjectPaper::class, 'paper_id');
    }

    public function user(){
        return $this->belongsTo(Clientuser::class, 'client_user_id');
    }

    public function rank(){
        $rank =$this->getClientUserTestRankByCategoryIdBySubcategoryIdBySubjectIdByPaperIdByTestScore($this->category_id,$this->subcat_id,$this->subject_id,$this->paper_id,$this->test_score);
        $totalRank =$this->getClientUserTestTotalRankByCategoryIdBySubcategoryIdBySubjectIdByPaperId($this->category_id,$this->subcat_id,$this->subject_id,$this->paper_id);
        return ($rank + 1).'/'.$totalRank;
    }
    public function totalMarks(){
        $totalMarks = 0;
        $loginClient = Auth::guard('client')->user();
        if(is_object($loginClient)){
            $clientId = $loginClient->id;
        } else {
            $clientId = Auth::guard('clientuser')->user()->client_id;
        }
        $subcategory = ClientOnlineTestSubCategory::find($this->subcat_id);
        if( is_object($subcategory) && 0 == $subcategory->client_id && 0 == $subcategory->category_id){
            $questions = ClientOnlineTestQuestion::getClientQuestionsByCategoryIdBySubcategoryIdBySubjectIdByPaperIdByClientId($subcategory->category_id,$this->subcat_id,$this->subject_id,$this->paper_id ,$subcategory->client_id);
        } else {
            $questions = ClientOnlineTestQuestion::getClientQuestionsByCategoryIdBySubcategoryIdBySubjectIdByPaperIdByClientId($this->category_id,$this->subcat_id,$this->subject_id,$this->paper_id ,$clientId);
        }
        if( is_object($questions) && false == $questions->isEmpty()){
            foreach($questions as $question){
                $totalMarks += $question->positive_marks;
            }
        }
        $percentage = round(($this->test_score/$totalMarks)*100,2);
        return ['totalMarks' => $totalMarks, 'percentage' => $percentage];
    }

    public static function getClientScoreByUserId($studentId){
        return static::join('clientusers', 'clientusers.id', '=', 'client_scores.client_user_id')
                ->join('client_online_test_subjects', 'client_online_test_subjects.id' , '=', 'client_scores.subject_id' )
                ->join('client_online_test_subject_papers', 'client_online_test_subject_papers.id' , '=', 'client_scores.paper_id' )
                ->where('client_scores.client_user_id', $studentId)
                ->where('client_online_test_subject_papers.date_to_inactive', '>=',date('Y-m-d H:i:s'))
                ->select('client_scores.*', 'client_online_test_subjects.name as subject', 'client_online_test_subject_papers.name as paper')
                ->get();
    }

    public static function getUserTestResultsByCategoryBySubcategoryByUserId(Request $request){
        $ranks = [];
        $marks = [];
        $category = InputSanitise::inputInt($request->get('category'));
        $subcategory = InputSanitise::inputInt($request->get('subcategory'));
        $student = InputSanitise::inputInt($request->get('student'));
        $scores = static::join('clientusers', 'clientusers.id', '=', 'client_scores.client_user_id')
                ->join('client_online_test_subjects', 'client_online_test_subjects.id' , '=', 'client_scores.subject_id' )
                ->join('client_online_test_subject_papers', 'client_online_test_subject_papers.id' , '=', 'client_scores.paper_id' )
                ->where('client_scores.category_id', $category)->where('client_scores.subcat_id', $subcategory)
                ->where('client_scores.client_user_id', $student)
                ->select('client_scores.*', 'client_online_test_subjects.name as subject', 'client_online_test_subject_papers.name as paper')->get();
        if( false == $scores->isEmpty()){
            foreach($scores as $score){
                $ranks[$score->id] = $score->rank();
                $marks[$score->id] = $score->totalMarks();
            }
        }
        $result['scores'] = $scores;
        $result['ranks'] = $ranks;
        $result['marks'] = $marks;
        return $result;
    }

    protected static function getAllUsersResults(Request $request){
        $categoryId = $request->get('category');
        $subcategoryId = $request->get('subcategory');
        $subjectId = $request->get('subject');
        $paperId = $request->get('paper');

        $result = static::join('clientusers', 'clientusers.id', '=', 'client_scores.client_user_id');

        if($categoryId > 0 ) {
            $result->where('client_scores.category_id', $categoryId);
        }
        if($subcategoryId > 0 ) {
            $result->where('client_scores.subcat_id', $subcategoryId);
        }
        if( $subjectId > 0){
            $result->where('client_scores.subject_id', $subjectId);
        }
        if($paperId > 0){
            $result->where('client_scores.paper_id', $paperId);
        }
        return $result->select('client_scores.*', 'clientusers.name as username')->orderBy('test_score', 'desc')->get();
    }
}
