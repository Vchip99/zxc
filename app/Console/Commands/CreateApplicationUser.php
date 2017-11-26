<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\InstamojoDetail;
use App\Models\UserBasedAuthentication;
use App\Models\Client;
use DB;

class CreateApplicationUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'createapplicationuser:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create application user';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('create client on instamojo:');
        $errorCount = 0;
        DB::connection('mysql2')->beginTransaction();
        try
        {
            // check access token for application base auth
            $instamojoDetail = InstamojoDetail::where('client_id', '4IfB5qdRnGjcq1LqCgkHLdARUvK3oAg1FyGdnqIR')->first();

            if(!is_object($instamojoDetail)){
                exit();
            }
            $applicationAccessToken = $instamojoDetail->application_base_access_token;
            $applicationTokenType = $instamojoDetail->application_base_token_type;

            $clients = Client::all();
            if(is_object($clients) && false == $clients->isEmpty()){
                foreach($clients as $client){
                    $instamojoAuthErrors = '';
                    // sign up client
                    $signupPostFields = [
                                    'email'=> $client->email,
                                    'password'=> $client->email,
                                    'phone'=> $client->phone,
                                    'referrer'=> $instamojoDetail->referrer
                                  ];

                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                      CURLOPT_URL => "https://test.instamojo.com/v2/users/",
                      CURLOPT_RETURNTRANSFER => true,
                      CURLOPT_ENCODING => "",
                      CURLOPT_MAXREDIRS => 10,
                      CURLOPT_TIMEOUT => 60,
                      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                      CURLOPT_CUSTOMREQUEST => "POST",
                      CURLOPT_POSTFIELDS => $signupPostFields,
                      CURLOPT_HTTPHEADER => array(
                        "authorization: Bearer ".$applicationAccessToken."",
                        "cache-control: no-cache",
                        "content-type: multipart/form-data"
                      ),
                    ));

                    $response = curl_exec($curl);
                    $err = curl_error($curl);

                    curl_close($curl);

                    if($err) {
                        $instamojoAuthErrors.= 'signup_error-' .(string)$err;
                        $errorCount++;
                    } else {
                        $result = json_decode($response);
                        if(!empty($result->id)){
                            $userAuth  = new UserBasedAuthentication;
                            $userAuth->vchip_client_id = $client->id;
                            $userAuth->instamojo_client_id = $result->id;
                            $userAuth->save();
                            $this->info('sign up client:'. $client->name);
                        }
                    }

                    // user based auth
                    $userAuthPostFields = [
                                    'grant_type'=>'password',
                                    'client_id' => $instamojoDetail->client_id,
                                    'client_secret' => $instamojoDetail->client_secret,
                                    'username' => $client->email,
                                    'password' => $client->email
                                  ];

                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                      CURLOPT_URL => "https://test.instamojo.com/oauth2/token/",
                      CURLOPT_RETURNTRANSFER => true,
                      CURLOPT_ENCODING => "",
                      CURLOPT_MAXREDIRS => 10,
                      CURLOPT_TIMEOUT => 60,
                      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                      CURLOPT_CUSTOMREQUEST => "POST",
                      CURLOPT_POSTFIELDS => $userAuthPostFields,
                      CURLOPT_HTTPHEADER => array(
                        "cache-control: no-cache",
                        "content-type: multipart/form-data"
                      ),
                    ));

                    $response = curl_exec($curl);
                    $err = curl_error($curl);

                    curl_close($curl);

                    if ($err) {
                        $instamojoAuthErrors.= 'user_auth_error-' .(string)$err;
                        $errorCount++;
                    } else {
                        $result = json_decode($response);
                        if(!empty($result->access_token) && !empty($result->refresh_token) && !empty($result->token_type)){
                            $userAuth  = UserBasedAuthentication::where('vchip_client_id', $client->id)->first();
                            if(is_object($userAuth)){
                                $userAuth->access_token = $result->access_token;
                                $userAuth->refresh_token = $result->refresh_token;
                                $userAuth->token_type = $result->token_type;
                                $userAuth->save();
                                DB::connection('mysql2')->commit();
                                $this->info('create user auth client:'. $client->name);
                            }
                        }
                    }
                    if(!empty($instamojoAuthErrors)){
                        $this->info($instamojoAuthErrors);
                    }
                }
            }
        }
        catch(Exception $e)
        {
            DB::connection('mysql2')->rollback();
        }
        if( 0 == $errorCount ){
            $this->info('clients are successfully signup on instamojo.');
        } else {
            $this->info('errors are created while client signup on instamojo.');
        }
    }
}
