<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginLog extends Model
{
    protected $fillable = [
        'username',
        'ip_address',
        'success',
        'raw_payload',
    ];
    
    protected $casts = [
        'raw_payload' => 'array',
        'success' => 'boolean',
    ];   
}
