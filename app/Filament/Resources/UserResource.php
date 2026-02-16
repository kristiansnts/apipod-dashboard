<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required(),
                Forms\Components\TextInput::make('email')->email()->required(),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => bcrypt($state))
                    ->required(fn (string $context): bool => $context === 'create'),
                Forms\Components\Section::make('API & Subscription')
                    ->schema([
                        Forms\Components\TextInput::make('apitoken')
                            ->default(fn () => 'sk-' . Str::random(48))
                            ->required()
                            ->suffixAction(
                                Forms\Components\Actions\Action::make('generateToken')
                                    ->icon('heroicon-m-arrow-path')
                                    ->action(function (Forms\Set $set) {
                                        $set('apitoken', 'sk-' . Str::random(48));
                                    })
                            ),
                        Forms\Components\Select::make('sub_id')
                            ->relationship('subscription', 'sub_name')
                            ->required(),
                        Forms\Components\Toggle::make('active')->default(true),
                        Forms\Components\DateTimePicker::make('expires_at'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('apitoken')->copyable()->fontFamily('mono')->limit(15),
                Tables\Columns\TextColumn::make('subscription.sub_name')->label('Plan'),
                Tables\Columns\IconColumn::make('active')->boolean(),
                Tables\Columns\TextColumn::make('expires_at')->dateTime()->sortable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\Action::make('viewUsage')
                    ->label('View Usage')
                    ->icon('heroicon-o-chart-bar')
                    ->url(fn (User $record): string => static::getUrl('viewUsage', ['record' => $record->id])),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageUsers::route('/'),
            'viewUsage' => Pages\ViewUsage::route('/{record}/usage'),
        ];
    }
}
