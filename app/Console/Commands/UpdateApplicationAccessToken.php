<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\InstamojoDetail;
use DB;

class UpdateApplicationAccessToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'updateapplicationaccesstoken:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update application access token';

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
        DB::connection('mysql')->beginTransaction();
        try
        {
            // check access token for application base auth
            $instamojoDetail = InstamojoDetail::first();
            $errorCount = 0;
            if(is_object($instamojoDetail)){
                // get & store application token
                $applicationPostFields = [
                                'grant_type' => 'client_credentials',
                                'client_id' => $instamojoDetail->client_id,
                                'client_secret' => $instamojoDetail->client_secret
                              ];
                if('local' == \Config::get('app.env')){
                    $appTokenUrl = "https://test.instamojo.com/oauth2/token/";
                } else {
                    $appTokenUrl = "https://api.instamojo.com/oauth2/token/";
                }

                $curl = curl_init();
                curl_setopt_array($curl, array(
                  CURLOPT_URL => $appTokenUrl,
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => "",
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 60,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => "POST",
                  CURLOPT_POSTFIELDS => $applicationPostFields,
                  CURLOPT_HTTPHEADER => array(
                    "cache-control: no-cache",
                    "content-type: multipart/form-data;"
                  ),
                ));

                $response = curl_exec($curl);
                $err = curl_error($curl);
                curl_close($curl);
                if ($err) {
                    echo $err;
                    $errorCount++;
                } else {
                    $result = json_decode($response);
                    if(!empty($result->access_token) && !empty($result->token_type)){
                        $instamojoDetail->application_base_access_token = $result->access_token;
                        $instamojoDetail->application_base_token_type = $result->token_type;
                        $instamojoDetail->save();
                        DB::connection('mysql')->commit();
                        $this->info($instamojoDetail->application_base_access_token);
                    } else {
                      $errorCount++;
                      $results = json_decode($response, true);
                      if(count($results) > 0){
                          $this->info('--------update_application_access_token_error--------</br>');
                          foreach($results as $key => $result){
                              $this->info($key.'->'.$result[0].'</br>');
                          }
                      }
                    }
                }
                if( 0 == $errorCount ){
                  $this->info('application access token successfully updated on instamojo.');
                } else {
                  $this->info('errors are created while application access token updated on instamojo.');
                }
            }
        }
        catch(Exception $e)
        {
            DB::connection('mysql')->rollback();
        }
    }
}
