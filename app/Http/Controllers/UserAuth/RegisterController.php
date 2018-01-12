<?php

namespace App\Http\Controllers\UserAuth;

use App\Models\User;
use App\Models\Client;
use App\Models\ClientHomePage;
use App\Models\ClientTestimonial;
use App\Models\ClientTeam;
use App\Models\ClientCustomer;
use App\Models\College;
use App\Models\CollegeDept;
use Validator, DB, Redirect;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Auth;
use App\Mail\EmailVerification;
use App\Mail\NewRegisteration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
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
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
            'user_type' => 'required',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        if('other' == $data['college']){
            $data['department'] = '';
            $data['year'] = '';
            $data['roll_no'] = '';
        }
        $emailToken= str_random(60);
        $user = User::create([
                'name' => $data['name'],
                'phone' => $data['phone'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
                'user_type' => $data['user_type'],
                'admin_approve' => 1,
                'degree' => 1,
                'college_id' => $data['college'],
                'college_dept_id' => $data['department'],
                'year' => $data['year'],
                'roll_no' => $data['roll_no'],
                'other_source' => $data['other_source'],
                'email_token' => $emailToken,
            ]);

        return $user;
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm()
    {
        return view('/');
    }

    /**
     * Get the guard to be used during registration.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('web');
    }

    /**
    *  Over-ridden the register method from the "RegistersUsers" trait
    *  Remember to take care while upgrading laravel
    */
    public function register(Request $request)
    {
        // Laravel validation
        $validator = $this->validator($request->all());

        if ($validator->fails())
        {
            $this->throwValidationException($request, $validator);
        }
        // Using database transactions is useful here because stuff happening is actually a transaction
        DB::beginTransaction();
        try
        {
            $user = $this->create($request->all());
            if( !is_object($user)){
                return redirect('/')->withErrors('Something went wrong.');
            }
            DB::commit();

            // After creating the user send an email with the random token generated in the create method above
            $email = new EmailVerification(new User(['email_token' => $user->email_token, 'name' => $user->name]));
            Mail::to($user->email)->send($email);
            $degree = [ 1 => 'Engineering'];
            $year   = [
                1 => 'First Year',
                2 => 'Second Year',
                3 => 'Third Year',
                4 => 'Fourth Year',
            ];
            $users = [
                // 1 => 'Admin/Owner of Institute ',
                2 => 'Student',
                3 => 'Lecturer',
                4 => 'HOD',
                5 => 'Principle / Director',
                6 => 'TNP Officer',
            ];
            if('other' != $request->get('college') && $request->get('college') > 0){
                $college = College::find($request->get('college'));
                if($request->get('department') > 0){
                    $collegeDept = CollegeDept::where('id', $request->get('department'))->where('college_id', $college->id)->first();
                }
            }
            $data['name'] = $user->name;
            $data['email'] = $user->email;
            $data['user_type'] = $users[$user->user_type];
            if($request->get('degree') > 0){
                $data['degree'] = $degree[$request->get('degree')];
            } else {
                $data['degree'] = '';
            }
            if('other' != $request->get('college') && $request->get('college') > 0){
                $data['college'] = $college->name?:'';
                if($request->get('department') > 0 && is_object($collegeDept)){
                    $data['department'] = $collegeDept->name;
                } else {
                    $data['department'] = '';
                }
                if($request->get('year') > 0){
                    $data['year'] = $year[$request->get('year')];
                } else {
                    $data['year'] = '';
                }
                $data['roll_no'] = $request->get('roll_no')?:'';
            } else{
                $data['college'] = $request->get('college');
                $data['department'] = '';
                $data['year'] = '';
                $data['roll_no'] = '';
            }

            $data['other_source'] = $request->get('other_source')?:'';

            // send mail to admin after new registration
            Mail::to('vchipdesigng8@gmail.com')->send(new NewRegisteration($data));

            return redirect('/')->with('message', 'Verify your email for your account activation.');
        }
        catch(Exception $e)
        {
            DB::rollback();
            return back();
        }
    }

    // Get the user who has the same token and change his/her status to verified i.e. 1
    public function verify($token)
    {
        // The verified method has been added to the user model and chained here
        // for better readability
        $user = User::where('email_token',$token)->first();

        if(is_object($user)){
            $user->verified();
            return redirect('login')->with('message', 'please login with credentials.');
        } else {
            return redirect('login')->withErrors('These credentials do not exist.');
        }
    }
}
