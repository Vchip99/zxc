<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use DB;
use App\Models\User;

class ApplyJob extends Model
{
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['company', 'job_description', 'mock_test','job_url'];

    /**
     *  add/update Area
     */
    protected static function addOrUpdateApplyJob( Request $request, $isUpdate=false){
        $company = $request->get('company');
        $jobDescription = $request->get('job_description');
        $mockTest = $request->get('mock_test');
        $jobUrl = $request->get('job_url');
        $applyJobId   = InputSanitise::inputInt($request->get('apply_job_id'));
        if( $isUpdate && isset($applyJobId)){
            $applyJob = static::find($applyJobId);
            if(!is_object($applyJob)){
            	return Redirect::to('admin/manageApplyJob');
            }
        } else{
            $applyJob = new static;
        }
        $applyJob->company = $company;
        $applyJob->job_description = $jobDescription;
        $applyJob->mock_test = $mockTest;
        $applyJob->job_url = $jobUrl;
        $applyJob->save();
        return $applyJob;
    }
}
