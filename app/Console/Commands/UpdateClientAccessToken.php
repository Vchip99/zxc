<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\InstamojoDetail;
use App\Models\UserBasedAuthentication;
use DB;

class UpdateClientAccessToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'updateclientaccesstoken:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update client access token';

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
        set_time_limit(0);
        // check access token for application base auth
        $instamojoDetail = InstamojoDetail::first();

        if(!is_object($instamojoDetail)){
            exit();
        }

        $userPostFields = [
                        'grant_type' => 'refresh_token',
                        'client_id' => $instamojoDetail->client_id,
                        'client_secret' => $instamojoDetail->client_secret
                      ];
        DB::connection('mysql2')->beginTransaction();
        try
        {
            if('local' == \Config::get('app.env')){
                $userAuthUrl = "https://test.instamojo.com/oauth2/token/";
            } else {
                $userAuthUrl = "https://api.instamojo.com/oauth2/token/";
            }
            $users = UserBasedAuthentication::all();
            if(is_object($users) && false == $users->isEmpty()){
              $errorCount = 0;
                foreach($users as $user){
                    $userPostFields['refresh_token'] =  $user->refresh_token;

                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                      CURLOPT_URL => $userAuthUrl,
                      CURLOPT_RETURNTRANSFER => true,
                      CURLOPT_ENCODING => "",
                      CURLOPT_MAXREDIRS => 10,
                      CURLOPT_TIMEOUT => 60,
                      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                      CURLOPT_CUSTOMREQUEST => "POST",
                      CURLOPT_POSTFIELDS => $userPostFields,
                      CURLOPT_HTTPHEADER => array(
                        "cache-control: no-cache",
                        "content-type: multipart/form-data"
                      ),
                    ));

                    $response = curl_exec($curl);
                    $err = curl_error($curl);

                    curl_close($curl);

                    if ($err) {
                      echo "cURL Error #:" . $err;
                      $errorCount++;
                    } else {
                        $result = json_decode($response);
                        if(!empty($result->access_token) && !empty($result->refresh_token)){
                            $updateClient = UserBasedAuthentication::where('vchip_client_id', $user->vchip_client_id)->first();
                            if(is_object($updateClient)){
                                $updateClient->access_token = $result->access_token;
                                $updateClient->refresh_token = $result->refresh_token;
                                $updateClient->token_type = $result->token_type;
                                $updateClient->save();
                                DB::connection('mysql2')->commit();
                                $this->info($result->access_token);
                            }
                        } else {
                          $errorCount++;
                          $results = json_decode($response, true);
                          if(count($results) > 0){
                              $this->info('--------user_auth_error--------</br>');
                              foreach($results as $key => $result){
                                  $this->info($key.'->'.$result[0].'</br>');
                              }
                          }
                        }
                    }
                    if( 0 == $errorCount ){
                      $this->info('clientId: '. $user->vchip_client_id .' is successfully updated on instamojo.');
                    } else {
                      $this->info('errors are created while clientId: '. $user->vchip_client_id .' updated on instamojo.');
                    }
                }
            }
        }
        catch(Exception $e)
        {
            DB::connection('mysql2')->rollback();
        }
    }
}
