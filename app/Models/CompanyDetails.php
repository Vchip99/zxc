<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use DB, Redirect;
use App\Libraries\InputSanitise;
use App\Models\PlacementArea;
use App\Models\PlacementCompany;

class CompanyDetails extends Model
{
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['placement_area_id', 'placement_company_id', 'about_company', 'industry_type', 'founded_year', 'founder_name', 'headquarters', 'ceo', 'products', 'website', 'mock_test_link'];

    /**
     *  create/update PlacementCompany
     */
    protected static function addOrUpdateCompanyDetails( Request $request, $isUpdate=false){

    	$areaId = InputSanitise::inputInt($request->get('area'));
    	$companyId = InputSanitise::inputInt($request->get('company'));
    	$companyDetailsId = InputSanitise::inputInt($request->get('company_details_id'));
    	$aboutCompany = $request->get('about_company');
    	$industryType = InputSanitise::inputString($request->get('industry_type'));
    	$foundedYear = InputSanitise::inputString($request->get('founded_year'));
    	$founderName = InputSanitise::inputString($request->get('founder_name'));
    	$headquarters = InputSanitise::inputString($request->get('headquarters'));
    	$ceo = InputSanitise::inputString($request->get('ceo'));
    	$products = $request->get('products');
    	$website = $request->get('website');
    	$mockTestLink = $request->get('mock_test_link');


        if( $isUpdate && isset($companyDetailsId)){
            $companyDetails = static::find($companyDetailsId);
            if(!is_object($companyDetails)){
            	return Redirect::to('admin/manageCompanyDetails');
            }
        } else{
            $companyDetails = new static;
        }
		$companyDetails->placement_area_id = $areaId;
        $companyDetails->placement_company_id = $companyId;
        $companyDetails->about_company = $aboutCompany;
        $companyDetails->industry_type = $industryType;
        $companyDetails->founded_year = $foundedYear;
        $companyDetails->founder_name = $founderName;
        $companyDetails->headquarters = $headquarters;
        $companyDetails->ceo = $ceo;
        $companyDetails->products = $products;
        $companyDetails->website = $website;
        $companyDetails->mock_test_link = $mockTestLink;
		$companyDetails->save();

        return $companyDetails;
    }

    public function area(){
    	return $this->belongsTo(PlacementArea::class, 'placement_area_id');
    }

    public function company(){
    	return $this->belongsTo(PlacementCompany::class, 'placement_company_id');
    }

    protected static function checkCompanyDetails($companyId){
        $result = static::where('placement_company_id', $companyId)->first();
        if(is_object($result)){
            return 'true';
        }
        return 'false';
    }
}
