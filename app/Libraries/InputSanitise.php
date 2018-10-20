<?php

namespace App\Libraries;
use Illuminate\Http\Request;
use App\Models\ClientHomePage;
use App\Models\Client;
use App\Models\Clientuser;
use DB, Cache, File,LRedis,Auth,Session;

class InputSanitise{

	public static function stripTrim($str){
		return strip_tags(trim($str));
	}

	public static function inputString($str){
		return static::stripTrim(filter_var( $str, FILTER_SANITIZE_STRING));
	}

	public static function inputInt($str){
		return static::stripTrim(filter_var( $str, FILTER_SANITIZE_NUMBER_INT));
	}

	public static function getCurrentClient(Request $request){
		return $request->getHost();
	}

	public static function checkDomain(Request $request){
    	$subdomain = ClientHomePage::where('subdomain', $request->getHost())->first();
        if(!is_object($subdomain)){
        	return false;
        }
        return $subdomain;
    }

    public static function checkCollegeUrl(Request $request){
        $requestCollegeUrl = explode('/', $request->path())[1];
        $sessionCollegeUrl = Session::get('college_user_url');
        if($requestCollegeUrl == $sessionCollegeUrl){
            return true;
        }
        return false;
    }

    public static function getCurrentGuard(){
        if(Auth::guard('admin')->check()){
            return "admin";
        }elseif(Auth::guard('web')->check()){
            return "user";
        }elseif(Auth::guard('client')->check()){
            return "client";
        }elseif(Auth::guard('clientuser')->check()){
            return "clientuser";
        }
        return false;
    }

    public static function getClientIdAndCretedBy(){
        if('client' == static::getCurrentGuard()){
            $clientId = Auth::guard('client')->user()->id;
            $createdBy = 0;
        } else {
            $clientUser = Auth::guard('clientuser')->user();
            $clientId = $clientUser->client_id;
            $createdBy = $clientUser->id;
        }
        return [$clientId,$createdBy];
    }

    public static function getLoginUserByGuardForClient(){
        if('client' == static::getCurrentGuard()){
            return Auth::guard('client')->user();
        } elseif('clientuser' == static::getCurrentGuard()){
            return Auth::guard('clientuser')->user();
        }
    }

    public static function delFolder($dir) {
        if(is_dir($dir)){
            $files = array_diff(scandir($dir), array('.','..'));
            if(count($files) > 0){
                foreach ($files as $file) {
                  (is_dir("$dir/$file")) ? static::delFolder("$dir/$file") : unlink("$dir/$file");
                }
                return rmdir($dir);
            }
            if(is_dir($dir)){
                return rmdir($dir);
            }
        }
        return;
    }

    public static function checkUserClient(Request $request, $user){
        $subdomainObj = self::checkDomain($request);
        if(is_object($subdomainObj) && $subdomainObj->client_id != $user->client_id){
            if('local' == \Config::get('app.env')){
                return 'http://'.$subdomainObj->subdomain;
            } else {
                return 'https://'.$subdomainObj->subdomain;
            }
        }
        return $subdomainObj;
    }

    public static function checkClientImagesDirForCkeditor($subdomain){
        // create client/subdomain dir in kcfinder upload dir
        $path = public_path().'/templateEditor/kcfinder/upload/images/'. $subdomain;
        if(!is_dir($path)){
            File::makeDirectory($path, $mode = 0777, true, true);
        }
        return;
    }

    public static function deleteCacheByString($searchString){
        $keys = LRedis::keys($searchString.'*');
        if(count($keys) > 0){
            foreach($keys as $key){
                LRedis::del($key);
            }
        }
        return ;
    }

