<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProviderResource\Pages;
use App\Filament\Resources\ProviderResource\RelationManagers;
use App\Models\Provider;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProviderResource extends Resource
{
    protected static ?string $model = Provider::class;
    protected static ?string $navigationIcon = 'heroicon-o-server-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('provider_type')
                    ->options([
                        'anthropic' => 'Anthropic (/v1/messages)',
                        'openai' => 'OpenAI (/v1/chat/completions)',
                        'groq' => 'Groq (OpenAI-compatible)',
                        'antigravity_proxy' => 'Antigravity Proxy',
                        'cliproxy' => 'Cliproxy',
                    ])
                    ->reactive()
                    ->required(),
                Forms\Components\TextInput::make('base_url')
                    ->required()
                    ->url()
                    ->maxLength(255)
                    ->placeholder('https://api.example.com')
                    ->hintAction(
                        Forms\Components\Actions\Action::make('set_default')
                            ->label('Set Default')
                            ->icon('heroicon-m-arrow-path')
                            ->action(function (Forms\Set $set, Forms\Get $get) {
                                $url = match ($get('provider_type')) {
                                    'anthropic' => 'https://api.anthropic.com',
                                    'openai' => 'https://api.openai.com',
                                    'groq' => 'https://api.groq.com',
                                    default => '',
                                };
                                $set('base_url', $url);
                            })
                    ),
                Forms\Components\Textarea::make('api_key')
                    ->label('API Key')
                    ->rows(3)
                    ->columnSpanFull()
                    ->visible(fn (Forms\Get $get) => in_array($get('provider_type'), ['antigravity_proxy', 'cliproxy', 'groq']))
                    ->required(fn (Forms\Get $get) => in_array($get('provider_type'), ['antigravity_proxy', 'cliproxy', 'groq'])),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('provider_type')->badge(),
                Tables\Columns\TextColumn::make('base_url'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\AccountsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProviders::route('/'),
            'create' => Pages\CreateProvider::route('/create'),
            'edit' => Pages\EditProvider::route('/{record}/edit'),
        ];
    }
}
