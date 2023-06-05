<?php

namespace App\Helpers;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Pnlinh\InfobipSms\Facades\InfobipSms;

class SmsMessage
{
    public $to;
    public $otp;

    /**
     * Create a new Sms instance.
     *
     * @return void
     */
    public function __construct($to, $otp)
    {
        $this->to = $to;
        $this->otp = $otp;

    }

    public function send(): void
    {
        InfobipSms::send($this->to, "Your OTP is: $this->otp");
    }
}

