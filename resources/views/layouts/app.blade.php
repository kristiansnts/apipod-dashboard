<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') | Apipod</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=IBM+Plex+Mono:wght@400;500;600&display=swap"
        rel="stylesheet">

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --primary: #2563eb;
            --primary-light: #eff6ff;
            --bg-main: #f9fafb;
            --sidebar-bg: #ffffff;
            --border-color: #e5e7eb;
            --text-main: #111827;
            --text-muted: #6b7280;
        }

        body {
            font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif;
            background-color: var(--bg-main);
            color: var(--text-main);
            -webkit-font-smoothing: antialiased;
        }

        .mono {
            font-family: 'IBM Plex Mono', ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        }

        .sidebar {
            width: 255px;
            background-color: var(--sidebar-bg);
            border-right: 1px solid var(--border-color);
        }

        .nav-link {
            display: flex;
            items-center: center;
            padding: 0.5rem 0.75rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
            transition: all 0.2s ease;
        }

        .nav-link:hover {
            background-color: #f3f4f6;
            color: #111827;
        }

        .nav-link.active {
            background-color: var(--primary-light);
            color: var(--primary);
        }

        .card {
            background-color: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }

        .tab-button {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            font-weight: 600;
            border-radius: 0.375rem;
            transition: all 0.2s;
        }

        .tab-button.active {
            background-color: #ffffff;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            border: 1px solid var(--border-color);
            color: #111827;
        }

        .tab-button:not(.active) {
            color: var(--text-muted);
        }

        .tab-button:not(.active):hover {
            background-color: rgba(255, 255, 255, 0.5);
            color: #111827;
        }

        .btn-primary {
            background-color: var(--primary);
            color: #ffffff;
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            transition: background-color 0.2s;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
        }

        .btn-secondary {
            background-color: #ffffff;
            border: 1px solid var(--border-color);
            color: #374151;
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            transition: background-color 0.2s;
        }

        .btn-secondary:hover {
            background-color: #f9fafb;
        }

        /* Sidebar scrollbar hide */
        .sidebar-content::-webkit-scrollbar {
            display: none;
        }

        .sidebar-content {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

<body class="antialiased min-h-screen flex" x-data="{ sidebarOpen: false }">
    <!-- Sidebar -->
    <aside
        class="sidebar fixed inset-y-0 left-0 z-50 flex flex-col transition-transform duration-300 ease-in-out lg:translate-x-0"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
        <div class="p-6">
            <a href="{{ url('/') }}" class="flex items-center gap-2.5">
                <div
                    class="w-8 h-8 bg-blue-600 rounded flex items-center justify-center font-bold text-white shadow-sm">
                    A</div>
                <span class="text-lg font-bold tracking-tight text-gray-900 mono uppercase">Apipod</span>
            </a>
        </div>

        <div class="flex-1 overflow-y-auto px-4 space-y-8 sidebar-content">
            <!-- Navigation Group -->
            <div>
                <h3 class="px-3 text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-4">Core</h3>
                <nav class="space-y-1">
                    <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Dashboard
                    </a>
                    <a href="{{ route('dashboard.usage') }}"
                        class="nav-link {{ request()->routeIs('dashboard.usage') ? 'active' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Usage & Quotas
                    </a>
                    <a href="{{ route('shop.index') }}"
                        class="nav-link {{ request()->routeIs('shop.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        Token Shop
                    </a>
                </nav>
            </div>

            <!-- Learning/Docs Group -->
            <div>
                <h3 class="px-3 text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-4">Learning</h3>
                <nav class="space-y-1">
                    <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.246.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        Quick Start
                    </a>
                    <a href="{{ route('dashboard.models') }}"
                        class="nav-link {{ request()->routeIs('dashboard.models') ? 'active' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                        </svg>
                        Models
                    </a>
                </nav>
            </div>

            <!-- Developer Group -->
            <div>
                <h3 class="px-3 text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-4">Developer</h3>
                <nav class="space-y-1">
                    <a href="{{ route('dashboard.api-keys') }}"
                        class="nav-link {{ request()->routeIs('dashboard.api-keys') ? 'active' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                        API Keys
                    </a>
                    <a href="{{ route('dashboard.analytics') }}"
                        class="nav-link {{ request()->routeIs('dashboard.analytics') ? 'active' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Analytics
                    </a>
                </nav>
            </div>
        </div>

        <!-- Sidebar Footer/User -->
        <div class="p-4 border-t border-gray-100">
            <div class="flex items-center gap-3 px-3 py-2">
                <div class="w-8 h-8 bg-blue-600 rounded flex items-center justify-center font-bold text-white text-xs">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <div class="flex-1 truncate">
                    <p class="text-[13px] font-bold text-gray-900 truncate">{{ auth()->user()->name }}</p>
                    <p class="text-[11px] text-gray-500 font-medium truncate italic">Free Plan</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-gray-400 hover:text-red-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Content -->
    <div class="flex-1 flex flex-col min-h-screen lg:ml-[255px]">
        <!-- Top Bar (Mobile Only Logo + Toggle) -->
        <header
            class="lg:hidden h-16 bg-white border-b border-gray-200 px-6 flex items-center justify-between sticky top-0 z-40">
            <div class="flex items-center gap-2">
                <div
                    class="w-6 h-6 bg-blue-600 rounded flex items-center justify-center font-bold text-white text-[10px]">
                    A</div>
                <span class="font-bold tracking-tight text-gray-900 mono uppercase">Apipod</span>
            </div>
            <button @click="sidebarOpen = !sidebarOpen" class="p-2 text-gray-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </header>

        <!-- Page Header -->
        <div class="px-6 py-8 lg:px-10 lg:py-12 border-b border-gray-100 bg-white">
            <div class="max-w-6xl mx-auto">
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight mb-2">@yield('title', 'Dashboard')</h1>
                <p class="text-[15px] font-medium text-gray-500">
                    @yield('subtitle', 'Manage your AI infrastructure and API orchestrations.')
                </p>
            </div>
        </div>

        <!-- Main Area -->
        <main class="flex-1 p-6 lg:p-10">
            <div class="max-w-6xl mx-auto">
                @yield('content')
            </div>
        </main>

        <!-- Footer -->
        <footer
            class="px-6 py-8 border-t border-gray-100 text-[11px] font-bold text-gray-400 uppercase tracking-widest text-center">
            &copy; {{ date('Y') }} Apipod Inc. &middot; Dashboard v1.0
        </footer>
    </div>

    <!-- Mobile Overlay -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false"
        class="fixed inset-0 bg-black/40 backdrop-blur-sm z-40 lg:hidden"
        x-transition:enter="transition opacity duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"></div>
</body>

</html>