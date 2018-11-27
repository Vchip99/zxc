<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Client\ClientBaseController;
use Redirect,Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;
use App\Models\Client;
use App\Models\Clientuser;
use App\Models\ClientReceipt;

class ClientReceiptController extends ClientBaseController
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
    protected $validateClientReceipt = [
        'offline_receipt_by' => 'required',
        'offline_address' => 'required',
        'is_offline_gst_applied' => 'required',
        'is_same_details' => 'required'
    ];

    protected function show($subdomainName){
        $receipt = ClientReceipt::where('client_id', Auth::guard('client')->user()->id)->first();
        // dd($receipt);
        if(!is_object($receipt)){
            $receipt = new ClientReceipt;
        }
        return view('client.receipt.create', compact('receipt','subdomainName'));
    }


    /**
     *  store
     */
    protected function store(Request $request){
        $v = Validator::make($request->all(), $this->validateClientReceipt);

        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $receipt = ClientReceipt::addOrUpdateClientReceipt($request);
            if(is_object($receipt)){
                DB::connection('mysql2')->commit();
                return Redirect::to('manageReceipt')->with('message', 'Receipt created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong while create receipt.');
        }
        return Redirect::to('manageReceipt');
    }

    /**
     *  update
     */
    protected function update(Request $request){
        $v = Validator::make($request->all(), $this->validateClientReceipt);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        $receiptId = InputSanitise::inputInt($request->get('receipt_id'));
        if(isset($receiptId)){
            DB::connection('mysql2')->beginTransaction();
            try
            {
                $receipt = ClientReceipt::addOrUpdateClientReceipt($request, true);
                if(is_object($receipt)){
                    DB::connection('mysql2')->commit();
                    return Redirect::to('manageReceipt')->with('message', 'Receipt updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
                return redirect()->back()->withErrors('something went wrong while update receipt.');
            }
        }
        return Redirect::to('manageReceipt');
    }
}