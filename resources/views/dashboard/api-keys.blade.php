@extends('layouts.app')

@section('title', 'API Keys')
@section('subtitle', 'Manage your authentication keys to access the Apipod gateway.')

@section('content')
    <!-- Tab Navigation (SumoPod Style) -->
    <div class="inline-flex p-1 bg-gray-200/50 rounded-[10px] mb-8">
        <a href="{{ route('home') }}" class="tab-button">Quick Start</a>
        <a href="{{ route('dashboard.usage') }}" class="tab-button">Usage & Quotas</a>
        <a href="{{ route('dashboard.models') }}" class="tab-button">Models</a>
        <a href="{{ route('dashboard.api-keys') }}" class="tab-button active">API Keys</a>
    </div>

    <div class="max-w-4xl">
        <!-- API Key Management Card -->
        <div class="card mb-8">
            <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-gray-900">Your API Keys</h3>
                    <p class="text-xs text-gray-400 font-medium">Use these keys to authenticate your requests via the Apipod
                        API.</p>
                </div>
                <button class="btn-primary text-sm">Create New Key</button>
            </div>

            <div class="divide-y divide-gray-100">
                <div class="px-8 py-6 hover:bg-gray-50/50 transition-colors">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="font-bold text-gray-900 text-sm">Default Secret Key</span>
                                <span
                                    class="px-2 py-0.5 rounded bg-blue-50 text-blue-600 text-[10px] font-bold uppercase tracking-widest border border-blue-100">Active</span>
                            </div>
                            <div class="flex items-center gap-4">
                                <code
                                    class="bg-gray-100 px-3 py-1.5 rounded-lg text-xs font-bold text-gray-700 mono flex-1">
                                        {{ $user->apitoken ? substr($user->apitoken, 0, 8) . '...' . substr($user->apitoken, -8) : 'No key generated yet.' }}
                                    </code>
                                <div class="flex items-center gap-2">
                                    <button
                                        class="text-blue-600 hover:text-blue-700 text-xs font-bold uppercase tracking-widest transition-colors flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                                        </svg>
                                        Copy
                                    </button>
                                    <button
                                        class="text-gray-400 hover:text-red-600 text-xs font-bold uppercase tracking-widest transition-colors flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Revoke
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center gap-6 text-[11px] font-bold text-gray-400 uppercase tracking-widest">
                        <span>Created: {{ $user->created_at->format('M d, Y') }}</span>
                        <span>Last Used:
                            {{ $user->usageLogs()->max('timestamp') ? \Carbon\Carbon::parse($user->usageLogs()->max('timestamp'))->diffForHumans() : 'Never' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security Best Practices -->
        <div class="card p-8 bg-blue-50/50 border-blue-100">
            <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                Security Recommendations
            </h3>
            <ul class="space-y-3 text-sm text-gray-500 font-medium">
                <li class="flex items-start gap-3">
                    <span class="text-blue-600 font-bold">•</span>
                    Never share your API keys or expose them in client-side code (browsers/mobile apps).
                </li>
                <li class="flex items-start gap-3">
                    <span class="text-blue-600 font-bold">•</span>
                    Rotate your keys periodically to minimize risk in case of accidental leaks.
                </li>
                <li class="flex items-start gap-3">
                    <span class="text-blue-600 font-bold">•</span>
                    Use different keys for development, staging, and production environments.
                </li>
            </ul>
        </div>
    </div>
@endsection