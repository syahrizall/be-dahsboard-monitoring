<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginLog extends Model
{
    protected $fillable = [
        'username',
        'ip_address',
        'success',
        'realm',
        'resolver',
        'token_type',
        'serial',
        'action',
        'raw_payload',
    ];
    
    protected $casts = [
        'raw_payload' => 'array',
        'success' => 'boolean',
    ];   
}
