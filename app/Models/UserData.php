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
    protected $fillable = ['category_id', 'sub_category_id','subject_id', 'paper_id', 'user_id', 'experiance', 'company', 'education', 'skill_ids','facebook', 'twitter', 'skype', 'google', 'youtube', 'resume'];

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
}
