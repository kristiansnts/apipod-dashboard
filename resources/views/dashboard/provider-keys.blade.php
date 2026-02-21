@extends('layouts.app')

@section('title', 'Provider Keys')
@section('subtitle', 'Manage your upstream API keys for BYOK access.')

@section('content')
    <div class="space-y-6">
        {{-- Security Notice --}}
        <div class="bg-blue-50 border border-blue-200 text-blue-800 rounded-lg p-4 text-sm">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                <div>
                    <p class="font-semibold">Your keys are encrypted at rest</p>
                    <p class="mt-1 text-blue-700">We never display, log, or store your full key in plain text. Only you have
                        access to your provider keys.</p>
                </div>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-4 text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 rounded-lg p-4 text-sm font-medium">
                {{ session('error') }}
            </div>
        @endif

        {{-- Add Key Form --}}
        <div class="card p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Add Provider Key</h2>

            @php
                $availableProviders = $providers->whereNotIn('id', $usedProviderIds);
            @endphp

            @if($availableProviders->isNotEmpty())
                <form method="POST" action="{{ route('dashboard.provider-keys.store') }}" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Provider</label>
                            <select name="provider_id" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                                <option value="">Select provider...</option>
                                @foreach($availableProviders as $provider)
                                    <option value="{{ $provider->id }}">
                                        {{ $provider->name }}
                                        @if(isset($modelsByProvider[$provider->id]))
                                            ({{ $modelsByProvider[$provider->id]->count() }} models)
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Label</label>
                            <input type="text" name="label" placeholder="e.g. My OpenRouter Key" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">API Key</label>
                            <input type="password" name="api_key" placeholder="sk-..." required minlength="10"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm mono">
                        </div>
                    </div>
                    <button type="submit" class="btn-primary">Add Key</button>
                </form>
            @else
                <p class="text-sm text-gray-500">All available providers already have a key configured.</p>
            @endif
        </div>

        {{-- Model Selection --}}
        <div class="card p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-2">Active Model</h2>
            <p class="text-sm text-gray-500 mb-4">Select one model for all your API requests. Free plan is limited to a
                single model.</p>

            @if($activeModel)
                <div class="bg-green-50 border border-green-200 rounded-lg p-3 mb-4 flex items-center gap-3">
                    <div class="w-2.5 h-2.5 rounded-full bg-green-500 flex-shrink-0"></div>
                    <div>
                        <p class="font-semibold text-sm text-green-900">{{ $activeModel->model_name }}</p>
                        <p class="text-xs text-green-700">{{ $activeModel->provider->name ?? '' }}</p>
                    </div>
                </div>
            @else
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4">
                    <p class="text-sm text-yellow-800">⚠ No model selected. Select one below to start making API requests.</p>
                </div>
            @endif

            @if($availableModels->isNotEmpty())
                <form method="POST" action="{{ route('dashboard.select-model') }}" class="flex items-end gap-3">
                    @csrf
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Choose Model</label>
                        <select name="model_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                            <option value="">Select a model...</option>
                            @foreach($availableModels->groupBy(fn($m) => $m->provider->name ?? 'Unknown') as $provName => $models)
                                <optgroup label="{{ $provName }}">
                                    @foreach($models as $model)
                                        <option value="{{ $model->llm_model_id }}" {{ $activeModel && $activeModel->llm_model_id == $model->llm_model_id ? 'selected' : '' }}>
                                            {{ $model->model_name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn-primary whitespace-nowrap hover:cursor-pointer">Set Model</button>
                </form>
            @else
                <p class="text-sm text-gray-500">Add a provider key first to see available models.</p>
            @endif
        </div>

        {{-- Keys List --}}
        <div class="card">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-900">Your Provider Keys</h2>
            </div>

            @if($keys->isEmpty())
                <div class="p-12 text-center">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <p class="text-gray-500 text-sm">No provider keys yet. Add one above to start using BYOK.</p>
                </div>
            @else
                <div class="divide-y divide-gray-100">
                    @foreach($keys as $key)
                        <div class="p-4 md:p-6 flex items-center justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $key->provider->provider_type ?? 'unknown' }}
                                    </span>
                                    <p class="font-semibold text-gray-900 text-sm">{{ $key->label }}</p>
                                    @if(!$key->is_active)
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Inactive</span>
                                    @endif
                                </div>
                                <div class="flex gap-4 mt-2 text-xs text-gray-400">
                                    <span class="mono">{{ $key->masked_key }}</span>
                                    <span>{{ $key->provider->name ?? '' }}</span>
                                    <span>Added {{ $key->created_at->diffForHumans() }}</span>
                                </div>
                                @if(isset($modelsByProvider[$key->provider_id]))
                                    <div class="flex flex-wrap gap-1.5 mt-2">
                                        @foreach($modelsByProvider[$key->provider_id] as $model)
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-600">{{ $model->model_name }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <form method="POST" action="{{ route('dashboard.provider-keys.delete', $key) }}"
                                onsubmit="return confirm('Remove this provider key? You will need to re-enter it to use BYOK with this provider.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium transition-colors">
                                    Remove
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection