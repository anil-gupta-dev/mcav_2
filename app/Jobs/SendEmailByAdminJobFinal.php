<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Notifications\SendEmailByAdmin;

use App\Models\EmailLog;

class SendEmailByAdminJobFinal implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $email;
    public $get_email_template= array();


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email, $get_email_template)
    {
        $this->email              = $email;
        $this->get_email_template = $get_email_template;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try
        {
            // \Notification::send($this->user, new FinaliseStatus($this->events));
            // $sample=[];
            if (!empty($this->get_email_template)) 
            {
               foreach ($this->get_email_template as $key => $value) {
                   $template = $value->template_name;
               }
             \Notification::route('mail', $this->email)->notify(new SendEmailByAdmin($this->get_email_template));
                $insert_log=new EmailLog;
                $insert_log->email=$this->email;
                $insert_log->template=$template;
                $insert_log->status=1;
                $insert_log->sended_at=now();
                $insert_log->sended_by='Admin';
                $insert_log->save();
            }else
            {
                $insert_log=new EmailLog;
                $insert_log->email=$this->email;
                $insert_log->template='The respoective descripton email template not found';
                $insert_log->status=0;
                $insert_log->sended_at=now();
                $insert_log->sended_by='Admin';
                $insert_log->save();
            }

            

            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
        /*Mail sending*/
             return true;
    }
}
