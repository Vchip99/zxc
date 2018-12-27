<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MentorSchedule;
use App\Libraries\InputSanitise;

class SendMentorScheduleMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sendmentorschedulemessages:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Mentor Schedule Messages';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // send mentor meeting messages to mentor and mantee
        $currentDate = date('Y-m-d');
        $meetings = MentorSchedule::where('meeting_date', '!=','')->where('meeting_date', '=',$currentDate)->get();
        if(is_object($meetings) && false == $meetings->isEmpty()){
            $messageCount = 0;
            foreach($meetings as $meeting){
                $msg = 'Hi,Today you have mentor meeting from '.$meeting->from_time.' to '.$meeting->to_time.'. for more details, please check schedule calender.';
                $message =mb_strimwidth($msg, 0, 150, "...");

                $userMobile = $meeting->getUser($meeting->user_id)->phone;
                if(!empty($userMobile)){
                    InputSanitise::sendSms($userMobile,$message);
                    $messageCount = $messageCount + 1;
                }
                $mentorMobile = $meeting->getMentor($meeting->mentor_id)->mobile;
                if(!empty($mentorMobile)){
                    InputSanitise::sendSms($mentorMobile,$message);
                    $messageCount = $messageCount + 1;
                }
            }
            $this->info('No. of messages send -'.$messageCount );
        } else {
            $this->info('No messages send.');
        }
    }
}
