@extends('layouts.app')
@section('title', 'My Profile — EMS')
@section('page-title', 'My Profile')
@section('page-subtitle', 'View and update your information')

@section('content')

<div class="max-w-2xl">


    {{-- Profile Header --}}
    <div class="bg-white rounded-xl border border-slate-200 p-6 mb-5">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0">
                <span class="text-2xl font-semibold text-indigo-600">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </span>
            </div>
            <div>
                <h2 class="text-xl font-semibold text-slate-800">{{ $user->name }}</h2>
                <p class="text-sm text-slate-500">{{ $user->email }}</p>
                <div class="mt-2 flex items-center gap-2">
                    <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-emerald-50 text-emerald-600">
                        {{ $user->employee->department ?? 'No department' }}
                    </span>
                    @if($user->employee?->position)
                        <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-slate-100 text-slate-600">
                            {{ $user->employee->position }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Job Information --}}
    <div class="bg-white rounded-xl border border-slate-200 p-6 mb-5">
        <h2 class="text-sm font-semibold text-slate-700 mb-4 pb-3 border-b border-slate-100 flex items-center gap-2">
            Job Information
            <span class="px-2 py-0.5 text-xs bg-slate-100 text-slate-500 rounded-full font-normal">Read only</span>
        </h2>
        <div class="grid grid-cols-2 gap-x-8 gap-y-4">
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
            <div>
                <p class="text-xs text-slate-400 mb-0.5">Status</p>
                @php $status = $user->employee->status ?? 'active'; @endphp
                <span class="inline-flex items-center gap-1.5 text-sm font-medium
                    {{ $status === 'active' ? 'text-emerald-600' : 'text-slate-500' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $status === 'active' ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                    {{ ucfirst($status) }}
                </span>
            </div>
        </div>
    </div>

    {{-- Contact Details — Display Mode --}}
    <div class="bg-white rounded-xl border border-slate-200 p-6" id="contact-display">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
            <h2 class="text-sm font-semibold text-slate-700">Contact Details</h2>
            <button onclick="showEditForm()"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium
                       text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Update Profile
            </button>
        </div>

        <div class="grid grid-cols-2 gap-x-8 gap-y-4">
            <div>
                <p class="text-xs text-slate-400 mb-0.5">Phone Number</p>
                <p class="text-sm font-medium text-slate-700">
                    {{ $user->employee->phone ?? '—' }}
                </p>
            </div>
            <div>
                <p class="text-xs text-slate-400 mb-0.5">Address</p>
                <p class="text-sm font-medium text-slate-700">
                    {{ $user->employee->address ?? '—' }}
                </p>
            </div>
        </div>
    </div>

    {{-- Contact Details — Edit Form (default hidden) --}}
    <div class="bg-white rounded-xl border border-indigo-200 p-6" id="contact-form" style="display:none">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
            <h2 class="text-sm font-semibold text-slate-700 flex items-center gap-2">
                Contact Details
                <span class="px-2 py-0.5 text-xs bg-indigo-50 text-indigo-600 rounded-full font-normal">Editing</span>
            </h2>
            <button type="button" onclick="hideEditForm()"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium
                       text-slate-500 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Cancel
            </button>
        </div>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg px-4 py-3 mb-4 space-y-1">
                @foreach($errors->all() as $error)
                    <p class="text-red-600 text-sm">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('employee.profile.update') }}">
            @csrf @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Phone Number</label>
                    <input type="text" name="phone"
                        value="{{ old('phone', $user->employee->phone ?? '') }}"
                        placeholder="+880 1XXX-XXXXXX"
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-200 bg-white text-sm
                               focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Address</label>
                    <textarea name="address" rows="3"
                        placeholder="Your current address..."
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-200 bg-white text-sm
                               focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                               transition-all resize-none">{{ old('address', $user->employee->address ?? '') }}</textarea>
                </div>
                <div class="flex items-center gap-3 pt-2">
                    <button type="submit"
                        class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium
                               rounded-lg transition-colors focus:outline-none focus:ring-2
                               focus:ring-indigo-500 focus:ring-offset-2">
                        Save Changes
                    </button>
                    <button type="button" onclick="hideEditForm()"
                        class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm
                               font-medium rounded-lg transition-colors">
                        Cancel
                    </button>
                </div>
            </div>
        </form>
    </div>

</div>

<script>
function showEditForm() {
    document.getElementById('contact-display').style.display = 'none';
    document.getElementById('contact-form').style.display = 'block';
}

function hideEditForm() {
    document.getElementById('contact-display').style.display = 'block';
    document.getElementById('contact-form').style.display = 'none';
}

// Validation error হলে form automatically দেখাবে
@if($errors->any())
    showEditForm();
@endif
</script>

@endsection