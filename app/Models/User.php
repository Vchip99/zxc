<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\ResetPasswordMailNotificatipn;
use App\Libraries\InputSanitise;
use App\Models\CollegeDept;
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
use Auth;

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
        'name', 'email', 'password','phone', 'user_type', 'verified', 'admin_approve', 'degree', 'college_id', 'college_dept_id', 'year', 'roll_no', 'other_source', 'photo','resume','recorded_video','email_token', 'remember_token'
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

    protected function getAllStudentsByCollegeIdByDeptIdByYear($college,$department,$year){
        $result = static::where('college_id', $college)
                ->where('year', $year)
                ->where('user_type', self::Student);
        if($department > 0){
            $result->where('college_dept_id', $department);
        }
        return $result->select('users.id', 'users.name')->get();
    }

    protected static function changeUserApproveStatus(Request $request){
        $collegeId = InputSanitise::inputInt($request->college_id);
        $departmentId = InputSanitise::inputInt($request->department_id);
        $studentId = InputSanitise::inputInt($request->student_id);
        $year = InputSanitise::inputInt($request->year);

        $student = static::where('college_id', $collegeId)
                ->where('college_dept_id', $departmentId)
                ->where('year', $year)
                ->where('id', $studentId)->first();
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

    public function department(){
        return $this->belongsTo(CollegeDept::class, 'college_dept_id');
    }

    protected static function searchStudent(Request $request){
        $user = Auth::user();
        $student = static::join('college_depts', 'college_depts.id', '=', 'users.college_dept_id')
                    ->where('users.college_id', $user->college_id)
                    ->where('users.user_type', self::Student);
        if($request->department > 0){
            $student->where('users.college_dept_id', $request->department);
        } elseif(self::Lecturer == $user->user_type || self::Hod == $user->user_type){
            $student->where('users.college_dept_id', $user->college_dept_id);
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

    protected function deleteOtherInfoByUserId($userId){
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
        return;
    }

    protected static function showOtherStudents(){
        return static::where('college_id', 'other')->select('id', 'name', 'email', 'phone', 'admin_approve', 'other_source', 'college_id', 'user_type', 'recorded_video')->get();
    }

    protected static function changeOtherStudentApproveStatus(Request $request){
        $userId = InputSanitise::inputInt($request->student_id);
        $student = static::find($userId);
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
        $ids = static::where('user_type', 1)->get()->pluck('id');
        if(is_object($ids) && false == $ids->isEmpty()){
            return \DB::connection('mysql2')->table('clients')->whereIn('user_id', $ids)->get();
        }
        return 'false';
    }


}
