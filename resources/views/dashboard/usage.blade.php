@extends('layouts.app')

@section('title', 'Usage & Quotas')
@section('subtitle', 'Monitor your token consumption and track your API usage logs.')

@section('content')
    <!-- Tab Navigation (SumoPod Style) -->
    <div class="inline-flex p-1 bg-gray-200/50 rounded-[10px] mb-8">
        <a href="{{ route('home') }}" class="tab-button">Quick Start</a>
        <a href="{{ route('dashboard.usage') }}" class="tab-button active">Usage & Quotas</a>
        <a href="{{ route('dashboard.models') }}" class="tab-button">Models</a>
        <a href="{{ route('dashboard.api-keys') }}" class="tab-button">API Keys</a>
    </div>

    <!-- Usage Overview Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="card p-6">
            <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-4">Tokens Used (Current)</h3>
            <div class="flex items-end gap-2 mb-2">
                <span
                    class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ number_format($user->tokens_used) }}</span>
                <span class="text-sm font-bold text-gray-400 pb-1">Tokens</span>
            </div>
            <div class="w-full bg-gray-100 h-1.5 rounded-full overflow-hidden">
                @php
                    $limit = ($user->plan && $user->plan->subscription) ? $user->plan->subscription->monthly_token_limit : 1000000;
                    $percentage = $limit > 0 ? ($user->tokens_used / $limit) * 100 : 0;
                @endphp
                <div class="bg-blue-600 h-full rounded-full" style="width: {{ min(100, $percentage) }}%"></div>
            </div>
            <p class="mt-3 text-[11px] font-bold text-gray-400 uppercase tracking-widest">
                {{ number_format(max(0, $limit - $user->tokens_used)) }} Tokens remaining
            </p>
        </div>

        <div class="card p-6">
            <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-4">Total Requests</h3>
            <div class="flex items-end gap-2 mb-2">
                <span
                    class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ number_format($usageLogs->total()) }}</span>
            </div>
            <p class="text-[11px] text-gray-500 font-medium">Recorded in the current billing cycle</p>
        </div>

        <div class="card p-6">
            <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-4">Quota Reset</h3>
            <div class="flex items-end gap-2 mb-2">
                <span class="text-xl font-extrabold text-gray-900 tracking-tight">
                    {{ $user->quota_reset_at ? $user->quota_reset_at->format('M d, Y') : 'Next Billing Date' }}
                </span>
            </div>
            <p class="text-[11px] text-gray-500 font-medium">Your limits will be automatically refreshed</p>
        </div>
    </div>

    <!-- Usage Logs Table -->
    <div class="card overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900 mono">Usage History</h3>
            <div class="flex gap-2">
                <button class="btn-secondary text-[11px] py-1 px-3">Export CSV</button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50/20">
                        <th class="px-6 py-4 text-[11px] font-bold text-gray-500 uppercase tracking-widest">ID</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-gray-500 uppercase tracking-widest">Model</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-gray-500 uppercase tracking-widest">Provider</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-gray-500 uppercase tracking-widest">Tokens</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-gray-500 uppercase tracking-widest">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 font-medium">
                    @forelse($usageLogs as $log)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 text-xs mono text-gray-400">#{{ $log->usage_id }}</td>
                            <td class="px-6 py-4">
                                <span class="text-[13px] font-bold text-gray-900 mono">{{ $log->requested_model }}</span>
                                <div class="text-[10px] text-gray-400">via {{ $log->routed_model }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-[12px] text-gray-600 uppercase font-bold">{{ $log->upstream_provider }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-[13px] text-gray-900 font-bold">{{ number_format($log->token_count) }}</div>
                                <div class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">
                                    {{ $log->input_tokens + $log->output_tokens }} total
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $log->status == '200' ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-red-50 text-red-600 border border-red-100' }}">
                                    {{ $log->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-400 text-sm italic">
                                No activity recorded yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $usageLogs->links() }}
        </div>
    </div>
@endsection