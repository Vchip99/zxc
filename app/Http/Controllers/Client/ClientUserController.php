<?php

namespace App\Http\Controllers\Client;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\ClientHomePage;
use App\Models\Client;
use Illuminate\Http\Request;
use Auth, Redirect, View, DB;
use App\Models\ClientOnlineCategory;
use App\Models\ClientOnlineCourse;
use App\Models\ClientOnlineTestSubjectPaper;
use App\Models\ClientOnlineTestSubject;
use App\Models\ClientOnlineVideo;
use App\Models\ClientScore;
use App\Models\Clientuser;
use App\Models\ClientOnlineTestCategory;
use App\Libraries\InputSanitise;

class ClientUserController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->middleware('clientuser');
        $client = Client::where('subdomain', $request->getHost())->first();
        if(is_object($client)){
            view::share('client', $client);
        }
    }

    protected function showClientUserDashBoard(Request $request){
        return view('clientuser.dashboard.dashboard');
    }

    protected function myCourses(){
        $categoryIds = [];
        $userId = Auth::guard('clientuser')->user()->id;
        $courses = ClientOnlineCourse::getRegisteredOnlineCourses($userId);
        foreach($courses as $course){
            $categoryIds[] = $course->category_id;
        }
        $categories = ClientOnlineCategory::find($categoryIds);
        $courseVideoCount = $this->getVideoCount($courses);

        return view('clientuser.dashboard.myCourses', compact('courses', 'categories', 'subcategories', 'courseVideoCount'));
    }

    protected function myCertificate(){
        return view('clientuser.dashboard.myCertificate');
    }

    protected function myTest(){
        $results = [];
        $testSubjectPapers   = [];
        $testSubjects        = [];
        $testSubjectPaperIds = [];
        $testSubjectIds      = [];
        $userId = Auth::guard('clientuser')->user()->id;
        $results = ClientOnlineTestSubjectPaper::getRegisteredSubjectPapersByUserId($userId);
        if(count($results)>0){
            $testSubjectPapers = $results['papers'];
            $testSubjectPaperIds = $results['paperIds'];;
            $testSubjectIds = $results['subjectIds'];
            $testSubjects = ClientOnlineTestSubject::getSubjectsByIds($results['subjectIds']);
        }
        $testCategories = ClientOnlineTestCategory::getTestCategoriesByRegisteredSubjectPapersByUserId($userId);
        $alreadyGivenPapers = ClientScore::getClientUserTestScoreBySubjectIdsByPaperIdsByUserId($testSubjectIds, $testSubjectPaperIds, $userId);
        $currentDate = date('Y-m-d');
        return view('clientuser.dashboard.myTest', compact('testSubjects', 'testSubjectPapers', 'testCategories','currentDate', 'alreadyGivenPapers'));
    }

    protected function getVideoCount($courses){
        $courseIds = [];
         if(false == $courses->isEmpty()){
            foreach($courses as $course){
                $courseIds[] = $course->id;
            }
            $courseIds = array_unique($courseIds);
        }
        return ClientOnlineVideo::getCoursevideoCount($courseIds);
    }

    protected function myCourseResults(){
        $user = Auth::guard('clientuser')->user();
        $categories = ClientOnlineCategory::where('client_id', $user->client_id)->get();
        $courses = ClientOnlineCourse::getRegisteredOnlineCourses($user->id);
        return view('clientuser.dashboard.myCourseResult', compact('courses', 'categories'));
    }

    protected function getCourseByCatIdBySubCatIdByUserId(Request $request){
        $result = [];
        $categoryId = $request->get('catId');
        $subcategoryId = $request->get('subcatId');
        $userId = $request->get('userId');
        $result['courses'] = ClientOnlineCourse::getRegisteredOnlineCourseByCatIdBySubCatId($categoryId,$subcategoryId, $userId);
        return $result;
    }

    protected function myTestResults(){
        $user = Auth::guard('clientuser')->user();
        $categories = ClientOnlineTestCategory::getTestCategoriesByRegisteredSubjectPapersByUserId($user->id);
        $results = ClientScore::where('client_user_id', $user->id)->get();
        $barchartLimits = range(100, 0, 10);
        return view('clientuser.dashboard.myTestResults', compact('categories','results','barchartLimits'));
    }

    protected function showUserTestResultsByCategoryBySubcategoryByUserId(Request $request){

        return ClientScore::getUserTestResultsByCategoryBySubcategoryByUserId($request);
    }

    protected function profile(){
        return view('clientuser.dashboard.profile');
    }

    protected function updateProfile(Request $request){
        DB::connection('mysql2')->beginTransaction();
        try
        {
            $user = Clientuser::updateUser($request);
            if(is_object($user)){
                DB::connection('mysql2')->commit();
                return Redirect::to('profile')->with('message', 'Profile updated successfully.');
            }
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('profile');
    }
}
