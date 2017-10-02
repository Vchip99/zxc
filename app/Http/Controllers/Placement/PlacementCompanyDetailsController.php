<?php

namespace App\Http\Controllers\Placement;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PlacementArea;
use App\Models\PlacementCompany;
use App\Models\CompanyDetails;
use Redirect;
use Validator, Auth, DB;
use App\Libraries\InputSanitise;

class PlacementCompanyDetailsController extends Controller
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
    protected $validatePlacementCompanyDetails = [
        'area' => 'required',
        'company' => 'required',
        'about_company' => 'required',
        'industry_type' => 'required',
        'founded_year' => 'required',
        'founder_name' => 'required',
        'headquarters' => 'required',
        'ceo' => 'required',
        'products' => 'required',
        'website' => 'required',
        'mock_test_link' => 'required',
    ];


    /**
     *  show all PlacementCompant
     */
    protected function show(){
    	$companyDetails = CompanyDetails::paginate();
    	return view('placementCompanyDetails.list', compact('companyDetails'));
    }

    /**
     *  show create UI PlacementCompant
     */
    protected function create(){
    	$placementAreas = PlacementArea::all();
        $placementCompanies = [];
    	$companyDetail = new CompanyDetails;
    	return view('placementCompanyDetails.create', compact('placementAreas', 'companyDetail', 'placementCompanies'));
    }

    /**
     *  store placementCompany
     */
    protected function store(Request $request){
        $v = Validator::make($request->all(), $this->validatePlacementCompanyDetails);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::beginTransaction();
        try
        {
            $companyDetail = CompanyDetails::addOrUpdateCompanyDetails($request);
            if(is_object($companyDetail)){
                DB::commit();
                return Redirect::to('admin/managePlacementCompanyDetails')->with('message', 'Placement Company Details created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('admin/managePlacementCompanyDetails');
    }

    /**
     *  edit placementCompany
     */
    protected function edit($id){
    	$id = InputSanitise::inputInt(json_decode($id));
    	if(isset($id)){
    		$companyDetail = CompanyDetails::find($id);
    		if(is_object($companyDetail)){
    			$placementAreas = PlacementArea::all();
                $placementCompanies = PlacementCompany::getPlacementCompaniesByArea($companyDetail->placement_area_id);
    			return view('placementCompanyDetails.create', compact('placementAreas', 'companyDetail', 'placementCompanies'));
    		}
        }
		return Redirect::to('admin/managePlacementCompanyDetails');
    }

    /**
     *  update placementCompany
     */
    protected function update( Request $request){
        $v = Validator::make($request->all(), $this->validatePlacementCompanyDetails);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }

    	$companyDetailsId = InputSanitise::inputInt($request->get('company_details_id'));
    	if(isset($companyDetailsId)){
            DB::beginTransaction();
            try
            {
                $companyDetail = CompanyDetails::addOrUpdateCompanyDetails($request, true);
                if(is_object($companyDetail)){
                    DB::commit();
                    return Redirect::to('admin/managePlacementCompanyDetails')->with('message', 'Placement Company Details created successfully!');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return redirect()->back()->withErrors('something went wrong.');
            }
        }
		return Redirect::to('admin/managePlacementCompanyDetails');
    }

    protected function getPlacementCompaniesByArea(Request $request){
        return PlacementCompany::getPlacementCompaniesByArea($request->id);
    }

    protected function checkCompanyDetails(Request $request){
        return CompanyDetails::checkCompanyDetails($request->id);
    }
}

