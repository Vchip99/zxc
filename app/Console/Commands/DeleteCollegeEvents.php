<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Libraries\InputSanitise;
use App\Models\CollegeMessage;
use Auth,DB,File;


class DeleteCollegeEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deletecollegeevents:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'delete college events';

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
        // delete yesterdays college events
        DB::connection('mysql')->beginTransaction();
        try
        {
            $currentDate = date('Y-m-d');
            $collegeEvents = CollegeMessage::where('start_date', '!=','')->where('end_date','!=','')->where('end_date', '<',$currentDate)->get();
            if(is_object($collegeEvents) && false == $collegeEvents->isEmpty()){
                $deleteCount = 0;
                foreach($collegeEvents as $collegeEvent){
                    $dir = dirname($collegeEvent->photo);
                    exec('sudo rm -R '.public_path($dir));
                    $collegeEvent->delete();
                    $deleteCount++;
                }
                $this->info('No. of events deleted -'.$deleteCount);
                DB::connection('mysql')->commit();
            } else {
                $this->info('No events for delete.');
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql')->rollback();
        }
    }
}
