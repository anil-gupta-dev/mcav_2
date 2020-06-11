<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Jobs\SendEmailByCronJobFinal;
use App\Models\CronLog;
use App\Models\EmailLog;
use App\Models\EmailSettings;

class SendEmailByCronJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $yesterday_date;
    public $first_day_this_month;
    public $last_day_this_month;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($yesterday_date, $first_day_this_month, $last_day_this_month)
    {
         $this->yesterday_date  = $yesterday_date;
         $this->first_day_this_month  = $first_day_this_month;
         $this->last_day_this_month  = $last_day_this_month;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        
        $get_state_option =EmailSettings::where('option','cron_email_state')->pluck('value');
         if (!empty($get_state_option) ) {

            $get_state_option = $get_state_option[0];

            if (!empty($this->yesterday_date) && empty($this->first_day_this_month)  ) {
             
            $get_data =\DB::table("imported_excel_data")->distinct()->whereNotNull('email')->where('state','=',$get_state_option)->where('doi','=',$this->yesterday_date)->get();
            }
            else
            {
                
                $get_data =\DB::table("imported_excel_data")->distinct()->whereNotNull('email')->where('state','=',$get_state_option)->whereBetween('doi',[$this->first_day_this_month, $this->last_day_this_month])->get();
            }


            if (!empty($get_data)) {

                 $count_get_data = count($get_data);

                 $insert_log = new CronLog();
                 $insert_log->log = 'Email Sent By Cron : Totaly '.$count_get_data.' Records Found For the DOI: '.$this->yesterday_date;
                 $insert_log->updated_on = date("Y-m-d h:i:s:a");
                 $insert_log->save ();

                foreach ($get_data->chunk(100) as $key => $value) 
                {
                    foreach ($value as  $data) 
                    {
                        $email       = $data->email;
                        $email       = strtolower($email);
                        // $category    = $data->category;
                        $description = $data->activity_description;
                         $get_email_template =\DB::table("email_templates")->where('activity_description','=',$description)->get();
                         if (count($get_email_template)==1) 
                         {
                             dispatch(new SendEmailByCronJobFinal($email, $get_email_template));
                         }
                         else
                         {
                                // store log in cron log table
                                 $insert_cron_log = new CronLog();
                                 $insert_cron_log->log = 'No template found for the Activity Description '.$description;
                                 $insert_cron_log->updated_on = date("Y-m-d h:i:s:a");
                                 $insert_cron_log->save ();

                                 // sore log in Email log table with email id satus 0
                                    $insert_log=new EmailLog;
                                    $insert_log->email=$email;
                                    $insert_log->template='Template not found';
                                    $insert_log->status=0;
                                    $insert_log->sended_at=now();
                                    $insert_log->sended_by='Cron';
                                    $insert_log->save();

                         }
                    }
                }
            }
            else{

                 $insert_log = new CronLog();
                 $insert_log->log = 'Email Sent By Cron : No Email Found For the Date: '.$this->yesterday_date;
                 $insert_log->updated_on = date("Y-m-d h:i:s:a");
                 $insert_log->save ();

            }  
        }
        else{
             $insert_log = new CronLog();
             $insert_log->log = 'Email Sent By Cron Failed Not found Option=> cron_email_state';
             $insert_log->updated_on = date("Y-m-d h:i:s:a");
             $insert_log->save ();
        }

        
        // $get_data =\DB::table("imported_excel_data")->distinct()->whereNotNull('email')->where('state','=','Maharashtra')->whereBetween('doi', [$first_day_this_month, $last_day_this_month])->get();

       
    }
}
