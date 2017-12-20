<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB;
use App\Libraries\InputSanitise;

class PaperSection extends Model
{
    public $timestamps = false;
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
            	return Redirect::to('admin/manageSection');
            }
        } else{
            $section = new static;
        }
        $section->name = $sectionName;
        $section->save();
        return $section;
    }
}