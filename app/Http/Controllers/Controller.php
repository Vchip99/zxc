<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\TestCategory;
use App\Models\TestSubCategory;
use App\Models\TestSubject;
use App\Models\TestSubjectPaper;
use App\Models\User;
use App\Libraries\InputSanitise;
use View,Cache,Session;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

        $testCategoriesWithQuestions = Cache::remember('vchip:tests:testCategoriesWithQuestions',60, function() {
            return TestCategory::getTestCategoriesAssociatedWithQuestion();
        });
        view::share('testCategoriesWithQuestions', $testCategoriesWithQuestions);

        $chatAdminId = Cache::remember('vchip:chatAdminId',60, function() {
            $adminChatUser = User::where('email', 'ceo@vchiptech.com')->first();
            if(is_object($adminChatUser)){
                return $adminChatUser->id;
            } else {
                return 0;
            }
        });
        view::share('chatAdminId', $chatAdminId);

        if(Cache::has('vchip:chatAdminLive')){
            $chatAdminLive = Cache::get('vchip:chatAdminLive');
        } else {
            $chatAdminLive = false;
        }
        view::share('chatAdminLive', $chatAdminLive);

        // $testForCompanies = Cache::remember('vchip:testForCompanies',10, function() {
        //     return TestCategory::getCompanyTestCategoriesAssociatedWithQuestion();
        // });
        $testForCompany = TestSubjectPaper::getFirstCompanyTestPaperAssociatedWithQuestion();
        // dd(\DB::getQueryLog());
        // dd($testForCompany);
        view::share('testForCompany', $testForCompany);
    }
}
