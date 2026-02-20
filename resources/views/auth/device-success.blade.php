@extends('layouts.guest')

@section('title', 'Device Authorized')

@section('content')
    <div class="w-full max-w-md light-card p-10 rounded-[40px] animate-fade-in text-center">
        <div class="relative mb-10">
            <div
                class="relative inline-flex items-center justify-center w-24 h-24 rounded-full bg-emerald-50 border border-emerald-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-emerald-500" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <h2 class="text-3xl font-extrabold text-gray-900 mb-4">Device Authorized!</h2>
        <p class="text-gray-500 mb-10 leading-relaxed font-medium">
            Your terminal is now securely connected to the Apipod network. You can return to your CLI and start
            orchestrating.
        </p>

        <div class="bg-gray-50 border border-gray-100 rounded-3xl p-8 mb-8">
            <p class="text-[11px] text-gray-400 mb-4 font-bold uppercase tracking-[0.2em]">
                Closing in <span id="countdown" class="text-gray-900">5</span> seconds
            </p>
            <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                <div id="progress" class="bg-blue-600 h-full transition-all duration-1000 ease-linear rounded-full"
                    style="width: 100%"></div>
            </div>
        </div>

        <button onclick="window.close()"
            class="px-8 py-3 rounded-2xl bg-gray-50 text-gray-500 font-bold hover:bg-gray-100 hover:text-gray-700 transition-all text-sm uppercase tracking-widest">
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