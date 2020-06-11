<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Jobs\SendEmailByAdminJobFinal;

use App\Models\CronLog;

class SendEmailByAdminJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $state;
    public $roc;
    public $doi;
    public $description;
    // public $category;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($state, $roc, $doi, $description)
    {
         $this->state       = $state;
         $this->roc         = $roc;
         $this->doi         = $doi;
         $this->description = $description;
         // $this->category    = $category;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        
        $get_email_ids =\DB::table("imported_excel_data")->distinct()->whereNotNull('email')->where('state','=',$this->state)->where('roc','LIKE',$this->roc)->where('doi','=',$this->doi)->where('activity_description','LIKE',$this->description)->orderBy('email')->pluck("email");

        if (!empty($get_email_ids)) 
        {
             $count = count($get_email_ids);
             $insert_log = new CronLog();
             $insert_log->log = 'Email Sent By Admin count: '.$count;
             $insert_log->updated_on = date("Y-m-d h:i:s:a");
             $insert_log->save ();


            $get_email_template =\DB::table("email_templates")->where('activity_description','LIKE',$this->description)->get();


            foreach ($get_email_ids as $email) 
            {
                  $email =strtolower($email);
                  dispatch(new SendEmailByAdminJobFinal($email, $get_email_template));
            }
        }
        else
        {
             $insert_log = new CronLog();
             $insert_log->log = 'Email Sent By Admin count: No Email Found';
             $insert_log->updated_on = date("Y-m-d h:i:s:a");
             $insert_log->save ();
        }


         
    }
}
