<?php

namespace App\Models;

use App\Notifications\ClientResetPassword;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use App\Models\User;
use App\Models\ClientHomePage;
use App\Models\ClientTestimonial;
use App\Models\ClientTeam;
use App\Models\ClientCustomer;
use App\Models\ClientCourseComment;
use App\Models\ClientCourseCommentLike;
use App\Models\ClientCourseSubComment;
use App\Models\ClientCourseSubCommentLike;
use App\Models\ClientOnlineCategory;
use App\Models\ClientOnlineCourse;
use App\Models\ClientOnlineSubCategory;
use App\Models\ClientOnlineTestCategory;
use App\Models\ClientOnlineTestQuestion;
use App\Models\ClientOnlineTestSubCategory;
use App\Models\ClientOnlineTestSubject;
use App\Models\ClientOnlineTestSubjectPaper;
use App\Models\ClientOnlineVideo;
use App\Models\ClientOnlineVideoLike;

class Client extends Authenticatable
{
    use Notifiable;

    protected $connection = 'mysql2';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'user_id','email', 'password','phone', 'subdomain', 'verified', 'admin_approve', 'test_permission', 'course_permission','email_token', 'remember_token'
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
        $this->notify(new ClientResetPassword($token));
    }

     // Set the verified status to true and make the email token null
    public function verified()
    {
        $this->verified = 1;
        $this->email_token = null;
        $this->save();
        return;
    }

    protected static function changeClientPermissionStatus(Request $request){
        $userId = InputSanitise::inputInt($request->user_id);
        $clientId = InputSanitise::inputInt($request->client_id);
        $permissionType = InputSanitise::inputString($request->permission_type);

        $client = static::where('id', $clientId)->where('user_id', $userId)->first();
        if(is_object($client)){
            if('test' == $permissionType){
                if(1 == $client->test_permission){
                    $client->test_permission = 0;
                } else {
                    $client->test_permission = 1;
                }
            } else if('course' == $permissionType){
                if(1 == $client->course_permission){
                    $client->course_permission = 0;
                } else {
                    $client->course_permission = 1;
                }
            } else if('admin_approve' == $permissionType){
                if(1 == $client->admin_approve){
                    $client->admin_approve = 0;
                } else if(0 == $client->admin_approve) {
                    $client->admin_approve = 1;
                }
                \DB::connection('mysql')->beginTransaction();
                try
                {
                    $user = User::find($userId);
                    if(is_object($user) && 1 == $user->admin_approve){
                        $user->admin_approve   = 0;
                        $user->save();
                    } else if(is_object($user) && 0 == $user->admin_approve) {
                        $user->admin_approve   = 1;
                        $user->save();
                    }
                }
                catch(\Exception $e)
                {
                    \DB::connection('mysql')->rollback();
                    return redirect()->back();
                }
            }
            $client->save();
            return $client;
        }
        return 'false';
    }

    public function deleteOtherInfoByClient($client){
        $subdomain = explode('.', $client->subdomain);
        $clientFolderName = $subdomain[0];
        $clientFolder = "client_images/".$clientFolderName;
        if(is_dir($clientFolder)){
            InputSanitise::delFolder($clientFolder);
        }
        $clientUserFolder = "clientUserStorage/".str_replace(' ', '_', $client->name);
        if(is_dir($clientUserFolder)){
            InputSanitise::delFolder($clientUserFolder);
        }
        $clientHomePage = ClientHomePage::find($client->id);
        if(is_object($clientHomePage)){
            $clientHomePage->delete();
        }
        $clientTestimonials = ClientTestimonial::where('client_id',$client->id)->get();
        if(is_object($clientTestimonials) && false == $clientTestimonials->isEmpty()){
            foreach($clientTestimonials as $clientTestimonial){
                $clientTestimonial->delete();
            }
        }
        $clientTeams = ClientTeam::where('client_id',$client->id)->get();
        if(is_object($clientTeams) && false == $clientTeams->isEmpty()){
            foreach($clientTeams as $clientTeam){
                $clientTeam->delete();
            }
        }
        $clientCustomers = ClientCustomer::where('client_id',$client->id)->get();
        if(is_object($clientCustomers) && false == $clientCustomers->isEmpty()){
            foreach($clientCustomers as $clientCustomer){
                $clientCustomer->delete();
            }
        }

        ClientCourseComment::deleteClientCourseCommentsByClientId($client->id);
        ClientCourseCommentLike::deleteClientCourseCommentLikesByClientId($client->id);
        ClientCourseSubComment::deleteClientCourseSubCommentsByUserId($client->id);
        ClientCourseSubCommentLike::deleteClientCourseSubCommentLikesByUserId($client->id);
        ClientOnlineCategory::deleteClientOnlineCategoriesByClientId($client->id);
        ClientOnlineCourse::deleteClientOnlineCoursesByClientId($client->id);
        ClientOnlineSubCategory::deleteClientOnlineSubCategoriesByClientId($client->id);
        ClientOnlineTestCategory::deleteClientOnlineTestCategoriesByClientId($client->id);
        ClientOnlineTestQuestion::deleteClientOnlineTestQuestionsByClientId($client->id);
        ClientOnlineTestSubCategory::deleteClientOnlineTestSubCategoriesByClientId($client->id);
        ClientOnlineTestSubject::deleteClientOnlineTestSubjectsByClientId($client->id);
        ClientOnlineTestSubjectPaper::deleteClientOnlineTestSubjectPapersByClientId($client->id);
        ClientOnlineVideo::deleteClientOnlineVideosByClientId($client->id);
        ClientOnlineVideoLike::deleteClientOnlineVideoLikesByClientId($client->id);
        return;
    }
}
