<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'API Pod') | Smart AI Proxy</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --primary: #2563eb;
            --bg: #ffffff;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg);
            color: #111827;
        }

        .light-card {
            background: #ffffff;
            border: 1px solid #f3f4f6;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05);
        }

        .hero-bg-gradient {
            background: radial-gradient(circle at 50% -20%, rgba(37, 99, 235, 0.05) 0%, rgba(255, 255, 255, 0) 70%);
        }

        .btn-blue {
            background-color: var(--primary);
            color: white;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
            transition: all 0.2s ease;
        }

        .btn-blue:hover {
            background-color: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 6px 15px rgba(37, 99, 235, 0.2);
        }
    </style>
</head>

<body class="antialiased overflow-x-hidden min-h-screen flex items-center justify-center p-6 bg-gray-50/50">
    <!-- Background Elements -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden -z-10 hero-bg-gradient"></div>

    <div class="w-full flex flex-col items-center">
        <!-- Logo area -->
        <a href="{{ url('/') }}" class="flex items-center gap-2 mb-10 transform hover:scale-105 transition-transform">
            <div
                class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center font-bold text-xl text-white shadow-sm">
                A</div>
            <span class="text-2xl font-extrabold tracking-tight text-gray-900">Apipod</span>
        </a>

        @yield('content')
    </div>
</body>

</html>