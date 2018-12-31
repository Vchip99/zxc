<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB, Auth;
use App\Libraries\InputSanitise;
use App\Models\ClientBatch;
use App\Models\ClientExam;

class ClientOfflinePaperMark extends Model
{
    protected $connection = 'mysql2';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['client_batch_id','client_exam_id','clientuser_id','marks','total_marks','client_id','created_by' ];

    protected static function getOfflinePaperMarksByBatchIdByExamId(Request $request){
    	$paperId   = InputSanitise::inputInt($request->get('client_exam_id'));
        $clientBatchId = InputSanitise::inputInt($request->get('batch_id'));
        $resultArr = InputSanitise::getClientIdAndCretedBy();
        $clientId = $resultArr[0];
        $createdBy = $resultArr[1];
        return static::where('client_batch_id', $clientBatchId)->where('client_exam_id', $paperId)->where('client_id', $clientId)->get();
    }

    protected static function assignOfflinePaperMarks($request){
    	$paperId   = InputSanitise::inputInt($request->get('client_exam'));
        $clientBatchId = InputSanitise::inputInt($request->get('batch'));
        $totalMarks   = InputSanitise::inputInt($request->get('total_marks'));
        $studentMarks = $request->except('_token','client_exam','batch','total_marks');
        $resultArr = InputSanitise::getClientIdAndCretedBy();
        $clientId = $resultArr[0];
        $createdBy = $resultArr[1];

        if(count($studentMarks) > 0){
        	foreach($studentMarks as $studentId => $studentMark){
     			$student = static::where('client_batch_id', $clientBatchId)->where('client_exam_id', $paperId)->where('clientuser_id', $studentId)->where('client_id',$clientId)->first();
     			if(!is_object($student)){
     				$student = new static;
     			}
     			$student->client_batch_id = $clientBatchId;
     			$student->client_exam_id = $paperId;
     			$student->clientuser_id = $studentId;
                if(empty($studentMark)){
     			    $student->marks = '';
                } else {
                    $student->marks = (double)$studentMark;
                }
     			$student->total_marks = $totalMarks;
     			$student->client_id = $clientId;
                $student->created_by = $createdBy;
     			$student->save();
        	}
            return 'true';
        }
        return 'false';
    }

    protected static function deleteClientOfflinePaperMarkByBatchIdByClientId($clientBatchId,$clientId){
        return static::where('client_batch_id', $clientBatchId)->where('client_id', $clientId)->delete();
    }

    protected static function getOfflinePaperMarksByUserIdByClientId($clientUserId,$clientId){
        return static::where('clientuser_id', $clientUserId)->where('client_id', $clientId)->get();
    }

    protected static function getOfflinePaperMarksByBatchIdByUserIdByClientId($clientBatchId,$clientUserId,$clientId){
        return static::where('client_batch_id', $clientBatchId)->where('clientuser_id', $clientUserId)->where('client_id', $clientId)->get();
    }

    public function batch(){
        return $this->belongsTo(ClientBatch::class, 'client_batch_id');
    }

    public function paper(){
        return $this->belongsTo(ClientExam::class, 'client_exam_id');
    }

    public function rank(){
        $rank =$this::getUserRankByBatchIdByExamByClientId($this->client_batch_id,$this->client_exam_id,$this->client_id,$this->marks);
        $totalRank =$this::getTotalRankByBatchIdByExamByClientId($this->client_batch_id,$this->client_exam_id,$this->client_id);
        return ($rank + 1).'/'.$totalRank;
    }

    public static function getUserRankByBatchIdByExamByClientId($clientBatchId,$exam,$clientId,$marks){
        return static::where('client_batch_id', $clientBatchId)->where('client_exam_id', $exam)->where('client_id', $clientId)->where('marks', '!=', '')->where('marks', '>', DB::raw($marks))->count();
    }

    public static function getTotalRankByBatchIdByExamByClientId($clientBatchId,$exam,$clientId){
        return static::where('client_batch_id', $clientBatchId)->where('client_exam_id', $exam)->where('client_id', $clientId)->where('marks', '!=', '')->count();
    }

    protected static function deleteMarksByClientIdByBatchIdByClientUsers($clientId,$clientBatchId,$clientUserIds){
        return static::where('client_id', $clientId)->where('client_batch_id', $clientBatchId)->whereIn('clientuser_id', $clientUserIds)->delete();
    }

    protected static function assignClientOfflinePaperMarksToClientByClientIdByTeacherId($clientId,$teacherId){
        $paperMarks = static::where('client_id', $clientId)->where('created_by', $teacherId)->get();
        if(is_object($paperMarks) && false == $paperMarks->isEmpty()){
            foreach($paperMarks as $paperMark){
                $paperMark->created_by = 0;
                $paperMark->save();
            }
        }
    }

    protected static function deleteClientOfflinePaperMarksByClientId($clientId){
        $marks = static::where('client_id', $clientId)->get();
        if(is_object($marks) && false == $marks->isEmpty()){
            foreach($marks as $mark){
                $mark->delete();
            }
        }
        return;
    }
}