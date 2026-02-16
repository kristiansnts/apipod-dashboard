<?php

namespace App\Console\Commands;

use App\Services\CurrencyExchangeService;
use Illuminate\Console\Command;

class FetchExchangeRates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exchange-rates:fetch {--force : Force refresh even if cache exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch and cache the latest USD to IDR exchange rate';

    /**
     * Execute the console command.
     */
    public function handle(CurrencyExchangeService $exchangeService): int
    {
        $this->info('Fetching exchange rates...');

        try {
            if ($this->option('force')) {
                $rate = $exchangeService->refreshRate();
                $this->info('Exchange rate forcefully refreshed.');
            } else {
                $rate = $exchangeService->getUsdToIdrRate();
            }

            $isLive = $exchangeService->isUsingLiveRate();

            if ($isLive) {
                $this->components->success("Live rate fetched: 1 USD = Rp " . number_format($rate, 2));
            } else {
                $this->components->warn("Using fallback rate: 1 USD = Rp " . number_format($rate, 2));
                $this->warn('API may be unavailable. Check your API key or network connection.');
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->components->error('Failed to fetch exchange rates: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}

