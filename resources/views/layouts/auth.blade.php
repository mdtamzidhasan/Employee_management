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
        .input-field {
            @apply w-full px-4 py-2.5 rounded-lg border border-slate-200 bg-white text-sm text-slate-800
                   placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                   transition-all duration-150;
        }
        .btn-primary {
            @apply w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm
                   px-4 py-2.5 rounded-lg transition-all duration-150 focus:outline-none
                   focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2;
        }
    </style>
</head>
<body class="min-h-screen bg-slate-50 flex">

    {{-- Left panel --}}
    <div class="hidden lg:flex lg:w-1/2 bg-slate-900 relative overflow-hidden flex-col justify-between p-12">
        {{-- Background decoration --}}
        <div class="absolute inset-0">
            <div class="absolute top-0 right-0 w-96 h-96 bg-indigo-600/20 rounded-full -translate-y-1/2 translate-x-1/2 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-96 h-96 bg-indigo-800/20 rounded-full translate-y-1/2 -translate-x-1/2 blur-3xl"></div>
        </div>

        <div class="relative">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-500 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <span class="text-white font-semibold text-lg">EMS</span>
            </div>
        </div>

        <div class="relative">
            <h2 class="text-4xl font-semibold text-white leading-tight mb-4">
                Manage your team,<br>
                <span class="text-indigo-400">effortlessly.</span>
            </h2>
            <p class="text-slate-400 text-base leading-relaxed">
                A complete employee management system with role-based access, profile management, and more.
            </p>

            <div class="mt-10 flex flex-col gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <p class="text-slate-300 text-sm">Secure role-based authentication</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <p class="text-slate-300 text-sm">Full employee profile management</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <p class="text-slate-300 text-sm">Admin dashboard with analytics</p>
                </div>
            </div>
        </div>

        <p class="relative text-slate-600 text-xs">© {{ date('Y') }} Employee Management System</p>
    </div>

    {{-- Right panel (form) --}}
    <div class="flex-1 flex items-center justify-center p-8">
        <div class="w-full max-w-md">
            @yield('content')
        </div>
    </div>

</body>
</html>