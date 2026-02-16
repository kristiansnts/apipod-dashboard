<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Services\TokenUsageService;
use App\Services\CurrencyExchangeService;
use Filament\Resources\Pages\Page;
use Filament\Notifications\Notification;
use Carbon\Carbon;

class ViewUsage extends Page
{
    protected static string $resource = UserResource::class;

    protected static string $view = 'filament.resources.user-resource.pages.view-usage';
    
    public ?int $userId = null;
    public array $summary = [];
    public array $usageByModel = [];
    public array $dailyAnalytics = [];
    public array $topModels = [];
    public string $startDate = '';
    public string $endDate = '';
    public string $currency = 'USD';
    public float $exchangeRate = 15800.0;
    public bool $isLiveRate = false;

    public function mount(?int $record = null): void
    {
        $this->userId = $record;
        $this->startDate = Carbon::now()->subDays(30)->format('Y-m-d');
        $this->endDate = Carbon::now()->format('Y-m-d');
        
        // Get exchange rate from API or fallback
        $exchangeService = app(CurrencyExchangeService::class);
        $this->exchangeRate = $exchangeService->getUsdToIdrRate();
        $this->isLiveRate = $exchangeService->isUsingLiveRate();
        
        $this->loadData();
    }

    public function loadData(): void
    {
        $service = app(TokenUsageService::class);
        $startDate = Carbon::parse($this->startDate);
        $endDate = Carbon::parse($this->endDate);

        $this->summary = $service->calculateUserCost($this->userId, $startDate, $endDate);
        $this->usageByModel = $service->getUserUsageByModel($this->userId, $startDate, $endDate);
        $this->dailyAnalytics = $service->getDailyUsageAnalytics($this->userId, $startDate, $endDate);
        $this->topModels = $service->getTopModelsByCost($this->userId, 5, $startDate, $endDate);
    }

    public function updateDateRange(): void
    {
        $this->loadData();
    }

    public function toggleCurrency(): void
    {
        $this->currency = $this->currency === 'USD' ? 'IDR' : 'USD';
    }

    public function refreshExchangeRate(): void
    {
        $exchangeService = app(CurrencyExchangeService::class);
        $this->exchangeRate = $exchangeService->refreshRate();
        $this->isLiveRate = $exchangeService->isUsingLiveRate();
        
        // Send Filament notification to user
        if ($this->isLiveRate) {
            Notification::make()
                ->success()
                ->title('Exchange Rate Updated')
                ->body('1 USD = Rp ' . number_format($this->exchangeRate, 0, ',', '.'))
                ->send();
        } else {
            Notification::make()
                ->warning()
                ->title('Using Fallback Rate')
                ->body('Unable to fetch live rate. Check API key configuration.')
                ->send();
        }
    }

    public function formatCurrency(float $amount): string
    {
        if ($this->currency === 'IDR') {
            $convertedAmount = $amount * $this->exchangeRate;
            return 'Rp ' . number_format($convertedAmount, 0, ',', '.');
        }
        return '$' . number_format($amount, 4);
    }
}
