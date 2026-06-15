@extends('layouts.auth')
@section('title', 'Verify OTP — EMS')

@section('content')

<div class="w-full max-w-sm">

    <div class="text-center mb-8">
        <h1 class="text-2xl font-semibold text-slate-800">Verification Code</h1>
        <p class="text-sm text-slate-500 mt-2">
            We've sent a 6-digit code to <span class="font-medium text-slate-700">{{ $maskedEmail }}</span>
        </p>
    </div>

    {{-- Success message --}}
    @if(session('success'))
        <div class="flex items-center gap-2 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg mb-5 text-sm">
            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Error message --}}
    @error('otp')
        <div class="flex items-center gap-2 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg mb-5 text-sm">
            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            {{ $message }}
        </div>
    @enderror

    <form method="POST" action="{{ route('otp.verify.post') }}">
        @csrf

        <div class="mb-6">
            <label class="block text-sm font-medium text-slate-700 mb-2 text-center">
                Enter Verification Code
            </label>
            <input type="text"
                   name="otp"
                   maxlength="6"
                   inputmode="numeric"
                   autocomplete="one-time-code"
                   autofocus
                   placeholder="• • • • • •"
                   class="w-full px-4 py-3 text-center text-2xl font-semibold tracking-[0.5em] rounded-lg
                          border border-slate-200 bg-white
                          focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
        </div>

        <button type="submit"
            class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium
                   rounded-lg transition-colors focus:outline-none focus:ring-2
                   focus:ring-indigo-500 focus:ring-offset-2">
            Verify Code
        </button>
    </form>

    <div class="mt-6 text-center">
        <p class="text-xs text-slate-400 mb-2">Didn't receive the code?</p>
        <form method="POST" action="{{ route('otp.resend') }}">
            @csrf
            <button type="submit" class="text-sm font-medium text-indigo-600 hover:text-indigo-700">
                Resend Code
            </button>
        </form>
    </div>

    <div class="mt-4 text-center">
        <a href="{{ route('login') }}" class="text-xs text-slate-400 hover:text-slate-600">
            ← Back to login
        </a>
    </div>

</div>

@endsection