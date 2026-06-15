<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    protected $fillable = [
        'user_id',
        'code',
        'expires_at',
        'verified_at',
        'attempts',
        'ip_address',
    ];

    protected $casts = [
        'expires_at'  => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── এই OTP এখনো valid কিনা ──────────────────────────────
    public function isValid(): bool
    {
        return $this->verified_at === null
            && now()->isBefore($this->expires_at)
            && $this->attempts < 5;
    }

    public function isExpired(): bool
    {
        return now()->isAfter($this->expires_at);
    }
}