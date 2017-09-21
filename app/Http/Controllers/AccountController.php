<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CourseCourse;
use App\Models\TestSubjectPaper;
use App\Models\TestSubject;
use App\Models\RegisterLiveCourse;
use App\Models\DocumentsDoc;
use App\Models\VkitProject;
use App\Models\DiscussionPost;
use App\Models\DiscussionComment;
use App\Models\User;
use App\Models\RegisterProject;
use App\Models\CourseCategory;
use App\Models\CourseSubCategory;
use App\Models\TestCategory;
use App\Models\RegisterDocuments;
use App\Models\Score;
use App\Models\DiscussionPostLike;
use App\Models\DiscussionCommentLike;
use App\Models\DiscussionSubCommentLike;
use App\Models\CollegeDept;
use App\Models\College;
use App\Models\Notification;
use App\Models\ReadNotification;
use App\Models\AssignmentQuestion;
use App\Models\AssignmentAnswer;
use App\Models\AssignmentSubject;
use App\Models\AssignmentTopic;
use Excel;
use Auth,Hash,DB, Redirect,Session,Validator,Input;
use App\Libraries\InputSanitise;

class AccountController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
    }

    const AllPostModuleIdForMyQuestions = 1;
    const AllPostModuleIdForMyReplies   = 2;
    const Users = [
                1 => 'Admin/Owner of Institute ',
                2 => 'Student',
                3 => 'Lecturer',
                4 => 'HOD',
                5 => 'Principle / Director',
                6 => 'TNP Officer',
            ];
    const Student = 2;
    const Lecturer = 3;
    /**
     * Define your validation rules in a property in
     * the controller to reuse the rules.
     */
    protected $validateUpdatePassword = [
        'old_password' => 'required',
        'password' => 'required|different:old_password|confirmed',
        'password_confirmation' => 'required|same:password',
    ];

    protected function index(){
    	$user = Auth::user();
    	$userName = $user->name;
    	$userEmail = $user->email;
    	return view('auth.passwords.reset_pwd', compact('userName', 'userEmail'));
    }

    protected function checkEmail( Request $request){
    	$userPwd = Auth::user()->password;
    	if(Hash::check($request->get('old_password'), $userPwd)){
    		return 'true';
    	} else {
    		return 'false';
    	}
    }

    protected function updatePassword( Request $request){
        $v = Validator::make($request->all(), $this->validateUpdatePassword);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        DB::beginTransaction();
        try
        {
            $oldPassword = $request->get('old_password');
            $newPassword = $request->get('password');
            $user = Auth::user();
            $hashedPassword = $user->password;
            if(Hash::check($oldPassword, $hashedPassword)){
                $user->password = bcrypt($newPassword);
                $user->save();
                DB::commit();
                Auth::logout();
                return Redirect::to('/')->with('message', 'Password updated successfully. please login with new password.');
            } else {
                return redirect()->back()->withErrors('please enter correct old password.');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }

        return redirect('/');
    }

    protected function showProfile(){
        $users = self::Users;
        return view('dashboard.profile', compact('users'));
    }

    protected function myCourses(){
        $categoryIds = [];
        $userId = Auth::user()->id;
        $courses = CourseCourse::getRegisteredOnlineCourses($userId);
        foreach($courses as $course){
            $categoryIds[] = $course->course_category_id;
        }
        $categories = CourseCategory::find($categoryIds);
        return view('dashboard.myCourses', compact('courses', 'categories', 'subcategories'));
    }

    protected function myTest(){
        $results = [];
        $testSubjectPapers   = [];
        $testSubjects        = [];
        $testSubjectPaperIds = [];
        $testSubjectIds      = [];
        $userId = Auth::user()->id;
        $results = TestSubjectPaper::getRegisteredSubjectPapersByUserId($userId);
        if(count($results)>0){
            $testSubjectPapers = $results['papers'];
            $testSubjectPaperIds = $results['paperIds'];
            $testSubjectIds = $results['subjectIds'];
            $testSubjects = TestSubject::getSubjectsByIds($results['subjectIds']);
        }
        $testCategories = TestCategory::getTestCategoriesByRegisteredSubjectPapersByUserId($userId);
        $alreadyGivenPapers = Score::getTestUserScoreBySubjectIdsByPaperIdsByUserId($testSubjectIds, $testSubjectPaperIds, $userId);
        $currentDate = date('Y-m-d');
        return view('dashboard.myTest', compact('testSubjects', 'testSubjectPapers', 'testCategories', 'alreadyGivenPapers', 'currentDate'));
    }

    protected function myLiveCourses(){
        $categoryIds = [];
        $userId = Auth::user()->id;
        $liveCourses = RegisterLiveCourse::getRegisteredLiveCourses($userId);
        $liveCourseCategories =  RegisterLiveCourse::getCategoryIdsByRegisteredLiveCourses($userId);
        if(false == $liveCourseCategories->isEmpty()){
            foreach($liveCourseCategories as $liveCourseCategory){
                $categoryIds[] = $liveCourseCategory->category_id;
            }
            $categoryIds = array_unique($categoryIds);
        }
        return view('dashboard.myLiveCourses', compact('liveCourses', 'categoryIds'));
    }

    protected function myDocuments(){
        $userId = Auth::user()->id;
        $documents = DocumentsDoc::allRegisterDocuments($userId);
        $categories = RegisterDocuments::getRegisteredCategoriesByUserId($userId);
        return view('dashboard.myDocuments', compact('documents', 'categories'));
    }

    protected function myVkits(){
        $userId = Auth::user()->id;
        $projects = RegisterProject::getRegisteredProjectsByUserId($userId);
        $categories = RegisterProject::getRegisteredCategoriesByUserId($userId);
        return view('dashboard.myVkits', compact('projects', 'categories'));
    }

    protected function myQuestions(){
        $posts = DiscussionPost::where('user_id', Auth::user()->id)->orderBy('discussion_posts.id', 'desc')->get();
        $user = new User;
        $allPostModuleId = self::AllPostModuleIdForMyQuestions;
        $likesCount = DiscussionPostLike::getLikes();
        $currentUser = Auth::user()->id;
        return view('dashboard.myQuestions', compact('posts', 'user', 'allPostModuleId', 'likesCount', 'currentUser'));
    }

    protected function myReplies(){
        $postIds = [];
        $discussionComments = DiscussionComment::where('user_id', Auth::user()->id)->select('discussion_post_id')->get();
        if(false == $discussionComments->isEmpty()){
            foreach($discussionComments as $discussionComment){
                $postIds[]= $discussionComment->discussion_post_id;
            }
            $postIds = array_unique($postIds);
        }
        $posts = DiscussionPost::whereIn('id', $postIds)->orderBy('discussion_posts.id', 'desc')->get();
        $user = new User;
        $allPostModuleId = self::AllPostModuleIdForMyReplies;
        $likesCount = DiscussionPostLike::getLikes();
        $commentLikesCount = DiscussionCommentLike::getLiksByPosts($posts);
        $subcommentLikesCount = DiscussionSubCommentLike::getLiksByPosts($posts);
        $currentUser = Auth::user()->id;
        return view('dashboard.myReplies', compact('posts', 'user', 'allPostModuleId', 'likesCount', 'commentLikesCount', 'currentUser', 'subcommentLikesCount'));
    }

    protected function myCertificate(){
        return view('dashboard.myCertificate');
    }

    protected function myFavouriteArticles(){
        $userId = Auth::user()->id;
        $documents = DocumentsDoc::allFavouriteRegisterDocuments($userId);
        $categories = RegisterDocuments::getRegisteredFavouriteCategoriesByUserId($userId);
        return view('dashboard.myFavouriteArticles', compact('documents', 'categories'));
    }

    protected function students(Request $request){
        $user = Auth::user();
        $collegeDepts = [];
        if(5 == $user->user_type || 6 == $user->user_type){
            $collegeDepts = CollegeDept::where('college_id', $user->college_id)->get();
        }
        return view('dashboard.students', compact('students', 'collegeDepts'));
    }

    protected function changeApproveStatus(Request $request){
        return User::changeUserApproveStatus($request);
    }

    protected function deleteStudentFromCollege(Request $request){
        $result = User::deleteStudentFromCollege($request);
        if('true' == $result){
            return Redirect::to('students')->with('message', 'Student deleted successfully!');
        }
        return Redirect::to('students');
    }

    protected function searchStudent(Request $request){
        return User::searchStudent($request);
    }

    protected function studentTestResults($id=Null){
        $results = [];
        $students = [];
        $selectedStudent = '';
        $user = Auth::user();
        $categories = TestCategory::all();
        if(empty($id)){
            $id = Session::get('selected_student');
        }
        if($id > 0){
            $selectedStudent = User::find($id);
            if(!is_object($selectedStudent)){
                return redirect()->back();
            }
            if(4 == $user->user_type){
                $students = User::getAllStudentsByCollegeIdByDeptId($selectedStudent->college_id,$user->college_dept_id,$selectedStudent->user_type);
            } else {
                $students = User::getAllStudentsByCollegeIdByDeptId($selectedStudent->college_id,$selectedStudent->college_dept_id,$selectedStudent->user_type);
            }
            $results = Score::where('user_id', $id)->get();
            Session::set('selected_student', $id);
            Session::set('selected_user_type', $selectedStudent->user_type);
        }
        $collegeDepts = CollegeDept::where('college_id', $user->college_id)->get();
        $barchartLimits = range(100, 0, 10);
        return view('dashboard.studentTestResults', compact('categories','students','results','barchartLimits', 'selectedStudent', 'collegeDepts'));
    }

    protected function showTestResults(Request $request){
        $ranks = [];
        $marks = [];
        $user = Auth::user();

        $scores = Score::getScoreByCollegeIdByDeptIdByFilters($user->college_id,$request->department,$request);
        if( false == $scores->isEmpty()){
            foreach($scores as $score){
                $ranks[$score->id] = $score->rank($user->college_id);
                $marks[$score->id] = $score->totalMarks();
            }
        }
        $result['scores'] = $scores;
        $result['ranks'] = $ranks;
        $result['marks'] = $marks;
        if($request->student > 0){
            $selectedStudent = User::find($request->student);
            if(is_object($selectedStudent)){
                Session::set('selected_student', $selectedStudent->id);
                Session::set('selected_user_type', $selectedStudent->user_type);
            }
        }
        return $result;
    }

    protected function studentPlacement($id=NULL){
        $user = Auth::user();
        if(empty($id)){
            $id = Session::get('selected_student');
        }
        if($id > 0){
            $selectedStudent = User::find($id);
            if(!is_object($selectedStudent)){
                return redirect()->back();
            }
            if(4 == $user->user_type){
                $students = User::getAllStudentsByCollegeIdByDeptId($selectedStudent->college_id,$user->college_dept_id,$selectedStudent->user_type);
            } else {
                $students = User::getAllStudentsByCollegeIdByDeptId($selectedStudent->college_id,$selectedStudent->college_dept_id,$selectedStudent->user_type);
            }
            Session::set('selected_student', $id);
            Session::set('selected_user_type', $selectedStudent->user_type);
        } else {
            $students = User::getAllStudentsByCollegeIdByDeptId($user->college_id,$user->college_dept_id);
            $selectedStudent = '';
        }
        $collegeDepts = CollegeDept::where('college_id', $user->college_id)->get();
        return view('dashboard.studentPlacement', compact('students', 'selectedStudent', 'collegeDepts'));
    }

    protected function studentCourses($id=Null){
        $results = [];
        $students = [];
        $courses = [];
        $selectedStudent = '';
        $user = Auth::user();
        $categories = CourseCategory::all();
        if(empty($id)){
            $id = Session::get('selected_student');
        }
        if($id > 0){
            $selectedStudent = User::find($id);
            if(!is_object($selectedStudent)){
                return redirect()->back();
            }
            if(4 == $user->user_type){
                $students = User::getAllStudentsByCollegeIdByDeptId($selectedStudent->college_id,$user->college_dept_id,$selectedStudent->user_type);
            } else {
                $students = User::getAllStudentsByCollegeIdByDeptId($selectedStudent->college_id,$selectedStudent->college_dept_id,$selectedStudent->user_type);
            }
            $courses = CourseCourse::getRegisteredOnlineCourses($id);
            Session::set('selected_student', $id);
            Session::set('selected_user_type', $selectedStudent->user_type);
        }
        $collegeDepts = CollegeDept::where('college_id', $user->college_id)->get();

        return view('dashboard.studentCourses', compact('students', 'selectedStudent', 'collegeDepts', 'categories', 'courses'));
    }

    protected function updateProfile(Request $request){
        DB::beginTransaction();
        try
        {
            $user = User::updateUser($request);
            if(is_object($user)){
                DB::commit();
                return Redirect::to('profile')->with('message', 'User profile updated successfully.');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('profile');
    }

    protected function showStudentsByDepartmentByYear(Request $request){
        $user = Auth::user();
        if(3 == $user->user_type || 4 == $user->user_type){
            return User::getAllUsersByCollegeIdByDeptIdByYearByUserType($user->college_id,$user->college_dept_id, $request->year, $request->user_type);
        } else {
            return User::getAllUsersByCollegeIdByDeptIdByYearByUserType($user->college_id,$request->department, $request->year, $request->user_type);
        }
    }

    protected function getStudentById(Request $request){
        $selectedStudent = User::getStudentById($request->student);
        if(is_object($selectedStudent)){
            Session::set('selected_student', $selectedStudent->id);
            Session::set('selected_user_type', $selectedStudent->user_type);
            return $selectedStudent;
        }
        return;
    }

    protected function showStudentCourses(Request $request){
        $selectedStudent = User::getStudentById($request->student);
        if(is_object($selectedStudent)){
            Session::set('selected_student', $selectedStudent->id);
            Session::set('selected_user_type', $selectedStudent->user_type);
        }
        return CourseCourse::getOnlineCoursesByUserIdByCategoryBySubCategory($request->student,$request->category,$request->subcategory);
    }

    protected function myCourseResults(){
        $categoryIds = [];
        $user = Auth::user();
        $courses = CourseCourse::getRegisteredOnlineCourses($user->id);
        foreach($courses as $course){
            $categoryIds[] = $course->course_category_id;
        }
        $categories = CourseCategory::find($categoryIds);
        return view('dashboard.myCourseResult', compact('categories', 'courses'));
    }

    protected function myTestResults(){
        $user = Auth::user();
        $categories = TestCategory::all();
        $results = Score::where('user_id', $user->id)->get();
        $collegeDepts = CollegeDept::where('college_id', $user->college_id)->get();
        $barchartLimits = range(100, 0, 10);
        return view('dashboard.myTestResults', compact('categories','results','barchartLimits', 'collegeDepts'));
    }

    protected function showUserTestResultsByCatBySubCat(Request $request){
        $ranks = [];
        $marks = [];
        $collegeId = Auth::user()->college_id;
        $scores = Score::getUserTestResultsByCatBySubCat($request);
        if( false == $scores->isEmpty()){
            foreach($scores as $score){
                $ranks[$score->id] = $score->rank($collegeId);
                $marks[$score->id] = $score->totalMarks();
            }
        }
        $result['scores'] = $scores;
        $result['ranks'] = $ranks;
        $result['marks'] = $marks;
        return $result;
    }

    protected function allTestResults(){
        $colleges = College::all();
        $categories = TestCategory::all();
        $scores =[];
        return view('dashboard.allTestResults', compact('colleges', 'categories', 'scores'));
    }

    protected function getSubjectsByCatIdBySubcatId(Request $request){
        return TestSubject::getSubjectsByCatIdBySubcatid($request->catId, $request->subcatId);

    }

    protected function getPapersBySubjectId(Request $request){
        return TestSubjectPaper::getSubjectPapersBySubjectId($request->subjectId);
    }

    protected function getAllTestResults(Request $request){
        $ranks = [];
        $marks = [];
        $colleges = [];
        $departments = [];
        $scores = Score::getAllUsersResults($request);
        if( false == $scores->isEmpty()){
            foreach($scores as $score){
                $ranks[$score->id] = $score->rank($request->college);
                $marks[$score->id] = $score->totalMarks();
                if(is_object($score->user->college) && $score->user->college->id > 0){
                  $colleges[$score->id] = $score->user->college->name;
                }else if(is_object($score->user) && 'other' == $score->user->college_id){
                    $colleges[$score->id] = $score->user->other_source;
                }else{
                    $colleges[$score->id] = 'Client';
                }
                if(is_object($score->user->department) && $score->user->department->id > 0){
                  $departments[$score->id] = $score->user->department->name;
                }else if(is_object($score->user) && 'other' == $score->user->college_id){
                    $departments[$score->id] = 'Other';
                }else{
                    $departments[$score->id] = 'Client';
                }
            }
        }
        $results['scores'] = $scores;
        $results['ranks'] = $ranks;
        $results['marks'] = $marks;
        $results['colleges'] = $colleges;
        $results['departments'] = $departments;
        return $results;
    }

    protected function notifications(){
        DB::beginTransaction();
        try
        {
            Notification::readUserNotifications(Auth::user()->id);
            DB::commit();
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        $notifications =  Notification::where('admin_id', 0)->where('created_to', Auth::user()->id)->orderBy('id', 'desc')->paginate();
        return view('dashboard.notifications', compact('notifications'));
    }

    protected function adminMessages(Request $request){
        $sortIds = [];
        $allIds = [];
        $idsImploded = '';
        $selectedYear = !empty($request->get('year'))?$request->get('year'): date('Y');
        $selectedMonth = !empty($request->get('month'))?$request->get('month'): date('m');
        $readNotificationIds = ReadNotification::getReadNotificationIdsByUser($selectedYear,$selectedMonth);
        $allAdminNotifications = Notification::where('admin_id', 1)->whereYear('created_at', $selectedYear)->whereMonth('created_at', $selectedMonth)->orderBy('id', 'desc')->get();
        if(is_object($allAdminNotifications) && false == $allAdminNotifications->isEmpty()){
            foreach ($allAdminNotifications as $allAdminNotification) {
                if(!in_array($allAdminNotification->id, $readNotificationIds)){
                    $sortIds[] = $allAdminNotification->id;
                }
            }
            $allIds = array_merge($sortIds, $readNotificationIds);
            $idsImploded = "'" . implode("','", $allIds) . "'";
        }
        if(!empty($idsImploded)){
            $notifications =  Notification::where('admin_id', 1)->whereYear('created_at', $selectedYear)->whereMonth('created_at', $selectedMonth)->orderByRaw("FIELD(`id`,$idsImploded)")->paginate();
        } else {
            $notifications =  Notification::where('admin_id', 1)->whereYear('created_at', $selectedYear)->whereMonth('created_at', $selectedMonth)->paginate();
        }
        $years = range(2017, 2030);
        $months = array(
                    "1" => "January", "2" => "February", "3" => "March", "4" => "April",
                    "5" => "May", "6" => "June", "7" => "July", "8" => "August",
                    "9" => "September", "10" => "October", "11" => "November", "12" => "December",
                );
        return view('dashboard.adminNotifications', compact('notifications', 'readNotificationIds','years','months','selectedYear', 'selectedMonth'));

    }

    protected function downloadExcelResult(Request $request){
        // Define the Excel spreadsheet headers
        $resultArray[] = ['Sr. No.','Name','college','Department','Marks', 'Rank'];
        $scores = Score::getAllUsersResults($request);
        if( false == $scores->isEmpty()){
            foreach($scores as $index => $score){
                $result = [];
                $result['Sr. No.'] = $index +1;
                $result['Name'] = $score->user->name;

                if(is_object($score->user->college) && $score->user->college->id > 0){
                    $result['college'] = $score->user->college->name;
                }else if(is_object($score->user) && 'other' == $score->user->college_id){
                    $result['college'] = $score->user->other_source;
                }else{
                    $result['college'] = 'Client';
                }

                if(is_object($score->user->college) && $score->user->college->id > 0){
                    $result['Department'] = $score->user->college->name;
                }else if(is_object($score->user) && 'other' == $score->user->college_id){
                    $result['Department'] = $score->user->other_source;
                }else{
                    $result['Department'] = 'Client';
                }
                $totalMarks = $score->totalMarks()['totalMarks'];
                $result['Marks'] = (string) $score->test_score.'/'.$totalMarks;
                $result['Rank'] = (string) $score->rank($request->college);

                $resultArray[] = $result;
            }
        }
        if($request->get('college') > 0){
            $collegeName = College::find($request->get('college'))->name;
        } else {
            $collegeName = $request->get('college');
        }

        if($request->get('paper') > 0){
            $paperName = TestSubjectPaper::find($request->get('paper'))->name;
        } else {
            $paperName = $request->get('paper');
        }

        $collegeResult = $collegeName.'_'.$paperName.'_result';
        $sheetName = $paperName.' Test Result';
        return \Excel::create($collegeResult, function($excel) use ($sheetName,$resultArray) {
            $excel->sheet($sheetName , function($sheet) use ($resultArray)
            {
                $sheet->fromArray($resultArray);
            });
        })->download('xls');
    }

    protected function myAssignments(){
        $assignments = AssignmentQuestion::getStudentAssignments();
        $assignmentTeachers = User::getTeachers();
        return view('dashboard.assignmentLists', compact('assignments', 'assignmentTeachers'));
    }

    protected function doAssignment($id){
        $assignment = AssignmentQuestion::find($id);
        $answers = AssignmentAnswer::where('student_id', Auth::user()->id)->where('assignment_question_id', $id)->get();
        return view('dashboard.assignmentDetails', compact('assignment', 'answers'));
    }

    protected function createAssignmentAnswer(Request $request){
        $questionId   = InputSanitise::inputInt($request->get('assignment_question_id'));
        $studentId   = InputSanitise::inputInt($request->get('student_id'));
        $answer = $request->get('answer');
        $lecturerComment = $request->get('lecturer_comment');
        if(empty($answer) && empty($lecturerComment)){
            if(User::Student == Auth::user()->user_type){
                return Redirect::to('doAssignment/'.$questionId);
            } else {
                return Redirect::to('assignmentRemark/'.$questionId.'/'.$studentId);
            }
        }

        DB::beginTransaction();
        try
        {
            AssignmentAnswer::addAssignmentAnswer($request);
            DB::commit();
            if(!empty($answer)){
                return Redirect::to('doAssignment/'.$questionId)->with('message', 'Assignment updated successfully.');
            } else {
                return Redirect::to('assignmentRemark/'.$questionId.'/'.$studentId )->with('message', 'Assignment updated successfully.');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('doAssignment/'.$questionId);
    }

    protected function studentsAssignment(){
        $assignmentSubjects = [];
        $assignmentTopics = [];
        $assignmentUsers = [];
        $assignment = '';
        $selectedAssignmentYear = Session::get('selected_assignment_year');
        $selectedAssignmentSubject  = Session::get('selected_assignment_subject');
        $selectedAssignmentTopic = Session::get('selected_assignment_topic');
        $selectedAssignmentStudent = Session::get('selected_assignment_student');
        if($selectedAssignmentSubject > 0 && $selectedAssignmentYear > 0){
            $assignmentSubjects = AssignmentSubject::getAssignmentSubjectsByYear($selectedAssignmentYear);
        }
        if($selectedAssignmentSubject > 0){
            $assignmentTopics = AssignmentTopic::getAssignmentTopics($selectedAssignmentSubject);
        }
        if($selectedAssignmentStudent > 0){
            $assignmentUsers = User::getAssignmentUsers($selectedAssignmentYear);
        }
        if($selectedAssignmentSubject > 0 && $selectedAssignmentYear > 0 && $selectedAssignmentTopic > 0 && $selectedAssignmentStudent > 0){
            $assignment = AssignmentQuestion::getAssignmentByTopic($selectedAssignmentTopic);
        }
        return view('dashboard.studentsAssignment', compact('selectedAssignmentYear','selectedAssignmentSubject','selectedAssignmentTopic','selectedAssignmentStudent', 'assignmentSubjects', 'assignmentTopics', 'assignmentUsers', 'assignment'));
    }

    protected function assignmentRemark($assignmentId, $studentId){
        $assignmentId = InputSanitise::inputInt(json_decode($assignmentId));
        $studentId = InputSanitise::inputInt(json_decode($studentId));
        $assignment = AssignmentQuestion::find($assignmentId);
        $answers = AssignmentAnswer::where('student_id', $studentId)->where('assignment_question_id', $assignmentId)->get();
        $student = User::find($studentId);
        return view('dashboard.assignmentRemark', compact('assignment', 'answers', 'student'));
    }

    protected function getDepartmentLecturers(Request $request){
        return User::getTeachers($request->department);
    }
}