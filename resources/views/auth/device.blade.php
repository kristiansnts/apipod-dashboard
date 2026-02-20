@extends('layouts.guest')

@section('title', 'Authorize Device')

@section('content')
    <div class="w-full max-w-md glass p-8 lg:p-10 rounded-[32px] shadow-2xl animate-fade-in text-center">
        <div
            class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-indigo-500/20 mb-8 border border-indigo-500/20">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-indigo-400" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
        </div>

        <h2 class="text-3xl font-extrabold text-white mb-4">Authorize CLI</h2>
        <p class="text-gray-400 mb-10 leading-relaxed">
            Enter the authorization code from your terminal to connect <span
                class="bg-indigo-500/20 text-indigo-300 px-2 py-0.5 rounded font-mono text-sm">apipod-cli</span> to your
            account.
        </p>

        @if (session('success'))
            <div
                class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-4 py-3 rounded-2xl mb-8 flex items-center justify-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-500/10 border border-red-500/20 text-red-500 px-4 py-3 rounded-2xl mb-8">
                @foreach ($errors->all() as $error)
                    <p class="font-medium">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        @auth
            <form method="POST" action="{{ route('device.authorize') }}" class="space-y-8">
                @csrf
                <div>
                    <label for="user_code" class="block text-gray-400 text-sm font-medium mb-3 uppercase tracking-widest">Device
                        Code</label>
                    <input type="text" name="user_code" id="user_code" value="{{ old('user_code') }}" placeholder="XXXX-XXXX"
                        autocomplete="off" autofocus
                        class="w-full bg-white/5 border border-white/10 rounded-2xl py-5 text-center text-3xl font-mono tracking-[0.2em] text-white leading-tight focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all uppercase"
                        maxlength="9" required>
                </div>

                <button type="submit"
                    class="w-full btn-primary py-4 rounded-2xl text-white font-bold text-lg shadow-lg shadow-indigo-500/20 transition-all flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Authorize Device
                </button>
            </form>

            <div class="mt-8 pt-8 border-t border-white/5">
                <p class="text-sm text-gray-500">
                    Logged in as <span class="text-indigo-400 font-semibold">{{ Auth::user()->name }}</span>
                </p>
            </div>
        @else
            <div class="space-y-6">
                <p class="text-gray-400">Please sign in to your Apipod account to authorize this device.</p>
                <a href="{{ route('login') }}?redirect={{ urlencode(route('device.show')) }}"
                    class="w-full btn-primary inline-flex items-center justify-center py-4 rounded-2xl text-white font-bold text-lg transition-all">
                    Sign In to Continue
                </a>
                <p class="text-sm text-gray-500 pb-2">
                    Don't have an account? <a href="{{ route('register') }}"
                        class="text-indigo-400 hover:text-indigo-300 font-semibold ml-1">Register now</a>
                </p>
            </div>
        @endauth
    </div>

    <style>
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(20px);
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