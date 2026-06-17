<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SecurityLog;
use App\Models\User;
use App\Services\OtpService;
use App\Services\SecurityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OtpController extends Controller
{
    public function __construct(
        protected SecurityLogger $logger,
        protected OtpService $otpService,
    ) {}

    // OTP Verify Page 
    public function show()
    {
  
        if (!session()->has('otp_user_id')) {
            return redirect()->route('login');
        }

        $user = User::find(session('otp_user_id'));

        if (!$user) {
            session()->forget(['otp_user_id', 'otp_remember']);
            return redirect()->route('login');
        }


        $maskedEmail = $this->maskEmail($user->email);

        return view('auth.verify-otp', compact('maskedEmail'));
    }

    // OTP Verify
    public function verify(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ], [
            'otp.required' => 'Please enter the verification code.',
            'otp.size'     => 'Verification code must be 6 digits.',
            ]);

        if (!session()->has('otp_user_id')) {
            return redirect()->route('login');
        }

        $user = User::find(session('otp_user_id'));

        if (!$user) {
            session()->forget(['otp_user_id', 'otp_remember', 'otp_is_register']);
            return redirect()->route('login');
        }

        $result = $this->otpService->verify($user, $request->otp, $request->ip());

        if (!$result['success']) {
            return back()->withErrors(['otp' => $result['message']]);
        }

        // register flow check
        $isRegisterFlow = session('otp_is_register', false);
        $remember       = session('otp_remember', false);

        //  OTP correct — login 
        Auth::login($user, $remember);

        if ($isRegisterFlow) {
            $user->update(['email_verified_at' => now()]);
        }
    

        session()->forget(['otp_user_id', 'otp_remember', 'otp_is_register']);
        $request->session()->regenerate();

        if ($isRegisterFlow) {
            $this->logger->info(
                SecurityLog::EVENT_LOGIN_SUCCESS,
                "Registration completed and email verified: {$user->email}",
                ['user_id' => $user->id]
            );

            return redirect()->route('employee.profile')
                ->with('success', 'Registration successful. Welcome to EMS!');
        }

        $this->logger->info(
            SecurityLog::EVENT_LOGIN_SUCCESS,
            "Successful login (OTP verified): {$user->email}",
            ['role' => $user->role]
        );

        return redirect()->intended(
            $user->isAdmin()
                ? route('admin.dashboard')
                : route('employee.profile')
        )->with('success', 'Login successful. Welcome back!');
    }

  
    public function resend(Request $request)
    {
        if (!session()->has('otp_user_id')) {
            return redirect()->route('login');
        }

        $user = User::find(session('otp_user_id'));

        if (!$user) {
            session()->forget(['otp_user_id', 'otp_remember']);
            return redirect()->route('login');
        }

        $this->otpService->generateAndSend($user, $request->ip());

        return back()->with('success', 'A new verification code has been sent to your email.');
    }

    //  Email masking helper 
    private function maskEmail(string $email): string
    {
        [$name, $domain] = explode('@', $email);

        $visibleChars = min(2, strlen($name));
        $masked = substr($name, 0, $visibleChars) . str_repeat('*', max(strlen($name) - $visibleChars, 3));

        return $masked . '@' . $domain;
    }
}