<?php
namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Auth,Redirect,View,DB,Mail,Session,Validator,Cache,File;
use Illuminate\Http\RedirectResponse;
use App\Libraries\InputSanitise;
use App\Models\User;
use App\Models\Add;
use App\Models\MentorArea;
use App\Models\MentorSkill;
use App\Models\Mentor;
use App\Models\MentorSchedule;
use App\Models\MentorChatMessage;
use App\Models\MentorRating;
use App\Mail\MentorSignUp;
use Intervention\Image\ImageManagerStatic as Image;

class MentorHomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {

    }

    public function mentors(Request $request){
        if('local' == \Config::get('app.env')){
            $addUrl = 'http://localvchip.com/createAd';
        } else {
            $addUrl = 'https://vchipedu.com/createAd';
        }
        $date = date('Y-m-d');
        $ads = Add::getAdds($request->url(),$date);
        $mentors = Mentor::all();
        $skillNames = [];
        $mentorSkills = MentorSkill::all();
        if(is_object($mentorSkills) && false == $mentorSkills->isEmpty()){
            foreach($mentorSkills as $mentorSkill){
                $skillNames[$mentorSkill->id] = $mentorSkill->name;
            }
        }
        $mentorAreas = MentorArea::all();
        $reviewData = [];
        $ratingUsers = [];
        $userNames = [];
        $allRatings = MentorRating::all();
        if(is_object($allRatings) && false == $allRatings->isEmpty()){
            foreach($allRatings as $rating){
                $reviewData[$rating->mentor_id]['rating'][$rating->user_id] = [ 'rating' => $rating->rating,'review' => $rating->review, 'review_id' => $rating->id, 'updated_at' => $rating->updated_at->diffForHumans()];
                $ratingUsers[] = $rating->user_id;
            }
            foreach($reviewData as $dataId => $rating){
                $ratingSum = 0.0;
                foreach($rating as $userRatings){
                    foreach($userRatings as $userId => $userRating){
                        $ratingSum = (double) $ratingSum + (double) $userRating['rating'];
                    }
                    $reviewData[$dataId]['avg']  = $ratingSum/count($userRatings);
                }
            }
        }
        if(count($ratingUsers) > 0){
            $users = User::find($ratingUsers);
            if(is_object($users) && false == $users->isEmpty()){
                foreach($users as $user){
                    $userNames[$user->id] = [ 'name' => $user->name,'photo' => $user->photo];
                }
            }
        }
        return view('mentor.front.mentors', compact('ads','addUrl','mentors','skillNames','mentorAreas','reviewData','userNames'));
    }

    public function faq(Request $request){
        if('local' == \Config::get('app.env')){
            $addUrl = 'http://localvchip.com/createAd';
        } else {
            $addUrl = 'https://vchipedu.com/createAd';
        }
        $date = date('Y-m-d');
        $ads = Add::getAdds($request->url(),$date);
        return view('mentor.front.faq', compact('ads','addUrl'));
    }

    public function mentorinfo($subdomain,$id,Request $request){
        $mentor = Mentor::find(json_decode($id));
        if(!is_object($mentor)){
            return Redirect::to('mentors');
        }
        $skillNames = [];
        $mentorSkills = MentorSkill::all();
        if(is_object($mentorSkills) && false == $mentorSkills->isEmpty()){
            foreach($mentorSkills as $mentorSkill){
                $skillNames[$mentorSkill->id] = $mentorSkill->name;
            }
        }
        $mentors = Mentor::all();
        $reviewData = [];
        $ratingUsers = [];
        $userNames = [];
        $allRatings = MentorRating::where('mentor_id', $id)->get();
        if(is_object($allRatings) && false == $allRatings->isEmpty()){
            foreach($allRatings as $rating){
                $reviewData[$rating->mentor_id]['rating'][$rating->user_id] = [ 'rating' => $rating->rating,'review' => $rating->review, 'review_id' => $rating->id, 'updated_at' => $rating->updated_at->diffForHumans()];
                $ratingUsers[] = $rating->user_id;
            }
            foreach($reviewData as $dataId => $rating){
                $ratingSum = 0.0;
                foreach($rating as $userRatings){
                    foreach($userRatings as $userId => $userRating){
                        $ratingSum = (double) $ratingSum + (double) $userRating['rating'];
                    }
                    $reviewData[$dataId]['avg']  = $ratingSum/count($userRatings);
                }
            }
        }
        if(count($ratingUsers) > 0){
            $users = User::find($ratingUsers);
            if(is_object($users) && false == $users->isEmpty()){
                foreach($users as $user){
                    $userNames[$user->id] = [ 'name' => $user->name,'photo' => $user->photo];
                }
            }
        }
        return view('mentor.front.mentorinfo', compact('mentor','skillNames','mentors','reviewData','userNames'));
    }

    protected function mentorSignup(Request $request){
        $areaNames = [];
        $mentorAreas = MentorArea::all();
        if(is_object($mentorAreas) && false == $mentorAreas->isEmpty()){
            foreach($mentorAreas as $mentorArea){
                $areaNames[$mentorArea->id] = $mentorArea->name;
            }
        }
        return view('mentor.front.signup_mentor', compact('areaNames'));
    }

    protected function getMentorSkillsByAreaId(Request $request){
        return MentorSkill::getMentorSkillsByAreaId($request->area);
    }

    protected function mentorSignin(Request $request){
        return view('mentor.front.signin_mentor');
    }

    protected function sendMentorSignInOtp(Request $request){
        $mobile = $request->get('mobile');
        return InputSanitise::sendOtp($mobile);
    }

    protected function getMentorsByAreaIdBySkillId(Request $request){
        $result = [];
        $skillNames = [];
        $mentorSkills = MentorSkill::all();
        if(is_object($mentorSkills) && false == $mentorSkills->isEmpty()){
            foreach($mentorSkills as $mentorSkill){
                $result['skillNames'][$mentorSkill->id] = $mentorSkill->name;
            }
        } else {
            $result['skillNames'] = [];
        }
        $result['mentors'] = Mentor::getMentorsByAreaIdBySkillId($request);

        $ratingUsers = [];
        $allRatings = MentorRating::all();
        if(is_object($allRatings) && false == $allRatings->isEmpty()){
            foreach($allRatings as $rating){
                $result['ratingData'][$rating->mentor_id]['rating'][$rating->user_id] = [ 'rating' => $rating->rating,'review' => $rating->review, 'review_id' => $rating->id, 'updated_at' => $rating->updated_at->diffForHumans()];
                $ratingUsers[] = $rating->user_id;
            }
            foreach($result['ratingData'] as $dataId => $rating){
                $ratingSum = 0.0;
                foreach($rating as $userRatings){
                    foreach($userRatings as $userId => $userRating){
                        $ratingSum = (double) $ratingSum + (double) $userRating['rating'];
                    }
                    $result['ratingData'][$dataId]['avg']  = $ratingSum/count($userRatings);
                }
            }
        } else {
            $result['ratingData'] = [];
        }
        if(count($ratingUsers) > 0){
            $users = User::find($ratingUsers);
            if(is_object($users) && false == $users->isEmpty()){
                foreach($users as $user){
                    if(is_file($user->photo) && true == preg_match('/userStorage/',$user->photo)){
                        $isImageExist = 'system';
                    } else if(!empty($user->photo) && false == preg_match('/userStorage/',$user->photo)){
                        $isImageExist = 'other';
                    } else {
                        $isImageExist = 'false';
                    }
                    $result['userNames'][$user->id] = [ 'name' => $user->name,'photo' => $user->photo,'image_exist' => $isImageExist];
                }
            }
        } else {
            $result['userNames'] = [];
        }
        return $result;
    }

    protected function userLogin(Request $request){
        if(Auth::guard('web')->attempt([
                    'email' => $request->email,
                    'password' => $request->password
                ])){
            $request->session()->regenerate();
            return 'true';
        } else {
            return 'false';
        }
    }

    protected function userLogout(Request $request){
        Auth::guard()->logout();
        Session::flush();
        Session::regenerate();
        return redirect()->back()->with('message','Logout Successfully.');
    }

    protected function schedules(Request $request){
        if(is_object(Auth::user())){
            $dayColours = '';
            $calendarData = [];
            $results = [];
            $mentorSchedules = MentorSchedule::getSchedulesByUserId(Auth::user()->id);
            if(is_object($mentorSchedules) && false == $mentorSchedules->isEmpty()){
                foreach($mentorSchedules as $mentorSchedule){
                    if(!isset($results[$mentorSchedule->meeting_date])){
                        if(1 == $mentorSchedule->type){
                            $color = 'green';
                        } else if(2 == $mentorSchedule->type){
                            $color = 'yellow';
                        } else {
                            $color = '#e6004e';
                        }
                        $results[$mentorSchedule->meeting_date] = [
                            'start' => $mentorSchedule->meeting_date,
                            'color' => $color,
                        ];
                    }
                    $calendarData[$mentorSchedule->meeting_date][$mentorSchedule->id] = [
                        'title' => $mentorSchedule->mentor,
                        'from' => $mentorSchedule->from_time,
                        'to' => $mentorSchedule->to_time,
                        'comment' => $mentorSchedule->comment,
                        'type' => $mentorSchedule->type,
                    ];
                }
            }
            if(count($results) > 0){
                foreach($results as $result){
                    if(empty($dayColours)){
                        $dayColours = $result['start'].':'.$result['color'];
                    } else {
                        $dayColours .= ','.$result['start'].':'.$result['color'];
                    }
                }
            }
            $areaNames = [];
            $mentorAreas = MentorArea::all();
            if(is_object($mentorAreas) && false == $mentorAreas->isEmpty()){
                foreach($mentorAreas as $mentorArea){
                    $areaNames[$mentorArea->id] = $mentorArea->name;
                }
            }
            return view('mentor.front.calendar', compact('dayColours','calendarData','areaNames'));
        }
        return Redirect::to('/');
    }

    protected function getMentorsBySkillId(Request $request){
        return Mentor::getMentorsBySkillId($request);
    }

    protected function createUserSchedule(Request $request){
        DB::beginTransaction();
        try
        {
            $schedule = MentorSchedule::addMentorSchedule($request);
            if(is_object($schedule)){
                DB::commit();
                return Redirect::to('schedules')->with('message','Schedule created successfully.');
            }
        }
        catch(Exception $e)
        {
            DB::rollback();
            return back();
        }
        return Redirect::to('schedules');
    }

    protected function messages(){
        if(is_object(Auth::user())){
            $mentorIds = [];
            $mentors = [];
            $chatMessages = MentorChatMessage::where('sender_id', Auth::user()->id)
                ->orWhere('receiver_id', Auth::user()->id)->get();
            if(is_object($chatMessages) && false == $chatMessages->isEmpty()){
                foreach($chatMessages as $chatMessage){
                    if(Auth::user()->id == $chatMessage->sender_id && 0 == $chatMessage->generated_by_mentor){
                        $mentorIds[] = $chatMessage->receiver_id;
                    } else {
                        $mentorIds[] = $chatMessage->sender_id;
                    }
                }
                $chatMentors = Mentor::find(array_unique($mentorIds));
                if(is_object($chatMentors) && false == $chatMentors->isEmpty()){
                    foreach($chatMentors as $chatMentor){
                        $mentors[] = [
                            'id' => $chatMentor->id,
                            'name' => $chatMentor->name,
                            'email' => $chatMentor->email,
                            'photo' => $chatMentor->photo,
                        ];
                    }
                }
            }
            return view('mentor.front.messages', compact('mentors'));
        }
        return Redirect::to('/');
    }

    protected function menteeSignup(Request $request){
        return view('mentor.front.signup_mentee');
    }

    protected function giveMentorRating(Request $request){
        DB::beginTransaction();
        try {
            $rating = MentorRating::addOrUpdateMentorRating($request);
            if(is_object($rating)){
                DB::commit();
                return redirect()->back()->with('message', 'Rating given successfully.');;
            }
        }
        catch(Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors([$e->getMessage()]);
        }
        return redirect()->back();
    }
}
