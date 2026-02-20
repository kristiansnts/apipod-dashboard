<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TokenLedgerResource\Pages;
use App\Models\TokenLedger;
use App\Models\Organization;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TokenLedgerResource extends Resource
{
    protected static ?string $model = TokenLedger::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Management';
    protected static ?string $navigationLabel = 'Token Ledger';
    protected static ?string $modelLabel = 'Ledger Entry';

    // Read-only: no create/edit
    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('organization.name')
                    ->label('Organization')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'usage' => 'warning',
                        'topup' => 'success',
                        'reset' => 'info',
                        'adjustment' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('model')
                    ->placeholder('-')
                    ->searchable(),
                Tables\Columns\TextColumn::make('input_tokens')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('output_tokens')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cost_usd')
                    ->label('Cost (USD)')
                    ->money('usd')
                    ->sortable(),
                Tables\Columns\TextColumn::make('balance_after')
                    ->label('Balance After')
                    ->numeric()
                    ->sortable()
                    ->color(fn($state) => $state < 0 ? 'danger' : null),
                Tables\Columns\TextColumn::make('description')
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('request_id')
                    ->limit(20)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('org_id')
                    ->label('Organization')
                    ->options(Organization::pluck('name', 'id'))
                    ->searchable(),
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'usage' => 'Usage',
                        'topup' => 'Topup',
                        'reset' => 'Reset',
                        'adjustment' => 'Adjustment',
                    ]),
            ])
            ->actions([])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTokenLedger::route('/'),
        ];
    }
}
