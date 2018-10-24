<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Auth, DB, Cache;
use App\Libraries\InputSanitise;
use App\Models\TestSubjectPaper;
use App\Models\User;
use Intervention\Image\ImageManagerStatic as Image;

class UserData extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['category_id', 'sub_category_id','subject_id', 'paper_id', 'user_id', 'experiance', 'company', 'education', 'skill_ids','facebook', 'twitter', 'skype', 'google', 'youtube', 'resume','college_id'];

    /**
     *  add/update UserData
     */
    protected static function addOrUpdateUserData( Request $request, $isUpdate=false){
        $userDataId = InputSanitise::inputInt($request->get('user_data_id'));
        $categoryId = InputSanitise::inputInt($request->get('category'));
        $subcategoryId = InputSanitise::inputInt($request->get('subcategory'));
        $subjectId = InputSanitise::inputInt($request->get('subject'));
        $paperId = InputSanitise::inputInt($request->get('paper'));
        $userId = InputSanitise::inputInt($request->get('user'));
        $collegeId = InputSanitise::inputInt($request->get('college_id'));
        $year = InputSanitise::inputString($request->get('year'));
        $month = InputSanitise::inputString($request->get('month'));
        $company = InputSanitise::inputString($request->get('company'));
        $education = InputSanitise::inputString($request->get('education'));
        $skills = $request->get('skills');
        $facebook = $request->get('facebook');
        $twitter = $request->get('twitter');
        $skype = $request->get('skype');
        $google = $request->get('google');
        $youtube = $request->get('youtube');

        if( $isUpdate && isset($userDataId)){
            $userData = static::find($userDataId);
            if(!is_object($userData)){
            	return 'false';
            }
        } else{
            $userData = new static;
        }
        $userData->category_id = $categoryId;
        $userData->sub_category_id = $subcategoryId;
        $userData->subject_id = $subjectId;
        $userData->paper_id = $paperId;
        $userData->user_id = $userId;
        $userData->college_id = $collegeId;
        $userData->experiance = $year.','.$month;
        $userData->company = $company;
        $userData->education = $education;
        $userData->skill_ids = implode(',', $skills);
        $userData->facebook = $facebook;
        $userData->twitter = $twitter;
        $userData->skype = $skype;
        $userData->google = $google;
        if(!empty($youtube) && preg_match('/src="(.*)" frameborder/', $youtube, $matches)){
            if(!empty($matches[1]) && false == strpos($request->get('youtube'),'?enablejsapi=1')){
                $userData->youtube = str_replace($matches[1], $matches[1].'?enablejsapi=1', $youtube);
            } else if(!empty($matches[1]) && true == strpos($request->get('youtube'),'?enablejsapi=1')){
                $userData->youtube = $youtube;
            }
        }

        $userStoragePath = "userStorage/".$userId;
        if(!is_dir($userStoragePath)){
            mkdir($userStoragePath);
        }
        if($request->exists('resume')){
            $userResume = $request->file('resume')->getClientOriginalName();
            if(!empty($userData->resume) && file_exists($userData->resume)){
                unlink($userData->resume);
            }
            $request->file('resume')->move($userStoragePath, $userResume);
            $userData->resume = $userStoragePath."/".$userResume;
        }
        $userData->save();
        return $userData;
    }

    public function paper(){
        return $this->belongsTo(TestSubjectPaper::class, 'paper_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    protected static function getSelectedStudentBySkillId($skillId){
        return static::whereRaw("find_in_set($skillId , skill_ids)")->get();
    }

    protected function showVchipPlacementVideoByDepartmentByYear($college,$department,$year){
        $result = static::join('users','users.id','=','user_datas.user_id')
            ->where('users.user_type', User::Student)
            ->where('users.college_id', $college);
        if($department > 0){
            $result->where('users.college_dept_id', $department);
        }
        if($year > 0){
            $result->where('users.year', $year);
        }
        return $result->select('user_datas.id','users.name','users.email','user_datas.youtube','user_datas.resume','user_datas.skill_ids','user_datas.education','user_datas.experiance','user_datas.company')->get();
    }

    protected static function searchVchipStudentByDeptByYearByName(Request $request){
        $user = Auth::user();
        $result = static::join('users','users.id','=','user_datas.user_id')
                    ->where('users.college_id', $user->college_id)
                    ->where('users.user_type', User::Student)
                    ->where('users.name', 'LIKE', '%'.$request->student.'%');
        if($request->department > 0){
            $result->where('users.college_dept_id', $request->department);
        }
        if($request->year > 0){
            $result->where('users.year', $request->year);
        }
        return $result->select('user_datas.id','users.name','users.email','user_datas.youtube','user_datas.resume','user_datas.skill_ids','user_datas.education','user_datas.experiance','user_datas.company')->get();
    }
}
