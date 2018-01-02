<?php

namespace App\Http\Controllers\ZeroToHero;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ZeroToHero;
use App\Models\Designation;
use App\Models\Area;
use App\Models\Notification;
use Validator, Session, Auth, DB, Redirect;
use App\Libraries\InputSanitise;
use App\Mail\MailToSubscribedUser;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class ZeroToHeroController extends Controller
{
    /**
     *  check admin have permission or not, if not redirect to home
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
    protected $validateZeroToHero = [
        'hero' => 'required|string',
        'designation' => 'required|integer',
        'area' => 'required|integer',
        'url' => 'required|string',
        'release_date' => 'required|date',
    ];

    /**
     * show all hero
     */
    protected function show(){
    	$heros = ZeroToHero::paginate();
    	return view('zerotohero.list', compact('heros'));
    }

     /**
     * show all hero
     */
    protected function create(){
    	$designations = Designation::all();
    	$hero = new ZeroToHero;
    	$areas = [];
    	return view('zerotohero.create', compact('designations', 'hero', 'areas'));
    }

    /**
     *  store hero
     */
    protected function store(Request $request){
        $v = Validator::make($request->all(), $this->validateZeroToHero);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::beginTransaction();
        try
        {
            $hero = ZeroToHero::addOrUpdateZeroToHero($request);
            if(is_object($hero)){
                $messageBody = '';
                $notificationMessage = 'A new zero to hero video: <a href="'.$request->root().'/heros/'.$hero->id.'">'.$hero->name.'</a> has been added.';
                Notification::addNotification($notificationMessage, Notification::ADMINZEROTOHERO, $hero->id);
                DB::commit();
                $subscriedUsers = User::where('admin_approve', 1)->where('verified', 1)->select('email')->get();
                $allUsers = $subscriedUsers->chunk(100);
                set_time_limit(0);
                if(count($allUsers) > 0){
                    foreach($allUsers as $selectedUsers){
                        $messageBody .= '<p> Dear User</p>';
                        $messageBody .= '<p>'.$notificationMessage.' please have a look once.</p>';
                        $messageBody .= '<p><b> Thanks and Regard, </b></p>';
                        $messageBody .= '<b><a href="https://vchiptech.com"> Vchip Technology Team </a></b><br/>';
                        $messageBody .= '<b> More about us... </b><br/>';
                        $messageBody .= '<b><a href="https://vchipedu.com"> Digital Education </a></b><br/>';
                        $messageBody .= '<b><a href="mailto:info@vchiptech.com" target="_blank">E-mail</a></b><br/>';
                        $mailSubject = 'Vchipedu added a new hero to zero video';
                        Mail::bcc($selectedUsers)->queue(new MailToSubscribedUser($messageBody, $mailSubject));
                    }
                }
                return Redirect::to('admin/manageZeroToHero')->with('message', 'Zero To Hero created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
		return Redirect::to('admin/manageZeroToHero');
    }

    /**
     * edit hero
     */
    protected function edit($id){
    	$heroId = InputSanitise::inputInt(json_decode($id));
    	if(isset($heroId)){
    		$hero = ZeroToHero::find($heroId);
    		if(is_object($hero)){
    			$designations = Designation::all();
    			$areas = Area::getAreasByDesignation($hero->designation_id);

    			return view('zerotohero.create', compact('designations', 'hero', 'areas'));
    		}
    	}
		return Redirect::to('admin/manageZeroToHero');
    }

    /**
     * update hero
     */
    protected function update(Request $request){
        $v = Validator::make($request->all(), $this->validateZeroToHero);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        $heroId = InputSanitise::inputInt($request->get('hero_id'));
        if(isset($heroId)){
            DB::beginTransaction();
            try
            {
                $hero = ZeroToHero::addOrUpdateZeroToHero($request, true);
                if(is_object($hero)){
                    DB::commit();
                    return Redirect::to('admin/manageZeroToHero')->with('message', 'Zero To Hero updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('admin/manageZeroToHero');
    }

    /**
     *  delete hero
     */
    protected function delete(Request $request){
    	$heroId = InputSanitise::inputInt($request->get('hero_id'));
    	if(isset($heroId)){
    		$hero = ZeroToHero::find($heroId);
    		if(is_object($hero)){
                DB::beginTransaction();
                try
                {
        			$hero->delete();
                    DB::commit();
                    return Redirect::to('admin/manageZeroToHero')->with('message', 'Zero To Hero deleted successfully!');
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return redirect()->back()->withErrors('something went wrong.');
                }
    		}
    	}
		return Redirect::to('admin/manageZeroToHero');
    }

}
