<?php

namespace App\Notifications;

use App\Channels\SmsChannel;
use App\Helpers\SmsMessage;
use App\Mail\UserOTPMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserOTPNotification extends Notification
{
    use Queueable;

    public $method;
    public $otp;
    public $phone;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($method, $otp, $phone)
    {
        $this->method = $method;
        $this->otp = $otp;
        $this->phone = $phone;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return $this->method === 'otp_sms' ? [SmsChannel::class] : ['mail'];
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
     * @return SmsMessage
     */
    public function toSms($notifiable)
    {
        return (new SmsMessage($this->phone, $this->otp));

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
    }}
