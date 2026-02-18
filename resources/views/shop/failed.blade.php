@extends('layouts.app')

@section('title', 'Payment Failed')

@section('content')
<div class="max-w-md mx-auto mt-10">
    <div class="bg-white dark:bg-[#161615] rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] p-8 text-center">
        <div class="mb-6">
            <svg class="mx-auto h-16 w-16 text-[#F53003] dark:text-[#F61500]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        
        <h1 class="text-3xl font-bold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">Payment Failed</h1>
        <p class="text-[#706f6c] dark:text-[#A1A09A] mb-8">Something went wrong with your payment. Please try again.</p>

        <div class="bg-[#FAFAFA] dark:bg-[#1b1b18] rounded-lg p-4 mb-6 text-left border border-[#e3e3e0] dark:border-[#3E3E3A]">
            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mb-2"><span class="font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">Order ID:</span> {{ $payment->external_id }}</p>
            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mb-2"><span class="font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">Amount:</span> Rp{{ number_format($payment->amount, 0, ',', '.') }}</p>
            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]"><span class="font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">Status:</span> 
                <span class="text-[#F53003] dark:text-[#F61500] font-semibold">{{ $payment->status }}</span>
            </p>
        </div>

        <div class="space-y-3">
            <a href="{{ route('shop.index') }}" class="block w-full bg-[#1b1b18] dark:bg-[#eeeeec] hover:bg-black dark:hover:bg-white text-white dark:text-[#1C1C1A] border border-black dark:border-[#eeeeec] font-bold py-3 px-4 rounded-sm transition-colors text-sm">
                Try Again
            </a>
            <a href="{{ route('home') }}" class="block w-full bg-transparent hover:bg-gray-50 dark:hover:bg-[#1b1b18] text-[#1b1b18] dark:text-[#EDEDEC] border border-[#e3e3e0] dark:border-[#3E3E3A] font-bold py-3 px-4 rounded-sm transition-colors text-sm">
                Go to Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
