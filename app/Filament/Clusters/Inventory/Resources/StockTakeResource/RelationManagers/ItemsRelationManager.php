<?php

namespace App\Filament\Clusters\Inventory\Resources\StockTakeResource\RelationManagers;

use App\Models\Inventory\StockTake;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static ?string $title = 'Store Items';

    protected static string $relationship = 'items';

    public function table(Table $table): Table
    {
        $preventEdit = false;
        if ($this->ownerRecord instanceof StockTake) {
            $preventEdit = $this->ownerRecord->preventEdit();
        }

        return $table
            ->recordTitleAttribute('article.name')
            ->columns([
                Tables\Columns\TextColumn::make('article.name'),
                Tables\Columns\TextColumn::make('current_units'),
                TextInputColumn::make('actual_units')
                    ->rules(['nullable', 'numeric', 'min:0'])
                    ->disabled($preventEdit),
            ]);
    }
}
