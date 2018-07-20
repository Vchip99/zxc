<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Client\ClientBaseController;
use Redirect;
use Validator, Session, Auth, DB;
use App\Libraries\InputSanitise;
use App\Models\Client;
use App\Models\Clientuser;
use App\Models\ClientOfflinePaper;
use App\Models\ClientBatch;
use App\Models\ClientOfflinePaperMark;

class ClientOfflinePaperController extends ClientBaseController
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
    protected $validateOfflinePaper = [
        'batch' => 'required',
        'name' => 'required',
        'marks' => 'required',
    ];

    protected function show($subdomainName){
        $papers = ClientOfflinePaper::where('client_id', Auth::guard('client')->user()->id)->paginate();
        return view('client.offlinePaper.list', compact('papers','subdomainName'));
    }

    /**
     *  create offline paper
     */
    protected function create($subdomainName){
        $loginUser = Auth::guard('client')->user();
        $paper = new ClientOfflinePaper;
        $batches = ClientBatch::getBatchesByClientId($loginUser->id);
        return view('client.offlinePaper.create', compact('paper', 'subdomainName', 'batches'));
    }

    /**
     *  store offline paper
     */
    protected function store(Request $request){
        $v = Validator::make($request->all(), $this->validateOfflinePaper);

        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $paper = ClientOfflinePaper::addOrUpdateOfflinePaper($request);
            if(is_object($paper)){
                DB::connection('mysql2')->commit();
                return Redirect::to('manageOfflinePaper')->with('message', 'Offline paper created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('manageOfflinePaper');
    }

    /**
     *  edit offline paper
     */
    protected function edit($subdomainName, $id){
        $id = InputSanitise::inputInt(json_decode($id));
        if(isset($id)){
            $paper = ClientOfflinePaper::find($id);
            if(is_object($paper)){
                $batches = ClientBatch::getBatchesByClientId($paper->client_id);
                return view('client.offlinePaper.create', compact('paper', 'subdomainName', 'batches'));
            }
        }
        return Redirect::to('manageOfflinePaper');
    }

    /**
     *  update offline paper
     */
    protected function update(Request $request){
        $v = Validator::make($request->all(), $this->validateOfflinePaper);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }

        $subjectId = InputSanitise::inputInt($request->get('subject_id'));
        if(isset($subjectId)){
            DB::connection('mysql2')->beginTransaction();
            try
            {
                $paper = ClientOfflinePaper::addOrUpdateOfflinePaper($request, true);
                if(is_object($paper)){
                    DB::connection('mysql2')->commit();
                    return Redirect::to('manageOfflinePaper')->with('message', 'Offline paper updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
                return redirect()->back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('manageOfflinePaper');
    }

    protected function delete(Request $request){
        $paperId = InputSanitise::inputInt($request->get('paper_id'));
        $paper = ClientOfflinePaper::find($paperId);
        if(is_object($paper)){
            DB::connection('mysql2')->beginTransaction();
            try
            {
                ClientOfflinePaperMark::deleteClientOfflinePaperMarkByBatchIdByPaperIdByClientId($paper->client_batch_id,$paper->id,$paper->client_id);
                $paper->delete();
                DB::connection('mysql2')->commit();
                return Redirect::to('manageOfflinePaper')->with('message', 'Offline paper deleted successfully!');
            }
            catch(\Exception $e)
            {
                DB::connection('mysql2')->rollback();
                return back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('manageOfflinePaper');
    }

    protected function getOfflinePapersByBatchId(Request $request){
        $clientBatchId = InputSanitise::inputInt($request->get('batch_id'));
        return ClientOfflinePaper::getOfflinePapersByBatchId($clientBatchId);
    }

    protected function manageOfflineExam($subdomainName){
        $loginUser = Auth::guard('client')->user();
        $papers = [];
        $batches = ClientBatch::getBatchesByClientId($loginUser->id);
        return view('client.offlinePaper.offlinePaperMarks', compact('papers', 'subdomainName', 'batches'));
    }

    protected function getBatchStudentsAndMarksByBatchIdByPaperId(Request $request){
        $batchId   = InputSanitise::inputInt($request->get('batch_id'));
        $clientBatch = ClientBatch::getBatchById($batchId);
        $result = [];
        $batchUsers = [];
        if(is_object($clientBatch)){
            $userIds = explode(',', $clientBatch->student_ids);
            if(count($userIds) > 0){
                $batchUsers = Clientuser::whereIn('id',$userIds)->select('id','name')->get();
            }
        }
        $result['batchUsers'] = $batchUsers;
        $paperMarks = ClientOfflinePaperMark::getOfflinePaperMarksByBatchIdByPaperId($request);
        $result['studentMarks'] = [];
        if(is_object($paperMarks) && false == $paperMarks->isEmpty()){
            foreach($paperMarks as $paperMark){
                $result['studentMarks'][$paperMark->clientuser_id] = $paperMark;
            }
        }
        return $result;
    }

    protected function assignOfflinePaperMarks(Request $request){
        DB::connection('mysql2')->beginTransaction();
        try
        {
            ClientOfflinePaperMark::assignOfflinePaperMarks($request);
            DB::connection('mysql2')->commit();
            return Redirect::to('manageOfflineExam')->with('message', 'Assign marks to student successfully!');
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return back()->withErrors('something went wrong.');
        }
        return Redirect::to('manageOfflineExam');
    }

}