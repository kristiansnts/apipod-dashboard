<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8 text-center">
            <div class="mb-6">
                <svg class="mx-auto h-16 w-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            
            <h1 class="text-3xl font-bold text-gray-900 mb-4">Payment Successful!</h1>
            <p class="text-gray-600 mb-8">Your subscription has been activated.</p>

            <div class="bg-gray-50 rounded-lg p-4 mb-6 text-left">
                <p class="text-sm text-gray-600 mb-2"><span class="font-semibold">Order ID:</span> {{ $payment->external_id }}</p>
                <p class="text-sm text-gray-600 mb-2"><span class="font-semibold">Amount:</span> Rp{{ number_format($payment->amount, 0, ',', '.') }}</p>
                <p class="text-sm text-gray-600"><span class="font-semibold">Status:</span> 
                    <span class="text-green-600 font-semibold">{{ $payment->status }}</span>
                </p>
            </div>

            <div class="space-y-3">
                <a href="{{ route('dashboard') }}" class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition-colors">
                    Go to Dashboard
                </a>
                <a href="{{ route('shop.index') }}" class="block w-full bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-3 px-4 rounded-lg transition-colors">
                    Browse More Plans
                </a>
            </div>
        </div>
    </div>
</body>
</html>
