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
use App\Libraries\InputSanitise;
use View,Cache;

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

        $testCategoriesWithQuestions = Cache::remember('vchip:testCategoriesWithQuestions',60, function() {
            return TestCategory::getTestCategoriesAssociatedWithQuestion();
        });
        view::share('testCategoriesWithQuestions', $testCategoriesWithQuestions);

        // $testSubCategories = Cache::remember('testSubCategories',60, function() {
        //     return TestSubCategory::getAllTestSubCategories();
        // });
        // view::share('testSubCategories', $testSubCategories);

        // $testSubjects = Cache::remember('testSubjects',60, function() {
        //     return TestSubject::getAllSubjects();
        // });
        // view::share('testSubjects', $testSubjects);

        // $testSubjectPapers = Cache::remember('testSubjectPapers',60, function() {
        //     return TestSubjectPaper::getAllSubjectPapers();
        // });
        // view::share('testSubjectPapers', $testSubjectPapers);

    }
}
