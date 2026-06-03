@extends('layouts.app')
@section('title', $user->name . ' — EMS')
@section('page-title', $user->name)
@section('page-subtitle', 'Employee details')

@section('content')

<div class="max-w-2xl">

    <div class="mb-5 flex items-center justify-between">
        <a href="{{ route('admin.employees.index') }}"
            class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to employees
        </a>
        <a href="{{ route('admin.employees.edit', $user) }}"
            class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Edit
        </a>
    </div>

    {{-- Profile Card --}}
    <div class="bg-white rounded-xl border border-slate-200 p-6 mb-5">
        <div class="flex items-center gap-4 mb-6 pb-6 border-b border-slate-100">
            <div class="w-16 h-16 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0">
                <span class="text-2xl font-semibold text-indigo-600">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </span>
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
                    <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-indigo-50 text-indigo-600">
                        Employee
                    </span>
                </div>
            </div>
        </div>

        {{-- Job Details --}}
        <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Job Information</h3>
        <div class="grid grid-cols-2 gap-x-8 gap-y-4 mb-6">
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
                        ৳{{ number_format($user->employee->salary, 2) }}
                    @else
                        —
                    @endif
                </p>
            </div>
            <div>
                <p class="text-xs text-slate-400 mb-0.5">Joining Date</p>
                <p class="text-sm font-medium text-slate-700">
                    {{ $user->employee?->joining_date?->format('d M Y') ?? '—' }}
                </p>
            </div>
        </div>

        {{-- Contact --}}
        <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Contact</h3>
        <div class="grid grid-cols-2 gap-x-8 gap-y-4">
            <div>
                <p class="text-xs text-slate-400 mb-0.5">Phone</p>
                <p class="text-sm font-medium text-slate-700">{{ $user->employee->phone ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400 mb-0.5">Member since</p>
                <p class="text-sm font-medium text-slate-700">{{ $user->created_at->format('d M Y') }}</p>
            </div>
            @if($user->employee?->address)
                <div class="col-span-2">
                    <p class="text-xs text-slate-400 mb-0.5">Address</p>
                    <p class="text-sm font-medium text-slate-700">{{ $user->employee->address }}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Danger Zone --}}
    <div class="bg-white rounded-xl border border-red-100 p-6">
        <h3 class="text-sm font-semibold text-red-600 mb-1">Danger Zone</h3>
        <p class="text-xs text-slate-500 mb-4">Permanently delete this employee and all their data.</p>
        <form method="POST" action="{{ route('admin.employees.destroy', $user) }}"
              onsubmit="return confirm('Are you sure? This will permanently delete {{ $user->name }}.')">
            @csrf @method('DELETE')
            <button type="submit"
                class="px-4 py-2 bg-red-50 hover:bg-red-100 text-red-600 text-sm font-medium
                       border border-red-200 rounded-lg transition-colors">
                Delete Employee
            </button>
        </form>
    </div>

</div>
@endsection