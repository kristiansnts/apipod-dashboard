@extends('layouts.guest')

@section('title', 'Authorize Device — API Pod')

@section('content')
<div class="w-full max-w-md bg-white dark:bg-[#161615] p-8 rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]">
    <div class="text-center mb-6">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-[#F53003]/10 mb-4">
            <i class="fas fa-terminal text-2xl text-[#F53003]"></i>
        </div>
        <h2 class="text-2xl font-bold text-[#1b1b18] dark:text-[#EDEDEC]">Authorize Device</h2>
        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mt-2">Enter the code shown in your terminal to connect <strong>apipod-cli</strong> to your account.</p>
    </div>

    @if (session('success'))
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 px-4 py-3 rounded-sm mb-4">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 px-4 py-3 rounded-sm mb-4">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    @auth
        <form method="POST" action="{{ route('device.authorize') }}">
            @csrf
            <div class="mb-6">
                <label for="user_code" class="block text-[#1b1b18] dark:text-[#EDEDEC] text-sm font-bold mb-2">Device Code</label>
                <input type="text" name="user_code" id="user_code" value="{{ old('user_code') }}"
                       placeholder="XXXX-XXXX"
                       autocomplete="off"
                       autofocus
                       class="shadow appearance-none border border-[#e3e3e0] dark:border-[#3E3E3A] rounded w-full py-3 px-4 text-center text-2xl font-mono tracking-[0.3em] text-[#1b1b18] dark:text-[#EDEDEC] bg-white dark:bg-[#1b1b18] leading-tight focus:outline-none focus:shadow-outline focus:border-[#F53003] uppercase"
                       maxlength="9"
                       required>
            </div>

            <button type="submit" class="w-full bg-[#F53003] hover:bg-[#d92902] text-white font-bold py-3 px-4 rounded focus:outline-none focus:shadow-outline transition-colors">
                <i class="fas fa-check mr-2"></i>Authorize
            </button>
        </form>

        <div class="mt-4 text-center">
            <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">
                Logged in as <strong>{{ Auth::user()->name }}</strong>
            </p>
        </div>
    @else
        <div class="text-center">
            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mb-4">You need to log in first to authorize a device.</p>
            <a href="{{ route('login') }}?redirect={{ urlencode(route('device.show')) }}" class="w-full inline-block bg-[#1b1b18] dark:bg-[#eeeeec] hover:bg-black dark:hover:bg-white text-white dark:text-[#1C1C1A] font-bold py-2 px-4 rounded-sm transition-colors text-sm text-center">
                Log In
            </a>
            <div class="mt-3">
                <a href="{{ route('register') }}" class="text-sm text-[#F53003] hover:text-[#d92902] hover:underline">Don't have an account? Register</a>
            </div>
        </div>
    @endauth
</div>
@endsection
