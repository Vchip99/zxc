<?php

namespace App\Http\Controllers\Client;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\ClientHomePage;
use App\Models\ClientTestimonial;
use App\Models\ClientTeam;
use App\Models\ClientOnlineCourse;
use App\Models\Clientuser;
use App\Models\ClientInstituteCourse;
use App\Models\ClientUserInstituteCourse;
use App\Models\Client;
use Illuminate\Http\Request;
use Auth, Redirect, View, DB;

class ClientUsersInfoController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->middleware('client');
        $subdomain = ClientHomePage::where('subdomain', $request->getHost())->first();
        if(is_object($subdomain)){
            view::share('subdomain', $subdomain);
        }
        $client = Client::where('subdomain', $subdomain->subdomain)->first();
        if(is_object($client)){
            view::share('client', $client);
        }
    }

    protected function allUsers(){
        $instituteCourses = ClientInstituteCourse::all();
        return view('client.allUsers.allUsers', compact('instituteCourses'));
    }

    protected function searchUsers(Request $request){
        return Clientuser::searchUsers($request);
    }

    protected function changeClientPermissionStatus(Request $request){

        return ClientUserInstituteCourse::changeClientPermissionStatus($request);
    }

    protected function deleteStudent(Request $request){
        $result = [];
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $deleteStudent = Clientuser::deleteStudent($request);
            if('true' == $deleteStudent){
                DB::connection('mysql2')->commit();
                $result['delete_student'] = 'true';
            }

        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
        }
        return Clientuser::searchUsers($request);
    }

}