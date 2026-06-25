<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Employee Management')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'DM Sans', sans-serif; }
        .sidebar-link {
            display: flex; align-items: center; gap: 0.75rem;
            padding: 0.625rem 1rem; border-radius: 0.5rem;
            font-size: 0.875rem; font-weight: 500; color: #94a3b8;
            transition: all 150ms; text-decoration: none;
            width: 100%; background: transparent; border: none;
            cursor: pointer; text-align: left;
        }
        .sidebar-link:hover { color: #fff; background-color: rgba(255,255,255,0.1); }
        .sidebar-link.active { color: #fff; background-color: rgba(255,255,255,0.15); }
        .sidebar-link.danger { color: #f87171; }
        .sidebar-link.danger:hover { color: #fca5a5; background-color: rgba(239,68,68,0.1); }
        .user-card { padding: 0.75rem 1rem; border-radius: 0.5rem; background-color: rgba(255,255,255,0.05); margin-bottom: 0.5rem; }
        .sidebar-divider-top { border-top: 1px solid rgba(255,255,255,0.1); }
        .sidebar-divider-bottom { border-bottom: 1px solid rgba(255,255,255,0.1); }
        .badge-admin { background-color: rgba(99,102,241,0.2); color: #a5b4fc; }
        .badge-employee { background-color: rgba(16,185,129,0.2); color: #6ee7b7; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen">
<div class="flex min-h-screen">

    <aside class="w-64 bg-slate-900 flex flex-col fixed h-full z-30">
        <div class="px-6 py-5 sidebar-divider-bottom">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-indigo-500 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-white font-semibold text-sm leading-none">EMS</p>
                    <p class="text-slate-400 text-xs mt-1">Employee Manager</p>
                </div>
            </div>
        </div>

        <nav class="flex-1 px-3 py-4 space-y-1">
            @if(auth()->user()->isAdmin())
                <p class="px-4 py-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">Admin</p>
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    Dashboard
                </a>
                <a href="{{ route('admin.employees.index') }}" class="sidebar-link {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    Employees
                </a>
                <a href="{{ route('admin.password.config') }}"
                    class="sidebar-link {{ request()->routeIs('admin.password.config') ? 'active' : '' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                     Password Config
                </a>
                <a href="{{ route('admin.security.logs') }}"
                    class="sidebar-link {{ request()->routeIs('admin.security.logs') ? 'active' : '' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                        Security Logs
                </a>
                <a href="{{ config('app.url') }}/reports/"
                    class="sidebar-link">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                        Reports
                </a>
            @endif
            @if(auth()->user()->isEmployee())
                @php
                    $sidebarObjects  = [];
                    $isAdminUser     = false;

                    try {
                        $rbacSvc = app(\App\Services\RbacApiService::class);
                        $sidebarObjects = $rbacSvc->getUserObjects(auth()->id());
                    } catch (\Exception $e) {
                        $sidebarObjects = [];
                    }
                @endphp

                <p class="sidebar-section-label">EMPLOYEE</p>

                {{-- My Profile — সবসময় --}}
                <a href="{{ route('employee.profile') }}"
                    class="sidebar-link {{ request()->routeIs('employee.profile') ? 'active' : '' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    My Profile
                </a>

        {{-- Dynamic Objects from RBAC ─────────────────────────────
         যতগুলো object permission আছে সবগুলো দেখাবে
         কোনো hardcode নেই — RBAC Service থেকে dynamically আসে
        --}}
                @foreach($sidebarObjects as $obj)
                    <a href="{{ route('employee.object.show', $obj['slug']) }}"
                    class="sidebar-link {{ request()->is('employee/object/' . $obj['slug'] . '*') ? 'active' : '' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        {{-- Object type অনুযায়ী icon --}}
                        @if($obj['object_type'] === 'personal')
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z"/>
                        @elseif($obj['object_type'] === 'department')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        @elseif($obj['object_type'] === 'system')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        @else
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        @endif
                        </svg>
                        {{ $obj['name'] }}
                    </a>
                @endforeach

            @endif
            </nav>

            <div class="px-3 py-4 sidebar-divider-top">
                <div class="user-card">
                    <p class="text-white text-sm font-medium truncate">{{ auth()->user()->name }}</p>
                    <p class="text-slate-400 text-xs truncate mt-0.5">{{ auth()->user()->email }}</p>
                    <span class="mt-2 inline-block px-2 py-0.5 text-xs font-medium rounded-full {{ auth()->user()->isAdmin() ? 'badge-admin' : 'badge-employee' }}">
                        {{ ucfirst(auth()->user()->role) }}
                   </span>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                   @csrf
                   <button type="submit" class="sidebar-link danger">
                       <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                       Logout
                   </button>
                </form>
            </div>
        </aside>

    <main class="flex-1 ml-64">
        <header class="bg-white border-b border-slate-200 px-8 py-4 sticky top-0 z-20">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-lg font-semibold text-slate-800">@yield('page-title', 'Dashboard')</h1>
                    <p class="text-xs text-slate-400 mt-0.5">@yield('page-subtitle', '')</p>
                </div>
                <div class="flex items-center gap-2 text-sm text-slate-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    {{ now()->format('D, d M Y') }}
                </div>
            </div>
        </header>

        <div class="px-8 pt-6">
            @if(session('success'))
                <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg mb-4 text-sm">
                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm">
                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                    {{ session('error') }}
                </div>
            @endif
        </div>

        <div class="px-8 py-4 pb-10">
            @yield('content')
        </div>
    </main>

</div>
</body>
</html>