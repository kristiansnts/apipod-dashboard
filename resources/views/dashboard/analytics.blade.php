@extends('layouts.app')

@section('title', 'Analytics')
@section('subtitle', 'Understand your API traffic patterns, performance metrics, and cost distribution.')

@section('content')
    <div class="space-y-6">
        {{-- Row 1: Summary Stats --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="card p-6">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Total Requests</p>
                <p class="text-2xl font-extrabold text-gray-900 tracking-tight">{{ number_format($totalRequests) }}</p>
            </div>
            <div class="card p-6">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Total Tokens</p>
                <p class="text-2xl font-extrabold text-gray-900 tracking-tight">{{ number_format($totalTokens) }}</p>
            </div>
            <div class="card p-6">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Total Cost</p>
                <p class="text-2xl font-extrabold text-gray-900 tracking-tight">${{ number_format($totalCost, 4) }}</p>
            </div>
            <div class="card p-6">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Avg Latency</p>
                <p class="text-2xl font-extrabold text-gray-900 tracking-tight">{{ $avgLatency !== null ? $avgLatency.'ms' : 'N/A' }}</p>
            </div>
        </div>

        {{-- Row 2: Performance + Model Distribution --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Performance Snapshot --}}
            <div class="card p-6">
                <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-6">Performance Snapshot</h3>

                <div class="space-y-6">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-bold text-gray-700">Success Rate</span>
                            <span class="text-xs font-bold text-emerald-600">{{ $successRate !== null ? $successRate.'%' : 'N/A' }}</span>
                        </div>
                        <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                            <div class="bg-emerald-500 h-full rounded-full transition-all" style="width: {{ $successRate ?? 0 }}%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-bold text-gray-700">Cache Hit Rate</span>
                            <span class="text-xs font-bold text-blue-600">{{ $cacheHitRate !== null ? $cacheHitRate.'%' : 'N/A' }}</span>
                        </div>
                        <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                            <div class="bg-blue-500 h-full rounded-full transition-all" style="width: {{ $cacheHitRate ?? 0 }}%"></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-6 pt-4 border-t border-gray-100">
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Avg Latency</p>
                            <p class="text-2xl font-extrabold text-gray-900 tracking-tight">{{ $avgLatency !== null ? $avgLatency.'ms' : 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">P95 Latency</p>
                            <p class="text-2xl font-extrabold text-gray-900 tracking-tight">{{ $p95Latency !== null ? $p95Latency.'ms' : 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Model Distribution --}}
            <div class="card p-6">
                <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-6">Model Distribution</h3>

                @if($modelDistribution->isEmpty())
                    <div class="flex items-center justify-center h-48">
                        <p class="text-sm text-gray-400">No request data yet.</p>
                    </div>
                @else
                    @php
                        $distColors = ['bg-blue-500', 'bg-emerald-500', 'bg-amber-500', 'bg-purple-500', 'bg-pink-500', 'bg-cyan-500', 'bg-red-500', 'bg-indigo-500'];
                    @endphp

                    {{-- Distribution Bar --}}
                    <div class="w-full h-6 rounded-lg overflow-hidden flex mb-6">
                        @foreach($modelDistribution as $i => $dist)
                            @php $pct = $totalRequests > 0 ? round(($dist->count / $totalRequests) * 100, 1) : 0; @endphp
                            <div class="{{ $distColors[$i % count($distColors)] }} flex items-center justify-center text-white text-[9px] font-bold"
                                style="width: {{ $pct }}%"
                                title="{{ $dist->model }}: {{ $pct }}%">
                                @if($pct >= 10) {{ $pct }}% @endif
                            </div>
                        @endforeach
                    </div>

                    {{-- Model breakdown --}}
                    <div class="space-y-3">
                        @foreach($modelDistribution as $i => $dist)
                            @php $pct = $totalRequests > 0 ? round(($dist->count / $totalRequests) * 100, 1) : 0; @endphp
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 min-w-0 flex-1">
                                    <div class="w-2.5 h-2.5 rounded flex-shrink-0 {{ $distColors[$i % count($distColors)] }}"></div>
                                    <span class="text-xs font-bold text-gray-700 mono truncate">{{ $dist->model ?? 'unknown' }}</span>
                                </div>
                                <div class="flex items-center gap-4 flex-shrink-0">
                                    <span class="text-[11px] font-bold text-gray-500">{{ number_format($dist->count) }} reqs</span>
                                    <span class="text-[11px] font-bold text-gray-500">{{ number_format($dist->tokens) }} tok</span>
                                    <span class="text-[11px] font-bold text-gray-900">${{ number_format($dist->cost, 4) }}</span>
                                    <span class="text-[11px] font-bold text-blue-600 w-10 text-right">{{ $pct }}%</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Row 3: Recent Logs --}}
        <div class="card overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                <h3 class="text-sm font-bold text-gray-900 mono">Recent Requests</h3>
                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">Last 20</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50/20">
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-500 uppercase tracking-widest">Time</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-500 uppercase tracking-widest">Model</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-500 uppercase tracking-widest">Status</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-500 uppercase tracking-widest">Latency</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-500 uppercase tracking-widest">Cache</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-500 uppercase tracking-widest">Tokens</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-500 uppercase tracking-widest">Cost</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 font-medium">
                        @forelse($recentLogs as $log)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <span class="text-[12px] text-gray-500">{{ $log->created_at->format('M d, H:i:s') }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-[13px] font-bold text-gray-900 mono">{{ $log->model ?? '-' }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    @php $code = $log->status_code ?? 200; @endphp
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase
                                        {{ $code < 400 ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-red-50 text-red-600 border border-red-100' }}">
                                        {{ $code }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($log->latency_ms !== null)
                                        <span class="text-[13px] font-bold {{ $log->latency_ms > 5000 ? 'text-red-600' : ($log->latency_ms > 2000 ? 'text-amber-600' : 'text-gray-900') }}">
                                            {{ number_format($log->latency_ms) }}ms
                                        </span>
                                    @else
                                        <span class="text-[13px] text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($log->cache_hit)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-blue-50 text-blue-600 border border-blue-100">HIT</span>
                                    @else
                                        <span class="text-[10px] font-bold text-gray-400 uppercase">MISS</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-[13px] text-gray-900 font-bold">{{ number_format($log->input_tokens + $log->output_tokens) }}</div>
                                    <div class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">
                                        {{ number_format($log->input_tokens) }} in / {{ number_format($log->output_tokens) }} out
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-[13px] text-gray-900 font-bold">${{ number_format($log->cost_usd, 4) }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-gray-400 text-sm italic">
                                    No request data yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
