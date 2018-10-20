<?php

use Illuminate\Database\Seeder;

class InstamojoDetailsTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('instamojo_details')->insert([
            ['id' => 1,'client_id' => '4IfB5qdRnGjcq1LqCgkHLdARUvK3oAg1FyGdnqIR','client_secret' => 'SH57WlO5DM5tyRFhKxUFw0AW9pcMPJuJuox6o6BoPwPsXw13ugRMUnPVMSSs6AYj0ZL51PyMhtfew5cSRNMgKBOLT65mS4WqVat27WmjP1G8mh277SCJBuj5Kc5ypQYP','referrer' => 'vchipdesign','application_base_access_token' => '' ,'application_base_token_type' => '']
        ]);
    }
}
