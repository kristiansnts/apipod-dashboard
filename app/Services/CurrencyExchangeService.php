<?php

namespace App\Services;

use AshAllenDesign\LaravelExchangeRates\Classes\ExchangeRate;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CurrencyExchangeService
{
    protected ExchangeRate $exchangeRate;

    public function __construct(ExchangeRate $exchangeRate)
    {
        $this->exchangeRate = $exchangeRate;
    }

    /**
     * Get USD to IDR exchange rate with caching (24 hours)
     */
    public function getUsdToIdrRate(): float
    {
        return Cache::remember('exchange_rate_usd_to_idr', 86400, function () {
            try {
                // Fetch live rate from API (latest rate, no date needed for free tier)
                $rate = $this->exchangeRate->exchangeRate('USD', 'IDR');
                
                if ($rate && $rate > 0) {
                    return (float) $rate;
                }
                
                return $this->getFallbackRate();
            } catch (\Exception $e) {
                Log::warning('Failed to fetch exchange rate from API: ' . $e->getMessage());
                return $this->getFallbackRate();
            }
        });
    }

    /**
     * Get fallback rate from config
     */
    public function getFallbackRate(): float
    {
        return (float) config('app.usd_to_idr_fallback_rate', 15800);
    }

    /**
     * Force refresh the exchange rate
     */
    public function refreshRate(): float
    {
        Cache::forget('exchange_rate_usd_to_idr');
        return $this->getUsdToIdrRate();
    }

    /**
     * Check if using live rate or fallback
     */
    public function isUsingLiveRate(): bool
    {
        try {
            $rate = $this->exchangeRate->exchangeRate('USD', 'IDR');
            return $rate && $rate > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get exchange rate last updated time
     */
    public function getLastUpdated(): ?string
    {
        $cacheKey = 'exchange_rate_usd_to_idr';
        
        if (Cache::has($cacheKey)) {
            return 'Cached (updates daily at 1:00 AM)';
        }
        
        return null;
    }
}
