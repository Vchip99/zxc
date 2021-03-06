<?php

namespace App\Http\Controllers\Placement;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ApplyJob;
use Redirect,Validator, Auth, DB;
use App\Libraries\InputSanitise;
use App\Mail\MailToSubscribedUser;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Notification;

class PlacementApplyJobController extends Controller
{
    /**
     *  check admin have permission or not, if not redirect to admin/home
     */
    public function __construct() {
        $this->middleware(function ($request, $next) {
            $adminUser = Auth::guard('admin')->user();
            if(is_object($adminUser)){
                if($adminUser->hasRole('admin')){
                    return $next($request);
                }
            }
            return Redirect::to('admin/home');
        });
    }

    /**
     * Define your validation rules in a property in
     * the controller to reuse the rules.
     */
    protected $validateApplyJob = [
        'company' => 'required',
        'job_description' => 'required',
        'mock_test' => 'required',
        'job_url' => 'required',
    ];

    /**
     *  show all applyJobs
     */
    protected function show(){
        $applyJobs = ApplyJob::paginate();
        return view('applyJob.list', compact('applyJobs'));
    }

    /**
     *  show create UI applyJob
     */
    protected function create(){
        $applyJob = new ApplyJob;
        return view('applyJob.create', compact('applyJob'));
    }

    /**
     *  store ApplyJob
     */
    protected function store(Request $request){
        $v = Validator::make($request->all(), $this->validateApplyJob);
        if($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        InputSanitise::deleteCacheByString('vchip:placements*');
        DB::beginTransaction();
        try
        {
            $applyJob = ApplyJob::addOrUpdateApplyJob($request);
            if(is_object($applyJob)){
                $messageBody = '';
                $notificationMessage = 'A new job vacancy of company: <a href="'.$request->root().'/placements/" target="_blank">'.$applyJob->company.'</a> has been added.';
                Notification::addNotification($notificationMessage, Notification::ADMINCOMPANYJOB, $applyJob->id);
                DB::commit();
                $subscriedUsers = User::where('admin_approve', 1)->where('verified', 1)->select('email')->get();
                $allUsers = $subscriedUsers->chunk(100);
                set_time_limit(0);
                if(false == $allUsers->isEmpty()){
                    foreach($allUsers as $selectedUsers){
                        $messageBody .= '<p> Dear User</p>';
                        $messageBody .= '<p>'.$notificationMessage.' please have a look once.</p>';
                        $messageBody .= '<p><b> Thanks and Regard, </b></p>';
                        $messageBody .= '<b><a href="https://vchiptech.com"> Vchip Technology Team </a></b><br/>';
                        $messageBody .= '<b> More about us... </b><br/>';
                        $messageBody .= '<b><a href="https://vchipedu.com"> Digital Education </a></b><br/>';
                        $messageBody .= '<b><a href="mailto:info@vchiptech.com" target="_blank">E-mail</a></b><br/>';
                        $mailSubject = 'Vchipedu added a new company job';
                        Mail::bcc($selectedUsers)->queue(new MailToSubscribedUser($messageBody, $mailSubject));
                    }
                }
                return Redirect::to('admin/manageApplyJob')->with('message', 'Apply job created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('admin/manageApplyJob');
    }

    /**
     *  edit ApplyJob
     */
    protected function edit($id){
        $id = InputSanitise::inputInt(json_decode($id));
        if(isset($id)){
            $applyJob = ApplyJob::find($id);
            if(is_object($applyJob)){
                return view('applyJob.create', compact('applyJob'));
            }
        }
        return Redirect::to('admin/manageApplyJob');
    }


    /**
     *  update ApplyJob
     */
    protected function update( Request $request){
        $v = Validator::make($request->all(), $this->validateApplyJob);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        InputSanitise::deleteCacheByString('vchip:placements*');
        $applyJobId = InputSanitise::inputInt($request->get('apply_job_id'));
        if(isset($applyJobId)){
            DB::beginTransaction();
            try
            {
                $applyJob = ApplyJob::addOrUpdateApplyJob($request, true);
                if(is_object($applyJob)){
                    DB::commit();
                    return Redirect::to('admin/manageApplyJob')->with('message', 'Apply job updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return redirect()->back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('admin/manageApplyJob');
    }

    /**
     *  delete ApplyJob
     */
    protected function delete(Request $request){
        InputSanitise::deleteCacheByString('vchip:placements*');
        $applyJobId = InputSanitise::inputInt($request->get('apply_job_id'));
        if(isset($applyJobId)){
            $applyJob = ApplyJob::find($applyJobId);
            if(is_object($applyJob)){
                DB::beginTransaction();
                try
                {
                    $applyJob->delete();
                    DB::commit();
                    return Redirect::to('admin/manageApplyJob')->with('message', 'Apply job deleted successfully!');
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return redirect()->back()->withErrors('something went wrong.');
                }
            }
        }
        return Redirect::to('admin/manageApplyJob');
    }
}