@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- User Info -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold mb-4">Welcome, {{ auth()->user()->name }}!</h2>
            <div class="space-y-2">
                <p class="text-gray-700"><span class="font-semibold">Email:</span> {{ auth()->user()->email }}</p>
                <p class="text-gray-700"><span class="font-semibold">Status:</span>
                    <span class="{{ auth()->user()->active ? 'text-green-600' : 'text-red-600' }} font-bold">
                        {{ auth()->user()->active ? 'Active' : 'Inactive' }}
                    </span>
                </p>
                <p class="text-gray-700"><span class="font-semibold">Subscription:</span> {{ $quota['subscription_name'] ?? 'None' }}</p>
            </div>
        </div>

        <!-- Quota Usage -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold mb-4">Token Quota</h2>
            @if($quota['has_quota'])
                <div class="space-y-4">
                    <div class="flex justify-between items-end mb-1">
                        <span class="text-sm font-medium text-blue-700">Usage ({{ $quota['percentage'] }}%)</span>
                        <span class="text-sm font-medium text-blue-700">{{ number_format($quota['used']) }} / {{ number_format($quota['limit']) }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-4">
                        <div class="bg-blue-600 h-4 rounded-full" style="width: {{ $quota['percentage'] }}%"></div>
                    </div>
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Remaining: {{ number_format($quota['remaining']) }}</span>
                        @if($quota['reset_at'])
                            <span>Resets: {{ \Carbon\Carbon::parse($quota['reset_at'])->format('M d, Y') }}</span>
                        @endif
                    </div>
                </div>
            @else
                <p class="text-gray-500 italic">No active quota for this subscription.</p>
            @endif
        </div>
    </div>
</div>
@endsection