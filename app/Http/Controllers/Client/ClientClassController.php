<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Client\ClientBaseController;
use Redirect,Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;
use App\Models\Client;
use App\Models\Clientuser;
use App\Models\ClientBatch;
use App\Models\ClientClass;
use App\Models\ClientHoliday;
use App\Models\ClientExam;
use App\Models\ClientNotice;

class ClientClassController extends ClientBaseController
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
    protected $validateClientClass = [
        'batch' => 'required',
        'teacher' => 'required',
        'subject' => 'required',
        'topic' => 'required',
        'date' => 'required',
        'from_time' => 'required',
        'to_time' => 'required',
    ];

    protected function show($subdomainName){
        $classes = ClientClass::where('client_id', Auth::guard('client')->user()->id)->orderBy('id', 'desc')->paginate(50);
        return view('client.class.list', compact('classes','subdomainName'));
    }

    /**
     *  create class
     */
    protected function create($subdomainName){
        $loginUser = Auth::guard('client')->user();
        $class = new ClientClass;
        $batches = ClientBatch::getBatchesByClientId($loginUser->id);
        $teachers = Clientuser::getTeachersByClientId($loginUser->id);
        $clientName = $loginUser->name;
        return view('client.class.create', compact('class', 'subdomainName', 'batches', 'teachers','clientName'));
    }

    /**
     *  store class
     */
    protected function store(Request $request){
        $v = Validator::make($request->all(), $this->validateClientClass);

        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $clientClass = ClientClass::addOrUpdateClientClass($request);
            if(is_object($clientClass)){
                DB::connection('mysql2')->commit();
                return Redirect::to('manageClasses')->with('message', 'Class created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong while create class.');
        }
        return Redirect::to('manageClasses');
    }

    /**
     *  edit class
     */
    protected function edit($subdomainName, $id){
        $id = InputSanitise::inputInt(json_decode($id));
        if(isset($id)){
            $class = ClientClass::find($id);
            if(is_object($class)){
                $batches = ClientBatch::getBatchesByClientId($class->client_id);
                $teachers = Clientuser::getTeachersByClientId($class->client_id);
                $loginUser = Auth::guard('client')->user();
                $clientName = $loginUser->name;
                return view('client.class.create', compact('class', 'subdomainName', 'batches', 'teachers','clientName'));
            }
        }
        return Redirect::to('manageClasses');
    }

    /**
     *  update class
     */
    protected function update(Request $request){
        $v = Validator::make($request->all(), $this->validateClientClass);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        $classId = InputSanitise::inputInt($request->get('class_id'));
        if(isset($classId)){
            DB::connection('mysql2')->beginTransaction();
            try
            {
                $clientClass = ClientClass::addOrUpdateClientClass($request, true);
                if(is_object($clientClass)){
                    DB::connection('mysql2')->commit();
                    return Redirect::to('manageClasses')->with('message', 'Class updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
                return redirect()->back()->withErrors('something went wrong while update class.');
            }
        }
        return Redirect::to('manageClasses');
    }

    /**
     *  delete class
     */
    protected function delete(Request $request){
        $classId = InputSanitise::inputInt($request->get('class_id'));
        $class = ClientClass::find($classId);
        if(is_object($class)){
            DB::connection('mysql2')->beginTransaction();
            try
            {
                $class->delete();
                DB::connection('mysql2')->commit();
                return Redirect::to('manageClasses')->with('message', 'Class deleted successfully!');
            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
                return back()->withErrors('something went wrong while delete class.');
            }
        }
        return Redirect::to('manageClasses');
    }

    protected function manageSchedules($subdomainName){
        $results = [];
        $calendarData = [];
        $allBatches = [];
        $dayColours = '';
        $clientId = Auth::guard('client')->user()->id;
        $batches = ClientBatch::where('client_id', $clientId)->get();
        if(is_object($batches) && false == $batches->isEmpty()){
            $allBatches[0] = 'All';
            foreach($batches as $batch){
                $allBatches[$batch->id] = $batch->name;
            }
        }
        $emergencyNotices = ClientNotice::where('client_id', $clientId)->where('is_emergency', 1)->get();
        if(is_object($emergencyNotices) && false == $emergencyNotices->isEmpty()){
            foreach($emergencyNotices as $notice){
                if(!isset($results[$notice->date])){
                    $results[$notice->date] = [
                        'start' => $notice->date,
                        'color' => 'yellow',
                    ];
                }
                $calendarData[$notice->date]['emergency_notices'][] = [
                    'title' => $notice->notice,
                    'batch' => $allBatches[$notice->client_batch_id]
                ];
            }
        }
        $exams = ClientExam::where('client_id', $clientId )->get();
        if(is_object($exams) && false == $exams->isEmpty()){
            foreach($exams as $exam){
                if(!isset($results[$exam->date])){
                    $results[$exam->date] = [
                        'start' => $exam->date,
                        'color' => 'red',
                    ];
                }
                $calendarData[$exam->date]['exams'][] = [
                    'title' => $exam->name,
                    'subject' => $exam->subject,
                    'topic' => $exam->topic,
                    'from' => $exam->from_time,
                    'to' => $exam->to_time,
                    'batch' => $allBatches[$exam->client_batch_id]
                ];
            }
        }
        $holidays = ClientHoliday::where('client_id', $clientId )->get();
        if(is_object($holidays) && false == $holidays->isEmpty()){
            foreach($holidays as $holiday){
                if(!isset($results[$holiday->date])){
                    $results[$holiday->date] = [
                        'start' => $holiday->date,
                        'color' => 'green',
                    ];
                }
                $calendarData[$holiday->date]['holiday'][] = [
                    'title' => ($holiday->note)?:'Holiday',
                    'batch' => $allBatches[$holiday->client_batch_id]
                ];
            }
        }
        $notices = ClientNotice::where('client_id', $clientId)->where('is_emergency', 0)->get();
        if(is_object($notices) && false == $notices->isEmpty()){
            foreach($notices as $notice){
                if(!isset($results[$notice->date])){
                    $results[$notice->date] = [
                        'start' => $notice->date,
                        'color' => 'blue',
                    ];
                }
                $calendarData[$notice->date]['notices'][] = [
                    'title' => $notice->notice,
                    'batch' => $allBatches[$notice->client_batch_id]
                ];
            }
        }
        $classes = ClientClass::where('client_id', $clientId )->get();
        if(is_object($classes) && false == $classes->isEmpty()){
            foreach($classes as $class){
                if(!isset($results[$class->date])){
                    $results[$class->date] = [
                        'start' => $class->date,
                        'color' => '#e6004e',
                    ];
                }
                $calendarData[$class->date]['classes'][] = [
                    'subject' => $class->subject,
                    'topic' => $class->topic,
                    'from' => $class->from_time,
                    'to' => $class->to_time,
                    'batch' => $allBatches[$class->client_batch_id]
                ];
            }
        }

        if(count($results) > 0){
            foreach($results as $result){
                if(empty($dayColours)){
                    $dayColours = $result['start'].':'.$result['color'];
                } else {
                    $dayColours .= ','.$result['start'].':'.$result['color'];
                }
            }
        }
        return view('client.class.scheduleCalendar', compact('subdomainName','dayColours','calendarData'));
    }
}