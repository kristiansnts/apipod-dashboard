@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- User Info -->
        <div class="bg-white dark:bg-[#161615] rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] p-6">
            <h2 class="text-2xl font-bold mb-4 text-[#1b1b18] dark:text-[#EDEDEC]">Welcome, {{ auth()->user()->name }}!</h2>
            <div class="space-y-2">
                <p class="text-[#706f6c] dark:text-[#A1A09A]"><span class="font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">Email:</span> {{ auth()->user()->email }}</p>
                <p class="text-[#706f6c] dark:text-[#A1A09A]"><span class="font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">Status:</span>
                    <span class="{{ auth()->user()->active ? 'text-green-600 dark:text-green-500' : 'text-red-600 dark:text-red-500' }} font-bold">
                        {{ auth()->user()->active ? 'Active' : 'Inactive' }}
                    </span>
                </p>
                <p class="text-[#706f6c] dark:text-[#A1A09A]"><span class="font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">Subscription:</span> {{ $quota['subscription_name'] ?? 'None' }}</p>
            </div>
        </div>

        <!-- Quota Usage -->
        <div class="bg-white dark:bg-[#161615] rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] p-6">
            <h2 class="text-2xl font-bold mb-4 text-[#1b1b18] dark:text-[#EDEDEC]">Token Quota</h2>
            @if($quota['has_quota'])
                <div class="space-y-4">
                    <div class="flex justify-between items-end mb-1">
                        <span class="text-sm font-medium text-[#F53003] dark:text-[#FF4433]">Usage ({{ $quota['percentage'] }}%)</span>
                        <span class="text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC]">{{ number_format($quota['used']) }} / {{ number_format($quota['limit']) }}</span>
                    </div>
                    <div class="w-full bg-[#e3e3e0] dark:bg-[#3E3E3A] rounded-full h-4">
                        <div class="bg-[#F53003] dark:bg-[#FF4433] h-4 rounded-full" style="width: {{ $quota['percentage'] }}%"></div>
                    </div>
                    <div class="flex justify-between text-sm text-[#706f6c] dark:text-[#A1A09A]">
                        <span>Remaining: {{ number_format($quota['remaining']) }}</span>
                        @if($quota['reset_at'])
                            <span>Resets: {{ \Carbon\Carbon::parse($quota['reset_at'])->format('M d, Y') }}</span>
                        @endif
                    </div>
                </div>
            @else
                <p class="text-[#706f6c] dark:text-[#A1A09A] italic">No active quota for this subscription.</p>
            @endif
        </div>
    </div>
</div>
@endsection