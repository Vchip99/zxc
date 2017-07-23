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

    protected function deleteOtherInfoByClient($client){
        $subdomain = explode('.', $client->subdomain);
        $clientFolderName = $subdomain[0];
        $clientFolder = "client_images/".$clientFolderName;
        if(is_dir($clientFolder)){
            InputSanitise::delFolder($clientFolder);
        }
        $clientHomePage = ClientHomePage::find($client->id);
        if(is_object($clientHomePage)){
            $clientHomePage->delete();
        }
        $clientTestimonial = ClientTestimonial::find($client->id);
        if(is_object($clientTestimonial)){
            $clientTestimonial->delete();
        }
        $clientTeam = ClientTeam::find($client->id);
        if(is_object($clientTeam)){
            $clientTeam->delete();
        }
        $clientCustomer = ClientCustomer::find($client->id);
        if(is_object($clientCustomer)){
            $clientCustomer->delete();
        }
        return;
    }
}
