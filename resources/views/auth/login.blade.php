@extends('layouts.guest')

@section('title', 'Login')

@section('content')
    <div class="w-full max-w-md light-card p-8 lg:p-10 rounded-[32px] animate-fade-in">
        <div class="text-center mb-10">
            <h2 class="text-3xl font-extrabold text-gray-900 mb-2">Welcome Back</h2>
            <p class="text-gray-500 font-medium">Please enter your details to sign in</p>
        </div>

        <div class="mb-8">
            <a href="{{ route('social.redirect', 'github') }}"
                class="flex items-center justify-center w-full bg-white hover:bg-gray-50 text-gray-700 border border-gray-200 font-bold py-3.5 px-4 rounded-2xl transition-all duration-300 shadow-sm">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12" />
                </svg>
                Continue with GitHub
            </a>
        </div>

        <div class="relative flex items-center mb-8">
            <div class="flex-grow border-t border-gray-100"></div>
            <span class="flex-shrink mx-4 text-gray-400 text-[13px] font-bold uppercase tracking-widest">or email</span>
            <div class="flex-grow border-t border-gray-100"></div>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border border-red-100 text-red-600 px-4 py-3 rounded-2xl mb-6 text-sm font-medium">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            <div>
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email Address</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                    class="w-full bg-gray-50 border border-gray-100 rounded-2xl py-3.5 px-5 text-gray-900 leading-tight focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all font-medium placeholder:text-gray-300"
                    placeholder="name@company.com">
            </div>

            <div>
                <div class="flex items-center justify-between mb-2">
                    <label for="password" class="text-gray-700 text-sm font-bold">Password</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                            class="text-xs text-blue-600 font-bold hover:underline">Forgot?</a>
                    @endif
                </div>
                <input type="password" name="password" id="password" required
                    class="w-full bg-gray-50 border border-gray-100 rounded-2xl py-3.5 px-5 text-gray-900 leading-tight focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all font-medium placeholder:text-gray-300"
                    placeholder="••••••••">
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="remember" id="remember"
                    class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <label for="remember" class="ml-2 text-sm text-gray-500 font-medium">Remember me</label>
            </div>

            <button type="submit"
                class="w-full btn-blue py-4 rounded-2xl text-white font-extrabold text-lg shadow-lg shadow-blue-600/10 transition-all">
                Sign In
            </button>

            <p class="text-center text-gray-500 text-sm font-medium">
                New to Apipod?
                <a href="{{ route('register') }}" class="text-blue-600 font-extrabold hover:underline ml-1">Create
                    account</a>
            </p>
        </form>
    </div>

    <style>
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fade-in 0.6s ease-out forwards;
        }
    </style>
@endsection