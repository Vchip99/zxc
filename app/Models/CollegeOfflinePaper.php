<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB, Auth;
use App\Libraries\InputSanitise;
use App\Models\CollegeSubject;

class CollegeOfflinePaper extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name','college_id','college_dept_id','college_subject_id','year','marks','created_by'];

    /**
     *  add/update offline paper
     */
    protected static function addOrUpdateCollegeOfflinePaper( Request $request, $isUpdate=false){
        $paperName = InputSanitise::inputString($request->get('paper'));
        $marks = InputSanitise::inputString($request->get('marks'));
        $subjectId   = InputSanitise::inputInt($request->get('subject'));
        $departmentId   = InputSanitise::inputInt($request->get('department'));
        $year   = InputSanitise::inputInt($request->get('year'));
        $paperId   = InputSanitise::inputInt($request->get('paper_id'));
        if( $isUpdate && isset($paperId)){
            $paper = static::find($paperId);
            if(!is_object($paper)){
            	return 'false';
            }
        } else {
            $paper = new static;
        }

        $loginUser = Auth::user();
        $paper->name = $paperName;
        $paper->college_id = $loginUser->college_id;
        $paper->college_dept_id = $departmentId;
        $paper->college_subject_id = $subjectId;
        $paper->year = $year;
        $paper->marks = $marks;
        $paper->created_by = $loginUser->id;
        $paper->save();
        return $paper;
    }

    protected static function isCollegeOfflinePaperExist(Request $request){
        $subjectId = InputSanitise::inputInt($request->get('subject'));
        $departmentId = InputSanitise::inputInt($request->get('department'));
        $year = InputSanitise::inputInt($request->get('year'));
        $paper = InputSanitise::inputString($request->get('paper'));
        $paperId = InputSanitise::inputInt($request->get('paper_id'));
        $loginUser = Auth::guard('web')->user();
        $result = static::where('name', $paper)->where('college_id', $loginUser->college_id)->where('college_subject_id', $subjectId);
        if(!empty($paperId)){
            $result->where('id', '!=', $paperId);
        }
        $result->first();

        if(is_object($result) && 1 == $result->count()){
            return 'true';
        } else {
            return 'false';
        }
    }

    protected static function getCollegeOfflinePapersBySubjectId(Request $request){
    	$loginUser = Auth::guard('web')->user();
    	return static::where('college_id', $loginUser->college_id)
    		->where('college_subject_id', $request->subject_id)
    		->select('id','name','marks')
    		->get();
    }

    protected static function getCollegeOfflinePapersByCollegeIdByAssignedDeptsWithPagination($collegeId){
        $loginUser = Auth::guard('web')->user();
        $deptIds = explode(',',$loginUser->assigned_college_depts);
        $result = static::join('users','users.id','=','college_offline_papers.created_by')
            ->where('college_offline_papers.college_id', $collegeId);

        if(User::Lecturer == $loginUser->user_type){
            $result->where('college_offline_papers.created_by', $loginUser->id)->where('users.user_type',User::Lecturer);
        } else {
            $result->whereIn('users.user_type',[User::Lecturer,User::Hod]);
        }
        return $result->whereIn('college_offline_papers.college_dept_id', $deptIds)
                ->select('college_offline_papers.*','users.name as user')
                ->groupBy('college_offline_papers.id')
                ->paginate();
    }

    protected static function getCollegeOfflinePapersByCollegeIdWithPagination($collegeId){
        $loginUser = Auth::guard('web')->user();
        $result = static::join('users','users.id','=','college_offline_papers.created_by')
            ->where('college_offline_papers.college_id', $collegeId);
        if(User::TNP == $loginUser->user_type){
            $result->where('college_offline_papers.created_by', $loginUser->id);
        }
        return $result->select('college_offline_papers.*','users.name as user')
                ->groupBy('college_offline_papers.id')
                ->paginate();
    }

    protected static function deleteCollegeOfflinePapersBySubjectId($subjectId){
        $loginUser = Auth::user();
        $offlinePapers = static::where('college_id', $loginUser->college_id)->where('college_subject_id', $subjectId)->get();
        if(is_object($offlinePapers) && false == $offlinePapers->isEmpty()){
            foreach($offlinePapers as $offlinePaper){
                $offlinePaper->delete();
            }
        }
        return;
    }

    /**
     *  get category of subject
     */
    public function subject(){
        return $this->belongsTo(CollegeSubject::class, 'college_subject_id');
    }

    protected static function getCollegeOfflinePapersByDeptIdByYear(Request $request){
        $loginUser = Auth::guard('web')->user();
        return static::join('users','users.id','=','college_offline_papers.created_by')
                ->join('college_subjects','college_subjects.id', '=', 'college_offline_papers.college_subject_id')
                ->where('college_offline_papers.college_id', $loginUser->college_id)
                ->where('college_offline_papers.college_dept_id', $request->department)
                ->where('college_offline_papers.year', $request->year)
                ->select('college_offline_papers.*','users.name as user','college_subjects.name as subject')
                ->groupBy('college_offline_papers.id')
                ->get();
    }

    protected static function deleteCollegeOfflinePapersByCollegeIdByDepartmentIdsByUserId($collegeId,$removedDepts,$userId){
        $offlinePapers = static::where('college_id', $collegeId)
                ->whereIn('college_dept_id', $removedDepts)
                ->where('created_by', $userId)->get();
        if(is_object($offlinePapers) && false == $offlinePapers->isEmpty()){
            foreach($offlinePapers as $offlinePaper){
                CollegeOfflinePaperMarks::deleteCollegeOfflinePaperMarksByCollegeByPaperIdByUserId($collegeId,$offlinePaper->id,$userId);
                $offlinePaper->delete();
            }
        }
        return;
    }

    protected static function deleteCollegeOfflinePapersByUserId($userId){
        $offlinePapers = static::where('created_by', $userId)->get();
        if(is_object($offlinePapers) && false == $offlinePapers->isEmpty()){
            foreach($offlinePapers as $offlinePaper){
                $offlinePaper->delete();
            }
        }
        return;
    }
}
