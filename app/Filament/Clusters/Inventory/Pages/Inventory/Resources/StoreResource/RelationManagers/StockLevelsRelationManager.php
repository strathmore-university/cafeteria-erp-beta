<?php

namespace App\Filament\Clusters\Inventory\Pages\Inventory\Resources\StoreResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StockLevelsRelationManager extends RelationManager
{
    protected static string $relationship = 'stockLevels';

    public function table(Table $table): Table
    {
        return $table->recordTitleAttribute('article.name')
            ->columns([
                TextColumn::make('article.name')
                    ->searchable()->sortable(),
                TextColumn::make('article.type')->label('Type'),
                TextColumn::make('current_units')->numeric(),
                TextColumn::make('article.unit.name')
                    ->searchable()->sortable(),
                TextColumn::make('previous_units')->numeric(),
            ]);
    }
}
