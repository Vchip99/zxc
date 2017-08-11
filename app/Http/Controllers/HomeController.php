<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\MailToAdmin;
use App\Mail\ContactUs;
use App\Models\Category;
use App\Models\TestCategory;
use App\Models\User;
use App\Models\SubscriedUser;
use App\Models\College;
use App\Models\CollegeDept;
use App\Models\Designation;
use App\Models\ZeroToHero;
use App\Models\Area;
use View,DB,Session,Redirect;
use App\Mail\EmailVerification;
use App\Mail\SubscribedUserVerification;
use App\Libraries\InputSanitise;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $testCategories = TestCategory::all();
        // view::share('testCategories', $testCategories);
        parent::__construct();
    }

    /**
     * Show the signup.
     *
     * @return \Illuminate\Http\Response
     */
    public function signup()
    {
        $colleges = College::all();
        return view('header.signup', compact('colleges'));
    }

    public function getDepartments(Request $request){
        $collegeId = $request->get('college');
        return CollegeDept::where('college_id', $collegeId)->get();
    }

    /**
     *  show home
     */
    public function home(Request $request){
        return view('layouts.home');
    }

    public function register(){
        $categories = Category::all();
        return view('auth.register', compact('categories'));
    }

    /**
     *  show webinar
     */
    protected function webinar(){
        return view('webinar.webinar');
    }

    /**
     *  show webinar error
     */
    protected function webinarerror(){
        return view('webinar.webinarerror');
    }

    /**
     *  show vEducation
     */
    protected function vEducation(){
        return view('webinar.vEducation');
    }

    /**
     *  show vConnect
     */
    protected function vConnect(){
        return view('webinar.vConnect');
    }

    /**
     *  show vPendrive
     */
    protected function vPendrive(){
        return view('webinar.vPendrive');
    }

    /**
     *  show vCloud
     */
    protected function vCloud(){
        return view('webinar.vCloud');
    }

    /**
     *  show live video
     */
    protected function liveVideo(){
        return view('webinar.liveVideo');
    }

    /**
     *  show career
     */
    protected function career(){
        return view('more.career');
    }

    /**
     *  show partners
     */
    protected function ourpartner(){
        return view('more.ourpartner');
    }

    /**
     *  show contact us
     */
    protected function contactus(){
        return view('more.contactus');
    }

    /**
     *  show career
     */
    protected function heros(){
        $designations = Designation::all();
        $courses = [];
        $heros = ZeroToHero::all();
        return view('zerotohero.heros', compact('designations', 'heros'));
    }

    protected function sendMail(Request $request){
        try
        {
            Mail::to('vchipdesigng8@gmail.com')->send(new MailToAdmin($request));
            return redirect()->back()->with('message', 'Mail sent successfully! We will came back to you asap.');
        }
        catch(\Exception $e)
        {
            return redirect()->back()->withErrors('something went wrong.');
        }
    }

    protected function sendContactUsMail(Request $request){
        try
        {
            Mail::to('vchipdesigng8@gmail.com')->send(new ContactUs($request));
            return redirect()->back()->with('message', 'Mail sent successfully! We will came back to you asap.');
        }
        catch(\Exception $e)
        {
            return redirect()->back()->withErrors('something went wrong.');
        }
    }

    protected function verifyAccount(){
        return view('layouts.verify_account');
    }

    protected function verifyEmail(Request $request){
        $email = $request->get('email');
        if(!empty($email)){
            $user = User::where('email', $email)->where('verified', 0)->first();
            if(is_object($user)){
                DB::beginTransaction();
                try
                {
                    $user->verified = 0;
                    $user->email_token = str_random(60);
                    $user->save();

                    $email = new EmailVerification(new User(['email_token' => $user->email_token, 'name' => $user->name]));
                    Mail::to($user->email)->send($email);
                    DB::commit();
                    return redirect('/')->with('message', 'Verify your email for your account activation.');
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return redirect()->back()->withErrors('something went wrong.');
                }
            }
            return redirect()->back()->withErrors(['Email id does not exist or your account is already verified.']);
        }
        return redirect()->back()->withErrors(['Please enter email id.']);
    }

    protected function subscribedUser(Request $request){
        $user = SubscriedUser::where('email', $request->get('email'))->first();
        if(!is_object($user)){
            DB::beginTransaction();
            try
            {
                $subscriedUser = SubscriedUser::create([
                    'verified' => 0,
                    'email' => $request->get('email'),
                    'email_token' => str_random(60)
                ]);

                $email = new SubscribedUserVerification(new SubscriedUser(['email_token' => $subscriedUser->email_token, 'email' => $subscriedUser->email]));
                Mail::to($subscriedUser->email)->send($email);
                DB::commit();
                return redirect('/')->with('message', 'Verify your email for your account subscription.');
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return redirect()->back()->withErrors('something went wrong.');
            }
        }
        return redirect()->back()->withErrors(['You are already subscribed.']);
    }

    // Get the user who has the same token and change his/her status to verified i.e. 1
    public function verifySubscriedUser($token)
    {
        // The verified method has been added to the user model and chained here
        // for better readability
        $subscriedUser = SubscriedUser::where('email_token',$token)->where('verified', 0)->first();
        if(is_object($subscriedUser)){
            $subscriedUser->verified();
            return redirect('login')->with('message', 'Your subscription has been verified successfully.');
        } else {
            return redirect('/')->withErrors(['You are already subscribed.']);
        }
    }

    protected function getAreasByDesignation(Request $request){
        $designationId   = InputSanitise::inputInt($request->get('designation_id'));
        return Area::getAreasByDesignation($designationId);
    }

    protected function getHerosBySearchArray(Request $request){
        return ZeroToHero::getHerosBySearchArray($request);
    }

    protected function getHeroByDesignationByArea(Request $request){
        return ZeroToHero::getHeroByDesignationByArea($request);
    }

}
