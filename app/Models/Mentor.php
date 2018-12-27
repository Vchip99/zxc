<?php

namespace App\Models;

use App\Notifications\MentorResetPassword;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\MentorArea;
use Auth,DB;
use Intervention\Image\ImageManagerStatic as Image;

class Mentor extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','mobile','email','password','photo','designation','education','mentor_area_id','skills','linked_in','twitter','youtube','facebook','fees','about','experiance','achievement',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new MentorResetPassword($token));
    }

    public function mentorArea(){
        return $this->hasOne(MentorArea::class,'id','mentor_area_id');
    }

    protected static function updateMentorProfile($request){
        $mentor = static::find(Auth::guard('mentor')->user()->id);
        if(!is_object($mentor)){
            return 'false';
        }
        if(!empty($request->get('name'))){
            $mentor->name = $request->get('name');
        }
        if(!empty($request->get('designation'))){
            $mentor->designation = $request->get('designation');
        }
        if(!empty($request->get('area'))){
            $mentor->mentor_area_id = $request->get('area');
        }
        if(count($request->get('skills')) > 0){
            $mentor->skills = implode(',', $request->get('skills'));
        }
        if(!empty($request->get('linked_in'))){
            $mentor->linked_in = $request->get('linked_in');
        }
        if(!empty($request->get('twitter'))){
            $mentor->twitter = $request->get('twitter');
        }
        if(!empty($request->get('youtube'))){
            $mentor->youtube = $request->get('youtube');
        }
        if(!empty($request->get('facebook'))){
            $mentor->facebook = $request->get('facebook');
        }
        if(!empty($request->get('fees'))){
            $mentor->fees = $request->get('fees');
        }
        if(!empty($request->get('about'))){
            $mentor->about = $request->get('about');
        }
        if(!empty($request->get('experiance'))){
            $mentor->experiance = $request->get('experiance');
        }
        if(!empty($request->get('achievement'))){
            $mentor->achievement = $request->get('achievement');
        }
        $userStoragePath = "mentorImages/".$mentor->id;
        if(!is_dir($userStoragePath)){
            mkdir($userStoragePath);
        }
        if($request->exists('photo')){
            $userImage = $request->file('photo')->getClientOriginalName();
            if(!empty($mentor->photo) && file_exists($mentor->photo)){
                unlink($mentor->photo);
            }
            $request->file('photo')->move($userStoragePath, $userImage);
            $mentor->photo = $userStoragePath."/".$userImage;
            if(in_array($request->file('photo')->getClientMimeType(), ['image/jpg', 'image/jpeg', 'image/png'])){
                // open image
                $img = Image::make($mentor->photo);
                // enable interlacing
                $img->interlace(true);
                // save image interlaced
                $img->save();
            }
        }
        $mentor->save();
        return $mentor;
    }

    protected static function getMentorsByAreaIdBySkillId($request){
        $area = $request->get('area');
        $skill = $request->get('skill');
        $result = DB::table('mentors');
        if($area > 0){
            $result->where('mentor_area_id', $area);
        }
        if($skill > 0){
            $result->whereRaw("find_in_set($skill , skills)");
        }
        return $result->select('id','name','designation','education','photo','fees','skills','linked_in','twitter','facebook','youtube')->get();
    }

    protected static function getMentorsBySkillId($request){
        $skill = $request->get('skill');
        return DB::table('mentors')->whereRaw("find_in_set($skill , skills)")->select('id','name')->get();
    }

}
