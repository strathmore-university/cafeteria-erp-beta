<?php

namespace App\Filament\Clusters\Inventory\Pages\Inventory\Resources;

use App\Filament\Clusters\Inventory;
use App\Filament\Clusters\Inventory\Pages\Inventory\Resources\StockTakeResource\Pages\ListStockTakes;
use App\Filament\Clusters\Inventory\Pages\Inventory\Resources\StockTakeResource\Pages\ViewStockTake;
use App\Filament\Clusters\Inventory\Pages\Inventory\Resources\StockTakeResource\RelationManagers\ItemsRelationManager;
use App\Models\Inventory\StockTake;
use Exception;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class StockTakeResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-clipboard';

    protected static ?string $slug = 'inventory/stock-takes';

    protected static ?string $cluster = Inventory::class;

    protected static ?string $model = StockTake::class;

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        $cols = 2;

        return $form->schema([
            TextInput::make('store_id')->label('Store')
                ->formatStateUsing(fn ($record) => $record->store->name),
            TextInput::make('created_by')->label('Created by')
                ->formatStateUsing(fn ($record) => Str::title($record->creator->name)),
            TextInput::make('description'),
            TextInput::make('status')->label('Status')
                ->formatStateUsing(fn ($state) => Str::title($state)),
            Section::make([
                placeholder('created_at', 'Created Date'),
                placeholder('updated_at', 'Last Modified Date'),
                placeholder('started_at', 'Started at'),
                placeholder('concluded_at', 'Concluded at'),
            ])->columns($cols),
        ])->disabled();
    }

    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('store.name')
                    ->searchable()->sortable(),
                TextColumn::make('creator.name')
                    ->searchable()->sortable(),
                TextColumn::make('started_at')->dateTime(),
                TextColumn::make('concluded_at')->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')->badge()
                    ->formatStateUsing(fn (string $state): string => Str::title($state))
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        default => 'warning'
                    }),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'completed' => 'Completed',
                    'draft' => 'Draft',
                ]),
            ])
            ->actions([ViewAction::make()]);
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStockTakes::route('/'),
            'view' => ViewStockTake::route('/{record}/view'),
        ];
    }
}
