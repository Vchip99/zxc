<?php

namespace App\Http\Controllers\MentorAuth;

use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Auth;
use App\Models\MentorArea;
use App\Models\MentorSkill;
use App\Models\Mentor;
use App\Mail\MentorSignUp;
use Redirect,View,DB,Mail,Session,Cache,File;

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
    protected $redirectTo = '/mentor/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('mentor.guest');
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
            'mobile' => 'required|numeric|regex:/[0-9]{10}/|unique:mentors',
            'email' => 'required|email|max:255|unique:mentors',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
            'designation' => 'required',
            'education' => 'required',
            'area' => 'required',
            'skills' => 'required',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return Mentor
     */
    protected function create(array $data)
    {
        $skills = '';
        if(count($data['skills']) > 0){
            $skills = implode(',', $data['skills']);
        }

        return Mentor::create([
                'name' => $data['name'],
                'mobile' => $data['mobile'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
                'designation' => $data['designation'],
                'education' => $data['education'],
                'mentor_area_id' => $data['area'],
                'skills' => $skills
            ]);
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm()
    {
        return view('mentor.auth.register');
    }

    /**
     * Get the guard to be used during registration.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('mentor');
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
            return redirect()->back()->withErrors($validator->errors());
        }
        // Using database transactions is useful here because stuff happening is actually a transaction
        DB::beginTransaction();
        try
        {
            $mentor = $this->create($request->all());
            if( !is_object($mentor)){
                return redirect()->back()->withErrors($mentor);
            }
            DB::commit();
            $data = [];
            $data['name'] = $request->get('name');
            $data['mobile'] = $request->get('mobile');
            $data['email'] = $request->get('email');
            // send mail to admin after new registration
            Mail::to('vchipdesigng8@gmail.com')->send(new MentorSignUp($data));
            return redirect('/')->with('message', 'Signup successfully,  Please login.');
        }
        catch(Exception $e)
        {
            DB::rollback();
            return back();
        }
    }
}
