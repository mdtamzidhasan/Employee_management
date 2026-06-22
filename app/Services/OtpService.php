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

    // Generate new otp and send to the email
    public function generateAndSend(User $user, string $ip): Otp
    {
        // Time when send Last OTP 
        // $lastOtp = Otp::where('user_id', $user->id)
        //            ->latest()
        //            ->first();

        // if ($lastOtp && $lastOtp->created_at->diffInSeconds(now()) < 60) {
        //     throw new \Exception('Please wait 1 minute before requesting another OTP.');
        // }
        // ── invalidate old otp
        Otp::where('user_id', $user->id)
           ->whereNull('verified_at')
           ->update(['expires_at' => now()]); // expire 

        //new 6 digit code generate 
        $code = (string) random_int(100000, 999999);

        $otp = Otp::create([
            'user_id'    => $user->id,
            'code'       => $code,
            'expires_at' => now()->addMinutes(5),
            'attempts'   => 0,
            'ip_address' => $ip,
        ]);

        // Send Email
        Mail::to($user->email)->send(new OtpMail($code, $user->name));

        $this->logger->info(
            SecurityLog::EVENT_OTP_SENT,
            "OTP sent to: {$user->email}",
            ['user_id' => $user->id, 'ip' => $ip]
        );

        return $otp;
    }

    // OTP verify 
    public function verify(User $user, string $code, string $ip): array
    {
        $otp = Otp::where('user_id', $user->id)
                  ->whereNull('verified_at')
                  ->latest()
                  ->first();

        
        if (!$otp) {
            $this->logger->warning(
                SecurityLog::EVENT_OTP_FAILED,
                "No active OTP found for: {$user->email}",
                ['user_id' => $user->id, 'ip' => $ip]
            );

            return ['success' => false, 'message' => 'No OTP found. Please request a new one.'];
        }

        // Expired 
        if ($otp->isExpired()) {
            $this->logger->warning(
                SecurityLog::EVENT_OTP_FAILED,
                "Expired OTP attempt for: {$user->email}",
                ['user_id' => $user->id, 'ip' => $ip]
            );

            return ['success' => false, 'message' => 'OTP has expired. Please request a new one.'];
        }

        //  Too many attempts 
        if ($otp->attempts >= 5) {
                $user->update([
                'locked_until' => now()->addMinutes(30),
            ]);

            $this->logger->critical(
            SecurityLog::EVENT_ACCOUNT_LOCKED,
            "Account locked after too many OTP attempts: {$user->email}",
            ['user_id' => $user->id, 'ip' => $ip]
            );

            return ['success' => false, 'message' => 'Too many failed attempts. Account locked for 30 minutes.'];
        }

        // invalid Code 
        if (!hash_equals($otp->code, $code)) {
            $otp->increment('attempts');

            $this->logger->warning(
                SecurityLog::EVENT_OTP_FAILED,
                "Invalid OTP attempt for: {$user->email}",
                ['user_id' => $user->id, 'ip' => $ip, 'attempts' => $otp->attempts]
            );

            $remaining = 5 - $otp->attempts;
            return ['success' => false, 'message' => "Invalid OTP. {$remaining} attempt(s) remaining."];
        }

        // valid OTP
        $otp->update(['verified_at' => now()]);

        $this->logger->info(
            SecurityLog::EVENT_OTP_VERIFIED,
            "OTP verified successfully for: {$user->email}",
            ['user_id' => $user->id, 'ip' => $ip]
        );

        return ['success' => true, 'message' => 'OTP verified successfully.'];
    }
}