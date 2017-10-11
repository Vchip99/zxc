<?php

namespace App\Http\Controllers\Placement;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PlacementArea;
use App\Models\PlacementCompany;
use App\Models\CompanyDetails;
use App\Models\PlacementProcess;
use Redirect;
use Validator, Auth, DB;
use App\Libraries\InputSanitise;

class PlacementCompanyController extends Controller
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
    protected $validatePlacementCompany = [
        'area' => 'required',
        'company' => 'required',
    ];


    /**
     *  show all PlacementCompant
     */
    protected function show(){
    	$placementCompanies = PlacementCompany::paginate();
    	return view('placementCompany.list', compact('placementCompanies'));
    }

    /**
     *  show create UI PlacementCompant
     */
    protected function create(){
    	$placementAreas = PlacementArea::all();
    	$placementCompany = new PlacementCompany;
    	return view('placementCompany.create', compact('placementAreas', 'placementCompany'));
    }

    /**
     *  store placementCompany
     */
    protected function store(Request $request){
        $v = Validator::make($request->all(), $this->validatePlacementCompany);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::beginTransaction();
        try
        {
            $placementCompany = PlacementCompany::addOrUpdatePlacementCompany($request);
            if(is_object($placementCompany)){
                DB::commit();
                return Redirect::to('admin/managePlacementCompany')->with('message', 'Placement Company created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('admin/managePlacementCompany');
    }

    /**
     *  edit placementCompany
     */
    protected function edit($id){
    	$id = InputSanitise::inputInt(json_decode($id));
    	if(isset($id)){
    		$placementCompany = PlacementCompany::find($id);
    		if(is_object($placementCompany)){
    			$placementAreas = PlacementArea::all();
    			return view('placementCompany.create', compact('placementAreas', 'placementCompany'));
    		}
        }
		return Redirect::to('admin/managePlacementCompany');
    }

    /**
     *  update placementCompany
     */
    protected function update( Request $request){
        $v = Validator::make($request->all(), $this->validatePlacementCompany);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }

    	$companyId = InputSanitise::inputInt($request->get('company_id'));
    	if(isset($companyId)){
            DB::beginTransaction();
            try
            {
                $placementCompany = PlacementCompany::addOrUpdatePlacementCompany($request, true);
                if(is_object($placementCompany)){
                    DB::commit();
                    return Redirect::to('admin/managePlacementCompany')->with('message', 'Placement Company created successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return redirect()->back()->withErrors('something went wrong.');
            }
        }
		return Redirect::to('admin/managePlacementCompany');
    }

   /**
     *  delete placement Company
     */
    protected function delete(Request $request){
        $companyId = InputSanitise::inputInt($request->get('company_id'));
        if(isset($companyId)){
            $placementCompany = PlacementCompany::find($companyId);
            if(is_object($placementCompany)){
                DB::beginTransaction();
                try
                {
                    $companyDetail = CompanyDetails::find($placementCompany->id);
                    if(is_object($companyDetail)){
                        $placementProcess = PlacementProcess::find($companyDetail->placement_company_id);
                        if(is_object($placementProcess)){
                            if(is_object($placementProcess->deleteFaqs) && false == $placementProcess->deleteFaqs->isEmpty()){
                                foreach($placementProcess->deleteFaqs as $placementFaq){
                                    $placementFaq->delete();
                                }
                            }
                            $placementProcess->deletePlacementProcessComments();
                            $placementProcess->delete();
                        }
                        $companyDetail->delete();
                    }
                    $placementCompany->delete();
                    DB::commit();
                    return Redirect::to('admin/managePlacementCompany')->with('message', 'Placement Company deleted successfully!');
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return redirect()->back()->withErrors('something went wrong.');
                }
            }
        }
        return Redirect::to('admin/managePlacementCompany');
    }

}
