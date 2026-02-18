@extends('layouts.guest')

@section('title', 'Login')

@section('content')
<div class="w-full max-w-md bg-white dark:bg-[#161615] p-8 rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]">
    <h2 class="text-2xl font-bold mb-6 text-center text-[#1b1b18] dark:text-[#EDEDEC]">Login</h2>

    <div class="mb-6">
        <a href="{{ route('social.redirect', 'github') }}" class="flex items-center justify-center w-full bg-[#1b1b18] dark:bg-[#eeeeec] hover:bg-black dark:hover:bg-white text-white dark:text-[#1C1C1A] border border-black dark:border-[#eeeeec] font-bold py-2 px-4 rounded-sm transition-colors text-sm">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12"/></svg>
            Continue with GitHub
        </a>
    </div>

    <div class="relative flex items-center mb-6">
        <div class="flex-grow border-t border-[#e3e3e0] dark:border-[#3E3E3A]"></div>
        <span class="flex-shrink mx-4 text-[#706f6c] dark:text-[#A1A09A] text-sm">or</span>
        <div class="flex-grow border-t border-[#e3e3e0] dark:border-[#3E3E3A]"></div>
    </div>
    
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf
        
        <div class="mb-4">
            <label for="email" class="block text-[#1b1b18] dark:text-[#EDEDEC] text-sm font-bold mb-2">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                   class="shadow appearance-none border border-[#e3e3e0] dark:border-[#3E3E3A] rounded w-full py-2 px-3 text-[#1b1b18] dark:text-[#EDEDEC] bg-white dark:bg-[#1b1b18] leading-tight focus:outline-none focus:shadow-outline focus:border-[#1b1b18] dark:focus:border-[#EDEDEC]">
        </div>

        <div class="mb-4">
            <label for="password" class="block text-[#1b1b18] dark:text-[#EDEDEC] text-sm font-bold mb-2">Password</label>
            <input type="password" name="password" id="password" required
                   class="shadow appearance-none border border-[#e3e3e0] dark:border-[#3E3E3A] rounded w-full py-2 px-3 text-[#1b1b18] dark:text-[#EDEDEC] bg-white dark:bg-[#1b1b18] leading-tight focus:outline-none focus:shadow-outline focus:border-[#1b1b18] dark:focus:border-[#EDEDEC]">
        </div>

        <div class="mb-4">
            <label class="flex items-center">
                <input type="checkbox" name="remember" class="mr-2 rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">Remember me</span>
            </label>
        </div>

        <button type="submit" class="w-full bg-[#F53003] hover:bg-[#d92902] text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition-colors">
            Login
        </button>

        <div class="mt-4 text-center">
            <a href="{{ route('register') }}" class="text-sm text-[#F53003] hover:text-[#d92902] hover:underline">Don't have an account? Register</a>
        </div>
    </form>
</div>
@endsection
