<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiKey extends Model
{
    protected $fillable = [
        'name',
        'key',
        'is_active',
        'last_used_at',
        'last_used_ip',
    ];

    protected $casts = [
        'is_active'     => 'boolean',
        'last_used_at'  => 'datetime',
    ];
}