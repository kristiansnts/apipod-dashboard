<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UsageLogResource\Pages;
use App\Models\UsageLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UsageLogResource extends Resource
{
    protected static ?string $model = UsageLog::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    // List Only: Disable Form
    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('timestamp')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user_id')
                    ->label('User ID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('requested_model')
                    ->label('Request')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('routed_model')
                    ->label('Actual Model')
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('upstream_provider')
                    ->label('Provider')
                    ->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state >= 200 && $state < 300 => 'success',
                        $state === 429 => 'warning',
                        $state >= 400 => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('token_count')
                    ->label('Tokens')
                    ->numeric()
                    ->sortable(),
            ])
            ->defaultSort('timestamp', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('upstream_provider')
                    ->options([
                        'antigravity' => 'Antigravity',
                        'ccs' => 'CCS',
                        'nvidia' => 'NVIDIA',
                    ]),
            ])
            ->actions([]) // Disable Edit/Delete for Logs
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageUsageLogs::route('/'),
        ];
    }

    // Disable global search and creation
    public static function canCreate(): bool { return false; }
}
