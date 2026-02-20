@extends('layouts.app')

@section('title', 'Analytics')
@section('subtitle', 'Understand your API traffic patterns, performance metrics, and cost distribution.')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Performance Overview -->
        <div class="card p-8">
            <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-8">Performance Snapshot</h3>

            <div class="space-y-8">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-bold text-gray-700">Request Success Rate</span>
                        <span class="text-xs font-bold text-emerald-600">99.8%</span>
                    </div>
                    <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                        <div class="bg-emerald-500 h-full rounded-full" style="width: 99.8%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-bold text-gray-700">Cache Hit Rate</span>
                        <span class="text-xs font-bold text-blue-600">24.5%</span>
                    </div>
                    <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                        <div class="bg-blue-500 h-full rounded-full" style="width: 24.5%"></div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-6 pt-4 border-t border-gray-50">
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Avg Latency</p>
                        <p class="text-2xl font-extrabold text-gray-900 tracking-tight">142ms</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">P95 Latency</p>
                        <p class="text-2xl font-extrabold text-gray-900 tracking-tight">328ms</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Provider Distribution (Mock Chart) -->
        <div class="card p-8">
            <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-6">Provider Distribution</h3>
            <div class="flex items-center justify-center h-48 relative">
                <!-- Simple CSS Donut Chart Mock -->
                <div
                    class="w-32 h-32 rounded-full border-[16px] border-blue-500 border-r-indigo-500 border-b-emerald-400 border-l-orange-400 opacity-80">
                </div>
                <div class="absolute text-center">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Total</p>
                    <p class="text-xl font-extrabold text-gray-900">{{ number_format($logs->count()) }}</p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 mt-6">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded bg-blue-500"></div>
                    <span class="text-xs font-bold text-gray-600 uppercase tracking-wider">Anthropic</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded bg-indigo-500"></div>
                    <span class="text-xs font-bold text-gray-600 uppercase tracking-wider">OpenAI</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded bg-emerald-400"></div>
                    <span class="text-xs font-bold text-gray-600 uppercase tracking-wider">Google</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded bg-orange-400"></div>
                    <span class="text-xs font-bold text-gray-600 uppercase tracking-wider">NVIDIA</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table (Last 10 Requests) -->
    <div class="card overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/30">
            <h3 class="font-bold text-gray-900">Real-time Performance Logs</h3>
            <span
                class="px-3 py-1 bg-emerald-50 text-emerald-600 text-[10px] font-bold uppercase tracking-widest border border-emerald-100 rounded">Live</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50/20">
                        <th class="px-8 py-4 text-[11px] font-bold text-gray-500 uppercase tracking-widest">Timestamp</th>
                        <th class="px-8 py-4 text-[11px] font-bold text-gray-500 uppercase tracking-widest">Model</th>
                        <th class="px-8 py-4 text-[11px] font-bold text-gray-500 uppercase tracking-widest">Provider</th>
                        <th class="px-8 py-4 text-[11px] font-bold text-gray-500 uppercase tracking-widest">Latency</th>
                        <th class="px-8 py-4 text-[11px] font-bold text-gray-500 uppercase tracking-widest">Tokens</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($logs->take(10) as $log)
                        <tr class="hover:bg-gray-50/30 transition-colors text-[13px] font-medium">
                            <td class="px-8 py-4 text-gray-400 tabular-nums">Just now</td>
                            <td class="px-8 py-4 text-gray-900 mono">{{ $log->requested_model }}</td>
                            <td class="px-8 py-4 uppercase text-xs font-bold text-gray-600">{{ $log->upstream_provider }}</td>
                            <td class="px-8 py-4 text-gray-500">124ms</td>
                            <td class="px-8 py-4 text-gray-900">{{ number_format($log->token_count) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-10 text-center text-gray-400">No telemetry data available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection