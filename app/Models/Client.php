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
use App\Models\Clientuser;
use Auth;

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
        'name', 'email', 'password','phone', 'subdomain', 'admin_approve','remember_token', 'photo', 'plan_id'
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

    public function userCount(){
        return Clientuser::where('client_id', Auth::guard('client')->user()->id)->count();
    }

    public function userCountByClientId($clientId){
        return Clientuser::where('client_id', $clientId)->count();
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

    protected static function changeClientPermissionStatus(Request $request){
        $clientId = InputSanitise::inputInt($request->client_id);
        $permissionType = $request->permission_type;
        $client = static::find($clientId);
        if(is_object($client)){
            if('admin_approve' == $permissionType){
                if(1 == $client->admin_approve){
                    $client->admin_approve = 0;
                } else if(0 == $client->admin_approve) {
                    $client->admin_approve = 1;
                }
            }
            $client->save();
            return $client;
        }
        return 'false';
    }

    protected static function updateClientProfile(Request $request){
        $dbUserImagePath = '';
        $email = $request->get('email');
        $phone = $request->get('phone');

        $client = static::find(Auth::guard('client')->user()->id);
        if(is_object($client)){
            $client->email = $email;
            $client->phone = $phone;
            $userStoragePath = "client_images/".str_replace(' ', '_', $client->name)."/client";
            if(!is_dir($userStoragePath)){
                mkdir($userStoragePath, 0755, true);
            }
            if($request->exists('photo')){
                $userImage = $request->file('photo')->getClientOriginalName();
                $userImagePath = $userStoragePath."/".$client->photo;
                if(!empty($client->photo) && file_exists($userImagePath)){
                    unlink($userImagePath);
                }
                $request->file('photo')->move($userStoragePath, $userImage);
                $dbUserImagePath = $userStoragePath."/".$userImage;
            }

            if(!empty($dbUserImagePath)){
                $client->photo = $dbUserImagePath;
            }
            $client->save();
        }
        return;
    }

    protected static function isCLientExists(Request $request){
        if('local' == \Config::get('app.env')){
            $subdomain = $request->subdomain.'.localvchip.com';
        } else {
            $subdomain = $request->subdomain.'.vchipedu.com';
        }
        $client = static::where('subdomain', $subdomain)->first();
        if(is_object($client)){
            return 'true';
        } else {
            return 'false';
        }
    }
}
