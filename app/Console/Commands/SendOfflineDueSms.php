<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ClientOfflinePayment;
use App\Libraries\InputSanitise;
use Auth;

class SendOfflineDueSms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sendofflineduesms:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Offline Due Sms';

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
        $loginUser = Auth::guard('client')->user();
        $duePayments = ClientOfflinePayment::where('due_date', date('Y-m-d'))->get();
        if(is_object($duePayments) && false == $duePayments->isEmpty()){
            $this->info('Start Sending Due Sms.');
            $sendCount = 0;
            foreach($duePayments as $duePayment){
                    $user = $duePayment->user;
                    $userName = $user->name;
                    $userPhone = $user->phone;
                if(!empty($userPhone)){
                    $batchName = $duePayment->batch->name;
                    $clientName = $duePayment->client->name;
                    $result = InputSanitise::sendOfflineDueSms($userPhone, $userName, $batchName, $clientName);
                    $sendCount += 1;
                    $this->info($result);
                }
            }
            $this->info('Total Sent Due Sms count -'.$sendCount);
        }
    }
}
