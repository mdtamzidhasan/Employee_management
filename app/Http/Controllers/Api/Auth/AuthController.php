<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\SecurityLog;
use App\Models\User;
use App\Services\SecurityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct(protected SecurityLogger $logger) {}

    public function register(RegisterRequest $request)
    {
        // ── Same business logic as Web AuthController ────────
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'employee',
        ]);

        $user->employee()->create();

        $this->logger->info(
            SecurityLog::EVENT_LOGIN_SUCCESS,
            "New employee registered via API: {$user->email}",
            ['user_id' => $user->id, 'name' => $user->name]
        );

        // ── API specific: token তৈরি করো ──────────────────────
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful.',
            'user'    => new UserResource($user->load('employee')),
            'token'   => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        // ── Account lock check — Same as Web ──────────────────
        if ($user && $user->locked_until && now()->isBefore($user->locked_until)) {
            $minutesLeft = now()->diffInMinutes($user->locked_until) + 1;

            $this->logger->warning(
                SecurityLog::EVENT_ACCOUNT_LOCKED,
                "API login attempt on locked account: {$request->email}",
                ['email' => $request->email, 'locked_until' => $user->locked_until]
            );

            return response()->json([
                'message' => "Account is locked. Try again in {$minutesLeft} minute(s).",
            ], 423); // 423 Locked
        }

        $credentials = $request->only('email', 'password');

        // ── Same Auth::attempt() logic — কিন্তু session ছাড়া ────
        if (!Auth::validate($credentials)) {

            // ── Login failed — Same logic as Web ──────────────
            if ($user) {
                $recentFails = SecurityLog::where('event_type', SecurityLog::EVENT_LOGIN_FAILED)
                    ->where('user_id', $user->id)
                    ->where('created_at', '>=', now()->subMinutes(1))
                    ->count();

                $attempts = $recentFails + 1;

                $data = [
                    'failed_login_attempts' => $attempts,
                    'last_failed_at'        => now(),
                ];

                if ($attempts >= 5) {
                    $data['locked_until'] = now()->addMinutes(30);
                    $user->update($data);

                    $this->logger->critical(
                        SecurityLog::EVENT_ACCOUNT_LOCKED,
                        "Account locked after {$attempts} failed API login attempts: {$request->email}",
                        ['email' => $request->email, 'attempts' => $attempts]
                    );

                    return response()->json([
                        'message' => 'Too many failed attempts. Account locked for 30 minutes.',
                    ], 423);
                }

                $user->update($data);
                $remaining = 5 - $attempts;

                $this->logger->warning(
                    SecurityLog::EVENT_LOGIN_FAILED,
                    "Failed API login attempt for: {$request->email}",
                    ['email' => $request->email, 'attempts' => $attempts]
                );

                return response()->json([
                    'message' => "Invalid credentials. {$remaining} attempt(s) remaining before lockout.",
                ], 401);
            }

            $this->logger->warning(
                SecurityLog::EVENT_LOGIN_FAILED,
                "Failed API login for unknown email: {$request->email}",
                ['email' => $request->email]
            );

            return response()->json([
                'message' => 'These credentials do not match our records.',
            ], 401);
        }

        // ── Login success — Same logic as Web ──────────────────
        $user->update([
            'failed_login_attempts' => 0,
            'locked_until'          => null,
            'last_failed_at'        => null,
        ]);

        $this->logger->info(
            SecurityLog::EVENT_LOGIN_SUCCESS,
            "Successful API login: {$request->email}",
            ['role' => $user->role]
        );

        // ── API specific: পুরানো token মুছে নতুন token দাও ──────
        $user->tokens()->delete(); // আগের সব token revoke
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful.',
            'user'    => new UserResource($user->load('employee')),
            'token'   => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function logout(Request $request)
    {
        $email = $request->user()->email;

        $this->logger->info(
            SecurityLog::EVENT_LOGOUT,
            "User logged out via API: {$email}"
        );

        // ── Current token revoke করো ───────────────────────────
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }

    public function me(Request $request)
    {
        return new UserResource($request->user()->load('employee'));
    }
}