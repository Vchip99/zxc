<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Client\ClientBaseController;
use Redirect;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;
use App\Models\Client;
use App\Models\Clientuser;
use App\Models\ClientOfflinePayment;
use App\Models\ClientBatch;
use App\Models\ClientUploadTransaction;

class ClientOfflinePaymentController extends ClientBaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->middleware('client');
    }

    /**
     * Define your validation rules in a property in
     * the controller to reuse the rules.
     */
    protected $validateOfflinePayment = [
        'batch' => 'required',
        'user' => 'required',
        'amount' => 'required'
    ];

    protected function show($subdomainName){
        $payments = ClientOfflinePayment::where('client_id', Auth::guard('client')->user()->id)->orderBy('id', 'desc')->paginate(50);
        return view('client.offlinePayment.list', compact('payments','subdomainName'));
    }

    /**
     *  create offline payment
     */
    protected function create($subdomainName){
        $loginUser = Auth::guard('client')->user();
        $payment = new ClientOfflinePayment;
        $batches = ClientBatch::getBatchesByClientId($loginUser->id);
        return view('client.offlinePayment.create', compact('payment', 'subdomainName', 'batches'));
    }

    /**
     *  store offline payment
     */
    protected function store(Request $request){
        $v = Validator::make($request->all(), $this->validateOfflinePayment);

        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $payment = ClientOfflinePayment::addOrUpdateOfflinePayment($request);
            if(is_object($payment)){
                ClientOfflinePayment::updateDueDateByClientIdByBatchIdByUserId($payment->client_id,$payment->client_batch_id,$payment->clientuser_id,$payment->id);
                DB::connection('mysql2')->commit();
                return Redirect::to('manageOfflinePayments')->with('message', 'Offline Payment created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong while creating offline payment.');
        }
        return Redirect::to('manageOfflinePayments');
    }

    /**
     *  edit offline payment
     */
    protected function edit($subdomainName, $id){
        $id = InputSanitise::inputInt(json_decode($id));
        if(isset($id)){
            $payment = ClientOfflinePayment::find($id);
            if(is_object($payment)){
                $batches = ClientBatch::getBatchesByClientId($payment->client_id);
                $clientBatch = ClientBatch::getBatchById($payment->client_batch_id);
                $users = [];
                if(is_object($clientBatch)){
                    $userIds = explode(',', $clientBatch->student_ids);
                    if(count($userIds) > 0){
                        $users = Clientuser::find($userIds);
                    }
                }
                return view('client.offlinePayment.create', compact('payment', 'subdomainName', 'batches', 'users'));
            }
        }
        return Redirect::to('manageOfflinePayments');
    }

    /**
     *  update offline payment
     */
    protected function update(Request $request){
        $v = Validator::make($request->all(), $this->validateOfflinePayment);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }

        $paymentId = InputSanitise::inputInt(json_decode($request->get('payment_id')));
        if(isset($paymentId)){
            DB::connection('mysql2')->beginTransaction();
            try
            {
                $payment = ClientOfflinePayment::addOrUpdateOfflinePayment($request, true);
                if(is_object($payment)){
                    DB::connection('mysql2')->commit();
                    return Redirect::to('manageOfflinePayments')->with('message', 'Offline Payment updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
                return redirect()->back()->withErrors('something went wrong while updateing offline payment.');
            }
        }
        return Redirect::to('manageOfflinePayments');
    }

    protected function delete(Request $request){
        $paymentId = InputSanitise::inputInt(json_decode($request->get('payment_id')));
        $payment = ClientOfflinePayment::find($paymentId);
        if(is_object($payment)){
            DB::connection('mysql2')->beginTransaction();
            try
            {
                $payment->delete();
                DB::connection('mysql2')->commit();
                return Redirect::to('manageOfflinePayments')->with('message', 'Offline Payment deleted successfully!');
            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
                return back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('manageOfflinePayments');
    }

    protected function batchPayments($subdomainName){
        $clientId = Auth::guard('client')->user()->id;
        $payments = ClientOfflinePayment::getPaymentsByClientId($clientId);
        $batchPayments = [];
        $batchUsers = [];
        $usersPayment = [];
        if(is_object($payments) && false == $payments->isEmpty()){
            foreach($payments as $payment){
                if(empty($batchPayments[$payment->client_batch_id]['batch'])){
                    $batchPayments[$payment->client_batch_id]['batch'] = $payment->batch->name;
                }
                if(empty($batchPayments[$payment->client_batch_id]['amount'])){
                    $batchPayments[$payment->client_batch_id]['amount'] = (int)$payment->amount;
                } else {
                    $batchPayments[$payment->client_batch_id]['amount'] += (int)$payment->amount;
                }
                if(empty($batchUsers[$payment->client_batch_id][$payment->clientuser_id]['user'])){
                    $selectedUser = $payment->user;
                    $batchUsers[$payment->client_batch_id][$payment->clientuser_id]['user'] = $selectedUser->name;
                    $batchUsers[$payment->client_batch_id][$payment->clientuser_id]['email'] = $selectedUser->email;
                    $batchUsers[$payment->client_batch_id][$payment->clientuser_id]['phone'] = $selectedUser->phone;
                }
                if(empty($batchUsers[$payment->client_batch_id][$payment->clientuser_id]['amount'])){
                    $batchUsers[$payment->client_batch_id][$payment->clientuser_id]['amount'] = (int)$payment->amount;
                } else {
                    $batchUsers[$payment->client_batch_id][$payment->clientuser_id]['amount'] += (int)$payment->amount;
                }
                $usersPayment[$payment->client_batch_id][$payment->clientuser_id][] = [
                    'amount' => (int)$payment->amount,
                    'due_date' => $payment->due_date,
                    'comment' => $payment->comment,
                    'date' => date('Y-m-d', strtotime($payment->updated_at)),
                ];
            }
        }
        return view('client.offlinePayment.batchPayments', compact('subdomainName','batchPayments','batchUsers', 'usersPayment'));
    }

    protected function getTotalPaidByBatchIdByUserId(Request $request){
        $result = [];
        $payments = ClientOfflinePayment::getTotalPaidByBatchIdByUserId($request);
        if(is_object($payments) && false == $payments->isEmpty()){
            foreach($payments as $payment){
                if(empty($result['paid'])){
                    $result['paid'] = $payment->amount;
                    $result['total'] = $payment->amount;
                } else {
                    $result['paid'] .= ','.$payment->amount;
                    $result['total'] += $payment->amount;
                }
            }
        }
        return $result;
    }

    protected function duePayments($subdomainName){
        $dueUsers = [];
        $loginUser = Auth::guard('client')->user();
        $batches = ClientBatch::getBatchesByClientId($loginUser->id);
        $duePayments = ClientOfflinePayment::where('client_id',$loginUser->id)->where('due_date', date('Y-m-d'))->get();

        if(is_object($duePayments) && false == $duePayments->isEmpty()){
            foreach($duePayments as $duePayment){
                $userPayments = ClientOfflinePayment::where('client_id',$duePayment->client_id)->where('client_batch_id',$duePayment->client_batch_id)->where('clientuser_id',$duePayment->clientuser_id)->where('created_at','<=', date('Y-m-d').'00-00-00')->get();
                if(is_object($userPayments) && false == $userPayments->isEmpty()){
                    foreach($userPayments as $userPayment){
                        if(empty($dueUsers[$userPayment->client_batch_id][$userPayment->clientuser_id]['user'])){
                            $dueUsers[$userPayment->client_batch_id][$userPayment->clientuser_id]['user'] = $userPayment->user->name;
                        }
                        if(empty($dueUsers[$userPayment->client_batch_id][$userPayment->clientuser_id]['batch'])){
                            $dueUsers[$userPayment->client_batch_id][$userPayment->clientuser_id]['batch'] = $userPayment->batch->name;
                        }
                        $dueUsers[$userPayment->client_batch_id][$userPayment->clientuser_id]['comment'] = $userPayment->comment;
                        if(empty($dueUsers[$userPayment->client_batch_id][$userPayment->clientuser_id]['amount'])){
                            $dueUsers[$userPayment->client_batch_id][$userPayment->clientuser_id]['amount'] = (int)$userPayment->amount;
                        } else {
                            $dueUsers[$userPayment->client_batch_id][$userPayment->clientuser_id]['amount'] += (int)$userPayment->amount;
                        }
                    }
                }
            }
        }
        return view('client.offlinePayment.duePayments', compact('subdomainName','batches', 'dueUsers'));
    }

    protected function getDueStudentsByBatchIdByDueDate(Request $request){
        $batchId = InputSanitise::inputInt($request->get('batch_id'));
        $dueDate = InputSanitise::inputString($request->get('due_date'));
        $dueUsers = [];
        $loginUser = Auth::guard('client')->user();
        $batches = ClientBatch::getBatchesByClientId($loginUser->id);
        $duePayments = ClientOfflinePayment::where('client_id',$loginUser->id)->where('client_batch_id', $batchId)->where('due_date', $dueDate)->get();

        if(is_object($duePayments) && false == $duePayments->isEmpty()){
            foreach($duePayments as $duePayment){
                $userPayments = ClientOfflinePayment::where('client_id',$duePayment->client_id)->where('client_batch_id',$duePayment->client_batch_id)->where('clientuser_id',$duePayment->clientuser_id)->where('created_at','<=', date('Y-m-d').'00-00-00')->get();
                if(is_object($userPayments) && false == $userPayments->isEmpty()){
                    foreach($userPayments as $userPayment){
                        if(empty($dueUsers[$userPayment->client_batch_id][$userPayment->clientuser_id]['user'])){
                            $dueUsers[$userPayment->client_batch_id][$userPayment->clientuser_id]['user'] = $userPayment->user->name;
                        }
                        if(empty($dueUsers[$userPayment->client_batch_id][$userPayment->clientuser_id]['batch'])){
                            $dueUsers[$userPayment->client_batch_id][$userPayment->clientuser_id]['batch'] = $userPayment->batch->name;
                        }
                        $dueUsers[$userPayment->client_batch_id][$userPayment->clientuser_id]['comment'] = $userPayment->comment;
                        if(empty($dueUsers[$userPayment->client_batch_id][$userPayment->clientuser_id]['amount'])){
                            $dueUsers[$userPayment->client_batch_id][$userPayment->clientuser_id]['amount'] = (int)$userPayment->amount;
                        } else {
                            $dueUsers[$userPayment->client_batch_id][$userPayment->clientuser_id]['amount'] += (int)$userPayment->amount;
                        }
                    }
                }
            }
        }
        return $dueUsers;
    }

    protected function userUploadedTransactions($subdomainName){
        $loginUser = Auth::guard('client')->user();
        $transactions = ClientUploadTransaction::where('client_id',$loginUser->id)->orderBy('id', 'desc')->get();
        return view('client.offlinePayment.uploadedTransactions', compact('subdomainName','transactions'));
    }
}