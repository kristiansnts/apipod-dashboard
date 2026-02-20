<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LlmModelResource\Pages;
use App\Models\LlmModel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LlmModelResource extends Resource
{
    protected static ?string $model = LlmModel::class;
    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('model_name')
                    ->required()
                    ->placeholder('e.g. claude-sonnet-4-5'),
                Forms\Components\Select::make('provider_id')
                    ->relationship('provider', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Section::make('Token Pricing (USD per 1M tokens)')
                    ->schema([
                        Forms\Components\TextInput::make('input_cost_per_1m')
                            ->numeric()
                            ->default(0)
                            ->prefix('$'),
                        Forms\Components\TextInput::make('output_cost_per_1m')
                            ->numeric()
                            ->default(0)
                            ->prefix('$'),
                    ])->columns(2),
                Forms\Components\Section::make('Model Capabilities')
                    ->schema([
                        Forms\Components\Toggle::make('tool_support')
                            ->label('Tool/Function Calling Support')
                            ->default(false),
                        Forms\Components\TextInput::make('max_context')
                            ->label('Max Context Window')
                            ->numeric()
                            ->nullable()
                            ->suffix('tokens')
                            ->helperText('Maximum context length'),
                        Forms\Components\TextInput::make('default_weight')
                            ->label('Default Weight')
                            ->numeric()
                            ->default(100)
                            ->helperText('Higher = more likely to be chosen in routing'),
                    ])->columns(3),
                Forms\Components\Section::make('Rate Limits')
                    ->description('Leave empty for unlimited')
                    ->schema([
                        Forms\Components\TextInput::make('rpm')
                            ->label('RPM')
                            ->helperText('Requests per minute')
                            ->numeric()
                            ->nullable(),
                        Forms\Components\TextInput::make('tpm')
                            ->label('TPM')
                            ->helperText('Tokens per minute')
                            ->numeric()
                            ->nullable(),
                        Forms\Components\TextInput::make('rpd')
                            ->label('RPD')
                            ->helperText('Requests per day')
                            ->numeric()
                            ->nullable(),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('model_name')->searchable(),
                Tables\Columns\TextColumn::make('provider.name')
                    ->label('Provider')
                    ->badge(),
                Tables\Columns\TextColumn::make('input_cost_per_1m')
                    ->label('Input / 1M')
                    ->money('usd')
                    ->sortable(),
                Tables\Columns\TextColumn::make('output_cost_per_1m')
                    ->label('Output / 1M')
                    ->money('usd')
                    ->sortable(),
                Tables\Columns\IconColumn::make('tool_support')
                    ->boolean()
                    ->label('Tools'),
                Tables\Columns\TextColumn::make('max_context')
                    ->label('Context')
                    ->numeric()
                    ->placeholder('-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('default_weight')
                    ->label('Weight')
                    ->sortable(),
                Tables\Columns\TextColumn::make('rpm')
                    ->label('RPM')
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageLlmModels::route('/'),
        ];
    }
}
