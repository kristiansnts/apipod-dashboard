<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlanResource\Pages;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\LlmModel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PlanResource extends Resource
{
    protected static ?string $model = Plan::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Shop';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Plan Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->minValue(0),
                        Forms\Components\TextInput::make('duration_days')
                            ->required()
                            ->numeric()
                            ->default(30)
                            ->suffix('days')
                            ->minValue(1),
                        Forms\Components\Select::make('sub_id')
                            ->label('Subscription')
                            ->options(Subscription::all()->pluck('sub_name', 'sub_id'))
                            ->searchable()
                            ->nullable(),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Quota & Limits')
                    ->schema([
                        Forms\Components\TextInput::make('token_quota')
                            ->label('Monthly Token Quota')
                            ->numeric()
                            ->default(0)
                            ->helperText('Total tokens per billing cycle (e.g. 3000000 for 3M)'),
                        Forms\Components\TextInput::make('max_api_keys')
                            ->label('Max API Keys')
                            ->numeric()
                            ->default(1)
                            ->minValue(1),
                        Forms\Components\TextInput::make('rate_limit_rpm')
                            ->label('Rate Limit RPM')
                            ->numeric()
                            ->nullable()
                            ->helperText('Requests per minute (empty = unlimited)'),
                        Forms\Components\TextInput::make('rate_limit_tpm')
                            ->label('Rate Limit TPM')
                            ->numeric()
                            ->nullable()
                            ->helperText('Tokens per minute (empty = unlimited)'),
                    ])->columns(2),

                Forms\Components\Section::make('Allowed Models')
                    ->schema([
                        Forms\Components\Select::make('allowedModels')
                            ->label('Models accessible with this plan')
                            ->multiple()
                            ->relationship('allowedModels', 'model_name')
                            ->preload()
                            ->helperText('Leave empty to allow all models (BYOK/Free plan behavior)'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('token_quota')
                    ->label('Token Quota')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_api_keys')
                    ->label('Max Keys')
                    ->sortable(),
                Tables\Columns\TextColumn::make('duration_days')
                    ->suffix(' days')
                    ->sortable(),
                Tables\Columns\TextColumn::make('subscription.sub_name')
                    ->label('Subscription')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('allowed_models_count')
                    ->counts('allowedModels')
                    ->label('Models')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->trueLabel('Active plans')
                    ->falseLabel('Inactive plans'),
            ])
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
            'index' => Pages\ManagePlans::route('/'),
        ];
    }
}
