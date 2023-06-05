<?php

namespace App\Http\Controllers;

use App\Http\Requests\OTPVerificationRequest;
use Illuminate\Support\Facades\Cache;

class VerifyController extends Controller
{
    public function showOtpForm() {
        return view('auth.otp');
    }

    public function otp(OTPVerificationRequest $request)
    {
        if(request('otp') == Cache::get(auth()->user()->otpAuthKey())) {
            auth()->user()->update(['isVerified' => true]);
            return redirect('/home');
        }

        return back()->withErrors('OTP is invalid or has expired.');
    }
}
