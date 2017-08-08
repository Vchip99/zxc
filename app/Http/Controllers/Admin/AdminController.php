<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Mail;
use App\Models\Admin;
use App\Models\SubscriedUser;
use App\Models\User;
use App\Models\Client;
use App\Models\Clientuser;
use App\Mail\MailToSubscribedUser;
use Auth,Hash,Session,Redirect,Validator,DB;
use App\Libraries\InputSanitise;


class AdminController extends Controller
{
    public function __construct() {
        $this->middleware('admin');
    }

    protected function home(){
        return view('admin.home');
    }

    protected function writeMail(){
        return view('admin.writeMail');
    }

    protected function sendSubscribedMails(Request $request){
        $mailContent = $request->get('mail_content');
        $dom = new \DOMDocument();
        $dom->loadHTML($mailContent);
        $imgs = $dom->getElementsByTagName("img");
        foreach($imgs as $img){
            $src = $request->root().$img->getAttribute('src');
            $img->setAttribute( 'src' , $src );
        }
        $body = $dom->saveHTML();
        try
        {
            $subscriedUsers = SubscriedUser::where('verified', 1)->select('email')->get()->toArray();
            $subscriedUsers = implode(',', array_column($subscriedUsers, 'email'));
            Mail::to($subscriedUsers)->queue(new MailToSubscribedUser($body));
            return redirect()->back()->with('message', 'Mail will be sent successfully to all subscribed users.');
        }
        catch(\Exception $e)
        {
            return redirect()->back()->withErrors('something went wrong.');
        }
    }

    protected function manageClients(){
        $clients = User::getClients();
        if('false' == $clients){
            $clients = [];
        }
        return view('admin.clients', compact('clients'));
    }

    protected function changeClientPermissionStatus(Request $request){
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $client = Client::changeClientPermissionStatus($request);
            if(is_object($client)){
                DB::connection('mysql')->commit();
                DB::connection('mysql2')->commit();
            } else {
                DB::connection('mysql')->rollback();
                DB::connection('mysql2')->rollback();
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql')->rollback();
            DB::connection('mysql2')->rollback();
            return redirect()->back();
        }

        return User::getClients();
    }

    protected function deleteClient(Request $request){
        DB::connection('mysql')->beginTransaction();
        try
        {
            DB::connection('mysql2')->beginTransaction();
            try
            {
                $client = Client::find($request->client_id);
                if(is_object($client)){
                    Clientuser::deleteAllClientUsersInfoByClientId($client->id);
                    $client->deleteOtherInfoByClient($client);
                    $client->delete();
                    DB::connection('mysql2')->commit();
                }
            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
                return redirect()->back();
            }

            $user = User::find($request->user_id);
            if(is_object($user)){
                $user->deleteOtherInfoByUserId($user->id);
                $user->delete();
                DB::connection('mysql')->commit();
                return Redirect::to('admin/manageClients')->with('message', 'Client deleted successfully');
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql')->rollback();
            DB::connection('mysql2')->rollback();
            return redirect()->back();
        }
    }

}
