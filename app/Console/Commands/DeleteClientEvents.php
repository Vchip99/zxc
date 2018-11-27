<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Libraries\InputSanitise;
use App\Models\ClientMessage;
use Auth,DB,File;

class DeleteClientEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deleteclientevents:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'delete client events';

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
        // delete yesterdays client events
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $currentDate = date('Y-m-d');
            $clientEvents = ClientMessage::where('start_date', '!=','')->where('end_date','!=','')->where('end_date', '<',$currentDate)->get();
            if(is_object($clientEvents) && false == $clientEvents->isEmpty()){
                $deleteCount = 0;
                foreach($clientEvents as $clientEvent){
                    $dir = dirname($clientEvent->photo);
                    exec('sudo rm -R '.public_path($dir));
                    $clientEvent->delete();
                    $deleteCount++;
                }
                $this->info('No. of events deleted -'.$deleteCount);
                DB::connection('mysql2')->commit();
            } else {
                $this->info('No events for delete.');
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
        }
    }
}
