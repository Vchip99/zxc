<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\ResetPasswordMailNotificatipn;
use App\Libraries\InputSanitise;
use App\Models\Client;
use App\Models\CollegeDept;
use App\Models\College;
use App\Models\RegisterDocuments;
use App\Models\RegisterFavouriteDocuments;
use App\Models\RegisterLiveCourse;
use App\Models\RegisterOnlineCourse;
use App\Models\RegisterPaper;
use App\Models\RegisterProject;
use App\Models\BlogComment;
use App\Models\BlogCommentLike;
use App\Models\BlogSubComment;
use App\Models\BlogSubCommentLike;
use App\Models\CourseComment;
use App\Models\CourseCommentLike;
use App\Models\CourseSubComment;
use App\Models\CourseSubCommentLike;
use App\Models\CourseVideoLike;
use App\Models\DiscussionPost;
use App\Models\LiveCourseComment;
use App\Models\LiveCourseCommentLike;
use App\Models\LiveCourseSubComment;
use App\Models\LiveCourseSubCommentLike;
use App\Models\LiveCourseVideoLike;
use App\Models\VkitProjectLike;
use App\Models\VkitProjectComment;
use App\Models\VkitProjectCommentLike;
use App\Models\VkitProjectSubComment;
use App\Models\VkitProjectSubCommentLike;
use App\Models\Score;
use App\Models\UserSolution;
use App\Models\Notification;
use App\Models\ReadNotification;
use App\Models\PlacementProcessLike;
use App\Models\PlacementProcessCommentLike;
use App\Models\PlacementProcessSubCommentLike;
use App\Models\PlacementProcessComment;
use App\Models\PlacementProcessSubComment;
use Auth, DB;
use Intervention\Image\ImageManagerStatic as Image;

class User extends Authenticatable
{
    use Notifiable;

    protected $connection = 'mysql';

