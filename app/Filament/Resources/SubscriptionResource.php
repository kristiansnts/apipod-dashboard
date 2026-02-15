<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriptionResource\Pages;
use App\Models\Subscription;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Subscription Info')
                    ->schema([
                        Forms\Components\TextInput::make('sub_name')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('price')
                            ->numeric()
                            ->prefix('Rp'),
                        Forms\Components\Textarea::make('system_prompt')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Model Weights (Load Balancing)')
                    ->description('Tentukan bobot persentase untuk tiap model LLM')
                    ->schema([
                        Forms\Components\Repeater::make('quotaItems')
                            ->relationship('quotaItems')
                            ->schema([
                                Forms\Components\Select::make('llm_model_id')
                                    ->label('Model')
                                    ->options(\App\Models\LlmModel::all()->pluck('model_name', 'llm_model_id'))
                                    ->required()
                                    ->searchable()
                                    ->columnSpan(2),
                                Forms\Components\TextInput::make('percentage_weight')
                                    ->label('Weight (%)')
                                    ->numeric()
                                    ->default(0)
                                    ->required()
                                    ->suffix('%')
                                    ->columnSpan(1),
                            ])
                            ->columns(3)
                            ->grid(2)
                            ->addActionLabel('Tambah Model')
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sub_name')->searchable(),
                Tables\Columns\TextColumn::make('price')->money('IDR'),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSubscriptions::route('/'),
        ];
    }
}
