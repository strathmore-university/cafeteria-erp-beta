<?php

namespace App\Filament\Clusters\Procurement\Resources\GoodsReceivedNoteResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function table(Table $table): Table
    {
        $preventEdit = $this->ownerRecord->preventEdit();

        return $table
            ->recordTitleAttribute('article.name')
            ->columns([
                Tables\Columns\TextColumn::make('article.name')
                    ->searchable()
                    ->sortable(),
                TextInputColumn::make('batch_number')->disabled($preventEdit)
                    ->rules(['nullable', 'string']),
                TextInputColumn::make('units')->disabled($preventEdit)
                    ->rules(['numeric']),
                TextInputColumn::make('price')->disabled($preventEdit)
                    ->rules(['numeric']),
                // todo: add expires at here and on the form as well

                Tables\Columns\TextColumn::make('total_value')
                    ->numeric()
                    ->prefix('Ksh. '),
            ])
            ->headerActions([])
            ->bulkActions([])
            ->filters([])
            ->actions([]);
    }
}
