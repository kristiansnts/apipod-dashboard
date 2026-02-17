@extends('layouts.app')

@section('title', 'Subscription Shop')

@section('content')
<div class="max-w-7xl mx-auto">
    <h1 class="text-3xl font-bold mb-8 text-center">Choose Your Plan</h1>

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($plans as $plan)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6">
                    <h2 class="text-2xl font-bold">{{ $plan->name }}</h2>
                    <div class="mt-4">
                        <span class="text-4xl font-bold">Rp{{ number_format($plan->price, 0, ',', '.') }}</span>
                        <span class="text-blue-100">/ {{ $plan->duration_days }} days</span>
                    </div>
                </div>

                <div class="p-6">
                    @if($plan->description)
                        <p class="text-gray-600 mb-4">{{ $plan->description }}</p>
                    @endif

                    @if($plan->subscription)
                        <div class="mb-4 space-y-2">
                            <p class="text-sm text-gray-700">
                                <span class="font-semibold">Subscription:</span> {{ $plan->subscription->sub_name }}
                            </p>
                            @if($plan->subscription->monthly_token_limit)
                                <p class="text-sm text-gray-700">
                                    <span class="font-semibold">Token Limit:</span> {{ number_format($plan->subscription->monthly_token_limit) }}
                                </p>
                            @endif
                        </div>
                    @endif

                    <form method="POST" action="{{ route('shop.purchase', $plan) }}">
                        @csrf
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition-colors">
                            Purchase Now
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <p class="text-gray-500 text-lg">No plans available at the moment.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection