@extends('layouts.app')

@section('title', 'Complete Payment')
@section('subtitle', 'Finalize your plan purchase securely via Midtrans.')

@section('content')
    <div class="max-w-2xl mx-auto">
        <!-- Order Summary Card -->
        <div class="card p-8 mb-8">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Order Summary</h2>

            <div class="space-y-4 mb-8">
                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                    <span class="text-sm font-medium text-gray-500">Plan</span>
                    <span class="text-sm font-bold text-gray-900">{{ $plan->name }}</span>
                </div>
                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                    <span class="text-sm font-medium text-gray-500">Duration</span>
                    <span class="text-sm font-bold text-gray-900">{{ $plan->duration_days }} Days</span>
                </div>
                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                    <span class="text-sm font-medium text-gray-500">Order ID</span>
                    <span class="text-xs font-bold text-gray-400 mono">{{ $payment->external_id }}</span>
                </div>
                <div class="flex items-center justify-between py-3">
                    <span class="text-sm font-bold text-gray-900">Total</span>
                    <span
                        class="text-2xl font-extrabold text-gray-900 tracking-tight">Rp{{ number_format($payment->amount, 0, ',', '.') }}</span>
                </div>
            </div>

            <button id="pay-button"
                class="w-full btn-blue py-4 rounded-lg font-bold text-[13px] uppercase tracking-widest active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                Pay Now
            </button>
        </div>

        <!-- Security Info -->
        <div class="card p-6 bg-gray-50/50 border-gray-100">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                <div>
                    <p class="text-sm font-bold text-gray-700 mb-1">Secure Payment</p>
                    <p class="text-xs text-gray-500 font-medium leading-relaxed">Your payment is processed securely by
                        Midtrans. We support QRIS, bank transfers, e-wallets, credit cards, and more.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Midtrans Snap.js -->
    <script
        src="{{ $isProduction ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
        data-client-key="{{ $clientKey }}"></script>

    <script type="text/javascript">
        document.getElementById('pay-button').addEventListener('click', function () {
            window.snap.pay('{{ $snapToken }}', {
                onSuccess: function (result) {
                    window.location.href = '{{ route("shop.success", ["payment" => $payment->id]) }}';
                },
                onPending: function (result) {
                    window.location.href = '{{ route("shop.success", ["payment" => $payment->id]) }}';
                },
                onError: function (result) {
                    window.location.href = '{{ route("shop.failed", ["payment" => $payment->id]) }}';
                },
                onClose: function () {
                    // User closed the popup without completing the payment
                }
            });
        });
    </script>
@endsection