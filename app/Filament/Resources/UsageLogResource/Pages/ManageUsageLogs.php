<?php

namespace App\Filament\Resources\UsageLogResource\Pages;

use App\Filament\Resources\UsageLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageUsageLogs extends ManageRecords
{
    protected static string $resource = UsageLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
