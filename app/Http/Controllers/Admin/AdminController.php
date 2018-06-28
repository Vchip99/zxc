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
use App\Models\ClientPlan;
use App\Models\WebdevelopmentPayment;
use App\Models\PayableClientSubCategory;
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
        if(empty($mailContent)){
            return redirect()->back();
        }
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
            $mailSubject = 'Hello Vchip User';
            Mail::bcc($subscriedUsers)->queue(new MailToSubscribedUser($body,$mailSubject));
            return redirect()->back()->with('message', 'Mail will be sent successfully to all subscribed users.');
        }
        catch(\Exception $e)
        {
            return redirect()->back()->withErrors('something went wrong.');
        }
    }

    protected function manageClients(){
        $clients = User::getClients();
        return view('admin.clients', compact('clients'));
    }

    protected function changeClientPermissionStatus(Request $request){
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $client = Client::changeClientPermissionStatus($request);
            if(is_object($client)){
                DB::connection('mysql2')->commit();
            } else {
                DB::connection('mysql2')->rollback();
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back();
        }

        return User::getClients();
    }

    protected function deleteClient(Request $request){
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $client = Client::find($request->client_id);
            if(is_object($client)){
                Clientuser::deleteAllClientUsersInfoByClientId($client->id);
                $client->deleteOtherInfoByClient($client);
                $purchasedSubCategories = PayableClientSubCategory::getPayableSubCategoryByClientId($client->id);
                    if(is_object($purchasedSubCategories) && false == $purchasedSubCategories->isEmpty()){
                        foreach($purchasedSubCategories as $purchasedSubCategory){
                            $purchasedSubCategory->end_date = date('Y-m-d');
                            $purchasedSubCategory->save();
                        }
                    }
                $client->delete();
                DB::connection('mysql2')->commit();
                return Redirect::to('admin/manageClients')->with('message', 'Client deleted successfully');
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back();
        }
    }

    protected function manageClientHistory(){
        $clients = User::getClients();
        return view('admin.clientHistory', compact('clients'));
    }

    protected function getClientHistory(Request $request){
        $result = [];
        $total = 0;
        if($request->client_id > 0){
            $clientPlans = ClientPlan::where('client_id', $request->client_id)->orderBy('id', 'desc')->get();
        } else {
            $clientPlans = ClientPlan::orderBy('id', 'desc')->get();
        }
        if(is_object($clientPlans) && false == $clientPlans->isEmpty()){
            foreach($clientPlans as $clientPlan){
                $result['plans'][]= [
                                'client' => $clientPlan->client(),
                                'start_date' => $clientPlan->start_date,
                                'plan' => $clientPlan->plan->name,
                                'end_date' => $clientPlan->end_date,
                                'final_amount' => $clientPlan->final_amount,
                                'payment_status' => $clientPlan->payment_status,
                                'plan_id' => $clientPlan->plan_id,
                            ];
                $total += $clientPlan->final_amount;
            }
        } else {
            $result['plans'] = [];
        }
        if($request->client_id > 0){
            $purchasedSubCategories = PayableClientSubCategory::where('client_id', $request->client_id)->get();
        } else {
            $purchasedSubCategories = PayableClientSubCategory::all();
        }

        if(is_object($purchasedSubCategories) && false == $purchasedSubCategories->isEmpty()){
            foreach($purchasedSubCategories as $purchasedSubCategory){
                $result['purchasedSubCategories'][]= [
                                'client' => $purchasedSubCategory->clientName(),
                                'start_date' => $purchasedSubCategory->start_date,
                                'end_date' => $purchasedSubCategory->end_date,
                                'sub_category' => $purchasedSubCategory->sub_category,
                                'price' => $purchasedSubCategory->admin_price,
                            ];
                $total += $purchasedSubCategory->admin_price;
            }
        } else {
            $result['purchasedSubCategories'] = [];
        }
        $result['total'] = $total;
        return $result;
    }

    protected function manageWebDevelopments(){
        $totalSum = 0;
        $webDevelopments = WebdevelopmentPayment::all();
        if(is_object($webDevelopments) && false == $webDevelopments->isEmpty()){
            foreach($webDevelopments as $webDevelopment){
                $totalSum = $totalSum + $webDevelopment->price;
            }
        }
        return view('webDevelopment.webDevelopment', compact('webDevelopments', 'totalSum'));
    }
}
