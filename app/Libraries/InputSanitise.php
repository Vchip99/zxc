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
       	$files = array_diff(scandir($dir), array('.','..'));
        foreach ($files as $file) {
          (is_dir("$dir/$file")) ? static::delFolder("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }

    public static function checkModulePermission(Request $request, $module){
    	$client = Client::where('subdomain', $request->getHost())->first();
        if(is_object($client)){
            if('course' == $module && 0 == $client->course_permission){
                return 'false';
            }
            if('test' == $module && 0 == $client->test_permission){
                return 'false';
            }
            return 'true';
        }
        return 'false';
    }

}