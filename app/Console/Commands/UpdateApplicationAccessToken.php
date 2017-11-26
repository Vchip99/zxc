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
            $instamojoDetail = InstamojoDetail::where('client_id', '4IfB5qdRnGjcq1LqCgkHLdARUvK3oAg1FyGdnqIR')->first();

            if(is_object($instamojoDetail)){

                // get & store application token
                $applicationPostFields = [
                                'grant_type' => 'client_credentials',
                                'client_id' => $instamojoDetail->client_id,
                                'client_secret' => $instamojoDetail->client_secret
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
                } else {
                    $result = json_decode($response);
                    if(!empty($result->access_token) && !empty($result->token_type)){
                        $instamojoDetail->application_base_access_token = $result->access_token;
                        $instamojoDetail->application_base_token_type = $result->token_type;
                        $instamojoDetail->save();
                        DB::connection('mysql')->commit();
                        $this->info($instamojoDetail->application_base_access_token);
                    }
                }
            }
        }
        catch(Exception $e)
        {
            DB::connection('mysql')->rollback();
        }
    }
}
