<?php

namespace App\Libraries;
use Illuminate\Http\Request;
use App\Models\ClientHomePage;
use App\Models\Client;
use DB;

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

    public static function delFolder($dir) {
        if(is_dir($dir)){
            $files = array_diff(scandir($dir), array('.','..'));
            if(count($files) > 0){
                foreach ($files as $file) {
                  (is_dir("$dir/$file")) ? static::delFolder("$dir/$file") : unlink("$dir/$file");
                }
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

}