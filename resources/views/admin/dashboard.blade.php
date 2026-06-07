@extends('layouts.app')
@section('title', 'Dashboard — EMS')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Overview of your organization')

@section('content')

{{-- Stats Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm text-slate-500 font-medium">Total Employees</p>
            <div class="w-9 h-9 bg-indigo-50 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-3xl font-semibold text-slate-800">
            {{ \App\Models\User::where('role', 'employee')->count() }}
        </p>
        <p class="text-xs text-slate-400 mt-1">Registered employees</p>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm text-slate-500 font-medium">Active</p>
            <div class="w-9 h-9 bg-emerald-50 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-3xl font-semibold text-slate-800">
            {{ \App\Models\Employee::where('status', 'active')->count() }}
        </p>
        <p class="text-xs text-slate-400 mt-1">Currently active</p>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm text-slate-500 font-medium">Inactive</p>
            <div class="w-9 h-9 bg-slate-100 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                </svg>
            </div>
        </div>
        <p class="text-3xl font-semibold text-slate-800">
            {{ \App\Models\Employee::where('status', 'inactive')->count() }}
        </p>
        <p class="text-xs text-slate-400 mt-1">Inactive employees</p>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm text-slate-500 font-medium">Departments</p>
            <div class="w-9 h-9 bg-amber-50 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
        </div>
        <p class="text-3xl font-semibold text-slate-800">
            {{ \App\Models\Employee::distinct()->whereNotNull('department')->count('department') }}
        </p>
        <p class="text-xs text-slate-400 mt-1">Unique departments</p>
    </div>

</div>

{{-- Recent Employees --}}
<div class="bg-white rounded-xl border border-slate-200">
    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
        <h2 class="text-sm font-semibold text-slate-700">Recent Employees</h2>
        <a href="{{ route('admin.employees.index') }}"
            class="text-xs text-indigo-600 hover:text-indigo-700 font-medium transition-colors">
            View all →
        </a>
    </div>

    @php
        $recent = \App\Models\User::with('employee')
            ->where('role', 'employee')
            ->latest()
            ->take(5)
            ->get();
    @endphp

    @if($recent->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-slate-400">
            <svg class="w-10 h-10 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <p class="text-sm">No employees yet.</p>
            <a href="{{ route('admin.employees.create') }}" class="mt-2 text-xs text-indigo-600 hover:text-indigo-700">Add the first one →</a>
        </div>
    @else
        <div class="divide-y divide-slate-50">
            @foreach($recent as $emp)
                <div class="flex items-center justify-between px-6 py-3.5 hover:bg-slate-50 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full overflow-hidden flex-shrink-0">
    @if($emp->employee?->profile_photo)
        <img src="{{ $emp->employee->profile_photo }}"
             alt="{{ $emp->name }}"
             class="w-full h-full object-cover">
    @else
        <div class="w-full h-full bg-indigo-100 flex items-center justify-center">
            <span class="text-sm font-semibold text-indigo-600">
                {{ strtoupper(substr($emp->name, 0, 1)) }}
            </span>
        </div>
    @endif
</div>
                        <div>
                            <p class="text-sm font-medium text-slate-800">{{ $emp->name }}</p>
                            <p class="text-xs text-slate-400">{{ $emp->email }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        @if($emp->employee)
                            <span class="text-xs text-slate-500">{{ $emp->employee->department ?? '—' }}</span>
                            <span class="px-2 py-0.5 text-xs rounded-full font-medium
                                {{ ($emp->employee->status ?? 'active') === 'active'
                                    ? 'bg-emerald-50 text-emerald-600'
                                    : 'bg-slate-100 text-slate-500' }}">
                                {{ ucfirst($emp->employee->status ?? 'active') }}
                            </span>
                        @endif
                        <a href="{{ route('admin.employees.show', $emp) }}"
                            class="text-slate-400 hover:text-indigo-600 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

@endsection