<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailVerification;
use App\Models\CourseCourse;
use App\Models\CourseVideo;
use App\Models\TestSubjectPaper;
use App\Models\TestSubject;
use App\Models\RegisterLiveCourse;
use App\Models\DocumentsDoc;
use App\Models\VkitProject;
use App\Models\DiscussionPost;
use App\Models\DiscussionComment;
use App\Models\User;
use App\Models\RegisterProject;
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
use App\Models\CollegeSubject;
use App\Models\AssignmentTopic;
use App\Models\DiscussionCategory;
use App\Models\ChatMessage;
use App\Models\Question;
use App\Models\RegisterOnlineCourse;
use App\Models\CourseComment;
use App\Models\CourseSubComment;
use App\Models\CourseVideoLike;
use App\Models\CourseCommentLike;
use App\Models\CourseSubCommentLike;
use App\Models\UserSolution;
use App\Models\PaperSection;
use App\Models\VkitProjectComment;
use App\Models\VkitProjectLike;
use App\Models\VkitProjectCommentLike;
use App\Models\VkitProjectSubCommentLike;
use App\Models\CollegeCategory;
use App\Models\CourseCategory;
use App\Models\VkitCategory;
use App\Models\Skill;
use App\Models\CollegeUserAttendance;
use App\Models\CollegeOfflinePaper;
use App\Models\UserData;
use App\Models\TestSubCategory;
use Excel;
use Auth,Hash,DB, Redirect,Session,Validator,Input,Cache;
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

    protected $validateAddEmail = [
        'email' => 'required|max:255',
        'password' => 'required',
        'confirm_password' => 'required|same:password'
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

    protected function showProfile(Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $colleges = [];
        $collegeDepts = [];
        $otherDepts = [];
        $obtainedScore = 0;
        $totalScore = 0;
        $users = self::Users;
        $loginUser = Auth::user();
        if(User::Student == $loginUser->user_type){
            if('other' == $loginUser->college_id){
                $colleges = College::all();
            }
            $userScores = Score::where('user_id',  $loginUser->id)->get();
            if(is_object($userScores) && false == $userScores->isEmpty()){
                foreach($userScores as $userScore){
                    $obtainedScore += $userScore->test_score;
                    $questions = Question::getQuestionsByCategoryIdBySubcategoryIdBySubjectIdByPaperId($userScore->category_id, $userScore->subcat_id, $userScore->subject_id, $userScore->paper_id);
                    if(is_object($questions) && false == $questions->isEmpty()){
                        foreach($questions as $question){
                            $totalScore += $question->positive_marks;
                        }
                    }
                }
            }
        } elseif(User::Lecturer == $loginUser->user_type || User::Hod == $loginUser->user_type){
            $userAssgnDepts = explode(',', $loginUser->assigned_college_depts);
            $otherDeptIds = array_diff($userAssgnDepts, [$loginUser->college_dept_id]);
            if(count($otherDeptIds) > 0){
                $depts = CollegeDept::find($otherDeptIds);
                if(is_object($depts) && false == $depts->isEmpty()){
                    foreach($depts as $dept){
                        $otherDepts[] = $dept->name;
                    }
                }
            }
        }
        return view('dashboard.profile', compact('users', 'colleges', 'collegeDepts', 'obtainedScore', 'totalScore', 'loginUser','otherDepts'));
    }

    protected function myCollegeCourses(Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $categoryIds = [];
        $userId = Auth::user()->id;
        $courses = CourseCourse::getCoursesAssociatedWithVideosByCollegeIdByDeptId(Auth::user()->college_id);
        foreach($courses as $course){
            $categoryIds[] = $course->course_category_id;
        }
        $categories = CollegeCategory::find($categoryIds);
        return view('dashboard.myCollegeCourses', compact('courses', 'categories'));
    }

    protected function myVchipCourses(Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $categoryIds = [];
        $userId = Auth::user()->id;
        $courses = CourseCourse::getRegisteredOnlineCourses($userId);
        foreach($courses as $course){
            $categoryIds[] = $course->course_category_id;
        }
        $categories = CourseCategory::find($categoryIds);
        return view('dashboard.myVchipCourses', compact('courses', 'categories', 'subcategories'));
    }

    protected function vchipCourseDetails($collegeUrl,$courseId,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $courseId = json_decode(trim($courseId));
        $course = Cache::remember('vchip:courses:Course-'.$courseId,30, function() use ($courseId){
            return CourseCourse::getOnlineCourseByCourseId($courseId);
        });
        if(is_object($course)){
            $videos = Cache::remember('vchip:courses:videos:courseId-'.$courseId,30, function() use ($courseId){
                return CourseVideo::getCourseVideosByCourseId($courseId);
            });

            $isCourseRegistered = RegisterOnlineCourse::isCourseRegistered($courseId);
            $isVchipCourse = true;
            return view('dashboard.courseDetails', compact('videos','isCourseRegistered','courseId','course','isVchipCourse'));
        }
        return Redirect::to('/college/'.$collegeUrl.'/myVchipCourses');
    }

    protected function collegeCourseDetails($collegeUrl,$courseId,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $courseId = json_decode(trim($courseId));
        $course = Cache::remember($collegeUrl.':courses:Course-'.$courseId,30, function() use ($courseId){
            return CourseCourse::getOnlineCourseByCourseIdByCollegeId($courseId,Auth::user()->college_id);
        });
        if(is_object($course)){
            $videos = Cache::remember($collegeUrl.':courses:videos:courseId-'.$courseId,30, function() use ($courseId){
                return CourseVideo::getCourseVideosByCourseId($courseId);
            });
            $isCourseRegistered = RegisterOnlineCourse::isCourseRegistered($courseId);
            $isVchipCourse = false;
            return view('dashboard.courseDetails', compact('videos','isCourseRegistered','courseId','course','isVchipCourse'));
        }
        return Redirect::to('/college/'.$collegeUrl.'/myCollegeCourses');
    }

    protected function vchipCourseEpisode($collegeUrl,$videoId,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $videoId = json_decode(trim($videoId));
        if(isset($videoId)){
            $video = Cache::remember('vchip:courses:video-'.$videoId,30, function() use ($videoId){
                return CourseVideo::getCourseVideoByVideoId($videoId);
            });
            if(is_object($video)){
                $courseId = $video->course_id;
                $courseVideos = Cache::remember('vchip:courses:videos:courseId-'.$courseId,30, function() use ($courseId){
                    return CourseVideo::getCourseVideosByCourseId($courseId);
                });
                $comments = CourseComment::where('course_video_id', $videoId)->orderBy('id', 'desc')->get();
                $likesCount = CourseVideoLike::getLikesByVideoId($videoId);
                $commentLikesCount = CourseCommentLike::getLikesByVideoId($videoId);
                $subcommentLikesCount = CourseSubCommentLike::getLikesByVideoId($videoId);
                $currentUser = Auth::user();
                $isVchipCourse = true;
                return view('dashboard.courseEpisode', compact('video', 'courseVideos', 'comments', 'likesCount', 'commentLikesCount', 'currentUser', 'subcommentLikesCount','isVchipCourse'));
            }
        }
        return Redirect::to('/college/'.$collegeUrl.'/myVchipCourses');
    }

    protected function collegeCourseEpisode($collegeUrl,$videoId,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $videoId = json_decode(trim($videoId));

        if(isset($videoId)){
            $video = Cache::remember($collegeUrl.':courses:video-'.$videoId,30, function() use ($videoId){
                return CourseVideo::getCourseVideoByCollegeIdByVideoId(Auth::user()->college_id,$videoId);
            });

            if(is_object($video)){
                $courseId = $video->course_id;
                $courseVideos = Cache::remember($collegeUrl.':courses:videos:courseId-'.$courseId,30, function() use ($courseId){
                    return CourseVideo::getCourseVideosByCourseId($courseId);
                });
                $comments = CourseComment::where('course_video_id', $videoId)->orderBy('id', 'desc')->get();
                $likesCount = CourseVideoLike::getLikesByVideoId($videoId);
                $commentLikesCount = CourseCommentLike::getLikesByVideoId($videoId);
                $subcommentLikesCount = CourseSubCommentLike::getLikesByVideoId($videoId);
                $currentUser = Auth::user();
                $isVchipCourse = false;
                return view('dashboard.courseEpisode', compact('video', 'courseVideos', 'comments', 'likesCount', 'commentLikesCount', 'currentUser', 'subcommentLikesCount','isVchipCourse'));
            }
        }
        return Redirect::to('/college/'.$collegeUrl.'/myCollegeCourses');
    }

    protected function myVchipTest($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $results = [];
        $testSubjectPapers   = [];
        $testSubjects        = [];
        $testSubjectPaperIds = [];
        $testSubjectIds      = [];
        $subcategoryIds = [];
        $subcategories = [];
        $userId = Auth::user()->id;
        $results = TestSubjectPaper::getRegisteredSubjectPapersByUserId($userId);
        if(count($results)>0){
            $testSubjectPapers = $results['papers'];
            $testSubjectPaperIds = $results['paperIds'];
            $testSubjectIds = $results['subjectIds'];
            $selectedSubjects = TestSubject::getSubjectsByIds($results['subjectIds']);
            if(is_object($selectedSubjects) && false == $selectedSubjects->isEmpty()){
                foreach($selectedSubjects as $selectedSubject){
                    $testSubjects[$selectedSubject->test_sub_category_id][] = $selectedSubject;
                    $subcategoryIds[] = $selectedSubject->test_sub_category_id;
                }
            }
            if(count($subcategoryIds) > 0){
                $selectedSubCategories = TestSubCategory::find(array_unique($subcategoryIds));
                if(is_object($selectedSubCategories) && false == $selectedSubCategories->isEmpty()){
                    foreach($selectedSubCategories as $selectedSubCategory){
                        $subcategories[$selectedSubCategory->id] = $selectedSubCategory->name;
                    }
                }
            }
        }
        $testCategories = Cache::remember('vchip:tests:testCategoriesWithQuestions',60, function() {
            return TestCategory::getTestCategoriesAssociatedWithQuestion();
        });
        $alreadyGivenPapers = Score::getTestUserScoreBySubjectIdsByPaperIdsByUserId($testSubjectIds, $testSubjectPaperIds, $userId);
        $currentDate = date('Y-m-d H:i:s');
        return view('dashboard.myVchipTest', compact('testSubjects', 'testSubjectPapers', 'testCategories', 'alreadyGivenPapers', 'currentDate','subcategories'));

    }
    protected function myCollegeTest(Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $results = [];
        $testSubjectPapers   = [];
        $testSubjects        = [];
        $testSubjectPaperIds = [];
        $testSubjectIds      = [];
        $subcategoryIds = [];
        $subcategories = [];
        $user = Auth::user();
        $results = TestSubjectPaper::getSubjectPapersByCollegeIdByCollegeDeptId($user->college_id);
        if(count($results)>0){
            $testSubjectPapers = $results['papers'];
            $testSubjectPaperIds = $results['paperIds'];
            $testSubjectIds = $results['subjectIds'];
            $selectedSubjects = TestSubject::getSubjectsByIds($results['subjectIds']);
            if(is_object($selectedSubjects) && false == $selectedSubjects->isEmpty()){
                foreach($selectedSubjects as $selectedSubject){
                    $testSubjects[$selectedSubject->test_sub_category_id][] = $selectedSubject;
                    $subcategoryIds[] = $selectedSubject->test_sub_category_id;
                }
            }
            if(count($subcategoryIds) > 0){
                $selectedSubCategories = TestSubCategory::find(array_unique($subcategoryIds));
                if(is_object($selectedSubCategories) && false == $selectedSubCategories->isEmpty()){
                    foreach($selectedSubCategories as $selectedSubCategory){
                        $subcategories[$selectedSubCategory->id] = $selectedSubCategory->name;
                    }
                }
            }
        }
        $testCategories = CollegeCategory::getTestCategoriesByCollegeIdByDeptIdAssociatedWithQuestion($user->college_id);
        $alreadyGivenPapers = Score::getTestUserScoreBySubjectIdsByPaperIdsByUserId($testSubjectIds, $testSubjectPaperIds, $user->id);
        $currentDate = date('Y-m-d H:i:s');
        return view('dashboard.myCollegeTest', compact('testSubjects', 'testSubjectPapers', 'testCategories', 'alreadyGivenPapers', 'currentDate','subcategories'));
    }

    protected function showUserTestResult($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $categoryId = $request->get('category_id');
        $subcatId   = $request->get('subcategory_id');
        $paperId   = $request->get('paper_id');
        $subjectId  = $request->get('subject_id');
        $loginUser = Auth::user();
        $userId = $loginUser->id;
        $collegeId = $loginUser->college_id;
        $totalMarks = 0 ;
        $userAnswers = [];
        $positiveMarks = 0;
        $negativeMarks = 0;

        $score = Score::getUserTestResultByCategoryIdBySubcategoryIdBySubjectIdByPaperId($categoryId,$subcatId,$paperId,$subjectId,$userId);
        if(is_object($score)){
            $collegeRank =Score::getUserTestRankByCategoryIdBySubcategoryIdBySubjectIdByPaperIdByTestScore($categoryId,$subcatId,$subjectId,$paperId,$score->test_score,$collegeId);
            $collegeTotalRank =Score::getUserTestTotalRankByCategoryIdBySubcategoryIdBySubjectIdByPaperId($categoryId,$subcatId,$subjectId, $paperId,$collegeId);
            $globalRank =Score::getUserTestRankByCategoryIdBySubcategoryIdBySubjectIdByPaperIdByTestScore($categoryId,$subcatId,$subjectId,$paperId,$score->test_score,'all');
            $globalTotalRank =Score::getUserTestTotalRankByCategoryIdBySubcategoryIdBySubjectIdByPaperId($categoryId,$subcatId,$subjectId, $paperId,'all');

            $userSolutions = UserSolution::getUserSolutionsByUserIdByscoreIdByBubjectIdByPaperId($userId, $score->id, $subjectId, $paperId);
            if(is_object($userSolutions) && false == $userSolutions->isEmpty()){
                foreach($userSolutions as $userSolution){
                    $userAnswers[$userSolution->ques_id] = $userSolution->user_answer;
                }
            }

            $questions = Question::getQuestionsByCategoryIdBySubcategoryIdBySubjectIdByPaperId($categoryId, $subcatId, $subjectId, $paperId);
            if(is_object($questions) && false == $questions->isEmpty()){
                foreach($questions as $question){
                    if(isset($userAnswers[$question->id])){
                        $totalMarks += $question->positive_marks;
                        if($question->answer == $userAnswers[$question->id] && $question->question_type == 1){
                            $positiveMarks = (float) $positiveMarks + (float) $question->positive_marks;
                        } else if($userAnswers[$question->id] >= $question->min && $userAnswers[$question->id] <= $question->max && $question->question_type == 0){
                            $positiveMarks = (float) $positiveMarks + (float) $question->positive_marks;
                        } else if($userAnswers[$question->id]=='unsolved' || $userAnswers[$question->id] =='' ){
                            continue;
                        } else {
                            $negativeMarks =  (float) $negativeMarks + (float) $question->negative_marks;
                        }
                    }
                }
            }
            $percentile = ceil(((($globalTotalRank + 1) - ($globalRank +1) )/ $globalTotalRank)*100);
            $percentage = ceil(($score->test_score/$totalMarks)*100);
            if(($score->right_answered + $score->wrong_answered) > 0){
                $accuracy =  ceil(($score->right_answered/($score->right_answered + $score->wrong_answered))*100);
            } else {
                $accuracy = 0;
            }
            return view('dashboard.myPaperResult', compact('score', 'globalRank', 'totalMarks', 'globalTotalRank', 'percentile', 'percentage', 'accuracy', 'collegeRank', 'collegeTotalRank', 'positiveMarks', 'negativeMarks'));
        } else {
            return Redirect::to('/college/'.$collegeUrl.'/myVchipTest');
        }
    }

    protected function showUserTestSolution(Request $request){
        $sections = [];
        $paper = TestSubjectPaper::find($request->paper_id);
        $userId = $request->user_id;
        $scoreId = $request->score_id;
        if(is_object($paper)){
            $questions = Question::getQuestionsByCategoryIdBySubcategoryIdBySubjectIdByPaperId($paper->test_category_id, $paper->test_sub_category_id, $paper->test_subject_id, $paper->id);

            foreach($questions as $question){
                $results['questions'][$question->section_type][] = $question;
            }
            if(count(array_keys($results['questions'])) > 0){
                $paperId = $paper->id;
                $paperSections = Cache::remember('vchip:tests:paperSections:paperId-'.$paperId,30, function() use ($paperId) {
                    return PaperSection::where('test_subject_paper_id', $paperId)->get();
                });
                if(is_object($paperSections) && false == $paperSections->isEmpty()){
                    foreach($paperSections as $paperSection){
                        if(in_array($paperSection->id, array_keys($results['questions']))){
                            $sections[$paperSection->id] = $paperSection;
                        }
                    }
                }
            }
            $userSolutions = UserSolution::getUserSolutionsByUserIdByscoreIdByBubjectIdByPaperId($userId, $scoreId, $paper->test_subject_id, $paper->id);
            foreach ($userSolutions  as $key => $result) {
                $userResults[$result->ques_id] = $result;
            }
            return view('dashboard.myTestSolution', compact('results', 'userResults', 'paper', 'sections'));
        }
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

    protected function myDocuments(Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $userId = Auth::user()->id;
        $documents = DocumentsDoc::allRegisterDocuments($userId);
        $categories = RegisterDocuments::getRegisteredCategoriesByUserId($userId);
        return view('dashboard.myDocuments', compact('documents', 'categories'));
    }

    protected function myVchipVkits(Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $userId = Auth::user()->id;
        $projects = RegisterProject::getRegisteredProjectsByUserId($userId);
        $categories = RegisterProject::getRegisteredCategoriesByUserId($userId);
        return view('dashboard.myVchipVkits', compact('projects', 'categories'));
    }

    protected function myCollegeVkits(Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $user = Auth::user();
        $projects = VkitProject::getVkitProjectsByCollegeIdByDeptId($user->college_id);
        $categories = CollegeCategory::getProjectCategoriesByCollegeIdByDeptId($user->college_id);
        return view('dashboard.myCollegeVkits', compact('projects', 'categories'));
    }

    /**
     *  show vkits project by Id
     */
    protected function vkitproject($collegeUrl,Request $request,$id,$subcommentId=NULL){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $project = Cache::remember('vchip:projects:project-'.$id,60, function() use ($id){
            return VkitProject::getVkitProjectsById(json_decode($id));
        });
        if(is_object($project)){
            $projects = Cache::remember('vchip:projects:projects-'.$project->category_id,60, function() use($project){
                return VkitProject::getVkitProjectsByCategoryId($project->category_id);
            });
            $comments = VkitProjectComment::where('vkit_project_id', $id)->orderBy('id', 'desc')->get();
            $likesCount = VkitProjectLike::getLikesByVkitProjectId($id);
            $commentLikesCount = VkitProjectCommentLike::getLikesByVkitProjectId($id);
            $subcommentLikesCount = VkitProjectSubCommentLike::getLikesByVkitProjectId($id);
            $currentUser = Auth::user();
            if(is_object($currentUser)){
                $userId = $currentUser->id;
                $registeredProjects = RegisterProject::getRegisteredVkitProjectsByUserId($userId);
                if(false == $registeredProjects->isEmpty()){
                    foreach($registeredProjects as $registeredProject){
                        $registeredProjectIds[] = $registeredProject->project_id;
                    }
                }
            }
            if(is_object($currentUser)){
                if($id > 0 || $subcommentId > 0){
                    DB::beginTransaction();
                    try
                    {
                        if($id > 0 && $subcommentId == NULL){
                            $readNotification = ReadNotification::readNotificationByModuleByModuleIdByUser(Notification::ADMINVKITPROJECT,$id,$currentUser->id);
                            if(is_object($readNotification)){
                                DB::commit();
                            }
                        } else {
                            Session::set('show_subcomment_area', $subcommentId);
                        }
                        Session::set('project_comment_area', 0);
                    }
                    catch(\Exception $e)
                    {
                        DB::rollback();
                        return redirect()->back()->withErrors('something went wrong.');
                    }
                } else {
                    Session::set('show_subcomment_area', 0);
                }
            } else {
                $currentUser = NULL;
            }
            return view('dashboard.myVkitsProject', compact('project', 'projects', 'comments', 'registeredProjectIds', 'likesCount', 'commentLikesCount', 'currentUser', 'subcommentLikesCount'));
        }
        return Redirect::to('/');
    }

    /**
     *  show vkits project by Id
     */
    protected function collegeVkitproject($collegeUrl,Request $request,$id,$subcommentId=NULL){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $project = Cache::remember(Session::get('college_user_url').':projects:project-'.$id,60, function() use ($id){
            return VkitProject::getCollegeVkitProjectsById(json_decode($id));
        });
        if(is_object($project)){
            $projects = Cache::remember(Session::get('college_user_url').':projects:projects-'.$project->category_id,60, function() use ($project) {
                return VkitProject::getCollegeVkitProjectsByCategoryId($project->category_id);
            });
            $comments = VkitProjectComment::where('vkit_project_id', $id)->orderBy('id', 'desc')->get();
            $likesCount = VkitProjectLike::getLikesByVkitProjectId($id);
            $commentLikesCount = VkitProjectCommentLike::getLikesByVkitProjectId($id);
            $subcommentLikesCount = VkitProjectSubCommentLike::getLikesByVkitProjectId($id);
            $currentUser = Auth::user();
            if(is_object($currentUser)){
                $userId = $currentUser->id;
                $registeredProjects = RegisterProject::getRegisteredVkitProjectsByUserId($userId);
                if(false == $registeredProjects->isEmpty()){
                    foreach($registeredProjects as $registeredProject){
                        $registeredProjectIds[] = $registeredProject->project_id;
                    }
                }
            }
            return view('dashboard.myCollegeVkitProject', compact('project', 'projects', 'comments', 'registeredProjectIds', 'likesCount', 'commentLikesCount', 'currentUser', 'subcommentLikesCount'));
        }
        return Redirect::to('/');
    }

    protected function myQuestions(Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $loginUser = Auth::user();
        $posts = DiscussionPost::where('user_id', $loginUser->id)->orderBy('discussion_posts.id', 'desc')->get();
        $user = new User;
        $allPostModuleId = self::AllPostModuleIdForMyQuestions;
        $likesCount = DiscussionPostLike::getLikes();
        $currentUser = $loginUser->id;
        $discussionCategories =DiscussionCategory::all();
        return view('dashboard.myQuestions', compact('posts', 'user', 'allPostModuleId', 'likesCount', 'currentUser', 'discussionCategories'));
    }

    protected function myReplies(Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $postIds = [];
        $loginUser = Auth::user();
        $discussionComments = DiscussionComment::where('user_id', $loginUser->id)->select('discussion_post_id')->get();
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
        $currentUser = $loginUser->id;
        return view('dashboard.myReplies', compact('posts', 'user', 'allPostModuleId', 'likesCount', 'commentLikesCount', 'currentUser', 'subcommentLikesCount'));
    }

    protected function myCertificate(Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        return view('dashboard.myCertificate');
    }

    protected function myFavouriteArticles(Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $userId = Auth::user()->id;
        $documents = DocumentsDoc::allFavouriteRegisterDocuments($userId);
        $categories = RegisterDocuments::getRegisteredFavouriteCategoriesByUserId($userId);
        return view('dashboard.myFavouriteArticles', compact('documents', 'categories'));
    }

    protected function students($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $loginUser = Auth::user();
        $collegeDepts = [];
        $collegeDeptNames = [];
        $collegeAllDeptNames = [];
        if(User::Hod == $loginUser->user_type || User::Lecturer == $loginUser->user_type){
            $deptIds = explode(',',$loginUser->assigned_college_depts);
            $collegeDepts = CollegeDept::getDepartmentsByCollegeIdByDeptIds($loginUser->college_id,$deptIds);
            if(is_object($collegeDepts) && false == $collegeDepts->isEmpty()){
                foreach($collegeDepts as $collegeDept){
                    $collegeDeptNames[$collegeDept->id] = $collegeDept->name;
                }
            }
            $collegeAllDepts = CollegeDept::getDepartmentsByCollegeId($loginUser->college_id);
            if(is_object($collegeAllDepts) && false == $collegeAllDepts->isEmpty()){
                foreach($collegeAllDepts as $collegeDept){
                    $collegeAllDeptNames[$collegeDept->id] = $collegeDept->name;
                }
            }
        } else {
            $collegeDepts = CollegeDept::getDepartmentsByCollegeId($loginUser->college_id);
            if(is_object($collegeDepts) && false == $collegeDepts->isEmpty()){
                foreach($collegeDepts as $collegeDept){
                    $collegeDeptNames[$collegeDept->id] = $collegeDept->name;
                    $collegeAllDeptNames[$collegeDept->id] = $collegeDept->name;
                }
            }
        }

        $users = [];
        $selectedDept = Session::get('user_info_selected_department');
        $selectedYear = Session::get('user_info_selected_year');
        $selectedUserType = Session::get('user_info_selected_user_type');

        if(isset($selectedUserType) && isset($selectedDept) && isset($selectedYear)){
            $users = User::getUsersByUserTypeByDeptIdByYear($selectedUserType,$selectedDept,$selectedYear);
        }
        return view('dashboard.students', compact('collegeDepts','selectedDept','selectedYear','selectedUserType','users','collegeDeptNames','collegeAllDeptNames'));
    }

    protected function changeApproveStatus(Request $request){
        return User::changeUserApproveStatus($request);
    }

    protected function deleteStudentFromCollege($collegeUrl,Request $request){
        DB::beginTransaction();
        try
        {
            $result = User::deleteStudentFromCollege($request);
            if('true' == $result){
                DB::commit();
                return Redirect::to('/college/'.$collegeUrl.'/students')->with('message', 'Student deleted successfully!');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('/college/'.$collegeUrl.'/students');
    }

    protected function searchStudent(Request $request){
        $user = Auth::user();
        $result['departments'] = CollegeDept::getDepartmentsByCollegeId($user->college_id);
        $result['users']  = User::searchStudent($request);
        Session::put('user_info_selected_department',$request->department);
        Session::put('user_info_selected_year',$request->year);
        Session::put('user_info_selected_user_type',$request->user_type);
        if(User::Student == $request->user_type){
            $result['assignDepts'] = CollegeDept::find(explode(',', $user->assigned_college_depts));
        }
        return $result;
    }

    protected function assignDepatementsToUser(Request $request){
        DB::beginTransaction();
        try
        {
            $userData = User::find($request->get('user'));
            if(!is_object($userData)){
                return Redirect::to('college/'.Session::get('college_user_url').'/students');
            }
            $oldAssignedDepts = explode(',', $userData->assigned_college_depts);
            $user = User::assignDepatementsToUser($request);
            if(is_object($user)){
                $newAssignedDepts = explode(',', $user->assigned_college_depts);
                $removedDepts = array_values(array_diff($oldAssignedDepts, $newAssignedDepts));
                if(count($removedDepts) > 0){
                    // delete attendance by college dept userid
                    CollegeUserAttendance::deleteAttendanceByCollegeIdByDepartmentIdsByUserId($user->college_id,$removedDepts,$user->id);
                    // delete peper and marks
                    CollegeOfflinePaper::deleteCollegeOfflinePapersByCollegeIdByDepartmentIdsByUserId($user->college_id,$removedDepts,$user->id);
                    // remove depts for topic
                    AssignmentTopic::removeDepartmentsByCollegeIdByDepartmentIdsByUserId($user->college_id,$removedDepts,$user->id);
                    // remove topics for empty depts
                    AssignmentTopic::deleteTopicsByCollegeIdByUserIdForEmptyDept($user->college_id,$user->id);

                    // remove depts for assignment question
                    AssignmentQuestion::removeDepartmentsByCollegeIdByDepartmentIdsByUserId($user->college_id,$removedDepts,$user->id);
                    // delete assignment question for empty depts
                    AssignmentQuestion::deleteAssignmentsByCollegeIdByUserIdForEmptyDept($user->college_id,$user->id);
                    // delete answer
                    AssignmentAnswer::deleteAnswersByUserIdByStudentDeptIds($user->id,$removedDepts);

                }
                DB::commit();
                return Redirect::to('college/'.Session::get('college_user_url').'/students')->with('message', 'Departments assigned successfully.');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('college/'.Session::get('college_user_url').'/students');
    }

    protected function searchContact(Request $request){
        return User::searchContact($request);
    }

    protected function studentCollegeTestResults($collegeUrl,Request $request,$id=Null){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $results = [];
        $students = [];
        $selectedStudent = '';
        $user = Auth::user();
        $categories = CollegeCategory::getCollegeCategoriesByCollegeIdByDeptId($user->college_id);
        if(empty($id)){
            $id = Session::get('selected_student');
        }
        if($id > 0){
            $selectedStudent = User::find($id);
            if(!is_object($selectedStudent)){
                return redirect()->back();
            }
            // if(4 == $user->user_type){
            //     $students = User::getAllStudentsByCollegeIdByDeptId($selectedStudent->college_id,$user->college_dept_id,$selectedStudent->user_type);
            // } else {
                $students = User::getAllStudentsByCollegeIdByDeptId($selectedStudent->college_id,$selectedStudent->college_dept_id,$selectedStudent->user_type);
            // }

            $results = Score::getScoresWithCollegeTestCategoriesByCollegeIdByDeptIdByUserId($selectedStudent->college_id,$selectedStudent->college_dept_id,$id);
            Session::set('selected_student', $id);
            Session::set('selected_user_type', $selectedStudent->user_type);
        }
        if(5 == $user->user_type || 6 == $user->user_type){
            $collegeDepts = CollegeDept::where('college_id', $user->college_id)->get();
        } else {
            $collegeDepts = [];
        }
        return view('dashboard.studentCollegeTestResults', compact('categories','students','results','selectedStudent', 'collegeDepts'));
    }

    protected function lecturerPapers($collegeUrl,Request $request,$id=Null){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $results = [];
        $users = [];
        $selectedUser = '';
        $user = Auth::user();
        if(empty($id)){
            $id = Session::get('selected_lecturer');
        }
        if($id > 0){
            $selectedUser = User::find($id);
            if(!is_object($selectedUser)){
                return redirect()->back();
            }
            $users = User::getAllStudentsByCollegeIdByDeptId($selectedUser->college_id,$selectedUser->college_dept_id,$selectedUser->user_type);
            $results = TestSubjectPaper::getPapersByUserIdByCollegeIdByDeptId($id,$user->college_id);
            Session::set('selected_lecturer', $id);
            Session::set('selected_user_type', $selectedUser->user_type);
        } else {
            $users = User::getAllStudentsByCollegeIdByDeptId($user->college_id,$user->college_dept_id,User::Lecturer);
        }
        if(User::Directore == $user->user_type || User::TNP == $user->user_type){
            $collegeDepts = CollegeDept::where('college_id', $user->college_id)->get();
        } else {
            $collegeDepts = [];
        }
        return view('dashboard.lecturerPapers', compact('users','results','selectedUser','collegeDepts'));
    }

    protected function showStudentsByUserType(Request $request){
        $userType = $request->get('user_type');
        $department = $request->get('department');
        $user = Auth::user();
        if(User::Directore == $user->user_type || User::TNP == $user->user_type){
            return User::getAllStudentsByCollegeIdByDeptId($user->college_id,$department,$userType);
        } else {
            return User::getAllStudentsByCollegeIdByDeptId($user->college_id,$user->college_dept_id,$userType);
        }
    }

    protected function getLecturerPapers(Request $request){
        $lecturer = $request->lecturer;
        $user = Auth::user();
        Session::set('selected_lecturer', $lecturer);
        return TestSubjectPaper::getPapersByUserIdByCollegeIdByDeptId($lecturer,$user->college_id);
    }

    protected function studentVchipTestResults($collegeUrl,Request $request,$id=Null){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $results = [];
        $students = [];
        $selectedStudent = '';
        $user = Auth::user();
        $categories = TestCategory::getAllTestCategories();
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
            $results = Score::getScoresWithTestCategoriesByUserId($id);
            Session::set('selected_student', $id);
            Session::set('selected_user_type', $selectedStudent->user_type);
        }
        if(5 == $user->user_type || 6 == $user->user_type){
            $collegeDepts = CollegeDept::where('college_id', $user->college_id)->get();
        } else {
            $collegeDepts = [];
        }
        return view('dashboard.studentVchipTestResults', compact('categories','students','results','selectedStudent', 'collegeDepts'));
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

    protected function showCollegeTestResults(Request $request){
        $ranks = [];
        $marks = [];
        $user = Auth::user();

        $scores = Score::getCollegeScoreByCollegeIdByDeptIdByFilters($user->college_id,$request->department,$request);
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

    protected function studentCollegePlacement($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $students = [];
        $userSkills = [];
        $user = Auth::user();
        $year = Session::get('selected_college_placement_year');
        $department = Session::get('selected_college_placement_department');
        if($year && $department ){
            $students = User::showPlacementVideoByDepartmentByYear($user->college_id,$department,$year,User::Student);
            Session::set('selected_college_placement_year', $year);
            Session::set('selected_college_placement_department', $department);
        }
        $allSkills = Skill::all();
        if(is_object($allSkills) && false == $allSkills->isEmpty()){
            foreach($allSkills as $skill){
                $userSkills[$skill->id] = $skill->name;
            }
        }
        $collegeDepts = CollegeDept::where('college_id', $user->college_id)->get();
        return view('dashboard.studentCollegePlacement', compact('students', 'year', 'collegeDepts','department','userSkills'));
    }

    protected function studentVchipPlacement($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $students = [];
        $userSkills = [];
        $user = Auth::user();
        $year = Session::get('selected_vchip_placement_year');
        $department = Session::get('selected_vchip_placement_department');
        if($year && $department){
            $students = UserData::showVchipPlacementVideoByDepartmentByYear($user->college_id,$department,$year);
            Session::set('selected_vchip_placement_year', $year);
            Session::set('selected_vchip_placement_department', $department);
        }

        $allSkills = Skill::all();
        if(is_object($allSkills) && false == $allSkills->isEmpty()){
            foreach($allSkills as $skill){
                $userSkills[$skill->id] = $skill->name;
            }
        }
        $collegeDepts = CollegeDept::where('college_id', $user->college_id)->get();
        return view('dashboard.studentVchipPlacement', compact('students', 'year', 'collegeDepts','department','userSkills'));
    }

    protected function studentCollegeCourses($collegeUrl,Request $request,$id=Null){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $results = [];
        $students = [];
        $courses = [];
        $selectedStudent = '';
        $user = Auth::user();
        $categories = CollegeCategory::getCollegeCategoriesByCollegeIdByDeptId($user->college_id);
        if(empty($id)){
            $id = Session::get('selected_student');
        }
        if($id > 0){
            $selectedStudent = User::find($id);
            if(!is_object($selectedStudent)){
                return redirect()->back();
            }
            // if(4 == $user->user_type){
            //     $students = User::getAllStudentsByCollegeIdByDeptId($selectedStudent->college_id,$user->college_dept_id,$selectedStudent->user_type);
            // } else {
                $students = User::getAllStudentsByCollegeIdByDeptId($selectedStudent->college_id,$selectedStudent->college_dept_id,$selectedStudent->user_type);
            // }
            $courses = CourseCourse::getRegisteredCollegeOnlineCourses($id);
            Session::set('selected_student', $id);
            Session::set('selected_user_type', $selectedStudent->user_type);
        }
        if(5 == $user->user_type || 6 == $user->user_type){
            $collegeDepts = CollegeDept::where('college_id', $user->college_id)->get();
        } else {
            $collegeDepts = [];
        }

        return view('dashboard.studentCollegeCourses', compact('students', 'selectedStudent', 'collegeDepts', 'categories', 'courses'));
    }

    protected function lecturerCourses($collegeUrl,Request $request,$id=Null){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $results = [];
        $users = [];
        $selectedUser = '';
        $user = Auth::user();
        if(empty($id)){
            $id = Session::get('selected_lecturer');
        }
        if($id > 0){
            $selectedUser = User::find($id);
            if(!is_object($selectedUser)){
                return redirect()->back();
            }
            $users = User::getAllStudentsByCollegeIdByDeptId($selectedUser->college_id,$selectedUser->college_dept_id,$selectedUser->user_type);
            $results = CourseCourse::getCoursesByUserIdByCollegeIdByDeptId($id,$selectedUser->college_id);
            Session::set('selected_lecturer', $id);
            Session::set('selected_user_type', $selectedUser->user_type);
        } else {
            $users = User::getAllStudentsByCollegeIdByDeptId($user->college_id,$user->college_dept_id,User::Lecturer);
        }
        if(5 == $user->user_type || 6 == $user->user_type){
            $collegeDepts = CollegeDept::where('college_id', $user->college_id)->get();
        } else {
            $collegeDepts = [];
        }
        return view('dashboard.lecturerCourses', compact('users', 'selectedUser', 'results','collegeDepts'));
    }

    protected function getLecturerCourses(Request $request){
        $lecturer = $request->lecturer;
        $user = Auth::user();
        Session::set('selected_lecturer', $lecturer);
        return CourseCourse::getCoursesByUserIdByCollegeIdByDeptId($lecturer,$user->college_id);
    }

    protected function studentVchipCourses($collegeUrl,Request $request,$id=Null){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $results = [];
        $students = [];
        $courses = [];
        $selectedStudent = '';
        $user = Auth::user();
        $categories = CourseCategory::getCourseCategoriesForAdmin();
        if(empty($id)){
            $id = Session::get('selected_student');
        }
        if($id > 0){
            $selectedStudent = User::find($id);
            if(!is_object($selectedStudent)){
                return redirect()->back();
            }
            // if(4 == $user->user_type){
            //     $students = User::getAllStudentsByCollegeIdByDeptId($selectedStudent->college_id,$user->college_dept_id,$selectedStudent->user_type);
            // } else {
                $students = User::getAllStudentsByCollegeIdByDeptId($selectedStudent->college_id,$selectedStudent->college_dept_id,$selectedStudent->user_type);
            // }
            $courses = CourseCourse::getRegisteredOnlineCourses($id);
            Session::set('selected_student', $id);
            Session::set('selected_user_type', $selectedStudent->user_type);
        }
        if(5 == $user->user_type || 6 == $user->user_type){
            $collegeDepts = CollegeDept::where('college_id', $user->college_id)->get();
        } else {
            $collegeDepts = [];
        }

        return view('dashboard.studentVchipCourses', compact('students', 'selectedStudent', 'collegeDepts', 'categories', 'courses'));
    }

    protected function updateProfile(Request $request){
        DB::beginTransaction();
        try
        {
            $user = User::updateUser($request);
            if(is_object($user)){
                if($user->college_id > 0){
                    $collegeUrl = $user->college->url;
                } else {
                    $collegeUrl = 'other';
                }
                Session::put('college_user_url',$collegeUrl);
                DB::commit();
                return Redirect::to('college/'.Session::get('college_user_url').'/profile')->with('message', 'User profile updated successfully.');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('college/'.Session::get('college_user_url').'/profile');
    }

    protected function updateUserProfile(Request $request){
        DB::beginTransaction();
        try
        {
            $user = User::updateUserProfile($request);
            if(is_object($user)){
                DB::commit();
                return Redirect::to('college/'.Session::get('college_user_url').'/students')->with('message', 'User profile updated successfully.');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('college/'.Session::get('college_user_url').'/students');
    }

    protected function showStudentsByDepartmentByYear(Request $request){
        $user = Auth::user();
        if(3 == $user->user_type || 4 == $user->user_type){
            return User::getAllUsersByCollegeIdByDeptIdByYearByUserType($user->college_id,$user->college_dept_id, $request->year, $request->user_type);
        } else {
            return User::getAllUsersByCollegeIdByDeptIdByYearByUserType($user->college_id,$request->department, $request->year, $request->user_type);
        }
    }

    protected function showPlacementVideoByDepartmentByYear(Request $request){
        $user = Auth::user();
        Session::set('selected_college_placement_year',$request->year);
        Session::set('selected_college_placement_department',$request->department);
        $result['users'] = User::showPlacementVideoByDepartmentByYear($user->college_id,$request->department, $request->year, User::Student);
        $allSkills = Skill::all();
        if(is_object($allSkills) && false == $allSkills->isEmpty()){
            foreach($allSkills as $skill){
                $result['skills'][$skill->id] = $skill->name;
            }
        }
        return $result;
    }


    protected function showVchipPlacementVideoByDepartmentByYear(Request $request){
        $user = Auth::user();
        Session::set('selected_vchip_placement_year',$request->year);
        Session::set('selected_vchip_placement_department',$request->department);
        $result['users'] = UserData::showVchipPlacementVideoByDepartmentByYear($user->college_id,$request->department, $request->year);
        $allSkills = Skill::all();
        if(is_object($allSkills) && false == $allSkills->isEmpty()){
            foreach($allSkills as $skill){
                $result['skills'][$skill->id] = $skill->name;
            }
        }
        return $result;
    }

    protected function searchStudentByDeptByYearByName(Request $request){
        $user = Auth::user();
        Session::set('selected_college_placement_year',$request->year);
        Session::set('selected_college_placement_department',$request->department);
        $result['users'] = User::searchStudentByDeptByYearByName($request);
        $allSkills = Skill::all();
        if(is_object($allSkills) && false == $allSkills->isEmpty()){
            foreach($allSkills as $skill){
                $result['skills'][$skill->id] = $skill->name;
            }
        }
        return $result;
    }

    protected function searchVchipStudentByDeptByYearByName(Request $request){
        $user = Auth::user();
        Session::set('selected_vchip_placement_year',$request->year);
        Session::set('selected_vchip_placement_department',$request->department);
        $result['users'] = UserData::searchVchipStudentByDeptByYearByName($request);
        $allSkills = Skill::all();
        if(is_object($allSkills) && false == $allSkills->isEmpty()){
            foreach($allSkills as $skill){
                $result['skills'][$skill->id] = $skill->name;
            }
        }
        return $result;
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

    protected function showCollegeStudentCourses(Request $request){
        $selectedStudent = User::getStudentById($request->student);
        if(is_object($selectedStudent)){
            Session::set('selected_student', $selectedStudent->id);
            Session::set('selected_user_type', $selectedStudent->user_type);
        }
        return CourseCourse::getOnlineCollegeCoursesByUserIdByCategoryBySubCategory($request->student,$request->category,$request->subcategory);
    }

    protected function myVchipCourseResults(Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $categoryIds = [];
        $user = Auth::user();
        $courses = CourseCourse::getRegisteredOnlineCourses($user->id);
        if(is_object($courses) && false == $courses->isEmpty()){
            foreach($courses as $course){
                $categoryIds[] = $course->course_category_id;
            }
        }
        $categories = CourseCategory::find($categoryIds);
        return view('dashboard.myVchipCourseResult', compact('categories', 'courses'));
    }

    protected function myCollegeCourseResults(Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $categoryIds = [];
        $user = Auth::user();
        $courses = CourseCourse::getRegisteredCollegeOnlineCourses($user->id);
        if(is_object($courses) && false == $courses->isEmpty()){
            foreach($courses as $course){
                $categoryIds[] = $course->course_category_id;
            }
        }
        $categories = CollegeCategory::find($categoryIds);
        return view('dashboard.myCollegeCourseResult', compact('categories', 'courses'));
    }

    protected function myCollegeTestResults(Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $user = Auth::user();
        $categories = CollegeCategory::getTestCategoriesByRegisteredSubjectPapersByUserId($user->id,$user->college_id,$user->college_dept_id);
        $results = Score::getScoresWithCollegeTestCategoriesByCollegeIdByDeptIdByUserId($user->college_id,$user->college_dept_id,$user->id);
        return view('dashboard.myCollegeTestResults', compact('categories','results'));
    }

    protected function myVchipTestResults(Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $user = Auth::user();
        $categories = TestCategory::getTestCategoriesByRegisteredSubjectPapersByUserId($user->id);
        $results = Score::getScoresWithTestCategoriesByUserId($user->id);
        return view('dashboard.myVchipTestResults', compact('categories','results'));
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

    protected function collegeTestResults(){
        $colleges = College::all();
        $user = Auth::user();
        $categories = CollegeCategory::getCollegeCategoriesByCollegeIdByDeptId($user->college_id);
        $scores =[];
        return view('dashboard.collegeTestResults', compact('colleges', 'categories', 'scores'));
    }

    protected function vchipTestResults(){
        $colleges = College::all();
        $categories = TestCategory::getAllTestCategories();
        $scores =[];
        return view('dashboard.vchipTestResults', compact('colleges', 'categories', 'scores'));
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

    protected function notifications(Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        DB::beginTransaction();
        try
        {
            $loginUser = Auth::user();
            Notification::readUserNotifications($loginUser->id);
            DB::commit();
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        $notifications =  Notification::where('admin_id', 0)->where('created_to', $loginUser->id)->orderBy('id', 'desc')->paginate();
        return view('dashboard.notifications', compact('notifications'));
    }

    protected function adminMessages(Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
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

    protected function myAssignments($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $assignments = AssignmentQuestion::getStudentAssignments();
        $assignmentTeachers = User::getTeachers();
        return view('dashboard.assignmentLists', compact('assignments', 'assignmentTeachers'));
    }

    protected function doAssignment($collegeUrl,$id,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $assignment = AssignmentQuestion::find($id);
        $answers = AssignmentAnswer::where('student_id', Auth::user()->id)->where('assignment_question_id', $id)->get();
        return view('dashboard.assignmentDetails', compact('assignment', 'answers'));
    }

    protected function createAssignmentAnswer($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $questionId   = InputSanitise::inputInt($request->get('assignment_question_id'));
        $studentId   = InputSanitise::inputInt($request->get('student_id'));
        $answer = $request->get('answer');
        $loginUser = Auth::user();
        if(empty($answer) && false == $request->exists('attached_link')){
            if(User::Student == $loginUser->user_type){
                return Redirect::to('college/'.Session::get('college_user_url').'/doAssignment/'.$questionId);
            } else {
                return Redirect::to('college/'.Session::get('college_user_url').'/assignmentRemark/'.$questionId.'/'.$studentId);
            }
        }

        DB::beginTransaction();
        try
        {
            AssignmentAnswer::addAssignmentAnswer($request);
            DB::commit();
            if(User::Student == $loginUser->user_type){
                return Redirect::to('college/'.Session::get('college_user_url').'/doAssignment/'.$questionId)->with('message', 'Assignment updated successfully.');
            } else {
                return Redirect::to('college/'.Session::get('college_user_url').'/assignmentRemark/'.$questionId.'/'.$studentId )->with('message', 'Assignment updated successfully.');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('college/'.Session::get('college_user_url').'/doAssignment/'.$questionId);
    }

    protected function studentsAssignment($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $assignmentSubjects = [];
        $assignmentTopics = [];
        $assignmentUsers = [];
        $assignment = '';
        $selectedAssignmentYear = Session::get('selected_assignment_year');
        $selectedAssignmentSubject  = Session::get('selected_assignment_subject');
        $selectedAssignmentTopic = Session::get('selected_assignment_topic');
        $selectedAssignmentStudent = Session::get('selected_assignment_student');
        $selectedAssignmentDepartment = Session::get('selected_assignment_department');

        if($selectedAssignmentSubject > 0 && $selectedAssignmentYear > 0){
            $assignmentSubjects = CollegeSubject::getCollegeSubjectByYear($selectedAssignmentYear);
        }
        if($selectedAssignmentSubject > 0){
            $assignmentTopics = AssignmentTopic::getAssignmentTopics($selectedAssignmentSubject);
        }
        if($selectedAssignmentStudent > 0){
            $assignmentUsers = User::getAssignmentUsers($selectedAssignmentYear,$selectedAssignmentDepartment);
        }
        if($selectedAssignmentSubject > 0 && $selectedAssignmentYear > 0 && $selectedAssignmentTopic > 0 && $selectedAssignmentStudent > 0){
            $assignment = AssignmentQuestion::getAssignmentByTopic($selectedAssignmentTopic);
        }
        $loginUser = Auth::user();
        $selectedStudentName = '';
        $selectedStudent = User::find($selectedAssignmentStudent);
        if(is_object($selectedStudent)){
            $selectedStudentName = $selectedStudent->name;
        }
        if(User::Hod == $loginUser->user_type || User::Lecturer == $loginUser->user_type){
            $deptIds = explode(',',$loginUser->assigned_college_depts);
            $collegeDepts = CollegeDept::getDepartmentsByCollegeIdByDeptIds($loginUser->college_id,$deptIds);
        } else {
            $collegeDepts = CollegeDept::getDepartmentsByCollegeId($loginUser->college_id);
        }
        return view('dashboard.studentsAssignment', compact('selectedAssignmentYear','selectedAssignmentSubject','selectedAssignmentTopic','selectedAssignmentStudent', 'assignmentSubjects', 'assignmentTopics', 'assignmentUsers', 'assignment','collegeDepts','selectedAssignmentDepartment','selectedStudentName'));
    }

    protected function assignmentRemark($collegeUrl,$assignmentId, $studentId,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
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

    protected function studentVideo($collegeUrl,Request $request,$id=Null){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $students = [];
        $selectedStudent = '';
        $selectedStudentSkills = [];
        $user = Auth::user();
        if(empty($id)){
            $id = Session::get('selected_student');
        }
        if($id > 0){
            $selectedStudent = User::find($id);
            if(!is_object($selectedStudent)){
                return redirect()->back();
            }
            $selectedStudentSkills = explode(',', $selectedStudent->skills);
            $students = User::getAllStudentsByCollegeIdByDeptId($selectedStudent->college_id,$selectedStudent->college_dept_id,$selectedStudent->user_type);
            Session::set('selected_student', $id);
            Session::set('selected_user_type', $selectedStudent->user_type);
        }
        $collegeDepts = CollegeDept::where('college_id', $user->college_id)->get();
        $skills = Skill::all();
        return view('dashboard.studentVideo', compact('students', 'selectedStudent', 'collegeDepts','skills','selectedStudentSkills'));
    }

    protected function updateStudentVideo($collegeUrl,Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        DB::beginTransaction();
        try
        {
            $student = User::find($request->student);
            if(is_object($student)){
                $userStoragePath = "userStorage/".$student->id;
                if(!is_dir($userStoragePath)){
                    mkdir($userStoragePath);
                }

                $dom = new \DOMDocument;
                $dom->loadHTML($request->recorded_video);
                $iframes = $dom->getElementsByTagName('iframe');
                foreach ($iframes as $iframe) {
                    $url =  '?enablejsapi=1';
                    if (strpos($iframe->getAttribute('src'), $url) === false) {
                        $iframe->setAttribute('src', $iframe->getAttribute('src').$url);
                    }
                }
                $html = $dom->saveHTML();
                $body = explode('<body>', $html);
                $body = explode('</body>', $body[1]);

                $student->recorded_video = $body[0];

                if($request->exists('resume')){
                    $userResume = $request->file('resume')->getClientOriginalName();
                    if(!empty($student->resume) && file_exists($student->resume)){
                        unlink($student->resume);
                    }
                    $request->file('resume')->move($userStoragePath, $userResume);
                    $student->resume = $userStoragePath."/".$userResume;
                }
                $userSkills = '';

                if(is_array($request->skills)){
                    foreach($request->skills as $index => $skill){
                        if(0 == $index){
                            $userSkills = $skill;
                        } else {
                            $userSkills .= ','.$skill;
                        }
                    }
                    $student->skills = $userSkills;
                } else {
                    $student->skills = $userSkills;
                }
                $student->save();
                DB::commit();
                Session::set('selected_student', $student->id);
                Session::set('selected_user_type', $student->user_type);
                return Redirect::to('college/'.$collegeUrl.'/studentVideo')->with('message', 'User video url updated successfully.');
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('college/'.$collegeUrl.'/studentVideo');
    }

    protected function allChatMessages(Request $request){
        if( false == InputSanitise::checkCollegeUrl($request)){
            return Redirect::to('/');
        }
        $result = ChatMessage::showchatusers();
        $users = $result['chatusers'];
        if(isset($result['unreadCount'])){
            $unreadCount = $result['unreadCount'];
        }
        $onlineUsers = $result['onlineUsers'];
        return view('dashboard.chatMessages', compact('users', 'unreadCount', 'onlineUsers'));
    }

    protected function dashboardPrivateChat(Request $request){
        return ChatMessage::privatechat($request);
    }

    protected function dashboardSendMessage(Request $request){
        return ChatMessage::sendMessage($request);
    }

    protected function getContacts(){
        return ChatMessage::showchatusers();
    }

    protected function addEmail(Request $request){
        $v = Validator::make($request->all(), $this->validateAddEmail);
        if ($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
        if(!empty($request->get('email'))){
            $checkEmail = User::where('email', $request->get('email'))->first();
            if(is_object($checkEmail)){
                return Redirect::to('college/'.Session::get('college_user_url').'/profile')->withErrors('The email id '.$request->get('email').' is already exist.');
            }
        }
        DB::beginTransaction();
        try
        {
            $user = User::addEmail($request);
            if(is_object($user)){
                DB::commit();
                if(!empty($user->email) && filter_var($user->email, FILTER_VALIDATE_EMAIL)){
                    // send mail
                    $email = new EmailVerification(new User(['email_token' => $user->email_token, 'name' => $user->name]));
                    Mail::to($user->email)->send($email);
                    return Redirect::to('college/'.Session::get('college_user_url').'/profile')->with('message', 'Verification email sent successfully. please check email and verify.');
                } else {
                    return Redirect::to('college/'.Session::get('college_user_url').'/profile')->with('message', 'Email added successfully!');
                }
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return redirect()->back()->withErrors('something went wrong.');
        }
        return Redirect::to('college/'.Session::get('college_user_url').'/profile');
    }

    protected function updateEmail(Request $request){
        $email = $request->get('email');
        if(!empty($email)){
            $existingUser = User::where('email', $email)->first();
            if(!is_object($existingUser)){
                DB::beginTransaction();
                try
                {
                    $user = Auth::user();
                    $user->email = $email;
                    $user->verified = 0;
                    $user->email_token = str_random(60);
                    $user->save();

                    $email = new EmailVerification(new User(['email_token' => $user->email_token, 'name' => $user->name]));
                    Mail::to($user->email)->send($email);
                    DB::commit();
                    return Redirect::to('college/'.Session::get('college_user_url').'/profile')->with('message', 'Verification email sent successfully. please check email and verify.');
                }
                catch(\Exception $e)
                {
                    DB::rollback();
                    return redirect()->back()->withErrors('something went wrong.');
                }
            }
            return redirect()->back()->withErrors(['Email id already exist, so please use another email id.']);
        }
        return redirect()->back()->withErrors(['Please enter email id.']);
    }

    protected function verifyMobile(Request $request){
        $userMobile = $request->get('phone');
        $userOtp = $request->get('user_otp');
        $serverOtp = Cache::get($userMobile);
        if($serverOtp == $userOtp){
            DB::beginTransaction();
            try
            {
                User::verifyMobile($request);
                DB::commit();
                if(Cache::has($userMobile) && Cache::has('mobile')){
                    Cache::forget($userMobile);
                    Cache::forget('mobile-'.$userMobile);
                }
                return Redirect::to('college/'.Session::get('college_user_url').'/profile')->with('message', 'Mobile number verified successfully.');
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return redirect()->back()->withErrors('something went wrong.');
            }
        } else {
            return Redirect::to('college/'.Session::get('college_user_url').'/profile')->withErrors('Entered wrong otp.');
        }
    }

    protected function updateMobile(Request $request){
        $userMobile = $request->get('phone');
        $userOtp = $request->get('user_otp');
        $serverOtp = Cache::get($userMobile);
        if($serverOtp == $userOtp){
            DB::beginTransaction();
            try
            {
                User::updateMobile($request);
                DB::commit();
                if(Cache::has($userMobile) && Cache::has('mobile')){
                    Cache::forget($userMobile);
                    Cache::forget('mobile-'.$userMobile);
                }
                return Redirect::to('college/'.Session::get('college_user_url').'/profile')->with('message', 'Mobile number updated successfully.');
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return redirect()->back()->withErrors('something went wrong.');
            }
        } else {
            return Redirect::to('college/'.Session::get('college_user_url').'/profile')->withErrors('Entered wrong otp.');
        }
    }
}