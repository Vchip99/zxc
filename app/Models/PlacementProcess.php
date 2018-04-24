<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB;
use App\Libraries\InputSanitise;
use App\Models\PlacementArea;
use App\Models\PlacementCompany;
use App\Models\ExamPattern;
use App\Models\PlacementFaq;
use App\Models\PlacementProcessComment;
use App\Models\PlacementProcessCommentLike;
use App\Models\PlacementProcessSubComment;
use App\Models\PlacementProcessSubCommentLike;
use App\Models\PlacementProcessLike;
use App\Models\PlacementExperiance;

class PlacementProcess extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['placement_area_id', 'placement_company_id', 'selection_process','academic_criteria', 'aptitude_syllabus', 'hr_questions', 'job_link'];

    /**
     *  create/update PlacementCompany
     */
    protected static function addOrUpdatePlacementProcess( Request $request, $isUpdate=false){

        $patterns = [];
        $addExamPatterns = [];
        $updateExamPatterns = [];

    	$areaId = InputSanitise::inputInt($request->get('area'));
    	$companyId = InputSanitise::inputInt($request->get('company'));
    	$placementProcessId = InputSanitise::inputInt($request->get('placement_process_id'));
    	$selectionProcess = $request->get('selection_process');
    	$academicCriteria = $request->get('academic_criteria');
    	$aptitudeSyllabus = $request->get('aptitude_syllabus');
    	$hrQuestions = $request->get('hr_questions');
    	$jobLink = $request->get('job_link');

        if( $isUpdate && isset($placementProcessId)){
            $placementProcess = static::find($placementProcessId);
            if(!is_object($placementProcess)){
            	return Redirect::to('admin/managePlacementProcess');
            }
        } else{
            $placementProcess = new static;
        }
		$placementProcess->placement_area_id = $areaId;
        $placementProcess->placement_company_id = $companyId;
        $placementProcess->selection_process = $selectionProcess;
        $placementProcess->academic_criteria = $academicCriteria;
        $placementProcess->aptitude_syllabus = $aptitudeSyllabus;
        $placementProcess->hr_questions = $hrQuestions;
        $placementProcess->job_link = $jobLink;
		$placementProcess->save();


        if( $isUpdate && isset($placementProcessId)){
            $examPatterns = $request->except('_token','_method', 'area', 'company', 'selection_process','academic_criteria', 'aptitude_syllabus', 'hr_questions', 'job_link', 'placement_process_id', 'pattern_count', 'area_text', 'company_text');
            if(count($examPatterns) > 0){
                foreach($examPatterns as $index => $examPattern){
                    $explodes = explode('_', $index);
                    if('new' == $explodes[0]){
                        $addExamPatterns[] = $explodes[1];
                    } else {
                        $updateExamPatterns[$explodes[1]][$explodes[0]] = $examPattern;
                    }
                }
            }
            if(count($updateExamPatterns) > 0){
                $examPatterns = ExamPattern::where('placement_company_id', $companyId)->get();
                if(is_object($examPatterns) && false == $examPatterns->isEmpty()){
                    // update or delete
                    foreach($examPatterns as $examPattern){
                        if(false == in_array($examPattern->id, $addExamPatterns)){
                            if(isset($updateExamPatterns[$examPattern->id])){
                                $examPattern->testing_area = $updateExamPatterns[$examPattern->id]['area'];
                                $examPattern->no_of_question = $updateExamPatterns[$examPattern->id]['question'];
                                $examPattern->duration = $updateExamPatterns[$examPattern->id]['duration'];
                                $examPattern->save();
                            } else {
                                $examPattern->delete();
                            }
                        }
                    }
                }
                // add new
                foreach($updateExamPatterns as $index => $updateExamPattern){
                    if(true == in_array($index, $addExamPatterns)){
                        $newExamPattern = new ExamPattern;
                        $newExamPattern->testing_area = $updateExamPattern['area'];
                        $newExamPattern->no_of_question = $updateExamPattern['question'];
                        $newExamPattern->duration = $updateExamPattern['duration'];
                        $newExamPattern->placement_area_id = $areaId;
                        $newExamPattern->placement_company_id = $companyId;
                        $newExamPattern->save();

                    }
                }

            }
        } else {
            $patternCount = InputSanitise::inputInt($request->get('pattern_count'));
            if($patternCount > 0){
                for($i=1; $i<=$patternCount; $i++){
                    $testingArea = $request->get('area_'.$i);
                    $noOfQues = $request->get('question_'.$i);
                    $duration = $request->get('duration_'.$i);
                    if(!empty($testingArea) && !empty($noOfQues) && !empty($duration)){
                        $patterns[] = [
                                    'testing_area' => $testingArea,
                                    'no_of_question' => $noOfQues,
                                    'duration' => $duration,
                                    'placement_area_id' => $areaId,
                                    'placement_company_id' => $companyId
                                ];
                    }
                }
                if(count($patterns) > 0){
                    DB::table('exam_patterns')->insert($patterns);
                }
            }
        }

        return $placementProcess;
    }

    public function area(){
    	return $this->belongsTo(PlacementArea::class, 'placement_area_id');
    }

    public function company(){
    	return $this->belongsTo(PlacementCompany::class, 'placement_company_id');
    }

    protected static function checkPlacementCompanyProcesss($companyId){
        $result = static::where('placement_company_id', $companyId)->first();
        if(is_object($result)){
            return 'true';
        }
        return 'false';
    }

    public function deleteFaqs(){
        return $this->hasMany(PlacementFaq::class, 'placement_company_id');
    }

    public function deletePlacementProcessComments(){
        PlacementProcessComment::deletePlacementProcessCommentsByCompanyId($this->placement_company_id);
        PlacementProcessCommentLike::deletePlacementProcessCommentLikesByCompanyId($this->placement_company_id);
        PlacementProcessSubComment::deletePlacementProcessSubCommentsByCompanyId($this->placement_company_id);
        PlacementProcessSubCommentLike::deletePlacementProcessSubCommentLikesByCompanyId($this->placement_company_id);
        PlacementProcessLike::deletePlacementProcessLikesByCompanyId($this->placement_company_id);
        PlacementExperiance::deletePlacementExperiancesByCompanyId($this->placement_company_id);
        ExamPattern::deleteExamPatternsByCompanyId($this->placement_company_id);
        return;
    }
}
