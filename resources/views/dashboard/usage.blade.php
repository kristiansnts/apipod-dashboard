@extends('layouts.app')

@section('title', 'Usage & Quotas')
@section('subtitle', 'Monitor your token consumption and track your API usage logs.')

@section('content')


    <!-- Usage Overview Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="card p-6">
            <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-4">Token Balance</h3>
            <div class="flex items-end gap-2 mb-2">
                <span
                    class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ number_format($org->token_balance ?? 0) }}</span>
                <span class="text-sm font-bold text-gray-400 pb-1">Tokens</span>
            </div>
            <div class="w-full bg-gray-100 h-1.5 rounded-full overflow-hidden">
                @php
                    $limit = $org?->plan?->token_quota ?? 1000000;
                    $percentage = $limit > 0 ? (($org->token_balance ?? 0) / $limit) * 100 : 0;
                @endphp
                <div class="bg-blue-600 h-full rounded-full" style="width: {{ min(100, $percentage) }}%"></div>
            </div>
            <p class="mt-3 text-[11px] font-bold text-gray-400 uppercase tracking-widest">
                of {{ number_format($limit) }} quota
            </p>
        </div>

        <div class="card p-6">
            <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-4">Total Requests</h3>
            <div class="flex items-end gap-2 mb-2">
                <span
                    class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ number_format($ledgerEntries->total()) }}</span>
            </div>
            <p class="text-[11px] text-gray-500 font-medium">Recorded in the current billing cycle</p>
        </div>

        <div class="card p-6">
            <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-4">Quota Reset</h3>
            <div class="flex items-end gap-2 mb-2">
                <span class="text-xl font-extrabold text-gray-900 tracking-tight">
                    {{ $org?->quota_reset_at ? $org->quota_reset_at->format('M d, Y') : 'Next Billing Date' }}
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
                        <th class="px-6 py-4 text-[11px] font-bold text-gray-500 uppercase tracking-widest">Type</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-gray-500 uppercase tracking-widest">Model</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-gray-500 uppercase tracking-widest">Tokens</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-gray-500 uppercase tracking-widest">Cost</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-gray-500 uppercase tracking-widest">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 font-medium">
                    @forelse($ledgerEntries as $entry)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase
                                    {{ $entry->type === 'usage' ? 'bg-blue-50 text-blue-600 border border-blue-100' : 'bg-emerald-50 text-emerald-600 border border-emerald-100' }}">
                                    {{ $entry->type }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-[13px] font-bold text-gray-900 mono">{{ $entry->model ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-[13px] text-gray-900 font-bold">{{ number_format($entry->total_tokens) }}</div>
                                <div class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">
                                    {{ number_format($entry->input_tokens) }} in / {{ number_format($entry->output_tokens) }} out
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-[13px] text-gray-900 font-bold">${{ number_format($entry->cost_usd, 4) }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-[12px] text-gray-600">{{ $entry->created_at->format('M d, H:i') }}</span>
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
            {{ $ledgerEntries->links() }}
        </div>
    </div>
@endsection