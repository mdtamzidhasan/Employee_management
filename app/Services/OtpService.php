<?php

namespace App\Services;

use App\Mail\OtpMail;
use App\Models\Otp;
use App\Models\SecurityLog;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class OtpService
{
    public function __construct(protected SecurityLogger $logger) {}

    // ── নতুন OTP generate করে email পাঠায় ───────────────────
    public function generateAndSend(User $user, string $ip): Otp
    {
        // ── পুরানো unverified OTP গুলো invalidate করো ────────
        Otp::where('user_id', $user->id)
           ->whereNull('verified_at')
           ->update(['expires_at' => now()]); // expire করে দাও

        // ── নতুন 6 digit code generate করো ──────────────────
        $code = (string) random_int(100000, 999999);

        $otp = Otp::create([
            'user_id'    => $user->id,
            'code'       => $code,
            'expires_at' => now()->addMinutes(5),
            'attempts'   => 0,
            'ip_address' => $ip,
        ]);

        // ── Email পাঠাও ──────────────────────────────────────
        Mail::to($user->email)->send(new OtpMail($code, $user->name));

        $this->logger->info(
            SecurityLog::EVENT_OTP_SENT,
            "OTP sent to: {$user->email}",
            ['user_id' => $user->id, 'ip' => $ip]
        );

        return $otp;
    }

    // ── OTP verify করো ───────────────────────────────────────
    public function verify(User $user, string $code, string $ip): array
    {
        $otp = Otp::where('user_id', $user->id)
                  ->whereNull('verified_at')
                  ->latest()
                  ->first();

        // ── কোনো OTP নেই ──────────────────────────────────────
        if (!$otp) {
            $this->logger->warning(
                SecurityLog::EVENT_OTP_FAILED,
                "No active OTP found for: {$user->email}",
                ['user_id' => $user->id, 'ip' => $ip]
            );

            return ['success' => false, 'message' => 'No OTP found. Please request a new one.'];
        }

        // ── Expired ──────────────────────────────────────────
        if ($otp->isExpired()) {
            $this->logger->warning(
                SecurityLog::EVENT_OTP_FAILED,
                "Expired OTP attempt for: {$user->email}",
                ['user_id' => $user->id, 'ip' => $ip]
            );

            return ['success' => false, 'message' => 'OTP has expired. Please request a new one.'];
        }

        // ── Too many attempts ──────────────────────────────────
        if ($otp->attempts >= 5) {
            $this->logger->critical(
                SecurityLog::EVENT_OTP_FAILED,
                "Too many OTP attempts for: {$user->email}",
                ['user_id' => $user->id, 'ip' => $ip]
            );

            return ['success' => false, 'message' => 'Too many failed attempts. Please request a new OTP.'];
        }

        // ── Code ভুল ──────────────────────────────────────────
        if ($otp->code !== $code) {
            $otp->increment('attempts');

            $this->logger->warning(
                SecurityLog::EVENT_OTP_FAILED,
                "Invalid OTP attempt for: {$user->email}",
                ['user_id' => $user->id, 'ip' => $ip, 'attempts' => $otp->attempts]
            );

            $remaining = 5 - $otp->attempts;
            return ['success' => false, 'message' => "Invalid OTP. {$remaining} attempt(s) remaining."];
        }

        // ── সঠিক OTP ─────────────────────────────────────────
        $otp->update(['verified_at' => now()]);

        $this->logger->info(
            SecurityLog::EVENT_OTP_VERIFIED,
            "OTP verified successfully for: {$user->email}",
            ['user_id' => $user->id, 'ip' => $ip]
        );

        return ['success' => true, 'message' => 'OTP verified successfully.'];
    }
}