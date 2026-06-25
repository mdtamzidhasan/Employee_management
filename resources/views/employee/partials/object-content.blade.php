@php
    $type      = $viewData['type'] ?? 'generic';
    $operation = $operation ?? 'view';
@endphp

{{-- Salary View --}}
@if($type === 'salary' && $operation === 'view')
    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <h3 class="text-sm font-semibold text-slate-700 mb-4">My Salary Information</h3>
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-indigo-50 rounded-lg p-4">
                <p class="text-xs text-slate-400 mb-1">Monthly Salary</p>
                <p class="text-2xl font-bold text-indigo-600">
                    @if($viewData['salary'])
                        ৳{{ number_format($viewData['salary'], 2) }}
                    @else
                        Not Set
                    @endif
                </p>
            </div>
        </div>
    </div>

{{-- Salary Export --}}
@elseif($type === 'salary' && $operation === 'export')
    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <h3 class="text-sm font-semibold text-slate-700 mb-4">Export Salary Details</h3>
        <div class="flex gap-3">
            <a href="{{ route('employee.details.download') }}" target="_blank"
               class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white
                      text-sm font-medium rounded-lg transition-colors">
                Download PDF
            </a>
        </div>
    </div>

{{-- Employee List View --}}
@elseif(in_array($type, ['employee_list', 'department_employees', 'salary_list']) && $operation === 'view')
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-slate-700">
                @if($type === 'department_employees')
                    {{ $viewData['department'] }} Department — Employees
                @elseif($type === 'salary_list')
                    Employee Salary List
                @else
                    All Employees
                @endif
            </h3>
            <span class="px-2.5 py-1 bg-slate-100 text-slate-600 text-xs rounded-full">
                {{ $viewData['employees']->count() }} employees
            </span>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase">
                        Employee
                    </th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase">
                        Department
                    </th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase">
                        Position
                    </th>
                    @if($type === 'salary_list')
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase">
                            Salary
                        </th>
                    @endif
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase">
                        Status
                    </th>
                    @if(in_array('edit', $operations) || in_array('delete', $operations))
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase">
                            Actions
                        </th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($viewData['employees'] as $emp)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full overflow-hidden flex-shrink-0">
                                    @if($emp->employee?->profile_photo)
                                        <img src="{{ $emp->employee->profile_photo }}"
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full bg-indigo-100 flex items-center justify-center">
                                            <span class="text-xs font-semibold text-indigo-600">
                                                {{ strtoupper(substr($emp->name, 0, 1)) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-medium text-slate-700">{{ $emp->name }}</p>
                                    <p class="text-xs text-slate-400">{{ $emp->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-slate-600">
                            {{ $emp->employee?->department ?? '—' }}
                        </td>
                        <td class="px-5 py-3.5 text-slate-600">
                            {{ $emp->employee?->position ?? '—' }}
                        </td>
                        @if($type === 'salary_list')
                            <td class="px-5 py-3.5 font-medium text-slate-700">
                                @if($emp->employee?->salary)
                                    ৳{{ number_format($emp->employee->salary, 2) }}
                                @else —
                                @endif
                            </td>
                        @endif
                        <td class="px-5 py-3.5">
                            @php $status = $emp->employee?->status ?? 'active'; @endphp
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded-full
                                {{ $status === 'active' ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-500' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $status === 'active' ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                {{ ucfirst($status) }}
                            </span>
                        </td>
                        @if(in_array('edit', $operations) || in_array('delete', $operations))
                            <td class="px-5 py-3.5">
                                <div class="flex gap-2">
                                    @if(in_array('edit', $operations))
                                        <a href="{{ route('admin.employees.edit', $emp->id) }}"
                                           class="px-3 py-1.5 bg-amber-50 hover:bg-amber-100
                                                  text-amber-600 text-xs font-medium rounded-lg">
                                            Edit
                                        </a>
                                    @endif
                                    @if(in_array('delete', $operations))
                                        <form method="POST"
                                              action="{{ route('admin.employees.destroy', $emp->id) }}"
                                              onsubmit="return confirm('Delete {{ $emp->name }}?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="px-3 py-1.5 bg-red-50 hover:bg-red-100
                                                       text-red-600 text-xs font-medium rounded-lg">
                                                Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center text-slate-400">
                            No employees found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

{{-- Employee Create --}}
@elseif($type === 'employee_list' && $operation === 'create')
    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <h3 class="text-sm font-semibold text-slate-700 mb-4">Create New Employee</h3>
        <a href="{{ route('admin.employees.create') }}"
           class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white
                  text-sm font-medium rounded-lg transition-colors">
            Go to Create Employee Form
        </a>
    </div>

{{-- Reports --}}
@elseif($type === 'reports')
    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <h3 class="text-sm font-semibold text-slate-700 mb-4">Reports</h3>
        <a href="{{ $viewData['reports_url'] }}" target="_blank"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600
                  hover:bg-indigo-700 text-white text-sm font-medium rounded-lg">
            Open Reports Dashboard
        </a>
    </div>

{{-- Security Logs --}}
@elseif($type === 'security_logs' && $operation === 'view')
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100">
            <h3 class="text-sm font-semibold text-slate-700">Security Logs</h3>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase">Time</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase">Event</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase">Severity</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase">Description</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($viewData['logs'] as $log)
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-3 text-xs text-slate-500">
                            {{ $log->created_at->timezone('Asia/Dhaka')->format('d M, h:i A') }}
                        </td>
                        <td class="px-5 py-3">
                            <span class="text-xs font-mono bg-slate-100 text-slate-600 px-2 py-0.5 rounded">
                                {{ $log->event_type }}
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            <span class="px-2 py-0.5 text-xs font-medium rounded-full
                                {{ $log->severity === 'critical' ? 'bg-red-50 text-red-600' :
                                   ($log->severity === 'warning' ? 'bg-amber-50 text-amber-600' :
                                    'bg-emerald-50 text-emerald-600') }}">
                                {{ ucfirst($log->severity) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-slate-600 text-xs max-w-xs truncate">
                            {{ $log->description }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-10 text-center text-slate-400">No logs found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

{{-- Generic --}}
@else
    <div class="bg-white rounded-xl border border-slate-200 p-8 text-center">
        <p class="text-slate-400 text-sm">
            {{ $viewData['message'] ?? "Content for '{$operation}' operation is loading..." }}
        </p>
    </div>
@endif