@extends('layouts.app')

@section('title', 'Model Weights')
@section('subtitle', 'Control how your requests are distributed across AI models.')

@section('content')
    <div class="space-y-6">
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

        {{-- Info --}}
        <div class="bg-blue-50 border border-blue-200 text-blue-800 rounded-lg p-4 text-sm">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="font-semibold">Weighted Model Routing</p>
                    <p class="mt-1 text-blue-700">Each request is routed to a model based on these weights. A model with 70% weight will handle ~70% of your requests. Weights must total 100%.</p>
                </div>
            </div>
        </div>

        @if($quotaItems->isEmpty())
            <div class="card p-6">
                <div class="text-center py-10">
                    <p class="text-sm font-medium text-gray-500 mb-2">No models configured for your plan yet.</p>
                    <p class="text-xs text-gray-400">Contact support or upgrade your plan to get started.</p>
                </div>
            </div>
        @else
            <form method="POST" action="{{ route('dashboard.model-weights.update') }}"
                x-data="{
                    weights: {
                        @foreach($quotaItems as $item)
                            '{{ $item->quota_id }}': {{ $item->percentage_weight }},
                        @endforeach
                    },
                    get total() {
                        return Object.values(this.weights).reduce((sum, w) => sum + parseInt(w || 0), 0);
                    },
                    get isValid() {
                        return this.total === 100;
                    }
                }">
                @csrf

                {{-- Weight Distribution Bar --}}
                <div class="card p-6 mb-6">
                    <h2 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Distribution Preview</h2>
                    <div class="w-full h-8 rounded-lg overflow-hidden flex" style="min-width: 0">
                        @php
                            $colors = ['bg-blue-500', 'bg-emerald-500', 'bg-amber-500', 'bg-purple-500', 'bg-pink-500', 'bg-cyan-500', 'bg-red-500', 'bg-indigo-500'];
                        @endphp
                        @foreach($quotaItems as $i => $item)
                            <div class="{{ $colors[$i % count($colors)] }} transition-all duration-300 flex items-center justify-center text-white text-[10px] font-bold overflow-hidden"
                                :style="'width: ' + (weights['{{ $item->quota_id }}'] || 0) + '%'">
                                <span x-show="weights['{{ $item->quota_id }}'] >= 10"
                                    x-text="weights['{{ $item->quota_id }}'] + '%'"></span>
                            </div>
                        @endforeach
                    </div>
                    <div class="flex flex-wrap gap-3 mt-3">
                        @foreach($quotaItems as $i => $item)
                            <div class="flex items-center gap-1.5 text-xs text-gray-600">
                                <div class="w-2.5 h-2.5 rounded {{ $colors[$i % count($colors)] }}"></div>
                                <span class="font-medium">{{ $item->llmModel->model_name ?? 'Unknown' }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Model Weight Controls --}}
                <div class="card overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                        <h3 class="text-sm font-bold text-gray-900 mono">Model Weights</h3>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-bold" :class="isValid ? 'text-green-600' : 'text-red-600'"
                                x-text="total + '%'"></span>
                            <span class="text-[10px] font-bold uppercase tracking-widest"
                                :class="isValid ? 'text-green-500' : 'text-red-500'"
                                x-text="isValid ? 'Valid' : 'Must equal 100%'"></span>
                        </div>
                    </div>

                    <div class="divide-y divide-gray-100">
                        @foreach($quotaItems as $i => $item)
                            <div class="px-6 py-5 flex items-center gap-6">
                                {{-- Color dot + Model info --}}
                                <div class="flex items-center gap-3 w-64 flex-shrink-0">
                                    <div class="w-3 h-3 rounded {{ $colors[$i % count($colors)] }}"></div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-900 mono">{{ $item->llmModel->model_name ?? 'Unknown' }}</p>
                                        @if($item->llmModel?->provider)
                                            @php
                                                $providerName = strtolower($item->llmModel->provider->name ?? 'unknown');
                                                $badgeClass = match ($providerName) {
                                                    'openai' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                                    'anthropic' => 'bg-orange-50 text-orange-700 border-orange-100',
                                                    'google', 'google ai' => 'bg-blue-50 text-blue-700 border-blue-100',
                                                    'anti-gravity', 'antigravity' => 'bg-purple-50 text-purple-700 border-purple-100',
                                                    default => 'bg-gray-50 text-gray-600 border-gray-200',
                                                };
                                            @endphp
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ $badgeClass }}">
                                                {{ $item->llmModel->provider->name }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Slider --}}
                                <div class="flex-1">
                                    <input type="range" min="0" max="100" step="1"
                                        x-model.number="weights['{{ $item->quota_id }}']"
                                        class="w-full h-2 rounded-lg appearance-none cursor-pointer accent-blue-600 bg-gray-200">
                                </div>

                                {{-- Number input --}}
                                <div class="flex items-center gap-1 flex-shrink-0">
                                    <input type="number" name="weights[{{ $item->quota_id }}]" min="0" max="100"
                                        x-model.number="weights['{{ $item->quota_id }}']"
                                        class="w-16 px-2 py-1.5 text-sm font-bold text-center border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 mono">
                                    <span class="text-sm font-bold text-gray-400">%</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Save button --}}
                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50 flex items-center justify-between">
                        <p class="text-[11px] text-gray-400 font-medium">Changes take effect on your next API request.</p>
                        <button type="submit" class="btn-primary text-sm"
                            :disabled="!isValid"
                            :class="!isValid ? 'opacity-50 cursor-not-allowed' : ''">
                            Save Weights
                        </button>
                    </div>
                </div>
            </form>
        @endif
    </div>
@endsection
