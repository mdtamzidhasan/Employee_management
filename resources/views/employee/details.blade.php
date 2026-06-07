@extends('layouts.app')
@section('title', 'Employee Details — EMS')
@section('page-title', 'Employee Details')
@section('page-subtitle', 'Your complete profile information')

@section('content')
<div class="max-w-2xl">

    {{-- Download Button --}}
    <div class="flex justify-end mb-5">
        <a href="{{ route('employee.details.download') }}"
            target="_blank"
            class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700
                   text-white text-sm font-medium rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Generate PDF
        </a>
    </div>


    {{-- Profile Header --}}
<div class="bg-white rounded-xl border border-slate-200 p-6 mb-5">
    <div class="flex items-center gap-4">

        {{-- Photo --}}
        <div class="w-16 h-16 rounded-full overflow-hidden flex-shrink-0 ring-2 ring-slate-200">
            @if($user->employee->profile_photo)
                <img src="{{ $user->employee->profile_photo }}"
                     alt="{{ $user->name }}"
                     class="w-full h-full object-cover">
            @else
                <div class="w-full h-full bg-indigo-100 flex items-center justify-center">
                    <span class="text-2xl font-semibold text-indigo-600">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </span>
                </div>
            @endif
        </div>
            <div>
                <h2 class="text-xl font-semibold text-slate-800">{{ $user->name }}</h2>
                <p class="text-sm text-slate-500">{{ $user->email }}</p>
                <div class="mt-2 flex items-center gap-2">
                    @php $status = $user->employee->status ?? 'active'; @endphp
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full
                        {{ $status === 'active' ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-500' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $status === 'active' ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                        {{ ucfirst($status) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- All Details --}}
    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <h2 class="text-sm font-semibold text-slate-700 mb-4 pb-3 border-b border-slate-100">
            Complete Information
        </h2>

        <div class="grid grid-cols-2 gap-x-8 gap-y-5">

            {{-- Account --}}
            <div class="col-span-2">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Account</p>
            </div>
            <div>
                <p class="text-xs text-slate-400 mb-0.5">Full Name</p>
                <p class="text-sm font-medium text-slate-700">{{ $user->name }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400 mb-0.5">Email</p>
                <p class="text-sm font-medium text-slate-700">{{ $user->email }}</p>
            </div>

            {{-- Divider --}}
            <div class="col-span-2 border-t border-slate-100 pt-2">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Job</p>
            </div>
            <div>
                <p class="text-xs text-slate-400 mb-0.5">Department</p>
                <p class="text-sm font-medium text-slate-700">{{ $user->employee->department ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400 mb-0.5">Position</p>
                <p class="text-sm font-medium text-slate-700">{{ $user->employee->position ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400 mb-0.5">Salary</p>
                <p class="text-sm font-medium text-slate-700">
                    @if($user->employee?->salary)
                        {{ number_format($user->employee->salary, 2) }}
                    @else —
                    @endif
                </p>
            </div>
            <div>
                <p class="text-xs text-slate-400 mb-0.5">Joining Date</p>
                <p class="text-sm font-medium text-slate-700">
                    {{ $user->employee?->joining_date?->format('d M Y') ?? '—' }}
                </p>
            </div>

            {{-- Divider --}}
            <div class="col-span-2 border-t border-slate-100 pt-2">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Contact</p>
            </div>
            <div>
                <p class="text-xs text-slate-400 mb-0.5">Phone</p>
                <p class="text-sm font-medium text-slate-700">{{ $user->employee->phone ?? '—' }}</p>
            </div>
            
            @if($user->employee?->address)
            <div class="col-span-2">
                <p class="text-xs text-slate-400 mb-0.5">Address</p>
                <p class="text-sm font-medium text-slate-700">{{ $user->employee->address }}</p>
            </div>
            @endif

        </div>
    </div>

</div>

@endsection