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
use App\Models\ClientOnlinePaperSection;
use App\Models\ClientOnlineVideo;
use App\Models\ClientOnlineVideoLike;
use App\Models\Clientuser;
use App\Models\ClientAssignmentSubject;
use App\Models\ClientAssignmentTopic;
use App\Models\ClientAssignmentQuestion;
use App\Models\ClientAssignmentAnswer;
use App\Models\ClientReadNotification;
use App\Models\ClientNotification;
use App\Models\BankDetail;
use App\Models\ClientChatMessage;
use Auth;
use Intervention\Image\ImageManagerStatic as Image;

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
        'name', 'email', 'password','phone', 'subdomain', 'admin_approve','remember_token', 'photo', 'plan_id','allow_non_verified_email','absent_sms','exam_sms','offline_exam_sms','notice_sms','emergency_notice_sms','holiday_sms','assignment_sms','lecture_sms','individual_sms','login_using','academic_sms_count','message_sms_count','lecture_sms_count','otp_sms_count','debit_sms_count','credit_sms_count'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    const Student = 1;
    const Parents = 2;
    const Both    = 3;
    const None    = 4;

    const Facebook = 1;
    const Google   = 2;

    // sms groups
    // 1 - academic_sms_count -  absent_sms, exam_sms, offline_exam_sms, assignment_sms
    // 2 - message_sms_count -  notice_sms, emergency_notice_sms, holiday_sms, individual_sms, offline due sms
    // 3 - lecture_sms_count -  lecture_sms
    // 4 - otp_sms_count - otp_sms_count

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
        return Clientuser::where('client_id', Auth::guard('client')->user()->id)->where('user_type', Clientuser::Student)->count();
    }

    public function userCountByClientId($clientId){
        return Clientuser::where('client_id', $clientId)->where('user_type', Clientuser::Student)->count();
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
        $clientHomePage = ClientHomePage::where('client_id',$client->id)->first();
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
        ClientCourseSubComment::deleteClientCourseSubCommentsByClientId($client->id);
        ClientCourseSubCommentLike::deleteClientCourseSubCommentLikesByClientId($client->id);
        ClientOnlineCategory::deleteClientOnlineCategoriesByClientId($client->id);
        ClientOnlineCourse::deleteClientOnlineCoursesByClientId($client->id);
        ClientOnlineSubCategory::deleteClientOnlineSubCategoriesByClientId($client->id);
        ClientOnlineTestCategory::deleteClientOnlineTestCategoriesByClientId($client->id);
        ClientOnlineTestQuestion::deleteClientOnlineTestQuestionsByClientId($client->id);
        ClientOnlineTestSubCategory::deleteClientOnlineTestSubCategoriesByClientId($client->id);
        ClientOnlineTestSubject::deleteClientOnlineTestSubjectsByClientId($client->id);
        ClientOnlineTestSubjectPaper::deleteClientOnlineTestSubjectPapersByClientId($client->id);
        ClientOnlinePaperSection::deleteClientPaperSectionsByClientId($client->id);
        ClientOnlineVideo::deleteClientOnlineVideosByClientId($client->id);
        ClientOnlineVideoLike::deleteClientOnlineVideoLikesByClientId($client->id);
        ClientAssignmentSubject::deleteClientAssignmentSubjectByClientId($client->id);
        ClientAssignmentTopic::deleteClientAssignmentTopicByClientId($client->id);
        ClientAssignmentQuestion::deleteClientAssignmentQuestionByClientId($client->id);
        ClientAssignmentAnswer::deleteClientAssignmentAnswerByClientId($client->id);
        ClientReadNotification::deleteClientReadNotification($client->id);
        ClientNotification::deleteClientNotification($client->id);
        BankDetail::deleteBankDetails($client->id);
        ClientChatMessage::deleteClientChatMessagesByClientId($client->id);
        InputSanitise::delFolder("clientAssignmentStorage/".$client->id);
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
        $phone = $request->get('phone');

        $client = static::find(Auth::guard('client')->user()->id);
        if(is_object($client)){
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
                if(in_array($request->file('photo')->getClientMimeType(), ['image/jpg', 'image/jpeg', 'image/png'])){
                    // open image
                    $img = Image::make($client->photo);
                    // enable interlacing
                    $img->interlace(true);
                    // save image interlaced
                    $img->save();
                }
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

    public function unreadChatMessagesCount(){
        $clientId = Auth::guard('client')->user()->id;
        return ClientChatMessage::where('receiver_id', $clientId)->where('client_id', $clientId)->where('is_read', 0)->count();
    }
    protected static function toggleNonVerifiedEmailStatus(){
        $client = Auth::guard('client')->user();
        if(is_object($client)){
            if(1 == $client->allow_non_verified_email){
                $client->allow_non_verified_email = 0;
            } else if(0 == $client->allow_non_verified_email) {
                $client->allow_non_verified_email = 1;
            }
            $client->save();
            return $client->allow_non_verified_email;
        }
        return;
    }

    protected static function changeClientSetting(Request $request){
        $column = $request->get('column');
        $value = $request->get('value');
        $client = Auth::guard('client')->user();
        if(is_object($client)){
            $client->$column = $value;
            $client->save();
        }
        return;
    }
}
