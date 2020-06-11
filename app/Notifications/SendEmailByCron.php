<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\EmailTemplate;
use App\Models\EmailSettings;
use App\Models\EmailLog;
use App\Models\CronLog;

class SendEmailByCron extends Notification
{
    use Queueable;
    public $subject;
    public $message;
    public $attachment_path;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($subject, $message, $attachment_path)
    {
        $this->subject =$subject;
        $this->message =$message;
        $this->attachment_path =$attachment_path;
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
            $from_name = EmailSettings::where('option','from_name')->pluck('value');
            $email_from = EmailSettings::where('option','email_from')->pluck('value');
            if (!empty($from_name)) {$from_name =$from_name[0];}
            else{$from_name ='Administrator';}
            if (!empty($email_from)) {$email_from =$email_from[0];}
            else{$email_from ='';}

        return (new MailMessage)
                    ->from($email_from,$from_name)
                    ->subject($this->subject)
                    // ->line($this->message)
                    ->attach(storage_path('/email/'.$this->attachment_path))
                    ->markdown('email.markdown_admin', ['message' => $this->message]);
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
