<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Libraries\InputSanitise;
use DB;

class UserBasedAuthentication extends Model
{
	protected $connection = 'mysql2';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['vchip_client_id', 'instamojo_client_id', 'access_token', 'refresh_token', 'token_type'];

    protected static function deleteUserBasedAuthenticationsByClientId($clientId){
        $auths = static::where('vchip_client_id', $clientId)->get();
        if(is_object($auths) && false == $auths->isEmpty()){
            foreach($auths as $auth){
                $auth->delete();
            }
        }
        return;
    }
}
