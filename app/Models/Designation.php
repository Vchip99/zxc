<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB;
use App\Libraries\InputSanitise;
use App\Models\Area;

class Designation extends Model
{
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

    /**
     *  add/update Designation
     */
    protected static function addOrUpdateDesignation( Request $request, $isUpdate=false){
        $designationName = InputSanitise::inputString($request->get('designation'));
        $designationId   = InputSanitise::inputInt($request->get('designation_id'));
        if( $isUpdate && isset($designationId)){
            $designation = static::find($designationId);
            if(!is_object($designation)){
            	return Redirect::to('admin/manageDesignation');
            }
        } else{
            $designation = new static;
        }
        $designation->name = $designationName;
        $designation->save();
        return $designation;
    }

    public function areas(){
        return $this->hasMany(Area::class, 'designation_id');
    }
}
