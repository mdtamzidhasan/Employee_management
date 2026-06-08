<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecurityLog extends Model
{
    protected $fillable = [
        'user_id',
        'event_type',
        'severity',
        'ip_address',
        'user_agent',
        'url',
        'description',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    // Event types
    const EVENT_LOGIN_SUCCESS    = 'login_success';
    const EVENT_LOGIN_FAILED     = 'login_failed';
    const EVENT_ACCOUNT_LOCKED   = 'account_locked';
    const EVENT_LOGOUT           = 'logout';
    const EVENT_PASSWORD_CHANGED = 'password_changed';
    const EVENT_PHOTO_UPLOADED   = 'photo_uploaded';
    const EVENT_PHOTO_REJECTED   = 'photo_rejected';
    const EVENT_PDF_DOWNLOADED   = 'pdf_downloaded';
    const EVENT_EMPLOYEE_CREATED = 'employee_created';
    const EVENT_EMPLOYEE_UPDATED = 'employee_updated';
    const EVENT_EMPLOYEE_DELETED = 'employee_deleted';
    const EVENT_CONFIG_CHANGED   = 'password_config_changed';
    const EVENT_RATE_LIMITED     = 'rate_limit_exceeded';
    const EVENT_SESSION_ANOMALY  = 'session_anomaly';
    const EVENT_UNAUTHORIZED     = 'unauthorized_access';

    // Severity levels
    const SEVERITY_INFO     = 'info';
    const SEVERITY_WARNING  = 'warning';
    const SEVERITY_CRITICAL = 'critical';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}