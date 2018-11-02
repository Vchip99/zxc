<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\Client;
use App\Models\ClientPlan;
use App\Models\Plan;
use App\Mail\BillGenerated;
use App\Mail\DegradePlan;
use DB;

class GenerateBillAndDeActivatePlan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generatebillanddeactivateplan:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Bill And DeActivate Plan';

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
        set_time_limit(0);
        DB::connection('mysql2')->beginTransaction();
        try
        {
            DB::connection('mysql')->beginTransaction();
            $clients = Client::all();
            if(is_object($clients) && false == $clients->isEmpty()){
                foreach($clients as $client){
                    //get current plan record of client to downgrade plan if not paid after one month of due date
                    $currentClientPlan = ClientPlan::where('client_id', $client->id)->where('plan_id','>' ,1)->where('degrade_plan', 0)->orderBy('id', 'desc')->first();
                    if(is_object($currentClientPlan)){
                        $dueDate = date('Y-m-d', strtotime('+1 month', strtotime($currentClientPlan->start_date)));
                        // downgrade plan to free
                        if(date('Y-m-d') == $dueDate && 'Credit' == $currentClientPlan->payment_status){
                            $this->info('degrade plan:'.$client->name.'<br/>');
                            $currentClientPlan->delete();
                            $clientPlanArray = [
                                        'client_id' => $client->id,
                                        'plan_id' => 1,
                                        'plan_amount' => 0,
                                        'final_amount' => 0,
                                        'start_date' => $startDate,
                                        'end_date' => $endDate,
                                        'payment_status' => 'free',
                                        'degrade_plan' => 0
                                    ];
                            ClientPlan::addFirstTimeClientPlan($clientPlanArray);
                            $client->plan_id = 1;
                            $client->save();
                            DB::connection('mysql')->commit();
                            DB::connection('mysql2')->commit();
                            $data['client'] = $client->name;
                            Mail::to($client->email)->send(new DegradePlan($data));
                        }
                    }
                    // downgrade client current plan after compeleted of current plan if have record in client plan table
                    $clientPlans = ClientPlan::where('client_id', $client->id)->where('start_date','<=',date('Y-m-d'))->where('end_date','>=',date('Y-m-d'))->orderBy('plan_id', 'desc')->get();
                    if(is_object($clientPlans) && false == $clientPlans->isEmpty()){
                        foreach($clientPlans as $index => $clientPlan){
                            if(0 == $index){
                                if($client->plan_id != $clientPlan->plan_id){
                                    $client->plan_id = $clientPlan->plan_id;
                                    $client->save();
                                    DB::connection('mysql2')->commit();
                                    $this->info('The client '.$client->name.'\'s plan has been changed.<br/>');
                                }
                            }
                        }
                    }
                }
            }
        }
        catch(Exception $e)
        {
            DB::connection('mysql')->rollback();
            DB::connection('mysql2')->rollback();
        }
    }
}
