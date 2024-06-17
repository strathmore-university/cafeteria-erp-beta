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
    protected static ?string $model = StockLevel::class;

    protected static ?string $cluster = Inventory::class;

    protected static ?int $navigationSort = 3;

    protected static ?string $slug = 'stock-levels';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('store.name')->searchable()->sortable(),
                TextColumn::make('article.name')->searchable()->sortable(),
                TextColumn::make('previous_units')->sortable(),
                TextColumn::make('current_units')->sortable(),
                IconColumn::make('is_sold_stock')->boolean()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->actions([])
            ->bulkActions([]);
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

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }
}