    public static function sendOtp($mobile){
        $mobileNo = '91'.$mobile;
        $otp = rand(100000, 999999);
        $userMessage = 'Your OTP: '.$otp;
        Cache::put($mobile, $otp, 10);
        Cache::put('mobile-'.$mobile, $mobile, 10);
        $message = rawurlencode($userMessage);

        // $smsUrl = 'http://api.bizztel.com/composeapi/?userid=info@vchiptech.com&pwd=vchipsms&route=1&senderid=VCHIPP&destination='.$mobileNo.'&message='.$message;
        $smsUrl = 'http://5.189.153.48:8080/vendorsms/pushsms.aspx?user=vchip99&password=vchip&msisdn='.$mobileNo.'&sid=VCPEDU&msg='.$message.'&fl=0&gwid=2';

        // Send the GET request with cURL
        $ch = curl_init($smsUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    public static function sendOfflineDueSms($mobile, $userName, $batchName, $clientName){
        $userMessage = 'Dear '.$userName.', Today is your due date for payment of batch- '.$batchName.'. Thanks '.$clientName;
        return self::sendSms($mobile,$userMessage);
    }

    public static function checkMobileAndSendOpt(Request $request,$mobile){
        $result = [];
        if(!empty($mobile)){
            $client = Client::where('subdomain', $request->getHost())->first();
            if(is_object($client)){
                DB::connection('mysql2')->beginTransaction();
                try
                {
                    $loginUser = Auth::guard('clientuser')->user();
                    if(is_object($loginUser)){
                        $parents = Clientuser::where('parent_phone','=', $mobile)->whereNotNull('parent_phone')->where('id','!=',$loginUser->id)->where('client_id', $client->id)->get();
                    } else {
                        $parents = Clientuser::where('parent_phone','=', $mobile)->whereNotNull('parent_phone')->where('client_id', $client->id)->get();
                    }
                    if(is_object($parents) && $parents->count() > 0){
                        $result['status'] = 'error';
                        $result['message'] = 'This number is already in use, for more detail, please contact to admin @ '.$client->phone;
                    } else {
                        if(is_object($loginUser) && $mobile == $loginUser->parent_phone){
                            $result['status'] = 'error';
                            $result['message'] = 'This number is already in use and assign to your parent.so enter another no.';
                        } else {
                            $result['status'] = 'success';
                            $result['message'] = self::sendOtp($mobile);
                            self::setSmsCountStats($client);
                            $client->save();
                            DB::connection('mysql2')->commit();
                        }
                    }
                }
                catch(\Exception $e)
                {
                    DB::connection('mysql2')->rollback();
                }
            }
        } else {
            $result['status'] = 'error';
            $result['message'] = 'Please enter mobile no';
        }
        return $result;
    }

    public static function checkMobileAndSendOptForParent(Request $request,$mobile){
        $result = [];
        if(!empty($mobile)){
            $client = Client::where('subdomain', $request->getHost())->first();
            if(is_object($client)){
                DB::connection('mysql2')->beginTransaction();
                try
                {
                    $loginUser = Auth::guard('clientuser')->user();
                    if(is_object($loginUser)){
                        $parents = Clientuser::where('parent_phone','=', $mobile)->whereNotNull('parent_phone')->where('id','!=',$loginUser->id)->where('client_id', $client->id)->get();
                    } else {
                        $parents = Clientuser::where('parent_phone','=', $mobile)->whereNotNull('parent_phone')->where('client_id', $client->id)->get();
                    }
                    if(is_object($parents) && $parents->count() > 0){
                        $result['status'] = 'error';
                        $result['message'] = 'This number is already in use, for more detail, please contact to admin @ '.$client->phone;
                    } else {
                        if(is_object($loginUser)){
                            $clientUsers = Clientuser::where('phone','=', $mobile)->whereNotNull('phone')->where('id','!=',$loginUser->id)->where('client_id', $client->id)->get();
                        } else {
                            $clientUsers = Clientuser::where('phone','=', $mobile)->whereNotNull('phone')->where('client_id', $client->id)->get();
                        }
                        if(is_object($clientUsers) && $clientUsers->count() > 0){
                            $result['status'] = 'error';
                            $result['message'] = 'This number is already in use, for more detail, please contact to admin @ '.$client->phone;
                        } else {
                            if(is_object($loginUser) && $mobile == $loginUser->parent_phone){
                                $result['status'] = 'error';
                                $result['message'] = 'This number is already in use and assign to your parent.so enter another no.';
                            } else {
                                $result['status'] = 'success';
                                $result['message'] = self::sendOtp($mobile);
                                self::setSmsCountStats($client);
                                $client->save();
                                DB::connection('mysql2')->commit();
                            }
                        }
                    }
                }
                catch(\Exception $e)
                {
                    DB::connection('mysql2')->rollback();
                }
            }
        } else {
            $result['status'] = 'error';
            $result['message'] = 'Please enter mobile no';
        }
        return $result;
    }

    public static function sendAbsentSms($absentStudents,$sendSmsStatus,$batchName,$attendanceDate,$client){
        $clientName = $client->name;
        $clientId = $client->id;
        $students = Clientuser::getClientApproveStudentsByClientIdByIdsForSms($clientId,$absentStudents);
        if(is_object($students) && false == $students->isEmpty()){
            $sendSmsNumbers = self::getSendSmsNumber($students,$sendSmsStatus);
            if(count($sendSmsNumbers) >  $client->debit_sms_count){
                return self::sendClientCreditSms($client->phone,$clientName);
            } else {
                foreach($students as $student){
                    if(Client::Student == $sendSmsStatus || Client::Both == $sendSmsStatus){
                        $mobile = $student->phone;
                        $message = 'Dear '.$student->name.', You are absent on date '.$attendanceDate.' for batch- '.$batchName.'. Thanks '.$clientName;
                        if(!empty($mobile) && 10 == strlen($mobile)){
                            self::sendSms($mobile,$message);
                            self::setSmsCountStats($client);
                        }
                    }
                    if(Client::Parents == $sendSmsStatus || Client::Both == $sendSmsStatus){
                        $mobile = $student->parent_phone;
                        $message = 'Dear Parent, Your child '.$student->name.', is absent on date '.$attendanceDate.' for batch- '.$batchName.'. Thanks '.$clientName;
                        if(!empty($mobile) && 10 == strlen($mobile)){
                            self::sendSms($mobile,$message);
                            self::setSmsCountStats($client);
                        }
                    }
                }
                $client->save();
            }
        }
        return;
    }

    public static function sendSms($mobile,$message){
        $mobileNo = '91'.$mobile;
        $message = rawurlencode($message);
        $smsUrl = 'http://5.189.153.48:8080/vendorsms/pushsms.aspx?user=vchip99&password=vchip&msisdn='.$mobileNo.'&sid=VCPEDU&msg='.$message.'&fl=0&gwid=2';

        // Send the GET request with cURL
        $ch = curl_init($smsUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    public static function sendExamSms($allBatchStudents,$sendSmsStatus,$batchId,$batchName,$examName,$examDate,$fromTime,$toTime,$client,$isUpdate){
        $clientName = $client->name;
        $clientId = $client->id;
        if($batchId > 0 && !empty($allBatchStudents)){
            $students = Clientuser::getClientApproveStudentsByClientIdByIdsForSms($clientId,$allBatchStudents);
        } else {
            $students = Clientuser::getClientApproveStudentsByClientIdForSms($clientId);
        }
        if(is_object($students) && false == $students->isEmpty()){
            $sendSmsNumbers = self::getSendSmsNumber($students,$sendSmsStatus);
            if(count($sendSmsNumbers) >  $client->debit_sms_count){
                return self::sendClientCreditSms($client->phone,$clientName);
            } else {
                foreach($students as $student){
                    if(Client::Student == $sendSmsStatus || Client::Both == $sendSmsStatus){
                        $mobile = $student->phone;
                        if($batchId > 0) {
                            if( true == $isUpdate){
                                $message = 'Dear '.$student->name.', The Exam-' .$examName.' is updated on '.$examDate.' from '.$fromTime.' to '.$toTime.' for batch- '.$batchName.'. Thanks '.$clientName;
                            } else {
                                $message = 'Dear '.$student->name.', The Exam- '.$examName.' is on '.$examDate.' from '.$fromTime.' to '.$toTime.' for batch- '.$batchName.'. Thanks '.$clientName;
                            }
                        } else {
                            if( true == $isUpdate){
                                $message = 'Dear '.$student->name.', The Exam- '.$examName.' is updated on '.$examDate.' from '.$fromTime.' to '.$toTime.'. Thanks '.$clientName;
                            } else {
                                $message = 'Dear '.$student->name.', The Exam- '.$examName.' is on '.$examDate.' from '.$fromTime.' to '.$toTime.'. Thanks '.$clientName;
                            }
                        }
                        if(!empty($mobile) && 10 == strlen($mobile)){
                            self::sendSms($mobile,$message);
                            self::setSmsCountStats($client);
                        }
                    }
                    if(Client::Parents == $sendSmsStatus || Client::Both == $sendSmsStatus){
                        $mobile = $student->parent_phone;
                        if($batchId > 0) {
                            if( true == $isUpdate){
                                $message = 'Dear Parent, Your child '.$student->name.', have exam- '.$examName.' and its updated schedule is on '.$examDate.' from '.$fromTime.' to '.$toTime.' for batch- '.$batchName.'. Thanks '.$clientName;
                            } else {
                                $message = 'Dear Parent, Your child '.$student->name.', have exam- '.$examName.' on '.$examDate.' from '.$fromTime.' to '.$toTime.' for batch- '.$batchName.'. Thanks '.$clientName;
                            }
                        } else {
                            if( true == $isUpdate){
                                $message = 'Dear Parent, Your child '.$student->name.', have exam- '.$examName.' and its updated schedule is on '.$examDate.' from '.$fromTime.' to '.$toTime.' Thanks '.$clientName;
                            } else {
                                $message = 'Dear Parent, Your child '.$student->name.', have exam- '.$examName.' on '.$examDate.' from '.$fromTime.' to '.$toTime.' Thanks '.$clientName;
                            }
                        }
                        if(!empty($mobile) && 10 == strlen($mobile)){
                            self::sendSms($mobile,$message);
                            self::setSmsCountStats($client);
                        }
                    }
                }
                $client->save();
            }
        }
        return;
    }

    public static function sendClientCreditSms($mobile,$clientName){
        $message = 'Dear '.$clientName.', You dont have sufficient sms credit to send sms. please topup.';
        return self::sendSms($mobile,$message);
    }

    public static function getSendSmsNumber($students,$sendSmsStatus){
        $sendSmsNumbers = [];
        foreach($students as $student){
            if((Client::Student == $sendSmsStatus || Client::Both == $sendSmsStatus) && !empty($student->phone) && 10 == strlen($student->phone)){
                $sendSmsNumbers[] = $student->phone;
            }
            if((Client::Parents == $sendSmsStatus || Client::Both == $sendSmsStatus) && !empty($student->parent_phone) && 10 == strlen($student->parent_phone)){
                $sendSmsNumbers[] = $student->parent_phone;
            }
        }
        return $sendSmsNumbers;
    }

    public static function setSmsCountStats($client){
        $client->academic_sms_count += 1;
        if($client->debit_sms_count > 0){
            $client->debit_sms_count -= 1;
        } else {
            $client->credit_sms_count += 1;
        }
        return;
    }

    public static function sendOfflinePaperMarkSms($presentStudentsMark,$sendSmsStatus,$batchId,$batchName,$paperName,$totalMarks,$client){
        $clientName = $client->name;
        $clientId = $client->id;
        $studentIds = array_keys($presentStudentsMark);
        $students = Clientuser::getClientApproveStudentsByClientIdByIdsForSms($clientId,$studentIds);
        if(is_object($students) && false == $students->isEmpty()){
            $sendSmsNumbers = self::getSendSmsNumber($students,$sendSmsStatus);
            if(count($sendSmsNumbers) >  $client->debit_sms_count){
                return self::sendClientCreditSms($client->phone,$clientName);
            } else {
                foreach($students as $student){
                    if(Client::Student == $sendSmsStatus || Client::Both == $sendSmsStatus){
                        $mobile = $student->phone;
                        if($batchId > 0) {
                            $message = 'Dear '.$student->name.', your offline exam\'s mark for paper -'.$paperName.' is '.$presentStudentsMark[$student->id].'/'.$totalMarks.' for batch- '.$batchName.'. Thanks '.$clientName;
                            if(!empty($mobile) && 10 == strlen($mobile)){
                                self::sendSms($mobile,$message);
                                self::setSmsCountStats($client);
                            }
                        }
                    }
                    if(Client::Parents == $sendSmsStatus || Client::Both == $sendSmsStatus){
                        $mobile = $student->parent_phone;
                        if($batchId > 0) {
                            $message = 'Dear Parent, Your child '.$student->name.', had offline exam and its mark for paper -'.$paperName.' is '.$presentStudentsMark[$student->id].'/'.$totalMarks.' for batch- '.$batchName.'. Thanks '.$clientName;
                            if(!empty($mobile) && 10 == strlen($mobile)){
                                self::sendSms($mobile,$message);
                                self::setSmsCountStats($client);
                            }
                        }
                    }
                }
                $client->save();
            }
        }
        return;
    }

    public static function sendNoticeSms($allBatchStudents,$sendSmsStatus,$batchId,$batchName,$notice,$isEmergency,$client){
        $clientName = $client->name;
        $clientId = $client->id;
        if($batchId > 0 && !empty($allBatchStudents)){
            $students = Clientuser::getClientApproveStudentsByClientIdByIdsForSms($clientId,$allBatchStudents);
        } else {
            $students = Clientuser::getClientApproveStudentsByClientIdForSms($clientId);
        }
        if(is_object($students) && false == $students->isEmpty()){
            $sendSmsNumbers = self::getSendSmsNumber($students,$sendSmsStatus);
            if(count($sendSmsNumbers) >  $client->debit_sms_count){
                return self::sendClientCreditSms($client->phone,$clientName);
            } else {
                foreach($students as $student){
                    if(Client::Student == $sendSmsStatus || Client::Both == $sendSmsStatus){
                        $mobile = $student->phone;
                        if($batchId > 0) {
                            if( true == $isEmergency){
                                $message = 'Emergency Notice@'.$batchName.'-'.$notice.'. Thanks '.$clientName;
                            } else {
                                $message = 'Notice@'.$batchName.'-'.$notice.'. Thanks '.$clientName;
                            }
                        } else {
                            if( true == $isEmergency){
                                $message = 'Emergency Notice-'.$notice.'. Thanks '.$clientName;
                            } else {
                                $message = 'Notice-'.$notice.'. Thanks '.$clientName;
                            }
                        }
                        if(!empty($mobile) && 10 == strlen($mobile)){
                            $message = substr($message,0,150);
                            self::sendSms($mobile,$message);
                            self::setSmsCountStats($client);
                        }
                    }
                    if(Client::Parents == $sendSmsStatus || Client::Both == $sendSmsStatus){
                        $mobile = $student->parent_phone;
                        if($batchId > 0) {
                            if( true == $isEmergency){
                                $message = 'Emergency Notice For your child:'.$student->name.'@'.$batchName.'-'.$notice.'. Thanks '.$clientName;
                            } else {
                                $message = 'Notice For your child:'.$student->name.'@'.$batchName.'-'.$notice.'. Thanks '.$clientName;
                            }
                        } else {
                            if( true == $isEmergency){
                                $message = 'Emergency Notice For your child:'.$student->name.'-'.$notice.'. Thanks '.$clientName;
                            } else {
                                $message = 'Notice For your child:'.$student->name.'-'.$notice.'. Thanks '.$clientName;
                            }
                        }
                        if(!empty($mobile) && 10 == strlen($mobile)){
                            $message = substr($message,0,150);
                            self::sendSms($mobile,$message);
                            self::setSmsCountStats($client);
                        }
                    }
                }
                $client->save();
            }
        }
        return;
    }

    public static function sendHolidaySms($allBatchStudents,$sendSmsStatus,$batchId,$batchName,$note,$client){
        $clientName = $client->name;
        $clientId = $client->id;
        if($batchId > 0 && !empty($allBatchStudents)){
            $students = Clientuser::getClientApproveStudentsByClientIdByIdsForSms($clientId,$allBatchStudents);
        } else {
            $students = Clientuser::getClientApproveStudentsByClientIdForSms($clientId);
        }
        if(is_object($students) && false == $students->isEmpty()){
            $sendSmsNumbers = self::getSendSmsNumber($students,$sendSmsStatus);
            if(count($sendSmsNumbers) >  $client->debit_sms_count){
                return self::sendClientCreditSms($client->phone,$clientName);
            } else {
                foreach($students as $student){
                    if(Client::Student == $sendSmsStatus || Client::Both == $sendSmsStatus){
                        $mobile = $student->phone;
                        if($batchId > 0) {
                            $message = 'Holiday@'.$batchName.'-'.$note.'. Thanks '.$clientName;
                        } else {
                            $message = 'Holiday-'.$note.'. Thanks '.$clientName;
                        }
                        if(!empty($mobile) && 10 == strlen($mobile)){
                            $message = substr($message,0,150);
                            self::sendSms($mobile,$message);
                            self::setSmsCountStats($client);
                        }
                    }
                    if(Client::Parents == $sendSmsStatus || Client::Both == $sendSmsStatus){
                        $mobile = $student->parent_phone;
                        if($batchId > 0){
                            $message = 'Holiday For your child:'.$student->name.'@'.$batchName.'-'.$note.'. Thanks '.$clientName;
                        } else {
                            $message = 'Holiday For your child:'.$student->name.'-'.$note.'. Thanks '.$clientName;
                        }
                        if(!empty($mobile) && 10 == strlen($mobile)){
                            $message = substr($message,0,150);
                            self::sendSms($mobile,$message);
                            self::setSmsCountStats($client);
                        }
                    }
                }
                $client->save();
            }
        }
        return;
    }

    public static function sendAssignmentSms($allBatchStudents,$sendSmsStatus,$batchId,$batchName,$topicName,$client){
        $clientName = $client->name;
        $clientId = $client->id;
        if($batchId > 0 && !empty($allBatchStudents)){
            $students = Clientuser::getClientApproveStudentsByClientIdByIdsForSms($clientId,$allBatchStudents);
        } else {
            $students = Clientuser::getClientApproveStudentsByClientIdForSms($clientId);
        }
        if(is_object($students) && false == $students->isEmpty()){
            $sendSmsNumbers = self::getSendSmsNumber($students,$sendSmsStatus);
            if(count($sendSmsNumbers) >  $client->debit_sms_count){
                return self::sendClientCreditSms($client->phone,$clientName);
            } else {
                foreach($students as $student){
                    if(Client::Student == $sendSmsStatus || Client::Both == $sendSmsStatus){
                        $mobile = $student->phone;
                        if($batchId > 0) {
                            $message = 'Dear '.$student->name.', New Assignment on topic "'.$topicName.'" has been created for batch- '.$batchName.'. Thanks '.$clientName;
                        } else {
                            $message = 'Dear '.$student->name.', New Assignment on topic "'.$topicName.'" has been created. Thanks '.$clientName;
                        }
                        if(!empty($mobile) && 10 == strlen($mobile)){
                            $message = substr($message,0,150);
                            self::sendSms($mobile,$message);
                            self::setSmsCountStats($client);
                        }
                    }
                    if(Client::Parents == $sendSmsStatus || Client::Both == $sendSmsStatus){
                        $mobile = $student->parent_phone;
                        if($batchId > 0){
                            $message = 'Dear Parent, For your child '.$student->name.', New Assignment on topic "'.$topicName.'" has been created for batch- '.$batchName.'. Thanks '.$clientName;
                        } else {
                            $message = 'Dear Parent, For your child '.$student->name.', New Assignment on topic "'.$topicName.'" has been created. Thanks '.$clientName;
                        }
                        if(!empty($mobile) && 10 == strlen($mobile)){
                            $message = substr($message,0,150);
                            self::sendSms($mobile,$message);
                            self::setSmsCountStats($client);
                        }
                    }
                }
                $client->save();
            }
        }
        return;
    }

    public static function sendLectureSms($lecture,$batchName,$lecturer,$client){
        $batchId = $lecture->client_batch_id;
        $mobile = $lecturer->phone;
        $clientName = $client->name;
        if($client->debit_sms_count < 1){
            return self::sendClientCreditSms($client->phone,$clientName);
        }
        if($batchId > 0) {
            $message = 'Dear '.$lecturer->name.', You have a lecture on topic "' .$lecture->topic.'" on '.$lecture->date.' from '.$lecture->from_time.' to '.$lecture->to_time.' for batch- '.$batchName.'. Thanks '.$clientName;
        } else {
            $message = 'Dear '.$lecturer->name.', You have a lecture on topic "' .$lecture->topic.'" on '.$lecture->date.' from '.$lecture->from_time.' to '.$lecture->to_time.'. Thanks '.$clientName;
        }
        if(!empty($mobile) && 10 == strlen($mobile)){
            self::sendSms($mobile,$message);
            self::setSmsCountStats($client);
            $client->save();
        }
        return;
    }

    public static function sendIndividualSms($studentsData,$sendSmsStatus,$batchName,$client){
        $clientName = $client->name;
        $clientId = $client->id;
        $students = Clientuser::getClientApproveStudentsByClientIdByIdsForSms($clientId,array_keys($studentsData));
        if(is_object($students) && false == $students->isEmpty()){
            $sendSmsNumbers = self::getSendSmsNumber($students,$sendSmsStatus);
            if(count($sendSmsNumbers) >  $client->debit_sms_count){
                return self::sendClientCreditSms($client->phone,$clientName);
            } else {
                foreach($students as $student){
                    if(Client::Student == $sendSmsStatus || Client::Both == $sendSmsStatus){
                        $mobile = $student->phone;
                        $message = 'Dear '.$student->name.'['.$batchName.'], '.$studentsData[$student->id].'. Thanks '.$clientName;
                        if(!empty($mobile) && 10 == strlen($mobile)){
                            $message = substr($message,0,150);
                            self::sendSms($mobile,$message);
                            self::setSmsCountStats($client);
                        }
                    }
                    if(Client::Parents == $sendSmsStatus || Client::Both == $sendSmsStatus){
                        $mobile = $student->parent_phone;
                        $message = 'Dear Parent, For your child '.$student->name.'['.$batchName.'], '.$studentsData[$student->id].'. Thanks '.$clientName;
                        if(!empty($mobile) && 10 == strlen($mobile)){
                            $message = substr($message,0,150);
                            self::sendSms($mobile,$message);
                            self::setSmsCountStats($client);
                        }
                    }
                }
                $client->save();
            }
        }
        return;
    }
}