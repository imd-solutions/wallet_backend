<?php

namespace App\Models;

use App\Notifications\UserOTPNotification;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthenticableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements Authenticatable, AuthorizableContract, CanResetPasswordContract
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, AuthenticableTrait, CanResetPassword;

    public const OTP_MINUTES = 1;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'provider_id',
        'isVerified',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'isVerified'        => 'boolean',
        ];

    /**
     * Function Case: User relationship with Profile.
     *
     * @return HasOne
     */
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function can($abilities, $arguments = [])
    {
        // TODO: Implement can() method.
    }

    public function otp() {
        return Cache::get($this->otpAuthKey());
    }

    public function otpAuthKey()
    {
        return "OTP_auth_{$this->id}";
    }

    public function cacheOTP()
    {
        $otp = rand(1000, 9999);
        Cache::put($this->otpAuthKey(), $otp, now()->addMinutes(User::OTP_MINUTES));
        return $otp;
    }

    public function sendUserOTP($method, $phone = null)
    {
        $this->notify(new UserOTPNotification($method, $this->cacheOTP(), $phone));
    }
}
