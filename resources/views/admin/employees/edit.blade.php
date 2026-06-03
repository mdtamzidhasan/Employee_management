@extends('layouts.app')
@section('title', 'Edit Employee — EMS')
@section('page-title', 'Edit Employee')
@section('page-subtitle', 'Update employee information')

@section('content')

<div class="max-w-2xl">

    <div class="mb-5">
        <a href="{{ route('admin.employees.index') }}"
            class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to employees
        </a>
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

    <form method="POST" action="{{ route('admin.employees.update', $user) }}">
        @csrf @method('PUT')

        {{-- Account Info --}}
        <div class="bg-white rounded-xl border border-slate-200 p-6 mb-5">
            <h2 class="text-sm font-semibold text-slate-700 mb-4 pb-3 border-b border-slate-100">Account Information</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full px-4 py-2.5 rounded-lg border text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all
                               {{ $errors->has('name') ? 'border-red-300 bg-red-50' : 'border-slate-200 bg-white' }}">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Email Address <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="w-full px-4 py-2.5 rounded-lg border text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all
                               {{ $errors->has('email') ? 'border-red-300 bg-red-50' : 'border-slate-200 bg-white' }}">
                </div>
            </div>
        </div>

        {{-- Job Info --}}
        <div class="bg-white rounded-xl border border-slate-200 p-6 mb-5">
            <h2 class="text-sm font-semibold text-slate-700 mb-4 pb-3 border-b border-slate-100">Job Details</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Department</label>
                    <input type="text" name="department"
                        value="{{ old('department', $user->employee->department ?? '') }}"
                        placeholder="e.g. Engineering"
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Position</label>
                    <input type="text" name="position"
                        value="{{ old('position', $user->employee->position ?? '') }}"
                        placeholder="e.g. Senior Developer"
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Salary (BDT)</label>
                    <input type="number" name="salary" step="0.01" min="0"
                        value="{{ old('salary', $user->employee->salary ?? '') }}"
                        placeholder="0.00"
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Joining Date</label>
                    <input type="date" name="joining_date"
                        value="{{ old('joining_date', $user->employee?->joining_date?->format('Y-m-d') ?? '') }}"
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Status</label>
                    <select name="status"
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                        <option value="active" {{ old('status', $user->employee->status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $user->employee->status ?? 'active') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Contact --}}
        <div class="bg-white rounded-xl border border-slate-200 p-6 mb-6">
            <h2 class="text-sm font-semibold text-slate-700 mb-4 pb-3 border-b border-slate-100">Contact Details</h2>
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Phone</label>
                    <input type="text" name="phone"
                        value="{{ old('phone', $user->employee->phone ?? '') }}"
                        placeholder="+880 1XXX-XXXXXX"
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Address</label>
                    <textarea name="address" rows="2"
                        placeholder="Full address..."
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all resize-none">{{ old('address', $user->employee->address ?? '') }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit"
                class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium
                       rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Save Changes
            </button>
            <a href="{{ route('admin.employees.show', $user) }}"
                class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-medium rounded-lg transition-colors">
                Cancel
            </a>
        </div>
    </form>
</div>

@endsection