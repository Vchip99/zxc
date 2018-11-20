<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use DB, Redirect,Auth;
use App\Libraries\InputSanitise;
use App\Models\CollegeDept;

class College extends Model
{
      /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name','url','absent_sms','exam_sms','offline_exam_sms','notice_sms','emergency_notice_sms','holiday_sms','assignment_sms','lecture_sms','academic_sms_count','message_sms_count','lecture_sms_count','otp_sms_count','debit_sms_count','credit_sms_count'];

    // sms groups
    // 1 - academic_sms_count -  absent_sms, exam_sms, offline_exam_sms, assignment_sms
    // 2 - message_sms_count -  notice_sms, emergency_notice_sms, holiday_sms, individual_sms, offline due sms
    // 3 - lecture_sms_count -  lecture_sms
    // 4 - otp_sms_count - otp_sms_count

    protected function addOrUpdateCollege(Request $request, $isUpdate=false){
    	$collegeName = InputSanitise::inputString($request->college);
      $collegeUrl = InputSanitise::inputString($request->url);

    	$collegeId = InputSanitise::inputInt($request->get('college_id'));
        if( $isUpdate && isset($collegeId)){
            $college = static::find($collegeId);
            if(!is_object($college)){
            	return 'false';
            }
        } else{
            $college = new static;
            $college->debit_sms_count = 500;
        }
    	$college->name = $collegeName;
      $college->url = $collegeUrl;
    	$college->save();
    	return $college;
    }

    public function departments(){
    	return $this->hasMany(CollegeDept::class, 'college_id');
    }

    protected static function isCollegeExist(Request $request){
      $college = InputSanitise::inputString($request->get('college'));
      $collegeUrl = InputSanitise::inputString($request->get('url'));
      $collegeId   = InputSanitise::inputInt($request->get('college_id'));
      $result = static::where('name', '=',$college)->where('url', '=',$collegeUrl);
      if(!empty($collegeId)){
        $result->where('id', '!=', $collegeId);
      }
      $result->first();
      if(is_object($result) && 1 == $result->count()){
          return 'true';
      } else {
          return 'false';
      }
    }

    protected static function changeCollegeSetting(Request $request){
        $column = $request->get('column');
        $value = $request->get('value');
        $college = static::find(Auth::user()->college_id);
        if(is_object($college)){
          if('notice_sms' == $column || 'emergency_notice_sms' == $column || 'holiday_sms' == $column){
            if(!empty($college->$column)){
              $noticeValues = explode(',', $college->$column);
              if(in_array($value, $noticeValues)){
                $newValues = array_diff($noticeValues, [$value]);
                if(count($newValues) > 0){
                  sort($newValues);
                  $college->$column = implode(',', $newValues);
                } else {
                  $college->$column = '';
                }
              } else {
                array_push($noticeValues, $value);
                sort($noticeValues);
                $college->$column = implode(',', $noticeValues);
              }
            } else {
              $college->$column = $value;
            }
          } else {
            $college->$column = $value;
          }
          $college->save();
        }
        return;
    }
}