    const Admin = 1;
    const Student = 2;
    const Lecturer = 3;
    const Hod = 4;
    const Directore = 5;
    const TNP = 6;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','phone', 'user_type', 'verified', 'admin_approve', 'degree', 'college_id', 'college_dept_id', 'year', 'roll_no', 'other_source', 'photo','resume','recorded_video','email_token', 'remember_token', 'google_provider_id', 'facebook_provider_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Send a password reset email to the user
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordMailNotificatipn($token));
    }

    // Set the verified status to true and make the email token null
    public function verified()
    {
        $this->verified = 1;
        $this->email_token = null;
        $this->save();
        return;
    }

    protected function getAllStudentsByCollegeIdByDeptId($college,$department,$userType=NULL){
        $result =  static::where('college_id', $college)
                ->where('college_dept_id', $department);
        if($userType > 0){
            $result->where('user_type', $userType);
        }
        return $result->get();
    }

    protected function getAllUsersByCollegeIdByDeptIdByYearByUserType($college,$department,$year,$userType){
        $result = static::where('college_id', $college);
        if($userType > 0){
            $result->where('user_type', $userType);
        }
        if($department > 0){
            $result->where('college_dept_id', $department);
        }
        if($year > 0){
            $result->where('year', $year);
        }
        return $result->select('users.id', 'users.name')->get();
    }

    protected static function changeUserApproveStatus(Request $request){
        $collegeId = InputSanitise::inputInt($request->college_id);
        $departmentId = InputSanitise::inputInt($request->department_id);
        $studentId = InputSanitise::inputInt($request->student_id);
        $year = InputSanitise::inputInt($request->year);

        $result = static::where('id', $studentId);

        if($collegeId > 0){
            $result->where('college_id', $collegeId);
        }
        if($departmentId > 0){
            $result->where('college_dept_id', $departmentId);
        }
        if($year > 0){
            $result->where('year', $year);
        }
        $student = $result->first();
        if(is_object($student)){
            if( 1 == $student->admin_approve){
                $student->admin_approve = 0;
            } else {
                $student->admin_approve = 1;
            }
            $student->save();
        }
        return;
    }

    protected static function deleteStudentFromCollege(Request $request){
        $collegeId = InputSanitise::inputInt($request->college_id);
        $departmentId = InputSanitise::inputInt($request->department_id);
        $studentId = InputSanitise::inputInt($request->student_id);
        $year = InputSanitise::inputInt($request->year);

        $student = static::where('college_id', $collegeId)
                ->where('college_dept_id', $departmentId)
                ->where('year', $year)
                ->where('id', $studentId)->first();
        if(is_object($student)){
            $student->college_id = 'other';
            $student->college_dept_id = 0;
            $student->year = 0;
            $student->roll_no = 0;
            $student->other_source = 'deleted by user- '.Auth::user()->name.' from college - '.$collegeId;
            $student->save();
            return 'true';
        }
        return 'false';
    }

    public function college(){
        return $this->belongsTo(College::class, 'college_id');
    }

    public function department(){
        return $this->belongsTo(CollegeDept::class, 'college_dept_id');
    }

    protected static function searchStudent(Request $request){
        $user = Auth::user();
        $student = static::join('college_depts', 'college_depts.id', '=', 'users.college_dept_id')
                    ->where('users.college_id', $user->college_id);
        if($request->department > 0){
            $student->where('users.college_dept_id', $request->department);
        } else if(3 == $user->user_type || 4 == $user->user_type){
            $student->where('users.college_dept_id', $user->college_dept_id);
        }
        if($request->user_type > 0){
            $student->where('users.user_type', $request->user_type);
        }
        if($request->year > 0){
            $student->where('users.year', $request->year);
        }
        if(!empty($request->student)){
            $student->where('users.name', 'LIKE', '%'.$request->student.'%');
        }
        return $student->select('users.id','users.name','users.roll_no','users.college_dept_id','users.college_id','users.year','users.email','users.phone','users.admin_approve', 'users.recorded_video','college_depts.name as department')->get();
    }

    protected static function updateUser(Request $request){
        $user = Auth::user();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        if(self::Student == $user->user_type){
            $user->year = $request->year;
            $user->roll_no = $request->roll_no;
        }

        $userStoragePath = "userStorage/".$user->id;
        if(!is_dir($userStoragePath)){
            mkdir($userStoragePath);
        }
        if($request->exists('photo')){
            $userImage = $request->file('photo')->getClientOriginalName();
            $userImagePath = $userStoragePath."/".$user->photo;
            if(!empty($user->photo) && file_exists($userImagePath)){
                unlink($userImagePath);
            }
            $request->file('photo')->move($userStoragePath, $userImage);
            $dbUserImagePath = $userStoragePath."/".$userImage;
        }
        if($request->exists('resume')){
            $userResume = $request->file('resume')->getClientOriginalName();
            $userResumePath = $userStoragePath."/".$user->resume;
            if(!empty($user->resume) && file_exists($userResumePath)){
                unlink($userResumePath);
            }
            $request->file('resume')->move($userStoragePath, $userResume);
            $dbUserResumePath = $userStoragePath."/".$userResume;
        }
        if(!empty($dbUserImagePath)){
            $user->photo = $dbUserImagePath;
            // open image
            $img = Image::make($user->photo);
            // enable interlacing
            $img->interlace(true);
            // save image interlaced
            $img->save();
        }
        if(!empty($dbUserResumePath)){
            $user->resume = $dbUserResumePath;
        }
        $user->save();
        return $user;
    }

    protected static function getStudentById($studentId){
        return static::where('id', $studentId)
                ->where('user_type', self::Student)
                ->select('id','resume','recorded_video')->first();
    }

    protected static function deleteStudent(Request $request){
        $userId = InputSanitise::inputInt($request->student_id);
        $student = static::find($userId);
        if(is_object($student)){
            $student->deleteOtherInfoByUserId($userId);
            $student->delete();
            return 'true';
        }
        return 'false';
    }

    public function deleteOtherInfoByUserId($userId){
        $userStorage = "userStorage/".$userId;
        if(is_dir($userStorage)){
            InputSanitise::delFolder($userStorage);
        }
        Score::deleteUserScoresByUserId($userId);
        UserSolution::deleteUserSolutionsByUserId($userId);
        RegisterDocuments::deleteRegisteredDocsByUserId($userId);
        RegisterFavouriteDocuments::deleteRegisteredFavouriteDocumentsByUserId($userId);
        RegisterLiveCourse::deleteRegisteredLiveCourseByUserIdByCourseId($userId);
        RegisterOnlineCourse::deleteRegisteredOnlineCoursesByUserId($userId);
        RegisterPaper::deleteRegisteredPapersByUserId($userId);
        RegisterProject::deleteRegisteredVkitProjectsByUserId($userId);
        BlogComment::deleteBlogCommentsByUserId($userId);
        BlogCommentLike::deleteBlogCommentLikesByUserId($userId);
        BlogSubComment::deleteBlogSubCommentsByUserId($userId);
        BlogSubCommentLike::deleteBlogSubCommentLikesByUserId($userId);
        CourseComment::deleteCourseCommentsByUserId($userId);
        CourseCommentLike::deleteCourseCommentLikesByUserId($userId);
        CourseSubComment::deleteCourseSubCommentsByUserId($userId);
        CourseSubCommentLike::deleteCourseSubCommentLikesByUserId($userId);
        CourseVideoLike::deleteCourseVideoLikesByUserId($userId);
        DiscussionPost::deleteAllDiscussionPostsByUserId($userId);
        LiveCourseComment::deleteLiveCourseCommentsByUserId($userId);
        LiveCourseCommentLike::deleteLiveCourseCommentLikesByUserId($userId);
        LiveCourseSubComment::deleteLiveCourseSubCommentsByUserId($userId);
        LiveCourseSubCommentLike::deleteLiveCourseSubCommentLikesByUserId($userId);
        LiveCourseVideoLike::deleteLiveCourseVideoLikesByUserId($userId);
        VkitProjectLike::deleteVkitProjectLikesByUserId($userId);
        VkitProjectComment::deleteVkitProjectCommentsByUserId($userId);
        VkitProjectCommentLike::deleteVkitProjectCommentLikesByUserId($userId);
        VkitProjectSubComment::deleteVkitProjectSubCommentsByUserId($userId);
        VkitProjectSubCommentLike::deleteVkitProjectSubCommentLikesByUserId($userId);

        PlacementProcessComment::deletePlacementProcessCommentsByUserId($userId);
        PlacementProcessSubComment::deletePlacementProcessSubCommentsByUserId($userId);

        PlacementProcessLike::deletePlacementProcessLikesByUserId($userId);
        PlacementProcessCommentLike::deletePlacementProcessCommentLikesByUserId($userId);
        PlacementProcessSubCommentLike::deletePlacementProcessSubCommentLikesByUserId($userId);


        return;
    }

    protected static function showOtherStudents(){
        return static::where('college_id', 'other')->select('id', 'name', 'email', 'phone', 'admin_approve', 'other_source', 'college_id', 'user_type', 'recorded_video')->get();
    }

    protected static function searchUsers(Request $request){
        $collegeId = $request->college_id;
        $departmentId = InputSanitise::inputInt($request->department_id);
        $userType = InputSanitise::inputInt($request->user_type);
        $year = InputSanitise::inputInt($request->selected_year);
        $userName = InputSanitise::inputString($request->student);
        if('other' == $collegeId){
            return static::where('users.college_id', $collegeId)
                        ->where('users.name', 'LIKE', '%'.$userName.'%')
                        ->select('id', 'name', 'email', 'phone', 'admin_approve', 'other_source', 'college_id', 'user_type', 'recorded_video')->get();
        } else {
            if(self::Directore == $userType || self::TNP == $userType){
                $student = static::where('users.user_type', $userType);
            } else {
                $student = static::join('college_depts', 'college_depts.id', '=', 'users.college_dept_id')
                            ->where('users.user_type', $userType);
            }
            if($departmentId > 0){
                $student->where('users.college_dept_id', $departmentId);
            }
            if($year > 0){
                $student->where('users.year', $year);
            }
            if($userName){
                $student->where('users.name', 'LIKE', '%'.$userName.'%');
            }
            if(self::Directore == $userType || self::TNP == $userType){
                return $student->where('users.college_id', $collegeId)->select('users.id','users.name','users.roll_no','users.college_dept_id','users.college_id','users.user_type','users.year','users.email','users.phone','users.admin_approve')->get();
            } else {
                return $student->where('users.college_id', $collegeId)
                                ->select('users.id','users.name','users.roll_no','users.college_dept_id','users.college_id','users.user_type','users.year','users.email','users.phone','users.admin_approve', 'users.recorded_video','college_depts.name as department')
                                ->get();
            }
        }
    }

    protected static function getClients(){
        return Client::all();
    }

    protected static function unApproveUsers($collegeId){
        if($collegeId > 0){
            $result = static::where('users.admin_approve', 0)->where('users.college_id', $collegeId);
            return $result->select('users.id','users.name','users.college_id','users.other_source as collegeName','users.admin_approve')->orderBy('college_id')->get();
        } else {
            $result = static::where('users.admin_approve', 0);
        return $result->where('user_type', '!=', '1')->select('users.id','users.name','users.college_id','users.other_source as collegeName','users.admin_approve')->orderBy('college_id')->get();
        }
    }

    function userNotificationCount(){
        return Notification::where('is_seen', 0)->where('created_to', Auth::user()->id)->count();
    }

    function adminNotificationCount($year=NULL,$month=NULL){
        $ids = ReadNotification::getReadNotificationIdsByUser();
        $result = Notification::where('admin_id', 1)->where('is_seen', 0)->whereNotIn('id', $ids);
        if($year > 0){
            $result->whereYear('created_at', $year);
        }
        if($month > 0){
            $result->whereMonth('created_at', $month);
        }
        return $result->count();
    }

    protected static function getAssignmentUsers($selectedAssignmentYear){
        return static::where('user_type', self::Student)->where('college_id', Auth::user()->college_id)->where('college_dept_id', Auth::user()->college_dept_id)->where('year', $selectedAssignmentYear)->get();
    }

    protected static function getTeachers($collegeDept=NULL){
        if( self::Student == Auth::user()->user_type){
            return static::join('assignment_questions', 'assignment_questions.lecturer_id', '=', 'users.id')
                ->whereIn('users.user_type', array(self::Lecturer,self::Hod))
                ->where('users.college_id', Auth::user()->college_id)
                ->where('users.college_dept_id', Auth::user()->college_dept_id)
                ->where('assignment_questions.year', Auth::user()->year)
                ->select('users.id', 'users.*')->groupBy('users.id')->get();
        } else if( self::Lecturer == Auth::user()->user_type){
            return static::join('assignment_questions', 'assignment_questions.lecturer_id', '=', 'users.id')
                ->whereIn('users.user_type', array(self::Lecturer,self::Hod))
                ->where('users.college_id', Auth::user()->college_id)
                ->where('users.college_dept_id', Auth::user()->college_dept_id)
                // ->where('assignment_questions.year', Auth::user()->year)
                ->select('users.id', 'users.*')->groupBy('users.id')->get();
        } else if( self::Hod == Auth::user()->user_type){
            return static::join('assignment_questions', 'assignment_questions.lecturer_id', '=', 'users.id')
                ->whereIn('users.user_type', array(self::Lecturer,self::Hod))
                ->where('users.college_id', Auth::user()->college_id)
                ->where('users.college_dept_id', Auth::user()->college_dept_id)
                ->select('users.id', 'users.*')->groupBy('users.id')->get();
        } else if( self::Directore == Auth::user()->user_type){
            return static::join('assignment_questions', 'assignment_questions.lecturer_id', '=', 'users.id')
                ->whereIn('users.user_type', array(self::Lecturer,self::Hod))
                ->where('users.college_dept_id', $collegeDept)
                ->select('users.id', 'users.*')->groupBy('users.id')->get();
        }
    }
}
