<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use DB, Redirect;
use App\Libraries\InputSanitise;
use App\Models\CollegeDept;

class College extends Model
{
      /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

    protected function addOrUpdateCollege(Request $request, $isUpdate=false){
    	$collegeName = InputSanitise::inputString($request->college);

    	$collegeId = InputSanitise::inputInt($request->get('college_id'));
        if( $isUpdate && isset($collegeId)){
            $college = static::find($collegeId);
            if(!is_object($college)){
            	return Redirect::to('admin/manageCollegeInfo');
            }
        } else{
            $college = new static;
        }
    	$college->name = $collegeName;
    	$college->save();
    	return $college;
    }

    public function departments(){
    	return $this->hasMany(CollegeDept::class, 'college_id');
    }

}
