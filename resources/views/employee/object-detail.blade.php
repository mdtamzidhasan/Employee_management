@extends('layouts.app')
@section('title', $objectName . ' — EMS')
@section('page-title', $objectName)
@section('page-subtitle', ucfirst($objectMeta['object_type'] ?? 'module'))

@section('content')

<div class="max-w-4xl">

    {{-- Operation Buttons --}}
    <div class="bg-white rounded-xl border border-slate-200 p-5 mb-5">
        <p class="text-xs text-slate-400 uppercase font-semibold tracking-wider mb-4">
            Available Actions
        </p>
        <div class="flex flex-wrap gap-3">

            @foreach($operations as $operation)
                @php
                    $btnConfig = match($operation) {
                        'view'   => ['label' => 'View',   'color' => 'bg-indigo-600 hover:bg-indigo-700',
                                     'icon'  => 'M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z'],
                        'create' => ['label' => 'Create', 'color' => 'bg-emerald-600 hover:bg-emerald-700',
                                     'icon'  => 'M12 4v16m8-8H4'],
                        'edit'   => ['label' => 'Edit',   'color' => 'bg-amber-500 hover:bg-amber-600',
                                     'icon'  => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'],
                        'delete' => ['label' => 'Delete', 'color' => 'bg-red-500 hover:bg-red-600',
                                     'icon'  => 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16'],
                        'export' => ['label' => 'Export', 'color' => 'bg-slate-700 hover:bg-slate-800',
                                     'icon'  => 'M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                        default  => ['label' => ucfirst($operation), 'color' => 'bg-slate-500 hover:bg-slate-600',
                                     'icon'  => 'M13 10V3L4 14h7v7l9-11h-7z'],
                    };
                @endphp

                <button
                    onclick="handleOperation('{{ $operation }}', '{{ $objectSlug }}')"
                    data-operation="{{ $operation }}"
                    class="inline-flex items-center gap-2 px-5 py-2.5
                           {{ $btnConfig['color'] }} text-white text-sm font-medium
                           rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="{{ $btnConfig['icon'] }}"/>
                    </svg>
                    {{ $btnConfig['label'] }}
                </button>
            @endforeach

        </div>
    </div>

    {{-- Content Area — Operation এ Click করলে এখানে দেখাবে --}}
    <div id="content-area">

        {{-- Default: View operation এর content দেখাও --}}
        @if(in_array('view', $operations))
            @include('employee.partials.object-content', [
                'operation' => 'view',
                'viewData'  => $viewData,
                'objectSlug' => $objectSlug,
                'operations' => $operations,
            ])
        @else
            <div class="bg-white rounded-xl border border-slate-200 p-8 text-center text-slate-400">
                <p>Select an action above to get started.</p>
            </div>
        @endif

    </div>

</div>

<script>
function handleOperation(operation, objectSlug) {
    // Active button highlight করো
    document.querySelectorAll('[data-operation]').forEach(btn => {
        btn.classList.remove('ring-2', 'ring-offset-2', 'ring-white');
    });
    event.currentTarget.classList.add('ring-2', 'ring-offset-2', 'ring-white');

    // Content area update করো
    const contentArea = document.getElementById('content-area');
    contentArea.innerHTML = '<div class="bg-white rounded-xl border border-slate-200 p-8 text-center"><div class="animate-spin w-6 h-6 border-2 border-indigo-500 border-t-transparent rounded-full mx-auto"></div></div>';

    // AJAX দিয়ে operation content আনো
    fetch(`/employee/object/${objectSlug}/action?operation=${operation}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'text/html',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        }
    })
    .then(response => response.text())
    .then(html => {
        contentArea.innerHTML = html;
    })
    .catch(error => {
        contentArea.innerHTML = `<div class="bg-red-50 border border-red-200 rounded-xl p-5 text-red-600 text-sm">Failed to load content. Please try again.</div>`;
    });
}
</script>

@endsection