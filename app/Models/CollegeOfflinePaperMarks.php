<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB, Auth;
use App\Libraries\InputSanitise;
use App\Models\CollegeSubject;

class CollegeOfflinePaperMarks extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['college_id','college_subject_id','college_offline_paper_id','user_id','marks','total_marks','created_by'];

    protected static function assignCollegeOfflinePaperMarks($request){
    	$paperId   = InputSanitise::inputInt($request->get('paper'));
        $subjectId = InputSanitise::inputInt($request->get('subject'));
        $totalMarks   = InputSanitise::inputInt($request->get('total_marks'));
        $studentMarks = $request->except('_token','paper','subject','total_marks');
        $loginUser = Auth::user();

        if(count($studentMarks) > 0){
        	foreach($studentMarks as $studentId => $studentMark){
     			$student = static::where('college_id', $loginUser->college_id)->where('college_subject_id',$subjectId)->where('college_offline_paper_id', $paperId)->where('user_id', $studentId)->first();
     			if(!is_object($student)){
     				$student = new static;
     			}
     			$student->college_id = $loginUser->college_id;
     			$student->college_offline_paper_id = $paperId;
     			$student->user_id = $studentId;
                if('' == $studentMark){
     			    $student->marks = '';
                } else {
                    $student->marks = (double)$studentMark;
                }
     			$student->total_marks = $totalMarks;
     			$student->college_subject_id = $subjectId;
                $student->created_by = $loginUser->id;
     			$student->save();
        	}
            return 'true';
        }
        return 'false';
    }

    protected static function getOfflinePaperMarksBySubjectIdByPaperId(Request $request){
    	$paperId   = InputSanitise::inputInt($request->get('paper_id'));
        $subjectId = InputSanitise::inputInt($request->get('subject_id'));
        $loginUser = Auth::user();
        return static::where('college_id', $loginUser->college_id)->where('college_subject_id',$subjectId)->where('college_offline_paper_id', $paperId)->select('id','marks','total_marks','user_id')->get();
    }

    protected static function getOfflinePaperMarksByPaperId($paperId){
        $loginUser = Auth::user();
        return static::where('college_id', $loginUser->college_id)->where('college_offline_paper_id', $paperId)->select('id','marks','total_marks','user_id')->get();
    }

    protected static function deleteCollegeOfflinePaperMarksBySubjectId($subjectId){
        $loginUser = Auth::user();
        $offlinePaperMarks = static::where('college_id', $loginUser->college_id)->where('college_subject_id', $subjectId)->get();
        if(is_object($offlinePaperMarks) && false == $offlinePaperMarks->isEmpty()){
            foreach($offlinePaperMarks as $offlinePaperMark){
                $offlinePaperMark->delete();
            }
        }
        return;
    }

    protected static function deleteCollegeOfflinePaperMarksByCollegeByPaperIdByUserId($collegeId,$paperId,$userId){
        $offlinePaperMarks = static::where('college_id', $collegeId)->where('college_offline_paper_id', $paperId)->where('created_by', $userId)->get();
        if(is_object($offlinePaperMarks) && false == $offlinePaperMarks->isEmpty()){
            foreach($offlinePaperMarks as $offlinePaperMark){
                $offlinePaperMark->delete();
            }
        }
        return;
    }

    protected static function deleteCollegeOfflinePaperMarksByUserId($userId){
        $offlinePaperMarks = static::where('created_by', $userId)->get();
        if(is_object($offlinePaperMarks) && false == $offlinePaperMarks->isEmpty()){
            foreach($offlinePaperMarks as $offlinePaperMark){
                $offlinePaperMark->delete();
            }
        }
        return;
    }
}
