@extends('layouts.app')

@section('title', 'AI Models')
@section('subtitle', 'Browse available models, providers, and their corresponding token pricing.')

@section('content')
    <!-- Tab Navigation (SumoPod Style) -->
    <div class="inline-flex p-1 bg-gray-200/50 rounded-[10px] mb-8">
        <a href="{{ route('home') }}" class="tab-button">Quick Start</a>
        <a href="{{ route('dashboard.usage') }}" class="tab-button">Usage & Quotas</a>
        <a href="{{ route('dashboard.models') }}" class="tab-button active">Models</a>
        <a href="{{ route('dashboard.api-keys') }}" class="tab-button">API Keys</a>
    </div>

    <!-- Models Table Card -->
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-6 py-4 text-[11px] font-bold text-gray-500 uppercase tracking-widest">Model</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-gray-500 uppercase tracking-widest">Provider</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-gray-500 uppercase tracking-widest">Context</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-gray-500 uppercase tracking-widest">Input Price</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-gray-500 uppercase tracking-widest">Output Price
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 font-medium">
                    @foreach($models as $model)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-6">
                                <div class="text-sm font-bold text-gray-900 mono">{{ $model->model_name }}</div>
                            </td>
                            <td class="px-6 py-6">
                                @php
                                    $providerName = strtolower($model->provider->name ?? 'unknown');
                                    $badgeClass = match ($providerName) {
                                        'openai' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                        'anthropic' => 'bg-orange-50 text-orange-700 border-orange-100',
                                        'google', 'google ai' => 'bg-blue-50 text-blue-700 border-blue-100',
                                        'anti-gravity', 'antigravity' => 'bg-purple-50 text-purple-700 border-purple-100',
                                        default => 'bg-gray-50 text-gray-600 border-gray-200',
                                    };
                                @endphp
                                <span
                                    class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider border {{ $badgeClass }}">
                                    {{ $model->provider->name ?? 'Unknown' }}
                                </span>
                            </td>
                            <td class="px-6 py-6">
                                <div class="text-[13px] text-gray-600">
                                    {{ $model->tpm ? number_format($model->tpm) : 'Standard' }}
                                </div>
                                <div class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-0.5">Tokens/Min
                                </div>
                            </td>
                            <td class="px-6 py-6">
                                <div class="text-[13px] font-bold text-gray-900">
                                    ${{ number_format($model->input_cost_per_1m, 4) }}</div>
                                <div class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mt-0.5">/ 1M Tokens
                                </div>
                            </td>
                            <td class="px-6 py-6">
                                <div class="text-[13px] font-bold text-gray-900">
                                    ${{ number_format($model->output_cost_per_1m, 4) }}</div>
                                <div class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mt-0.5">/ 1M Tokens
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection