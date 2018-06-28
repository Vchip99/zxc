<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB;
use App\Libraries\InputSanitise;

class PaperSection extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'duration', 'test_category_id', 'test_sub_category_id', 'test_subject_id', 'test_subject_paper_id'];

    /**
     *  add/update course section
     */
    protected static function addOrUpdatePaperSection( Request $request, $isUpdate=false){
        $sectionName = InputSanitise::inputString($request->get('section'));
        $sectionId   = InputSanitise::inputInt($request->get('section_id'));
        if( $isUpdate && isset($sectionId)){
            $section = static::find($sectionId);
            if(!is_object($section)){
            	return 'false';
            }
        } else{
            $section = new static;
        }
        $section->name = $sectionName;
        $section->save();
        return $section;
    }

    protected static function deletePaperSectionsByPaperId($paperId){
        $results = static::where('test_subject_paper_id', $paperId)->get();
        if(is_object($results) && false == $results->isEmpty()){
            foreach($results as $result){
                $result->delete();
            }
        }
        return;
    }
}
