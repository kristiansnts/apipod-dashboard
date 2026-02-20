@extends('layouts.guest')

@section('title', 'Device Authorized')

@section('content')
    <div class="w-full max-w-md glass p-10 rounded-[40px] shadow-2xl animate-fade-in text-center">
        <div class="relative mb-10">
            <div class="absolute inset-0 bg-emerald-500/20 blur-2xl rounded-full"></div>
            <div
                class="relative inline-flex items-center justify-center w-24 h-24 rounded-full bg-emerald-500/10 border border-emerald-500/20">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-emerald-400" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <h2 class="text-3xl font-extrabold text-white mb-4">Device Authorized!</h2>
        <p class="text-gray-400 mb-10 leading-relaxed font-medium">
            Your terminal is now securely connected to the Apipod network. You can return to your CLI and start
            orchestrating.
        </p>

        <div class="bg-indigo-500/5 border border-white/5 rounded-3xl p-8 mb-8">
            <p class="text-sm text-gray-500 mb-4 font-semibold uppercase tracking-widest">
                Closing in <span id="countdown" class="text-white">5</span> seconds
            </p>
            <div class="w-full bg-white/5 rounded-full h-1 overflow-hidden">
                <div id="progress" class="bg-indigo-500 h-full transition-all duration-1000 ease-linear"
                    style="width: 100%"></div>
            </div>
        </div>

        <button onclick="window.close()"
            class="px-8 py-3 rounded-2xl bg-white/5 text-gray-400 font-bold hover:bg-white/10 hover:text-white transition-all">
            Close Window
        </button>
    </div>

    <script>
        let seconds = 5;
        const countdownEl = document.getElementById('countdown');
        const progressEl = document.getElementById('progress');

        const timer = setInterval(() => {
            seconds--;
            countdownEl.textContent = seconds;
            progressEl.style.width = (seconds / 5 * 100) + '%';

            if (seconds <= 0) {
                clearInterval(timer);
                window.close();
                countdownEl.textContent = '0';
                document.querySelector('button').textContent = 'Tab can be closed';
            }
        }, 1000);
    </script>

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