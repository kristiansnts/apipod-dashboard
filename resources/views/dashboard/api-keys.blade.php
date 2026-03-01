@extends('layouts.app')

@section('title', 'API Keys')
@section('subtitle', 'Manage your API keys for accessing the APIPod proxy.')

@section('content')
    <div class="space-y-6">
        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-4 flex items-start gap-3">
                <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="font-medium">{{ session('success') }}</p>
                    @if(session('new_key'))
                        <div class="mt-2 bg-green-100 rounded-lg p-3">
                            <p class="text-xs font-medium text-green-700 mb-1">⚠️ Copy this key now — you won't see it again:</p>
                            <code class="mono text-sm break-all select-all">{{ session('new_key') }}</code>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 rounded-lg p-4">
                <p class="font-medium">{{ session('error') }}</p>
            </div>
        @endif

        {{-- Create Key Form --}}
        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-gray-900">Create New Key</h2>
                <span class="text-sm text-gray-500">{{ $apiKeys->where('is_active', true)->count() }} active keys</span>
            </div>

            <form method="POST" action="{{ route('dashboard.api-keys.create') }}" class="flex flex-col md:flex-row gap-3">
                @csrf
                <input type="text" name="name" placeholder="Key name (e.g. Production, Dev)" required
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                <input type="number" name="token_limit" placeholder="Token limit (optional)" min="1"
                    class="w-48 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                <button type="submit" class="btn-primary whitespace-nowrap">Create Key</button>
            </form>
        </div>

        {{-- Keys List --}}
        <div class="card">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-900">Your API Keys</h2>
            </div>

            @if($apiKeys->isEmpty())
                <div class="p-12 text-center">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                    <p class="text-gray-500 text-sm">No API keys yet. Create one above to get started.</p>
                </div>
            @else
                <div class="divide-y divide-gray-100">
                    @foreach($apiKeys as $key)
                        <div class="p-4 md:p-6 flex items-center justify-between gap-4 {{ !$key->is_active ? 'opacity-50' : '' }}">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <p class="font-semibold text-gray-900 text-sm">{{ $key->name }}</p>
                                    @if(!$key->is_active)
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Revoked</span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Active</span>
                                    @endif
                                </div>
                                <p class="mono text-sm text-gray-500 mt-1">{{ $key->key_prefix }}••••••••</p>
                                <div class="flex gap-4 mt-2 text-xs text-gray-400">
                                    <span>Created {{ $key->created_at->diffForHumans() }}</span>
                                    @if($key->last_used_at)
                                        <span>Last used {{ $key->last_used_at->diffForHumans() }}</span>
                                    @else
                                        <span>Never used</span>
                                    @endif
                                    @if($key->token_limit)
                                        <span>Limit: {{ number_format($key->used_tokens) }}/{{ number_format($key->token_limit) }}
                                            tokens</span>
                                    @else
                                        <span>Used: {{ number_format($key->used_tokens) }} tokens</span>
                                    @endif
                                </div>

                                {{-- Per-key usage bar --}}
                                @if($key->token_limit && $key->is_active)
                                    @php
                                        $keyPercent = round(($key->used_tokens / $key->token_limit) * 100, 1);
                                        $keyPercent = min(100, $keyPercent);
                                        $keyColor = $keyPercent > 90 ? 'bg-red-500' : ($keyPercent > 70 ? 'bg-yellow-500' : 'bg-blue-500');
                                    @endphp
                                    <div class="w-48 bg-gray-200 rounded-full h-1.5 mt-2">
                                        <div class="{{ $keyColor }} h-1.5 rounded-full" style="width: {{ $keyPercent }}%"></div>
                                    </div>
                                @endif
                            </div>

                            @if($key->is_active)
                                <form method="POST" action="{{ route('dashboard.api-keys.revoke', $key) }}"
                                    onsubmit="return confirm('Are you sure you want to revoke this key? This cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium transition-colors">
                                        Revoke
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection