@extends('layouts.app')
@section('title', 'Password Configuration — EMS')
@section('page-title', 'Password Configuration')
@section('page-subtitle', 'Set password requirements for all employees')

@section('content')

<div class="max-w-2xl">

    {{-- Info --}}
    <div class="bg-indigo-50 border border-indigo-200 rounded-xl px-5 py-4 mb-6 flex items-start gap-3">
        <svg class="w-5 h-5 text-indigo-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <p class="text-sm font-medium text-indigo-700">Changes apply to all future registrations</p>
            <p class="text-xs text-indigo-500 mt-0.5">Existing passwords are not affected until next change.</p>
        </div>
    </div>

    {{-- Success --}}
    @if(session('success'))
        <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg mb-5 text-sm">
            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.password.config.update') }}">
        @csrf @method('PUT')

        {{-- Length --}}
        <div class="bg-white rounded-xl border border-slate-200 p-6 mb-5">
            <h2 class="text-sm font-semibold text-slate-700 mb-4 pb-3 border-b border-slate-100">
                Length Requirements
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">
                        Minimum Length
                        <span class="text-slate-400 font-normal">(8–32)</span>
                    </label>
                    <input type="number" name="min_length"
                        value="{{ old('min_length', $config->min_length) }}"
                        min="8" max="32" required
                        class="w-full px-4 py-2.5 rounded-lg border text-sm focus:outline-none
                               focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all
                               {{ $errors->has('min_length') ? 'border-red-300 bg-red-50' : 'border-slate-200' }}">
                    @error('min_length')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">
                        Maximum Length
                        <span class="text-slate-400 font-normal">(32–128)</span>
                    </label>
                    <input type="number" name="max_length"
                        value="{{ old('max_length', $config->max_length) }}"
                        min="32" max="128" required
                        class="w-full px-4 py-2.5 rounded-lg border text-sm focus:outline-none
                               focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all
                               {{ $errors->has('max_length') ? 'border-red-300 bg-red-50' : 'border-slate-200' }}">
                    @error('max_length')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">
                        Minimum Words
                        <span class="text-slate-400 font-normal">(0 = no requirement)</span>
                    </label>
                    <input type="number" name="min_words"
                        value="{{ old('min_words', $config->min_words) }}"
                        min="0" max="10" required
                        class="w-full px-4 py-2.5 rounded-lg border text-sm focus:outline-none
                               focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all
                               {{ $errors->has('min_words') ? 'border-red-300 bg-red-50' : 'border-slate-200' }}">
                    @error('min_words')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-slate-400 mt-1">e.g. "correct horse battery" = 3 words</p>
                </div>

            </div>
        </div>

        {{-- Character Requirements --}}
        <div class="bg-white rounded-xl border border-slate-200 p-6 mb-5">
            <h2 class="text-sm font-semibold text-slate-700 mb-4 pb-3 border-b border-slate-100">
                Character Requirements
            </h2>
            <div class="space-y-3">

                {{-- Uppercase --}}
                <label class="flex items-center justify-between p-3 rounded-lg border border-slate-200 cursor-pointer hover:bg-slate-50 transition-colors">
                    <div>
                        <p class="text-sm font-medium text-slate-700">Require Uppercase</p>
                        <p class="text-xs text-slate-400 mt-0.5">At least one uppercase letter (A–Z)</p>
                    </div>
                    <div class="relative">
                        <input type="hidden" name="require_uppercase" value="0">
                        <input type="checkbox" name="require_uppercase" value="1"
                            {{ old('require_uppercase', $config->require_uppercase) ? 'checked' : '' }}
                            class="sr-only peer">
                        <div class="w-10 h-6 bg-slate-200 rounded-full peer-checked:bg-indigo-600 transition-colors"></div>
                        <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-all peer-checked:translate-x-4"></div>
                    </div>
                </label>

                {{-- Lowercase --}}
                <label class="flex items-center justify-between p-3 rounded-lg border border-slate-200 cursor-pointer hover:bg-slate-50 transition-colors">
                    <div>
                        <p class="text-sm font-medium text-slate-700">Require Lowercase</p>
                        <p class="text-xs text-slate-400 mt-0.5">At least one lowercase letter (a–z)</p>
                    </div>
                    <div class="relative">
                        <input type="hidden" name="require_lowercase" value="0">
                        <input type="checkbox" name="require_lowercase" value="1"
                            {{ old('require_lowercase', $config->require_lowercase) ? 'checked' : '' }}
                            class="sr-only peer">
                        <div class="w-10 h-6 bg-slate-200 rounded-full peer-checked:bg-indigo-600 transition-colors"></div>
                        <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-all peer-checked:translate-x-4"></div>
                    </div>
                </label>

                {{-- Number --}}
                <label class="flex items-center justify-between p-3 rounded-lg border border-slate-200 cursor-pointer hover:bg-slate-50 transition-colors">
                    <div>
                        <p class="text-sm font-medium text-slate-700">Require Number</p>
                        <p class="text-xs text-slate-400 mt-0.5">At least one number (0–9)</p>
                    </div>
                    <div class="relative">
                        <input type="hidden" name="require_number" value="0">
                        <input type="checkbox" name="require_number" value="1"
                            {{ old('require_number', $config->require_number) ? 'checked' : '' }}
                            class="sr-only peer">
                        <div class="w-10 h-6 bg-slate-200 rounded-full peer-checked:bg-indigo-600 transition-colors"></div>
                        <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-all peer-checked:translate-x-4"></div>
                    </div>
                </label>

                {{-- Special Char --}}
                <label class="flex items-center justify-between p-3 rounded-lg border border-slate-200 cursor-pointer hover:bg-slate-50 transition-colors">
                    <div>
                        <p class="text-sm font-medium text-slate-700">Require Special Character</p>
                        <p class="text-xs text-slate-400 mt-0.5">At least one special char (!@#$%^&*...)</p>
                    </div>
                    <div class="relative">
                        <input type="hidden" name="require_special_char" value="0">
                        <input type="checkbox" name="require_special_char" value="1"
                            {{ old('require_special_char', $config->require_special_char) ? 'checked' : '' }}
                            class="sr-only peer">
                        <div class="w-10 h-6 bg-slate-200 rounded-full peer-checked:bg-indigo-600 transition-colors"></div>
                        <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-all peer-checked:translate-x-4"></div>
                    </div>
                </label>

            </div>
        </div>

        {{-- Security Policy --}}
        <div class="bg-white rounded-xl border border-slate-200 p-6 mb-6">
            <h2 class="text-sm font-semibold text-slate-700 mb-4 pb-3 border-b border-slate-100">
                Security Policy
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">
                        Expiry Days
                        <span class="text-slate-400 font-normal">(1–365)</span>
                    </label>
                    <input type="number" name="password_expiry_days"
                        value="{{ old('password_expiry_days', $config->password_expiry_days) }}"
                        min="1" max="365" required
                        class="w-full px-4 py-2.5 rounded-lg border text-sm focus:outline-none
                               focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all
                               {{ $errors->has('password_expiry_days') ? 'border-red-300 bg-red-50' : 'border-slate-200' }}">
                    @error('password_expiry_days')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-slate-400 mt-1">Password expires after N days</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">
                        Change Cooldown (hrs)
                        <span class="text-slate-400 font-normal">(1–168)</span>
                    </label>
                    <input type="number" name="change_cooldown_hours"
                        value="{{ old('change_cooldown_hours', $config->change_cooldown_hours) }}"
                        min="1" max="168" required
                        class="w-full px-4 py-2.5 rounded-lg border text-sm focus:outline-none
                               focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all
                               {{ $errors->has('change_cooldown_hours') ? 'border-red-300 bg-red-50' : 'border-slate-200' }}">
                    @error('change_cooldown_hours')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-slate-400 mt-1">Change once per N hours</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">
                        History Count
                        <span class="text-slate-400 font-normal">(1–20)</span>
                    </label>
                    <input type="number" name="password_history_count"
                        value="{{ old('password_history_count', $config->password_history_count) }}"
                        min="1" max="20" required
                        class="w-full px-4 py-2.5 rounded-lg border text-sm focus:outline-none
                               focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all
                               {{ $errors->has('password_history_count') ? 'border-red-300 bg-red-50' : 'border-slate-200' }}">
                    @error('password_history_count')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-slate-400 mt-1">Cannot reuse last N passwords</p>
                </div>

            </div>
        </div>

        {{-- Current Summary --}}
        <div class="bg-slate-50 rounded-xl border border-slate-200 p-5 mb-6">
            <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Current Active Rules</h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-3">
                <div class="bg-white rounded-lg border border-slate-200 px-3 py-2.5 text-center">
                    <p class="text-xl font-semibold text-slate-800">{{ $config->min_length }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">Min length</p>
                </div>
                <div class="bg-white rounded-lg border border-slate-200 px-3 py-2.5 text-center">
                    <p class="text-xl font-semibold text-slate-800">{{ $config->max_length }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">Max length</p>
                </div>
                <div class="bg-white rounded-lg border border-slate-200 px-3 py-2.5 text-center">
                    <p class="text-xl font-semibold text-slate-800">{{ $config->min_words }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">Min words</p>
                </div>
                <div class="bg-white rounded-lg border border-slate-200 px-3 py-2.5 text-center">
                    <p class="text-xl font-semibold text-slate-800">{{ $config->password_expiry_days }}d</p>
                    <p class="text-xs text-slate-400 mt-0.5">Expiry</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <span class="px-2.5 py-1 text-xs font-medium rounded-full
                    {{ $config->require_uppercase ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-400' }}">
                    {{ $config->require_uppercase ? '✓' : '✗' }} Uppercase
                </span>
                <span class="px-2.5 py-1 text-xs font-medium rounded-full
                    {{ $config->require_lowercase ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-400' }}">
                    {{ $config->require_lowercase ? '✓' : '✗' }} Lowercase
                </span>
                <span class="px-2.5 py-1 text-xs font-medium rounded-full
                    {{ $config->require_number ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-400' }}">
                    {{ $config->require_number ? '✓' : '✗' }} Number
                </span>
                <span class="px-2.5 py-1 text-xs font-medium rounded-full
                    {{ $config->require_special_char ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-400' }}">
                    {{ $config->require_special_char ? '✓' : '✗' }} Special char
                </span>
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex items-center gap-3">
            <button type="submit"
                class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium
                       rounded-lg transition-colors focus:outline-none focus:ring-2
                       focus:ring-indigo-500 focus:ring-offset-2">
                Save Configuration
            </button>
            <a href="{{ route('admin.dashboard') }}"
                class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm
                       font-medium rounded-lg transition-colors">
                Cancel
            </a>
        </div>

    </form>
</div>

@endsection