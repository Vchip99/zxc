<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\DeActivatedClientPayableSubCategory;
use App\Models\ClientOnlineTestSubCategory;
use App\Models\PayableClientSubCategory;
use App\Models\ClientScore;
use App\Models\ClientUserSolution;
use DB;

class DeActivateClientPayableSubCategory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deactivateclientpayablesubcategory:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'DeActivate Client Payable SubCategory';

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
        try
        {
            $payableClientSubCategories = PayableClientSubCategory::getDeActivePayableSubCategory();
            if(is_object($payableClientSubCategories) && false == $payableClientSubCategories->isEmpty()){
                DB::connection('mysql2')->beginTransaction();
                try
                {
                    foreach($payableClientSubCategories as $payableClientSubCategory){
                        $testSubcategory = ClientOnlineTestSubCategory::find($payableClientSubCategory->sub_category_id);
                        if(is_object($testSubcategory)){
                            if(true == is_object($testSubcategory->subjects) && false == $testSubcategory->subjects->isEmpty()){
                                foreach($testSubcategory->subjects as $testSubject){
                                    if(true == is_object($testSubject->papers) && false == $testSubject->papers->isEmpty()){
                                        foreach($testSubject->papers as $paper){
                                            if(true == is_object($paper->questions) && false == $paper->questions->isEmpty()){
                                                foreach($paper->questions as $question){
                                                    ClientUserSolution::deleteClientUserSolutionsByQuestionId($question->id);
                                                }
                                            }
                                            ClientScore::deleteScoresByPaperId($paper->id);
                                            $paper->deletePayableRegisteredPaper();
                                        }
                                    }
                                }
                            }
                        }
                        if(is_file($payableClientSubCategory->client_image)){
                            unlink($payableClientSubCategory->client_image);
                        }
                        $payableClientSubCategory->client_image = '';
                        $payableClientSubCategory->save();
                        DB::connection('mysql2')->commit();
                        $data['client'] = $payableClientSubCategory->client->name;
                        $data['subCategory'] = $payableClientSubCategory->sub_category;
                        $data['startDate'] = $payableClientSubCategory->start_date;
                        $data['endDate'] = $payableClientSubCategory->end_date;
                        $this->info('trigger mail to convey purchased sub category has been de-activated to:'.$payableClientSubCategory->client->name.'.<br/>');
                        Mail::to($payableClientSubCategory->client->email)->send(new DeActivatedClientPayableSubCategory($data));
                    }
                }
                catch(\Exception $e)
                {
                    DB::connection('mysql2')->rollback();
                }
            } else {
                $this->info('No Sub Category has been compelted activation duration on yesterday.');
            }
        }
        catch(Exception $e)
        {
            DB::connection('mysql2')->rollback();
        }
    }
}
