@extends('layouts.app')

@section('title', 'Plan Status')
@section('subtitle', 'View your current plan details and quota usage.')

@section('content')
    <div class="space-y-6">
        {{-- Current Plan Card --}}
        <div class="card p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Current Plan</h2>

            @if($plan)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Plan Name</p>
                        <p class="text-xl font-bold text-gray-900 mt-1">{{ $plan->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Monthly Quota</p>
                        <p class="text-xl font-bold text-gray-900 mt-1">{{ number_format($plan->token_quota) }} tokens</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Max API Keys</p>
                        <p class="text-xl font-bold text-gray-900 mt-1">{{ $plan->max_api_keys }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Price</p>
                        <p class="text-xl font-bold text-gray-900 mt-1">Rp {{ number_format($plan->price, 0, ',', '.') }}</p>
                    </div>
                </div>
            @else
                <div class="text-center py-8">
                    <p class="text-gray-500">No active plan. <a href="{{ route('shop.index') }}"
                            class="text-blue-600 hover:underline font-medium">Browse plans →</a></p>
                </div>
            @endif
        </div>

        {{-- Quota Usage --}}
        @if($org && $plan)
            <div class="card p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Token Quota</h2>

                <div class="mb-4">
                    @php
                        $percent = $plan->token_quota > 0 ? round(($org->token_balance / $plan->token_quota) * 100, 1) : 0;
                        $percent = max(0, min(100, $percent));
                        $barColor = $percent > 50 ? 'bg-green-500' : ($percent > 20 ? 'bg-yellow-500' : 'bg-red-500');
                    @endphp
                    <div class="flex justify-between text-sm mb-2">
                        <span class="font-medium text-gray-700">{{ number_format($org->token_balance) }} remaining</span>
                        <span class="text-gray-500">{{ $percent }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="{{ $barColor }} h-3 rounded-full transition-all duration-500"
                            style="width: {{ $percent }}%"></div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm font-medium text-gray-500">Token Balance</p>
                        <p class="text-lg font-bold {{ $org->token_balance < 0 ? 'text-red-600' : 'text-gray-900' }}">
                            {{ number_format($org->token_balance) }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm font-medium text-gray-500">Quota Resets</p>
                        <p class="text-lg font-bold text-gray-900">
                            {{ $org->quota_reset_at ? $org->quota_reset_at->format('M d, Y') : 'Not set' }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm font-medium text-gray-500">Organization</p>
                        <p class="text-lg font-bold text-gray-900">{{ $org->name }}</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Rate Limits --}}
        @if($plan && ($plan->rate_limit_rpm || $plan->rate_limit_tpm))
            <div class="card p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Rate Limits</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if($plan->rate_limit_rpm)
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm font-medium text-gray-500">Requests per Minute</p>
                            <p class="text-lg font-bold text-gray-900">{{ number_format($plan->rate_limit_rpm) }} RPM</p>
                        </div>
                    @endif
                    @if($plan->rate_limit_tpm)
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm font-medium text-gray-500">Tokens per Minute</p>
                            <p class="text-lg font-bold text-gray-900">{{ number_format($plan->rate_limit_tpm) }} TPM</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- Allowed Models --}}
        @if($plan)
            <div class="card p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Available Models</h2>
                @php $models = $plan->allowedModels; @endphp
                @if($models->isEmpty())
                    <p class="text-sm text-gray-500">All models are available on your plan.</p>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($models as $model)
                            <div class="bg-gray-50 rounded-lg p-3 flex items-center gap-3">
                                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                <div>
                                    <p class="font-medium text-gray-900 text-sm">{{ $model->model_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $model->provider->name ?? 'Unknown' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif
    </div>
@endsection