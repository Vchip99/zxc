<?php

namespace App\Models;

use App\Notifications\ClientuserResetPassword;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use DB, Auth, Cache,Mail;
use App\Models\RegisterClientOnlinePaper;
use App\Models\RegisterClientOnlineCourses;
use App\Models\ClientScore;
use App\Models\Client;
use App\Models\ClientUserSolution;
use App\Models\ClientNotification;
use App\Models\ClientReadNotification;
use App\Models\ClientOnlineCourse;
use App\Models\ClientUserPurchasedCourse;
use App\Models\ClientOnlineTestSubCategory;
use App\Models\PayableClientSubCategory;
use App\Models\ClientUserPurchasedTestSubCategory;
use App\Models\ClientCourseComment;
use App\Models\ClientCourseCommentLike;
use App\Models\ClientCourseSubComment;
use App\Models\ClientCourseSubCommentLike;
use App\Models\ClientOnlineVideoLike;
use App\Models\ClientAssignmentAnswer;
use App\Models\ClientChatMessage;
use App\Models\ClientBatch;
use Intervention\Image\ImageManagerStatic as Image;
use App\Mail\ClientUserEmailVerification;

class Clientuser extends Authenticatable
{
    use Notifiable;
    protected $connection = 'mysql2';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','phone', 'client_id', 'verified', 'client_approve', 'email_token', 'remember_token', 'photo','resume','recorded_video', 'google_provider_id', ' facebook_provider_id', 'unchecked_assignments','batch_ids','number_verified','user_type','assigned_modules'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    const Student = 1;
    const Teacher = 2;
    const Parents = 3;

