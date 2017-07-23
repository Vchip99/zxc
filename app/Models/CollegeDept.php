<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use DB, Redirect;
use App\Libraries\InputSanitise;

class CollegeDept extends Model
{
      /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'college_id'];

    protected function addOrUpdateCollegeDept(Request $request, $collegeId, $isUpdate=false){

    	$deptmentArr = [];
    	$existCollegeId = InputSanitise::inputInt($request->get('college_id'));
    	if(!empty($existCollegeId)){
    		$deptments = $request->except('_token', 'college', '_method', 'college_id', 'delete_depts');

    		if(count($deptments) > 0){
    			foreach($deptments as $deptmentId => $deptmentName){
    				$id = explode('_', $deptmentId);
    				$deptId = $id[1];
    				$dept = static::where('id',$deptId)->where('college_id', $existCollegeId)->first();
		            if(is_object($dept)){
		            	$dept->name = $deptmentName;
		            	$dept->save();
		            } else {
		            	$dept = new static;
		            	$dept->name = $deptmentName;
		            	$dept->college_id = $existCollegeId;
		            	$dept->save();
		            }
    			}
    		}
    		$deleteDepts = trim($request->get('delete_depts'), ',');
    		if(!empty($deleteDepts)){
    			$deleteIds = explode(',', $deleteDepts);
    			if(count($deleteIds) > 0){
    				DB::table('college_depts')->where('college_id', $existCollegeId)->whereIn('id', $deleteIds)->delete();

    			}
    		}
    		return 'true';
    	} else {
	    	$deptments = count($request->except('_token', 'college'));

	    	if($deptments > 0){
	    		for($i=1; $i<=$deptments; $i++){
	    			$deptmentArr[] = [
	    				'name' => $request->get('department_'.$i),
	    				'college_id' => $collegeId
	    			];
	    		}
	    		if(count($deptmentArr) > 0){
	    			DB::table('college_depts')->insert($deptmentArr);
	    			return 'true';
	    		}
	    	}
    	}
    	return 'false';
    }

}
