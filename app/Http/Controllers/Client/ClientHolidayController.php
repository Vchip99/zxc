<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Client\ClientBaseController;
use Redirect,Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;
use App\Models\Client;
use App\Models\Clientuser;
use App\Models\ClientBatch;
use App\Models\ClientHoliday;

class ClientHolidayController extends ClientBaseController
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
    protected $validateClientHoliday = [
        'batch' => 'required',
        'date' => 'required',
    ];

    protected function show($subdomainName){
        $holidays = ClientHoliday::where('client_id', Auth::guard('client')->user()->id)->orderBy('id', 'desc')->paginate(50);
        return view('client.holiday.list', compact('holidays','subdomainName'));
    }

    /**
     *  create holiday
     */
    protected function create($subdomainName){
        $loginUser = Auth::guard('client')->user();
        $holiday = new ClientHoliday;
        $batches = ClientBatch::getBatchesByClientId($loginUser->id);
        return view('client.holiday.create', compact('holiday', 'subdomainName', 'batches'));
    }

    /**
     *  store holiday
     */
    protected function store(Request $request){
        $v = Validator::make($request->all(), $this->validateClientHoliday);

        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $holiday = ClientHoliday::addOrUpdateClientHoliday($request);
            if(is_object($holiday)){
                DB::connection('mysql2')->commit();
                $this->sendHolidayMessage($holiday);
                return Redirect::to('manageHolidays')->with('message', 'Holiday created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong while create holiday.');
        }
        return Redirect::to('manageHolidays');
    }

    /**
     *  edit holiday
     */
    protected function edit($subdomainName, $id){
        $id = InputSanitise::inputInt(json_decode($id));
        if(isset($id)){
            $holiday = ClientHoliday::find($id);
            if(is_object($holiday)){
                $batches = ClientBatch::getBatchesByClientId($holiday->client_id);
                return view('client.holiday.create', compact('holiday', 'subdomainName', 'batches'));
            }
        }
        return Redirect::to('manageHolidays');
    }

    /**
     *  update holiday
     */
    protected function update(Request $request){
        $v = Validator::make($request->all(), $this->validateClientHoliday);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        $holidayId = InputSanitise::inputInt($request->get('holiday_id'));
        if(isset($holidayId)){
            DB::connection('mysql2')->beginTransaction();
            try
            {
                $holiday = ClientHoliday::addOrUpdateClientHoliday($request, true);
                if(is_object($holiday)){
                    DB::connection('mysql2')->commit();
                    $this->sendHolidayMessage($holiday);
                    return Redirect::to('manageHolidays')->with('message', 'Holiday updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
                return redirect()->back()->withErrors('something went wrong while update holiday.');
            }
        }
        return Redirect::to('manageHolidays');
    }

    /**
     *  delete holiday
     */
    protected function delete(Request $request){
        $holidayId = InputSanitise::inputInt($request->get('holiday_id'));
        $holiday = ClientHoliday::find($holidayId);
        if(is_object($holiday)){
            DB::connection('mysql2')->beginTransaction();
            try
            {
                $holiday->delete();
                DB::connection('mysql2')->commit();
                return Redirect::to('manageHolidays')->with('message', 'Holiday deleted successfully!');
            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
                return back()->withErrors('something went wrong while delete holiday.');
            }
        }
        return Redirect::to('manageHolidays');
    }

    protected function sendHolidayMessage($holiday){
        $client = Auth::guard('client')->user();
        $sendSmsStatus = $client->holiday_sms;
        if(Client::None != $sendSmsStatus){
            $allBatchStudents = [];
            if($holiday->client_batch_id > 0){
                $clientBatch = ClientBatch::where('client_id',$holiday->client_id)->where('id',$holiday->client_batch_id)->first();
                if(is_object($clientBatch)){
                    if(!empty($clientBatch->student_ids)){
                        $allBatchStudents = explode(',', $clientBatch->student_ids);
                    }
                }
                $batchName = $clientBatch->name;
            } else {
                $batchName = 'All';
            }
            InputSanitise::sendHolidaySms($allBatchStudents,$sendSmsStatus,$holiday->client_batch_id,$batchName,$holiday->note,$client->name,$client->id);
        }
    }
}