@extends('layouts.app')

@section('title', 'AI Dashboard')
@section('subtitle', 'Get started with Apipod and view your model orchestrations.')

@section('content')
    <!-- Tab Navigation (SumoPod Style) -->
    <div class="inline-flex p-1 bg-gray-200/50 rounded-[10px] mb-8">
        <a href="{{ route('home') }}" class="tab-button active">Quick Start</a>
        <a href="{{ route('dashboard.usage') }}" class="tab-button">Usage & Quotas</a>
        <a href="{{ route('dashboard.models') }}" class="tab-button">Models</a>
        <a href="{{ route('dashboard.api-keys') }}" class="tab-button">API Keys</a>
    </div>

    <!-- Main Content Card -->
    <div class="card p-8 lg:p-10 mb-8">
        <div class="max-w-3xl">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Quick Start Guide</h2>
            <p class="text-gray-500 mb-8 leading-relaxed font-medium">
                Connect your application to Apipod's unified gateway and start routing requests to multiple AI providers
                with a single API key.
            </p>

            <div class="space-y-10">
                <!-- Step 1 -->
                <div class="flex gap-6">
                    <div
                        class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center font-bold text-blue-600 text-sm border border-blue-100">
                        1</div>
                    <div>
                        <h3 class="font-bold text-gray-900 mb-2">Create an API Key</h3>
                        <p class="text-sm text-gray-500 mb-4 font-medium leading-relaxed">Generated a secure key to
                            authenticate your requests. You can manage multiple keys for different projects.</p>
                        <button class="btn-secondary text-sm">Manage API Keys</button>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="flex gap-6">
                    <div
                        class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center font-bold text-blue-600 text-sm border border-blue-100">
                        2</div>
                    <div class="flex-1">
                        <h3 class="font-bold text-gray-900 mb-2">Configure Endpoint</h3>
                        <p class="text-sm text-gray-500 mb-4 font-medium leading-relaxed">Use our unified endpoint which
                            automatically handles fallbacks and provider healthy checks.</p>

                        <div
                            class="bg-gray-50 border border-gray-100 rounded-lg p-4 mono text-xs text-gray-700 flex items-center justify-between">
                            <span>https://api.apipod.com/v1</span>
                            <button
                                class="text-blue-600 font-bold uppercase tracking-widest text-[10px] hover:underline">Copy</button>
                        </div>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="flex gap-6">
                    <div
                        class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center font-bold text-blue-600 text-sm border border-blue-100">
                        3</div>
                    <div>
                        <h3 class="font-bold text-gray-900 mb-2">Explore Documentation</h3>
                        <p class="text-sm text-gray-500 mb-4 font-medium leading-relaxed">Look into our advanced
                            orchestration features like model-to-model fallbacks and token budget management.</p>
                        <a href="#" class="text-blue-600 text-sm font-bold hover:underline">Read Full Docs &rarr;</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Usage Preview Grid (SumoPod Style minor stats) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="card p-6">
            <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-4">Current Usage</h3>
            <div class="flex items-end gap-2 mb-2">
                <span
                    class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ number_format(auth()->user()->tokens_used) }}</span>
                @php
                    $limit = (auth()->user()->plan && auth()->user()->plan->subscription) ? auth()->user()->plan->subscription->monthly_token_limit : 1000000;
                    $percentage = $limit > 0 ? (auth()->user()->tokens_used / $limit) * 100 : 0;
                @endphp
                <span class="text-sm font-bold text-gray-400 pb-1">/ {{ number_format($limit) }} Tokens</span>
            </div>
            <div class="w-full bg-gray-100 h-1.5 rounded-full overflow-hidden">
                <div class="bg-blue-600 h-full rounded-full" style="width: {{ min(100, $percentage) }}%"></div>
            </div>
        </div>

        <div class="card p-6">
            <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-4">Active Keys</h3>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                    <span class="text-sm font-bold text-gray-900">prod-backend-main</span>
                </div>
                <span class="text-[11px] font-bold text-gray-400 mono">sk_live_...2d9f</span>
            </div>
        </div>
    </div>
@endsection