<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB;
use App\Libraries\InputSanitise;
use App\Models\PlacementArea;
use App\Models\PlacementCompany;

class ExamPattern extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['placement_area_id', 'placement_company_id', 'testing_area','no_of_question', 'duration'];

    protected static function deleteExamPatternsByCompanyId($companyId){
        $examPatterns = static::where('placement_company_id', $companyId)->get();
        if(is_object($examPatterns) && false == $examPatterns->isEmpty()){
            foreach($examPatterns as $examPattern){
                $examPattern->delete();
            }
        }
        return;
    }
}
