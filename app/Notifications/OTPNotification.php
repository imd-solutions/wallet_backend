<?php

namespace App\Notifications;

use App\Mail\UserOTPMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OTPNotification extends Notification
{
    use Queueable;

    public $method;
    public $otp;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($method, $otp)
    {
        $this->method = $method;
        $this->otp = $otp;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        dd($this->method);
        return $this->method === 'otp_sms' ? ['sms'] : ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return UserOTPMail
     */
    public function toMail($notifiable)
    {
        return (new UserOTPMail($this->otp))->to($notifiable->email);
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return UserOTPMail
     */
    public function toSms($notifiable)
    {
//        return (new UserOTPMail($this->otp))->to('test@test.com');
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
