@extends('layouts.app')

@section('title', 'Dashboard')
@section('subtitle', 'Overview of your plan, quota, and usage history.')

@section('content')
    <div class="space-y-6">
        {{-- Row 1: Current Plan & Quota Info --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Current Plan Card --}}
            <div class="card p-6 lg:col-span-2">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-sm font-bold text-gray-400 uppercase tracking-widest">Current Plan</h2>
                    @if($plan)
                        <span
                            class="px-2.5 py-1 rounded-full bg-blue-50 text-blue-600 text-[10px] font-bold uppercase tracking-wider border border-blue-100">
                            {{ $plan->name }}
                        </span>
                    @endif
                </div>

                @if($plan)
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div>
                            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-1">Monthly Quota</p>
                            <p class="text-2xl font-extrabold text-gray-900 tracking-tight">
                                {{ number_format($plan->token_quota) }} <span
                                    class="text-sm font-bold text-gray-400">tokens</span></p>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-1">Max API Keys</p>
                            <p class="text-2xl font-extrabold text-gray-900 tracking-tight">{{ $plan->max_api_keys }}</p>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-1">Price</p>
                            <p class="text-2xl font-extrabold text-gray-900 tracking-tight">
                                Rp{{ number_format($plan->price, 0, ',', '.') }}</p>
                        </div>
                    </div>
                @else
                    <div class="text-center py-10 bg-gray-50/50 rounded-xl border border-dashed border-gray-200">
                        <p class="text-sm font-medium text-gray-500 mb-4">No active plan found for your organization.</p>
                        <a href="{{ route('shop.index') }}" class="btn-primary text-xs inline-flex items-center gap-2">
                            Browse Subscription Plans →
                        </a>
                    </div>
                @endif
            </div>

            {{-- Quota Reset Card --}}
            <div class="card p-6">
                <h2 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-6">Next Quota Reset</h2>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xl font-extrabold text-gray-900 tracking-tight">
                            {{ $org?->quota_reset_at?->format('M d, Y') ?? 'Pending' }}
                        </p>
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">Automatic Refresh</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Row 2: Token Balance & Daily Request Progress --}}
        @if($org && $plan)
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="card p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-sm font-bold text-gray-400 uppercase tracking-widest">Token Balance</h2>
                        <span class="text-sm font-bold text-gray-900">
                            {{ number_format($org->token_balance) }} <span class="text-gray-400">remaining</span>
                        </span>
                    </div>

                    @php
                        $percent = $plan->token_quota > 0 ? round(($org->token_balance / $plan->token_quota) * 100, 1) : 0;
                        $percent = max(0, min(100, $percent));
                        $barColor = $percent > 50 ? 'bg-blue-600' : ($percent > 20 ? 'bg-amber-500' : 'bg-red-500');
                    @endphp

                    <div class="w-full bg-gray-100 rounded-full h-3 mb-2">
                        <div class="{{ $barColor }} h-3 rounded-full transition-all duration-1000" style="width: {{ $percent }}%"></div>
                    </div>
                    <div class="flex justify-between items-center mt-3">
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">
                            Consumed: {{ number_format($plan->token_quota - $org->token_balance) }} tokens
                        </p>
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">
                            {{ $percent }}% Capacity
                        </p>
                    </div>
                </div>

                <div class="card p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-sm font-bold text-gray-400 uppercase tracking-widest">Daily Requests</h2>
                        <span class="text-sm font-bold text-gray-900">
                            {{ number_format($dailyRequestCount) }} <span class="text-gray-400">/ {{ number_format($plan->daily_request_cap) }}</span>
                        </span>
                    </div>

                    @php
                        $dailyPercent = $plan->daily_request_cap > 0 ? round(($dailyRequestCount / $plan->daily_request_cap) * 100, 1) : 0;
                        $dailyPercent = max(0, min(100, $dailyPercent));
                        $dailyBarColor = $dailyPercent < 50 ? 'bg-blue-600' : ($dailyPercent < 80 ? 'bg-amber-500' : 'bg-red-500');
                    @endphp

                    <div class="w-full bg-gray-100 rounded-full h-3 mb-2">
                        <div class="{{ $dailyBarColor }} h-3 rounded-full transition-all duration-1000" style="width: {{ $dailyPercent }}%"></div>
                    </div>
                    <div class="flex justify-between items-center mt-3">
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">
                            {{ number_format($plan->daily_request_cap - $dailyRequestCount) }} requests left today
                        </p>
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">
                            {{ $dailyPercent }}% Used
                        </p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Row 3: Usage History --}}
        <div class="card overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                <h3 class="text-sm font-bold text-gray-900 mono">Usage History</h3>
                <a href="{{ route('dashboard.analytics') }}"
                    class="text-[11px] font-bold text-blue-600 uppercase tracking-widest hover:underline">View Analytics
                    &rarr;</a>
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
    </div>
@endsection