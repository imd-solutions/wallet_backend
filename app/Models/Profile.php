<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_id',
        'firstname',
        'lastname',
        'username',
        'password',
        'country_id',
        'currency_code',
        'language_code',
        'scopes',
        'active',
        'balance',
        'last_login',
        'deactivated_at',
        'deactivated_reason',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'customer_id' => 'integer',
        'country_id'  => 'integer',
        'scopes'      => 'array',
        'active'      => 'boolean',
    ];

    protected $dates = [
        'last_login',
        'deactivated_at'
    ];
}
