@extends('layouts.auth')
@section('title', 'Register — EMS')

@section('content')
<div>
    <div class="mb-8">
        <h1 class="text-2xl font-semibold text-slate-800">Create account</h1>
        <p class="text-slate-500 text-sm mt-1">Fill in your details to get started</p>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-lg px-4 py-3 mb-6 space-y-1">
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

    <form method="POST" action="{{ route('register.post') }}" class="space-y-5">
        @csrf

        {{-- Name --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Full name</label>
            <input
                type="text"
                name="name"
                value="{{ old('name') }}"
                placeholder="Abdur Rahman"
                required autofocus
                class="w-full px-4 py-2.5 rounded-lg border text-sm text-slate-800 placeholder-slate-400
                       bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                       transition-all duration-150
                       {{ $errors->has('name') ? 'border-red-300 bg-red-50' : 'border-slate-200' }}">
        </div>

        {{-- Email --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Email address</label>
            <input
                type="email"
                name="email"
                value="{{ old('email') }}"
                placeholder="name@gmail.com"
                required
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
            oninput="checkPassword(this.value)"
            class="w-full px-4 py-2.5 rounded-lg border bg-white text-sm text-slate-800
                   placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500
                   focus:border-transparent transition-all duration-150 pr-10
                   {{ $errors->has('password') ? 'border-red-300 bg-red-50' : 'border-slate-200' }}">
        <button type="button" onclick="togglePass('password')"
            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
        </button>
    </div>

    {{-- Requirements --}}
    <div class="mt-2 p-3 bg-slate-50 rounded-lg border border-slate-100 space-y-1.5">
        <p class="text-xs font-medium text-slate-400 mb-2">Password requirements:</p>

        <div id="req-min" class="flex items-center gap-2 text-xs text-slate-400 transition-colors duration-200">
            <span id="icon-min" class="w-3.5 h-3.5 rounded-full border-2 border-slate-300 flex-shrink-0 flex items-center justify-center text-white text-xs"></span>
            Use Complex Password Or At least {{ $config->min_length }} characters
        </div>

        <div id="req-max" class="flex items-center gap-2 text-xs text-slate-400 transition-colors duration-200">
            <span id="icon-max" class="w-3.5 h-3.5 rounded-full border-2 border-slate-300 flex-shrink-0 flex items-center justify-center text-white text-xs"></span>
            Maximum {{ $config->max_length }} characters
        </div>

        @if($config->min_words > 0)
        <div id="req-words" class="flex items-center gap-2 text-xs text-slate-400 transition-colors duration-200">
            <span id="icon-words" class="w-3.5 h-3.5 rounded-full border-2 border-slate-300 flex-shrink-0 flex items-center justify-center text-white text-xs"></span>
            At least {{ $config->min_words }} words
        </div>
        @endif

        @if($config->require_uppercase)
        <div id="req-upper" class="flex items-center gap-2 text-xs text-slate-400 transition-colors duration-200">
            <span id="icon-upper" class="w-3.5 h-3.5 rounded-full border-2 border-slate-300 flex-shrink-0 flex items-center justify-center text-white text-xs"></span>
            At least one uppercase letter (A–Z)
        </div>
        @endif

        @if($config->require_lowercase)
        <div id="req-lower" class="flex items-center gap-2 text-xs text-slate-400 transition-colors duration-200">
            <span id="icon-lower" class="w-3.5 h-3.5 rounded-full border-2 border-slate-300 flex-shrink-0 flex items-center justify-center text-white text-xs"></span>
            At least one lowercase letter (a–z)
        </div>
        @endif

        @if($config->require_number)
        <div id="req-num" class="flex items-center gap-2 text-xs text-slate-400 transition-colors duration-200">
            <span id="icon-num" class="w-3.5 h-3.5 rounded-full border-2 border-slate-300 flex-shrink-0 flex items-center justify-center text-white text-xs"></span>
            At least one number (0–9)
        </div>
        @endif

        @if($config->require_special_char)
        <div id="req-special" class="flex items-center gap-2 text-xs text-slate-400 transition-colors duration-200">
            <span id="icon-special" class="w-3.5 h-3.5 rounded-full border-2 border-slate-300 flex-shrink-0 flex items-center justify-center text-white text-xs"></span>
            At least one special character (!@#$%...)
        </div>
        @endif

    </div>
</div>
{{-- 16+ character hint --}}
<div id="long-password-hint"
     style="display:none"
     class="mt-2 flex items-center gap-2 text-xs text-emerald-600 font-medium bg-emerald-50
            border border-emerald-200 rounded-lg px-3 py-2">
    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
    </svg>
    {{ $config->min_length }}+ characters — complexity requirements waived!
</div>

        {{-- Confirm Password --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Confirm password</label>
            <div class="relative">
                <input
                    type="password"
                    name="password_confirmation"
                    id="password_confirmation"
                    placeholder="Re-enter your password"
                    required
                    class="w-full px-4 py-2.5 rounded-lg border border-slate-200 bg-white text-sm text-slate-800
                           placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500
                           focus:border-transparent transition-all duration-150 pr-10">
                <button type="button" onclick="togglePass('password_confirmation')"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Submit --}}
        <button type="submit"
            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm
                   px-4 py-2.5 rounded-lg transition-all duration-150 focus:outline-none
                   focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            Create account
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-500">
        Already have an account?
        <a href="{{ route('login') }}" class="text-indigo-600 font-medium hover:text-indigo-700 transition-colors">
            Sign in
        </a>
    </p>
</div>


<script>
const minLen     = {{ $config->min_length }};
const maxLen     = {{ $config->max_length }};
const minWords   = {{ $config->min_words }};
const needUpper  = {{ $config->require_uppercase ? 'true' : 'false' }};
const needLower  = {{ $config->require_lowercase ? 'true' : 'false' }};
const needNum    = {{ $config->require_number ? 'true' : 'false' }};
const needSpec   = {{ $config->require_special_char ? 'true' : 'false' }};

const LONG_PASSWORD_THRESHOLD = {{ $config->min_length }}; // এই length এর উপরে complex rules নেই

function setReq(rowId, iconId, passed) {
    const row  = document.getElementById(rowId);
    const icon = document.getElementById(iconId);
    if (!row || !icon) return;

    if (passed) {
        row.className  = 'flex items-center gap-2 text-xs text-indigo-600 font-medium transition-colors duration-200';
        icon.className = 'w-3.5 h-3.5 rounded-full bg-indigo-500 border-2 border-indigo-500 flex-shrink-0 flex items-center justify-center text-white text-xs';
        icon.textContent = '✓';
    } else {
        row.className  = 'flex items-center gap-2 text-xs text-slate-400 transition-colors duration-200';
        icon.className = 'w-3.5 h-3.5 rounded-full border-2 border-slate-300 flex-shrink-0 flex items-center justify-center text-white text-xs';
        icon.textContent = '';
    }
}

function hideReq(rowId) {
    const row = document.getElementById(rowId);
    if (row) row.style.display = 'none';
}

function showReq(rowId) {
    const row = document.getElementById(rowId);
    if (row) row.style.display = 'flex';
}

function countWords(str) {
    const spaced = str.replace(/([a-z])([A-Z])/g, '$1 $2');
    return spaced.trim() === '' ? 0 : spaced.trim().split(/\s+/).length;
}

function checkPassword(val) {
    const isLong = val.length >= LONG_PASSWORD_THRESHOLD;

    // Length check — সবসময় দেখাবে
    setReq('req-min', 'icon-min', val.length >= minLen);
    setReq('req-max', 'icon-max', val.length <= maxLen && val.length > 0);

    // Word count — সবসময় দেখাবে
    setReq('req-words', 'icon-words', countWords(val) >= minWords);

    if (isLong) {
        // 16+ character → complex rules hide করো বা green করো
        setReq('req-upper',   'icon-upper',   true);
        setReq('req-lower',   'icon-lower',   true);
        setReq('req-num',     'icon-num',     true);
        setReq('req-special', 'icon-special', true);

        // hint text দেখাও
        const hint = document.getElementById('long-password-hint');
        if (hint) hint.style.display = 'block';

    } else {
        // 8–15 character → complex rules check করো
        setReq('req-upper',   'icon-upper',   !needUpper || /[A-Z]/.test(val));
        setReq('req-lower',   'icon-lower',   !needLower || /[a-z]/.test(val));
        setReq('req-num',     'icon-num',     !needNum   || /[0-9]/.test(val));
        setReq('req-special', 'icon-special', !needSpec  || /[\W_]/.test(val));

        const hint = document.getElementById('long-password-hint');
        if (hint) hint.style.display = 'none';
    }
}

function togglePass(id) {
    const input = document.getElementById(id);
    input.type  = input.type === 'password' ? 'text' : 'password';
}
</script>
@endsection