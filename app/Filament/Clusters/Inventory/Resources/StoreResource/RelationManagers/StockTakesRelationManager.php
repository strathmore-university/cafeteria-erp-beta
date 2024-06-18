<?php

namespace App\Filament\Clusters\Inventory\Resources\StoreResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class StockTakesRelationManager extends RelationManager
{
    protected static string $relationship = 'stockTakes';

    public function table(Table $table): Table
    {
        return $table->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('creator.name')->searchable()->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable(),
                TextColumn::make('concluded_at')->dateTime()->sortable(),
                TextColumn::make('status')->badge()
                    ->formatStateUsing(fn ($state) => Str::title($state))
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        default => 'gray',
                    })->sortable(),
            ])
            ->actions([
                Action::make('view')
                    ->url(fn ($record) => get_record_url($record))
                    ->icon('heroicon-o-eye'),
            ]);
    }
}
