<?php

namespace App\Http\Controllers\Placement;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PlacementArea;
use App\Models\PlacementCompany;
use App\Models\CompanyDetails;
use App\Models\PlacementFaq;
use Redirect,Validator, Auth, DB;
use App\Libraries\InputSanitise;

class PlacementFaqController extends Controller
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
    protected $validatePlacementFaq = [
        'area' => 'required',
        'company' => 'required',
    ];

    /**
     *  show all PlacementFaq
     */
    protected function show(){
        $placementFaqs = PlacementFaq::paginate();
        return view('placementFaq.list', compact('placementFaqs'));
    }

    /**
     *  show create UI PlacementFaq
     */
    protected function create(){
        $placementAreas = PlacementArea::all();
        $placementCompanies = [];
        $placementFaq = new PlacementFaq;
        return view('placementFaq.create', compact('placementAreas', 'placementFaq', 'placementCompanies'));
    }

    /**
     *  store placementCompany
     */
    protected function store(Request $request){
        $v = Validator::make($request->all(), $this->validatePlacementFaq);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::beginTransaction();
        try
        {
            $placementFaq = PlacementFaq::addOrUpdatePlacementFaq($request);
            if('true' == $placementFaq){
                DB::commit();
                return Redirect::to('admin/managePlacementFaq')->with('message', 'Placement Faq created successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('admin/managePlacementFaq');
    }

    /**
     *  edit placementCompany
     */
    protected function edit($id){
        $id = InputSanitise::inputInt(json_decode($id));
        if(isset($id)){
            $placementFaq = PlacementFaq::find($id);
            if(is_object($placementFaq)){
                $placementAreas = PlacementArea::all();
                $placementCompanies = PlacementCompany::getPlacementCompaniesByArea($placementFaq->placement_area_id);
                return view('placementFaq.create', compact('placementAreas', 'placementFaq', 'placementCompanies'));
            }
        }
        return Redirect::to('admin/managePlacementFaq');
    }


    /**
     *  update placementCompany
     */
    protected function update( Request $request){
        $v = Validator::make($request->all(), $this->validatePlacementFaq);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }

        $faqId = InputSanitise::inputInt($request->get('faq_id'));
        if(isset($faqId)){
            DB::beginTransaction();
            try
            {
                $placementFaq = PlacementFaq::addOrUpdatePlacementFaq($request, true);
                if(is_object($placementFaq)){
                    DB::commit();
                    return Redirect::to('admin/managePlacementFaq')->with('message', 'Placement Faq created successfully!');
                } else {
                    return Redirect::to('admin/managePlacementFaq');
                }
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return redirect()->back()->withErrors('something went wrong.');
            }
        }
        return Redirect::to('admin/managePlacementFaq');
    }
}