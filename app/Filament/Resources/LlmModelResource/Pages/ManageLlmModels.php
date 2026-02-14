<?php

namespace App\Filament\Resources\LlmModelResource\Pages;

use App\Filament\Resources\LlmModelResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageLlmModels extends ManageRecords
{
    protected static string $resource = LlmModelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
