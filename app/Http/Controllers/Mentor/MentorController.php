<?php

namespace App\Http\Controllers\Mentor;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use App\Models\Mentor;
use App\Models\User;
use App\Models\MentorArea;
use App\Models\MentorSkill;
use App\Models\MentorSchedule;
use App\Models\MentorChatMessage;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;

class MentorController extends Controller
{
    /**
     *  check admin have permission or not, if not redirect to home
     */
	public function __construct() {
        $this->middleware('mentor');
    }

    /**
     *  show profile
     */
    protected function mentorProfile(){
        $skills = '';
        $mentorSkillArr = [];
        $mentorSkills = [];
        $loginUser = Auth::guard('mentor')->user();
        $mentorAreas = MentorArea::all();
        if(!empty($loginUser->skills)){
            $mentorSkillArr = explode(',',$loginUser->skills);
            $mentorSkills = MentorSkill::getMentorSkillsByAreaId($loginUser->mentor_area_id);
            if(is_object($mentorSkills) && false == $mentorSkills->isEmpty()){
                foreach($mentorSkills as $index => $mentorSkill){
                    if(in_array($mentorSkill->id,$mentorSkillArr)){
                        if(empty($skills)){
                            $skills = $mentorSkill->name;
                        } else {
                            $skills .= ','.$mentorSkill->name;
                        }
                    }
                }
            }
        }
        return view('mentor.dashboard.profile', compact('loginUser','skills','mentorAreas','mentorSkills','mentorSkillArr'));
    }

    protected function updateMentorProfile(Request $request){
        DB::beginTransaction();
        try
        {
            $mentor = Mentor::updateMentorProfile($request);
            if(is_object($mentor)){
                DB::commit();
                return Redirect::to('mentor/profile')->with('message','Profile updated successfully.');
            }
        }
        catch(Exception $e)
        {
            DB::rollback();
            return back();
        }
        return Redirect::to('mentor/profile');
    }

    /**
     *  show calendar
     */
    protected function mentorCalendar(){
        $dayColours = '';
        $calendarData = [];
        $results = [];
        $mentorSchedules = MentorSchedule::getSchedulesByMentorId(Auth::guard('mentor')->user()->id);
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
                    'title' => $mentorSchedule->user,
                    'email' => $mentorSchedule->email,
                    'mobile' => $mentorSchedule->mobile,
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
        return view('mentor.dashboard.calendar', compact('dayColours','calendarData'));
    }

    protected function getStudentByEmail(Request $request){
        return User::getUserByEmailId($request->email);
    }

    protected function createSchedule(Request $request){
        DB::beginTransaction();
        try
        {
            $schedule = MentorSchedule::addMentorSchedule($request);
            if(is_object($schedule)){
                DB::commit();
                return Redirect::to('mentor/calendar')->with('message','Schedule created successfully.');
            }
        }
        catch(Exception $e)
        {
            DB::rollback();
            return back();
        }
        return Redirect::to('mentor/calendar');
    }

    protected function changeMeetingTypeById(Request $request){
        DB::beginTransaction();
        try
        {
            $result = MentorSchedule::changeMeetingTypeById($request);
            if('true' == $result){
                DB::commit();
            }
        }
        catch(Exception $e)
        {
            DB::rollback();
            return back();
        }
        return;
    }

    protected function messages(){
        $users = [];
        $userIds = [];
        $chatMessages = MentorChatMessage::where('sender_id', Auth::guard('mentor')->user()->id)
            ->orWhere('receiver_id', Auth::guard('mentor')->user()->id)->get();

        if(is_object($chatMessages) && false == $chatMessages->isEmpty()){
            foreach($chatMessages as $chatMessage){
                if(Auth::guard('mentor')->user()->id == $chatMessage->sender_id && 1 == $chatMessage->generated_by_mentor){
                    $userIds[] = $chatMessage->receiver_id;
                } else {
                    $userIds[] = $chatMessage->sender_id;
                }
            }
            $chatUsers = User::find(array_unique($userIds));
            if(is_object($chatUsers) && false == $chatUsers->isEmpty()){
                foreach($chatUsers as $chatUser){
                    $users[] = [
                        'id' => $chatUser->id,
                        'name' => $chatUser->name,
                        'email' => $chatUser->email,
                        'photo' => $chatUser->photo,
                    ];
                }
            }
        }
        return view('mentor.dashboard.messages', compact('users'));
    }

}
