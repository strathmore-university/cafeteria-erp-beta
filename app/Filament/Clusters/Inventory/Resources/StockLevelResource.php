<?php

namespace App\Filament\Clusters\Inventory\Resources;

use App\Filament\Clusters\Inventory;
use App\Filament\Clusters\Inventory\Resources\StockLevelResource\Pages\ListStockLevels;
use App\Models\Inventory\StockLevel;
use Filament\Resources\Resource;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class StockLevelResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $cluster = Inventory::class;

    protected static ?string $model = StockLevel::class;

    protected static ?string $slug = 'stock-levels';

    protected static ?int $navigationSort = 3;

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('store.name')->searchable()->sortable(),
            TextColumn::make('article.name')->searchable()->sortable(),
            TextColumn::make('current_units')->numeric(),
            TextColumn::make('previous_units')->numeric(),
            IconColumn::make('is_sold_stock')->boolean()
                ->toggleable(isToggledHiddenByDefault: true),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStockLevels::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return StockLevel::with(['article:id,name', 'store:id,name'])
            ->select([
                'id', 'team_id', 'store_id', 'article_id',
                'current_units', 'previous_units', 'is_sold_stock',
            ]);
    }
}
