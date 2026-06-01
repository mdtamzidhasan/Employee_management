@extends('layouts.auth')
@section('title', 'Login — EMS')

@section('content')
<div>
    <div class="mb-8">
        <h1 class="text-2xl font-semibold text-slate-800">Welcome back</h1>
        <p class="text-slate-500 text-sm mt-1">Sign in to your account to continue</p>
    </div>

    {{-- Validation errors --}}
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-lg px-4 py-3 mb-6">
            @foreach($errors->all() as $error)
                <p class="text-red-600 text-sm flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $error }}
                </p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
        @csrf

        {{-- Email --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Email address</label>
            <input
                type="email"
                name="email"
                value="{{ old('email') }}"
                placeholder="name@gmail.com"
                required autofocus
                class="w-full px-4 py-2.5 rounded-lg border text-sm text-slate-800 placeholder-slate-400
                       bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                       transition-all duration-150
                       {{ $errors->has('email') ? 'border-red-300 bg-red-50' : 'border-slate-200' }}">
        </div>

        {{-- Password --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
            <div class="relative">
                <input
                    type="password"
                    name="password"
                    id="password"
                    placeholder="Enter your password"
                    required
                    class="w-full px-4 py-2.5 rounded-lg border border-slate-200 bg-white text-sm text-slate-800
                           placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500
                           focus:border-transparent transition-all duration-150 pr-10
                           {{ $errors->has('password') ? 'border-red-300 bg-red-50' : 'border-slate-200' }}">
                <button type="button" onclick="togglePassword()"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                    <svg id="eye-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Remember me --}}
        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="remember"
                    class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
                <span class="text-sm text-slate-600">Remember me</span>
            </label>
        </div>

        {{-- Submit --}}
        <button type="submit"
            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm
                   px-4 py-2.5 rounded-lg transition-all duration-150 focus:outline-none
                   focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            Sign in
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-500">
        Don't have an account?
        <a href="{{ route('register') }}" class="text-indigo-600 font-medium hover:text-indigo-700 transition-colors">
            Create one
        </a>
    </p>
</div>

<script>
function togglePassword() {
    const input = document.getElementById('password');
    input.type = input.type === 'password' ? 'text' : 'password';
}
</script>
@endsection