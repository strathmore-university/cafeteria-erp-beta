<?php

namespace App\Filament\Clusters\Inventory\Resources;

use App\Filament\Clusters\Inventory;
use App\Filament\Clusters\Inventory\Resources\StockTransferResource\Pages\CreateStockTransfer;
use App\Filament\Clusters\Inventory\Resources\StockTransferResource\Pages\EditStockTransfer;
use App\Filament\Clusters\Inventory\Resources\StockTransferResource\Pages\ListStockTransfers;
use App\Filament\Clusters\Inventory\Resources\StockTransferResource\Pages\ViewStockTransfer;
use App\Filament\Clusters\Inventory\Resources\StockTransferResource\RelationManagers\ItemsRelationManager;
use App\Models\Inventory\StockTransfer;
use App\Models\Inventory\Store;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class StockTransferResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $model = StockTransfer::class;

    protected static ?string $cluster = Inventory::class;

    protected static ?string $slug = 'stock-transfers';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        $cols = 2;

        return $form->schema([
            Section::make([
                Select::make('from_store_id')->required()->preload()
                    ->label('From Station:')->searchable()->reactive()
                    ->relationship('from', 'name')
                    ->afterStateUpdated(function ($state, Set $set): void {
                        if (blank($state)) {
                            $set('to_store_id', null);
                        }
                    }),
                Select::make('to_store_id')->required()->preload()
                    ->label('To Station:')->searchable()->reactive()
                    ->options(fn (Get $get) => self::toStores($get)),
            ])->columns($cols),
            TextInput::make('narration')->required()
                ->string()->maxLength(255),
            TextInput::make('status')->required()->maxLength(255)
                ->string()->visible(fn ($record) => $record?->exists)
                ->formatStateUsing(fn ($state) => Str::title($state)),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('creator.name')->searchable()->sortable(),
            TextColumn::make('to.name')->searchable()->sortable(),
            TextColumn::make('to.name')->searchable()->sortable(),
            TextColumn::make('status'),
        ])->actions([ViewAction::make()]);
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
            'index' => ListStockTransfers::route('/'),
            'create' => CreateStockTransfer::route('/create'),
            'edit' => EditStockTransfer::route('/{record}/edit'),
            'view' => ViewStockTransfer::route('/{record}/view'),
        ];
    }

    private static function toStores(Get $get): array
    {
        return Store::whereNotIn('id', [$get('from_store_id')])
            ->whereCanShipStock(true)
            ->pluck('name', 'id')
            ->toArray();
    }
}
