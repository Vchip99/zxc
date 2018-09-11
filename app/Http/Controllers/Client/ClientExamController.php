<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Client\ClientBaseController;
use Redirect,Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;
use App\Models\Client;
use App\Models\Clientuser;
use App\Models\ClientBatch;
use App\Models\ClientExam;

class ClientExamController extends ClientBaseController
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
    protected $validateClientExam = [
        'batch' => 'required',
        'name' => 'required',
        'subject' => 'required',
        'topic' => 'required',
        'date' => 'required',
        'from_time' => 'required',
        'to_time' => 'required',
    ];

    protected function show($subdomainName){
        $exams = ClientExam::where('client_id', Auth::guard('client')->user()->id)->orderBy('id', 'desc')->paginate(50);
        return view('client.exam.list', compact('exams','subdomainName'));
    }

    /**
     *  create exam
     */
    protected function create($subdomainName){
        $loginUser = Auth::guard('client')->user();
        $exam = new ClientExam;
        $batches = ClientBatch::getBatchesByClientId($loginUser->id);
        return view('client.exam.create', compact('exam', 'subdomainName', 'batches'));
    }

    /**
     *  store exam
     */
    protected function store(Request $request){
        $v = Validator::make($request->all(), $this->validateClientExam);

        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $clientExam = ClientExam::addOrUpdateClientExam($request);
            if(is_object($clientExam)){
                $this->sendExam($clientExam,false);
                DB::connection('mysql2')->commit();
                return Redirect::to('manageExams')->with('message', 'Exam created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong while create exam.');
        }
        return Redirect::to('manageExams');
    }

    /**
     *  edit exam
     */
    protected function edit($subdomainName, $id){
        $id = InputSanitise::inputInt(json_decode($id));
        if(isset($id)){
            $exam = ClientExam::find($id);
            if(is_object($exam)){
                $batches = ClientBatch::getBatchesByClientId($exam->client_id);
                return view('client.exam.create', compact('exam', 'subdomainName', 'batches'));
            }
        }
        return Redirect::to('manageExams');
    }

    /**
     *  update exam
     */
    protected function update(Request $request){
        $v = Validator::make($request->all(), $this->validateClientExam);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        $examId = InputSanitise::inputInt($request->get('exam_id'));
        if(isset($examId)){
            DB::connection('mysql2')->beginTransaction();
            try
            {
                $clientExam = ClientExam::addOrUpdateClientExam($request, true);
                if(is_object($clientExam)){
                    $this->sendExam($clientExam,true);
                    DB::connection('mysql2')->commit();
                    return Redirect::to('manageExams')->with('message', 'Exam updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
                return redirect()->back()->withErrors('something went wrong while update exam.');
            }
        }
        return Redirect::to('manageExams');
    }

    /**
     *  delete exam
     */
    protected function delete(Request $request){
        $examId = InputSanitise::inputInt($request->get('exam_id'));
        $exam = ClientExam::find($examId);
        if(is_object($exam)){
            DB::connection('mysql2')->beginTransaction();
            try
            {
                $exam->delete();
                DB::connection('mysql2')->commit();
                return Redirect::to('manageExams')->with('message', 'Exam deleted successfully!');
            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
                return back()->withErrors('something went wrong while delete exam.');
            }
        }
        return Redirect::to('manageExams');
    }

    protected function sendExam($clientExam,$isUpdate){
        $client = Auth::guard('client')->user();
        $sendSmsStatus = $client->exam_sms;
        if(Client::None != $sendSmsStatus){
            $allBatchStudents = [];
            if($clientExam->client_batch_id > 0){
                $clientBatch = ClientBatch::where('client_id',$clientExam->client_id)->where('id',$clientExam->client_batch_id)->first();
                if(is_object($clientBatch)){
                    if(!empty($clientBatch->student_ids)){
                        $allBatchStudents = explode(',', $clientBatch->student_ids);
                    }
                }
                $batchName = $clientBatch->name;
            } else {
                $batchName = 'All';
            }
            InputSanitise::sendExamSms($allBatchStudents,$sendSmsStatus,$clientExam->client_batch_id,$batchName,$clientExam->name,$clientExam->date,$clientExam->from_time,$clientExam->to_time,$client, $isUpdate);
        }
        return;
    }
}