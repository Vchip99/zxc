<?php

namespace App\Libraries;
use Illuminate\Http\Request;
use App\Models\ClientHomePage;
use App\Models\Client;
use DB, Cache, File,LRedis,Auth;

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
                return 'http://'.$user->client->subdomain;
            } else {
                return 'https://'.$user->client->subdomain;;
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
        $smsUrl = 'http://5.189.153.48:8080/vendorsms/pushsms.aspx?user=vchip99&password=vchip&msisdn='.$mobileNo.'&sid=VCHIPP&msg='.$message.'&fl=0&gwid=2';

        // Send the GET request with cURL
        $ch = curl_init($smsUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    public static function sendOfflineDueSms($mobile, $userName, $batchName, $clientName){
        $mobileNo = '91'.$mobile;
        $otp = rand(100000, 999999);
        $userMessage = 'Dear '.$userName.', Today is your due date for payment of batch- '.$batchName.'. Thanks '.$clientName;
        $message = rawurlencode($userMessage);

        // $smsUrl = 'http://api.bizztel.com/composeapi/?userid=info@vchiptech.com&pwd=vchipsms&route=1&senderid=VCHIPP&destination='.$mobileNo.'&message='.$message;
        $smsUrl = 'http://5.189.153.48:8080/vendorsms/pushsms.aspx?user=vchip99&password=vchip&msisdn='.$mobileNo.'&sid=VCHIPP&msg='.$message.'&fl=0&gwid=2';

        // Send the GET request with cURL
        $ch = curl_init($smsUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

}