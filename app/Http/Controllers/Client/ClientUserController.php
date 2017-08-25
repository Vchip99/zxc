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
use App\Models\ClientUserInstituteCourse;
use App\Models\ClientNotification;
use App\Models\ClientReadNotification;
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
        $clientApproveCourses = [];
        $userId = Auth::guard('clientuser')->user()->id;
        $userCourses = ClientUserInstituteCourse::getCoursesByUser($userId);
        if(is_object($userCourses) && false == $userCourses->isEmpty()){
            foreach($userCourses as $userCourse){
                if(1 == $userCourse->course_permission){
                    $clientApproveCourses[] = $userCourse->client_institute_course_id;
                }
            }
        }
        $courses = ClientOnlineCourse::getRegisteredOnlineCourses($userId,$clientApproveCourses);
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
        $clientApproveCourses = [];
        $userId = Auth::guard('clientuser')->user()->id;
        $userCourses = ClientUserInstituteCourse::getCoursesByUser($userId);
        if(is_object($userCourses) && false == $userCourses->isEmpty()){
            foreach($userCourses as $userCourse){
                if(1 == $userCourse->test_permission){
                    $clientApproveCourses[] = $userCourse->client_institute_course_id;
                }
            }
        }
        $results = ClientOnlineTestSubjectPaper::getRegisteredSubjectPapersByUserId($userId);
        if(count($results)>0){
            $testSubjectPapers = $results['papers'];
            $testSubjectPaperIds = $results['paperIds'];
            $testSubjectIds = $results['subjectIds'];
            $testSubjects = ClientOnlineTestSubject::getSubjectsByIds($results['subjectIds']);
        }
        $testCategories = ClientOnlineTestCategory::getTestCategoriesByRegisteredSubjectPapersByUserId($userId);
        $alreadyGivenPapers = ClientScore::getClientUserTestScoreBySubjectIdsByPaperIdsByUserId($testSubjectIds, $testSubjectPaperIds, $userId);
        $currentDate = date('Y-m-d');
        return view('clientuser.dashboard.myTest', compact('testSubjects', 'testSubjectPapers', 'testCategories','currentDate', 'alreadyGivenPapers', 'clientApproveCourses'));
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
        $categoryIds = [];
        $clientApproveCourses = [];
        $user = Auth::guard('clientuser')->user();
        $userCourses = ClientUserInstituteCourse::getCoursesByUser($user->id);
        if(is_object($userCourses) && false == $userCourses->isEmpty()){
            foreach($userCourses as $userCourse){
                if(1 == $userCourse->course_permission){
                    $clientApproveCourses[] = $userCourse->client_institute_course_id;
                }
            }
        }

        $courses = ClientOnlineCourse::getRegisteredOnlineCourses($user->id,$clientApproveCourses);
        foreach($courses as $course){
            $categoryIds[] = $course->category_id;
        }
        $categories = ClientOnlineCategory::find($categoryIds);
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
        $clientApproveCourses = [];
        $user = Auth::guard('clientuser')->user();
        $userCourses = ClientUserInstituteCourse::getCoursesByUser($user->id);
        if(is_object($userCourses) && false == $userCourses->isEmpty()){
            foreach($userCourses as $userCourse){
                if(1 == $userCourse->test_permission){
                    $clientApproveCourses[] = $userCourse->client_institute_course_id;
                }
            }
        }
        $categories = ClientOnlineTestCategory::getTestCategoriesByRegisteredSubjectPapersByUserId($user->id,$clientApproveCourses);
        $results = ClientScore::where('client_user_id', $user->id)->whereIn('client_institute_course_id',$clientApproveCourses)->get();

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

    protected function clientMessages(Request $request){
        $sortIds = [];
        $allIds = [];
        $testCourseIds = [];
        $testSubjectPapersIds = [];
        $idsImploded = '';
        $selectedYear = !empty($request->get('year'))?$request->get('year'): date('Y');
        $selectedMonth = !empty($request->get('month'))?$request->get('month'): date('m');

        $onlineVideos = ClientOnlineVideo::getClientCourseVideosByAssignedClientUserInstituteCourse();
        if(is_object($onlineVideos) && false == $onlineVideos->isEmpty()){
            foreach($onlineVideos as $onlineVideo){
                $onlineVideoIds[] = $onlineVideo->id;
            }
        }

        $testSubjectPapers= ClientOnlineTestSubjectPaper::getClientOnlineTestSubjectPapersByAssignedClientUserInstituteCourse();
        if(is_object($testSubjectPapers) && false == $testSubjectPapers->isEmpty()){
            foreach($testSubjectPapers as $testSubjectPaper){
                $testSubjectPapersIds[] = $testSubjectPaper->id;
            }
        }
        $readNotificationIds = ClientReadNotification::getReadNotificationIdsByUser($selectedYear,$selectedMonth);

        $queryForTestPapers = ClientNotification::where('client_id', Auth::guard('clientuser')->user()->client_id)
                        ->where('notification_module', 2)->whereIn('created_module_id', $testSubjectPapersIds)
                        ->where('created_by',0)->where('created_to',0)
                        ->whereYear('created_at', $selectedYear)->whereMonth('created_at', $selectedMonth);

        $allAdminNotifications = ClientNotification::where('client_id', Auth::guard('clientuser')->user()->client_id)
                        ->where('notification_module', 1)->whereIn('created_module_id', $onlineVideoIds)
                        ->where('created_by',0)->where('created_to',0)
                        ->whereYear('created_at', $selectedYear)->whereMonth('created_at', $selectedMonth)
                        ->union($queryForTestPapers)->orderBy('id', 'desc')->get();

        if(is_object($allAdminNotifications) && false == $allAdminNotifications->isEmpty()){
            foreach ($allAdminNotifications as $allAdminNotification) {
                if(!in_array($allAdminNotification->id, $readNotificationIds)){
                    $sortIds[] = $allAdminNotification->id;
                }
            }
            $allIds = array_merge($sortIds, $readNotificationIds);
            $idsImploded = "'" . implode("','", $allIds) . "'";
        }
        $result =  ClientNotification::where('client_id', Auth::guard('clientuser')->user()->client_id);
        if( count($allIds) > 0 && !empty($idsImploded)){
            $result->whereIn('id', $allIds)->orderByRaw("FIELD(`id`,$idsImploded)");
        }
        $notifications = $result->where('created_by',0)->where('created_to',0)->whereYear('created_at', $selectedYear)->whereMonth('created_at', $selectedMonth)->orderBy('id', 'desc')->paginate();
        $years = range(2017, 2030);
        $months = array(
                    "1" => "January", "2" => "February", "3" => "March", "4" => "April",
                    "5" => "May", "6" => "June", "7" => "July", "8" => "August",
                    "9" => "September", "10" => "October", "11" => "November", "12" => "December",
                );
        return view('clientuser.dashboard.adminNotifications', compact('notifications', 'readNotificationIds', 'years', 'months','selectedYear', 'selectedMonth'));
    }


    protected function myNotifications(){
        DB::connection('mysql2')->beginTransaction();
        try
        {
            ClientNotification::readUserNotifications(Auth::guard('clientuser')->user()->id);
            DB::connection('mysql2')->commit();
        }
        catch(\Exception $e)
        {
            DB::connection('mysql2')->rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        $notifications =  ClientNotification::where('client_id', Auth::guard('clientuser')->user()->client_id)->where('created_to', Auth::guard('clientuser')->user()->id)->orderBy('id', 'desc')->paginate();
        return view('clientuser.dashboard.notifications', compact('notifications'));
    }
}
