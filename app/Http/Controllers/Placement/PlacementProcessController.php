<?php

namespace App\Http\Controllers\Placement;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PlacementArea;
use App\Models\PlacementCompany;
use App\Models\CompanyDetails;
use App\Models\PlacementProcess;
use App\Models\ExamPattern;
use Redirect,Validator, Auth, DB;
use App\Libraries\InputSanitise;

class PlacementProcessController extends Controller
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
    protected $validatePlacementProcess = [
        'area' => 'required',
        'company' => 'required',
        'selection_process' => 'required',
        'academic_criteria' => 'required',
        'aptitude_syllabus' => 'required',
        'hr_questions' => 'required',
        'job_link' => 'required',
    ];


    /**
     *  show all PlacementCompany
     */
    protected function show(){
    	$placementProcesses = PlacementProcess::paginate();
    	return view('placementProcess.list', compact('placementProcesses'));
    }

    /**
     *  show create UI PlacementCompan
     */
    protected function create(){
    	$placementAreas = PlacementArea::all();
        $placementCompanies = [];
        $examPatterns = [];
    	$placementProcess = new PlacementProcess;
        $examPatternCounts = 1;
    	return view('placementProcess.create', compact('placementAreas', 'placementProcess', 'placementCompanies', 'examPatterns', 'examPatternCounts'));
    }

    /**
     *  store placementCompany
     */
    protected function store(Request $request){
        $v = Validator::make($request->all(), $this->validatePlacementProcess);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::beginTransaction();
        try
        {
            $placementProcess = PlacementProcess::addOrUpdatePlacementProcess($request);
            if(is_object($placementProcess)){
                DB::commit();
                return Redirect::to('admin/managePlacementProcess')->with('message', 'Placement Process created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('admin/managePlacementProcess');
    }

    /**
     *  edit placementCompany
     */
    protected function edit($id){
    	$id = InputSanitise::inputInt(json_decode($id));
    	if(isset($id)){
    		$placementProcess = PlacementProcess::find($id);
    		if(is_object($placementProcess)){
    			$placementAreas = PlacementArea::all();
                $placementCompanies = PlacementCompany::getPlacementCompaniesByArea($placementProcess->placement_area_id);
    			$examPatterns = ExamPattern::where('placement_company_id', $placementProcess->placement_company_id)->get();
                $examPatternCounts = count($examPatterns);
                return view('placementProcess.create', compact('placementAreas', 'placementProcess', 'placementCompanies', 'examPatterns', 'examPatternCounts'));
    		}
        }
		return Redirect::to('admin/managePlacementProcess');
    }

    /**
     *  update placementCompany
     */
    protected function update( Request $request){
        $v = Validator::make($request->all(), $this->validatePlacementProcess);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }

    	$placementProcessId = InputSanitise::inputInt($request->get('placement_process_id'));
    	if(isset($placementProcessId)){
            DB::beginTransaction();
            try
            {
                $placementProcess = PlacementProcess::addOrUpdatePlacementProcess($request, true);
                if(is_object($placementProcess)){
                    DB::commit();
                    return Redirect::to('admin/managePlacementProcess')->with('message', 'Placement Process updated successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return redirect()->back()->withErrors('something went wrong.');
            }
        }
		return Redirect::to('admin/managePlacementProcess');
    }

    protected function checkPlacementCompanyProcesss(Request $request){
        return PlacementProcess::checkPlacementCompanyProcesss($request->id);
    }

}
