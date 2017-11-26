<?php

namespace App\Models;

use App\Notifications\ClientuserResetPassword;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use DB, Auth;
use App\Models\RegisterClientOnlinePaper;
use App\Models\RegisterClientOnlineCourses;
use App\Models\ClientScore;
use App\Models\Client;
use App\Models\ClientUserSolution;
use App\Models\ClientNotification;
use App\Models\ClientReadNotification;
use App\Models\ClientOnlineCourse;
use App\Models\ClientUserPurchasedCourse;

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
        'name', 'email', 'password','phone', 'client_id', 'verified', 'client_approve', 'email_token', 'remember_token', 'photo','resume','recorded_video'
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
        $courseId = InputSanitise::inputInt($request->get('course_id'));
        $clientId = Auth::guard('client')->user()->id;
        $result = static::where('client_id', $clientId);

        if(!empty($request->get('student'))){
            $result->where('name', 'LIKE', '%'.$request->get('student').'%');
        }

        $results['users'] = $result->select('clientusers.*')->get();
        $results['courses'] = ClientOnlineCourse::getCourseAssocaitedWithVideos();
        $results['userPurchasedCourses'] = ClientUserPurchasedCourse::getClientUserCourses($clientId);
        $results['testSubCategories'] = ClientOnlineTestSubCategory::showSubCategoriesAssociatedWithQuestion($request);
        $results['userPurchasedTestSubCategories'] = ClientUserPurchasedTestSubCategory::getClientUserTestSubCategories($clientId);
        return $results;
    }

    protected static function deleteStudent(Request $request){
        $clientId = InputSanitise::inputInt($request->client_id);
        $userId = InputSanitise::inputInt($request->client_user_id);

        $student = static::where('id',$userId)->where('client_id',$clientId)->first();
        if(is_object($student)){
            $student->deleteOtherInfoByUserId($userId,$clientId);
            $student->deleteUserStorageFolder();
            $student->delete();
            return 'true';
        }
        return 'false';
    }

    protected function deleteOtherInfoByUserId($userId,$clientId){
        RegisterClientOnlineCourses::deleteRegisteredOnlineCoursesByUserId($userId,$clientId);
        RegisterClientOnlinePaper::deleteRegisteredPapersByUserId($userId,$clientId);
        ClientScore::deleteClientUserScores($userId);
        ClientUserSolution::deleteClientUserSolutions($userId);
        return;
    }

    public function deleteUserStorageFolder(){
        $client = Client::find($this->client_id);
        $userStoragePath = "clientUserStorage/".str_replace(' ', '_', $client->name)."/".$this->id;
        if(is_dir($userStoragePath)){
            InputSanitise::delFolder($userStoragePath);
        }
    }

    protected static function changeClientUserApproveStatus(Request $request){
        $clientId = InputSanitise::inputInt($request->client_id);
        $userId = InputSanitise::inputInt($request->client_user_id);

        $student = static::where('id',$userId)->where('client_id',$clientId)->first();
        if(is_object($student)){
            if( 1 == $student->client_approve){
                $student->client_approve = 0;
            } else {
                $student->client_approve = 1;
            }
            $student->save();
            return 'true';
        }
        return 'false';
    }

    protected static function getAllStudentsByClientId($clientId){
        return static::where('client_id', $clientId)
                ->select('clientusers.*')->get();
    }

    protected static function updateUser(Request $request){
        $user = Auth::guard('clientuser')->user();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;

        $client = Client::find($user->client_id);
        $userStoragePath = "clientUserStorage/".str_replace(' ', '_', $client->name)."/".$user->id;
        if(!is_dir($userStoragePath)){
            mkdir($userStoragePath, 0755, true);
        }
        if($request->exists('photo')){
            $userImage = $request->file('photo')->getClientOriginalName();
            $userImagePath = $userStoragePath."/".$user->photo;
            if(!empty($user->photo) && file_exists($userImagePath)){
                unlink($userImagePath);
            }
            $request->file('photo')->move($userStoragePath, $userImage);
            $dbUserImagePath = $userStoragePath."/".$userImage;
        }
        if($request->exists('resume')){
            $userResume = $request->file('resume')->getClientOriginalName();
            $userResumePath = $userStoragePath."/".$user->resume;
            if(!empty($user->resume) && file_exists($userResumePath)){
                unlink($userResumePath);
            }
            $request->file('resume')->move($userStoragePath, $userResume);
            $dbUserResumePath = $userStoragePath."/".$userResume;
        }
        if(!empty($dbUserImagePath)){
            $user->photo = $dbUserImagePath;
        }
        if(!empty($dbUserResumePath)){
            $user->resume = $dbUserResumePath;
        }
        $user->save();
        return $user;
    }

    protected static function getStudentById($studentId){
        return static::where('id', $studentId)
                ->select('id','resume','recorded_video')->first();
    }

    protected static function deleteAllClientUsersInfoByClientId($clientId){
        $users = static::where('client_id', $clientId)->get();
        if(is_object($users) && false == $users->isEmpty()){
            foreach($users as $user){
                ClientScore::deleteClientUserScores($user->id);
                ClientUserSolution::deleteClientUserSolutions($user->id);
                $user->delete();
            }
        }
        RegisterClientOnlineCourses::deleteRegisteredOnlineCoursesClientId($clientId);
        RegisterClientOnlinePaper::deleteRegisteredPapersClientId($clientId);
    }

    function adminNotificationCount($year=NULL,$month=NULL){
        $ids = [];
        $notificationCount = [];
        $ids = ClientReadNotification::getReadNotificationIdsByUser($year,$month);
        $resultQuery = ClientNotification::where('client_id', Auth::guard('clientuser')->user()->client_id)
                    ->where('is_seen', 0)->whereNotIn('id', $ids)->where('created_by',0)->where('created_to',0);
        if($year > 0){
            $resultQuery->whereYear('created_at', $year);
        }
        if($month > 0){
            $resultQuery->whereMonth('created_at', $month);
        }
        $results = $resultQuery->get();

        if(is_object($results) && false == $results->isEmpty()){
            foreach($results as $result){
                if(1 == $result->notification_module || 2 == $result->notification_module ){
                    $notificationCount[] = $result->id;
                }
            }
        }

        return count($notificationCount);
    }

    function userNotificationCount(){
        return ClientNotification::where('client_id', Auth::guard('clientuser')->user()->client_id)->where('created_to', Auth::guard('clientuser')->user()->id)->where('is_seen', 0)->count();

    }

    protected static function searchStudentForAssignment(){
        return static::where('client_id', Auth::guard('client')->user()->id)->select('clientusers.*')->get();
    }

    protected static function isInBetweenFirstTwenty(){
        $result = 'false';
        $users = static::where('client_id', Auth::guard('clientuser')->user()->client_id)->take(20)->get();
        if(is_object($users) && false == $users->isEmpty()){
            foreach($users as $user){
                if(Auth::guard('clientuser')->user()->id == $user->id){
                    $result = 'true';
                }
            }
        }
        return $result;
    }

    public function client(){
        return $this->belongsTo(Client::class, 'client_id');
    }
}