<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PlacementArea;
use App\Models\PlacementCompany;
use App\Models\PlacementProcess;
use App\Models\CompanyDetails;
use App\Models\PlacementFaq;
use App\Models\ExamPattern;
use App\Models\PlacementExperiance;
use DB, Auth, Session;
use Validator, Redirect,Hash;
use App\Libraries\InputSanitise;


class PlacementController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected function show(){
        $companyDetails = CompanyDetails::first();
        $placementAreas = PlacementArea::getPlacementAreas();
        if(is_object($companyDetails)){
    	   $placementProcess = PlacementProcess::where('placement_company_id', $companyDetails->placement_company_id)->first();
            if(is_object($placementProcess)){
                $placementFaqs = PlacementFaq::where('placement_company_id', $placementProcess->placement_company_id)->get();
                $examPatterns = ExamPattern::where('placement_company_id', $placementProcess->placement_company_id)->get();
                $placementExperiances= PlacementExperiance::where('placement_company_id', $placementProcess->placement_company_id)->get();
            } else {
                $placementFaqs = [];
                $examPatterns = [];
                $placementExperiances = [];
            }
            $placementCompanies = [];
            $selectedCompany = 0;
            $selectedArea = 0;
    	   return view('placement.placements', compact('placementProcess', 'placementAreas', 'placementCompanies', 'companyDetails', 'selectedCompany', 'selectedArea', 'placementFaqs', 'examPatterns', 'placementExperiances'));
        } else {
            $companyDetails = '';
            $placementCompanies = [];
            $selectedCompany = 0;
            $selectedArea = 0;
            $placementFaqs = [];
            $examPatterns = [];
            $placementExperiances = [];
            return view('placement.placements', compact('placementProcess', 'placementAreas', 'placementCompanies', 'companyDetails', 'selectedCompany', 'selectedArea', 'placementFaqs', 'examPatterns', 'placementExperiances'));
        }

    }

    protected function showPlacements(Request $request){
        $companyDetails = CompanyDetails::where('placement_company_id',$request->get('company_id'))->first();
        $placementProcess = PlacementProcess::where('placement_company_id',$request->get('company_id'))->first();
        $placementAreas = PlacementArea::getPlacementAreas();
        if(is_object($companyDetails) && is_object($placementProcess)){
            $placementCompanies = PlacementCompany::where('placement_area_id',$companyDetails->placement_area_id)->get();
            $selectedCompany = $request->get('company_id');
            $selectedArea = $companyDetails->placement_area_id;
            $placementFaqs = PlacementFaq::where('placement_company_id', $companyDetails->placement_company_id)->get();
            $examPatterns = ExamPattern::where('placement_company_id', $companyDetails->placement_company_id)->get();
            $placementExperiances= PlacementExperiance::where('placement_company_id', $companyDetails->placement_company_id)->get();
            return view('placement.placements', compact('placementProcess', 'placementAreas', 'placementCompanies', 'companyDetails', 'selectedCompany', 'selectedArea', 'placementFaqs', 'examPatterns', 'placementExperiances'));
        }
        return Redirect::to('placements');
    }

    protected function getPlacementCompaniesByArea(Request $request){
        return PlacementCompany::getPlacementCompaniesByArea($request->id);
    }

    protected function getPlacementCompaniesByAreaForFront(Request $request){
        return PlacementCompany::getPlacementCompaniesByAreaForFront($request->id);
    }

    protected function createPlacementExperiance(Request $request){
        DB::beginTransaction();
        try
        {
            PlacementExperiance::createPlacementExperiance($request);
            DB::commit();
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('placements');
    }

    protected function placementExperiance($id){
        $id = json_decode($id);
        if(isset($id)){
            $placementExperiance = PlacementExperiance::find($id);
            if(is_object($placementExperiance)){
                $placementExperiances = placementExperiance::all();
                return view('placement.placementExperiance', compact('placementExperiance', 'placementExperiances'));
            }
        }
        return Redirect::to('placements');
    }

}