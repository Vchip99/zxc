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
            $allPlan = [];
            $plans = Plan::all();
            if(is_object($plans) && false == $plans->isEmpty()){
                foreach($plans as $plan){
                    $allPlan[$plan->id] = $plan;
                }
            }
            DB::connection('mysql')->beginTransaction();
            $clients = Client::all();
            if(is_object($clients) && false == $clients->isEmpty()){
                foreach($clients as $client){
                    $currentClientPlan = ClientPlan::where('client_id', $client->id)->where('plan_id','>' ,1)->where('degrade_plan', 0)->orderBy('id', 'desc')->first();
                    if(is_object($currentClientPlan)){
                        $dueDate = date('Y-m-d', strtotime('+1 month', strtotime($currentClientPlan->start_date)));
                        $startDate = date('Y-m-d', strtotime('+1 day'));
                        $endDate = date('Y-m-d', strtotime('+1 years', strtotime($startDate)));
                        if(date('Y-m-d') == $currentClientPlan->end_date && 'Credit' == $currentClientPlan->payment_status){
                            $this->info('trigger mail to convey do the payment and generate bill:'.$client->name.'.<br/>');
                            $clientPlanArray = [
                                        'client_id' => $client->id,
                                        'plan_id' => $client->plan_id,
                                        'plan_amount' => $allPlan[$client->plan_id]->amount,
                                        'final_amount' => $allPlan[$client->plan_id]->amount,
                                        'start_date' => $startDate,
                                        'end_date' => $endDate,
                                        'payment_status' => '',
                                        'degrade_plan' => 0
                                    ];
                            ClientPlan::addFirstTimeClientPlan($clientPlanArray);
                            DB::connection('mysql')->commit();
                            $data['client'] = $client->name;
                            $data['plan'] = $allPlan[$client->plan_id]->name;
                            $data['price'] = $allPlan[$client->plan_id]->amount;
                            $data['startDate'] = $startDate;
                            $data['endDate'] = $endDate;
                            Mail::to($client->email)->send(new BillGenerated($data));
                        } else if(date('Y-m-d') == $dueDate && empty($currentClientPlan->payment_status)){
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
