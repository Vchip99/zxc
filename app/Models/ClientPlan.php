<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB, Auth;
use App\Libraries\InputSanitise;
use App\Models\Plan;
use App\Models\Client;

class ClientPlan extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['client_id', 'plan_id', 'plan_amount', 'final_amount', 'start_date', 'end_date', 'payment_status', 'degrade_plan'];

    protected static function addFirstTimeClientPlan($clientPlanArray){
    	$clientPlan = new static;
        $clientPlan->client_id = $clientPlanArray['client_id'];
        $clientPlan->plan_id = $clientPlanArray['plan_id'];
        $clientPlan->plan_amount = $clientPlanArray['plan_amount'];
        $clientPlan->final_amount = $clientPlanArray['final_amount'];
        $clientPlan->start_date = $clientPlanArray['start_date'];
        $clientPlan->end_date = $clientPlanArray['end_date'];
        $clientPlan->payment_status = $clientPlanArray['payment_status'];
        $clientPlan->degrade_plan = $clientPlanArray['degrade_plan'];
        $clientPlan->save();
    	return $clientPlan;
    }

    public function plan(){
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    public function client(){
        $client =Client::find($this->client_id);
        if(is_object($client)){
            return $client->name;
        } else {
            return 'deleted';
        }
    }

    protected static function getLastPaidClientPlan(){
        return static::join('payments', 'payments.client_plan_id', '=', 'client_plans.id')
            ->where('client_plans.client_id', Auth::guard('client')->user()->id)
            ->where('client_plans.end_date', '>=', date('Y-m-d'))
            ->select('client_plans.*')->orderBy('client_plans.id', 'desc')->first();
    }

    protected static function getLastClientPlanForBill(){
        return static::where('client_id', Auth::guard('client')->user()->id)
            ->where('start_date', '<=', date('Y-m-d'))
            ->where('end_date', '>=', date('Y-m-d'))
            ->orderBy('id', 'desc')->first();
    }

    protected static function getLastClientPlan(){
        return static::where('client_id', Auth::guard('client')->user()->id)
            ->orderBy('id', 'desc')->first();
    }

    protected static function getClientPlanByPlanId($planId){
        return static::where('client_id', Auth::guard('client')->user()->id)
            ->where('plan_id', $planId)
            ->where('start_date','<=',date('Y-m-d'))->where('end_date','>=',date('Y-m-d'))
            ->orderBy('id', 'desc')->first();
    }
}
