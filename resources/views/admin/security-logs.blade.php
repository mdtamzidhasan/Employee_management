@extends('layouts.app')
@section('title', 'Security Logs — EMS')
@section('page-title', 'Security Logs')
@section('page-subtitle', 'Monitor all security events')

@section('content')

{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-slate-200 p-4">
        <p class="text-xs text-slate-500 mb-1">Events Today</p>
        <p class="text-2xl font-semibold text-slate-800">{{ $stats['total_today'] }}</p>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-4">
        <p class="text-xs text-slate-500 mb-1">Failed Logins</p>
        <p class="text-2xl font-semibold text-amber-600">{{ $stats['failed_logins'] }}</p>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-4">
        <p class="text-xs text-slate-500 mb-1">Critical Events</p>
        <p class="text-2xl font-semibold text-red-600">{{ $stats['critical_today'] }}</p>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-4">
        <p class="text-xs text-slate-500 mb-1">Locked Accounts</p>
        <p class="text-2xl font-semibold text-red-600">{{ $stats['locked_accounts'] }}</p>
    </div>
</div>

{{-- Log Table --}}
<div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100">
        <h2 class="text-sm font-semibold text-slate-700">Recent Security Events</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100">
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Time</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Event</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Severity</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">User</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">IP Address</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Description</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($logs as $log)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 text-xs text-slate-500 whitespace-nowrap">
                            {{ $log->created_at->format('d M, h:i A') }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-xs font-mono bg-slate-100 text-slate-600 px-2 py-0.5 rounded">
                                {{ $log->event_type }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 text-xs font-medium rounded-full
                                {{ $log->severity === 'critical' ? 'bg-red-50 text-red-600' :
                                   ($log->severity === 'warning'  ? 'bg-amber-50 text-amber-600' :
                                                                     'bg-emerald-50 text-emerald-600') }}">
                                {{ ucfirst($log->severity) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-slate-600">
                            {{ $log->user?->name ?? 'Guest' }}
                        </td>
                        <td class="px-4 py-3 text-xs font-mono text-slate-500">
                            {{ $log->ip_address }}
                        </td>
                        <td class="px-4 py-3 text-slate-600 text-xs max-w-xs truncate">
                            {{ $log->description }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-slate-400 text-sm">
                            No security events recorded yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $logs->links() }}
        </div>
    @endif
</div>

@endsection