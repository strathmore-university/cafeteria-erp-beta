<?php

namespace App\Filament\Clusters\Accounting\Resources;

use App\Filament\Clusters\Accounting;
use App\Filament\Clusters\Accounting\Resources\PaymentModesResource\Pages\CreatePaymentModes;
use App\Filament\Clusters\Accounting\Resources\PaymentModesResource\Pages\EditPaymentModes;
use App\Filament\Clusters\Accounting\Resources\PaymentModesResource\Pages\ListPaymentModes;
use App\Filament\Clusters\Accounting\Resources\PaymentModesResource\Pages\ViewPaymentModes;
use App\Models\Accounting\PaymentMode;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PaymentModesResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $model = PaymentMode::class;

    protected static ?string $cluster = Accounting::class;

    protected static ?string $slug = 'payment-modes';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        $cols = 2;

        return $form->schema([
            text_input('name'),
            text_input('description'),
            text_input('kfs_account_number'),
            text_input('object_code'),
            text_input('internal_account_number'),
            text_input('internal_object_code'),

            text_input('revenue_account_number'),
            text_input('revenue_object_code'),

            Toggle::make('requires_approval')->required()
                ->default(false),
            Toggle::make('requires_verification')->required()
                ->default(false),

            Section::make([
                placeholder('created_at', 'Created at'),
                placeholder('updated_at', 'Last updated at'),
            ])->columns($cols),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name'),
            TextColumn::make('kfs_account_number'),
            TextColumn::make('object_code'),
            TextColumn::make('revenue_account_number')
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('revenue_object_code')
                ->toggleable(isToggledHiddenByDefault: true),
            IconColumn::make('is_active')->boolean(),
        ])->actions([ViewAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPaymentModes::route('/'),
            'create' => CreatePaymentModes::route('/create'),
            'edit' => EditPaymentModes::route('/{record}/edit'),
            'view' => ViewPaymentModes::route('/{record}/view'),
        ];
    }
}
