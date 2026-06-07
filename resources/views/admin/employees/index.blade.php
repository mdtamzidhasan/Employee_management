@extends('layouts.app')
@section('title', 'Employees — EMS')
@section('page-title', 'Employees')
@section('page-subtitle', 'Manage all employee records')

@section('content')

{{-- Toolbar --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">

    {{-- Search & Filter --}}
    <form method="GET" action="{{ route('admin.employees.index') }}"
          class="flex items-center gap-2 flex-1 max-w-lg">
        <div class="relative flex-1">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search name or email..."
                class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-slate-200 bg-white text-sm
                       focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
        </div>

        <select name="department"
            class="px-3 py-2.5 rounded-lg border border-slate-200 bg-white text-sm text-slate-600
                   focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            <option value="">All departments</option>
            @foreach($departments as $dept)
                <option value="{{ $dept }}" {{ request('department') === $dept ? 'selected' : '' }}>
                    {{ $dept }}
                </option>
            @endforeach
        </select>

        <button type="submit"
            class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-medium
                   rounded-lg transition-colors">
            Filter
        </button>

        @if(request()->hasAny(['search', 'department']))
            <a href="{{ route('admin.employees.index') }}"
               class="px-4 py-2.5 text-slate-500 hover:text-slate-700 text-sm transition-colors">
                Clear
            </a>
        @endif
    </form>

    {{-- Add button --}}
    <a href="{{ route('admin.employees.create') }}"
        class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700
               text-white text-sm font-medium rounded-lg transition-colors flex-shrink-0">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Add Employee
    </a>
</div>

{{-- Table --}}
<div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    @if($employees->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-slate-400">
            <svg class="w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <p class="text-sm font-medium text-slate-500">No employees found</p>
            <p class="text-xs mt-1">Try adjusting your search or filter.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Employee</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Department</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Position</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Joined</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="text-right px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($employees as $emp)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4">
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
                                        <p class="font-medium text-slate-800">{{ $emp->name }}</p>
                                        <p class="text-xs text-slate-400">{{ $emp->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                {{ $emp->employee->department ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                {{ $emp->employee->position ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-slate-500">
                                {{ $emp->employee?->joining_date?->format('d M Y') ?? '—' }}
                            </td>
                            <td class="px-6 py-4">
                                @php $status = $emp->employee->status ?? 'active'; @endphp
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full
                                    {{ $status === 'active' ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-500' }}">
                                    <span class="w-1.5 h-1.5 rounded-full
                                        {{ $status === 'active' ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                    {{ ucfirst($status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.employees.show', $emp) }}"
                                        class="p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                                        title="View">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.employees.edit', $emp) }}"
                                        class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors"
                                        title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.employees.destroy', $emp) }}"
                                          onsubmit="return confirm('Delete {{ $emp->name }}? This cannot be undone.')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                            title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($employees->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $employees->withQueryString()->links() }}
            </div>
        @endif
    @endif
</div>

{{-- Count info --}}
<p class="mt-3 text-xs text-slate-400">
    Showing {{ $employees->firstItem() ?? 0 }}–{{ $employees->lastItem() ?? 0 }} of {{ $employees->total() }} employees
</p>

@endsection