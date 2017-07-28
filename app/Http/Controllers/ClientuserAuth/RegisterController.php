<?php

namespace App\Http\Controllers\ClientuserAuth;

use App\Models\Clientuser;
use App\Models\Client;
use App\Models\ClientUserInstituteCourse;
use Validator, Redirect;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use DB;
use Mail;
use App\Mail\ClientUserEmailVerification;
use App\Mail\NewClientUserRegistration;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('clientuser.guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'phone' => 'required|regex:/[0-9]{10}/',
            'email' => 'required|max:255',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
            'course' => 'required',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return Clientuser
     */
    protected function create(array $data)
    {
        $subdomain = request()->getHost();
        $client = Client::where('subdomain', $subdomain)->first();
        if(!is_object($client)){
            return Redirect::to('/');
        }

        $clientUser = Clientuser::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'client_id' => $client->id,
            'password' => bcrypt($data['password']),
            'email_token' => str_random(60),
        ]);

        $courseIds = $data['course'];
        $arrInsertCourses = [];
        if(is_array($courseIds)){
            foreach($courseIds as $courseId){
                $arrInsertCourses[] = ['client_user_id' => $clientUser->id,
                    'client_id' => $clientUser->client_id,
                    'client_institute_course_id' => $courseId,
                ];
            }
            if(is_array($arrInsertCourses)){
                ClientUserInstituteCourse::insert($arrInsertCourses);
            }
        }
        return $clientUser;
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm()
    {
        return view('clientuser.auth.register');
    }

    /**
     * Get the guard to be used during registration.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('clientuser');
    }

    public function register(Request $request)
    {
        // Laravel validation
        $validator = $this->validator($request->all());
        if ($validator->fails())
        {
            $this->throwValidationException($request, $validator);
        }

        // Using database transactions is useful here because stuff happening is actually a transaction
        // I don't know what I said in the last line! Weird!
        DB::beginTransaction();
        try
        {
            $user = $this->create($request->all());
            // After creating the user send an email with the random token generated in the create method above
            $clientUserEmail = new ClientUserEmailVerification(new Clientuser(['email_token' => $user->email_token, 'name' => $user->name]));
            Mail::to($user->email)->send($clientUserEmail);

            $client = Clientuser::getClientByClientUserEmail($request, $user->email);
            if(is_object($client)){
                $data = [
                    'name' => $user->name,
                    'email' => $user->email,
                ];
                Mail::to($client->email)->send(new NewClientUserRegistration($data));
            }
            DB::commit();
            return redirect('/')->with('message', 'Verify your email for your account activation.');
        }
        catch(Exception $e)
        {
            DB::rollback();
            return back();
        }
    }

     // Get the user who has the same token and change his/her status to verified i.e. 1
    public function verify($subdomain, $token)
    {
        // The verified method has been added to the user model and chained here
        // for better readability
        $user = Clientuser::where('email_token',$token)->first();
        if(is_object($user)){
            $user->verified();
            return redirect('/')->with('message', 'please login with credentials.');
        } else {
            return redirect('/')->withErrors('These credentials do not exist.');
        }
    }

}
