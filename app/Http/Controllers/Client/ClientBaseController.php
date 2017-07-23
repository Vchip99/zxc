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
use App\Models\ClientOnlineTestSubCategory;
use App\Models\ClientCustomer;
use App\Models\Client;
use Illuminate\Http\Request;
use Auth, Redirect, View, DB;

class ClientBaseController extends BaseController
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

    protected function showDashBoard(){
        return view('client.dashboard');
    }

    protected function manageClientHome(){
        $onlineCourses = ClientOnlineCourse::getCurrentCoursesByClient(Auth::guard('client')->user()->subdomain);
        $defaultCourse = ClientOnlineCourse::where('name', 'How to use course')->first();
        $defaultTest = ClientOnlineCourse::where('name', 'How to use test')->first();

        $onlineTestSubcategories = ClientOnlineTestSubCategory::getCurrentSubCategoriesAssociatedWithQuestion(Auth::guard('client')->user()->subdomain);
        $subdomain = ClientHomePage::where('subdomain', Auth::guard('client')->user()->subdomain)->first();
        $testimonials = ClientTestimonial::where('client_id', Auth::guard('client')->user()->id)->get();
        $clientTeam = ClientTeam::where('client_id', Auth::guard('client')->user()->id)->get();
        $clientCustomers = ClientCustomer::where('client_id', Auth::guard('client')->user()->id)->get();
        return view('client.home', compact('subdomain', 'testimonials', 'clientTeam', 'clientCustomers','onlineCourses', 'defaultCourse', 'defaultTest', 'onlineTestSubcategories'));
    }

    protected function updateClientHome(Request $request){
        DB::connection('mysql2')->beginTransaction();
        try
        {
            ClientHomePage::updateClientHomePage($request);
            ClientTestimonial::updateTestimonials($request);
            ClientTeam::updateTeam($request);
            ClientCustomer::updateCustomer($request);
            DB::connection('mysql2')->commit();
            return Redirect::to('manageClientHome');
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
    }
}