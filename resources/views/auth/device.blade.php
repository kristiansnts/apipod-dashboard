@extends('layouts.guest')

@section('title', 'Authorize Device')

@section('content')
    <div class="w-full max-w-md light-card p-8 lg:p-10 rounded-[32px] animate-fade-in text-center">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-blue-50 mb-8 border border-blue-100">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-blue-600" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
        </div>

        <h2 class="text-3xl font-extrabold text-gray-900 mb-4">Authorize CLI</h2>
        <p class="text-gray-500 mb-10 leading-relaxed font-medium">
            Enter the authorization code from your terminal to connect <span
                class="bg-blue-50 text-blue-600 px-2 py-0.5 rounded font-mono text-sm">apipod-cli</span> to your
            account.
        </p>

        @if (session('success'))
            <div
                class="bg-emerald-50 border border-emerald-100 text-emerald-600 px-4 py-3 rounded-2xl mb-8 flex items-center justify-center gap-2 text-sm font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-50 border border-red-100 text-red-600 px-4 py-3 rounded-2xl mb-8 text-sm font-medium">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        @auth
            <form method="POST" action="{{ route('device.authorize') }}" class="space-y-8">
                @csrf
                <div>
                    <label for="user_code"
                        class="block text-gray-400 text-[11px] font-bold mb-3 uppercase tracking-[0.2em]">Device
                        Code</label>
                    <input type="text" name="user_code" id="user_code" value="{{ old('user_code') }}" placeholder="XXXX-XXXX"
                        autocomplete="off" autofocus
                        class="w-full bg-gray-50 border border-gray-100 rounded-2xl py-5 text-center text-3xl font-bold font-mono tracking-[0.2em] text-gray-900 leading-tight focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all uppercase placeholder:text-gray-200"
                        maxlength="9" required>
                </div>

                <button type="submit"
                    class="w-full btn-blue py-4 rounded-2xl text-white font-extrabold text-lg shadow-lg shadow-blue-600/10 transition-all flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Authorize Device
                </button>
            </form>

            <div class="mt-8 pt-8 border-t border-gray-50">
                <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">
                    Logged in as <span class="text-blue-600">{{ Auth::user()->name }}</span>
                </p>
            </div>
        @else
            <div class="space-y-6">
                <p class="text-gray-500 font-medium leading-relaxed">Please sign in to your Apipod account to authorize this
                    device.</p>
                <a href="{{ route('login') }}?redirect={{ urlencode(route('device.show')) }}"
                    class="w-full btn-blue inline-flex items-center justify-center py-4 rounded-2xl text-white font-extrabold text-lg transition-all shadow-lg shadow-blue-600/10">
                    Sign In to Continue
                </a>
                <p class="text-sm text-gray-500 font-medium">
                    Don't have an account? <a href="{{ route('register') }}"
                        class="text-blue-600 font-extrabold hover:underline ml-1">Register now</a>
                </p>
            </div>
        @endauth
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