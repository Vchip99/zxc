<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ClientOfflinePayment;
use App\Libraries\InputSanitise;
use App\Models\Client;
use Auth;
use DB;

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
        $smsUsersArr = [];
        $duePayments = ClientOfflinePayment::where('due_date', date('Y-m-d'))->get();
        if(is_object($duePayments) && false == $duePayments->isEmpty()){
            $this->info('Start Sending Due Sms.');
            $sendCount = 0;
            foreach($duePayments as $duePayment){
                $user = $duePayment->user;
                $userName = $user->name;
                $userPhone = $user->phone;
                if(!empty($userPhone) && 10 == strlen($userPhone)){
                    $batchName = $duePayment->batch->name;
                    $smsUsersArr[$duePayment->client_id][] = [
                        'name' => $userName,
                        'phone' => $userPhone,
                        'batch' => $batchName
                    ];
                }
            }

            if(count($smsUsersArr) > 0){
                DB::connection('mysql2')->beginTransaction();
                try
                {
                    foreach($smsUsersArr as $clientId => $smsUsers){
                        $client =  Client::find($clientId);
                        if(is_object($client)){
                            if(count($smsUsersArr[$clientId]) >  $client->debit_sms_count){
                                return InputSanitise::sendClientCreditSms($client->phone,$client->name);
                            } else {
                                foreach($smsUsers as $smsUser){
                                    $result = InputSanitise::sendOfflineDueSms($smsUser['phone'], $smsUser['name'], $smsUser['batch'], $client->name);
                                    InputSanitise::setSmsCountStats($client);
                                    $sendCount += 1;
                                    $this->info($result);
                                }
                                $client->save();
                            }
                        }
                    }
                    DB::connection('mysql2')->commit();
                }
                catch(\Exception $e)
                {
                    DB::connection('mysql2')->rollback();
                }
            }
            $this->info('Total Sent Due Sms count -'.$sendCount);
        }
    }
}
