<?php

namespace App\Filament\Clusters\Retail\Resources;

use App\Filament\Clusters\Retail;
use App\Filament\Clusters\Retail\Resources\RetailSessionResource\Pages\ListRetailSessions;
use App\Filament\Clusters\Retail\Resources\RetailSessionResource\Pages\ViewRetailSession;
use App\Models\Retail\RetailSession;
use Exception;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RetailSessionResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $model = RetailSession::class;

    protected static ?string $slug = 'retail-sessions';

    protected static ?string $cluster = Retail::class;

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('code'),
            // todo: add customer
            TextInput::make('restaurant_id')
                ->formatStateUsing(fn (RetailSession $session) => $session->restaurant->getAttribute('name')),
            TextInput::make('cashier_id')
                ->formatStateUsing(fn (RetailSession $session) => $session->cashier->name),
            TextInput::make('initial_cash_float'),
            TextInput::make('ending_cash_float'),
            TextInput::make('closed_by')
                ->formatStateUsing(fn (RetailSession $session) => $session->accountant?->name),
            Toggle::make('is_open'),
            Toggle::make('is_closed'),
            Section::make([
                placeholder('created_at', 'Created at'),
                placeholder('updated_at', 'Last updated at'),
                placeholder('closed_at', 'Close at'),
            ])->columns(3),
        ]);
    }

    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('code')->searchable()->sortable(),
            TextColumn::make('restaurant.name')->searchable()->sortable(),
            TextColumn::make('cashier.name')->searchable()->sortable(),
            TextColumn::make('initial_cash_float')->numeric(),
            TextColumn::make('ending_cash_float')->numeric(),
            IconColumn::make('is_closed')->boolean(),
            TextColumn::make('closed_at')
                ->toggleable(isToggledHiddenByDefault: true),
        ])->filters([
            SelectFilter::make('restaurant_id')->searchable()
                ->relationship('restaurant', 'name')
                ->preload()->label('Restaurant'),
        ])->actions([ViewAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRetailSessions::route('/'),
            'view' => ViewRetailSession::route('/{record}/view'),
        ];
    }
}
