<?php

namespace App\Filament\Clusters\Procurement\Resources\CreditNoteResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static ?string $title = 'Credit Note Items';

    protected static string $relationship = 'items';

    public function table(Table $table): Table
    {
        return $table->recordTitleAttribute('article.name')
            ->columns([
                Tables\Columns\TextColumn::make('article.name')
                    ->searchable()->sortable(),
                grn_item_numeric_column($this->ownerRecord, 'units'),
                grn_item_numeric_column($this->ownerRecord, 'price'),
                Tables\Columns\TextColumn::make('total_value')
                    ->numeric()->sortable()->prefix('Ksh. '),
            ]);
    }
}
