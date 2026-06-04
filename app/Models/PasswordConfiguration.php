<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordConfiguration extends Model
{
    protected $fillable = [
        'min_length',
        'max_length',
        'min_words',
        'require_uppercase',
        'require_lowercase',
        'require_number',
        'require_special_char',
        'password_expiry_days',
        'change_cooldown_hours',
        'password_history_count',
    ];

    protected $casts = [
        'require_uppercase'    => 'boolean',
        'require_lowercase'    => 'boolean',
        'require_number'       => 'boolean',
        'require_special_char' => 'boolean',
    ];

    public static function getConfig(): self
    {
        return self::firstOrCreate([], [
            'min_length'             => 8,
            'max_length'             => 64,
            'min_words'              => 0,
            'require_uppercase'      => true,
            'require_lowercase'      => true,
            'require_number'         => true,
            'require_special_char'   => false,
            'password_expiry_days'   => 90,
            'change_cooldown_hours'  => 24,
            'password_history_count' => 5,
        ]);
    }
}