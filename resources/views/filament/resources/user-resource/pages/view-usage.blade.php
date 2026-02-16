<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Date Range Filter & Currency Toggle -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <div class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Start Date</label>
                    <input type="date" wire:model="startDate" 
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                </div>
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">End Date</label>
                    <input type="date" wire:model="endDate" 
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                </div>
                <button wire:click="updateDateRange" 
                    class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition">
                    Update
                </button>
                <!-- Currency Toggle -->
                <button wire:click="toggleCurrency" type="button"
                    class="px-6 py-2 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 text-gray-900 dark:text-white rounded-lg shadow-sm border border-gray-300 dark:border-gray-600 transition flex items-center gap-2 font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="font-semibold">{{ $currency }}</span>
                </button>
            </div>
            @if($currency === 'IDR')
            <div class="mt-3 flex items-center justify-between text-xs">
                <div class="text-gray-500 dark:text-gray-400">
                    Exchange Rate: 1 USD = Rp {{ number_format($exchangeRate, 0, ',', '.') }}
                    @if($isLiveRate)
                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                            Live Rate
                        </span>
                    @else
                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                            Fallback Rate
                        </span>
                    @endif
                </div>
                <button wire:click="refreshExchangeRate" wire:loading.attr="disabled" type="button"
                    class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium flex items-center gap-1 disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg wire:loading.remove wire:target="refreshExchangeRate" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <svg wire:loading wire:target="refreshExchangeRate" class="animate-spin w-3 h-3" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span wire:loading.remove wire:target="refreshExchangeRate">Refresh Rate</span>
                    <span wire:loading wire:target="refreshExchangeRate">Refreshing...</span>
                </button>
            </div>
            @endif
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Cost</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">
                            {!! $this->formatCurrency($summary['total_cost'] ?? 0) !!}
                        </p>
                    </div>
                    <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-full">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Tokens</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">
                            {{ number_format($summary['total_tokens'] ?? 0) }}
                        </p>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-full">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Input Tokens</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">
                            {{ number_format($summary['total_input_tokens'] ?? 0) }}
                        </p>
                    </div>
                    <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-full">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Output Tokens</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">
                            {{ number_format($summary['total_output_tokens'] ?? 0) }}
                        </p>
                    </div>
                    <div class="p-3 bg-orange-100 dark:bg-orange-900/30 rounded-full">
                        <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Usage by Model -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Usage by Model</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-gray-700 dark:text-gray-300 uppercase bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-6 py-3">Model</th>
                            <th class="px-6 py-3 text-right">Input Tokens</th>
                            <th class="px-6 py-3 text-right">Output Tokens</th>
                            <th class="px-6 py-3 text-right">Total Tokens</th>
                            <th class="px-6 py-3 text-right">Requests</th>
                            <th class="px-6 py-3 text-right">Input Cost</th>
                            <th class="px-6 py-3 text-right">Output Cost</th>
                            <th class="px-6 py-3 text-right">Total Cost</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usageByModel as $usage)
                        <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                {{ $usage['model'] }}
                            </td>
                            <td class="px-6 py-4 text-right text-gray-600 dark:text-gray-400">
                                {{ number_format($usage['input_tokens']) }}
                            </td>
                            <td class="px-6 py-4 text-right text-gray-600 dark:text-gray-400">
                                {{ number_format($usage['output_tokens']) }}
                            </td>
                            <td class="px-6 py-4 text-right text-gray-600 dark:text-gray-400">
                                {{ number_format($usage['total_tokens']) }}
                            </td>
                            <td class="px-6 py-4 text-right text-gray-600 dark:text-gray-400">
                                {{ number_format($usage['request_count']) }}
                            </td>
                            <td class="px-6 py-4 text-right text-gray-600 dark:text-gray-400">
                                {!! $this->formatCurrency($usage['input_cost']) !!}
                            </td>
                            <td class="px-6 py-4 text-right text-gray-600 dark:text-gray-400">
                                {!! $this->formatCurrency($usage['output_cost']) !!}
                            </td>
                            <td class="px-6 py-4 text-right font-semibold text-gray-900 dark:text-white">
                                {!! $this->formatCurrency($usage['total_cost']) !!}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                No usage data available for the selected period
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Daily Usage Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Daily Usage Trend</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-gray-700 dark:text-gray-300 uppercase bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-6 py-3">Date</th>
                            <th class="px-6 py-3 text-right">Input Tokens</th>
                            <th class="px-6 py-3 text-right">Output Tokens</th>
                            <th class="px-6 py-3 text-right">Total Tokens</th>
                            <th class="px-6 py-3 text-right">Requests</th>
                            <th class="px-6 py-3 text-right">Cost</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dailyAnalytics as $day)
                        <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                {{ \Carbon\Carbon::parse($day['date'])->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 text-right text-gray-600 dark:text-gray-400">
                                {{ number_format($day['input_tokens']) }}
                            </td>
                            <td class="px-6 py-4 text-right text-gray-600 dark:text-gray-400">
                                {{ number_format($day['output_tokens']) }}
                            </td>
                            <td class="px-6 py-4 text-right text-gray-600 dark:text-gray-400">
                                {{ number_format($day['total_tokens']) }}
                            </td>
                            <td class="px-6 py-4 text-right text-gray-600 dark:text-gray-400">
                                {{ number_format($day['request_count']) }}
                            </td>
                            <td class="px-6 py-4 text-right font-semibold text-gray-900 dark:text-white">
                                {!! $this->formatCurrency($day['total_cost']) !!}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                No daily usage data available
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
