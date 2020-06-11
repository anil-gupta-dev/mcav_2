<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

use App\Models\EmailTemplate;
use App\Models\EmailSettings;

class SendEmailByAdmin extends Notification
{
    use Queueable;
    public $get_email_template = array();

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($get_email_template)
    {
        $this->get_email_template=$get_email_template;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
            foreach ($this->get_email_template as $key => $value) {
                $subject=$value->subject;
                $message=$value->message;
                $attachment_path=$value->attachment_path;
            }
            $from_name = EmailSettings::where('option','from_name')->pluck('value');
            $email_from = EmailSettings::where('option','email_from')->pluck('value');
            if (!empty($from_name)) {$from_name =$from_name[0];}
            else{$from_name ='Administrator';}
            if (!empty($email_from)) {$email_from =$email_from[0];}
            else{$email_from ='';}
        
        return (new MailMessage)
                    ->from($email_from,$from_name)
                    ->subject($subject)
                    // ->line($message)
                    ->attach(storage_path('/email/'.$attachment_path))
                    ->markdown('email.markdown_admin', ['message' => $message]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
