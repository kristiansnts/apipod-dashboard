<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrganizationResource\Pages;
use App\Models\Organization;
use App\Models\Plan;
use App\Services\QuotaEnforcementService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class OrganizationResource extends Resource
{
    protected static ?string $model = Organization::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationGroup = 'Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Organization Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\Select::make('plan_id')
                            ->label('Plan')
                            ->options(Plan::where('is_active', true)->pluck('name', 'id'))
                            ->searchable()
                            ->nullable(),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Disable to block this organization'),
                    ])->columns(2),

                Forms\Components\Section::make('Token Balance')
                    ->schema([
                        Forms\Components\TextInput::make('token_balance')
                            ->numeric()
                            ->default(0)
                            ->helperText('Current token balance (can be adjusted manually)'),
                        Forms\Components\DateTimePicker::make('quota_reset_at')
                            ->label('Next Quota Reset')
                            ->nullable(),
                        Forms\Components\DateTimePicker::make('next_billing_at')
                            ->label('Next Billing Date')
                            ->nullable(),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('plan.name')
                    ->label('Plan')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('token_balance')
                    ->label('Token Balance')
                    ->numeric()
                    ->sortable()
                    ->color(fn($state) => $state <= 0 ? 'danger' : ($state < 100000 ? 'warning' : 'success')),
                Tables\Columns\TextColumn::make('users_count')
                    ->counts('users')
                    ->label('Users'),
                Tables\Columns\TextColumn::make('api_keys_count')
                    ->counts('apiKeys')
                    ->label('API Keys'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quota_reset_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
                Tables\Filters\SelectFilter::make('plan_id')
                    ->label('Plan')
                    ->options(Plan::pluck('name', 'id')),
                Tables\Filters\Filter::make('negative_balance')
                    ->label('Negative Balance')
                    ->query(fn($query) => $query->where('token_balance', '<', 0)),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('add_tokens')
                    ->label('Add Tokens')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->form([
                        Forms\Components\TextInput::make('amount')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->label('Token Amount'),
                        Forms\Components\TextInput::make('description')
                            ->default('Manual token topup')
                            ->maxLength(255),
                    ])
                    ->action(function (Organization $record, array $data) {
                        $service = app(QuotaEnforcementService::class);
                        $service->recordTopup($record->id, (int) $data['amount'], $data['description']);

                        Notification::make()
                            ->title('Tokens added')
                            ->body("Added {$data['amount']} tokens to {$record->name}")
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('toggle_block')
                    ->label(fn(Organization $record) => $record->is_active ? 'Block' : 'Unblock')
                    ->icon(fn(Organization $record) => $record->is_active ? 'heroicon-o-no-symbol' : 'heroicon-o-check-circle')
                    ->color(fn(Organization $record) => $record->is_active ? 'danger' : 'success')
                    ->requiresConfirmation()
                    ->action(function (Organization $record) {
                        $record->update(['is_active' => !$record->is_active]);
                        Notification::make()
                            ->title($record->is_active ? 'Organization unblocked' : 'Organization blocked')
                            ->success()
                            ->send();
                    }),
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
            'index' => Pages\ListOrganizations::route('/'),
            'create' => Pages\CreateOrganization::route('/create'),
            'edit' => Pages\EditOrganization::route('/{record}/edit'),
        ];
    }
}
