<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;


use App\Models\User;
use App\Services\SecurityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\SecurityLog;

class AuthController extends Controller
{
    public function __construct(protected SecurityLogger $logger) {}

    public function showRegister()
    {
        $config = \App\Models\PasswordConfiguration::getConfig();
        return view('auth.register', compact('config'));
    }

    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'employee',
        ]);

        $user->employee()->create();
        Auth::login($user);

        // ── Log registration ───────────────────────────────
        $this->logger->info(
            SecurityLog::EVENT_LOGIN_SUCCESS,
            "New employee registered: {$user->email}",
            ['user_id' => $user->id, 'name' => $user->name]
        );

        return redirect()->route('employee.profile')
            ->with('success', 'Registration successful. Welcome!');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        // ── Account lock check ─────────────────────────────
        if ($user && $user->locked_until && now()->isBefore($user->locked_until)) {
            $minutesLeft = now()->diffInMinutes($user->locked_until) + 1;

            $this->logger->warning(
                SecurityLog::EVENT_ACCOUNT_LOCKED,
                "Login attempt on locked account: {$request->email}",
                ['email' => $request->email, 'locked_until' => $user->locked_until]
            );

            return back()->withErrors([
                'email' => "Account is locked. Try again in {$minutesLeft} minute(s).",
            ])->withInput($request->only('email'));
        }

        $credentials = $request->only('email', 'password');
        $remember    = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {

            // ── Login success ──────────────────────────────
            if ($user) {
                $user->update([
                    'failed_login_attempts' => 0,
                    'locked_until'          => null,
                    'last_failed_at'        => null,
                ]);
            }

            $request->session()->regenerate();

            $this->logger->info(
                SecurityLog::EVENT_LOGIN_SUCCESS,
                "Successful login: {$request->email}",
                ['role' => auth()->user()->role]
            );

            return redirect()->intended(
                auth()->user()->isAdmin()
                    ? route('admin.dashboard')
                    : route('employee.profile')
            )->with('success', 'Login successful. Welcome back!');
        }

        // ── Login failed ───────────────────────────────────────
        if ($user) {
            $attempts = $user->failed_login_attempts + 1;
            $data = [
            'failed_login_attempts' => $attempts,
            'last_failed_at'        => now(),
            ];

            if ($attempts >= 5) {
                // 5 বার হলে lock করো
                $data['locked_until'] = now()->addMinutes(30);
                $user->update($data);

                $this->logger->critical(
                SecurityLog::EVENT_ACCOUNT_LOCKED,
                "Account locked after {$attempts} failed attempts: {$request->email}",
                ['email' => $request->email, 'attempts' => $attempts]
                );

                return back()->withErrors([
                     'email' => 'Too many failed attempts. Account locked for 30 minutes.',
                ])->withInput($request->only('email'));
            }

            // 5 এর কম হলে remaining দেখাও
            $user->update($data);
            $remaining = 5 - $attempts;

            $this->logger->warning(
                SecurityLog::EVENT_LOGIN_FAILED,
                "Failed login attempt for: {$request->email}",
                ['email' => $request->email, 'attempts' => $attempts]
            );

            return back()->withErrors([
            'email' => "Invalid credentials. {$remaining} attempt(s) remaining before lockout.",
            ])->withInput($request->only('email'));
        }

        $this->logger->warning(
            SecurityLog::EVENT_LOGIN_FAILED,
            "Failed login for unknown email: {$request->email}",
            ['email' => $request->email]
        );

        return back()->withErrors([
            'email' => 'These credentials do not match our records.',
        ])->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        $email = auth()->user()->email;

        $this->logger->info(
            SecurityLog::EVENT_LOGOUT,
            "User logged out: {$email}"
        );

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'You have been logged out successfully.');
    }
}