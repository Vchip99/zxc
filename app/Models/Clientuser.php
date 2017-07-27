<?php

namespace App\Models;

use App\Notifications\ClientuserResetPassword;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use DB, Auth;
use App\Models\ClientInstituteCourse;
use App\Models\ClientUserInstituteCourse;
use App\Models\RegisterClientOnlinePaper;
use App\Models\RegisterClientOnlineCourses;

class Clientuser extends Authenticatable
{
    use Notifiable;
    protected $connection = 'mysql2';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','phone', 'client_id', 'verified', 'client_approve', 'email_token', 'remember_token'
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
        $this->notify(new ClientuserResetPassword($token));
    }

    protected static function verifyUserWithSubdomain(Request $request, $userId){
        $result = static::join('clients', 'clients.id', '=', 'clientusers.client_id')
                ->where('clientusers.id', $userId)
                ->where('clients.subdomain', $request->getHost())
                ->get();

        if($result->isEmpty()){
            return 'false';
        }
        return 'true';
    }

    public function verified()
    {
        $this->verified = 1;
        $this->email_token = null;
        $this->save();
    }

    protected static function getClientByClientUserEmail(Request $request, $email){
        return static::join('clients', 'clients.id', '=', 'clientusers.client_id')
                ->where('clientusers.email', $email)
                ->where('clients.subdomain', $request->getHost())
                ->select('clients.*')
                ->first();
    }

    protected static function searchUsers($request){
        $results = [];
        $institueCourses = [];
        $courseId = InputSanitise::inputInt($request->get('course_id'));
        $clientId = Auth::guard('client')->user()->id;
        $result = static::join('client_user_institute_courses', 'client_user_institute_courses.client_user_id', '=', 'clientusers.id')
                ->join('client_institute_courses', 'client_institute_courses.id', '=', 'client_user_institute_courses.client_institute_course_id');
        if($courseId > 0){
            $result->where('client_user_institute_courses.client_institute_course_id', $courseId);
        }
        if(!empty($request->get('student'))){
            $result->where('clientusers.name', 'LIKE', '%'.$request->get('student').'%');
        }
        $result->where('client_user_institute_courses.client_id', $clientId);

        $results['users'] = $result->select('clientusers.*', 'client_institute_courses.id as course_id','client_institute_courses.name as courseName')
                            ->groupBy('clientusers.id')->groupBy('client_institute_courses.id')->get();

        $coursesResult = ClientUserInstituteCourse::join('client_institute_courses', 'client_institute_courses.id', '=', 'client_user_institute_courses.client_institute_course_id')
                    ->join('clientusers', 'clientusers.id', '=', 'client_user_institute_courses.client_user_id');
        if($courseId > 0){
            $coursesResult->where('client_user_institute_courses.client_institute_course_id', $courseId);
        }
        if(!empty($request->get('student'))){
            $coursesResult->where('clientusers.name', 'LIKE', '%'.$request->get('student').'%');
        }
        $coursesResult->where('client_user_institute_courses.client_id', $clientId);

        $courses = $coursesResult->select('client_user_institute_courses.*', 'client_institute_courses.name as courseName')->get();

        if(is_object($courses) && false == $courses->isEmpty($courses)){
            foreach($courses as $course){
                $institueCourses[$course->client_user_id][$course->client_institute_course_id] = $course;
            }
        }

        $results['institueCourses'] = $institueCourses;
        return $results;
    }

    protected static function deleteStudent(Request $request){
        $clientId = InputSanitise::inputInt($request->client_id);
        $userId = InputSanitise::inputInt($request->client_user_id);

        $student = static::where('id',$userId)->where('client_id',$clientId)->first();
        dd($student);
        if(is_object($student)){
            $student->deleteOtherInfoByUserId($userId,$clientId);
            $student->delete();
            return 'true';
        }
        return 'false';
    }

    protected function deleteOtherInfoByUserId($userId,$clientId){
        RegisterClientOnlineCourses::deleteRegisteredOnlineCoursesByUserId($userId,$clientId);
        RegisterClientOnlinePaper::deleteRegisteredPapersByUserId($userId,$clientId);
        return;
    }
}
