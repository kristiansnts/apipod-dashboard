@extends('layouts.guest')

@section('title', 'Device Authorized — API Pod')

@section('content')
<div class="w-full max-w-md bg-white dark:bg-[#161615] p-8 rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]">
    <div class="text-center">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-green-100 dark:bg-green-900/30 mb-6">
            <i class="fas fa-check-circle text-4xl text-green-500"></i>
        </div>

        <h2 class="text-2xl font-bold text-[#1b1b18] dark:text-[#EDEDEC] mb-2">Device Authorized!</h2>
        <p class="text-[#706f6c] dark:text-[#A1A09A] mb-6">
            Your terminal is now connected. You can return to your CLI.
        </p>

        <div class="bg-[#f8f8f7] dark:bg-[#1b1b18] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg px-6 py-4 mb-6">
            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
                This page will close in <span id="countdown" class="font-bold text-[#1b1b18] dark:text-[#EDEDEC]">5</span> seconds
            </p>
            <div class="mt-3 w-full bg-[#e3e3e0] dark:bg-[#3E3E3A] rounded-full h-1.5">
                <div id="progress" class="bg-green-500 h-1.5 rounded-full transition-all duration-1000 ease-linear" style="width: 100%"></div>
            </div>
        </div>

        <button onclick="window.close()" class="text-sm text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC] underline transition-colors">
            Close now
        </button>
    </div>
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
            // Fallback if window.close() is blocked by browser
            countdownEl.textContent = '0';
            document.querySelector('button').textContent = 'You can close this tab';
        }
    }, 1000);
</script>
@endsection
