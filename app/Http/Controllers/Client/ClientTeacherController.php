<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Libraries\InputSanitise;
use App\Models\ClientHomePage;
use App\Models\Clientuser;
use App\Models\Client;
use App\Models\ClientScore;
use App\Models\ClientOnlineCategory;
use App\Models\ClientOnlineSubCategory;
use App\Models\ClientOnlineCourse;
use App\Models\ClientOnlineVideo;
use App\Models\ClientOnlineTestCategory;
use App\Models\ClientOnlineTestSubCategory;
use App\Models\ClientOnlineTestSubject;
use App\Models\ClientOnlineTestSubjectPaper;
use App\Models\ClientOnlinePaperSection;
use App\Models\ClientOnlineTestQuestion;
use App\Models\ClientBatch;
use App\Models\ClientUserAttendance;
use App\Models\ClientOfflinePaperMark;
use App\Models\ClientAssignmentSubject;
use App\Models\ClientAssignmentTopic;
use App\Models\ClientAssignmentQuestion;
use App\Models\ClientAssignmentAnswer;
use App\Models\ClientMessage;
use App\Models\ClientClass;
use Auth, Redirect, View, DB, Session, Validator, Hash,Cache,Excel;

class ClientTeacherController extends BaseController
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
            $client = Client::where('subdomain', $subdomain->subdomain)->first();
            if(is_object($client)){
                view::share('client', $client);
            }
        }
    }

    protected function addTeacher($subdomainName,Request $request){
        return view('client.teacher.addTeacher', compact('subdomainName'));
    }

    protected function addMobileTeacher($subdomainName,Request $request){
        $userMobile = $request->get('phone');
        $userOtp = $request->get('user_otp');
        $serverOtp = Cache::get($userMobile);
        if($serverOtp == $userOtp){
            DB::connection('mysql2')->beginTransaction();
            try
            {
                $clientId = Auth::guard('client')->user()->id;
                Clientuser::addMobileUser($request,$clientId,Clientuser::Teacher);
                DB::connection('mysql2')->commit();
                if(Cache::has($userMobile) && Cache::has('mobile-'.$userMobile)){
                    Cache::forget($userMobile);
                    Cache::forget('mobile-'.$userMobile);
                }
                return Redirect::to('addTeachers')->with('message', 'Mobile teacher added successfully.');
            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
                return redirect()->back()->withErrors('something went wrong while add mobile teachers.');
            }
        } else {
            return Redirect::to('addTeachers')->withErrors('Entered wrong otp.');
        }
    }

    protected function addEmailTeacher($subdomainName,Request $request){
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $clientId = Auth::guard('client')->user()->id;
            $result = Clientuser::addEmailUser($request,$clientId,Clientuser::Teacher);
            if('true' == $result['status']){
                DB::connection('mysql2')->commit();
                if(isset($result['duplicate_email']) && count($result['duplicate_email']) > 0){
                    $emailStr = implode(',', $result['duplicate_email']);
                    return Redirect::to('addTeachers')->withErrors('Following Email id/User id teachers are not added-'.$emailStr);
                } else {
                    return Redirect::to('addTeachers')->with('message', 'Email teacher added successfully.');
                }
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong while add email teachers.');
        }
        return Redirect::to('addTeachers');
    }

    protected function uploadClientTeachers($subdomain, Request $request){
        if($request->hasFile('teachers')){
            $path = $request->file('teachers')->getRealPath();
            $users = \Excel::selectSheetsByIndex(0)->load($path, function($reader) {
                            $reader->formatDates(false);
                        })->get();
            $loginUser = Auth::guard('client')->user();
            if($users->count()){
                $allUsers = [];
                foreach ($users as $key => $user) {
                    if(!empty($user->name) && !empty($user->phone) && !empty($user->emailuser_id) && !empty($user->password)){
                        $allUsers[] = [
                            'name' => (string)$user->name,
                            'phone' => (string)$user->phone,
                            'email' => (string)$user->emailuser_id,
                            'password' => (string)$user->password,
                            'client_id' => $loginUser->id,
                            'client_approve' => 1,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ];
                    }
                }
                if(count($allUsers) > 0){
                    DB::connection('mysql2')->beginTransaction();
                    try
                    {
                        $result = [];
                        foreach($allUsers as $insertData){
                            $existingUser = Clientuser::where('email',$insertData['email'])->where('client_id',$insertData['client_id'])->first();
                            if(!is_object($existingUser)){
                                $user = new Clientuser;
                                $user->name = $insertData['name'];
                                $user->email = $insertData['email'];
                                $user->phone = $insertData['phone'];
                                $user->password = bcrypt($insertData['password']);
                                $user->client_id = $insertData['client_id'];
                                $user->verified = 0;
                                $user->client_approve = $insertData['client_approve'];
                                if(filter_var($insertData['email'], FILTER_VALIDATE_EMAIL)){
                                    $user->email_token = str_random(60);
                                } else {
                                    $user->email_token = '';
                                }
                                $user->user_type = Clientuser::Teacher;
                                $user->save();
                                if(!empty($user->email) && filter_var($user->email, FILTER_VALIDATE_EMAIL)){
                                    $clientUserEmail = new ClientUserEmailVerification(new Clientuser(['email_token' => $user->email_token, 'name' => $user->name]));
                                    Mail::to($user->email)->send($clientUserEmail);
                                }
                            } else {
                                $result['duplicate_email'][] = $insertData['email'];
                            }
                        }
                        DB::connection('mysql2')->commit();
                        if(isset($result['duplicate_email']) && count($result['duplicate_email']) > 0){
                            $emailStr = implode(',', $result['duplicate_email']);
                            return Redirect::to('addTeachers')->withErrors('Following Email id/User id teachers are not added-'.$emailStr);
                        } else {
                            return Redirect::to('addTeachers')->with('message', 'Teachers added successfully!');
                        }
                    }
                    catch(\Exception $e)
                    {
                        DB::connection('mysql2')->rollback();
                        return redirect()->back()->withErrors('something went wrong while upload Teachers.');
                    }
                }
            }
        }
        return Redirect::to('addTeachers');
    }

    protected function allTeacher($subdomainName,Request $request){
        $loginUser = Auth::guard('client')->user();
        $allModules = [
            Clientuser::CourseModule => 'Course',
            Clientuser::TestModule => 'Test',
            Clientuser::UserInfoModule => 'UserInfo',
            Clientuser::AllTestResultModule => 'AllTestResult',
            Clientuser::BatchModule => 'Batch',
            Clientuser::AssignmentModule => 'Assignment',
            Clientuser::EventModule => 'Event/Message'
        ];
        $clientTeachers = Clientuser::getTeachersByClientId($loginUser->id);
        return view('client.teacher.allTeacher', compact('subdomainName', 'allModules', 'clientTeachers'));
    }

    protected function changeClientTeacherModuleStatus($subdomainName,Request $request){
        Clientuser::changeClientTeacherModuleStatus($request);
        return Redirect::to('allTeachers');
    }

    /**
     *  delete teacher
     */
    protected function deleteClientTeacher($subdomainName,Request $request){
        $teacherId = InputSanitise::inputInt($request->get('teacher_id'));
        $clientTeacher = Clientuser::find($teacherId);
        if(is_object($clientTeacher)){
            DB::connection('mysql2')->beginTransaction();
            try
            {
                if(!empty($clientTeacher->assigned_modules)){
                    $assignmentModules = explode(',', $clientTeacher->assigned_modules);
                    if(in_array(Clientuser::CourseModule, $assignmentModules)){
                        ClientOnlineCategory::assignClientOnlineCategoriesToClientByClientIdByTeacherId($clientTeacher->client_id,$clientTeacher->id);
                        ClientOnlineSubCategory::assignClientOnlineSubCategoriesToClientByClientIdByTeacherId($clientTeacher->client_id,$clientTeacher->id);
                        ClientOnlineCourse::assignClientOnlineCoursesToClientByClientIdByTeacherId($clientTeacher->client_id,$clientTeacher->id);
                        ClientOnlineVideo::assignClientOnlineVideosToClientByClientIdByTeacherId($clientTeacher->client_id,$clientTeacher->id);
                    }
                    if(in_array(Clientuser::TestModule, $assignmentModules)){
                        ClientOnlineTestCategory::assignClientTestCategoriesToClientByClientIdByTeacherId($clientTeacher->client_id,$clientTeacher->id);
                        ClientOnlineTestSubCategory::assignClientTestSubCategoriesToClientByClientIdByTeacherId($clientTeacher->client_id,$clientTeacher->id);
                        ClientOnlineTestSubject::assignClientTestSubjectsToClientByClientIdByTeacherId($clientTeacher->client_id,$clientTeacher->id);
                        ClientOnlineTestSubjectPaper::assignClientTestPapersToClientByClientIdByTeacherId($clientTeacher->client_id,$clientTeacher->id);
                        ClientOnlinePaperSection::assignClientTestPaperSectionsToClientByClientIdByTeacherId($clientTeacher->client_id,$clientTeacher->id);
                        ClientOnlineTestQuestion::assignClientTestQuestionsToClientByClientIdByTeacherId($clientTeacher->client_id,$clientTeacher->id);
                    }
                    if(in_array(Clientuser::BatchModule, $assignmentModules)){
                        ClientBatch::assignClientBatchesToClientByClientIdByTeacherId($clientTeacher->client_id,$clientTeacher->id);
                        ClientUserAttendance::assignClientUserAttendanceToClientByClientIdByTeacherId($clientTeacher->client_id,$clientTeacher->id);
                        ClientOfflinePaperMark::assignClientOfflinePaperMarksToClientByClientIdByTeacherId($clientTeacher->client_id,$clientTeacher->id);
                    }
                    if(in_array(Clientuser::AssignmentModule, $assignmentModules)){
                        ClientAssignmentSubject::assignClientAssignmentSubjectsToClientByClientIdByTeacherId($clientTeacher->client_id,$clientTeacher->id);
                        ClientAssignmentTopic::assignClientAssignmentTopicsToClientByClientIdByTeacherId($clientTeacher->client_id,$clientTeacher->id);
                        ClientAssignmentQuestion::assignClientAssignmentQuestionsToClientByClientIdByTeacherId($clientTeacher->client_id,$clientTeacher->id);
                        ClientAssignmentAnswer::assignClientAssignmentAnswersToClientByClientIdByTeacherId($clientTeacher->client_id,$clientTeacher->id);
                    }
                    if(in_array(Clientuser::EventModule, $assignmentModules)){
                        ClientMessage::assignClientMessagesToClientByClientIdByTeacherId($clientTeacher->client_id,$clientTeacher->id);
                    }
                }
                ClientClass::assignClientClassesToClientByClientIdByTeacherId($clientTeacher->client_id,$clientTeacher->id);
                $clientTeacher->delete();
                DB::connection('mysql2')->commit();
                return Redirect::to('allTeachers')->with('message', 'Teacher deleted successfully!');
            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
                return back()->withErrors('something went wrong while delete teacher.');
            }
        }
        return Redirect::to('allTeachers');
    }
}