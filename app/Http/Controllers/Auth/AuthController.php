<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'employee',
        ]); // Registration logic will be handled by RegisterRequest

        // Create an associated employee record if relation exists
        $user->employee()->create();

        Auth::login($user);

        return redirect()->route('employee.profile')->with('success', 'Registration successful. Welcome!');
    }



    public function showLogin()
    {
        return view('auth.login');
    }

public function login(LoginRequest $request)
{
    $credentials = $request->only('email', 'password'); // ✅ শুধু এটুকুই দরকার
    $remember    = $request->boolean('remember');

    if (Auth::attempt($credentials, $remember)) {
        $request->session()->regenerate();

        return redirect()->intended(
            auth()->user()->isAdmin()
                ? route('admin.dashboard')
                : route('employee.profile')
        )->with('success', 'Login successful. Welcome back!');
    }

    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ])->withInput($request->only('email', 'remember'));
}


    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }
}