    const CourseModule = 1;
    const TestModule = 2;
    const UserInfoModule = 3;
    const AllTestResultModule = 4;
    const BatchModule = 5;
    const AssignmentModule = 6;
    const EventModule = 7;

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ClientuserResetPassword($token));
    }

    protected static function verifyUserWithSubdomain(Request $request, $userId){
        $result = static::join('clients', 'clients.id', '=', 'clientusers.client_id')
                ->where('clientusers.id', $userId)
                ->where('clients.subdomain', $request->getHost())
                ->get();

        if($result->isEmpty()){
            return 'false';
        }
        return 'true';
    }

    public function verified(){
        $this->verified = 1;
        $this->email_token = null;
        $this->save();
    }

    protected static function getClientByClientUserEmail(Request $request, $email){
        return static::join('clients', 'clients.id', '=', 'clientusers.client_id')
                ->where('clientusers.email', $email)
                ->where('clients.subdomain', $request->getHost())
                ->select('clients.*')
                ->first();
    }

    protected static function searchUsers($request){
        $results = [];
        $courseId = InputSanitise::inputInt($request->get('course_id'));
        $resultArr = InputSanitise::getClientIdAndCretedBy();
        $clientId = $resultArr[0];
        $result = static::where('client_id', $clientId)->where('user_type', self::Student);

        if(!empty($request->get('student'))){
            $result->where('name', 'LIKE', '%'.$request->get('student').'%');
        }

        $results['users'] = $result->select('clientusers.*')->get();
        $results['courses'] = ClientOnlineCourse::getCourseAssocaitedWithVideos();
        $results['userPurchasedCourses'] = ClientUserPurchasedCourse::getClientUserCourses($clientId);
        $results['testSubCategories'] = ClientOnlineTestSubCategory::showSubCategoriesAssociatedWithQuestion($request);
        $results['userPurchasedTestSubCategories'] = ClientUserPurchasedTestSubCategory::getClientUserTestSubCategories($clientId);

        $payableSubCategories = PayableClientSubCategory::getPayableSubCategoryByClientId($clientId);
        if(is_object($payableSubCategories) && false == $payableSubCategories->isEmpty()){
            foreach($payableSubCategories as $payableSubCategory){
                $results['purchasedPayableSubCategories'][$payableSubCategory->sub_category_id] = $payableSubCategory;
            }
        } else {
            $results['purchasedPayableSubCategories'] = [];
        }
        if(count(array_keys($results['purchasedPayableSubCategories'])) > 0){
            $results['clientPurchasedSubCategories'] = ClientOnlineTestSubCategory::showPayableSubcategoriesByIdesAssociatedWithQuestion(array_keys($results['purchasedPayableSubCategories']));
        } else {
            $results['clientPurchasedSubCategories'] = [];
        }
        return $results;
    }

    protected static function deleteStudent(Request $request){
        $clientId = InputSanitise::inputInt($request->client_id);
        $userId = InputSanitise::inputInt($request->client_user_id);

        $student = static::where('id',$userId)->where('client_id',$clientId)->where('user_type', self::Student)->first();
        if(is_object($student)){
            $student->deleteOtherInfoByUserId($userId,$clientId);
            $student->deleteUserStorageFolder();
            $student->delete();
            return 'true';
        }
        return 'false';
    }

    protected function deleteOtherInfoByUserId($userId,$clientId){
        RegisterClientOnlineCourses::deleteRegisteredOnlineCoursesByUserId($userId,$clientId);
        RegisterClientOnlinePaper::deleteRegisteredPapersByUserId($userId,$clientId);
        ClientScore::deleteClientUserScores($userId);
        ClientUserSolution::deleteClientUserSolutions($userId);
        ClientCourseComment::deleteClientCourseCommentsByClientIdByUserId($clientId, $userId);
        ClientCourseCommentLike::deleteClientCourseCommentLikesByClientIdByUserId($clientId, $userId);
        ClientCourseSubComment::deleteClientCourseSubCommentsByClientIdByUserId($clientId,$userId);
        ClientCourseSubCommentLike::deleteClientCourseSubCommentLikesByClientIdByUserId($clientId,$userId);
        ClientOnlineVideoLike::deleteClientOnlineVideoLikesByClientIdByUserId($clientId,$userId);
        ClientAssignmentAnswer::deleteClientAssignmentAnswerByClientIdByUserId($clientId,$userId);
        ClientReadNotification::deleteClientReadNotificationByClientIdByUserId($clientId,$userId);
        ClientChatMessage::deleteClientChatMessagesByClientIdByUserId($clientId,$userId);
        return;
    }

    public function deleteUserStorageFolder(){
        $client = Client::find($this->client_id);
        $userStoragePath = "clientUserStorage/".str_replace(' ', '_', $client->name)."/".$this->id;
        if(is_dir($userStoragePath)){
            InputSanitise::delFolder($userStoragePath);
        }
    }

    protected static function changeClientUserApproveStatus(Request $request){
        $clientId = InputSanitise::inputInt($request->client_id);
        $userId = InputSanitise::inputInt($request->client_user_id);

        $student = static::where('id',$userId)->where('client_id',$clientId)->where('user_type', self::Student)->first();
        if(is_object($student)){
            if( 1 == $student->client_approve){
                $student->client_approve = 0;
            } else {
                $student->client_approve = 1;
            }
            $student->save();
            return 'true';
        }
        return 'false';
    }

    protected static function getAllStudentsByClientId($clientId){
        return static::where('client_id', $clientId)->select('clientusers.*')->where('user_type', self::Student)->get();
    }

    protected static function updateUser(Request $request){
        $user = Auth::guard('clientuser')->user();
        $user->name = $request->name;

        $client = Client::find($user->client_id);
        $userStoragePath = "clientUserStorage/".str_replace(' ', '_', $client->name)."/".$user->id;
        if(!is_dir($userStoragePath)){
            mkdir($userStoragePath, 0755, true);
        }
        if($request->exists('photo')){
            $userImage = $request->file('photo')->getClientOriginalName();
            if(!empty($user->photo) && file_exists($user->photo)){
                unlink($user->photo);
            }
            $request->file('photo')->move($userStoragePath, $userImage);
            $dbUserImagePath = $userStoragePath."/".$userImage;
            // open image
            $img = Image::make($dbUserImagePath);
            // enable interlacing
            $img->interlace(true);
            // save image interlaced
            $img->save();
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
                ->select('id','resume','recorded_video')->where('user_type', self::Student)->first();
    }

    protected static function getStudentsByIds($studentIds){
        $resultArr = InputSanitise::getClientIdAndCretedBy();
        $clientId = $resultArr[0];
        return static::whereIn('id', $studentIds)->where('client_id', $clientId)->where('user_type', self::Student)->get();
    }

    protected static function deleteAllClientUsersInfoByClientId($clientId){
        $users = static::where('client_id', $clientId)->get();
        if(is_object($users) && false == $users->isEmpty()){
            foreach($users as $user){
                ClientScore::deleteClientUserScores($user->id);
                ClientUserSolution::deleteClientUserSolutions($user->id);
                $user->delete();
            }
        }
        RegisterClientOnlineCourses::deleteRegisteredOnlineCoursesClientId($clientId);
        RegisterClientOnlinePaper::deleteRegisteredPapersClientId($clientId);
        ClientUserPurchasedCourse::deleteClientUserCourses($clientId);
        ClientUserPurchasedTestSubCategory::deleteClientUserTestSubCategories($clientId);
    }

    function adminNotificationCount($year=NULL,$month=NULL){
        $ids = [];
        $notificationCount = [];
        $clientUser = Auth::guard('clientuser')->user();
        $clientUserId = $clientUser->id;
        $clientId = $clientUser->client_id;

        $ids = ClientReadNotification::getReadNotificationIdsByUser($year,$month);
        $resultQuery = ClientNotification::where('client_id', $clientId)
                    ->where('is_seen', 0)->whereNotIn('id', $ids)->where('created_by',0)->where('created_to',0);
        if($year > 0){
            $resultQuery->whereYear('created_at', $year);
        }
        if($month > 0){
            $resultQuery->whereMonth('created_at', $month);
        }
        $results = $resultQuery->get();

        if(is_object($results) && false == $results->isEmpty()){
            foreach($results as $result){
                if(1 == $result->notification_module || 2 == $result->notification_module ){
                    $notificationCount[] = $result->id;
                }
            }
        }
        return count($notificationCount);

    }

    function userNotificationCount(){
        $clientUser = Auth::guard('clientuser')->user();
        $clientUserId = $clientUser->id;
        $clientId = $clientUser->client_id;
        return ClientNotification::where('client_id', $clientId)->where('created_to', $clientUserId)->where('is_seen', 0)->count();
    }

    protected static function searchStudentForAssignment($batchId=NULL){
        $resultArr = InputSanitise::getClientIdAndCretedBy();
        $clientId = $resultArr[0];
        if($batchId > 0){
            $userIds = [];
            $clientBatch = ClientBatch::getBatchById($batchId);
            if(is_object($clientBatch)){
                $userIds = explode(',', $clientBatch->student_ids);
            }
            if(count($userIds) > 0){
                return static::where('client_id', $clientId)->whereIn('id', $userIds)->where('user_type', self::Student)->select('clientusers.*')->get();
            } else {
                return static::where('client_id', $clientId)->where('user_type', self::Student)->select('clientusers.*')->get();
            }
        } else {
            return static::where('client_id', $clientId)->where('user_type', self::Student)->select('clientusers.*')->get();
        }
    }

    protected static function isInBetweenFirstTen(){
        $result = 'false';
        $loginUser = Auth::guard('clientuser')->user();
        $users = static::where('client_id', $loginUser->client_id)->take(10)->get();
        if(is_object($users) && false == $users->isEmpty()){
            foreach($users as $user){
                if($loginUser->id == $user->id){
                    $result = 'true';
                }
            }
        }
        return $result;
    }

    public function client(){
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function chatroomid(){
        $senderUserId = $this->client_id;
        $receiverId = $this->id;
        $roomMembers = [$senderUserId,$receiverId];
        return 'chatmessages_'.$roomMembers[0].'_'.$roomMembers[1];
    }

    protected static function searchContact($subDomainName,Request $request){
        $chatusers = [];
        $unreadCount = [];
        $loginUser = Auth::guard('client')->user();
        $currentUserId = $loginUser->id;
        $client = $loginUser->client;
        $contact = InputSanitise::inputString($request->contact);
        if( 1 == $client->allow_non_verified_email){
            $users = static::where('name', 'LIKE', '%'.$contact.'%')->where('client_id', $currentUserId)->where('client_approve',1)->get();
        } else {
            $users = static::where('name', 'LIKE', '%'.$contact.'%')->where('client_id', $currentUserId)->where('verified',1)->where('client_approve',1)->get();
        }
        if(is_object($users) && false == $users->isEmpty()){
            foreach($users as $user){
                if(is_file($user->photo) && true == preg_match('/clientUserStorage/',$user->photo)){
                    $isImageExist = 'system';
                } else if(!empty($user->photo) && false == preg_match('/clientUserStorage/',$user->photo)){
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
                ];
            }
        }
        if(count($chatusers) > 0){
            $searchIds = array_column($chatusers, 'id');
            $chatMessages = ClientChatMessage::where('receiver_id',  $currentUserId)->whereIn('sender_id', $searchIds)->where('client_id', $currentUserId)->where('is_read', 0)->select('sender_id' , \DB::raw('count(*) as unread'))->groupBy('sender_id')->get();
            if(is_object($chatMessages) && false == $chatMessages->isEmpty()){
                foreach($chatMessages as $chatMessage){
                    $unreadCount[$chatMessage->sender_id] = $chatMessage->unread;
                }
            }
        }
        $result['users'] =  $chatusers;
        $result['unreadCount'] =  $unreadCount;
        $result['onlineUsers'] = ClientChatMessage::checkOnlineUsers($subDomainName);
        return $result;
    }

    /**
     * search client student/user
     */
    protected static function searchClientStudent(Request $request){
        $student = InputSanitise::inputString($request->get('student'));
        $clientId = Auth::guard('client')->user()->id;
        return static::where('name', 'like', '%'.$student.'%')->where('client_id', $clientId)->where('user_type', self::Student)->get();
    }

    protected static function addEmail(Request $request){
        $user = Auth::guard('clientuser')->user();
        $user->email = $request->get('email');
        $user->password = bcrypt($request->get('password'));
        $user->save();
        return $user;
    }

    protected static function updateMobile(Request $request){
        $userMobile = $request->get('phone');
        $user = Auth::guard('clientuser')->user();
        $user->phone = $userMobile;
        $user->number_verified = 1;
        $user->save();

        // un approve number if have same number to other users with same client
        $otherUsers = static::where('phone', $user->phone)->where('client_id', $user->client_id)->where('id','!=', $user->id)->get();
        if(is_object($otherUsers) && false == $otherUsers->isEmpty()){
            foreach($otherUsers as $otherUser){
                $otherUser->number_verified = 0;
                $otherUser->save();
            }
        }
        return;
    }
    protected static function verifyMobile(Request $request){
        $userMobile = $request->get('phone');
        $user = Auth::guard('clientuser')->user();
        $user->number_verified = 1;
        $user->save();

        // un approve number if have same number to other users with same client
        $otherUsers = static::where('phone', $user->phone)->where('client_id', $user->client_id)->where('id','!=', $user->id)->get();
        if(is_object($otherUsers) && false == $otherUsers->isEmpty()){
            foreach($otherUsers as $otherUser){
                $otherUser->number_verified = 0;
                $otherUser->save();
            }
        }
        return;
    }

    protected static function addMobileUser(Request $request,$clientId,$userType){
        $numberVerified = 1;
        $email = '';
        $phone = InputSanitise::inputString($request->get('phone'));
        $emailToken = '';
        $password = '';
        $name = InputSanitise::inputString($request->get('name'));

        $clientUser = static::create([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'client_id' => $clientId,
            'client_approve' => 1,
            'password' => bcrypt($password),
            'email_token' => $emailToken,
            'number_verified' => $numberVerified,
            'user_type' => $userType,
        ]);
        // un approve number if have same number to other users with same client
        $otherUsers = static::where('phone', $clientUser->phone)->where('client_id', $clientId)->where('id','!=', $clientUser->id)->get();
        if(is_object($otherUsers) && false == $otherUsers->isEmpty()){
            foreach($otherUsers as $otherUser){
                $otherUser->number_verified = 0;
                $otherUser->save();
            }
        }
        return $clientUser;
    }

    protected static function addEmailUser(Request $request,$clientId,$userType){
        $insertArr = [];
        $allInputs = $request->except('_token');
        $result = [];
        if(count($allInputs) > 0){
            foreach($allInputs as $index => $value){
                if(!empty($value)){
                    $indexArr = explode('_', $index);
                    $insertArr[$indexArr[1]][$indexArr[0]] = $value;
                }
            }
        }
        if(count($insertArr) > 0){
            foreach($insertArr as $insertData){
                $existingUser = static::where('email',$insertData['email'])->where('client_id',$clientId)->first();
                if(!is_object($existingUser)){
                    $user = new static;
                    $user->name = $insertData['name'];
                    $user->email = $insertData['email'];
                    $user->phone = (!empty($insertData['phone']))?$insertData['phone']:'';
                    $user->password = bcrypt($insertData['password']);
                    $user->client_id = $clientId;
                    $user->verified = 0;
                    $user->client_approve = 1;
                    if(!empty($insertData['email']) && filter_var($insertData['email'], FILTER_VALIDATE_EMAIL)){
                        $user->email_token = str_random(60);
                    } else {
                        $user->email_token = '';
                    }
                    $user->user_type = $userType;
                    $user->save();
                    if(!empty($user->email) && filter_var($user->email, FILTER_VALIDATE_EMAIL)){
                        $clientUserEmail = new ClientUserEmailVerification(new Clientuser(['email_token' => $user->email_token, 'name' => $user->name]));
                        Mail::to($user->email)->send($clientUserEmail);
                    }
                } else {
                    $result['duplicate_email'][] = $insertData['email'];
                }
            }
            $result['status'] = 'true';
        } else {
            $result['status'] = 'false';
        }
        return $result;
    }

    protected static function getTeachersByClientId($clientId){
        return static::where('client_id', $clientId)->where('user_type', self::Teacher)->get();
    }

    protected static function changeClientTeacherModuleStatus(Request $request){
        $clientId = InputSanitise::inputInt($request->client_id);
        $userId = InputSanitise::inputInt($request->client_user_id);
        $moduleId = InputSanitise::inputInt($request->module_id);
        $moduleStatus = $request->module_status;

        $teacher = static::where('id',$userId)->where('client_id',$clientId)->where('user_type', self::Teacher)->first();
        if(is_object($teacher)){
            if(!empty($teacher->assigned_modules)){
                $assignedModules = explode(',', $teacher->assigned_modules);
                if('true' == $moduleStatus){
                    array_push($assignedModules, $moduleId);
                    sort($assignedModules);
                    $teacher->assigned_modules = implode(',', $assignedModules);
                } else {
                    $assignedModules = array_diff($assignedModules, [$moduleId]);
                    sort($assignedModules);
                    $teacher->assigned_modules = implode(',', $assignedModules);
                }
            } else {
                $teacher->assigned_modules = $moduleId;
            }
            $teacher->save();
            return 'true';
        }
        return 'false';
    }
}