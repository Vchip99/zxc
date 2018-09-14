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
use App\Models\ChatMessage;
use App\Models\PlacementExperiance;
use Auth, DB, Cache;
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
        'name', 'email', 'password','phone', 'user_type', 'verified', 'admin_approve', 'degree', 'college_id', 'college_dept_id', 'year', 'roll_no', 'other_source', 'photo','resume','recorded_video','email_token', 'remember_token', 'google_provider_id', 'facebook_provider_id','number_verified'
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

    public function chatroomid(){
        $senderUserId = Auth::user()->id;
        $receiverId = $this->id;
        $roomMembers = [$receiverId, $senderUserId];
        sort($roomMembers);
        return 'chatmessages_'.$roomMembers[0].'_'.$roomMembers[1];
    }

    public function isOnline()
    {
        return Cache::has('vchip:online_user-' . $this->id);
    }

    public function getCollegeName(){
        if($this->college_id > 0){
            return $this->college->name;
        } else {
            return $this->other_source;
        }
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
        // $user->email = $request->email;
        // $user->phone = $request->phone;
        // $user->user_type = $request->user_type;
        if(self::Student == $user->user_type){
            $user->year = $request->year;
            $user->roll_no = $request->roll_no;
        } else {
            $user->year = 0;
            $user->roll_no = 0;
        }

        $user->college_id = $request->college;
        if('other' == $request->college){
            $user->college_dept_id = 0;
            $user->other_source = $request->other_source;
        } elseif($request->college > 0){
            if(self::Directore == $request->user_type || self::TNP == $request->user_type){
                $user->college_dept_id = 0;
            } else {
                $user->college_dept_id = $request->department;
            }
            $user->other_source = '';
        }

        $userStoragePath = "userStorage/".$user->id;
        if(!is_dir($userStoragePath)){
            mkdir($userStoragePath);
        }
        if($request->exists('photo')){
            $userImage = $request->file('photo')->getClientOriginalName();
            if(!empty($user->photo) && file_exists($user->photo)){
                unlink($user->photo);
            }
            $request->file('photo')->move($userStoragePath, $userImage);
            $dbUserImagePath = $userStoragePath."/".$userImage;
        }
        if($request->exists('resume')){
            $userResume = $request->file('resume')->getClientOriginalName();
            if(!empty($user->resume) && file_exists($user->resume)){
                unlink($user->resume);
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
        ChatMessage::deleteChatMessagesByUserId($userId);
        Notification::deleteUserNotificationByUserId($userId);
        PlacementExperiance::deletePlacementExperiancesByUserId($userId);
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
        $loginUser = Auth::user();
        return static::where('user_type', self::Student)->where('college_id', $loginUser->college_id)->where('college_dept_id', $loginUser->college_dept_id)->where('year', $selectedAssignmentYear)->get();
    }

    protected static function getTeachers($collegeDept=NULL){
        $loginUser = Auth::user();
        if( self::Student == $loginUser->user_type){
            return static::join('assignment_questions', 'assignment_questions.lecturer_id', '=', 'users.id')
                ->whereIn('users.user_type', array(self::Lecturer,self::Hod))
                ->where('users.college_id', $loginUser->college_id)
                ->where('users.college_dept_id', $loginUser->college_dept_id)
                ->where('assignment_questions.year', $loginUser->year)
                ->select('users.id', 'users.*')->groupBy('users.id')->get();
        } else if( self::Lecturer == $loginUser->user_type){
            return static::join('assignment_questions', 'assignment_questions.lecturer_id', '=', 'users.id')
                ->whereIn('users.user_type', array(self::Lecturer,self::Hod))
                ->where('users.college_id', $loginUser->college_id)
                ->where('users.college_dept_id', $loginUser->college_dept_id)
                // ->where('assignment_questions.year', $loginUser->year)
                ->select('users.id', 'users.*')->groupBy('users.id')->get();
        } else if( self::Hod == $loginUser->user_type){
            return static::join('assignment_questions', 'assignment_questions.lecturer_id', '=', 'users.id')
                ->whereIn('users.user_type', array(self::Lecturer,self::Hod))
                ->where('users.college_id', $loginUser->college_id)
                ->where('users.college_dept_id', $loginUser->college_dept_id)
                ->select('users.id', 'users.*')->groupBy('users.id')->get();
        } else if( self::Directore == $loginUser->user_type){
            return static::join('assignment_questions', 'assignment_questions.lecturer_id', '=', 'users.id')
                ->whereIn('users.user_type', array(self::Lecturer,self::Hod))
                ->where('users.college_dept_id', $collegeDept)
                ->select('users.id', 'users.*')->groupBy('users.id')->get();
        }
    }

    public function unreadChatMessagesCount(){
        return ChatMessage::where('receiver_id', Auth::user()->id)->where('is_read', 0)->count();
    }

    protected static function searchContact(Request $request){
        $chatusers = [];
        $unreadCount = [];
        $currentUserId = Auth::user()->id;
        $contact = InputSanitise::inputString($request->contact);
        $users = static::where('name', 'LIKE', '%'.$contact.'%')->where('verified',1)->where('admin_approve',1)->get();
        if(is_object($users) && false == $users->isEmpty()){
            foreach($users as $user){
                if($currentUserId != $user->id){
                    if(is_file($user->photo) && true == preg_match('/userStorage/',$user->photo)){
                        $isImageExist = 'system';
                    } else if(!empty($user->photo) && false == preg_match('/userStorage/',$user->photo)){
                        $isImageExist = 'other';
                    } else {
                        $isImageExist = 'false';
                    }
                    $chatusers[] = [
                        'id' => $user->id,
                        'name' => $user->name,
                        'photo' => $user->photo,
                        'image_exist' => $isImageExist,
                        'chat_room_id' => $user->chatroomid(),
                        'college' => $user->getCollegeName(),
                    ];
                }
            }
        }
        if(count($chatusers) > 0){
            $searchIds = array_column($chatusers, 'id');
            $chatMessages = ChatMessage::where('receiver_id',  $currentUserId)->whereIn('sender_id', $searchIds)->where('is_read', 0)->select('sender_id' , \DB::raw('count(*) as unread'))->groupBy('sender_id')->get();
            if(is_object($chatMessages) && false == $chatMessages->isEmpty()){
                foreach($chatMessages as $chatMessage){
                    $unreadCount[$chatMessage->sender_id] = $chatMessage->unread;
                }
            }
        }
        $result['users'] =  $chatusers;
        $result['unreadCount'] =  $unreadCount;
        $result['onlineUsers'] = ChatMessage::checkOnlineUsers();
        return $result;
    }

    protected static function collegeUser(){
        return static::where('user_type', self::Student)->where('admin_approve', 1)->get();
    }

    protected static function verifyUserByEmailIdByPaperId($email,$paper){
        return static::join('scores', 'scores.user_id', '=', 'users.id')->where('users.email', $email)->where('users.user_type', self::Student)->where('users.admin_approve', 1)->where('scores.paper_id', $paper)->select('users.*')->first();
    }

    protected static function addEmail(Request $request){
        $user = Auth::user();
        $user->email = $request->get('email');
        $user->password = bcrypt($request->get('password'));
        $user->email_token = str_random(60);
        $user->save();
        return $user;
    }

    protected static function verifyMobile(Request $request){
        $userMobile = $request->get('phone');
        $user = Auth::user();
        $user->number_verified = 1;
        $user->save();

        // un approve number if have same number to other users with same client
        $otherUsers = static::where('phone', $user->phone)->whereNotNull('phone')->where('id','!=', $user->id)->get();
        if(is_object($otherUsers) && false == $otherUsers->isEmpty()){
            foreach($otherUsers as $otherUser){
                $otherUser->number_verified = 0;
                $otherUser->save();
            }
        }
        return;
    }

    protected static function updateMobile(Request $request){
        $userMobile = $request->get('phone');
        $user = Auth::user();
        $user->phone = $userMobile;
        $user->number_verified = 1;
        $user->save();

        // un approve number if have same number to other users with same client
        $otherUsers = static::where('phone', $user->phone)->whereNotNull('phone')->where('id','!=', $user->id)->get();
        if(is_object($otherUsers) && false == $otherUsers->isEmpty()){
            foreach($otherUsers as $otherUser){
                $otherUser->number_verified = 0;
                $otherUser->save();
            }
        }
        return;
    }
}
