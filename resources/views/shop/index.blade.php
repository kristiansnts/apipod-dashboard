@extends('layouts.app')

@section('title', 'Subscription')
@section('subtitle', 'Select a plan to increase your token limits and access advanced provider orchestration.')

@section('content')
    <div class="max-w-6xl mx-auto">
        @if(session('error'))
            <div
                class="bg-red-50 border border-red-100 text-red-600 px-6 py-4 rounded-xl mb-10 flex items-center gap-3 text-sm font-medium">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
            @forelse($plans as $plan)
                <div class="card flex flex-col p-8 transition-all hover:border-blue-200">
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-2">
                            <h2 class="text-xl font-bold text-gray-900 tracking-tight">{{ $plan->name }}</h2>
                            @if($plan->price > 500000)
                                <span
                                    class="px-2 py-0.5 rounded bg-blue-50 text-blue-600 text-[10px] font-bold uppercase tracking-widest border border-blue-100">Recommended</span>
                            @endif
                        </div>

                        <div class="mb-6 flex items-baseline gap-1">
                            <span
                                class="text-3xl font-extrabold text-gray-900 tracking-tighter">Rp{{ number_format($plan->price, 0, ',', '.') }}</span>
                            <span class="text-gray-400 text-sm font-semibold">/ {{ $plan->duration_days }} Days</span>
                        </div>

                        @if($plan->description)
                            <p class="text-sm text-gray-500 font-medium leading-relaxed mb-8">{{ $plan->description }}</p>
                        @endif

                        <div class="space-y-4 mb-10">
                            @if($plan->subscription)
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-blue-600 shrink-0" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span
                                        class="text-sm font-semibold text-gray-700">{{ number_format($plan->subscription->monthly_token_limit) }}
                                        Tokens / Monthly</span>
                                </div>
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-blue-600 shrink-0" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="text-sm font-semibold text-gray-700">{{ $plan->subscription->sub_name }}
                                        Tier</span>
                                </div>
                            @endif
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-blue-600 shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-sm font-semibold text-gray-700">Priority Provider Routing</span>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('shop.purchase', $plan) }}">
                        @csrf
                        <button type="submit"
                            class="w-full btn-blue py-3.5 rounded-lg font-bold text-[13px] uppercase tracking-widest active:scale-[0.98] transition-all">
                            Select Plan
                        </button>
                    </form>
                </div>
            @empty
                <div class="col-span-full card p-20 text-center bg-gray-50/50 border-dashed">
                    <div
                        class="w-16 h-16 bg-white rounded-xl shadow-sm border border-gray-100 flex items-center justify-center mx-auto mb-6 text-gray-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">No Plans Available</h3>
                    <p class="text-sm text-gray-500 max-w-sm mx-auto font-medium">We're updating our subscription model. Please
                        check back shortly.</p>
                </div>
            @endforelse
        </div>

        <!-- Help Info -->
        <div class="card p-10 bg-white border-blue-100 flex flex-col md:flex-row items-center gap-10">
            <div class="flex-1">
                <h3 class="text-xl font-bold text-gray-900 mb-2">Need a tailored solution?</h3>
                <p class="text-sm text-gray-500 font-medium leading-relaxed max-w-lg">For enterprises requiring custom rate
                    limits, on-premise deployments, or dedicated support channels, we offer custom enterprise agreements.
                </p>
            </div>
            <button class="btn-secondary whitespace-nowrap px-8">Contact Sales Team</button>
        </div>
    </div>
@endsection