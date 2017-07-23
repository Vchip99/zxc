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
use View;

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
        // $testCategories = TestCategory::getAllTestCategories();
        // view::share('testCategories', $testCategories);

        $testCategoriesWithQuestions = TestCategory::getTestCategoriesAssociatedWithQuestion();
        view::share('testCategoriesWithQuestions', $testCategoriesWithQuestions);

        $testSubCategories = TestSubCategory::getAllTestSubCategories();
        view::share('testSubCategories', $testSubCategories);

        $testSubjects = TestSubject::getAllSubjects();
        view::share('testSubjects', $testSubjects);

        $testSubjectPapers = TestSubjectPaper::getAllSubjectPapers();
        view::share('testSubjectPapers', $testSubjectPapers);
    }
}
