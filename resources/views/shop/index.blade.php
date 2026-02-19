@extends('layouts.app')

@section('title', 'Subscription Shop')

@section('content')
<div class="max-w-7xl mx-auto">
    <h1 class="text-3xl font-bold mb-8 text-center text-[#1b1b18] dark:text-[#EDEDEC]">Choose Your Plan</h1>

    @if(session('error'))
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 rounded-sm mb-6">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($plans as $plan)
            <div class="bg-white dark:bg-[#161615] rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] overflow-hidden flex flex-col h-full">
                <div class="p-6 border-b border-[#e3e3e0] dark:border-[#3E3E3A] bg-[#FAFAFA] dark:bg-[#1b1b18]">
                    <h2 class="text-2xl font-bold text-[#1b1b18] dark:text-[#EDEDEC]">{{ $plan->name }}</h2>
                    <div class="mt-4">
                        <span class="text-4xl font-bold text-[#F53003] dark:text-[#F61500]">Rp{{ number_format($plan->price, 0, ',', '.') }}</span>
                        <span class="text-[#706f6c] dark:text-[#A1A09A]">/ {{ $plan->duration_days }} days</span>
                    </div>
                </div>

                <div class="p-6 flex-grow flex flex-col justify-between">
                    <div>
                        @if($plan->description)
                            <p class="text-[#706f6c] dark:text-[#A1A09A] mb-4">{{ $plan->description }}</p>
                        @endif

                        @if($plan->subscription)
                            <div class="mb-4 space-y-2">
                                <p class="text-sm text-[#1b1b18] dark:text-[#EDEDEC]">
                                    <span class="font-semibold">Subscription:</span> {{ $plan->subscription->sub_name }}
                                </p>
                                @if($plan->subscription->monthly_token_limit)
                                    <p class="text-sm text-[#1b1b18] dark:text-[#EDEDEC]">
                                        <span class="font-semibold">Token Limit:</span> {{ number_format($plan->subscription->monthly_token_limit) }}
                                    </p>
                                @endif
                            </div>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('shop.purchase', $plan) }}" class="mt-6">
                        @csrf
                        <button type="submit" class="w-full inline-block dark:bg-[#eeeeec] dark:border-[#eeeeec] dark:text-[#1C1C1A] dark:hover:bg-white dark:hover:border-white hover:bg-black hover:border-black px-5 py-3 bg-[#1b1b18] rounded-sm border border-black text-white text-sm font-medium leading-normal transition-all">
                            Purchase Now
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <p class="text-[#706f6c] dark:text-[#A1A09A] text-lg">No plans available at the moment.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection